<?php
$hash = $argv[1];

$redis=new Redis() or die("Can'f load redis module.");
$redis->connect('127.0.0.1');
$additional_params = $redis->get("export_".$hash);

$params = json_decode($additional_params,true);

$_POST=$params['POST'];
$_GET=$params['GET'];
//print_r($_GET);
if (!isset($_POST['max_posts']))
	$_POST['max_posts']=50000;
if (!isset($_POST['max_authors']))
	$_POST['max_authors']=50000;

$not_a_filter = array('max_posts','max_authors','stime','etime','start','end','order_id');
foreach ($_POST as $key => $value) {
	if(!in_array($key,$not_a_filter)){
		$filters[$key]=$value;
	}
}

//$_POST['mw_привет']=1;
// $_POST['mw_мясо']=1;
// $_POST['post_read']=0;
// $_POST['post_imp']=0;
// $_POST['gender']='м';
// $_POST['age_min']=1;
// $_POST['age_max']=60;

error_reporting(0);
require_once('/var/www/new/com/config.php');
require_once('/var/www/new/com/db.php');
require_once('/var/www/new/com/func.php');
require_once('/var/www/new/bot/kernel.php');
require_once('auth.php');
require_once('func_export.php');
require_once('/var/www/com/loc.php');
//------------new----------------
require_once('/var/www/com/sent.php');
require_once('/var/www/new/com/porter.php');
require_once( '/var/www/new/com/phpmorphy/src/common.php');

$dir = '/var/www/new/com/phpmorphy/dicts';
$lang = 'ru_RU';
$opts = array( 'storage' => PHPMORPHY_STORAGE_FILE );
$morphy = new phpMorphy($dir, $lang, $opts);

$word_stem=new Lingua_Stem_Ru();
//------------new----------------
date_default_timezone_set ( 'Europe/Moscow' );

if (isset($_POST['start'])) $_POST['stime']=$_POST['start'];
if (isset($_POST['end'])) $_POST['etime']=$_POST['end'];

$FILTER_REGEX = '/[^а-яА-Яa-zA-ZёЁ\.\,\'\"0-9\!\?\s\/\@\:\(\)\“\_\-\&\;\#\<\>]/isu';
//export_fix_1
function detect_country($string){
	global $wobot;
	return array_search($string,$wobot['destn3']);
}
//export_fix_1 end
function getTonalIndex($engagement,$tonalArray)
{
	if(count($engagement)!=count($tonalArray)) return 0;	
	$sum=0;
	for ($i=0; $i < count($engagement); $i++) { 
		if ($tonalArray[$i]=='+') {
			$sum+=$engagement[$i];
		}elseif($tonalArray[$i]=='-'){
			$sum-=$engagement[$i];
		}
	}
	return $sum;
}
function preAgeCheck($out_age){
	$intervals = array('do14','15-18','19-25','26-30','31-35','36-45','46-55','af56');
	foreach ($intervals as $item) {
		if(!isset($out_age[$item])){
			$out_age[$item]=array();
		}
	}
	return $out_age;
}
function addAgeInf($age,$nastr,$out_age){
	switch ($age)
	{
	case ($age<=14):
	{
		$out_age['do14']['count']++;
		if ($nastr==1)
		{
			$out_age['do14']['pos']++;
		}
		else
		if ($nastr==-1)
		{
			$out_age['do14']['neg']++;
		}
		else
		if ($nastr==0)
		{
			$out_age['do14']['neu']++;
		}
	}
	break;
	case (($age>=15) && ($age<=18)):
	{
		$out_age['15-18']['count']++;
		if ($nastr==1)
		{
			$out_age['15-18']['pos']++;
		}
		else
		if ($nastr==-1)
		{
			$out_age['15-18']['neg']++;
		}
		else
		if ($nastr==0)
		{
			$out_age['15-18']['neu']++;
		}
	}
	break;
	case (($age>=19) && ($age<=25)):
	{
		$out_age['19-25']['count']++;
		if ($nastr==1)
		{
			$out_age['19-25']['pos']++;
		}
		else
		if ($nastr==-1)
		{
			$out_age['19-25']['neg']++;
		}
		else
		if ($nastr==0)
		{
			$out_age['19-25']['neu']++;
		}
	}
	break;
	case (($age>=26) && ($age<=30)):
	{
		$out_age['26-30']['count']++;
		if ($nastr==1)
		{
			$out_age['26-30']['pos']++;
		}
		else
		if ($nastr==-1)
		{
			$out_age['26-30']['neg']++;
		}
		else
		if ($nastr==0)
		{
			$out_age['26-30']['neu']++;
		}
	}
	break;
	case (($age>=31) && ($age<=35)):
	{
		$out_age['31-35']['count']++;
		if ($nastr==1)
		{
			$out_age['31-35']['pos']++;
		}
		else
		if ($nastr==-1)
		{
			$out_age['31-35']['neg']++;
		}
		else
		if ($nastr==0)
		{
			$out_age['31-35']['neu']++;
		}
	}
	break;
	case (($age>36) && ($age<=45)):
	{
		$out_age['36-45']['count']++;
		if ($nastr==1)
		{
			$out_age['36-45']['pos']++;
		}
		else
		if ($nastr==-1)
		{
			$out_age['36-45']['neg']++;
		}
		else
		if ($nastr==0)
		{
			$out_age['36-45']['neu']++;
		}
	}
	break;
	case (($age>=46) && ($age<=55)):
	{
		$out_age['46-55']['count']++;
		if ($nastr==1)
		{
			$out_age['46-55']['pos']++;
		}
		else
		if ($nastr==-1)
		{
			$out_age['46-55']['neg']++;
		}
		else
		if ($nastr==0)
		{
			$out_age['46-55']['neu']++;
		}
	}
	break;
	case ($age>=56):
	{
		$out_age['af56']['count']++;
		if ($nastr==1)
		{
			$out_age['af56']['pos']++;
		}
		else
		if ($nastr==-1)
		{
			$out_age['af56']['neg']++;
		}
		else
		if ($nastr==0)
		{
			$out_age['af56']['neu']++;
		}
	}
	break;	
	}
	return $out_age;
}

ignore_user_abort(true);
set_time_limit(0);
ini_set('max_execution_time',0);
ob_implicit_flush();
ini_set("memory_limit", "2048M");

$db = new database();
$db->connect();

$res_micr=array('mail.ru','twitter.com','mblogi.qip.ru','rutvit.ru','friendfeed.com','godudu.com','juick.com','jujuju.ru','sports.ru','mylife.ru','chikchirik.ru','chch.ru','f5.ru','zizl.ru','smsnik.com');
$soc_netw=array('1c-club.com','cjclub.ru','diary.ru','facebook.com','myspace.com','orkut.com','vkontakte.ru','stranamam.ru','dietadiary.com','ya.ru','vk.com');
$nov_res=array('km.ru','regnum.ru','akm.ru','arms-tass.su','annews.ru','itar-tass.com','interfax.ru','interfax-russia.ru','oreanda.ru','1prime.ru','rbc.ru','rbc.ru','ria.ru','rosbalt.ru','tasstelecom.ru','finmarket.ru','expert.ru','newtimes.ru','akzia.ru','aif.ru','argumenti.ru','bg.ru','vedomosti.ru','izvestia.ru','itogi.ru','kommersant.ru','kommersant.ru','kp.ru','mospravda.ru','mn.ru','mk.ru','ng.ru','novayagazeta.ru','newizv.ru','kommersant.ru','politjournal.ru','profile.ru','rbcdaily.ru','gosrf.ru','rodgaz.ru','rg.ru','russianews.ru','senat.org','sobesednik.ru','tribuna.ru','trud.ru','newstube.ru','vesti.ru','mir24.tv','ntv.ru','1tv.ru','rutv.ru','tvkultura.ru','tvc.ru','tvzvezda.ru','5-tv.ru','ren-tv.com','radiovesti.ru','govoritmoskva.ru','ruvr.ru','kommersant.ru','cultradio.ru','radiomayak.ru','radiorus.ru','rusnovosti.ru','msk.ru','infox.ru','lenta.ru','lentacom.ru','newsru.com','temadnya.ru','newsinfo.ru','rb.ru','utronews.ru','moscow-post.ru','apn.ru','argumenti.ru','wek.ru','vz.ru','gazeta.ru','grani.ru','dni.ru','evrazia.org','ej.ru','izbrannoe.ru','inopressa.ru','inosmi.ru','inforos.ru','kommersant.ru','kreml.org','polit.ru','pravda.ru','rabkor.ru','russ.ru','smi.ru','svpressa.ru','segodnia.ru','stoletie.ru','strana.ru','utro.ru','fedpress.ru','lifenews.ru','belrus.ru','pfrf.ru','rosculture.ru','kremlin.ru','gov.ru','rosnedra.com');
$exc_src=array('diary.ru','foursquare.com','ya.ru','yandex.ru','twitpic.com','mail.ru','kp.ru','liveinternet.ru','ya.ru');
//original user
// $_GET['test_token'] = '06e82decff5d0eb94004c8d9c7bf1671';
// $_GET['test_user_id'] = '1187';

//new user
// $_GET['test_token'] = '67d66ffc51b4e4f1a3f7f796bd6e6f7e';
// $_GET['test_user_id'] = '204';

// new user 2
// $_GET['test_token'] = 'a0c74b68b23c3a94e910fd0060f33963';
// $_GET['test_user_id'] = '194';
// echo '123';
auth();
// $user['tariff_id']=1;$user['user_id']=194;
//echo "12";
if (!$loged){
	echo 'not_loged_in!';
	die();
}
$db->query("INSERT INTO blog_export (order_id,export_time,start_time,end_time,hash_code,progress) VALUES(".$_POST['order_id'].",".time().",".strtotime($_POST['start']).",".strtotime($_POST['end']).",\"".$hash."\",0)");
//echo '123';
if ($user['tariff_id']==3)
{
	$infus=$db->query('SELECT order_id,user_id FROM blog_orders WHERE order_id='.$_POST['order_id'].' LIMIT 1');
	$usri=$db->fetch($infus);
	if ($usri['user_id']==61)
	{
		$user['user_id']=61;
	}
}
// //first
// $_POST['order_id']=2286;
// $_POST['stime']='01.09.2012';
// $_POST['etime']='30.12.2012';

// //second
// // $_POST['order_id']=2331;
// // $_POST['stime']='01.01.2013';
// // $_POST['etime']='14.01.2013';

// //new user
// // $_POST['order_id']=737;
// // $_POST['stime']='01.03.2012';
// // $_POST['etime']='31.12.2012';

// //new user2
// $_POST['order_id']=712;
// $_POST['stime']='20.03.2012';
// $_POST['etime']='01.02.2013';

//$_POST=$_GET;
if (intval($_POST['order_id'])==0) die();
//echo '12345';
//print_r($_SESSION);
//print_r($user);
//$res=$db->query("SELECT order_id,order_name,order_keyword,order_start,order_end,order_last,third_sources,order_metrics,order_src,order_graph from blog_orders WHERE order_id=".intval($_POST['order_id'])." and user_id=".intval($user['user_id'])." LIMIT 1");
$res=$db->query("SELECT order_id,order_name,order_keyword,order_start,order_end,order_last,third_sources from blog_orders WHERE order_id=".intval($_POST['order_id'])." and user_id=".intval($user['user_id'])." LIMIT 1");
$order=$db->fetch($res);

$type_text="";

unset($mtag);
//$query1='SELECT * FROM blog_tag WHERE user_id='.intval($user['user_id']);
$query1='SELECT * FROM blog_tag WHERE order_id='.intval($_POST['order_id']);
   $respost1=$db->query($query1);
while($tgl1 = $db->fetch($respost1))
{
	$tagsall[$tgl1['tag_tag']]=$tgl1['tag_name'];
	$tagsallrev[$tgl1['tag_name']]=$tgl1['tag_tag'];
	$mtag[$tgl1['tag_tag']]=$tgl1['tag_name'];
}
$total_tag_count = count($mtag);
$tag_list = array_keys($mtag);
$qqq=get_query();
//echo $qqq; die();
$qqq1=preg_replace('/SELECT \* FROM/is','SELECT count(*) as cnt FROM',$qqq);
$qqq1=preg_replace('/ORDER BY p\.post_time DESC/is','GROUP BY post_host',$qqq1);
$qqq2=preg_replace('/ORDER BY p\.post_time DESC/is','ORDER BY p.post_engage DESC LIMIT 10',$qqq);
//echo '<br>'.$qqq2.'<br>';
$countqposts=$db->query($qqq1);//'SELECT count(*) as cnt from blog_post as p LEFT JOIN robot_blogs2 AS b ON p.blog_id=b.blog_id WHERE p.order_id="'.intval($_POST['order_id']).'" AND p.post_time>'.intval(strtotime($_POST['ntime'])).' AND p.post_time<'.intval(strtotime($_POST['etime'])).' GROUP BY post_host');
while ($count=$db->fetch($countqposts))
{
	$cnt+=$count['cnt'];
	$cnt_host++;
}
$qpost=$db->query($qqq); // запрос на все посты $sqw='SELECT * from blog_post as p LEFT JOIN robot_blogs2 AS b ON p.blog_id=b.blog_id WHERE p.order_id="'.intval($_POST['order_id']).'" '.getisshow3().' ORDER BY p.post_time DESC';
$page4_post=$db->query($qqq2); // $sqw_page4='SELECT * from blog_post as p LEFT JOIN robot_blogs2 AS b ON p.blog_id=b.blog_id WHERE p.order_id="'.intval($_POST['order_id']).'" '.getisshow3().' ORDER BY p.post_engage DESC LIMIT 10';
//$countqposts=$db->query('SELECT count(*) as cnt from blog_post as p LEFT JOIN robot_blogs2 AS b ON p.blog_id=b.blog_id WHERE p.order_id="'.intval($_POST['order_id']).'" AND p.post_time>'.intval(strtotime($_POST['ntime'])).' AND p.post_time<'.intval(strtotime($_POST['etime'])).' GROUP BY post_host');
while($pst = $db->fetch($qpost))
{		
	$outcash['link'][$ii]=str_replace("\n","",$pst['post_link']);
	$outcash['time'][$ii]=$pst['post_time']; // time
	$outcash['content'][$ii]=$pst['post_content']; //content
	$outcash['fcontent'][$ii]=$pst['ful_com_post'];
	$outcash['isfav'][$ii]=$pst['post_fav']; // is favorite
	$outcash['nastr'][$ii]=$pst['post_nastr'];
	$outcash['isspam'][$ii]=$pst['post_spam'];
	$outcash['nick'][$ii]=$pst['blog_nick'];
	$outcash['tag'][$ii]=$pst['post_tag'];
	$outcash['comm'][$ii]=$pst['blog_readers'];
	$outcash['eng'][$ii]=$pst['post_engage'];
	//export_fix_1 begin
	$aeng=json_decode($pst['post_advengage'],true);
	$outcash['comment'][$ii]=intval($aeng['comment']);
	$outcash['likes'][$ii]=intval($aeng['likes']);
	$outcash['retweet'][$ii]=intval($aeng['retweet']);
	//export_fix_1 end
	$outcash['loc'][$ii]=$pst['blog_location'];
	$outcash['gender'][$ii]=$pst['blog_gender'];
	$outcash['age'][$ii]=$pst['blog_age'];
	$outcash['login'][$ii]=$pst['blog_login'];
	$outcash['host'][$ii]=$pst['post_host'];
	$outcash['blog_id'][$ii]=$pst['blog_id'];
	$outcash['blog_link'][$ii]=$pst['blog_link'];
	$ii++;
	//$arroutcash['login']		
	$outmas_count_comments += intval($aeng['comment']);
	$outmas_count_likes += intval($aeng['likes']);		
	$outmas_count_retweets += intval($aeng['retweet']);
}
$kk=0;
	
$outmas['Sh1']=array();
$outmas['Sh2']=array();
$outmas['Sh2']['time_distr']=array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0);
$outmas['Sh3']=array();
$outmas['Sh4']=array();
$outmas['Sh5']=array();
$outmas['Sh6']=array();
$outmas['Sh7']=array();
$outmas['Sh8']=array();
$outmas['Sh9']=array();
$outmas['Sh10']=array();

$out_age['do14']=array();
$out_age['15-18']=array();
$out_age['19-25']=array();
$out_age['26-30']=array();
$out_age['31-35']=array();
$out_age['36-45']=array();
$out_age['46-55']=array();
$out_age['af56']=array();
while($pst4 = $db->fetch($page4_post))
{	

	if (count($word)!=0)
	{				
		$c=0;
		foreach ($word as $item)
		{
			if ($_POST['words']=='selected')
			{
				if (preg_match('/[\s\t]'.$word_stem->stem_word($item).'/isu',$pst4['post_content']))
				{
					$c++;
				}
			}
			elseif ($_POST['words']=='except')
			{
				if (!preg_match('/[\s\t]'.$word_stem->stem_word($item).'/isu',$pst4['post_content']))
				{
					$c++;
				}
			}
		}
		if ($c==0) continue;
	}
	/*$outmas['Sh4']['top'][$kk]['content']=str_replace("\n",'',$pst4['post_content']);
	$outmas['Sh4']['top'][$kk]['src']=$pst4['post_host'];
	$outmas['Sh4']['top'][$kk]['engage']=$pst4['post_engage'];
	$outmas['Sh4']['top'][$kk]['foll']=$pst4['blog_readers'];*/			
	if ($pst4['blog_id']==0)
	{
		//print_r($pst4);
		$regex='/http\:\/\/(?<nick>.*?)\./is';
		//echo $out['nick'][0].' ';
		preg_match_all($regex,$pst4['post_link'],$out);
		$new_us=$db->query('SELECT * FROM robot_blogs2 WHERE blog_nick=\''.$out['nick'][0].'\' AND blog_link=\'livejournal\.com\'');
		$us=$db->fetch($new_us);
		$pst4['blog_readers']=$us['blog_readers'];
	}
	$outmas['Sh4']['top'][0][]=count($outmas['Sh4']['top'][0])+1;
	//if ((intval(date('H',$pst4['post_time']))>0)||(intval(date('i',$pst4['post_time']))>0)||(intval(date('s',$pst4['post_time']))>0)) $stime=date("H:i:s d.m.Y",$pst4['post_time']);
	//else $stime=date("d.m.Y",$pst4['post_time']);
	$stime=date("H:i:s d.m.Y",$pst4['post_time']);
	$ss_time=explode(' ',$stime);
	//$outmas['Sh4']['top'][1][]=$stime;
	//$outmas['Sh4']['top'][2][]=$stime;
	$outmas['Sh4']['top'][1][]=($ss_time[1]=='')?$ss_time[0]:$ss_time[1];
	$outmas['Sh4']['top'][2][]=($ss_time[1]=='')?'':$ss_time[0];
	$outmas['Sh4']['top'][3][]=iconv("UTF-8", "UTF-8//IGNORE",str_replace('\"','',str_replace("\n",'',html_entity_decode($pst4['post_content']))));
	$outmas['Sh4']['top'][4][]=iconv("UTF-8", "UTF-8//IGNORE", str_replace("\n","",$pst4['post_link']));
	if ($pst4['post_host']=='livejournal.com')
	{
		if ($pst4['blog_id']!=0)
		{
			$outmas['Sh4']['top'][5][]=iconv("UTF-8", "UTF-8//IGNORE", 'http://'.$pst4['blog_login'].'.livejournal.com/');
		}
		else
		{
			$regex='/http\:\/\/(?<nick>.*?)\./isu';
			preg_match_all($regex,$pst4['post_link'],$out);
			$outmas['Sh4']['top'][5][]=iconv("UTF-8", "UTF-8//IGNORE", 'http://'.$out['nick'][0].'.livejournal.com/');
		}
	}
	else
	if (($pst4['post_host']=='vk.com') || ($pst4['post_host']=='vkontakte.ru'))
	{
		$outmas['Sh4']['top'][5][]='http://vk.com/'.($pst4['blog_login'][0]=='-'?'club'.mb_substr($pst4['blog_login'],1,mb_strlen($pst4['blog_login'],'UTF-8')-1,'UTF-8'):'id'.$pst4['blog_login']);
	}
	else
	if ($pst4['post_host']=='twitter.com')
	{
		$outmas['Sh4']['top'][5][]='http://twitter.com/'.$pst4['blog_login'];
	}
	else
	if ($pst4['post_host']=='facebook.com')
	{
		$outmas['Sh4']['top'][5][]='http://facebook.com/'.$pst4['blog_login'];
	}
	else
	if ($pst4['post_host']=='mail.ru')
	{
		$outmas['Sh4']['top'][5][]='http://blogs.'.$pst4['blog_link'].'/'.$pst4['blog_login'];
	}
	else
	if ($pst4['post_host']=='liveinternet.ru')
	{
		$outmas['Sh4']['top'][5][]='http://liveinternet.ru/users/'.$pst4['blog_login'];
	}
	else
	if ($pst4['post_host']=='ya.ru')
	{
		$outmas['Sh4']['top'][5][]='http://'.$pst4['blog_login'].'.ya.ru';
	}
	else
	if ($pst4['post_host']=='yandex.ru')
	{
		$outmas['Sh4']['top'][5][]='http://'.$pst4['blog_login'].'.ya.ru';
	}
	else
	if ($pst4['post_host']=='rutwit.ru')
	{
		$outmas['Sh4']['top'][5][]='http://rutwit.ru/'.$pst4['blog_login'];
	}
	else
	if ($pst4['post_host']=='rutvit.ru')
	{
		$outmas['Sh4']['top'][5][]='http://rutwit.ru/'.$pst4['blog_login'];
	}
	else
	if ($pst4['post_host']=='babyblog.ru')
	{
		$outmas['Sh4']['top'][5][]='http://www.babyblog.ru/user/info/'.$pst4['blog_login'];
	}
	else
	if ($pst4['post_host']=='blog.ru')
	{
		$outmas['Sh4']['top'][5][]='http://'.$pst4['blog_login'].'.blog.ru/profile';
	}
	else
	if ($pst4['post_host']=='foursquare.com')
	{
		$outmas['Sh4']['top'][5][]='https://ru.foursquare.com/'.$pst4['blog_login'];
	}
	else
	if ($pst4['post_host']=='kp.ru')
	{
		$outmas['Sh4']['top'][5][]='http://blog.kp.ru/users/'.$pst4['blog_login'].'/profile/';
	}
	else
	if ($pst4['post_host']=='aif.ru')
	{
		$outmas['Sh4']['top'][5][]='http://blog.aif.ru/users/'.$pst4['blog_login'].'/profile';
	}
	else
	if ($pst4['post_host']=='friendfeed.com')
	{
		$outmas['Sh4']['top'][5][]='http://friendfeed.com/'.$pst4['blog_login'];
	}
	else
    if ($pst4['post_host']=='google.com')
    {
    	$outmas['Sh4']['top'][5][]='http://plus.google.com/'.$pst4['blog_login'].'/about';
    }
	else
	{
		$outmas['Sh4']['top'][5][]=$pst4['post_link'];
	}
	$hosts[$pst4['post_host']]++;
	$aengjson=json_decode($pst4['post_advengage'],true);
	$outmas['Sh4']['top'][6][]=intval($aengjson['comment']);
	$outmas['Sh4']['top'][7][]=intval($aengjson['likes']);
	$outmas['Sh4']['top'][8][]=intval($aengjson['retweet']);
	$outmas['Sh4']['top'][9][]=($pst4['post_nastr']>0) ? '+' : ($pst4['post_nastr']<0) ? '-' : '';
	$outmas['Sh4']['top'][10][]=intval($pst4['post_engage']);
	$outmas['Sh4']['top'][11][]=intval($pst4['blog_readers']);
	$kk++;
}		
$outmas['Sh4']['dominant_host'] = array_search(max($hosts),$hosts);
if ($outmas['Sh4']['dominant_host']==null){
	$outmas['Sh4']['dominant_host']="";
}
$outmas['Sh4']['tonalIndex']=getTonalIndex($outmas['Sh4']['top'][10],$outmas['Sh4']['top'][9]);		
// echo($outmas['Sh4']['dominant_host']."\n");
// die();	
foreach ($outcash['link'] as $key => $llink)
{			
	$mentions_counter++;	
	//echo $llink.'<br>';
	$link=urldecode($llink);
	$time=$outcash['time'][$key];
	$content=html_entity_decode($outcash['content'][$key],ENT_QUOTES,'UTF-8');
	$parts=explode("\n",$content);
	$content=(($parts[0]!='')?$parts[0]:$parts[1]);
	$fcontent=preg_replace('/(\d)\)\./isu','$1.',$outcash['fcontent'][$key]);
	$comm=intval($outcash['comm'][$key]);
	$gn_time_start = microtime(true);
	$isfav=$outcash['isfav'][$key];
	$tag=$outcash['tag'][$key];
	$rtag=explode(',',$tag);
	$eng=$outcash['eng'][$key];
	$total_end+=$eng;
	$loc=$outcash['loc'][$key];
	$gender=$outcash['gender'][$key];
	$blog_id=$outcash['blog_id'][$key];
	if ($gender==0)
	{
		$gender='-';
	}
	else
	{
		if ($gender==1)
		{
			$gender='Ж';
		}
		else
		{
			$gender='М';
		}
	}
	$age=$outcash['age'][$key];
	$age = (is_null($age) || $age==0) ? 0 : intval($age);
	$strtag='';
	if($order['order_engage']=='1')
	{
		$engtext='Engagement'."\t";
		$eng=$outcash['eng'][$key]."\t";
	}
	else
	{
		$engtext='';
		$eng='';
	}
	//$outmas['Sh2']['table']['count'][mktime(0,0,0,date("n",$time),date("j",$time),date("Y",$time))]++;
	//$outmas['Sh2']['table']['count']['date']=++;
	//$strtag=mb_substr($strtag,0,mb_strlen($strtag,"UTF-8")-2,"UTF-8");
	if ($isfav==1) $isfav='+';
	else $isfav='-';
	$nastr=$outcash['nastr'][$key];

	$isspam=$outcash['isspam'][$key];
	if ($isspam==1) $isspam='+';
	else $isspam='-';

	$gn_time_end = microtime(true);
	$gn_time += $gn_time_end - $gn_time_start;

	$nick = is_null($outcash['nick'][$key]) ? "" : $outcash['nick'][$key];
	//if ($nick!='')
	//if ((trim($nick)!='') && ($hn!='.'))
	{
		//$uniq_mas[$nick.':'.$blog_id]++;
	}
	$login=$outcash['login'][$key];
    $hn=parse_url($link);
    $hn=$hn['host'];
    $ahn=explode('.',$hn);
    $hn = $ahn[count($ahn)-2].'.'.$ahn[count($ahn)-1];
	$hh = $ahn[count($ahn)-2];
	$hosts_engeges[$hn]+=$eng;

	if($nastr==1){
		$hosts_positive[$hn]++;
	}elseif($nastr==-1){
		$hosts_negative[$hn]++;
	}

	if ($hn=='.')
	{
		$hn=$outcash['host'][$key];
	}
	//echo $hn.' '.$link.' '.$content.' '.$nick.'<br>';
	if (($hn=='vk.com') || ($hn=='vkontakte.ru') || ($hn=='facebook.com'))
	{
		$count_likes_all+=$eng;
		if ($hn=='facebook.com')
		{
			if (!isset($mvalue_src[$login.':'.$hn]) && ($login!=''))
			{
				$value_fb+=$comm;
				$mvalue_src[$login.':'.$hn]=1;
			}
		}
		else
		{
			if (!isset($mvalue_src[$login.':'.$hn]) && ($login!=''))
			{
				$value_vk+=$comm;
				$mvalue_src[$login.':'.$hn]=1;
			}
		}
		//$outmas[10]['eng_mas'][0][0]++;
	}
	elseif ($hn=='livejournal.com')
	{
		$count_comment_all+=$eng;
		if (!isset($mvalue_src[$login.':'.$hn]) && ($login!=''))
		{
			$value_lj+=$comm;
			$mvalue_src[$login.':'.$hn]=1;
		}
		//$outmas[10]['eng_mas'][1][0]++;
	}
	elseif ($hn=='twitter.com')
	{
		$count_retwits_all+=$eng;
		if (!isset($mvalue_src[$login.':'.$hn]) && ($login!=''))
		{
			$value_tw+=$comm;
			$mvalue_src[$login.':'.$hn]=1;
		}
		//$outmas[10]['eng_mas'][2][0]++;
	}
	if ($hn!='.')
	{
		//echo($hn."\n");
		$mcount_res[$hn]++;
	}
	//echo $hn.' '.$link.' '.$nick.'<br>';
	if ($hn=='livejournal.com')
	{
		$rgx='/\/\/(?<nk>.*?)\./is';
		preg_match_all($rgx,$link,$out);
		$nick=$out['nk'][0];
		$login=$nick;
	}
	$count_top_res[mktime(0,0,0,date("n",$time),date("j",$time),date("Y",$time))][$hn]++;
	if ((trim($nick)!='') && ($hn!='.'))
	{
		$uniq_mas[$nick.':'.$blog_id]++;
		$top_speakers[$nick.':'.$blog_id]['comments_count']+=$outcash['comment'][$key];
		$top_speakers[$nick.':'.$blog_id]['likes_count']+=$outcash['likes'][$key];
		$top_speakers[$nick.':'.$blog_id]['retweet_count']+=$outcash['retweet'][$key];				

		$top_speakers[$nick.':'.$blog_id]['location']=(!$wobot['destn1'][$loc]) ? '' : $wobot['destn1'][$loc];;
		$top_speakers[$nick.':'.$blog_id]['gender']=$gender;
		$top_speakers[$nick.':'.$blog_id]['age']=$age;

		$top_speakers[$nick.':'.$blog_id]['login']=$login;
		$top_speakers[$nick.':'.$blog_id]['count']++;
		$top_speakers[$nick.':'.$blog_id]['foll']=$comm;
		if ($nastr==1)
		{
			$top_speakers[$nick.':'.$blog_id]['pos']++;
		}
		elseif ($nastr==-1)
		{
			$top_speakers[$nick.':'.$blog_id]['neg']++;
		}
		else
		{
			$top_speakers[$nick.':'.$blog_id]['neu']++;
		}
		$top_speakers[$nick.':'.$blog_id]['src']=$hn;
		if ($hn=='twitter.com')
		{
			$top_speakers[$nick.':'.$blog_id]['link']='http://twitter.com/'.$login;
		}
		else
		if ($hn=='livejournal.com')
		{
			$top_speakers[$nick.':'.$blog_id]['link']='http://'.$login.'.livejournal.com';
		}
		else
		if (($hn=='vkontakte.ru') || ($hn=='vk.com'))
		{
			//echo $login.'!!!<br>';
			$top_speakers[$nick.':'.$blog_id]['link']='http://vk.com/id'.$login;
		}
		else
		if ($hn=='facebook.com')
		{
			$top_speakers[$nick.':'.$blog_id]['link']='http://facebook.com/'.$login;
		}
		else
		if ($hn=='mail.ru')
		{
			$top_speakers[$nick.':'.$blog_id]['link']='http://blogs.'.$outcash['blog_link'][$key].'/'.$login;
		}
		else
		if ($hn=='liveinternet.ru')
		{
			$top_speakers[$nick.':'.$blog_id]['link']='http://www.liveinternet.ru/users/'.$login;
		}
		else
		if ($hn=='ya.ru')
		{
			$top_speakers[$nick.':'.$blog_id]['link']='http://'.$login.'.ya.ru';
		}
		else
		if ($hn=='yandex.ru')
		{
			$top_speakers[$nick.':'.$blog_id]['link']='http://'.$login.'.ya.ru';
		}
		else
		if ($hn=='rutwit.ru')
		{
			$top_speakers[$nick.':'.$blog_id]['link']='http://rutwit.ru/'.$login;
		}
		else
		if ($hn=='rutvit.ru')
		{
			$top_speakers[$nick.':'.$blog_id]['link']='http://rutwit.ru/'.$login;
		}
		else
		if ($hn=='babyblog.ru')
		{
			$top_speakers[$nick.':'.$blog_id]['link']='http://www.babyblog.ru/user/info/'.$login;
		}
		else
		if ($hn=='blog.ru')
		{
			$top_speakers[$nick.':'.$blog_id]['link']='http://'.$login.'.blog.ru/profile';
		}
		else
		if ($hn=='foursquare.com')
		{
			$top_speakers[$nick.':'.$blog_id]['link']='https://ru.foursquare.com/'.$login;
		}
		else
		if ($hn=='kp.ru')
		{
			$top_speakers[$nick.':'.$blog_id]['link']='http://blog.kp.ru/users/'.$login.'/profile/';
		}
		else
		if ($hn=='aif.ru')
		{
			$top_speakers[$nick.':'.$blog_id]['link']='http://blog.aif.ru/users/'.$login.'/profile';
		}
		else
		if ($hn=='friendfeed.com')
		{
			$top_speakers[$nick.':'.$blog_id]['link']='http://friendfeed.com/'.$login;
		}
		else
	    if ($hn=='google.com')
	    {
	    	$top_speakers[$nick.':'.$blog_id]['link']='http://plus.google.com/'.$login.'/about';
	    }
	}
	//print_r($top_speakers);
	if (($hn=='twitter.com') || ($hn=='livejournal.com'))
	{
		$nick=$login;
	}
	//if (!in_array($hn,$resorrr))
	{
		//continue;
	}
	//print_r($resorrr);
	//echo $content."\t";
	//$time=mktime(0,0,0,date('n',$time),date('j',$time),date('Y',$time));
	if ((intval(date('H',$time))>0)||(intval(date('i',$time))>0)||(intval(date('s',$time))>0)) $stime=date("H:i:s d.m.Y",$time);
	else $stime=date("d.m.Y",$time);
	$isshow=1;
	$cc=0;

	if (in_array($hn,$soc_netw))
	{
		$type_re='социальная сеть';
	}
	else
	if (in_array($hn,$nov_res))
	{
		$type_re='новостной ресурс';
	}
	else
	if (in_array($hn,$res_micr))
	{
		$type_re='микроблог';
	}
	else
	{
		$type_re='форум или блог';
	}


	//if ($_POST['all_main']!='true')
	//{
		//$isshow=get_isshow();
	//}
	//echo $isshow;
	if ($nastr==1) $nstr='+';
	elseif ($nastr==-1) $nstr='-';
	else $nstr='';

		$content=strip_tags($content);
		$content_r=$content;
		//$content_r=rtrim($content_r);
		$content_f='';
		$content_r=preg_replace('[^A-Za-z0-9_]', '', $content_r);
		if (($order['ful_com']=='1') && (!in_array($hn,$exc_src)))
		{
			$content_f=$fcontent;
			$content_f=strip_tags($content_f);
			$content_f=html_entity_decode($content_f);
			$content_f=strip_tags(html_entity_decode(preg_replace('/[^а-яА-Яa-zA-ZёЁ\,\.\'\"\!\?0-9\/\@\:\(\)\“\_\-\&\;\#\<\>]/isu',' ',$content_f),ENT_QUOTES,'UTF-8'));			
		}
		else
		{
			$tabful="";
			$content_f='';
			$fulpretext='';
		}	

		$outmas['Sh1']['order_name']=iconv("UTF-8", "UTF-8//IGNORE", $order['order_name']);
		$outmas['Sh1']['order_time']=date('d.m.Y',strtotime($_POST['stime'])).' - '.((strtotime($_POST['etime'])==0)?date('d.m.Y',strtotime($_POST['etime'])):date('d.m.Y',strtotime($_POST['etime'])));
		$outmas['Sh1']['order_keyword']=iconv("UTF-8", "UTF-8//IGNORE", $order['order_keyword']);
		$outmas['Sh1']['filters']=isset($filters) ? $filters : array("filters"=>"none");
	
		$outmas['Sh2']['count_posts']=intval($cnt);
		// $outmas['Sh2']['uniq_auth']=count($uniq_mas);
		if ($order['order_end']!=0)
		{
			if ($order['order_last']<$order['order_end'])
			{
				$midle_cnt=intval($cnt/(($order['order_last']-$order['order_start'])/86400));
			}
			else
			{
				$midle_cnt=intval($cnt/(($order['order_end']-$order['order_start'])/86400));
			}
		}
		else
		{
			$midle_cnt=intval($cnt/(($order['order_last']-$order['order_start'])/86400));
		}
		if (!isset($mas_value[$login.':'.$hn]) && ($outcash['blog_id'][$key]!=0) && ($outcash['nick'][$key]!=''))
		{
			$value_count+=$comm;
			$mas_value[$login.':'.$hn]=1;
			//echo $nick.' '.$hn.' '.$comm.' '.$value_count.'<br>';
		}
		//export_fix_1 begin
		if ($ss_time[1]!='') {					
			$outmas['Sh2']['time_distr'][intval(substr($ss_time[0],0,2))]++;
		}

		$outmas['Sh2']['comments_count'] = $outmas_count_comments;
		$outmas['Sh2']['likes_count'] = $outmas_count_likes;
		$outmas['Sh2']['retweet_count'] = $outmas_count_retweets;
		//export_fix_1 end
		$outmas['Sh2']['post_in_day']=intval($cnt/((strtotime($_POST['etime'])-strtotime($_POST['stime'])+86400)/86400));//$midle_cnt;
		$outmas['Sh2']['count_hosts']=intval($cnt_host);
		$outmas['Sh2']['audience']=intval($value_count);
		// $outmas['Sh2']['uniq_auth']=count($mas_value);
		$outmas['Sh2']['period']=$_POST['sd'].' — '.$_POST['ed'];
		//based on code from ./export3.php 761-772----------------------------------------
		$digest = ($content_f=='')?$content:$content_f;
		if ($hn!='twitter.com'){
			$msent=get_sentence(preg_replace('/\s+/isu',' ',preg_replace($FILTER_REGEX,' ',$digest)));
			//print_r($msent);
			$digest=get_needed_sentence($msent,$order['order_keyword']);
		}
		if ($digest=='') $digest=$content;
		if (mb_strlen($digest,'UTF-8')>400) $digest=mb_substr($digest, 0, 400, 'UTF-8').'...';
		// -------------------------------------------------------------------------------
		$stime=date("H:i:s d.m.Y",$time);
		$ss_time=explode(' ',$stime);
		if($mentions_counter<=$_POST['max_posts']&&$_POST['type']!='analytics'){
			$outmas['Sh3'][0][]  = ($ss_time[1]=='')?$ss_time[0]:$ss_time[1]; // date
			$outmas['Sh3'][1][]  = ($ss_time[1]=='')?'':$ss_time[0]; // time				
			$outmas['Sh3'][2][]  = iconv("UTF-8", "UTF-8//IGNORE", $hn); // hostname
			$outmas['Sh3'][3][]  = mb_substr(iconv("UTF-8", "UTF-8//IGNORE", (($hn!='twitter.com')?preg_replace('/\s+/isu',' ',preg_replace($FILTER_REGEX,' ',(($content_f=='')?$content:$content_f))):' '.preg_replace($FILTER_REGEX,' ',$content))), 0, 32000,'UTF-8'); // content
			$outmas['Sh3'][4][]  = iconv("UTF-8", "UTF-8//IGNORE", trim($digest)); //digest
			$outmas['Sh3'][5][]  = iconv("UTF-8", "UTF-8//IGNORE", $link); // link
			$outmas['Sh3'][6][]  = $outcash['likes'][$key];
			$outmas['Sh3'][7][]  = $outcash['comment'][$key];
			$outmas['Sh3'][8][]  = $outcash['retweet'][$key];
			$outmas['Sh3'][9][]  = $comm; // ohvat
			$outmas['Sh3'][10][] = intval($eng); // engagement	
			$outmas['Sh3'][11][] = $nstr; // tonalnost +\-\empty
			$outmas['Sh3'][12][] = iconv("UTF-8", "UTF-8//IGNORE", $nick); // Author
			$outmas['Sh3'][13][] = $gender; // gender
			$outmas['Sh3'][14][] = $age;// age
			$outmas['Sh3'][15][] = (!$wobot['destn1'][$loc]) ? '' : $wobot['destn1'][$loc]; // region
			$outmas['Sh3'][16][] = $type_re; // type
			$outmas['Sh3'][17][] = $isspam; // removed
			$outmas['Sh3'][18][] = $isfav; // izbrannoe
			// if has tags
			if($total_tag_count>0){
				for ($i=0; $i < $total_tag_count; $i++) { 					
					//array_key_exists('abba', $some_dict)
					//$outmas['Sh3'][18+$i][]= in_array($tag_list[$i],$rtag)? $mtag[$tag_list[$i]] : ''; //tags					
					$outmas['Sh3'][19+$i][]= (in_array($tag_list[$i],$rtag)) ? $mtag[$tag_list[$i]] : ''; //tags					
				}

				foreach ($rtag as $itertag) {
					if($itertag!='')
						$tagTime[mktime(0,0,0,date("n",$time),date("j",$time),date("Y",$time))][$itertag]++;
					else
						$tagTime[mktime(0,0,0,date("n",$time),date("j",$time),date("Y",$time))]["undf"]++;
				}
			}
		}
		if (isset($wobot['destn1'][$loc]))
		{
			$top_loc[$wobot['destn1'][$loc]]['count']++;
			if ($nastr==1)
			{
				$top_loc[$wobot['destn1'][$loc]]['pos']++;
				$top_loc[$wobot['destn1'][$loc]]['neg']=intval($top_loc[$wobot['destn1'][$loc]]['neg']);
				$top_loc[$wobot['destn1'][$loc]]['neu']=intval($top_loc[$wobot['destn1'][$loc]]['neu']);
			}
			elseif ($nastr==-1)
			{
				$top_loc[$wobot['destn1'][$loc]]['neg']++;
				$top_loc[$wobot['destn1'][$loc]]['pos']=intval($top_loc[$wobot['destn1'][$loc]]['pos']);
				$top_loc[$wobot['destn1'][$loc]]['neu']=intval($top_loc[$wobot['destn1'][$loc]]['neu']);
			}
			else
			{
				$top_loc[$wobot['destn1'][$loc]]['neu']++;
				$top_loc[$wobot['destn1'][$loc]]['neg']=intval($top_loc[$wobot['destn1'][$loc]]['neg']);
				$top_loc[$wobot['destn1'][$loc]]['pos']=intval($top_loc[$wobot['destn1'][$loc]]['pos']);
			}
		}				
		if($age!=0)
			$ageTime[mktime(0,0,0,date("n",$time),date("j",$time),date("Y",$time))] = addAgeInf($age,$nastr,preAgeCheck($ageTime[mktime(0,0,0,date("n",$time),date("j",$time),date("Y",$time))]));

		foreach ($rtag as $itertag) {
			if($itertag==""){
				$tagStat["undf"]['count']++;
				if ($nastr==1){
					$tagStat["undf"]['pos']++;
				}elseif ($nastr==-1){
					$tagStat["undf"]['neg']++;
				}elseif ($nastr==0){
					$tagStat["undf"]['neu']++;
				}
				break;
			}
			$tagStat[$itertag]['count']++;
			if ($nastr==1){
				$tagStat[$itertag]['pos']++;
			}elseif ($nastr==-1){
				$tagStat[$itertag]['neg']++;
			}elseif ($nastr==0){
				$tagStat[$itertag]['neu']++;
			}
		}

		if ($nastr==1)
		{
			$count_wth_loc_pos++;
			$count_pos++;
			$din_nastr[mktime(0,0,0,date("n",$time),date("j",$time),date("Y",$time))]['pos']++;
			$din_nastr[mktime(0,0,0,date("n",$time),date("j",$time),date("Y",$time))]['neg']=intval($din_nastr[mktime(0,0,0,date("n",$time),date("j",$time),date("Y",$time))]['neg']);
			$din_nastr[mktime(0,0,0,date("n",$time),date("j",$time),date("Y",$time))]['neu']=intval($din_nastr[mktime(0,0,0,date("n",$time),date("j",$time),date("Y",$time))]['neu']);
			$top_pos['time'][]=$time;
			$top_pos['src'][]=$hn;
			$top_pos['link'][]=$link;
			$top_pos['type'][]=$type_re;
			$top_pos['content'][]=(($hn!='twitter.com')?preg_replace('/\s+/isu',' ',preg_replace('/[^а-яА-Яa-zA-ZёЁ\.\,\'\"0-9\!\?\s\/\@\:\(\)\“\_\-\&\;]/isu',' ',(($content_f=='')?$content:$content_f))):preg_replace('/[^а-яА-Яa-zA-ZёЁ\.\,\'\"0-9\!\?\s\/\@\:\(\)\“\-\_\&\;]/isu',' ',$content));//str_replace("\n",'',((($content_f=='')?rtrim($content):rtrim($content_f))));
			$top_pos['eng'][]=intval($eng);

			$top_pos['comment'][]=$outcash['comment'][$key];
			$top_pos['likes'][]=$outcash['likes'][$key];
			$top_pos['retweet'][]=$outcash['retweet'][$key];

			$top_pos['nick'][]=$nick;
			$top_pos['gen'][]=$gender;
			$top_pos['age'][]=$age;
			$top_pos['foll'][]=$comm;
			$top_pos['loc'][]=$wobot['destn1'][$loc];
			$top_pos['tags'][]=$strtag;
		}
		else
		if ($nastr==-1)
		{
			$count_wth_loc_neg++;
			$count_neg++;
			$din_nastr[mktime(0,0,0,date("n",$time),date("j",$time),date("Y",$time))]['neg']++;
			$din_nastr[mktime(0,0,0,date("n",$time),date("j",$time),date("Y",$time))]['pos']=intval($din_nastr[mktime(0,0,0,date("n",$time),date("j",$time),date("Y",$time))]['pos']);
			$din_nastr[mktime(0,0,0,date("n",$time),date("j",$time),date("Y",$time))]['neu']=intval($din_nastr[mktime(0,0,0,date("n",$time),date("j",$time),date("Y",$time))]['neu']);
			$top_neg['time'][]=$time;
			$top_neg['src'][]=$hn;
			$top_neg['link'][]=$link;
			$top_neg['type'][]=$type_re;
			$top_neg['content'][]=(($hn!='twitter.com')?preg_replace('/\s+/isu',' ',preg_replace('/[^а-яА-Яa-zA-ZёЁ\.\,\'\"0-9\!\?\s\/\@\:\(\)\“\_\-\&\;]/isu',' ',(($content_f=='')?$content:$content_f))):preg_replace('/[^а-яА-Яa-zA-ZёЁ\.\,\'\"0-9\!\?\s\/\@\:\(\)\“\_\-\&\;]/isu',' ',$content));//str_replace("\n",'',addslashes((($content_f=='')?trim($content):trim($content_f))));
			$top_neg['eng'][]=intval($eng);

			$top_neg['comment'][]=$outcash['comment'][$key];
			$top_neg['likes'][]=$outcash['likes'][$key];
			$top_neg['retweet'][]=$outcash['retweet'][$key];

			$top_neg['nick'][]=$nick;
			$top_neg['gen'][]=$gender;
			$top_neg['age'][]=$age;
			$top_neg['foll'][]=$comm;
			$top_neg['loc'][]=$wobot['destn1'][$loc];
			$top_neg['tags'][]=$strtag;
		}
		else
		if ($nastr==0)
		{
			$count_wth_loc_neu++;
			$count_neu++;
			$din_nastr[mktime(0,0,0,date("n",$time),date("j",$time),date("Y",$time))]['neu']++;
			$din_nastr[mktime(0,0,0,date("n",$time),date("j",$time),date("Y",$time))]['pos']=intval($din_nastr[mktime(0,0,0,date("n",$time),date("j",$time),date("Y",$time))]['pos']);
			$din_nastr[mktime(0,0,0,date("n",$time),date("j",$time),date("Y",$time))]['neg']=intval($din_nastr[mktime(0,0,0,date("n",$time),date("j",$time),date("Y",$time))]['neg']);
		}
		if ($gender=='М')
		{
			$out_gen['М']['count']++;
		}
		elseif ($gender=='Ж')
		{
			$out_gen['Ж']['count']++;
		}				
		if ($age!=0)
		{					
			//switch
			$out_age = addAgeInf($age,$nastr,$out_age);
		}				
}
//var_dump($tagStat);

$sum_host_engage = array_sum($hosts_engeges);
$num = $hosts_engeges[$outmas['Sh4']['dominant_host']]/$sum_host_engage;		
$outmas['Sh4']['dominant_host_engage'] = $num;

ksort($din_nastr);

foreach ($din_nastr as $key => $item)
{
	if ((intval(date('H',$key))>0)||(intval(date('i',$key))>0)) $key_mas_din=date("H:i:s d.m.Y",$key);
	else $key_mas_din=date("d.m.Y",$key);
	$mas_din_8[0][]=mktime(0,0,0,date("n",$key),date("j",$key),date("Y",$key));
	$mas_din_8[1][]=$item['neu'];
	$mas_din_8[2][]=$item['neg'];
	$mas_din_8[3][]=$item['pos'];
}
$outmas['Sh8']['dinams']=$mas_din_8;
$neu_neg_pos = intval($count_neu)+intval($count_neg)+intval($count_pos);
if($neu_neg_pos!=0){			
	$outmas['Sh8']['all'][0][]=intval($count_neu)/$neu_neg_pos;
	$outmas['Sh8']['all'][0][]=intval($count_neg)/$neu_neg_pos;
	$outmas['Sh8']['all'][0][]=intval($count_pos)/$neu_neg_pos;
}else{
	$outmas['Sh8']['all'][0]=array(0,0,0);
}		
$outmas['Sh8']['all'][1][]=intval($count_neu);
$outmas['Sh8']['all'][1][]=intval($count_neg);
$outmas['Sh8']['all'][1][]=intval($count_pos);				
$temp_var1 = substr_replace(intval($outmas['Sh8']['all'][0][2]*1000)."%", ".", -2,0);
$temp_var2 = substr_replace(intval($outmas['Sh8']['all'][0][1]*1000)."%", ".", -2,0);
$outmas['Sh8']['top'][0][]=$outmas['Sh8']['all'][1][2]." (".$temp_var1.")";//positive
$outmas['Sh8']['top'][1][]=$outmas['Sh8']['all'][1][1]." (".$temp_var2.")";//negative
$outmas['Sh8']['top'][0][]='';
$outmas['Sh8']['top'][1][]='';
$outmas['Sh8']['top'][0][]= array_search(max($hosts_positive),$hosts_positive);
$outmas['Sh8']['top'][1][]= array_search(max($hosts_negative),$hosts_negative);
$outmas['Sh8']['top'][0][]=intval($hosts_positive[end($outmas['Sh8']['top'][0])]);
$outmas['Sh8']['top'][1][]=intval($hosts_negative[end($outmas['Sh8']['top'][1])]);
$outmas['Sh8']['top'][0][]='';
$outmas['Sh8']['top'][1][]='';		
$graph=$order['order_graph'];
//fclose($h);
$mmtime=json_decode($graph,true);
$mtime=$mmtime['all'];
//echo json_encode($count_top_res);
foreach ($mtime as $hn=>$gtime){
//if (in_array($hn,$av_host)||(($indother==1)&&(!in_array($hn,$all_host)))) {
//$timet[date('Y',$time)][date('n',$time)][date('j',$time)]++;
	foreach($gtime as $year=>$years) {
		foreach($years as $month=>$months){
			foreach($months as $day=>$days){
					$timet[$year][$month][$day]+=$days;
			}
		}
	}
}
if ($order['order_end']==0)
{
	$order['order_end']=$order['order_last'];
}
//echo strtotime($_POST['sd']).' '.strtotime($_POST['ed']);
if ((intval(strtotime($_POST['stime']))==0) && (intval(strtotime($_POST['etime']))==0))
{
	$_POST['stime']=date('j.n.Y',$order['order_start']);
	$_POST['etime']=date('j.n.Y',$order['order_end']);
}
//echo $_POST['stime'].' '.$_POST['etime'];
for($t=strtotime($_POST['stime']);$t<=strtotime($_POST['etime']);$t=mktime(0,0,0,date("n",$t),date("j",$t)+1,date("Y",$t)))
{
	//$outmas['Sh2']['graph'][mktime(0,0,0,date("n",$t),date("j",$t),date("Y",$t))]['count']=intval($timet[date('Y',$t)][date('n',$t)][date('j',$t)]);
	$outmas['Sh2']['graph'][mktime(0,0,0,date("n",$t),date("j",$t),date("Y",$t))]['count']=0;
	foreach ($count_top_res[mktime(0,0,0,date("n",$t),date("j",$t),date("Y",$t))] as $keyy => $itemm)
	{
		//echo $itemm.' ';
		$outmas['Sh2']['graph'][mktime(0,0,0,date("n",$t),date("j",$t),date("Y",$t))]['count']+=$itemm;
	}
	$zap='';
	$strtags='';
	//echo mktime(0,0,0,date("n",$t),date("j",$t),date("Y",$t)).'<br>';
	if (isset($outmas1[mktime(0,0,0,date("n",$t),date("j",$t),date("Y",$t))]))
	{
		foreach ($outmas1[mktime(0,0,0,date("n",$t),date("j",$t),date("Y",$t))] as $key => $item)
		{
			if ($key!='')
			{
				$strtags.=$zap.$key;
				$zap=',';
			}
		}
	}
	$outmas['Sh2']['graph'][mktime(0,0,0,date("n",$t),date("j",$t),date("Y",$t))]['tags']=$strtags;
	if (!isset($din_nastr[mktime(0,0,0,date("n",$t),date("j",$t),date("Y",$t))]))
	{
		//echo mktime(0,0,0,date("n",$t),date("j",$t),date("Y",$t)).' ';
		/*$outmas['Sh8']['dinams'][mktime(0,0,0,date("n",$t),date("j",$t),date("Y",$t))]['neu']=0;
		$outmas['Sh8']['dinams'][mktime(0,0,0,date("n",$t),date("j",$t),date("Y",$t))]['neg']=0;
		$outmas['Sh8']['dinams'][mktime(0,0,0,date("n",$t),date("j",$t),date("Y",$t))]['pos']=0;*/
		if ((intval(date('H',$t))>0)||(intval(date('i',$t))>0)) $stime_key_t=date("H:i:s d.m.Y",$t);
		else $stime_key_t=date("d.m.Y",$t);
		$outmas['Sh8']['dinams'][0][]=mktime(0,0,0,date("n",$t),date("j",$t),date("Y",$t));
		$outmas['Sh8']['dinams'][1][]=0;
		$outmas['Sh8']['dinams'][2][]=0;
		$outmas['Sh8']['dinams'][3][]=0;
	}
}
array_multisort($outmas['Sh8']['dinams'][0],SORT_ASC,$outmas['Sh8']['dinams'][1],SORT_ASC,$outmas['Sh8']['dinams'][2],SORT_ASC,$outmas['Sh8']['dinams'][3],SORT_ASC);
foreach ($outmas['Sh8']['dinams'][0] as $key => $item)
{
	if ((intval(date('H',$item))>0)||(intval(date('i',$item))>0)) $stime_key_t1=date("H:i:s d.m.Y",$item);
	else $stime_key_t1=date("d.m.Y",$item);
	$outmas['Sh8']['dinams'][0][$key]=$stime_key_t1;
}
$graph_2=$outmas['Sh2']['graph'];
unset($outmas['Sh2']['graph']);
$i=0;
foreach ($graph_2 as $key => $item)
{
	if ((intval(date('H',$key))>0)||(intval(date('i',$key))>0)) $stime_key=date("H:i:s d.m.Y",$key);
	else $stime_key=date("d.m.Y",$key);			
	$outmas['Sh2']['graph'][0][]=$stime_key;
	$outmas['Sh2']['graph'][1][]=$item['count'];
	$outmas['Sh2']['graph'][2][]=$item['tags'];	
	$i++;
}
//array_multisort($outmas['Sh3']['engage'],SORT_DESC,$outmas['content'],SORT_DESC,$outmas['foll'],SORT_DESC,$outmas['src'],SORT_DESC);
$outmas['Sh2']['engage']=intval($total_end);
arsort($mcount_res);
//print_r($mcount_res);
$i=0;
ksort($count_top_res);
$outmas['Sh5']['top_count'][0][]='Дата:';
//print_r($mcount_res);
foreach ($mcount_res as $key => $item)
{
	$outmas['Sh5']['count'][0][]=$key;
	$outmas['Sh5']['count'][1][]=$item;

	$p5_all_p+=$item;

	if ($i<5)
	{
		$j=0;
		$outmas['Sh5']['top_count'][$i+1][]=$key;
		foreach ($count_top_res as $kk => $ii)
		{
			if ((intval(date('H',$kk))>0)||(intval(date('i',$kk))>0)) $kk_time=date("H:i:s d.m.Y",$kk);
			else $kk_time=date("d.m.Y",$kk);
			
			if (!in_array($kk_time,$outmas['Sh5']['top_count'][0]))
			{
				$outmas['Sh5']['top_count'][0][]=$kk_time;
			}

			$outmas['Sh5']['top_count'][$i+1][]=intval($ii[$key]);

			$j++;
		}
	}
	$i++;
}		

foreach ($mcount_res as $key => $item)
{			
	if (count($outmas['Sh5']['proc'][0])<5)
	{		
		$outmas['Sh5']['proc'][0][]=$key;
		$outmas['Sh5']['proc'][1][]=$item;
		$outmas['Sh5']['proc'][2][]=$item/$p5_all_p;
		$outmas['Sh5']['proc'][3][]=$hosts_engeges[$key];
		$outmas['Sh5']['proc'][4][]=$hosts_engeges[$key]/$sum_host_engage;
	}
	else
	{				
		break;
		//$other_rs+=$item;
	}
}		
$outmas['Sh5']['proc'][0][]='другие';
$outmas['Sh5']['proc'][1][]=$p5_all_p - array_sum($outmas['Sh5']['proc'][1]);
$outmas['Sh5']['proc'][2][]=end($outmas['Sh5']['proc'][1])/$p5_all_p;
$outmas['Sh5']['proc'][3][]=$sum_host_engage - array_sum($outmas['Sh5']['proc'][3]);
$outmas['Sh5']['proc'][4][]=end($outmas['Sh5']['proc'][3])/$sum_host_engage;
for ($sind=0;$sind<count($outmas['Sh5']['proc'][4]);$sind++)
	$outmas['Sh5']['proc'][5][] = 0;
	// $outmas['Sh5']['proc'][5] = array(0,0,0,0,0,0);

foreach ($top_speakers as $key => $item)
{	
	$keyinf=explode(':',$key);
	$top_speak['comments_count'] = $item['comments_count'];
	$top_speak['likes_count'] = $item['likes_count'];
	$top_speak['retweet_count'] = $item['retweet_count'];
	$top_speak['nick'][]=$keyinf[0];
	$top_speak['neg'][]=$item['neg'];
	$top_speak['pos'][]=$item['pos'];

	$top_speak['loc'][]=$item['location'];
	$top_speak['gnd'][]=$item['gender'];
	$top_speak['age'][]=$item['age'];

	$top_speak['neu'][]=$item['neu'];
	$top_speak['foll'][]=$item['foll'];
	$top_speak['src'][]=$item['src'];
	$top_speak['count'][]=$item['count'];
	$top_speak['link'][]=$item['link'];
	$top_speak['login'][]=$item['login'];			
}
$max_pos_author_key = array_search(max($top_speak['pos']), $top_speak['pos']);
$max_neg_author_key = array_search(max($top_speak['neg']), $top_speak['neg']);
$outmas['Sh8']['top'][0][]=($top_speak['nick'][$max_pos_author_key]==null)? "" : $top_speak['nick'][$max_pos_author_key];
$outmas['Sh8']['top'][1][]=($top_speak['nick'][$max_neg_author_key]==null)? "" : $top_speak['nick'][$max_neg_author_key];
$outmas['Sh8']['top'][0][]=intval($top_speak['pos'][$max_pos_author_key]);
$outmas['Sh8']['top'][1][]=intval($top_speak['neg'][$max_neg_author_key]);

function sorted_authors($ar,$limit){
	foreach ($ar['count'] as $key => $item)
	{	
		$author_counter++;
		if($author_counter>$limit) break;
		$result[0][]=$key+1;
		$result[1][]=$ar['nick'][$key];
		if ($ar['src'][$key]=='livejournal.com')
		{
			if ($ar['login'][$key]!='')
			{
				$result[2][]='http://'.$ar['login'][$key].'.livejournal.com/';
			}
			else
			{
				$regex='/http\:\/\/(?<nick>.*?)\./isu';
				preg_match_all($regex,$ar['nick'][$key],$out);
				$result[2][]='http://'.$out['nick'][0].'.livejournal.com/';
			}
		}
		else
		if (($ar['src'][$key]=='vk.com') || ($ar['src'][$key]=='vkontakte.ru'))
		{
			$result[2][]='http://vk.com/'.($ar['login'][$key][0]=='-'?'club'.mb_substr($ar['login'][$key],1,mb_strlen($ar['login'][$key],'UTF-8'),'UTF-8'):'id'.$ar['login'][$key]);
		}
		else
		if ($ar['src'][$key]=='twitter.com')
		{
			$result[2][]='http://twitter.com/'.$ar['login'][$key];
		}
		else
		if ($ar['src'][$key]=='facebook.com')
		{
			$result[2][]='http://facebook.com/'.$ar['login'][$key];
		}
		else
		if ($ar['src'][$key]=='mail.ru')
		{
			$result[2][]=$ar['link'][$key];
		}
		else
		if ($ar['src'][$key]=='liveinternet.ru')
		{
			$result[2][]=$ar['link'][$key];
		}
		else
		if ($ar['src'][$key]=='ya.ru')
		{
			$result[2][]=$ar['link'][$key];
		}
		else
		if ($ar['src'][$key]=='yandex.ru')
		{
			$result[2][]=$ar['link'][$key];
		}
		else
		if ($ar['src'][$key]=='rutwit.ru')
		{
			$result[2][]=$ar['link'][$key];
		}
		else
		if ($ar['src'][$key]=='rutvit.ru')
		{
			$result[2][]=$ar['link'][$key];
		}
		else
		if ($ar['src'][$key]=='babyblog.ru')
		{
			$result[2][]=$ar['link'][$key];
		}
		else
		if ($ar['src'][$key]=='blog.ru')
		{
			$result[2][]=$ar['link'][$key];
		}
		else
		if ($ar['src'][$key]=='foursquare.com')
		{
			$result[2][]=$ar['link'][$key];
		}
		else
		if ($ar['src'][$key]=='kp.ru')
		{
			$result[2][]=$ar['link'][$key];
		}
		else
		if ($ar['src'][$key]=='aif.ru')
		{
			$result[2][]=$ar['link'][$key];
		}
		else
		if ($ar['src'][$key]=='friendfeed.com')
		{
			$result[2][]=$ar['link'][$key];
		}
		else
	    if ($ar['src'][$key]=='google.com')
	    {
	    	$result[2][]=$ar['link'][$key];
	    }
		$result[3][]=intval($ar['foll'][$key]);
		$result[4][]=$ar['count'][$key];
		$result[5][]=intval($ar['comments_count'][$key]);
		$result[6][]=intval($ar['likes_count'][$key]);
		$result[7][]=intval($ar['retweet_count'][$key]);
		$result[8][]=intval($ar['neg'][$key]);
		$result[9][]=intval($ar['pos'][$key]);
		$result[10][]=intval($ar['neu'][$key]);
		$result[11][]=$ar['src'][$key];//source
		$result[12][]=$ar['gnd'][$key];//gender
		$result[13][]=$ar['age'][$key];//age
		$result[14][]=$ar['loc'][$key];//region
		//$result[7][]=$ar['link'][$key];
	}
	if (count($result)==0)
	{
		$result[0][]='';
		$result[1][]='';
		$result[2][]='';
		$result[3][]='';
		$result[4][]='';
		$result[5][]='';
		$result[6][]='';
		$result[7][]='';
		$result[8][]='';
		$result[9][]='';
		$result[10][]='';
		$result[11][]='';
		$result[12][]='';
		$result[13][]='';
		$result[14][]='';
	}
	return $result;
}

$top_prom1=$top_speak;
$top_prom2=$top_speak;

array_multisort($top_speak['nick'],SORT_ASC,$top_speak['count'],SORT_DESC,$top_speak['pos'],SORT_DESC,$top_speak['neg'],SORT_DESC,$top_speak['neu'],SORT_DESC,$top_speak['foll'],SORT_DESC,$top_speak['src'],SORT_DESC,$top_speak['link'],SORT_DESC,$top_speak['login'],SORT_DESC,$top_speak['loc'],SORT_DESC,$top_speak['gnd'],SORT_DESC,$top_speak['age'],SORT_DESC);

$authors_sorted_by_name = sorted_authors($top_speak,$_POST['max_authors']);

array_multisort($top_prom1['foll'],SORT_DESC,$top_prom1['count'],SORT_DESC,$top_prom1['nick'],SORT_DESC,$top_prom1['pos'],SORT_DESC,$top_prom1['neg'],SORT_DESC,$top_prom1['neu'],SORT_DESC,$top_prom1['src'],SORT_DESC,$top_prom1['link'],SORT_DESC,$top_prom1['login'],SORT_DESC,$top_prom1['loc'],SORT_DESC,$top_prom1['gnd'],SORT_DESC,$top_prom1['age'],SORT_DESC);

$authors_sorted_by_audience = sorted_authors($top_prom1,20);

array_multisort($top_prom2['count'],SORT_DESC,$top_prom2['foll'],SORT_DESC,$top_prom2['nick'],SORT_DESC,$top_prom2['pos'],SORT_DESC,$top_prom2['neg'],SORT_DESC,$top_prom2['neu'],SORT_DESC,$top_prom2['src'],SORT_DESC,$top_prom2['link'],SORT_DESC,$top_prom2['login'],SORT_DESC,$top_prom2['loc'],SORT_DESC,$top_prom2['gnd'],SORT_DESC,$top_prom2['age'],SORT_DESC);

$authors_sorted_by_count = sorted_authors($top_prom2,20);

function sort_tonal_authors($ar){
	$i=0;
	foreach ($ar['foll'] as $key => $item)
	{
		if ($i<10)
		{				
			$kkk=$ar['time'][$key];
			if ((intval(date('H',$kkk))>0)||(intval(date('i',$kkk))>0)) 
				$kkk_time=date("H:i:s d.m.Y",$kkk);
			else $kkk_time=date("d.m.Y",$kkk);
				$ss_time=explode(' ',$kkk_time);
			$result[0][]=($ss_time[1]=='')?$ss_time[0]:$ss_time[1];
			$result[1][]=($ss_time[1]=='')?'':$ss_time[0];
			$result[2][]=strip_tags($ar['content'][$key]);
			$result[3][]=$ar['link'][$key];
			$result[4][]=$ar['foll'][$key];
			$result[5][]=$ar['eng'][$key];
			$result[6][]=$ar['likes'][$key];
			$result[7][]=$ar['comment'][$key];
			$result[8][]=$ar['retweet'][$key];
			$result[9][]=$ar['nick'][$key];
			$result[10][]=$ar['gen'][$key];
			$result[11][]=$ar['age'][$key];
			$result[12][]=(isset($ar['loc'][$key]))? $ar['loc'][$key] : "";
			$result[13][]=$ar['src'][$key];
			$result[14][]=$ar['type'][$key];
		}
		$i++;
	}
	if (count($result[0])==0)
	{
		$result[0][]='';
		$result[1][]='';
		$result[2][]='';
		$result[3][]='';
		$result[4][]='';
		$result[5][]='';
		$result[6][]='';
		$result[7][]='';
		$result[8][]='';
		$result[9][]='';
		$result[10][]='';
		$result[11][]='';
		$result[12][]='';
		$result[13][]='';
		$result[14][]='';
	}
	return $result;
}

array_multisort($top_pos['foll'],SORT_DESC,$top_pos['time'],SORT_DESC,$top_pos['src'],SORT_DESC,$top_pos['link'],SORT_DESC,$top_pos['type'],SORT_DESC,$top_pos['content'],SORT_DESC,$top_pos['eng'],SORT_DESC,$top_pos['nick'],SORT_DESC,$top_pos['gen'],SORT_DESC,$top_pos['age'],SORT_DESC,$top_pos['loc'],SORT_DESC,$top_pos['tags'],SORT_DESC,$top_pos['comment'],SORT_DESC,$top_pos['likes'],SORT_DESC,$top_pos['retweet'],SORT_DESC);
array_multisort($top_neg['foll'],SORT_DESC,$top_neg['time'],SORT_DESC,$top_neg['src'],SORT_DESC,$top_neg['link'],SORT_DESC,$top_neg['type'],SORT_DESC,$top_neg['content'],SORT_DESC,$top_neg['eng'],SORT_DESC,$top_neg['nick'],SORT_DESC,$top_neg['gen'],SORT_DESC,$top_neg['age'],SORT_DESC,$top_neg['loc'],SORT_DESC,$top_neg['tags'],SORT_DESC,$top_neg['comment'],SORT_DESC,$top_neg['likes'],SORT_DESC,$top_neg['retweet'],SORT_DESC);

$out_top_pos_post = sort_tonal_authors($top_pos);
$out_top_neg_post = sort_tonal_authors($top_neg);

$outmas['Sh8']['top_positive']=$out_top_pos_post;
$outmas['Sh8']['top_negative']=$out_top_neg_post;


foreach ($top_loc as $key => $item)
{
	$top_loc_tosort['loc'][]=$key;
	$top_loc_tosort['pos'][]=$item['pos'];
	$top_loc_tosort['neg'][]=$item['neg'];
	$top_loc_tosort['neu'][]=$item['neu'];
	$top_loc_tosort['count'][]=$item['count'];
}
array_multisort($top_loc_tosort['count'],SORT_DESC,$top_loc_tosort['pos'],SORT_DESC,$top_loc_tosort['neg'],SORT_DESC,$top_loc_tosort['neu'],SORT_DESC,$top_loc_tosort['loc'],SORT_DESC);
$iter=0;
foreach ($top_loc_tosort['count'] as $key => $item)
{	
	$outm_loc[0][]=$top_loc_tosort['loc'][$key];
	if (detect_country($top_loc_tosort['loc'][$key])) 
	{
		$outm_country_count++;
	}
	else
	{
		$outm_city_count++;
	}
	//export_fix_1 end
	$outm_loc[1][]=$item;
	$outm_loc[2][]=$top_loc_tosort['pos'][$key];
	$outm_loc[3][]=$top_loc_tosort['neg'][$key];
	$outm_loc[4][]=$top_loc_tosort['neu'][$key];
	if ($iter<5)
	{
		$outm_toploc[0][]=$top_loc_tosort['loc'][$key];
		$outm_toploc[1][]=$item;
	}
	else
	{
		$count_othr_l_oc++;
	}
	$iter++;
}
$outmas['Sh2']['city_count'] = intval($outm_city_count);
$outmas['Sh2']['country_count'] = intval($outm_country_count);
$outm_toploc[0][]='Другие';
$outm_toploc[1][]=intval($count_othr_l_oc);
$outm_loc[0][]='Неопределено';
$outm_loc[1][]=$cnt - array_sum($outm_loc[1]);
$outm_loc[2][]=$count_pos - array_sum($outm_loc[2]);
$outm_loc[3][]=$count_neg - array_sum($outm_loc[3]);
$outm_loc[4][]=$count_neu - array_sum($outm_loc[4]);

$outmas['Sh6']=($_POST['type']!='analytics'?$authors_sorted_by_name:array());
$outmas['Sh7']['audience']=$authors_sorted_by_audience;
$outmas['Sh7']['count']=$authors_sorted_by_count;
$outmas['Sh2']['uniq_auth']=count($authors_sorted_by_name[0]);
$outmas['Sh9']['loc']=$outm_loc;
$outmas['Sh9']['top_loc']=$outm_toploc;	
ksort($out_age);	
foreach ($out_age as $key => $item)
{
	//$out_age1[0][]=$key;			
	if(isset($item))
	{
		$out_age1[0][]=intval($item['count']);
		$out_age1[1][]=intval($item['pos']);
		$out_age1[2][]=intval($item['neg']);
		$out_age1[3][]=intval($item['neu']);
	}else{
		$out_age1[0][]=0;
		$out_age1[1][]=0;
		$out_age1[2][]=0;
		$out_age1[3][]=0;
	}
}		
$out_age1[0][]=intval($cnt)-array_sum($out_age1[0]);
$out_age1[1][]=intval($count_pos)-array_sum($out_age1[1]);
$out_age1[2][]=intval($count_neg)-array_sum($out_age1[2]);
$out_age1[3][]=intval($count_neu)-array_sum($out_age1[3]);

$out_gen1[0][]=$out_gen['М']['count'];
$out_gen1[0][]=$out_gen['Ж']['count'];
$out_gen1[0][]=intval($cnt)-array_sum($out_gen1[0]);
$out_gen1[1][]=$out_gen1[0][0]/intval($cnt);
$out_gen1[1][]=$out_gen1[0][1]/intval($cnt);
$out_gen1[1][]=$out_gen1[0][2]/intval($cnt);
if (count($out_gen)==0)
{			
	$out_gen1[0]=array(0,0,0);
	$out_gen1[1]=array(0.0,0.0,0.0);
}
/*$redis=new Redis() or die("Can'f load redis module.");
$redis->connect('127.0.0.1');
for($t=strtotime($_POST['stime']);$t<=strtotime($_POST['etime']);$t=mktime(0,0,0,date("n",$t),date("j",$t)+1,date("Y",$t)))
{
	$var=$redis->get('order_'.$order['order_id'].'_'.$t);
	$m_dinams=json_decode($var,true);
	//print_r($m_dinams);
	//die();
	foreach ($m_dinams['sp_pr'] as $key => $item)
	{
		//echo $key.'<br>';
		foreach ($item as $k => $i)
		{
			if ($i['nick']=='') continue;
			if (!isset($yet_prms[$key]))
			{
				$count_value_per+=$i['readers'];
			}
			$yet_prms[$key]=1;
		}
	}
}
$outmas['Sh2']['audience']=intval($count_value_per);
$outmas['Sh2']['uniq_auth']=count($yet_prms);*/
$outmas['Sh10']['age']=$out_age1;
$outmas['Sh10']['gen']=$out_gen1;
if($total_tag_count>0){
	$outmas['Sh11']['tag_distr'][0][]='Дата';
	for ($i=1; $i < $total_tag_count+1; $i++) { 
		$outmas['Sh11']['tag_distr'][$i][]='Тег'.$i;
	}
	$outmas['Sh11']['tag_distr'][count($outmas['Sh11']['tag_distr'])][]='Неопределено';
}
ksort($ageTime);
ksort($tagTime);
$intervals = array('do14','15-18','19-25','26-30','31-35','36-45','46-55','af56');
foreach ($graph_2 as $key => $value) {
	$iterdate=date('d.m.Y',$key);
	$sum_of_ages=0;
	$sum_of_tags=0;
	$outmas['Sh10']['ages'][0][]=$iterdate;
	$ati=1;			
	foreach ($intervals as $item) {
		// for ageTime
		if(isset($ageTime[$key][$item]['count'])){
			$outmas['Sh10']['ages'][$ati][]=$ageTime[$key][$item]['count'];
			$sum_of_ages+=$ageTime[$key][$item]['count'];
		}else{
			$outmas['Sh10']['ages'][$ati][]=0;
		}
		$ati++;				
	}			
	//$totalAges+=$sum_of_ages;		
	$outmas['Sh10']['ages'][9][] = $value['count']-$sum_of_ages;
	//for tagTime
	if($total_tag_count>0){
		$outmas['Sh11']['tag_distr'][0][]=$iterdate;
		$tagcounter=1;
		foreach ($tag_list as $itertag) {
			if(isset($tagTime[$key][$itertag])){
				$outmas['Sh11']['tag_distr'][$tagcounter][]=$tagTime[$key][$itertag];
				$sum_of_tags+=$tagTime[$key][$itertag];
			}else{
				$outmas['Sh11']['tag_distr'][$tagcounter][]=0;
			}
			$tagcounter++;
		}
		$outmas['Sh11']['tag_distr'][$tagcounter][]=intval($tagTime[$key]["undf"]);
	}
}
if($total_tag_count>0){
	$tc1=1;
	foreach ($tag_list as $itertag) {			
		$outmas['Sh11']['tag_counts'][0][] = 'Тег'.$tc1;
		$tc1++;
		$outmas['Sh11']['tag_counts'][1][] = intval($tagStat[$itertag]['count']);
		$outmas['Sh11']['tag_counts'][2][] = intval($tagStat[$itertag]['count'])/intval($cnt);
		$outmas['Sh11']['tag_counts'][3][] = intval($tagStat[$itertag]['pos']);
		$outmas['Sh11']['tag_counts'][4][] = intval($tagStat[$itertag]['neg']);
		$outmas['Sh11']['tag_counts'][5][] = intval($tagStat[$itertag]['neu']);
	}	
	$outmas['Sh11']['tag_counts'][0][] = 'Неопределено';
	$outmas['Sh11']['tag_counts'][1][] = intval($tagStat["undf"]['count']);
	$outmas['Sh11']['tag_counts'][2][] = intval($tagStat["undf"]['count'])/intval($cnt);
	$outmas['Sh11']['tag_counts'][3][] = intval($tagStat["undf"]['pos']);
	$outmas['Sh11']['tag_counts'][4][] = intval($tagStat["undf"]['neg']);
	$outmas['Sh11']['tag_counts'][5][] = intval($tagStat["undf"]['neu']);
}

$outmas['Sh1']['hash']=$hash;
//echo json_encode($outmas);
// echo "Finished!\n";
//die();
//--------------------
		
$result_json['status'] = "NOT_OK";
while($result!='<class \'web.webapi.OK\'>'){
	$upload_counter++;
	// $my_url = 'http://188.120.239.225/upload';//'http://54.228.247.2/'.$hash;
	$my_url = 'http://188.120.239.225:8080/upload';
	echo $my_url."\n";
	$handle = curl_init($my_url);
	//curl_setopt($handle, CURLOPT_URL, $my_url);

	curl_setopt($handle, CURLOPT_RETURNTRANSFER, 1);   // возвращает веб-страницу		
	curl_setopt($handle, CURLOPT_HEADER, 0);           // не возвращает заголовки
	curl_setopt($handle, CURLOPT_FOLLOWLOCATION, 1);   // переходит по редиректам
	curl_setopt($handle, CURLOPT_ENCODING, "");        // обрабатывает все кодировки		
	curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 300); // таймаут соединения
	curl_setopt($handle, CURLOPT_TIMEOUT, 120);        // таймаут ответа
	curl_setopt($handle, CURLOPT_MAXREDIRS, 10); 
	curl_setopt($handle, CURLOPT_POST, 1);
	curl_setopt($handle, CURLOPT_PORT, 8080);
	curl_setopt($handle, CURLOPT_REFERER, 'http://production.wobot.com/');
	curl_setopt($handle, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)');
	$outmas_text = json_encode($outmas);
	$gzdata = gzencode($outmas_text, 9);		
	curl_setopt($handle, CURLOPT_POSTFIELDS, $gzdata);		
	curl_setopt($handle, CURLOPT_HTTPHEADER, array('Content-type: application/x-gzip'));
	curl_setopt($handle, CURLOPT_ENCODING, 'gzip');		
	$result = curl_exec($handle);
	echo $result;
	$result_json = json_decode($result,true);
	echo $result_json."\n";
	curl_close($handle);
	if($upload_counter>5){
		$db->query("INSERT INTO blog_export (order_id,export_time,start_time,end_time,hash_code,progress) VALUES(".$_POST['order_id'].",".time().",".strtotime($_POST['start']).",".strtotime($_POST['end']).",\"".$hash."\",0)");
		die();
	}
}
$cmd = 'php progress_scanner.php '.$hash;
shell_exec($cmd.' > /dev/null 2>/dev/null &');
?>