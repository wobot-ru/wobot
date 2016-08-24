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

function get_banki_responses_page($tid,$st,$et)
{
	global $redis,$mproxy;
	$c=0;
	// $count_page=get_count_banki_question_page('http://www.banki.ru/services/questions-answers/?id='.$tid);
	// for ($i=1;$i<=$count_page;$i++)
	$outnl['link'][0]='.';
	do
	{
		$page++;
		$nlink='http://www.banki.ru/services/responses/bank/'.$tid.'/?page='.$page;
		// $cont=parseUrl($nlink);
		while (1)
		{
			echo '.';
			$cont=parseUrlproxy($nlink,$mproxy[rand(0,399)]);
			// echo $cont;
			// $cont=iconv('windows-1251', 'UTF-8', $cont);
			if (!preg_match('/banki.ru/isu', $cont)) continue;
			if (!preg_match('/<link rel="icon" type="image\/x-icon" href="\/favicon.ico">/isu',$cont)) continue;
			if (trim($cont)=='') continue;
			break;
		}
		// echo $cont."\n";
		$regex='/<header class="responses__item__title">[\w\W]*?<a href="(?<link>[^\"]*?)" itemprop="summary">[\w\W]*?<div class="responses__item__message markup-inside-small markup-inside-small--bullet" data-full itemprop="description">(?<cont>[\w\W]*?)<\/div>[\w\W]*?<footer.*?<time[^\<]*?>(?<time>[\w\W]*?)<\/time>/isu';
		preg_match_all($regex, $cont, $out);
		// print_r($out);
		foreach ($out['time'] as $key => $item)
		{
			$time=strtotime($item);
			// echo $time.' '.$st."\n";
			if (trim($time)=='') continue;
			if ($time<$st) 
			{
				$c=1;
				continue;
			}
			if ($time>=$et+86400) continue; 
			$outmas['link'][]='http://www.banki.ru'.$out['link'][$key];
			$outmas['content'][]=preg_replace('/\s+/isu',' ',preg_replace('/<[^<]*?>/isu',' ',$out['cont'][$key]));
			$outmas['time'][]=$time;
			$regex='/response\/(?<id>\d+)\//isu';
			preg_match_all($regex, $out['link'][$key], $ott);
			/*$comment=parseUrl('http://www.banki.ru/services/responses/bank/response/'.$ott['id'][0].'/comments/1/');
			$comment=trim(preg_replace('/\<\!\-\- Page generation time\: \d+\.\d+ sec\. \-\-\>/isu','',$comment));
			// echo $comment;
			sleep(1);
			$comment=json_decode($comment,true);
			// print_r($comment);
			foreach ($comment as $comm)
			{
				if (strtotime($comm['dateCreate'])<$st) 
				{
					$c=1;
					continue;
				}
				if (strtotime($comm['dateCreate'])>=$et+86400) continue; 
				$outmas['link'][]='http://www.banki.ru'.$out['link'][$key].'#'.$comm['id'];
				$outmas['content'][]=$comm['raw_text'];
				$outmas['time'][]=strtotime($comm['dateCreate']);
			}*/
		}
		if ($c==1) break;
		sleep(1);
	}
	while (intval($c)==0);
	// print_r($outmas);
	return $outmas;
	// $cont=parseUrl('http://www.banki.ru/forum/?PAGE_NAME=read&FID=14&TID=14022');
}

// get_banki_responses_page('tcs',mktime(0,0,0,5,20,2016),mktime(0,0,0,5,23,2016));

// get_forum_banki(14,14022,mktime(0,0,0,5,20,2013),mktime(0,0,0,5,21,2013));

?>