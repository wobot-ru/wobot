<?
/*
Документируем код

Last updates:
$access_token - variable

TODO:
Важно
1) добавить в wall.get цикл и использование offset, ограничить цикл по дате поста start_time
Пока не важно:
2) расширить базу городов
3) проверить определение удаленных пользователей
4) определять ботов
5) строить облака тегов по комментариям

*/
// require_once('/var/www/daemon/com/config.php');
// require_once('/var/www/daemon/com/func.php');
// require_once('/var/www/daemon/com/db.php');
// require_once('/var/www/daemon/bot/kernel.php');

$db = new database();
$db->connect();

// error_reporting(0);
//ignore_user_abort(true);
set_time_limit(0);
ini_set('max_execution_time',0);
ob_implicit_flush();
ini_set("memory_limit", "2048M");
date_default_timezone_set ( 'Europe/Moscow' );

function get_twitter_3rd($grid,$ts,$te)
{
	$cont=parseUrl('https://twitter.com/'.$grid);
	// echo $cont;
	sleep(1);
	$regex='/<a class="ProfileTweet-timestamp.*?href="(?<link>.*?)".*?<span class="js-short-timestamp.*?data-time="(?<time>\d+)".*?<p class="ProfileTweet-text.*?>(?<cont>.*?)<\/p>/isu';
	preg_match_all($regex, $cont, $out);
	// print_r($out);
	foreach ($out['link'] as $key => $item)
	{
		if ($out['time'][$key]<$ts || $out['time'][$key]>($te+86400)) continue;
		$outmas['link'][]='http://twitter.com'.preg_replace('/\/status\//isu', '/statuses/', $item);
		$out['cont'][$key]=preg_replace('/<[^\<]*?>/isu', ' ', $out['cont'][$key]);
		$out['cont'][$key]=preg_replace('/\s+/isu', ' ', $out['cont'][$key]);
		$outmas['content'][]=$out['cont'][$key];
		$outmas['time'][]=$out['time'][$key];
		$outmas['engage'][]=0;
		$outmas['adv_engage'][]='';
		$regex='/^\/(?<author_id>[^\/]*?)\//isu';
		preg_match_all($regex, $item, $outauth);
		$outmas['author_id'][]=$outauth['author_id'][0];
	}
	// print_r($outmas);
	return $outmas;
}

// $ts = mktime(0,0,0,2,8,2015);
// $te = mktime(0,0,0,2,10,2015);

// get_twitter_3rd('park_muzeon',$ts,$te);

// get_twitter('ru_wobot',mktime(0,0,0,5,1,2013),mktime(0,0,0,2,25,2014));

?>
