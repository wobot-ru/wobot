<?

// require_once('/var/www/daemon/com/config.php');
// require_once('/var/www/daemon/com/func.php');
// require_once('/var/www/daemon/com/db.php');
// require_once('/var/www/daemon/bot/kernel.php');
//require_once('lcheck.php');

date_default_timezone_set('Europe/Moscow');
// error_reporting(E_ERROR);
ignore_user_abort(true);
set_time_limit(0);
ini_set('max_execution_time',0);
ini_set('default_charset','utf-8');
ob_implicit_flush();

// $redis = new Redis();    
// $redis->connect('127.0.0.1');

// $mproxy=json_decode($redis->get('proxy_list'),true);

function get_count_friends_page($url)
{
	global $redis,$mproxy;
	while (1)
	{
		$cont=parseUrlproxy($url,$mproxy[rand(0,400)]);
		$cont=iconv('windows-1251', 'UTF-8', $cont);
		if (!preg_match('/banki.ru/isu', $cont)) continue;
		if (!preg_match('/<link rel="icon" href="\/favicon.ico" type="image\/x-icon">/isu',$cont)) continue;
		if (trim($cont)=='') continue;
		break;
	}
	$regex='/totalItems\:\s(?<count>\d+)\;/isu';
	preg_match_all($regex, $cont, $out);
	// print_r($out);
	return (intval($out['count'][0]/25)+1);
	// $i=1;
	// $c=0;
	// $wrong_page=0;
	// $mintime=0;
	// do
	// {
	// 	echo '>';
	// 	$cont=parseUrl($url.'?PAGEN_6='.$i);
	// 	$cont=iconv('windows-1251', 'UTF-8', $cont);
	// 	$regex='/<span class="date">(?<time>.*?)</isu';
	// 	preg_match_all($regex, $cont, $out);
	// 	foreach ($out['time'] as $key => $item)
	// 	{
	// 		if (strtotime(trim($item))>=$mintime) $mintime=strtotime(trim($item));
	// 		elseif ($wrong_page==1) 
	// 		{
	// 			$c=1;
	// 			break;
	// 		}
	// 		else 
	// 		{
	// 			$i-=10;
	// 			$wrong_page=1;
	// 			break;
	// 		}
	// 	}
	// 	if ($wrong_page==1) $i++;
	// 	else $i+=10;
	// 	sleep(1);
	// }
	// while ($c==0);
	// return $i-2;
}

function get_friends_banki($fid,$tid,$st,$et)
{
	global $redis,$mproxy;
	$c=0;
	$count_page=get_count_friends_page('http://www.banki.ru/friends/group/'.$fid.'/forum/'.$tid.'/');
	echo $count_page;
	for ($i=$count_page;$i>0;$i--)
	{
		echo '.';
		// echo 'http://www.banki.ru/friends/group/'.$fid.'/forum/'.$tid.'/?PAGEN_6='.$i."\n";
		while (1)
		{
			// echo '*';
			$cont=parseUrlproxy('http://www.banki.ru/friends/group/'.$fid.'/forum/'.$tid.'/?PAGEN_6='.$i,$mproxy[rand(0,400)]);
			$cont=iconv('windows-1251', 'UTF-8', $cont);
			// echo $cont;
			if (!preg_match('/banki.ru/isu', $cont)) continue;
			if (!preg_match('/<link rel="icon" href="\/favicon.ico" type="image\/x-icon">/isu',$cont)) continue;
			if (trim($cont)=='') continue;
			break;
		}
		// echo $cont;
		$regex='/<span class="date">(?<date>.*?)<a href="(?<link>.*?)".*?<\/span>\s*<div class="formated">(?<cont>.*?)<\/div>/isu';
		preg_match_all($regex, $cont, $out);
		// print_r($out);
		foreach ($out['date'] as $key => $item)
		{
			$time=strtotime(trim($item));
			// echo $time.' '.$st."\n";
			if ($time<$st) 
			{
				$c=1;
				continue;
			}
			if ($time>=$et+86400) continue; 
			$outmas['link'][]='http://www.banki.ru/friends/group/'.$fid.'/forum/'.$tid.'/?PAGEN_6='.$i.$out['link'][$key];
			$outmas['content'][]=preg_replace('/\s+/isu',' ',preg_replace('/<[^<]*?>/isu',' ',$out['cont'][$key]));
			$outmas['time'][]=$time;
		}
		if ($c==1) break;
		sleep(1);
	}
	// print_r($outmas);
	return $outmas;
	// $cont=parseUrl('http://www.banki.ru/forum/?PAGE_NAME=read&FID=14&TID=14022');
}

// echo get_count_friends_page('http://www.banki.ru/friends/group/tcs-bank/forum/92444/');
// get_friends_banki('tcs-bank',92509,mktime(0,0,0,1,1,2015),mktime(0,0,0,5,23,2016));
// get_forum_banki('sberbank',178703,mktime(0,0,0,5,20,2013),mktime(0,0,0,5,21,2015));

?>