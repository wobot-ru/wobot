#!/usr/bin/php
<?
require_once('com/config.php');
require_once('com/func.php');
require_once('com/db.php');
require_once('bot/kernel.php');

error_reporting(0);



$db = new database();
$db->connect();
$handle=fopen('debug.txt','a');//order_end=0 or order_end>".time().'
while(1)
{
$result = $db->query("SELECT * FROM `robot_blogs2` WHERE blog_link='livejournal.com'");
while ($res=$db->fetch($result))
{
	echo $res['blog_id']."\n";
	sleep(1);
	$cont=parseUrl('http://'.$res['blog_login'].'.livejournal.com/profile');
	//echo $cont;
	$regex='/<a href=\'.*?\' class=\'region\'>(?<data>.*?)<\/a>/is';
	preg_match_all($regex,$cont,$out);
	if ($out['data'][0]=='')
	{
		$regex='/<a href=\'.*?\' class=\'locality\'>(?<data>.*?)<\/a>/is';
		preg_match_all($regex,$cont,$out);
		//print_r($out);
	}
	$loc=$out['data'][0];
	$regex='/<span class=\'expandcollapse on\' id=\'fofs_header\'><img id=\'fofs_arrow\' src=\'.*?\' align=\'absmiddle\' alt=\'\' \/>.*?\((?<fol>.*?)\).<\/span>/is';
	preg_match_all($regex,$cont,$out);
	//print_r($out);
	//echo $loc.' '.$res['blog_login'].' '.$out['fol'][0];
	//echo $cont;
	//echo $loc;
	$content_tw_loc=parseUrl('http://geocode-maps.yandex.ru/1.x/?geocode='.urlencode($loc).'&key=ANpUFEkBAAAAf7jmJwMAHGZHrcKNDsbEqEVjEUtCmufxQMwAAAAAAAAAAAAvVrubVT4btztbduoIgTLAeFILaQ==&results=1');
	//echo urlencode($user['location']);
	$regextw='/<pos>(?<loc_tw>.*?)<\/pos>/is';
	preg_match_all($regextw,$content_tw_loc,$out_tw);
	if ($out_tw['loc_tw'][0]!='')
	{
		if ($out_tw['loc_tw'][0]!='')
		{
			$regex='/(?<ch>\-?\d*?)\.(?<d>\d)/is';
			echo $key;
			preg_match_all($regex,$out_tw['loc_tw'][0],$out);
			$twl=$out['ch'][0].'.'.$out['d'][0].' '.$out['ch'][1].'.'.$out['d'][1];
		}
		echo "\n".'UPDATE robot_blogs2 SET blog_location=\''.$twl.'\', blog_readers='.$out['fol'][0].' WHERE blog_id='.$res['blog_id']."\n";
		$rru=$db->query('UPDATE robot_blogs2 SET blog_location=\''.$twl.'\', blog_readers='.$out['fol'][0].' WHERE blog_id='.$res['blog_id']);	
		echo $loc."\n";
	}
	else
	{
		$rru=$db->query('UPDATE robot_blogs2 SET blog_readers='.$out['fol'][0].' WHERE blog_id='.$res['blog_id']);	
		echo $loc."readers\n";
	}
}
}
?>
