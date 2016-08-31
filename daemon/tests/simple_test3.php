<?

require_once('/var/www/com/config.php');
require_once('/var/www/com/config_server.php');
require_once('/var/www/com/db.php');
$server=$config['server_host'];

$redis = new Redis();    
$redis->connect('127.0.0.1');

function parseUrlmail($url,$to,$subj,$body,$from)
{
	$uagent = "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13";
	$keyword=$word;
	$keyword=urlencode(iconv('utf-8','windows-1251',$keyword));
	//$url='http://2mm.ru/forum/search.php?mode=result&start=15';
	$postvars='mail='.$to.'&title='.$subj.'&content='.$body.'&from='.$from;
	$uagent = "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13";
	$ch = curl_init( $url );
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);   // возвращает веб-страницу
	curl_setopt($ch, CURLOPT_HEADER, 0);           // не возвращает заголовки
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);   // переходит по редиректам
	curl_setopt($ch, CURLOPT_POSTFIELDS    ,$postvars);
	curl_setopt($ch, CURLOPT_ENCODING, "");        // обрабатывает все кодировки
	curl_setopt($ch, CURLOPT_USERAGENT, $uagent);  // useragent
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 120); // таймаут соединения
	curl_setopt($ch, CURLOPT_TIMEOUT, 120);        // таймаут ответа
	curl_setopt($ch, CURLOPT_MAXREDIRS, 10);       // останавливаться после 10-ого редиректа
	$content = curl_exec( $ch );
	$err     = curl_errno( $ch );
	$errmsg  = curl_error( $ch );
	$header  = curl_getinfo( $ch );
	curl_close( $ch );
	return $content;
}

$content_count_confing=file_get_contents('/var/www/com/launcher.config.inc');
$count_config=json_decode($content_count_confing,true);

$assoc_name_daemon['fs']='tp_job3';
$assoc_name_daemon['ef']='efjob';
$assoc_name_daemon['uj']='multi_uj';
$assoc_name_daemon['transf']='re_transf';
$assoc_name_daemon['tj']='tagjob';
$assoc_name_daemon['dj']='dj';
$assoc_name_daemon['sj']='wobot.prodSentiment';
$assoc_name_daemon['auth']='auther';
$assoc_name_daemon['reposts']='vk_reposts';
$assoc_name_daemon['utasker']='tasker';
$assoc_name_daemon['updater']='updater';
$assoc_name_daemon['typhoon']='tp_typhoon';
$assoc_name_daemon['uservk']='uj_vk';
$assoc_name_daemon['cash']='cash_launcher';
$assoc_name_daemon['checkorders']='check_orders';
$assoc_name_daemon['freash']='freash_harv';

print_r($count_config);

$db=new database();
$db->connect();

$qlast_post=$db->query('SELECT post_id as cnt FROM blog_post WHERE post_time<'.time().' AND post_time>='.mktime(0,0,0,date('n'),date('j'),date('Y')));
$output='count_harv_post='.$db->num_rows($qlast_post).'<br>';
$qlast_post=$db->query('SELECT blog_id as cnt FROM robot_blogs2 WHERE blog_last_update=0');
$output='null_blogs='.$db->num_rows($qlast_post).'<br>';
$output.='LA: '.shell_exec('uptime')."\n";
$count_prev_queue=$redis->scard('prev_queue');
if ($count_prev_queue>40000) $addition_title.='*';
$count_transf_queue=$redis->scard('transf_queue');
if ($count_transf_queue>40000) $addition_title.='*';

$output.='<br>count_prev = '.$count_prev_queue.'| count_transf = '.$count_transf_queue;

$output.='<table border="1"><tr><td>Название демона</td><td>Работающих/Должно работать</td><td>Статус</td><tr>';
foreach ($count_config as $name => $count)
{
	echo '|'.$name.'|'."\n";
	$cont_shell=shell_exec('ps ax | grep '.$assoc_name_daemon[$name]);
	$mcont_shell=explode("\n", $cont_shell);
	// print_r($mcont_shell);
	$daemon_work=0;
	foreach ($mcont_shell as $item_cont_shell)
	{
		if (preg_match('/php '.$assoc_name_daemon[$name].'\.php \d+ \d+/isu', $item_cont_shell)) $daemon_work++;
	}
	if ($daemon_work==$count) $output.='<tr><td>'.$name.'</td><td>'.$daemon_work.'/'.$count.'</td><td><p style="color: green;">SUCCESS</p></td></tr>';
	else 
	{
		echo 'php /var/www/daemon/launcher.php '.$name.' start '.$count;
		shell_exec('php /var/www/daemon/launcher.php '.$name.' start '.$count);
		$addition_title.='*';
		$output.='<tr><td>'.$name.'</td><td>'.$daemon_work.'/'.$count.'</td><td><p style="color: red;">FAIL</p></td></tr>';
	}
	// print_r($mcont_shell);
}
$output.='</table>';

$msrc[]='twitter';
$msrc[]='yandex_blogs';
$msrc[]='facebook';
$msrc[]='vkontakte';
$msrc[]='vkontakte_video';
$msrc[]='topsy';
$msrc[]='slideshare';
$msrc[]='google';
$msrc[]='youtube';
$msrc[]='google_plus';
$msrc[]='bing';

$interval=4;

foreach ($msrc as $item)
{
	for ($i=0;$i<24;$i++)
	{
		if ($i%$interval!=0) continue;
		$msrc_val[$item][mktime($i,0,0,date('n'),date('j'),date('Y'))]=$redis->get('source_'.$item.'_'.mktime($i,0,0,date('n'),date('j'),date('Y')));

	}
}

$output.='<br><table border="1"><tr><td>source:</td>';

for ($i=0;$i<24;$i++)
{
	if ($i%$interval!=0) continue;
	$output.='<td>'.$i.'-'.($i+4).'</td>';
}

foreach ($msrc_val as $key => $item)
{
	$output.='<tr><td>'.$key.'</td>';
	foreach ($item as $k => $i)
	{
		$output.='<td>'.(intval($i)==0?'<p style="color: red;">'.intval($i).'</p>':intval($i)).'</td>';
	}
	$output.='</tr>';
}
$output.='</table>';

echo $output;

parseUrlmail('http://188.120.239.225/api/service/sendmail.php','zmei123@yandex.ru','Краткая статистика работы сервера ('.$server.') '.$addition_title,$output,'noreply2@wobot.ru');


?>