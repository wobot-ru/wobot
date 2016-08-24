<?
require_once('com/config.php');
require_once('com/func.php');
require_once('com/db.php');
require_once('bot/kernel.php');
require_once('com/tmhOAuth.php');
require_once('com/vkapi.class.php');

$api_id = 2124816; // Insert here id of your application
$secret_key = 'f98VkwX1Cc64xSj76vP4'; // Insert here secret key of your application
//$db = new database();
//$db->connect();
date_default_timezone_set ( 'Europe/Moscow' );

function get_vk($nick)
{
	$agemonthe=array('января','февраля','марта','апреля','мая','июня','июля','августа','сентября','октября','ноября','декабря');
	global $api_id,$secret_key,$db;
    $VK = new vkapi($api_id, $secret_key);
    $resp = $VK->api('getProfiles', array('uids'=>$nick,'fields'=>'city,nickname,lists,sex'));
    $nicks=$resp['response'][0]['first_name']." ".$resp['response'][0]['last_name'];
    $gender=$resp['response'][0]['sex'];
	$outmas['gender']=$gender;
	$outmas['name']=$nicks;
	$outmas['nick']=$nick;
	$cont=parseUrl('http://vkontakte.ru/id'.$nick);//http://vkontakte.ru/id9884728-косяк
	$cont=iconv('windows-1251','UTF-8',$cont);
	$regex='/<div class=\"label fl\_l\">Город\:<\/div>.*?<div class=\"labeled fl\_l\">(?<data>.*?)<\/div>/is';
	preg_match_all($regex,$cont,$out);
	if ($out['data'][0]=='')
	{
		$regex='/<div class=\"label fl\_l\">Родной город\:<\/div>.*?<div class=\"labeled fl\_l\">(?<data>.*?)<\/div>/is';
		preg_match_all($regex,$cont,$out);
	}
	if ($out['data'][0]!='')
	{
		$rru=$db->query("SELECT * FROM robot_location WHERE loc='".$out['data'][0]."'");
		if (mysql_num_rows($rru)==0)
		{
		$content_tw_loc=parseUrl('http://geocode-maps.yandex.ru/1.x/?geocode='.urlencode($out['data'][0]).'&key=ANpUFEkBAAAAf7jmJwMAHGZHrcKNDsbEqEVjEUtCmufxQMwAAAAAAAAAAAAvVrubVT4btztbduoIgTLAeFILaQ==&results=1');
			$regextw='/<pos>(?<loc_tw>.*?)<\/pos>/is';
			preg_match_all($regextw,$content_tw_loc,$out_tw);
			$rru=$db->query('INSERT INTO robot_location (loc,loc_coord) VALUES (\''.$out['data'][0].'\',\''.$out_tw['loc_tw'][0].'\')');
		}
		else
		{
			$rru1=$db->fetch($rru);
			$out_tw['loc_tw'][0]=$rru1['loc_coord'];
		}
	}
	$regex='/(?<ch>\-?\d*?)\.(?<d>\d)/is';
	preg_match_all($regex,$out_tw['loc_tw'][0],$out);
	if (($out['ch'][0]!='') && ($out['d'][0]!='') && ($out['ch'][1]!='') && ($out['d'][1]!=''))
	{
		$twl=$out['ch'][0].'.'.$out['d'][0].' '.$out['ch'][1].'.'.$out['d'][1];
	}
	$outmas['loc']=$twl;
	$outmas['nick']=$nick;
	$regex='/<div class=\"label fl\_l\">День рождения\:<\/div>.*?<div class=\"labeled fl_l\">(?<byt>.*?)<\/div>/is';
	preg_match_all($regex,$cont,$out5);
	foreach ($agemonthe as $key => $item)
	{
		$out5['byt'][0]=preg_replace('/\s'.$item.'\s/isu','.'.($key+1).'.',$out5['byt'][0]);
	}
	$out5['byt'][0]=preg_replace('/\sг\./isu','',$out5['byt'][0]);
	$rg='/(?<day>\d*?)\.(?<mon>\d*?)\.(?<year>\d\d\d\d)/is';
	preg_match_all($rg,$out5['byt'][0],$ot);
	$age=0;
	if (($ot['mon'][0]!='') && ($ot['day'][0]!='') && ($ot['year'][0]!=''))
	{
		$age=intval((time()-mktime(0,0,0,$ot['mon'][0],$ot['day'][0],$ot['year'][0]))/(86400*365));
	}
	$outmas['age']=intval($age);
	$outmas['fol']=0;
	//print_r($outmas);
	sleep(1);
	return $outmas;
}
//get_vk('38135575');

?>