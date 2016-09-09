<?php
ini_set('memory_limit', '2048M');
date_default_timezone_set ( 'Europe/Moscow' );
// $_POST['order_id']=3361;
// $_POST['start']='1.1.2012';
// $_POST['end']='1.1.2014';
$order_id = $_POST['order_id'];
error_reporting(0);

require_once('/var/www/new/com/config.php');
require_once('/var/www/new/com/db.php');
require_once('auth.php');
require_once('/var/www/new/com/func.php');
require_once('/var/www/new/bot/kernel.php');
require_once('func_export.php');
require_once('/var/www/com/loc.php');

// $user['tariff_id']=15;
// $user['user_id']=1187;

auth();
if (!$loged){
	echo '{"error":"authentication_error"}';
	die(); // if not loged in , die
}
if (!isset($_POST['order_id'])){
	echo '{"error":"missing the order_id parameter"}';
	die(); // if not loged in , die
}
$db = new database();
$db->connect();
$res=$db->fetch($db->query("SELECT * from blog_orders WHERE order_id=".intval($order_id)." and user_id=".intval($user['user_id'])." LIMIT 1"));
if(!$res){
	echo '{"error":"this order_id doesn\'t belong to this user"}';
	die();
} // if order_id not belongs to current user

$file_name=preg_replace('/[^а-яА-Яa-zA-Z\-\_0-9]/isu','_',($res['order_name']!=''?$res['order_name']:$res['order_keyword']));
$file_name=preg_replace('/\_+/isu','_',$file_name);
if (mb_strlen($file_name,'UTF-8')>100)
{
	$file_name=mb_substr($file_name,0,100,'UTF-8');
}

header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;");
header("Content-Disposition: attachment;filename=\"wobot_".
date('dmy',strtotime($_POST['start'])).'_'.date('dmy',strtotime($_POST['end'])).'_'.$file_name.".csv\"");
header("Cache-Control: max-age=0");

$res_micr=array('mail.ru','twitter.com','mblogi.qip.ru','rutvit.ru','friendfeed.com','godudu.com','juick.com','jujuju.ru','sports.ru','mylife.ru','chikchirik.ru','chch.ru','f5.ru','zizl.ru','smsnik.com');
$soc_netw=array('1c-club.com','cjclub.ru','diary.ru','facebook.com','myspace.com','orkut.com','vkontakte.ru','stranamam.ru','dietadiary.com','ya.ru','vk.com');
$nov_res=array('km.ru','regnum.ru','akm.ru','arms-tass.su','annews.ru','itar-tass.com','interfax.ru','interfax-russia.ru','oreanda.ru','1prime.ru','rbc.ru','rbc.ru','ria.ru','rosbalt.ru','tasstelecom.ru','finmarket.ru','expert.ru','newtimes.ru','akzia.ru','aif.ru','argumenti.ru','bg.ru','vedomosti.ru','izvestia.ru','itogi.ru','kommersant.ru','kommersant.ru','kp.ru','mospravda.ru','mn.ru','mk.ru','ng.ru','novayagazeta.ru','newizv.ru','kommersant.ru','politjournal.ru','profile.ru','rbcdaily.ru','gosrf.ru','rodgaz.ru','rg.ru','russianews.ru','senat.org','sobesednik.ru','tribuna.ru','trud.ru','newstube.ru','vesti.ru','mir24.tv','ntv.ru','1tv.ru','rutv.ru','tvkultura.ru','tvc.ru','tvzvezda.ru','5-tv.ru','ren-tv.com','radiovesti.ru','govoritmoskva.ru','ruvr.ru','kommersant.ru','cultradio.ru','radiomayak.ru','radiorus.ru','rusnovosti.ru','msk.ru','infox.ru','lenta.ru','lentacom.ru','newsru.com','temadnya.ru','newsinfo.ru','rb.ru','utronews.ru','moscow-post.ru','apn.ru','argumenti.ru','wek.ru','vz.ru','gazeta.ru','grani.ru','dni.ru','evrazia.org','ej.ru','izbrannoe.ru','inopressa.ru','inosmi.ru','inforos.ru','kommersant.ru','kreml.org','polit.ru','pravda.ru','rabkor.ru','russ.ru','smi.ru','svpressa.ru','segodnia.ru','stoletie.ru','strana.ru','utro.ru','fedpress.ru','lifenews.ru','belrus.ru','pfrf.ru','rosculture.ru','kremlin.ru','gov.ru','rosnedra.com');
$exc_src=array('diary.ru','foursquare.com','ya.ru','yandex.ru','twitpic.com','mail.ru','kp.ru','liveinternet.ru','ya.ru');

$query1='SELECT * FROM blog_tag WHERE order_id='.intval($_POST['order_id']);
$respost1=$db->query($query1);
while($tgl1 = $db->fetch($respost1))
{
	$mtag[$tgl1['tag_tag']]=$tgl1['tag_name'];
}
$total_tag_count = count($mtag);
$tag_list = array_keys($mtag);

$qqq=get_query();

$qpost=$db->query($qqq);

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
}

$counter = 0;

foreach ($outcash['link'] as $key => $llink)
{			
	$counter++;
	$link=urldecode($llink); //+

	$time=$outcash['time'][$key]; //+
	$stime=date("H:i:s d.m.Y",$time); //+
	$ss_time=explode(' ',$stime); //+

	$content=html_entity_decode($outcash['content'][$key],ENT_QUOTES,'UTF-8'); //+
	$parts=explode("\n",$content); //+
	$content=(($parts[0]!='')?$parts[0]:$parts[1]); //+
	$fcontent=preg_replace('/(\d)\)\./isu','$1.',$outcash['fcontent'][$key]); //+
	$comm=intval($outcash['comm'][$key]); // + 

	$isfav=$outcash['isfav'][$key]; // + 
	$tag=$outcash['tag'][$key]; //+
	$rtag=explode(',',$tag); //+
	$eng=$outcash['eng'][$key]; //+
	$loc=$outcash['loc'][$key]; //+
	$gender=$outcash['gender'][$key];//+
	if ($gender==0)//+
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
	$age=$outcash['age'][$key]; //+
	$age = (is_null($age) || $age==0) ? 0 : intval($age); //+

	if($order['order_engage']=='1') // +
	{
		$eng=$outcash['eng'][$key]."\t";
	}
	else
	{
		$eng='';
	}

	if ($isfav==1) $isfav='+'; //+
	else $isfav='-'; //+
	$nastr=$outcash['nastr'][$key]; // + 

	$isspam=$outcash['isspam'][$key]; //+
	if ($isspam==1) $isspam='+'; //+
	else $isspam='-'; //+

	$nick=$outcash['nick'][$key];
	$login=$outcash['login'][$key];
    $hn=parse_url($link); //+
    $hn=$hn['host']; // +
    $ahn=explode('.',$hn); // +
    $hn = $ahn[count($ahn)-2].'.'.$ahn[count($ahn)-1]; // +
	$hh = $ahn[count($ahn)-2]; // +

	if ($hn=='.') //+
	{
		$hn=$outcash['host'][$key];
	}
	
	if ($hn=='livejournal.com') //+
	{
		$rgx='/\/\/(?<nk>.*?)\./is';
		preg_match_all($rgx,$link,$out);
		$nick=$out['nk'][0];
		$login=$nick;
	}
	if (($hn=='twitter.com') || ($hn=='livejournal.com')) //+
	{
		$nick=$login;
	}

	if (in_array($hn,$soc_netw))//+
	{
		$type_re='социальная сеть';
	}
	else
	if (in_array($hn,$nov_res)) //+
	{
		$type_re='новостной ресурс';
	}
	else
	if (in_array($hn,$res_micr)) //+
	{
		$type_re='микроблог';
	}
	else
	{
		$type_re='форум или блог';
	}

	if ($nastr==0) $nstr=''; // +
	elseif ($nastr==1) $nstr='+'; // +
	elseif ($nastr==-1) $nstr='-'; // +

	$content=strip_tags($content); //+
	$content_r=$content; //+
	$content_f=''; //+
	$content_r=preg_replace('[^A-Za-z0-9_]', '', $content_r); //+
	if (($order['ful_com']=='1') && (!in_array($hn,$exc_src))) //+
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
	if($counter==1){
		$outmas = array("Дата","Время","Сайт","Контент","Ссылка","Лайки","Коментарии","Ретвиты","Охват","Вовлеченность","Тональность","Автор","Пол","Возраст","Регион","Тип","Удалено","Избранное");
		if($total_tag_count>0){
			for ($i=0; $i < $total_tag_count; $i++) { 					
				$outmas[] = "Тег ".($i+1);
			}
		}		
		foreach ($outmas as $key => $value) {
			$outmas[$key]= iconv("UTF-8", "CP1251//IGNORE",$outmas[$key]);
		}
	}else{
		$outmas = array();
		$outmas[]=($ss_time[1]=='')?$ss_time[0]:$ss_time[1]; // date
		$outmas[]=($ss_time[1]=='')?'':$ss_time[0]; // time	
		// $outmas[]=iconv("UTF-8", "UTF-8//IGNORE", $hn); // hostname
		$outmas[]=iconv("UTF-8", "CP1251//IGNORE", $hn); // hostname
		// $outmas[]=mb_substr(iconv("UTF-8", "UTF-8//IGNORE", (($hn!='twitter.com')?preg_replace('/\s+/isu',' ',preg_replace('/[^а-яА-Яa-zA-ZёЁ\.\,\'\"0-9\!\?\s\/\@\:\(\)\“\_\-\&\;\#\<\>]/isu',' ',(($content_f=='')?$content:$content_f))):' '.preg_replace('/[^а-яА-Яa-zA-ZёЁ\.\,\'\"0-9\!\?\s\/\@\:\(\)\“\_\-\&\;\#\<\>]/isu',' ',$content))), 0, 32000,'UTF-8'); // content
		$outmas[]=mb_substr(iconv("UTF-8", "CP1251//IGNORE", (($hn!='twitter.com')?preg_replace('/\s+/isu',' ',preg_replace('/[^а-яА-Яa-zA-ZёЁ\.\,\'\"0-9\!\?\s\/\@\:\(\)\“\_\-\&\;\#\<\>]/isu',' ',(($content_f=='')?$content:$content_f))):' '.preg_replace('/[^а-яА-Яa-zA-ZёЁ\.\,\'\"0-9\!\?\s\/\@\:\(\)\“\_\-\&\;\#\<\>]/isu',' ',$content))), 0, 32000,'CP1251'); // content
		// $outmas[]=iconv("UTF-8", "UTF-8//IGNORE", $link); // link
		$outmas[]=iconv("UTF-8", "CP1251//IGNORE", $link); // link
		$outmas[]=$outcash['likes'][$key];
		$outmas[]=$outcash['comment'][$key];
		$outmas[]=$outcash['retweet'][$key];
		$outmas[]=$comm; // ohvat
		$outmas[]=intval($eng); // engagement	
		$outmas[]=$nstr; // tonalnost +\-\empty
		// $outmas[]=iconv("UTF-8", "UTF-8//IGNORE", $nick); // Author
		$outmas[]=iconv("UTF-8", "CP1251//IGNORE", $nick); // Author
		$outmas[]=$gender; // gender
		$outmas[]=$age;// age
		// $outmas[]=(!$wobot['destn1'][$loc]) ? '' : $wobot['destn1'][$loc]; // region
		$outmas[]=iconv("UTF-8", "CP1251//IGNORE", (!$wobot['destn1'][$loc]) ? '' : $wobot['destn1'][$loc]); // region
		$outmas[]=iconv("UTF-8", "CP1251//IGNORE",$type_re); // type
		$outmas[]=$isspam; // removed
		$outmas[]=$isfav; // izbrannoe
		if($total_tag_count>0){
			for ($i=0; $i < $total_tag_count; $i++) { 					
				$outmas[]= iconv("UTF-8", "CP1251//IGNORE",(in_array($tag_list[$i],$rtag)) ? $mtag[$tag_list[$i]] : ''); //tags					
			}
		}
	}
	$csvout[]=$outmas;	
}

function outputCSV($data) {
  $outstream = fopen("php://output", 'w');
  function __outputCSV(&$vals, $key, $filehandler) {
    fputcsv($filehandler, $vals, ';', '"');
  }
  array_walk($data, '__outputCSV', $outstream);
  fclose($outstream);
}

// foreach ($csvout as $rid => $row){
// 	foreach ($row as $cid => $cell){
// 		$csvout[$rid][$cid] = iconv("UTF-8//IGNORE","CP1251",$csvout[$rid][$cid]);
// 	}
// }

outputCSV($csvout);
?>