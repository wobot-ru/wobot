<?

require_once('/var/www/daemon/com/config.php');
require_once('/var/www/daemon/com/db.php');
require_once('/var/www/daemon/bot/kernel.php');
require_once('/var/www/daemon/com/users.php');
require_once('cfg.php');//конфиг токенов
require_once('ch.php');//чекер постов

$fp = fopen('/var/www/pids/typhoon'.$order_delta.'.pid', 'w');
fwrite($fp, getmypid());
fclose($fp);

$redis=new Redis() or die("Can'f load redis module.");
$redis->connect('127.0.0.1');

/*require_once('yblogs.php');
require_once('facebook.php');
require_once('google.php');
require_once('topsy.php');
require_once('twitter.php');
require_once('youtube.php');
require_once('vkontakte2.php');
require_once('google_plus/get_gp.php');
require_once('slideshare.php');
require_once('bing.php');*/

date_default_timezone_set('Europe/Moscow');
error_reporting(E_ERROR);
ignore_user_abort(true);
set_time_limit(0);
ini_set('max_execution_time',0);
ini_set('default_charset','utf-8');
ob_implicit_flush();
$db = new database();
$db->connect();

$memcache_obj = memcache_connect('localhost', 11211);

//обнуление текущих номеров прокси для потоков
memcache_set($memcache_obj, 'yblogs', 0, 0, time()+86400);
memcache_set($memcache_obj, 'facebook', 0, 0, time()+86400);
memcache_set($memcache_obj, 'google', 0, 0, time()+86400);
memcache_set($memcache_obj, 'topsy', 0, 0, time()+86400);
memcache_set($memcache_obj, 'twitter', 0, 0, time()+86400);
memcache_set($memcache_obj, 'youtube', 0, 0, time()+86400);
memcache_set($memcache_obj, 'vkontakte2', 0, 0, time()+86400);
memcache_set($memcache_obj, 'google_plus', 0, 0, time()+86400);
memcache_set($memcache_obj, 'bing', 0, 0, time()+86400);
//memcache_set($memcache_obj, 'solr', 0, 0, time()+86400);
//memcache_set($memcache_obj, 'google_plus', 0, 0, time()+86400);

$mode=intval($_SERVER['argv'][1]);

while (1)
{
	//обновление списка прокси
	$mproxy=json_decode($redis->get('proxy_list'),true);
	memcache_set($memcache_obj, 'mproxy', json_encode($mproxy), 0, time()+86400);
	if (!$db->ping()) {
		echo "MYSQL disconnected, reconnect after 10 sec...\n";
		sleep(10);
		$db->connect();
	}
	echo intval(date('H',time()));
	$ressec=$db->query('SELECT order_id,user_id,ut_id,order_name,order_keyword,order_start,order_end,third_sources,order_lang,order_analytics,order_spam,order_settings FROM blog_orders WHERE '.($mode!=1?'user_id!=145 AND':'user_id=145 AND').' third_sources=1 and (order_end>='.mktime(0,0,0,date('n')-1,date('j')-1,date('Y')).') AND user_id!=0 ORDER BY order_id DESC');
	//$ressec=$db->query('SELECT order_id,order_name,order_keyword,order_start,order_end,third_sources,order_lang,order_analytics,order_spam FROM blog_orders WHERE order_id=1392 ORDER BY order_id DESC');

	echo 'SELECT order_id,order_name,order_keyword,order_start,order_end,third_sources,order_lang,order_analytics,order_spam,order_settings FROM blog_orders WHERE '.($mode!=1?'user_id!=145 AND':'user_id=145 AND').' third_sources=1 and (order_end>='.mktime(0,0,0,date('n')-1,date('j')-1,date('Y')).') AND user_id!=0 ORDER BY order_id DESC';
	while($blog=$db->fetch($ressec))
	{
		$qtariff=$db->query('SELECT * FROM user_tariff WHERE user_id='.$blog['user_id'].' AND ut_id='.$blog['ut_id']);
		//echo 'SELECT * FROM user_tariff WHERE user_id='.$blog['user_id'].' AND ut_id='.$blog['ut_id'];
		$tariff=$db->fetch($qtariff);
		if (($tariff['tariff_id']==10) && ($tariff['ut_date']<=0)) continue;

		if (memcache_get($memcache_obj, 'order_typhoon_'.$blog['order_id'])==1)
		{
			echo $blog['order_id'].' continued'."\n";
			continue;
		}
		$spams=json_decode($blog['order_spam'],true);
		if ($blog['third_sources']>=$blog['order_start'])
		{
			if ($blog['third_sources']!=0)
			{
				$mstart=$blog['third_sources'];
			}
			else
			{
				$mstart = $blog['order_start'];
			}
		}
		else
		{
			$mstart=$blog['order_start'];
		}
		if ($blog['order_end']>=time())
		{
			$mend=time();
		}
		else
		{
			if ($blog['order_end']!=0)
			{
				$mend=mktime(0,0,0,date('n',$blog['order_end']),date('j',$blog['order_end'])+1,date('Y',$blog['order_end']));//$blog['order_end']+86400;
			}
			else
			{
				$mend=time();
			}
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
		echo 'TEXTLANG='.$text_lang."\n";
		$mstart=mktime(0,0,0,date('n',$mstart),date('j',$mstart)-1,date('Y',$mstart));
		if ($mstart<$blog['order_start'])
		{
			$mstart=$blog['order_start'];
		}
		echo $mstart.' '.$mend.' '.$blog['order_id']."\n";
		
		//$m1=getpost_yandex((($blog['order_keyword']=='')?$blog['order_name']:$blog['order_keyword']),$mstart,$mend,$text_lang,$mproxy);
		$task=array(
			'text'=> (($blog['order_keyword']=='')?$blog['order_name']:$blog['order_keyword']),
			'mstart' => $mstart,
			'mend' => $mend,
			'geo' => $text_lang,
			'blog' => $blog
		);
		print_r($task);
		// die();
		memcache_set($memcache_obj, 'typhoon_'.$blog['order_id'], json_encode($task), 0, time()+86400);
		//echo memcache_get($memcache_obj, 'typhoon_'.$blog['order_id']);
		//die();
		$yandex_service=parseUrl('http://localhost/daemon/fsearch3/service.php?type=yblogs&order_id='.$blog['order_id']);
		echo '1 YANDEX'.$yandex_service."\n";
		
		//$m2=get_facebook((($blog['order_keyword']=='')?$blog['order_name']:$blog['order_keyword']),$mstart,$mend,$text_lang,$mproxy);
		$facebook_service=parseUrl('http://localhost/daemon/fsearch3/service.php?type=facebook&order_id='.$blog['order_id']);
		echo '2 FACEBOOK'.$facebook_service."\n";
		/*foreach ($m2['link'] as $key => $item)
		{
			$m1['link'][]=$item;
			$m1['content'][]=$m2['content'][$key];
			$m1['time'][]=$m2['time'][$key];
		}*/
		
		//$m3=getpost_google((($blog['order_keyword']=='')?$blog['order_name']:$blog['order_keyword']),$mstart,$mend,$text_lang,$mproxy);
		$google_service=parseUrl('http://localhost/daemon/fsearch3/service.php?type=google&order_id='.$blog['order_id']);
		echo '3 GOOGLE'.$google_service."\n";
		/*foreach ($m3['link'] as $key => $item)
		{
			$m1['link'][]=$item;
			$m1['content'][]=$m3['content'][$key];
			$m1['time'][]=$m3['time'][$key];
		}*/
		
		//$m4=getpost_topsy((($blog['order_keyword']=='')?$blog['order_name']:$blog['order_keyword']),$mstart,$mend,$text_lang,$mproxy);
		$topsy_service=parseUrl('http://localhost/daemon/fsearch3/service.php?type=topsy&order_id='.$blog['order_id']);
		echo '4 TOPSY'.$topsy_service."\n";
		/*foreach ($m4['link'] as $key => $item)
		{
			$m1['link'][]=$item;
			$m1['content'][]=$m4['content'][$key];
			$m1['time'][]=$m4['time'][$key];
		}*/

		//$m5=get_twitter((($blog['order_keyword']=='')?$blog['order_name']:$blog['order_keyword']),$mstart,$mend,$text_lang,$mproxy);
		$twitter_service=parseUrl('http://localhost/daemon/fsearch3/service.php?type=twitter&order_id='.$blog['order_id']);
		echo '5 TWITTER'.$twitter_service."\n";
		$twitter_service=parseUrl('http://localhost/daemon/fsearch3/service.php?type=typhoon_twitter&order_id='.$blog['order_id']);
		echo '5 TYPHOON TWITTER'.$twitter_service."\n";
		/*foreach ($m5['link'] as $key => $item)
		{
			$m1['link'][]=$item;
			$m1['content'][]=$m5['content'][$key];
			$m1['time'][]=$m5['time'][$key];
		}*/
		
		//$m6=get_post_yt((($blog['order_keyword']=='')?$blog['order_name']:$blog['order_keyword']),$mstart,$mend,$text_lang,$mproxy);
		$youtube_service=parseUrl('http://localhost/daemon/fsearch3/service.php?type=youtube&order_id='.$blog['order_id']);
		echo '6 YOUTUBE'.$youtube_service."\n";
		/*foreach ($m6['link'] as $key => $item)
		{
			$m1['link'][]=$item;
			$m1['content'][]=$m6['content'][$key];
			$m1['time'][]=$m6['time'][$key];
		}*/
		
		//$m7=get_vkontakte((($blog['order_keyword']=='')?$blog['order_name']:$blog['order_keyword']),$mstart,$mend,$text_lang,$vk_tokens[$order_delta % 10],$mproxy);
		$vkontakte_service=parseUrl('http://localhost/daemon/fsearch3/service.php?type=vkontakte2&order_id='.$blog['order_id']);
		echo '7 VKONTAKTE'.$vkontakte_service."\n";
		/*foreach ($m7['link'] as $key => $item)
		{
			$m1['link'][]=$item;
			$m1['content'][]=$m7['content'][$key];
			$m1['time'][]=$m7['time'][$key];
		}*/

		$gp_service=parseUrl('http://localhost/daemon/fsearch3/service.php?type=google_plus&order_id='.$blog['order_id']);
		//$m10=getpost_bing((($blog['order_keyword']=='')?$blog['order_name']:$blog['order_keyword']),$mstart,$mend,$text_lang,$mproxy);
		echo '8 GOOGLE_PLUS'.$gp_service."\n";

		
		$slideshare_service=parseUrl('http://localhost/daemon/fsearch3/service.php?type=slideshare&order_id='.$blog['order_id']);
		//$m9=get_slideshare((($blog['order_keyword']=='')?$blog['order_name']:$blog['order_keyword']),$mstart,$mend,$text_lang,$mproxy);
		echo '9 SLIDESHARE'.$slideshare_service."\n";
		/*foreach ($m9['link'] as $key => $item)
		{
			$m1['link'][]=$item;
			$m1['content'][]=$m9['content'][$key];
			$m1['time'][]=$m9['time'][$key];
		}*/
		
		$bing_service=parseUrl('http://localhost/daemon/fsearch3/service.php?type=bing&order_id='.$blog['order_id']);
		//$m10=getpost_bing((($blog['order_keyword']=='')?$blog['order_name']:$blog['order_keyword']),$mstart,$mend,$text_lang,$mproxy);
		echo '10 BING'.$bing_service."\n";
		/*foreach ($m10['link'] as $key => $item)
		{
			$m1['link'][]=$item;
			$m1['content'][]=$m10['content'][$key];
			$m1['time'][]=$m10['time'][$key];
		}*/

		// $solr_service=parseUrl('http://localhost/daemon/fsearch3/service.php?type=solr&order_id='.$blog['order_id']);
		// //$m10=getpost_bing((($blog['order_keyword']=='')?$blog['order_name']:$blog['order_keyword']),$mstart,$mend,$text_lang,$mproxy);
		// echo '11 SOLR'.$solr_service."\n";

		$elastic_service=parseUrl('http://localhost/daemon/fsearch3/service.php?type=elastic&order_id='.$blog['order_id']);
		//$m10=getpost_bing((($blog['order_keyword']=='')?$blog['order_name']:$blog['order_keyword']),$mstart,$mend,$text_lang,$mproxy);
		echo '12 ELASTIC'.$elastic_service."\n";

		/*foreach ($m10['link'] as $key => $item)
		{
			$m1['link'][]=$item;
			$m1['content'][]=$m10['content'][$key];
			$m1['time'][]=$m10['time'][$key];
		}*/

		memcache_set($memcache_obj, 'order_typhoon_'.$blog['order_id'], 1, 0, time()+86400);
	}
	echo 'idle...'."\n";
	sleep(5);
}
?>
