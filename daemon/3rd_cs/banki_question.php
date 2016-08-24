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

function get_count_banki_question_page($url)
{
	global $redis,$mproxy;
	while (1)
	{
		echo '.';
		$cont=parseUrlproxy($url,$mproxy[rand(0,399)]);
		// echo $cont;
		$cont=iconv('windows-1251', 'UTF-8', $cont);
		if (!preg_match('/banki.ru/isu', $cont)) continue;
		if (!preg_match('/<link rel="icon" href="\/favicon.ico" type="image\/x-icon">/isu',$cont)) continue;
		if (trim($cont)=='') continue;
		break;
	}
	// $cont=parseUrl($url);
	// $cont=iconv('windows-1251', 'UTF-8', $cont);
	$regex='/<a title="в конец" href="\/services\/questions\-answers\/\?PAGEN\_\d+\=(?<page>\d+)\&id=\d+">/isu';
	preg_match_all($regex, $cont, $out);
	return $out['page'][0];
}

function get_banki_question_page($tid,$st,$et)
{
	global $redis,$mproxy;
	$c=0;
	// $count_page=get_count_banki_question_page('http://www.banki.ru/services/questions-answers/?id='.$tid);
	// for ($i=1;$i<=$count_page;$i++)
	$outnl['link'][0]='.';
	do
	{
		if ($cont!='')
		{
			echo '/';
			$regex='/<a title="следующая" href="(?<link>.*?)">/isu';
			preg_match_all($regex, $cont, $outnl);
			$nlink=$outnl['link'][0];
			$page++;
			$nlink='http://www.banki.ru/services/questions-answers/?id='.$tid.'&p='.intval($page);
			$outnl['link'][0]=$nlink;
			// print_r($outnl);
		}
		else
		{
			echo '.';
			$nlink='http://www.banki.ru/services/questions-answers/?id='.$tid;
		}
		echo $nlink."\n";
		while (1)
		{
			$cont=parseUrlproxy($nlink,$mproxy[rand(0,399)]);
			$cont=iconv('windows-1251', 'UTF-8', $cont);
			if (!preg_match('/banki.ru/isu', $cont)) continue;
			if (!preg_match('/<link rel="icon" href="\/favicon.ico" type="image\/x-icon">/isu',$cont)) continue;
			if (trim($cont)=='') continue;
			break;
		}
		// echo $cont;
		// die();
		// $regex='/<td><strong>Вопрос\:<\/strong><\/td>\s*<td width=\"100\%\">(?<cont>.*?)<div style="float\: right">[\s\t]*<a rel="nofollow" href="(?<link>.*?)" style="color\: \#AAAABB\; font\-size\: \d+\%\;">.*?<span class="questionTime"><nobr>&middot; (?<date>.*?)<\/nobr><\/span>/isu';
		$regex='/<td width="100%">\s*<div>(?<cont>.*?)<\/div>\s*<div class="widget__info font-size-default float-right">\s*<a rel="nofollow" href="(?<link>.*?)">постоянный адрес<\/a>.*?<span class=".*?bull.*?">.*?<\/span>(?<date>.*?)<\/div>/isu';
		preg_match_all($regex, $cont, $out);
		// print_r($out);
		if (count($out['date'])==0) break;
		foreach ($out['date'] as $key => $item)
		{
			$time=strtotime($item);
			// echo $time.' '.$st."\n";
			if ($time<$st) 
			{
				echo 'GGGGG';
				$c=1;
				continue;
			}
			if ($time>=$et+86400) continue; 
			$outmas['link'][]='http://www.banki.ru'.$out['link'][$key];
			$outmas['content'][]=preg_replace('/\s+/isu',' ',preg_replace('/<[^<]*?>/isu',' ',$out['cont'][$key]));
			$outmas['time'][]=$time;
		}
		if ($c==1) break;
		sleep(1);
	}
	while (($outnl['link'][0]!='') && (intval($c)==0));
	// print_r($outmas);
	return $outmas;
	// $cont=parseUrl('http://www.banki.ru/forum/?PAGE_NAME=read&FID=14&TID=14022');
}

// echo get_banki_question_page(2836732,mktime(0,0,0,5,1,2016),mktime(0,0,0,5,23,2016));

//get_forum_banki(14,14022,mktime(0,0,0,1,25,2015),mktime(0,0,0,1,30,2015));

?>