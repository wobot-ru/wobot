<?
//require_once('/var/www/com/config.php');
require_once('com/func.php');
//require_once('/var/www/com/db.php');
require_once('bot/kernel.php');
require_once('com/tmhOAuth.php');
require_once('com/vkapi.class.php');

$db = new database();
$db->connect();
date_default_timezone_set ( 'Europe/Moscow' );

function get_fb($nick)
{
	$mm=array('male'=>'2','female'=>'1');
	global $db;
	do
	{
	    $json=parseUrlproxy("https://graph.facebook.com/".$nick."?access_token=158565747504200|YvEEJ72Q6m3tohIylBb62tQ5EVE");
		if ($json=='')
		{
			$attmp++;
			echo "\n".'continue...'."\n";
		}
	}
	while (($json=='') && ($attmp<3));		
	$sm=json_decode($json,true);
	if ($sm['location']['city']!='')
	{
		$rru=$db->query("SELECT * FROM robot_location WHERE loc='".$sm['location']['city']."'");
		if (mysql_num_rows($rru)==0)
		{
    	$content_tw_loc=parseUrl('http://geocode-maps.yandex.ru/1.x/?geocode='.urlencode($sm['location']['city']).'&key=ANpUFEkBAAAAf7jmJwMAHGZHrcKNDsbEqEVjEUtCmufxQMwAAAAAAAAAAAAvVrubVT4btztbduoIgTLAeFILaQ==&results=1');
    		$regextw='/<pos>(?<loc_tw>.*?)<\/pos>/is';
    		preg_match_all($regextw,$content_tw_loc,$out_tw);
    		$rru=$db->query('INSERT INTO robot_location (loc,loc_coord) VALUES (\''.$sm['location']['city'].'\',\''.$out_tw['loc_tw'][0].'\')');
    	}
    	else
    	{
    		$rru1=$db->fetch($rru);
    		$out_tw['loc_tw'][0]=$rru1['loc_coord'];
    	}
	}
	if ($out_tw['loc_tw'][0]!='')
	{
		$regex='/(?<ch>\-?\d*?)\.(?<d>\d)/is';
		preg_match_all($regex,$out_tw['loc_tw'][0],$out);
		$twl=$out['ch'][0].'.'.$out['d'][0].' '.$out['ch'][1].'.'.$out['d'][1];
	}
	$outmas['loc']=$twl;
	$outmas['gender']=0;
	if (isset($mm[$sm['gender']]))
	{
		$outmas['gender']=$mm[$sm['gender']];
	}
	$outmas['name']=$sm['name'];
	$outmas['nick']=$nick;
	$outmas['fol']=0;
	$outmas['age']=0;
	$outmas['ico']='';
	//print_r($outmas);
	//sleep(1);
	return $outmas;
}
//get_fb('1395518841');

?>
