<?
/*require_once('com/config.php');
require_once('com/func.php');
require_once('com/db.php');
require_once('bot/kernel.php');
//require_once('com/tmhOAuth.php');
//require_once('com/vkapi.class.php');
require_once('../fulljob/adv_src_func.php');
error_reporting(0);*/
$db=new database();
$db->connect();

function get_twitter($nick)
{
	global $db;
	do
	{
		$cont=parseUrlproxy('http://twitter.com/'.$nick);
		if ($cont=='')
		{
			$attmp++;
			echo "\n".'continue...'."\n";
		}
	}
	while (($cont=='') && ($attmp<3));
	$xpth_loc='//span[@class="location profile-field"]';
	$location=get_pst_al($xpth_loc,$cont);
	//echo '!'.$location.'!';
	if (trim($location)!='')
	{
		$rru=$db->query("SELECT * FROM robot_location WHERE loc='".trim($location)."'");
		if (mysql_num_rows($rru)==0)
		{
			$content_tw_loc=parseUrl('http://geocode-maps.yandex.ru/1.x/?geocode='.urlencode(trim($location)).'&key=ANpUFEkBAAAAf7jmJwMAHGZHrcKNDsbEqEVjEUtCmufxQMwAAAAAAAAAAAAvVrubVT4btztbduoIgTLAeFILaQ==&results=1');
			$regextw='/<pos>(?<loc_tw>.*?)<\/pos>/is';
			preg_match_all($regextw,$content_tw_loc,$out_tw);
			$rru=$db->query('INSERT INTO robot_location (loc,loc_coord) VALUES (\''.trim($location).'\',\''.$out_tw['loc_tw'][0].'\')');
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
	$regex='/<img[^<]*?src=\"(?<ico>.*?)\"[^<]*?class\=\"avatar size73\"/isu';
	preg_match_all($regex,$cont,$out);
	// $xpth_fol='//a[@data-nav="followers"]/strong';
	// $fol=preg_replace('/[^\d]/isu','',get_pst_al($xpth_fol,$cont));
	$regex='/title="(?<fol>[\d\s]+)[^\d]+" data-nav="followers"/isu';
	preg_match_all($regex, $cont, $out);
	$fol=preg_replace('/\s/isu', '', $out['fol'][0]);
	$outmas['loc']=$twl;
	$outmas['fol']=$fol;
	$outmas['nick']=$nick;
	$outmas['name']=$nick;
	$outmas['gender']=0;
	$outmas['age']=0;
	$outmas['ico']=$out['ico'][0];
	//print_r($outmas);
	echo $twl.' '.$fol.' '.$out['ico'][0]."\n";
	return $outmas;
}

function get_twitter2($nick)
{
	global $db;
	do
	{
		$cont=parseUrlproxy('http://twitter.com/'.$nick);
		if ($cont=='')
		{
			$attmp++;
			echo "\n".'continue...'."\n";
		}
	}
	while (($cont=='') && ($attmp<3));
	$cont=preg_replace('/<head>.*?<\/head>/isu','',$cont);
	$cont=preg_replace('/\s+/isu',' ',$cont);
	$cont=preg_replace('/<script.*?>.*?<\/script>/isu','',$cont);
	$cont=preg_replace('/<style.*?>.*?<\/style>/isu','',$cont);
	$cont=preg_replace('/<\/?strong>/isu','',$cont);
	$cont=preg_replace('/<span class="location">/isu',' loc ',$cont);
	$cont=preg_replace('/<img src="([^\"]*?)" alt="[^\"]*?" class="avatar size73">/isu',' ava $1 ',$cont);
	echo $cont;
	$regex='/<span class=[\"\']location profile-field[\"\']>(?<loc>.*?)<\/span>/isu';
	preg_match_all($regex, $cont, $outloc);
	$location=$outloc['loc'][0];
	if (trim($location)!='')
	{
		$rru=$db->query("SELECT * FROM robot_location WHERE loc='".trim($location)."'");
		if (mysql_num_rows($rru)==0)
		{
			$content_tw_loc=parseUrl('http://geocode-maps.yandex.ru/1.x/?geocode='.urlencode(trim($location)).'&key=ANpUFEkBAAAAf7jmJwMAHGZHrcKNDsbEqEVjEUtCmufxQMwAAAAAAAAAAAAvVrubVT4btztbduoIgTLAeFILaQ==&results=1');
			$regextw='/<pos>(?<loc_tw>.*?)<\/pos>/is';
			preg_match_all($regextw,$content_tw_loc,$out_tw);
			$rru=$db->query('INSERT INTO robot_location (loc,loc_coord) VALUES (\''.trim($location).'\',\''.$out_tw['loc_tw'][0].'\')');
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
	$regex='/data-element-term=[\"\']follower_stats[\"\'] data-nav=[\"\']followers[\"\']\s*>(?<fol>[\s\d\,\.]+).*?<\/a>/isu';
	preg_match_all($regex, $cont, $out);
	print_r($out);
	$outmas['fol']=preg_replace('/[^\d+]/isu','',$out['fol'][0]);
	$cont=preg_replace('/<.*?>/isu','|||',$cont);
	$mcont=explode('|||',$cont);
	// print_r($mcont);
	foreach ($mcont as $item)
	{
		$item=trim($item);
		if ($item=='') continue;
		if (preg_match('/^ava\shttps?\:\/\//isu',$item))
		{
			$mava=explode(' ', $item);
			$outmas['ico']=$mava[1];
		}
	}
	$outmas['nick']=$nick;
	$outmas['name']=$nick;
	$outmas['gender']=0;
	$outmas['age']=0;
	return $outmas;
}


// $cont=parseUrl('http://188.120.239.225/getlist.php');
// $mproxy=json_decode($cont,true);

// $_SERVER['argv'][2]=50;
// $_SERVER['argv'][1]=12;

// $qpr=$db->query('SELECT * FROM tp_proxys WHERE valid=1 AND response_time<10000 ORDER BY response_time ASC');
// while ($pr=$db->fetch($qpr))
// {
// 	//print_r($pr);
// 	if (count($mproxy)>10) continue;
// 	if ((!in_array($pr['proxy'],$mproxy)) && (($iter % $_SERVER['argv'][2])==$_SERVER['argv'][1]))
// 	{
// 		$mproxy[]=$pr['proxy'];
// 	}
// 	$iter++;
// }

// for ($i=0;$i<100;$i++)
// {
// 	shuffle($mproxy);
// 	get_twitter2('ru_wobot');
// }

?>
