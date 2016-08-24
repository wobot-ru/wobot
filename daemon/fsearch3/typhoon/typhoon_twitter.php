<?
require_once('/var/www/daemon/com/config.php');
require_once('/var/www/daemon/com/func.php');
require_once('/var/www/daemon/com/db.php');
// require_once('/var/www/daemon/com/qlib.php');
require_once('/var/www/daemon/bot/kernel.php');

//error_reporting(E_ERROR | E_WARNING | E_PARSE);
// error_reporting(0);
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

function getpost_typhoon_twitter($text,$ts,$te,$geo,$proxys)
{
	global $redis;
	$mkeyword=get_simple_query($text,'twitter');
	print_r($mkeyword);
	foreach ($mkeyword as $keyword)
	{
		unset($mcont);
		$scroll_cursor='';
		do
		{
			$scroll_cursor='&scroll_cursor='.$mcont['scroll_cursor'];
			echo 'https://twitter.com/i/search/timeline?q='.urlencode($keyword.' lang:ru since:'.date('Y-m-d',$ts-86400).' until:'.date('Y-m-d',$te-86400)).'&src=typd&include_available_features=1&include_entities=1'.$scroll_cursor."\n";
			$cont=parseUrl('https://twitter.com/i/search/timeline?q='.urlencode($keyword.' lang:ru since:'.date('Y-m-d',$ts-86400).' until:'.date('Y-m-d',$te-86400)).'&src=typd&include_available_features=1&include_entities=1'.$scroll_cursor);
			$mcont=json_decode($cont,true);
	        $regex='/<a href="(?<link>[^\"]*?)"[^\>]*?><span class="_timestamp.*?".*?data-time="(?<time>\d+)"[^\>]*?>.*?<p class="js-tweet-text tweet-text"[^\>]*?>(?<cont>.*?)<\/p>/is';
	        preg_match_all($regex, html_entity_decode($mcont['items_html']), $out);
	        // print_r($out);
	        foreach ($out['link'] as $key => $link)
	        {
				if (check_post(strip_tags($out['cont'][$key]),$text)==0) continue;
				if (($out['time'][$key]<$ts) && ($out['time'][$key]>=$te)) continue;

				$rg='/\/(?<auth>.*?)\/status\/isu';
				preg_match_all($rg, $link, $ot);
				$outmas['author'][]=$ot['auth'][0];
				$outmas['comm'][]='';//$out['comm'][$key];
				$outmas['time'][]=$out['time'][$key];
				$outmas['link'][]='http://twitter.com'.preg_replace('/\/status\//isu','/statuses/',$link);
				$outmas['content'][]=strip_tags($out['cont'][$key]);
				$outmas['fulltext'][]=strip_tags($out['cont'][$key]);
			}
			if (count($outmas['time'])>100) $outmas=post_slice($outmas);
			print_r($outmas);
		}
		while ($mcont['scroll_cursor']!='' && trim($mcont['items_html'])!='');
	}
	return $outmas;
}

// getpost_typhoon_twitter('"МАСКОМ"&("ГК"|группа /+1 компаний)',mktime(0,0,0,1,1,2015),mktime(0,0,0,2,6,2015),'ru',array('212.119.105.65:3128','46.38.42.14:8080'));
//echo mktime(0,0,0,6,1,2012);
//echo intval(chech_yandex_content(parseURLproxy('http://news.rambler.ru/rss/popular/head/')));
?>
