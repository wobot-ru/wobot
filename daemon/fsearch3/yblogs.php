<?
require_once('/var/www/daemon/com/config.php');
require_once('/var/www/daemon/com/func.php');
require_once('/var/www/daemon/com/db.php');
require_once('/var/www/daemon/bot/kernel.php');

//error_reporting(E_ERROR | E_WARNING | E_PARSE);
// error_reporting(E_ERROR);
ignore_user_abort(true);
set_time_limit(0);
ini_set('max_execution_time',0);
ini_set('default_charset','utf-8');
ob_implicit_flush();

// $redis=new Redis() or die("Can'f load redis module.");
// $redis->connect('127.0.0.1');

$db = new database();
$db->connect();

//каждую свою правку прокомментировал
//работает заебись осталось оформить как демона, добавить выдачу как для логов и засунуть на сервер

//необходимо для безопасности работы функции mktime
date_default_timezone_set ( 'Europe/Moscow' ); // локальное время на сервере

/*function parseURL($url) // функция загрузки страницы из инета по ссылке
{
        // create curl resource
        $ch = curl_init();

        // set url
        curl_setopt($ch, CURLOPT_URL, $url);

        //return the transfer as a string
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        // $output contains the output string
        $output = curl_exec($ch);

        // close curl resource to free up system resources
        curl_close($ch);

        return $output;
}*/

//изменял под свои
/*$google_key='ABQIAAAAAO2IQ2wEk8qzbhBVassz3RQWZvqf6w_6a-zsF36NDtijShSiFRQOmhalY_9HhuT-wxKN6B3fjfmnTA';*/
$yandex_key='03.14875897:a3af80c5e63a11257d729ee0287476f6';
/*$ip='91.78.181.108';*/

/*

Лимит запросов в сутки	1000 для яндекса
привязаны к аккаунту на http://xml.yandex.ru/ с подтверждением по смс
Документация: http://help.yandex.ru/xml/

Лимит запросов в сутки для гугла неопределен
Документация: http://code.google.com/apis/blogsearch/v1/jsondevguide.html

пример работы с коментами для YouTube
http://gdata.youtube.com/feeds/api/videos/VIDEO_ID/comments
*/

function chech_yandex_content($cont)
{
	return intval(preg_match('/\<rss xmlns\:yablogs\=\"urn\:yandex\-blogs\" xmlns\:wfw\=\"http\:\/\/wellformedweb\.org\/CommentAPI\/" version\=\"2\.0\">/isu',$cont));
}

function getpost_yandex($text,$ts,$te,$geo,$proxys)
{
	global $redis;
	$proxys=json_decode($redis->get('proxy_list'),true);
	for ($i=0;$i<20;$i++)
	{
		$new_proxys[]=$proxys[rand(0,count($proxys))];
	}
	$proxys=$new_proxys;
	if ($geo=='az')
	{
		$geotxt='&geo='.urlencode('Азербайджан');
		//echo $geotxt;
	}
	$i_proxy=0;
	$tmp_keyword=$text;
	// print_r($proxys);
	$i=0;
	do
	{
		echo '/';
		$cc=count($out['time']);
		do
		{
			// $cont=parseURLproxy('http://blogs.yandex.ru/search.rss?text='.urlencode($text).'&ft=all&from_day='.intval(date('d',$ts)).'&date=on&from_month='.date('m',$ts).'&from_year='.date('Y',$ts).'&to_day='.intval(date('d',$te)).'&to_month='.date('m',$te).'&to_year='.date('Y',$te).'&holdres=mark&p='.intval($i).'&numdoc=100&rd=0&spcctx=doc'.$geotxt.'&full=1',$proxys[$i_proxy]);
			// if ($fail_yandex==1)
			{
				echo '+';
				$cont=parseURLproxy_yandex('https://blogs.yandex.ru/search.rss?text='.urlencode($text).'&ft=all&from_day='.intval(date('d',$ts)).'&date=on&from_month='.date('m',$ts).'&from_year='.date('Y',$ts).'&to_day='.intval(date('d',$te)).'&to_month='.date('m',$te).'&to_year='.date('Y',$te).'&holdres=mark&p='.intval($i).'&numdoc=100&rd=0&spcctx=doc'.$geotxt.'&full=1',$proxys[$i_proxy]);
				// echo 'https://blogs.yandex.ru/search.rss?text='.urlencode($text).'&ft=all&from_day='.intval(date('d',$ts)).'&date=on&from_month='.date('m',$ts).'&from_year='.date('Y',$ts).'&to_day='.intval(date('d',$te)).'&to_month='.date('m',$te).'&to_year='.date('Y',$te).'&holdres=mark&p='.intval($i).'&numdoc=100&rd=0&spcctx=doc'.$geotxt.'&full=1'."\n";
			}
			/*else 
			{
				$cont=parseURL('http://blogs.yandex.ru/search.rss?text='.urlencode($text).'&ft=all&from_day='.intval(date('d',$ts)).'&date=on&from_month='.date('m',$ts).'&from_year='.date('Y',$ts).'&to_day='.intval(date('d',$te)).'&to_month='.date('m',$te).'&to_year='.date('Y',$te).'&holdres=mark&p='.intval($i).'&numdoc=100&rd=0&spcctx=doc'.$geotxt.'&full=1');
				if (($cont=="<?xml version=\"1.0\" encoding=\"utf-8\"?>\n")) $fail_yandex=1;
				if (preg_match('/<title>403<\/title>/is', $cont)) $fail_yandex=1;
			}*/
			//echo 'http://blogs.yandex.ru/search.rss?text='.urlencode($text).'&ft=all&from_day='.intval(date('d',$ts)).'&date=on&from_month='.date('m',$ts).'&from_year='.date('Y',$ts).'&to_day='.intval(date('d',$te)).'&to_month='.date('m',$te).'&to_year='.date('Y',$te).'&holdres=mark&p='.intval($i).'&numdoc=100&rd=0&spcctx=doc'.$geotxt;
			echo '.';
			if (chech_yandex_content($cont)==0)
			{
				echo '*';
				$i_proxy++;
			}
		}
		while ((chech_yandex_content($cont)==0) && ($i_proxy<count($proxys)));
		// echo $cont."\n";
		$mas=simplexml_load_string($cont);
		$json = json_encode($mas);
		$mas= json_decode($json,true);
		//print_r($mas);
		//echo 'http://blogs.yandex.ru/search.rss?text='.urlencode($text).'&ft=all&from_day='.intval(date('d',$ts)).'&date=on&from_month='.date('m',$ts).'&from_year='.date('Y',$ts).'&to_day='.intval(date('d',$te)).'&to_month='.date('m',$te).'&to_year='.date('Y',$te).'&holdres=mark&p='.intval($i).'&numdoc=100&rd=0&spcctx=doc'.$geotxt."\n";
		//$regex='/<item>.*?(<author>(?<author>.*?)<\/author>.*?)?<pubDate>(?<time>.*?)<\/pubDate>.*?<link>(?<link>.*?)<\/link>.*?(<wfw\:commentRss>(?<comm>.*?)<\/wfw\:commentRss>.*?)?<description>(?<content>.*?)<\/description>.*?<\/item>/is';
		//preg_match_all($regex,$cont,$out);
		//print_r($out);
		foreach ($mas['channel']['item'] as $key => $item)
		{
			$count_yb++;
			//echo strtotime($item).' '.$out['link'][$key].' '.strip_tags(str_replace('\n','',html_entity_decode($out['content'][$key],ENT_QUOTES,'UTF-8')))."\n";
			if (in_array(str_replace('\n','',$item['link']),$outmas['link']))
			{
				$c++;
			}
			else
			{
				if (check_post($item['title'].' '.$item['description'],$tmp_keyword)==0) continue;
				$hn=parse_url(str_replace('\n','',$item['link']));
		    	$hn=$hn['host'];
		    	$ahn=explode('.',$hn);
		    	$hn = $ahn[count($ahn)-2].'.'.$ahn[count($ahn)-1];
				$hh = $ahn[count($ahn)-2];
				if ($hn=='twitter.com')
				{
					$item['title']=preg_replace('/<a.*?href\=[\'\"](.*?)[\'\"].*?>.*?<\/a>/isu','$1',$item['title']);
					$item['description']=preg_replace('/<a.*?href\=[\'\"](.*?)[\'\"].*?>.*?<\/a>/isu','$1',$item['description']);
				}
				$outmas['author'][]=$item['author'];
				$outmas['comm'][]='';//$out['comm'][$key];
				$outmas['time'][]=strtotime($item['pubDate']);
				$outmas['link'][]=str_replace('\n','',$item['link']);
				if (trim($item['title'])=='')
				{
					$regex='/^(?<st>.{0,150})[^а-яa-zё]/isu';
					preg_match_all($regex, $item['description'], $out);
					$short_text=trim($out['st'][0]).'...';
				}
				$outmas['content'][]=strip_tags(preg_replace('/\s+/is',' ',html_entity_decode(trim($item['title'])!=''?$item['title']:($short_text==''?$item['link']:$short_text),ENT_QUOTES,'UTF-8')));
				$outmas['flag'][]='ya';
				$outmas['fulltext'][]=strip_tags(preg_replace('/\s+/is',' ',html_entity_decode(trim($item['description'])!=''?$item['description']:(trim($item['title'])==''?$item['link']:$item['title']),ENT_QUOTES,'UTF-8')));
			}
			if (count($outmas['time'])>100) $outmas=post_slice($outmas);
		}
		//echo count($out['time']);
		$i++;
		//sleep(1);
		//echo intval($c).'!!!';
		//print_r($outmas);
		//echo count($mas['channel']['item']);
	}
	while ((intval($c)<50) && (count($mas['channel']['item'])>10));//От зацикливаний!!!
	add_source_log('yandex_blogs',intval($count_yb));
	//print_r($outmas);
	//echo "\n";
	return $outmas;
}

// getpost_yandex('путин',mktime(0,0,0,3,1,2016),mktime(0,0,0,3,1,2016),'ru',array('212.119.105.65:3128','46.38.42.14:8080'));
//echo mktime(0,0,0,6,1,2012);
//echo intval(chech_yandex_content(parseURLproxy('http://news.rambler.ru/rss/popular/head/')));
?>
