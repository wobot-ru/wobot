<?
require_once('/var/www/daemon/com/config.php');
require_once('/var/www/daemon/com/func.php');
require_once('/var/www/daemon/com/db.php');
require_once('/var/www/daemon/bot/kernel.php');

function parseURLproxyTOPSY( $url,$proxy )
{
	$muagents[]='Mozilla/5.0 (Macintosh; Intel Mac OS X 10_8_2) AppleWebKit/537.17 (KHTML, like Gecko) Chrome/24.0.1309.0 Safari/537.17';
	$muagents[]='Mozilla/5.0 (Windows NT 6.2; WOW64) AppleWebKit/537.15 (KHTML, like Gecko) Chrome/24.0.1295.0 Safari/537.15';
	$muagents[]='Mozilla/5.0 (Windows NT 6.2; WOW64) AppleWebKit/537.14 (KHTML, like Gecko) Chrome/24.0.1292.0 Safari/537.14';
	$muagents[]='Mozilla/5.0 (Windows NT 6.2; WOW64) AppleWebKit/537.13 (KHTML, like Gecko) Chrome/24.0.1290.1 Safari/537.13';
	$muagents[]='Mozilla/5.0 (Windows NT 6.2) AppleWebKit/537.13 (KHTML, like Gecko) Chrome/24.0.1290.1 Safari/537.13';
	$muagents[]='Mozilla/5.0 (Macintosh; Intel Mac OS X 10_8_2) AppleWebKit/537.13 (KHTML, like Gecko) Chrome/24.0.1290.1 Safari/537.13';
	$muagents[]='Mozilla/5.0 (Macintosh; Intel Mac OS X 10_7_4) AppleWebKit/537.13 (KHTML, like Gecko) Chrome/24.0.1290.1 Safari/537.13';
	$uagent=$muagents[rand(0,count($muagents)-1)];
	$ch = curl_init( $url );
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);   // возвращает веб-страницу
	curl_setopt($ch, CURLOPT_HEADER, 0);           // не возвращает заголовки
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);   // переходит по редиректам
	curl_setopt($ch, CURLOPT_ENCODING, "");        // обрабатывает все кодировки
	curl_setopt($ch, CURLOPT_USERAGENT, $uagent);  // useragent
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5); // таймаут соединения
	curl_setopt($ch, CURLOPT_TIMEOUT, 5);        // таймаут ответа
	curl_setopt($ch, CURLOPT_MAXREDIRS, 10);       // останавливаться после 10-ого редиректа
	if ($proxy!='')
	{
	//echo 'proxy='.$proxy;
		// curl_setopt($ch, CURLOPT_PROXY, $proxy);
	}
	else
	{
		// return $nnl;
	}
	curl_setopt ($ch, CURLOPT_COOKIE, 'adult_concepts=1' ); 
	$content = curl_exec( $ch );
	$err     = curl_errno( $ch );
	$errmsg  = curl_error( $ch );
	$header  = curl_getinfo( $ch );
	//print_r($header);
	return $content;
}
//$cont=parseURLproxyTOPSY( 'http://otter.topsy.com/search.json?q=%D0%BF%D1%83%D1%82%D0%B8%D0%BD&order=date&perpage=100&allow_lang=ru&locale=ru&offset=0&mintime=1353787200&maxtime=1354046400&apikey=9F13B43332174FAC8F6948EB7A0278D5','49.0.174.208:80' );
//echo $cont;
//print_r($cont);
//die();
//error_reporting(E_ERROR | E_WARNING | E_PARSE);
error_reporting(E_ERROR);
ignore_user_abort(true);
set_time_limit(0);
ini_set('max_execution_time',0);
ini_set('default_charset','utf-8');
ob_implicit_flush();

$db = new database();
$db->connect();

//каждую свою правку прокомментировал
//работает заебись осталось оформить как демона, добавить выдачу как для логов и засунуть на сервер

//необходимо для безопасности работы функции mktime
date_default_timezone_set ( 'Europe/Moscow' ); // локальное время на сервере
// $api_key='&apikey=2686541C83304E5E875CA3FE43078DF0';

// $apikeys[0]='&apikey=9F13B43332174FAC8F6948EB7A0278D5';
// $apikeys[1]='&apikey=228DB09728BB43D9B93574F49C641F63';
// $apikeys[2]='&apikey=49BC1CF4E7C64B48AFE16A0E785F153C';
// $apikeys[3]='&apikey=2686541C83304E5E875CA3FE43078DF0';
// $apikeys[4]='&apikey=8CF123F695DF4570B1D5F99F1FB3A8C2';

$apikeys[0]='&apikey=09C43A9B270A470B8EB8F2946A9369F3';
$apikeys[1]='&apikey=09C43A9B270A470B8EB8F2946A9369F3';
$apikeys[2]='&apikey=09C43A9B270A470B8EB8F2946A9369F3';
$apikeys[3]='&apikey=09C43A9B270A470B8EB8F2946A9369F3';
$apikeys[4]='&apikey=09C43A9B270A470B8EB8F2946A9369F3';

function check_topsy_content($cont)
{
	return intval(json_decode($cont,true));
}

function getpost_topsy($text,$ts,$te,$geo,$proxys)
{
	global $api_key,$apikeys;
	$tmp_keyword=$text;
	$text=preg_replace('/[\(\)]/is','',$text);
	$i_proxy=0;
	do
	{
		//------ALL------
		do
		{
			$cont=parseURLproxyTOPSY('http://otter.topsy.com/search.json?q='.urlencode(preg_replace('/[\|\&]+/isu',' OR ',preg_replace('/[^а-яА-Яa-zA-Z\|\.\&\0-9]/isu',' ',$text))).'&order=date&perpage=100&allow_lang='.$geo.'&locale='.$geo.'&offset='.($i*100).'&mintime='.$ts.'&maxtime='.$te.$apikeys[date('s')%5],$proxys[$i_proxy]);
			if (check_topsy_content($cont)==0)
			{
				echo '*';
				$i_proxy++;
				if ($i_proxy==7) $api_key='';
			}
		}
		while ((check_topsy_content($cont)==0) && ($i_proxy<count($proxys)));
		//echo 'http://otter.topsy.com/search.json?q='.urlencode(preg_replace('/[\|\&]+/isu',' OR ',preg_replace('/[^а-яА-Яa-zA-Z\|\.\&\0-9]/isu',' ',$text))).'&order=date&perpage=100&allow_lang='.$geo.'&locale='.$geo.'&offset='.($i*100).'&mintime='.$ts.'&maxtime='.$te.$api_key;
		echo 1;
		//echo 'http://otter.topsy.com/search.json?q='.urlencode(preg_replace('/\|/isu',' OR ',preg_replace('/[^а-яА-Яa-zA-Z\|]/isu',' ',$text))).'&window=w&perpage=100&allow_lang='.$geo.'&locale='.$geo.'&offset='.($i*100).'&mintime='.$ts.'&maxtime='.$te."\n";
		$mas=json_decode($cont,true);
		//print_r($mas);
		foreach ($mas['response']['list'] as $key => $item)
		{
			$count_topsy++;
			if (check_post($item['content'],$tmp_keyword)==0) continue;
			if (($ts<$item['trackback_date']) && ($te>$item['trackback_date']))
			{
				$outmas['author'][]=$item['trackback_author_name'];
				$outmas['auth_link'][]=$item['trackback_author_url'];
				$outmas['time'][]=$item['trackback_date'];
				$outmas['link'][]=preg_replace('/status\//is','statuses/',$item['url']);
				$outmas['content'][]=$item['content'];
				if (count($outmas['time'])>100) $outmas=post_slice($outmas);
			}
		}
		//print_r($outmas);
		//------video-----
		do
		{
			$cont=parseURLproxyTOPSY('http://otter.topsy.com/search.json?q='.urlencode(preg_replace('/[\|\&]+/isu',' OR ',preg_replace('/[^а-яА-Яa-zA-Z\|\.\&\0-9]/isu',' ',$text))).'&order=date&perpage=100&allow_lang='.$geo.'&locale='.$geo.'&offset='.($i*100).'&mintime='.$ts.'&maxtime='.$te.'&type=video'.$apikeys[date('s')%5],$proxys[$i_proxy]);
			if (check_topsy_content($cont)==0)
			{
				echo '*';
				$i_proxy++;
			}
		}
		while ((check_topsy_content($cont)==0) && ($i_proxy<count($proxys)));
		echo 2;
		//echo 'http://otter.topsy.com/search.json?q='.urlencode(preg_replace('/\|/isu',' OR ',preg_replace('/[^а-яА-Яa-zA-Z\|]/isu',' ',$text))).'&window=w&perpage=100&allow_lang='.$geo.'&locale='.$geo.'&offset='.($i*100).'&mintime='.$ts.'&maxtime='.$te.'&type=video'."\n";
		$mas=json_decode($cont,true);
		foreach ($mas['response']['list'] as $key => $item)
		{
			$count_topsy++;
			if (check_post($item['content'],$tmp_keyword)==0) continue;
			if (($ts<$item['trackback_date']) && ($te>$item['trackback_date']) && (!in_array($item['url'],$outmas['link'])))
			{
				$outmas['author'][]=$item['trackback_author_name'];
				$outmas['auth_link'][]=$item['trackback_author_url'];
				$outmas['time'][]=$item['trackback_date'];
				$outmas['link'][]=preg_replace('/status\//is','statuses/',$item['url']);
				$outmas['content'][]=$item['content'];
				if (count($outmas['time'])>100) $outmas=post_slice($outmas);
			}
		}
		//print_r($outmas);
		//------tweet-----
		do
		{
			$cont=parseURLproxyTOPSY('http://otter.topsy.com/search.json?q='.urlencode(preg_replace('/[\|\&]+/isu',' OR ',preg_replace('/[^а-яА-Яa-zA-Z\|\.\&\0-9]/isu',' ',$text))).'&order=date&perpage=100&allow_lang='.$geo.'&locale='.$geo.'&offset='.($i*100).'&mintime='.$ts.'&maxtime='.$te.'&type=tweet'.$apikeys[date('s')%5],$proxys[$i_proxy]);
			if (check_topsy_content($cont)==0)
			{
				echo '*';
				$i_proxy++;
			}
		}
		while ((check_topsy_content($cont)==0) && ($i_proxy<count($proxys)));
		echo 3;
		//echo 'http://otter.topsy.com/search.json?q='.urlencode(preg_replace('/\|/isu',' OR ',preg_replace('/[^а-яА-Яa-zA-Z\|]/isu',' ',$text))).'&window=w&perpage=100&allow_lang='.$geo.'&locale='.$geo.'&offset='.($i*100).'&mintime='.$ts.'&maxtime='.$te.'&type=tweet'."\n";
		$mas=json_decode($cont,true);
		foreach ($mas['response']['list'] as $key => $item)
		{
			$count_topsy++;
			if (check_post($item['content'],$tmp_keyword)==0) continue;
			if (($ts<$item['trackback_date']) && ($te>$item['trackback_date']) && (!in_array($item['url'],$outmas['link'])))
			{
				$outmas['author'][]=$item['trackback_author_name'];
				$outmas['auth_link'][]=$item['trackback_author_url'];
				$outmas['time'][]=$item['trackback_date'];
				$outmas['link'][]=preg_replace('/status\//is','statuses/',$item['url']);
				$outmas['content'][]=$item['content'];
				if (count($outmas['time'])>100) $outmas=post_slice($outmas);
			}
		}
		//print_r($outmas);
		//------image-----
		do
		{
			$cont=parseURLproxyTOPSY('http://otter.topsy.com/search.json?q='.urlencode(preg_replace('/[\|\&]+/isu',' OR ',preg_replace('/[^а-яА-Яa-zA-Z\|\.\&\0-9]/isu',' ',$text))).'&order=date&perpage=100&allow_lang='.$geo.'&locale='.$geo.'&offset='.($i*100).'&mintime='.$ts.'&maxtime='.$te.'&type=image'.$apikeys[date('s')%5],$proxys[$i_proxy]);
			if (check_topsy_content($cont)==0)
			{
				echo '*';
				$i_proxy++;
			}
		}
		while ((check_topsy_content($cont)==0) && ($i_proxy<count($proxys)));
		echo 4;
		//echo 'http://otter.topsy.com/search.json?q='.urlencode(preg_replace('/\|/isu',' OR ',preg_replace('/[^а-яА-Яa-zA-Z\|]/isu',' ',$text))).'&window=w&perpage=100&allow_lang='.$geo.'&locale='.$geo.'&offset='.($i*100).'&mintime='.$ts.'&maxtime='.$te.'&type=image'."\n";
		$mas=json_decode($cont,true);
		foreach ($mas['response']['list'] as $key => $item)
		{
			$count_topsy++;
			if (check_post($item['content'],$tmp_keyword)==0) continue;
			if (($ts<$item['trackback_date']) && ($te>$item['trackback_date']) && (!in_array($item['url'],$outmas['link'])))
			{
				$outmas['author'][]=$item['trackback_author_name'];
				$outmas['auth_link'][]=$item['trackback_author_url'];
				$outmas['time'][]=$item['trackback_date'];
				$outmas['link'][]=preg_replace('/status\//is','statuses/',$item['url']);
				$outmas['content'][]=$item['content'];
				if (count($outmas['time'])>100) $outmas=post_slice($outmas);
			}
		}
		//print_r($outmas);
		//------video------
		do
		{
			$cont=parseURLproxyTOPSY('http://otter.topsy.com/search.json?q='.urlencode(preg_replace('/[\|\&]+/isu',' OR ',preg_replace('/[^а-яА-Яa-zA-Z\|\.\&\0-9]/isu',' ',$text))).'&order=date&perpage=100&allow_lang='.$geo.'&locale='.$geo.'&offset='.($i*100).'&mintime='.$ts.'&maxtime='.$te.'&type=link'.$apikeys[date('s')%5],$proxys[$i_proxy]);
			if (check_topsy_content($cont)==0)
			{
				echo '*';
				$i_proxy++;
			}
		}
		while ((check_topsy_content($cont)==0) && ($i_proxy<count($proxys)));
		echo 5;
		//echo 'http://otter.topsy.com/search.json?q='.urlencode(preg_replace('/\|/isu',' OR ',preg_replace('/[^а-яА-Яa-zA-Z\|]/isu',' ',$text))).'&window=w&perpage=100&allow_lang='.$geo.'&locale='.$geo.'&offset='.($i*100).'&mintime='.$ts.'&maxtime='.$te.'&type=video'."\n";
		$mas=json_decode($cont,true);
		foreach ($mas['response']['list'] as $key => $item)
		{
			$count_topsy++;
			if (check_post($item['content'],$tmp_keyword)==0) continue;
			if (($ts<$item['trackback_date']) && ($te>$item['trackback_date']) && (!in_array($item['url'],$outmas['link'])))
			{
				$outmas['author'][]=$item['trackback_author_name'];
				$outmas['auth_link'][]=$item['trackback_author_url'];
				$outmas['time'][]=$item['trackback_date'];
				$outmas['link'][]=preg_replace('/status\//is','statuses/',$item['url']);
				$outmas['content'][]=$item['content'];
				if (count($outmas['time'])>100) $outmas=post_slice($outmas);
			}
		}
		$i++;
		//sleep(1);
	}
	while ($i<3);
	add_source_log('topsy',intval($count_topsy));
	echo "\n";
	//print_r($outmas);
	return $outmas;
}
//(("windows mobile"|"виндоуз мобайл"|"виндовс мобайл"|"win mobile 7"|"win 7 mobile"|"mobile windows 7"|"win7 mobile")|((манго|mango)&(телефон|смартфон|коммуникатор)))~продать~продажа~green
//getpost_topsy('путин',mktime(0,0,0,11,25,2012),mktime(0,0,0,11,28,2012),'ru',array('49.0.174.208:80','109.104.168.94:3128','83.220.79.68:3128','195.64.211.173:3128'));

?>