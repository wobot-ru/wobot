<?

// require_once('/var/www/daemon/com/config.php');
// require_once('/var/www/daemon/com/func.php');
// require_once('/var/www/daemon/com/db.php');
// require_once('/var/www/daemon/bot/kernel.php');
// require_once('lcheck.php');

// require_once('/var/www/bot/kernel.php');

date_default_timezone_set('Europe/Moscow');
// error_reporting(E_ERROR);
ignore_user_abort(true);
set_time_limit(0);
ini_set('max_execution_time',0);
ini_set('default_charset','utf-8');
ob_implicit_flush();

$redis = new Redis();    
$redis->connect('127.0.0.1');
// $mproxy=json_decode($redis->get('proxy_list'),true);

$ascmon['января']=1;
$ascmon['февраля']=2;
$ascmon['марта']=3;
$ascmon['апреля']=4;
$ascmon['мая']=5;
$ascmon['июня']=6;
$ascmon['июля']=7;
$ascmon['августа']=8;
$ascmon['сентября']=9;
$ascmon['октября']=10;
$ascmon['ноября']=11;
$ascmon['декабря']=12;

function get_posts_sources($settings,$ts,$te)
{
	global $mproxy,$ascmon;
	while (1)
	{
		$cont=parseUrlproxy($settings['url'],$mproxy[rand(0,400)]);
		if (trim($cont)!='') break;
		echo '.';
	}
	if (preg_match('/charset="windows-1251"/is',$cont)) $cont=iconv('windows-1251', 'UTF-8', $cont);
	$cont=preg_replace('/[^a-zа-яё0-9\/\"\'\,\.\!\?\<\>\?\:\;\s\(\)\\\=\&\*\$\@\%\-\_]/isu', '', $cont);
	// echo $cont;
	preg_match_all($settings['regex'], $cont, $out);
	foreach ($out['time'] as $key => $time)
	{
		preg_match_all($settings['time_regex'], $time, $out_time);
		if (isset($ascmon[$out_time['month'][0]])) $out_time['month'][0]=$ascmon[$out_time['month'][0]];
		if ($out_time['year'][0]=='') $out_time['year'][0]=date('Y');
		$time=mktime($out_time['hour'][0],$out_time['min'][0],0,$out_time['month'][0],$out_time['day'][0],$out_time['year'][0]);
		echo $ts.' '.$time.' '.$te."\n";
		if ($time>=$ts && $time<$te+86400)
		{
			$outmas['time'][]=$time;
			$outmas['content'][]=strip_tags($out['content'][$key]);
			$outmas['link'][]=$settings['pre_link'].$out['link'][$key];
		}
	}
	// print_r($out);
	print_r($outmas);
	return $outmas;
}

/*$settings['url']='https://www.babyblog.ru/search/all?query=%D0%B3%D0%B5%D0%BC%D0%BE%D1%82%D0%B5%D1%81%D1%82&sort=date';
$settings['regex']='/<a class="js__objectTitle" href="(?<link>.*?)".*?>[\w\W]*?<b class="blog-created rel fl">(?<time>.*?)<\/b>[\w\W]*?<pre>(?<content>[\w\W]*?)<\/pre>/isu';
$settings['time_regex']='/(?<day>\d+)\s(?<month>[а-яё]*?)\,\s(?<hour>\d+)\:(?<min>\d+)/isu';
$settings['pre_link']='https://www.babyblog.ru';

echo json_encode($settings)."\n";

$settings['url']='http://www.baby.ru/search/?query=%D0%B3%D0%B5%D0%BC%D0%BE%D1%82%D0%B5%D1%81%D1%82&sort=created';
$settings['regex']='/<div class="teaser__aside">(?<time>.*?)<\/div>.*?<a class="teaser__title page-title" href="(?<link>.*?)".*?<div class="teaser__description">(?<content>.*?)<\/div>/isu';
$settings['time_regex']='/(?<day>\d+)\s(?<month>[а-яё]*?)\s(?<year>\d+)/isu';
$settings['pre_link']='http://www.baby.ru';

echo json_encode($settings)."\n";

$ts=mktime(0,0,0,1,1,2016);
$te=mktime(0,0,0,8,24,2016);*/
// get_posts_sources($settings,$ts,$te);

?>