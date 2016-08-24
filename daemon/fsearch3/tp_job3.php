<?

// Добавить поле в таблицу blog_orders ... third_party

require_once('/var/www/daemon/com/config.php');
require_once('/var/www/com/config_server.php');
//require_once('/var/www/daemon/com/func.php');
require_once('/var/www/daemon/com/db.php');
require_once('/var/www/daemon/bot/kernel.php');
require_once('/var/www/daemon/com/users.php');
require_once('/var/www/daemon/com/qlib.php');
require_once('cfg.php');//конфиг токенов
//require_once('lcheck.php');
require_once('/var/www/daemon/fsearch3/ch.php');
/*require_once('/var/www/userjob/get_vkontakte.php');
require_once('/var/www/userjob/get_twitter.php');
require_once('/var/www/userjob/get_livejournal.php');*/

proc_nice(10);

ini_set('memory_limit', '2048M');

$redis=new Redis() or die("Can'f load redis module.");
$redis->connect('127.0.0.1');

require_once('yblogs.php');
require_once('icerocket.php');
require_once('facebook.php');
require_once('google.php');
require_once('topsy.php');
require_once('twitter/twitter.php');
require_once('youtube.php');
require_once('vkontakte2.php');
require_once('google_plus/google_plus.php');
require_once('slideshare.php');
require_once('bing.php');
require_once('elastic.php');

date_default_timezone_set('Europe/Moscow');
// error_reporting(E_ERROR);
ignore_user_abort(true);
set_time_limit(0);
ini_set('max_execution_time',0);
ini_set('default_charset','utf-8');
ob_implicit_flush();

$db = new database();
$db->connect();

$memcache = memcache_connect("localhost", 11211);

//date('H')=15;
//print_r($_SERVER['argv']);
//die();
$order_delta=$_SERVER['argv'][1];
$debug_mode=$_SERVER['argv'][2];
$debug_mode=2;
$fp = fopen('/var/www/pids/fs'.$order_delta.'.pid', 'w');
fwrite($fp, getmypid());
fclose($fp);
echo $order_delta;

$engage_src['twitter.com']=1;
$engage_src['vk.com']=1;
$engage_src['facebook.com']=1;
$engage_src['livejournal.com']=1;

$iter=0;

$mproxy=json_decode($redis->get('proxy_list'),true);
for ($i=0;$i<20;$i++)
{
	$new_mproxy[]=$mproxy[rand(0,count($proxys))];
}
$mproxy=$new_mproxy;

while (1)
{
	if (!$db->ping()) {
		echo "MYSQL disconnected, reconnect after 10 sec...\n";
		sleep(10);
		$db->connect();
		die();
	}
	// if (((intval(date('H',time()))==23) && ((60-intval(date('i',time())))<20)) || ((intval(date('H',time()))==6) && ((60-intval(date('i',time())))<20)) || ((intval(date('H',time()))==15) && ((60-intval(date('i',time())))<20)) || ((intval(date('H',time()))==11) && ((60-intval(date('i',time())))<20)) || (date('H')==16) || (date('H')==7) || (date('H')==20))
	if (date('H') % 4 == 0)
	{
		unset($mproxy);
		$iter=0;
		$mproxy=json_decode($redis->get('proxy_list'),true);
		for ($i=0;$i<20;$i++)
		{
			$new_mproxy[]=$mproxy[rand(0,count($proxys))];
		}
		$mproxy=$new_mproxy;
		echo intval(date('H',time()));
		$ressec=$db->query('SELECT order_id,user_id,ut_id,order_name,order_keyword,order_start,order_end,third_sources,order_lang,order_analytics,order_spam,order_settings FROM blog_orders WHERE user_id!=145 AND MOD(order_id,'.$_SERVER['argv'][2].')='.$order_delta.' and third_sources!=0 and (order_end>='.mktime(0,0,0,date('n'),date('j')-1,date('Y')).') AND user_id!=0 AND ut_id!=0 ORDER BY order_id DESC');
		echo 'SELECT order_id,user_id,ut_id,order_name,order_keyword,order_start,order_end,third_sources,order_lang,order_analytics,order_spam,order_settings FROM blog_orders WHERE user_id!=145 AND MOD(order_id,'.$_SERVER['argv'][2].')='.$order_delta.' and third_sources!=0 and (order_end>='.mktime(0,0,0,date('n'),date('j')-1,date('Y')).') AND user_id!=0 AND ut_id!=0 ORDER BY order_id DESC';
	}
	else
	{
		echo intval(date('H',time()));
		$ressec=$db->query('SELECT order_id,user_id,ut_id,order_name,order_keyword,order_start,order_end,third_sources,order_lang,order_analytics,order_spam,order_settings FROM blog_orders WHERE user_id!=145 AND MOD(order_id,'.$_SERVER['argv'][2].')='.$order_delta.' and (third_sources=1 or third_sources=2) and (order_end>='.mktime(0,0,0,date('n'),date('j')-1,date('Y')).') AND user_id!=0 AND ut_id!=0 ORDER BY order_id DESC');
		echo 'SELECT order_id,user_id,ut_id,order_name,order_keyword,order_start,order_end,third_sources,order_lang,order_analytics,order_spam,order_settings FROM blog_orders WHERE user_id!=145 AND MOD(order_id,'.$_SERVER['argv'][2].')='.$order_delta.' and (third_sources=1 or third_sources=2) and (order_end>='.mktime(0,0,0,date('n'),date('j')-1,date('Y')).') AND user_id!=0 AND ut_id!=0 ORDER BY order_id DESC';
	}
	if ($db->num_rows($ressec)==0) 
	{
		if (date('H') % 10 == $_SERVER['argv'][1] % 10) $ressec=$db->query('SELECT order_id,user_id,ut_id,order_name,order_keyword,order_start,order_end,third_sources,order_lang,order_analytics,order_spam,order_settings FROM blog_orders WHERE user_id!=145 AND MOD(order_id,'.$_SERVER['argv'][2].')='.$order_delta.' and third_sources!=0 and (order_end>='.mktime(0,0,0,date('n'),date('j')-1,date('Y')).') AND user_id!=0 AND ut_id!=0 ORDER BY RAND() LIMIT 1');
		else sleep(30);
	}
	while($blog=$db->fetch($ressec))
	{
		$blog['order_keyword']=construct_object_query($blog['order_keyword']);
		print_r($blog);
		// if ((time()-$last_harv[$blog['order_id']])<10800) continue;
		// $last_harv[$blog['order_id']]=time();
		// if (isset($big_theme[$blog['order_id']]) && date('H')>6 && date('H')<19) continue;
		$filters=json_decode($blog['order_settings'],true);
		// print_r($filters);
		//биллинг по сообщениям
		//$qtariff=$db->query('SELECT DISTINCT blog_tariff.tariff_type FROM users, blog_tariff, user_tariff where users.user_id=user_tariff.user_id AND user_tariff.ut_id='.intval($blog['ut_id']).' AND user_tariff.user_id='.intval($blog['user_id']).' LIMIT 1');
		//$usertariff=$db->fetch($qtariff));
		$qtariff=$db->query('SELECT * FROM user_tariff as a LEFT JOIN blog_tariff as b ON a.tariff_id=b.tariff_id WHERE user_id='.$blog['user_id'].' AND ut_id='.$blog['ut_id']);
		echo 'SELECT * FROM user_tariff as a LEFT JOIN blog_tariff as b ON a.tariff_id=b.tariff_id WHERE user_id='.$blog['user_id'].' AND ut_id='.$blog['ut_id'];
		$tariff=$db->fetch($qtariff);
		print_r($tariff);
		echo 1;
		if (($tariff['tariff_id']==10) && ($tariff['ut_date']<=0)) continue;
		echo 2;
		if ($tariff['ut_date']<time()) continue;
		echo 3;
		$var=$redis->get('orders_'.$blog['order_id']);
		$m_dinams=json_decode($var,true);
		echo 4;
		if ($filters['new_limit']!='')
		{
			if ($m_dinams['count_post']>$filters['new_limit']) continue;
		}
		else
		{
			if ($m_dinams['count_post']>$tariff['tariff_posts']) continue;
		}
		echo 5;
		// if (intval($m_dinams['count_post'])>100000 && date('H')>6 && date('H')<19) 
		// {
		// 	$big_theme[$blog['order_id']]=1;
		// 	continue;
		// }
		echo 6;
		echo "\n".date('r')."\n";
		$spams=json_decode($blog['order_spam'],true);
		if ($blog['third_sources']>=$blog['order_start'])
		{
			if ($blog['third_sources']!=0) $mstart=$blog['third_sources'];
			else $mstart = $blog['order_start'];
		}
		else $mstart=$blog['order_start'];
		if ($blog['order_end']>=time())	$mend=time();
		else
		{
			if ($blog['order_end']!=0) $mend=mktime(0,0,0,date('n',$blog['order_end']),date('j',$blog['order_end'])+1,date('Y',$blog['order_end']));
			else $mend=time();
		}
		switch ($blog['order_lang']) {
		    case 0:
		        $text_lang='ru';
		        break;
		    case 1:
		        $text_lang='en';
		        break;
		    case 2:
		        $text_lang='ru';
		        break;
			case 4:
				$text_lang='az';
				break;
		}
		//echo 'TEXTLANG='.$text_lang."\n";
		$mstart=mktime(0,0,0,date('n',$mstart),date('j',$mstart)-1,date('Y',$mstart));
		if ($mstart<$blog['order_start'])
		{
			$mstart=$blog['order_start'];
		}
		echo "\n".$mstart.' '.$mend.' '.$blog['order_id'].' '.$blog['order_keyword'].' TEXTLANG='.$text_lang."\n";
		$temp_orderkw=$blog['order_keyword'];
		$time_start = microtime(true);
		$m1=getpost_yandex((($blog['order_keyword']=='')?$blog['order_name']:$blog['order_keyword']),$mstart,$mend,$text_lang,$mproxy);
		$time_end = microtime(true);
		echo '1 YANDEX (find: '.count($m1['link']).'; for: '.($time_end-$time_start).')'."\n";

		$o_kw_wnot=val_not($blog['order_keyword'],'');
		$blog['order_keyword']=$o_kw_wnot['kw'];

		$time_start = microtime(true);
		$m2=getpost_icerocket((($blog['order_keyword']=='')?$blog['order_name']:$blog['order_keyword']),$mstart,$mend,$text_lang,$mproxy);
		$time_end = microtime(true);
		echo '1.1 ICEROCKET (find: '.count($m2['link']).'; for: '.($time_end-$time_start).')'."\n";
		foreach ($m2['link'] as $key => $item)
		{
			$m1['link'][]=$item;
			$m1['content'][]=$m2['content'][$key];
			$m1['time'][]=$m2['time'][$key];
			$m1['fulltext'][count($m1['time'])-1]=$m2['fulltext'][$key];
			$m1['engage'][count($m1['time'])-1]=$m2['engage'][$key];
		}

		$time_start = microtime(true);
		$m2=get_facebook((($blog['order_keyword']=='')?$blog['order_name']:$blog['order_keyword']),$mstart,$mend,$text_lang,$mproxy);
		$time_end = microtime(true);
		echo '2 FACEBOOK (find: '.count($m2['link']).'; for: '.($time_end-$time_start).')'."\n";
		foreach ($m2['link'] as $key => $item)
		{
			$m1['link'][]=$item;
			$m1['content'][]=$m2['content'][$key];
			$m1['time'][]=$m2['time'][$key];
			$m1['fulltext'][count($m1['time'])-1]=$m2['fulltext'][$key];
			$m1['engage'][count($m1['time'])-1]=$m2['engage'][$key];
		}
		$time_start = microtime(true);
		$m3=getpost_google((($blog['order_keyword']=='')?$blog['order_name']:$blog['order_keyword']),$mstart,$mend,$text_lang,$mproxy);
		$time_end = microtime(true);
		echo '3 GOOGLE (find: '.count($m3['link']).'; for: '.($time_end-$time_start).')'."\n";
		foreach ($m3['link'] as $key => $item)
		{
			$m1['link'][]=$item;
			$m1['content'][]=$m3['content'][$key];
			$m1['time'][]=$m3['time'][$key];
		}
		$time_start = microtime(true);
		$m4=getpost_topsy((($blog['order_keyword']=='')?$blog['order_name']:$blog['order_keyword']),$mstart,$mend,$text_lang,$mproxy);
		$time_end = microtime(true);
		echo '4 TOPSY (find: '.count($m4['link']).'; for: '.($time_end-$time_start).')'."\n";
		foreach ($m4['link'] as $key => $item)
		{
			$m1['link'][]=$item;
			$m1['content'][]=$m4['content'][$key];
			$m1['time'][]=$m4['time'][$key];
		}
		$time_start = microtime(true);
		$m5=get_twitter((($blog['order_keyword']=='')?$blog['order_name']:$blog['order_keyword']),$mstart,$mend,$text_lang,$mproxy);
		$time_end = microtime(true);
		echo '5 TWITTER (find: '.count($m5['link']).'; for: '.($time_end-$time_start).')'."\n";
		foreach ($m5['link'] as $key => $item)
		{
			$m1['link'][]=$item;
			$m1['content'][]=$m5['content'][$key];
			$m1['time'][]=$m5['time'][$key];
			$m1['fulltext'][count($m1['time'])-1]=$m5['fulltext'][$key];
			$m1['engage'][count($m1['time'])-1]=$m5['engage'][$key];
		}
		$time_start = microtime(true);
		$m6=get_post_yt((($blog['order_keyword']=='')?$blog['order_name']:$blog['order_keyword']),$mstart,$mend,$text_lang,$mproxy);
		$time_end = microtime(true);
		echo '6 YOUTUBE (find: '.count($m6['link']).'; for: '.($time_end-$time_start).')'."\n";
		foreach ($m6['link'] as $key => $item)
		{
			$m1['link'][]=$item;
			$m1['content'][]=$m6['content'][$key];
			$m1['time'][]=$m6['time'][$key];
			$m1['fulltext'][count($m1['time'])-1]=$m6['fulltext'][$key];
		}
		$time_start = microtime(true);
		$m7=get_vkontakte((($blog['order_keyword']=='')?$blog['order_name']:$blog['order_keyword']),$mstart,$mend,$text_lang,$vk_tokens[$order_delta % 10],$mproxy);
		$time_end = microtime(true);
		echo '7 VKONTAKTE (find: '.count($m7['link']).'; for: '.($time_end-$time_start).')'."\n";
		foreach ($m7['link'] as $key => $item)
		{
			$m1['link'][]=$item;
			$m1['content'][]=$m7['content'][$key];
			$m1['time'][]=$m7['time'][$key];
			$m1['fulltext'][count($m1['time'])-1]=$m7['fulltext'][$key];
			$m1['engage'][count($m1['time'])-1]=$m7['engage'][$key];
		}
		$time_start = microtime(true);
		$m8=get_google_plus((($blog['order_keyword']=='')?$blog['order_name']:$blog['order_keyword']),$mstart,$mend,$text_lang,$mproxy);
		$time_end = microtime(true);
		echo '8 GOOGLE_PLUS (find: '.count($m8['link']).'; for: '.($time_end-$time_start).')'."\n";
		foreach ($m8['link'] as $key => $item)
		{
			$m1['link'][]=$item;
			$m1['content'][]=$m8['content'][$key];
			$m1['time'][]=$m8['time'][$key];
			$m1['nick'][count($m1['time'])-1]=$m8['nick'][$key];
			$m1['author_id'][count($m1['time'])-1]=$m8['author_id'][$key];
			$m1['ico'][count($m1['time'])-1]=$m8['ico'][$key];
			$m1['fulltext'][count($m1['time'])-1]=$m8['fulltext'][$key];
		}
		$time_start = microtime(true);
		// $m9=get_slideshare((($blog['order_keyword']=='')?$blog['order_name']:$blog['order_keyword']),$mstart,$mend,$text_lang,$mproxy);
		$time_end = microtime(true);
		echo '9 SLIDESHARE (find: '.count($m9['link']).'; for: '.($time_end-$time_start).')'."\n";
		foreach ($m9['link'] as $key => $item)
		{
			$m1['link'][]=$item;
			$m1['content'][]=$m9['content'][$key];
			$m1['time'][]=$m9['time'][$key];
		}
		//$m10=getpost_bing((($blog['order_keyword']=='')?$blog['order_name']:$blog['order_keyword']),$mstart,$mend,$text_lang,$mproxy);
		echo '10 BING (find: '.count($m10['link']).'; for: '.($time_end-$time_start).')'."\n";
		foreach ($m10['link'] as $key => $item)
		{
			$m1['link'][]=$item;
			$m1['content'][]=$m10['content'][$key];
			$m1['time'][]=$m10['time'][$key];
		}
		$time_start = microtime(true);
		// $m11=get_elastic((($blog['order_keyword']=='')?$blog['order_name']:$blog['order_keyword']),$mstart,$mend,$text_lang);
		$time_end = microtime(true);
		if (!isset($m_launc_elastic[mktime(date('H'),0,0,date('n'),date('j'),date('Y'))]))
		{
			$m_launc_elastic[mktime(date('H'),0,0,date('n'),date('j'),date('Y'))]=1;
			echo "\n".'http://localhost/tools/elastic.php?order_id='.intval($blog['order_id']).'&start='.$mstart.'&end='.$mend."\n";
			parseUrl('http://localhost/tools/elastic.php?order_id='.intval($blog['order_id']).'&start='.$mstart.'&end='.$mend);
		}
		file_get_contents('http://128.199.44.230/engine/api/addtask.php?order_id='.$blog['order_id'].'&host='.$config_server['server_ip']);
		echo '11 ELASTIC (find: '.count($m11['link']).'; for: '.($time_end-$time_start).')'."\n";
		foreach ($m11['link'] as $key => $item)
		{
			$m1['link'][]=$item;
			$m1['content'][]=$m11['content'][$key];
			$m1['time'][]=$m11['time'][$key];
		}
		$blog['order_keyword']=$temp_orderkw;
		//print_r($m1);	
		//die();
		if ($blog['order_end']>=time())
		{
			$cstart=mktime(0,0,0,date('n'),date('j'),date('Y'));
			$cend=mktime(0,0,0,date('n'),date('j'),date('Y'));
		}
		else
		{
			$cstart=$blog['order_end'];
			$cend=$blog['order_end'];
		}
		foreach ($m1['link'] as $key => $item)
		{
			if (($blog['order_analytics']==1) && (($m1['time'][$key]<$mstart-86400*2) || ($m1['time'][$key]>=$mend))) continue;
			if (($blog['order_analytics']==0) && (($m1['time'][$key]<$blog['order_start']) || ($m1['time'][$key]>=(($blog['order_end'])==0?time():($blog['order_end']+86400))))) continue;

			if (($debug_mode!='debug') && isset($rep[$item])) continue;

			$add_text='';
			if ($debug_mode!='debug')
			{
				if (mysql_num_rows($qw)!=0) continue;
				// if ((check_post(strip_tags($m1['content'][$key]),$blog['order_keyword'])==0) && (check_post(strip_tags($m1['fulltext'][$key]),$blog['order_keyword'])==0)) continue;
				if (check_post(strip_tags($m1['content'][$key].' '.$m1['fulltext'][$key]),$blog['order_keyword'])==0) continue;
				if (check_local(strip_tags($m1['content'][$key].' '.$m1['fulltext'][$key]),$text_lang)==0) continue;
			}
			else
			{
				if ($m1['flag'][$key]!='ya')
				{
					if (isset($rep[$item])) $add_text.=' REP';
					// if (mysql_num_rows($qw)!=0) continue;
					if (check_post($m1['content'][$key],$blog['order_keyword'])==0) $add_text.=' CHP';
					if (check_local($m1['content'][$key],$text_lang)==0) $add_text.=' LOC';
				}
				else
				{
					$add_text.=' YA';
					if (isset($rep[$item])) $add_text.=' REP';
					if ($text_lang=='en') continue;
					// if (mysql_num_rows($qw)!=0) continue;
					if (check_local($m1['content'][$key],$text_lang)==0) $add_text.=' LOC';
				}
			}
			//if (((mysql_num_rows($qw)==0) && (!in_array($item,$rep)) && (check_post($m1['content'][$key],$blog['order_keyword'])==1) && (check_local($m1['content'][$key],$text_lang)==1)) || (($text_lang!='en') && ($m1['flag'][$key]=='ya') && (mysql_num_rows($qw)==0) && (check_local($m1['content'][$key],$text_lang)==1)))			
			{
				echo $key.$m1['flag'][$key].' ';
				//echo $item['content']."\n";
				$rep[$item]=1;
				//echo $item.' '.check_filters($item,$filters)."\n";
				//continue;
				$check_filt=check_filters($item,$filters);
				// echo $item;
				// print_r($check_filt);
				if ($engage_src[$hn])
				{
					$mas_eng=json_decode($m1['engage'][$key],true);
					$engage_val=intval($mas_eng['likes'])+intval($mas_eng['comment'])+intval($mas_eng['retweet']);
				}
				if ($check_filt['value']==1)
				{
					$bb1=$check_filt['rb_info'];
					$hn=parse_url($item);
				    $hn=$hn['host'];
				    $ahn=explode('.',$hn);
				    $hn = $ahn[count($ahn)-2].'.'.$ahn[count($ahn)-1];
					$hh = $ahn[count($ahn)-2];
					$m2['content'][$key]=$m1['content'][$key];
					if ($hn=='google.com')
					{
						$quser=$db->query('SELECT blog_id FROM robot_blogs2 WHERE blog_login=\''.addslashes($m1['author_id'][$key]).'\' LIMIT 1');
						if ($db->num_rows($quser)==0)
						{
							$db->query('INSERT INTO robot_blogs2 (blog_link,blog_login,blog_nick,blog_ico) VALUES (\'plus.google.com\',\''.addslashes($m1['author_id'][$key]).'\',\''.addslashes($m1['nick'][$key]).'\',\''.addslashes($m1['ico'][$key]).'\')');
							$bb1['blog_id']=$db->insert_id();
						}
						else
						{
							$bb1=$db->fetch($quser);
						}
					}

					if ($hn=='twitter.com')	$m2['content'][$key]=preg_replace('/<a.*?href\=[\'\"](.*?)[\'\"].*?>.*?<\/a>/isu','$1',$m2['content'][$key]);
					$isspam=0;
					if (isset($spams[$bb1['blog_id']]))	$isspam=1;
					$cch++;
					if ($debug_mode=='debug') $m2['content'][$key]=trim($add_text).' '.$m2['content'][$key];
					if ($m1['time'][$key]<$cstart) $cstart=mktime(0,0,0,date('n',$m1['time'][$key]),date('j',$m1['time'][$key]),date('Y',$m1['time'][$key]));

					unset($queue_item);
					$queue_item['order_id']=$blog['order_id'];
					$queue_item['post_link']=$item;
					$queue_item['post_host']=$hn;
					$queue_item['post_time']=$m1['time'][$key];
					$queue_item['post_content']=$m2['content'][$key];
					$queue_item['post_engage']=($engage_src[$hn]!=1?0:($m1['engage'][$key]!=''?$engage_val:-1));
					$queue_item['post_advengage']=($engage_src[$hn]!=1?'':json_decode($m1['engage'][$key],true));
					$queue_item['blog_id']=intval($bb1['blog_id']);
					$queue_item['post_spam']=$isspam;
					$queue_item['post_ful_com']=$m1['fulltext'][$key];
					$queue_item['blog_location']=$check_filt['rb_info']['loc'];
					$queue_item['blog_last_update']=$check_filt['rb_info']['last_update'];
					$queue_item['blog_age']=$check_filt['rb_info']['age'];
					$queue_item['blog_gender']=$check_filt['rb_info']['gender'];
					$queue_item['order_keyword']=$temp_orderkw;
					$queue_item['order_name']=$blog['order_name'];
					$queue_item['order_start']=$blog['order_start'];
					$queue_item['order_end']=$blog['order_end'];
					$queue_item['order_settings']=$blog['order_settings'];
					$queue_item['user_id']=$blog['user_id'];
					$queue_item['tariff_id']=$tariff['tariff_id'];
					$queue_item['ut_date']=$tariff['ut_date'];
					$queue_item['ut_id']=$tariff['ut_id'];
					// print_r($queue_item);
					$redis->sAdd('prev_queue',json_encode($queue_item));
					// $db->query('INSERT INTO blog_post_prev (order_id,post_link,post_host,post_time,post_content,'.($engage_src[$hn]!=1?'post_engage,':($m1['engage'][$key]!=''?'post_engage,post_advengage,':'')).'blog_id'.((($debug_mode=='debug')&&(trim($add_text)!=''))||($isspam==1)?',post_spam':'').',post_ful_com) VALUES ('.$blog['order_id'].',\''.addslashes($item).'\',\''.addslashes($hn).'\',\''.$m1['time'][$key].'\',\''.addslashes($m2['content'][$key]).'\','.($engage_src[$hn]!=1?'0,':($m1['engage'][$key]!=''?$engage_val.',\''.$m1['engage'][$key].'\',':'')).intval($bb1['blog_id']).((($debug_mode=='debug')&&(trim($add_text)!=''))||($isspam==1)?',1':'').',\''.addslashes($m1['fulltext'][$key]).'\')');
				}
				elseif ($check_filt['value']==2)
				{
					$bb1=$check_filt['rb_info'];
					$isspam=0;
					if (isset($spams[$bb1['blog_id']]))
					{
						$isspam=1;
					}
					$hn=parse_url($item);
				    $hn=$hn['host'];
				    $ahn=explode('.',$hn);
				    $hn = $ahn[count($ahn)-2].'.'.$ahn[count($ahn)-1];
					$hh = $ahn[count($ahn)-2];
					if ($hn=='google.com')
					{
						$quser=$db->query('SELECT blog_id FROM robot_blogs2 WHERE blog_login=\''.addslashes($m1['author_id'][$key]).'\' LIMIT 1');
						if ($db->num_rows($quser)==0)
						{
							$db->query('INSERT INTO robot_blogs2 (blog_link,blog_login,blog_nick,blog_ico) VALUES (\'plus.google.com\',\''.addslashes($m1['author_id'][$key]).'\',\''.addslashes($m1['nick'][$key]).'\',\''.addslashes($m1['ico'][$key]).'\')');
							$bb1['blog_id']=$db->insert_id();
						}
						else
						{
							$bb1=$db->fetch($quser);
						}
					}
					unset($queue_item);
					$queue_item['order_id']=$blog['order_id'];
					$queue_item['post_link']=$item;
					$queue_item['post_host']=$hn;
					$queue_item['post_time']=$m1['time'][$key];
					$queue_item['post_content']=$m2['content'][$key];
					$queue_item['post_engage']=($engage_src[$hn]!=1?0:($m1['engage'][$key]!=''?$engage_val:-1));
					$queue_item['post_advengage']=($engage_src[$hn]!=1?'':json_decode($m1['engage'][$key],true));
					$queue_item['blog_id']=intval($bb1['blog_id']);
					$queue_item['post_spam']=$isspam;
					$queue_item['post_ful_com']=$m1['fulltext'][$key];
					$queue_item['blog_location']=$check_filt['rb_info']['loc'];
					$queue_item['blog_last_update']=$check_filt['rb_info']['last_update'];
					$queue_item['blog_age']=$check_filt['rb_info']['age'];
					$queue_item['blog_gender']=$check_filt['rb_info']['gender'];
					$queue_item['order_keyword']=$temp_orderkw;
					$queue_item['order_name']=$blog['order_name'];
					$queue_item['order_start']=$blog['order_start'];
					$queue_item['order_end']=$blog['order_end'];
					$queue_item['order_settings']=$blog['order_settings'];
					$queue_item['user_id']=$blog['user_id'];
					$queue_item['tariff_id']=$tariff['tariff_id'];
					$queue_item['ut_date']=$tariff['ut_date'];
					$queue_item['ut_id']=$tariff['ut_id'];
					// print_r($queue_item);
					$redis->sAdd('prev_queue',json_encode($queue_item));
					// $db->query('INSERT INTO blog_post_prev (order_id,post_link,post_host,post_time,post_content,'.($engage_src[$hn]!=1?'post_engage,':($m1['engage'][$key]!=''?'post_engage,post_advengage,':'')).'blog_id'.(($isspam==1)?',post_spam':'').',post_ful_com) VALUES ('.$blog['order_id'].',\''.addslashes($item).'\',\''.addslashes($hn).'\',\''.$m1['time'][$key].'\',\''.addslashes($m1['content'][$key]).'\','.($engage_src[$hn]!=1?'0,':($m1['engage'][$key]!=''?$engage_val.',\''.$m1['engage'][$key].'\',':'')).intval($bb1['blog_id']).(($isspam==1)?',1':'').',\''.addslashes($m1['fulltext'][$key]).'\')');
				}
			}
		}
		unset($rep);
		$qw=$db->query('UPDATE blog_orders SET third_sources='.time().' WHERE order_id='.$blog['order_id']);
	}
	echo 'idle...'."\n";
	sleep(10);
	if ($debug_mode=='debug') die();
}
?>
