<?
require_once('/var/www/daemon/com/config.php');
require_once('/var/www/daemon/com/func.php');
require_once('/var/www/daemon/com/db.php');
require_once('/var/www/daemon/bot/kernel.php');

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
	if ($geo=='az')
	{
		$geotxt='&geo='.urlencode('Азербайджан');
		//echo $geotxt;
	}
	$i_proxy=0;
	//print_r($mproxy);
	do
	{
		$i=0;
		sleep(1);
		if (intval($mintime)!=0) $te=(mktime(0,0,0,date('n',$te),date('j',$te),date('Y',$te))==mktime(0,0,0,date('n',$mintime),date('j',$mintime),date('Y',$mintime))?mktime(0,0,0,date('n',$mintime),date('j',$mintime)-1,date('Y',$mintime)):mktime(0,0,0,date('n',$mintime),date('j',$mintime),date('Y',$mintime)));
		do
		{
			echo '/';
			$cc=count($out['time']);
			$cont=parseURL('http://blogs.yandex.ru/search.rss?text='.urlencode($text).'&ft=all&from_day='.intval(date('d',$ts)).'&date=on&from_month='.date('m',$ts).'&from_year='.date('Y',$ts).'&to_day='.intval(date('d',$te)).'&to_month='.date('m',$te).'&to_year='.date('Y',$te).'&holdres=mark&p='.intval($i).'&numdoc=100&rd=0&spcctx=doc'.$geotxt.'&full=1');
			echo 'http://blogs.yandex.ru/search.rss?text='.urlencode($text).'&ft=all&from_day='.intval(date('d',$ts)).'&date=on&from_month='.date('m',$ts).'&from_year='.date('Y',$ts).'&to_day='.intval(date('d',$te)).'&to_month='.date('m',$te).'&to_year='.date('Y',$te).'&holdres=mark&p='.intval($i).'&numdoc=100&rd=0&spcctx=doc'.$geotxt.'&full=1'."\n\n\n\n";
			sleep(1);
			$mas=simplexml_load_string($cont);
			$json = json_encode($mas);
			$mas= json_decode($json,true);
			foreach ($mas['channel']['item'] as $key => $item)
			{
				add_source_log('yandex_blogs');
				//echo strtotime($item).' '.$out['link'][$key].' '.strip_tags(str_replace('\n','',html_entity_decode($out['content'][$key],ENT_QUOTES,'UTF-8')))."\n";
				if (in_array(str_replace('\n','',$item['link']),$outmas['link']))
				{
					$c++;
				}
				else
				{
					if (intval($mintime)==0) $mintime=strtotime($item['pubDate']);
					if (strtotime($item['pubDate'])<$mintime) 
					{
						//echo $item['pubDate'].' '.strtotime($item['pubDate'])."\n";
						$mintime=strtotime($item['pubDate']);
					}
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
						$short_text=trim($out['st'][0]);
					}
					$outmas['content'][]=strip_tags(preg_replace('/\s+/is',' ',html_entity_decode(trim($item['title'])!=''?$item['title']:($short_text==''?$item['link']:$short_text),ENT_QUOTES,'UTF-8')));
					$outmas['flag'][]='ya';
					$outmas['fulltext'][]=strip_tags(preg_replace('/\s+/is',' ',html_entity_decode(trim($item['description'])!=''?$item['description']:(trim($item['title'])==''?$item['link']:$item['title']),ENT_QUOTES,'UTF-8')));
				}
			}
			$i++;
			echo "\n".mktime(0,0,0,date('n',$mintime),date('j',$mintime),date('Y',$mintime)).' '.$ts."\n";
		}
		while (($i<11) && (count($mas['channel']['item'])>50));//От зацикливаний!!!
		if (count($mas['channel']['item'])<50) $failed++;
		else $failed=0;
		if ($failed>3) break;
	}
	while ((mktime(0,0,0,date('n',$mintime),date('j',$mintime),date('Y',$mintime))!=$ts));
	// print_r($outmas);
	//echo "\n";
	return $outmas;
}

//getpost_yandex('макаревич',mktime(0,0,0,1,27,2013),mktime(0,0,0,1,30,2013),'ru',array('178.161.137.138:3128','82.103.128.192:3128','188.128.99.94:3128','194.85.15.155:3128','46.29.9.194:3128','80.79.179.10:8181','91.143.35.115:8080','46.38.42.14:8080'));
//echo mktime(0,0,0,6,1,2012);
//echo intval(chech_yandex_content(parseURLproxy('http://news.rambler.ru/rss/popular/head/')));
?>
