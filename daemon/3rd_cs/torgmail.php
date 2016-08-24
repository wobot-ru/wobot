<?
session_start();

// require_once('/var/www/com/config.php');
// require_once('/var/www/com/func.php');
// require_once('/var/www/com/db.php');
// require_once('/var/www/bot/kernel.php');

//error_reporting(E_ERROR | E_WARNING | E_PARSE);
// error_reporting(E_ERROR);
ignore_user_abort(true);
set_time_limit(0);
ini_set('max_execution_time',0);
ini_set('default_charset','utf-8');
ob_implicit_flush();

// $db = new database();
// $db->connect();

$masmon['января']=1;
$masmon['февраля']=2;
$masmon['марта']=3;
$masmon['апреля']=4;
$masmon['мая']=5;
$masmon['июня']=6;
$masmon['июля']=7;
$masmon['августа']=8;
$masmon['сентября']=9;
$masmon['октября']=10;
$masmon['ноября']=11;
$masmon['декабря']=12;

function get_tmail($url,$ts,$te)
{
	global $masmon;
	$p=0;
	do
	{
		echo $url.'?page='.$p."\n";
		sleep(1);
		$cont=parseUrl($url.'?page='.$p);
		$p++;
		$cont=iconv('windows-1251','UTF-8',$cont);
		$regex='/<section class="js-review_item card__responses__response .*?">(?<cnt>.*?)<\/section>/isu';
		preg_match_all($regex, $cont, $out);
		// print_r($out['cnt']);
		foreach ($out['cnt'] as $key => $item)
		{
			$regextime='/<span>написал[а]?\s+(?<time>.*?)<\/span>/isu';
			preg_match_all($regextime,$item,$outtime);
			$regext='/(?<d>\d+)\s(?<m>[а-я]+)\s(?<y>\d\d\d\d)/isu';
			preg_match_all($regext,$outtime['time'][0],$outt);
			$time=mktime(0,0,0,$masmon[$outt['m'][0]],$outt['d'][0],$outt['y'][0]);
			if (($time>=$ts) && ($time<=$te))
			{
				
				$outmas['time'][]=$time;
				$regexengyes='/Да<span class="button__counter button__counter_vote">(?<yes>\d+)<\/span>/isu';
				preg_match_all($regexengyes,$item,$outengyes);
				$regexengno='/Нет<span class="button__counter button__counter_vote">(?<no>\d+)<\/span>/isu';
				preg_match_all($regexengno,$item,$outengno);
				$outmas['eng'][]=$outengno['no'][$key]+$outengyes['yes'][$key];
				$outmas['link'][]=$url.'?page='.$p;
				$regex_text='/<\/noindex>\s*<p>(?<text>.*?)<\/p>/isu';
				preg_match_all($regex_text, $item, $out_text);
				$regex_ans='/<span class="card__responses__response__answer__text">(?<ans>.*?)<\/span>/isu';
				preg_match_all($regex_ans, $item, $out_ans);
				$outmas['content'][]=preg_replace('/[\s\t]+/isu',' ',$out_text['text'][0].' '.$out_ans['ans'][0]);
			}
		}
		// print_r($outmas);
		// echo $time.' ';
	}	
	while (($time>($ts-86400)) && (count($out['cnt'])==15));
	// print_r($outmas);
	return $outmas;
}

// get_tmail('http://torg.mail.ru/review/shops/iqmobile-ru-cid1097/',mktime(0,0,0,7,1,2010),mktime(0,0,0,7,20,2013));

?>
