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

function get_count_page($url)
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
	// echo $cont;
	$regex='/<a href="\/forum\/\?PAGE_NAME=read&FID=\d+&TID=\d+&PAGEN_1=\d+#forum-message-list">(?<page>\d+)<\/a>/isu';
	preg_match_all($regex, $cont, $out);
	// print_r($out);
	return $out['page'][count($out['page'])-1];
}

function get_forum_banki($fid,$tid,$st,$et)
{
	global $redis,$mproxy;
	$c=0;
	// echo 'http://www.banki.ru/forum/?PAGE_NAME=read&FID='.$fid.'&TID='.$tid."\n";
	$count_page=get_count_page('http://www.banki.ru/forum/?PAGE_NAME=read&FID='.$fid.'&TID='.$tid);
	echo $count_page."\n";
	for ($i=$count_page;$i>=0;$i--)
	{
		echo '.';
		// echo 'http://www.banki.ru/forum/?PAGE_NAME=read&FID='.$fid.'&TID='.$tid.'&PAGEN_1='.$i."\n";
		while (1)
		{
			$cont=parseUrlproxy('http://www.banki.ru/forum/?PAGE_NAME=read&FID='.$fid.'&TID='.$tid.'&PAGEN_1='.$i,$mproxy[rand(0,400)]);
			$cont=iconv('windows-1251', 'UTF-8', $cont);
			// echo $cont;
			if (!preg_match('/banki.ru/isu', $cont)) continue;
			if (!preg_match('/<link rel="icon" href="\/favicon.ico" type="image\/x-icon">/isu',$cont)) continue;
			if (trim($cont)=='') continue;
			break;
		}
		// echo $cont;
		$regex='/<div class="forum-post-date">.*?<noindex>.*?<a[^<]*?href="(?<link>.*?)"[^<]*?>.*?<\/noindex>.*?<span>(?<date>.*?)<\/span>.*?<\/div>.*?<div class="forum-post-entry".*?>.*?<div.*?>(?<cont>.*?)<\/div>.*?<\/div>/isu';
		preg_match_all($regex, $cont, $out);
		// print_r($out);
		foreach ($out['date'] as $key => $item)
		{
			$time=strtotime($item);
			// echo $time.' '.$st."\n";
			if ($time<$st) 
			{
				$c=1;
				continue;
			}
			if ($time>=$et+86400) continue; 
			$outmas['link'][]=$out['link'][$key];
			$outmas['content'][]=preg_replace('/\s+/isu',' ',preg_replace('/<[^<]*?>/isu',' ',$out['cont'][$key]));
			$outmas['time'][]=$time;
		}
		// print_r($outmas);
		if ($c==1) break;
		sleep(1);
	}
	// print_r($outmas);
	return $outmas;
	// $cont=parseUrl('http://www.banki.ru/forum/?PAGE_NAME=read&FID=14&TID=14022');
}

// get_forum_banki(14,14022,mktime(0,0,0,5,20,2016),mktime(0,0,0,5,23,2016));

?>