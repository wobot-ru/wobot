<?

require_once('/var/www/daemon/com/db.php');
require_once('/var/www/daemon/com/config.php');
require_once('/var/www/daemon/userjob/bot/kernel.php');

require_once('/var/www/daemon/fulljob/adv_src_func.php');

require_once('/var/www/daemon/userjob/get_twitter2.php');
require_once('/var/www/daemon/userjob/get_livejournal.php');
require_once('/var/www/daemon/userjob/get_li.php');
require_once('/var/www/daemon/userjob/get_mail.php');
require_once('/var/www/daemon/userjob/get_rutwit.php');
require_once('/var/www/daemon/userjob/get_yaru.php');
require_once('/var/www/daemon/userjob/get_plus_google.php');
// require_once('userjob/get_vkontakte2.php');
require_once('/var/www/daemon/userjob/parsers/babyblog/get_babyblog.php');
require_once('/var/www/daemon/userjob/parsers/foursquare/get_foursquare.php');
require_once('/var/www/daemon/userjob/parsers/friendfeed/get_friendfeed.php');
require_once('/var/www/daemon/userjob/parsers/kp/get_kp.php');

error_reporting(0);

$server='beta.wobot.ru';

$db=new database();
$db->connect();

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

// echo 'SELECT SUM(post_engage) FROM blog_post WHERE post_host=\'twitter.com\' post_time>'.mktime(0,0,0,date('n'),date('j')-1,date('Y'));
$t='Вовлеченность: <br>';
$qeng=$db->query('SELECT SUM(post_engage) as cnt,post_host FROM blog_post WHERE post_time>'.mktime(0,0,0,date('n'),date('j')-1,date('Y')).' GROUP BY post_host ORDER BY cnt DESC');
while ($eng=$db->fetch($qeng))
{
	if ($eng['cnt']!=0) $output.=$eng['cnt'].' '.$eng['post_host'].'<br>';
	if ($eng['post_host']=='facebook.com' && $eng['cnt']==0) $additional_title.='*';
	if ($eng['post_host']=='vk.com' && $eng['cnt']==0) $additional_title.='*';
	if ($eng['post_host']=='livejournal.com' && $eng['cnt']==0) $additional_title.='*';
	if ($eng['post_host']=='twitter.com' && $eng['cnt']==0) $additional_title.='*';
}

$cont=$redis->get('proxy_list');
$mproxy=json_decode($cont,true);

$html='<table border="1"><tr><td>source:</td><td>nick</td><td>login</td><td>loc</td><td>fol</td><td>gender</td><td>age</td><td>ico</td></tr>';
$inf=get_liveinternet('3206612');
$html.='<tr><td>liveinternet</td><td>'.$inf['name'].'</td><td>'.$inf['nick'].'</td><td>'.$inf['loc'].'</td><td>'.$inf['fol'].'</td><td>'.$inf['gender'].'</td><td>'.$inf['age'].'</td><td>'.$inf['ico'].' <img src="'.$inf['ico'].'"></td></tr>';
$inf=get_lj('martin');
$html.='<tr><td>livejournal</td><td>'.$inf['name'].'</td><td>'.$inf['nick'].'</td><td>'.$inf['loc'].'</td><td>'.$inf['fol'].'</td><td>'.$inf['gender'].'</td><td>'.$inf['age'].'</td><td>'.$inf['ico'].' <img src="'.$inf['ico'].'"></td></tr>';
$inf=get_rutwit('Mayskiy');
$html.='<tr><td>rutwit</td><td>'.$inf['name'].'</td><td>'.$inf['nick'].'</td><td>'.$inf['loc'].'</td><td>'.$inf['fol'].'</td><td>'.$inf['gender'].'</td><td>'.$inf['age'].'</td><td>'.$inf['ico'].' <img src="'.$inf['ico'].'"></td></tr>';
$inf=get_twitter('ru_wobot');
$html.='<tr><td>twitter</td><td>'.$inf['name'].'</td><td>'.$inf['nick'].'</td><td>'.$inf['loc'].'</td><td>'.$inf['fol'].'</td><td>'.$inf['gender'].'</td><td>'.$inf['age'].'</td><td>'.$inf['ico'].' <img src="'.$inf['ico'].'"></td></tr>';
$inf=get_yaru('tatya-pichugi');
$html.='<tr><td>yaru</td><td>'.$inf['name'].'</td><td>'.$inf['nick'].'</td><td>'.$inf['loc'].'</td><td>'.$inf['fol'].'</td><td>'.$inf['gender'].'</td><td>'.$inf['age'].'</td><td>'.$inf['ico'].' <img src="'.$inf['ico'].'"></td></tr>';
$inf=get_babyblog('Mari_Kowalsky');
$html.='<tr><td>babyblog</td><td>'.$inf['name'].'</td><td>'.$inf['nick'].'</td><td>'.$inf['loc'].'</td><td>'.$inf['fol'].'</td><td>'.$inf['gender'].'</td><td>'.$inf['age'].'</td><td>'.$inf['ico'].' <img src="'.$inf['ico'].'"></td></tr>';
$inf=get_foursquare('25376859');
$html.='<tr><td>foursquare</td><td>'.$inf['name'].'</td><td>'.$inf['nick'].'</td><td>'.$inf['loc'].'</td><td>'.$inf['fol'].'</td><td>'.$inf['gender'].'</td><td>'.$inf['age'].'</td><td>'.$inf['ico'].' <img src="'.$inf['ico'].'"></td></tr>';
$inf=get_ff('ramjam');
$html.='<tr><td>friendfeed</td><td>'.$inf['name'].'</td><td>'.$inf['nick'].'</td><td>'.$inf['loc'].'</td><td>'.$inf['fol'].'</td><td>'.$inf['gender'].'</td><td>'.$inf['age'].'</td><td>'.$inf['ico'].' <img src="'.$inf['ico'].'"></td></tr>';
$inf=get_kp('4125286');
$html.='<tr><td>kp</td><td>'.$inf['name'].'</td><td>'.$inf['nick'].'</td><td>'.$inf['loc'].'</td><td>'.$inf['fol'].'</td><td>'.$inf['gender'].'</td><td>'.$inf['age'].'</td><td>'.$inf['ico'].' <img src="'.$inf['ico'].'"></td></tr>';
$inf=get_google_plus('103014885508349779123');
$html.='<tr><td>google plus</td><td>'.$inf['name'].'</td><td>'.$inf['nick'].'</td><td>'.$inf['loc'].'</td><td>'.$inf['fol'].'</td><td>'.$inf['gender'].'</td><td>'.$inf['age'].'</td><td>'.$inf['ico'].' <img src="'.$inf['ico'].'"></td></tr>';
$html.='</table>';
$output.=$html;

parseUrlmail('http://188.120.239.225/api/service/sendmail.php','zmei123@yandex.ru','Статистика работы сервера ('.$server.') '.$additional_title,$output,'noreply2@wobot.ru');

?>