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

// $db = new database();
// $db->connect();

// error_reporting(0);
//ignore_user_abort(true);
set_time_limit(0);
ini_set('max_execution_time',0);
ob_implicit_flush();
ini_set("memory_limit", "2048M");
date_default_timezone_set ( 'Europe/Moscow' );

$assok_ok['янв']=1;
$assok_ok['фев']=2;
$assok_ok['мар']=3;
$assok_ok['апр']=4;
$assok_ok['май']=5;
$assok_ok['июн']=6;
$assok_ok['июл']=7;
$assok_ok['авг']=8;
$assok_ok['сен']=9;
$assok_ok['окт']=10;
$assok_ok['ноя']=11;
$assok_ok['дек']=12;

function get_ok($grid,$ts,$te)
{
	global $assok_ok;
	$cont=parseUrl('http://www.odnoklassniki.ru/'.$grid);
	// echo $cont;
	sleep(1);
	$regex='/<span class="feed_date">(?<time>.*?)<\/span>.*?<div class="media-text_cnt">(?<cont>.*?)<a id="nohook_".*?href=\"(?<link>.*?)\"/isu';
	preg_match_all($regex, $cont, $out);
	// print_r($out);
	foreach ($out['link'] as $key => $item)
	{
		$regex_time='/(?<day>\d+)\s(?<mon>[а-я]+)/isu';
		preg_match_all($regex_time, $out['time'][$key], $outtime);
		$time=mktime(0,0,0,$assok_ok[$outtime['mon'][0]],$outtime['day'][0],date('Y'));
		// echo $time."\n";
		if ($time<$ts || $time>($te+86400)) continue;
		$outmas['link'][]='http://www.odnoklassniki.ru'.preg_replace('/\/status\//isu', '/statuses/', $item);
		$out['cont'][$key]=preg_replace('/<[^\<]*?>/isu', ' ', $out['cont'][$key]);
		$out['cont'][$key]=preg_replace('/\s+/isu', ' ', $out['cont'][$key]);
		$outmas['content'][]=$out['cont'][$key];
		$outmas['time'][]=$time;
		$outmas['engage'][]=0;
		$outmas['adv_engage'][]='';
		$regex='/^\/(?<author_id>[^\/]*?)\//isu';
		preg_match_all($regex, $item, $outauth);
		$outmas['author_id'][]='';
	}
	// print_r($outmas);
	return $outmas;
}

// get_ok('group/52473162563783',mktime(0,0,0,5,1,2013),mktime(0,0,0,6,25,2015));

?>
