<?
session_start();

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

function get_market($url,$ts,$te)
{
	global $masmon;
	$p=0;
	do
	{
		unset($outeng);
		sleep(1);
		echo $url.'&sort_by=date&page_num='.$p."\n";
		$content=parseUrl($url.'&sort_by=date&page_num='.$p);
		// echo $content;
		$regex1='/<span class="b-aura-usergrade__pro-num">(?<eng_pos>\d*?)<\/span>/is';
		preg_match_all($regex1,$content,$out1);
		$regex2='/<span class="b-aura-usergrade__contra-num">(?<eng_neg>\d*?)<\/span>/isu';
		preg_match_all($regex2,$content,$out2);
		// print_r($out1);
		foreach ($out1['eng_pos'] as $key => $item)
		{
			$outeng[]=intval($out1['eng_pos'][$key])+intval($out2['eng_neg'][$key]);
		}
		// print_r($outeng);
		$regex='/<div id="review\-\d+"(?<cnt>.*?)<\/div>\s*<\/div>\s*<\/div>/isu';
		preg_match_all($regex, $content, $out);
		// print_r($out);
		// die();
		foreach ($out['cnt'] as $key => $item)
		{
			$regex_auth='/<(span|a) class="b-aura-username" itemprop="author"[^\<]*?>(?<user_nick>.*?)<\/(span|a)>/isu';
			$regex_time='/<meta itemprop="datePublished" content="(?<time>[^\"]*?)">/isu';
			$regex_positive='/<div class="b-aura-userverdict__text" itemprop="pro">(?<pos>.*?)<\/div>/isu';
			$regex_negative='/<div class="b-aura-userverdict__text" itemprop="contra">(?<neg>.*?)<\/div>/isu';
			$regex_content='/<div class="b-aura-userverdict__text" itemprop="description">(?<cont>.*?)<\/div>/isu';
			$regex_answ='/<span class="b-aura-review__answer_short js-review-answer-short">(?<answ>.*?)<\/span>/isu';
			preg_match_all($regex_auth,$item,$out_auth);
			preg_match_all($regex_time,$item,$out_time);
			preg_match_all($regex_positive,$item,$out_positive);
			preg_match_all($regex_negative,$item,$out_negative);
			preg_match_all($regex_content,$item,$out_content);
			preg_match_all($regex_answ,$item,$out_answ);
			$time=strtotime($out_time['time'][0]);
			if (($time>$te)||($te<$ts)) continue;
			$mpost['time'][]=strtotime($out_time['time'][0]);
			$mpost['content'][]=preg_replace('/<[^\<]*?>/isu',' ',preg_replace('/[\s\t]+/isu',' ',$out_positive['pos'][0].' '.$out_negative['neg'][0].' '.$out_content['cont'][0].' '.$out_answ['answ'][0]));
			$mpost['link'][]=$url;
			$mpost['eng'][]=$outeng[$key];
			$mpost['author_id'][]=$out_auth['user_nick'][0];
			$mpost['author_name'][]=$out_auth['user_nick'][0];
		}
		// print_r($mpost);
		$p++;
		// echo count($out['cnt']).' '.$time;
	}	
	while (($time>($ts-86400)) && (count($out['cnt'])==10));
	// print_r($mpost);
	return $mpost;
}

// get_market('http://market.yandex.ru/shop-opinions.xml?shop_id=89991',mktime(0,0,0,7,15,2013),mktime(0,0,0,7,20,2013));

?>
