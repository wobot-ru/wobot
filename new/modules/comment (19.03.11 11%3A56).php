<?php
/*

Wobot inc. 2010

Отображение постов
Разработчики: Рыбаков Владимир, Юдин Роман
Запускается: при нажатии на кнопку Показать мастера отчетов

*/
require_once('com/config.php');
require_once('com/func.php');
require_once('com/db.php');
require_once('bot/kernel.php');
require_once('com/auth.php');

ignore_user_abort(true);
set_time_limit(0);
ini_set('max_execution_time',0);
ob_implicit_flush();

//print_r($_POST);

$db = new database();
$db->connect();

auth();
if (!$loged) die();

//loading metrics
if (intval($_POST['order_id'])==0) die();
$fn = "/var/www/data/blog/".$_POST['order_id'].".metrics";
$h = fopen($fn, "r");
$data = fread($h, filesize($fn));
$metrics=json_decode($data,true);
fclose($h);

//head text with description of report master form
if ($_POST['showmode']=="notspam")
{
	$mode="без спама";
}
elseif ($_POST['showmode']=="showfav")
{
	$mode="избранные";
}
elseif ($_POST['showmode']=="onlyspam")
{
	$mode="только спам";
}
elseif ($_POST['showmode']=="showall")
{
	$mode="все";
}

if ($_POST['unrep']=='on')
{
	$mode1="без дублей";
}
else
{
	$mode1="с дублями";
}

if ($_POST['markt']=="true")
{
	$type[]="социальные сети";
}
if ($_POST['markt1']=="true")
{
	$type[]="новостные ресурсы";
}
if ($_POST['markt2']=="true")
{
	$type[]="микроблоги";
}
if ($_POST['markt3']=="true")
{
	$type[]="форумы и блоги";
}

$type_text="";
foreach ($type as $ind => $items)
{
	if (count($type)!=1)
	{
		if ($ind!=(count($type)-1))
		{
			$type_text.=$items.", ";	
		}
		else
		{
			$type_text.=$items;
		}
	}	
	else
	{
		$type_text=$items;
	}
	
}

start_tpl('','<link href=\'/css/list_lk.css\' rel=\'stylesheet\' type=\'text/css\' />');
$html_out .='
     <div id=\'top\'>
	  <div class=\'top_line\'>
	    <span class=\'date\'>
	      Дата: с '.date('d.m.Y',strtotime($_POST['ntime'])).' по '.date('d.m.Y',strtotime($_POST['etime'])).'
	    </span>
	    <ul class=\'controls\'>
	      <li class=\'no_bullet\'>
	        <a href=\'#\'>'.$mode.'</a>
	      </li>
	      <li class=\'no_bullet\'>
	        '.$mode1.'
	      </li>
	      <li class=\'no_bullet\'>
	        '.$type_text.'
	      </li>
	    </ul>
	  </div>
	  <div class=\'bot_line\'>
	    <span>
	      Найдены <div style="display: inline;" id="colup"></div> на <div style="display: inline;" id="colup1"></div>.
	    </span>
	  </div>
	</div>
	<div id=\'table\'>
';

$time_start = microtime(true);
$gn_time = 0;

unset($resorrr);
foreach ($_POST as $inddd => $posttt)
{
	if ((substr($inddd, 0, 4)=='res_'))
	{
		if ($posttt=='true')
		{
			//str_replace("_",".",$inddd);
			$resorrr[]=str_replace("_",".",substr($inddd,4));
		}
	}
}

if ($_POST['positive']==''&&$_POST['negative']==''&&$_POST['neutral']=='')
{
	$_POST['positive']='true';
	$_POST['negative']='true';
	$_POST['neutral']='true';
}

$mastexttype=array('','Не важно','Средне','Важно','Очено важно');

if (intval($_POST['order_id'])!=0)
{
			$res=$db->query("SELECT * from blog_orders WHERE order_id=".intval($_POST['order_id'])." and user_id=".intval($user['user_id'])." LIMIT 1"); // проверка order_id

		if (mysql_num_rows($res)==0) die();
			$order = $db->fetch($res);

$fn = "/var/www/data/blog/".intval($order_id).".src";
$h = fopen($fn, "r");
$data = fread($h, filesize($fn));
$sources=json_decode($data, true);
$kkey=0;
if ($_POST['res_other']==true)
{
	foreach ($sources as $nname => $inddex)
	{
		$kkey++;
		if ($kkey>10)
		{
			$resorrr[]=$nname;
		}
	}
}
$colres=count($resorrr);
//echo $colres;
//print_r($resorrr);
/*echo "<br>!";
print_r($othersources);*/
			/*//---------------------------------SPAM ADD------------------------------------
			if ($_COOKIE['spam']!='')
			{
				$resspam1=$db->query("SELECT * from blog_spam WHERE order_id='".intval($_POST['order_id'])."' AND post_link='".$_COOKIE['spam']."' LIMIT 1");
				$orderspam1 = $db->fetch($resspam1);
				if (intval($orderspam1['spam_id'])==0)
				{
				$resspam2=$db->query("INSERT INTO  `blog_spam` (`spam_link` ,`order_id`) VALUES ('".$_COOKIE['spam']."' , '".$_POST['order_id']."');");
				}
				$_COOKIE['spam']='';
			}
			//-----------------------------------------------------------------------------


			//---------------------------------FAV ADD------------------------------------
			if ($_COOKIE['fav']!='')
			{
				$resfav1=$db->query("SELECT * from blog_fav WHERE order_id='".intval($_POST['order_id'])."' AND post_link='".$_COOKIE['fav']."' LIMIT 1");
				$orderfav1 = $db->fetch($resfav1);
				if (intval($orderfav1['fav_id'])==0)
				{
				$resfav2=$db->query("INSERT INTO  `blog_fav` (`post_link` ,`order_id`) VALUES ('".$_COOKIE['fav']."' , '".$_POST['order_id']."');");
				}
				else
				{
				$resfav2=$db->query("DELETE FROM  `blog_fav` WHERE post_link='".$_COOKIE['fav']."' and order_id='".$_POST['order_id']."'");
				}
				$_COOKIE['fav']='';
			}
			//-----------------------------------------------------------------------------		
			*/
			
			/*unset($orderspams);
			$ressp=$db->query("SELECT * from blog_spam WHERE order_id='".intval($_POST['order_id'])."'");
			//echo "SELECT * from blog_spam WHERE ord_id=".intval($_GET['order_id'])." AND spam_link=".$link." LIMIT 1";
			while($ordersp = $db->fetch($ressp))
			{
				//array_push($orderspams,$ordersp['post_link']);	
				$orderspams[]=urldecode($ordersp['post_link']);
			}
			
			unset($orderfavs);
			$resfav=$db->query("SELECT * from blog_fav WHERE order_id='".intval($_POST['order_id'])."'");
			//echo "SELECT * from blog_spam WHERE ord_id=".intval($_GET['order_id'])." AND spam_link=".$link." LIMIT 1";
			while($orderfav = $db->fetch($resfav))
			{
				//array_push($orderfavs,$orderfav['post_link']);
				//$fpizdec = fopen('debug.txt', 'a');
				//fwrite($fpizdec, $orderfav['post_link']);
				$orderfavs[]=urldecode($orderfav['post_link']);
				//$fpizdec = fopen('debug.txt', 'a');
				//fwrite($fpizdec, urldecode($orderfav['post_link']));
			}

			unset($ordernastrs);
			unset($ordernastrs2);
			$resnastr=$db->query("SELECT * from blog_nastr WHERE order_id='".intval($_POST['order_id'])."'");
			//echo "SELECT * from blog_spam WHERE ord_id=".intval($_GET['order_id'])." AND spam_link=".$link." LIMIT 1";
			while($ordernastr = $db->fetch($resnastr))
			{
				//array_push($orderfavs,$orderfav['post_link']);
				//$fpizdec = fopen('debug.txt', 'a');
				//fwrite($fpizdec, $orderfav['post_link']);
				$ordernastrs[]=array($ordernastr['post_nastr'],$ordernastr['post_link']);
				//$ordernastrs[]=$ordernastr['post_nastr'];
				//$fpizdec = fopen('debug.txt', 'a');
				//fwrite($fpizdec, urldecode($orderfav['post_link']));
			}*/
			//print_r($ordernastrs);
//echo 'order_id here';
			/*$res1=$db->query("SELECT * from blog_fav WHERE order_id=".intval($_GET['order_id']));
			unset($fav);
			while($favv = $db->fetch($res))
			{
				array_push($fav,$favv['post_link']);
			}*/
$colcom=0;
$colres=0;
$html_out .= '
	<form action="/user/fav" method="post" id="favform">
	<input name="order_id" type="hidden" value="'.$order['order_id'].'">
	<input name="fav_nick" id="fav_nick" type="hidden">
	<input name="fav_content" id="fav_content" type="hidden">
	<input name="fav_time" id="fav_time" type="hidden">
	<input name="fav_link" id="fav_link" type="hidden">
	<input name="fav_img" id="fav_img" type="hidden">
	<input name="fav_hn" id="fav_hn" type="hidden">
	<input name="fav_nastr" id="fav_nastr" type="hidden">
	<input name="fav_i" id="fav_i" type="hidden">
	</form>
        <script type="text/javascript">

		var members = [
';
/*
$fn = "/var/www/data/blog/".intval($_POST['order_id']).".xml";
//$fn = "4.xml";
$h = fopen($fn, "r");
$data = fread($h, filesize($fn));
fclose($h);
$data='<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type">
'.$data.'
</head>
</html>
';
$regexcash="/<link>(?<link>.*?)<\/link>.*?<time>(?<time>.*?)<\/time>.*?<content>(?<content>.*?)<\/content>.*?<nick>(?<nick>.*?)<\/nick>.*?<loc>(?<loc>.*?)<\/loc>/is";
preg_match_all($regexcash,$data,$outcash);*/

///$respost=$db->query("SELECT * from blog_post WHERE order_id='".intval($_POST['order_id'])."' ORDER BY post_time DESC");
//echo "SELECT * from blog_spam WHERE ord_id=".intval($_GET['order_id'])." AND spam_link=".$link." LIMIT 1";
$where=get_isshow2();
$query='SELECT * from blog_post as p LEFT JOIN robot_blogs2 AS b ON p.blog_id=b.blog_id WHERE p.order_id="'.intval($_POST['order_id']).'"'.((strlen($where)==0)?'':' AND ').$where.' ORDER BY p.post_time DESC';
//echo '<br><br>'.$query.'<br><br>';
//die();
$respost=$db->query($query);
$ii=0;
while($pst = $db->fetch($respost))
{
	$outcash['link'][$ii]=str_replace("\n","",$pst['post_link']);
	$outcash['time'][$ii]=$pst['post_time'];
	$outcash['content'][$ii]=$pst['post_content'];
	$outcash['isfav'][$ii]=$pst['post_fav'];
	$outcash['nastr'][$ii]=$pst['post_nastr'];
	$outcash['isspam'][$ii]=$pst['post_spam'];
	$outcash['nick'][$ii]=$pst['blog_nick'];
	$outcash['type'][$ii]=$pst['post_type'];
	$ii++;
}

$pn=intval($_GET['p']);
        //$dom = new DomDocument;
        //$res = @$dom->loadHTML($data);
//$dom->encoding='utf-8';
//$dom->schemaValidateSource='';
//$posts = $dom->getElementsByTagName("post");

//$xml = simplexml_load_file("data/blog/135.xml");
   // print_r($xml);
/*if ($_GET['time']!=0) {
list($_GET['time'],$tmp)=explode(',',$_GET['time'],2);
$_GET['time']/=1000;
$_GET['time']=mktime(0,0,0,date('n',$_GET['time']),date('j',$_GET['time']),date('Y',$_GET['time']));
//echo '<script>alert("'.date('n.j.Y',$_GET['time']).'");</script>';
}*/

//$user['user_pagecount']=50;

$i=0;
//echo '(($i>=50*'.$pn.'))&&($i<50*('.$pn.'+1))<br>';
//Сортировка сообщений
//array_multisort($outcash['time'],SORT_DESC,$outcash['link'],SORT_DESC,$outcash['content'],SORT_DESC,$outcash['nick'],SORT_DESC,$outcash['loc'],SORT_DESC);
unset($mas_rep);
unset($mas_spam);
unset($mas_rep_link);
$wkey=0;
foreach ($outcash['link'] as $key => $llink)
//foreach ($posts as $post)
{
	$link=urldecode($llink);
	$time=$outcash['time'][$key];
	$content=$outcash['content'][$key];
	$gn_time_start = microtime(true);
	$isfav=$outcash['isfav'][$key];
	$nastr=$outcash['nastr'][$key];
	/*if ($isfav==1) $isfav='+';
	else $isfav='-';
	$nastr=$outcash['nastr'][$key];
	if ($nastr==1) $nastr='+';
	else $nastr='-';*/
	$isspam=$outcash['isspam'][$key];
	$pis=$outcash['type'][$key];
	/*if ($isspam==1) $isspam='+';
	else $isspam='-';*/
	//$nastr=getnastr($content,$order['order_keyword']);
	/*foreach ($ordernastrs as $t=>$onastr)
	{
		if ($onastr[1]==urlencode($link))
		{	
			$nastr=$onastr[0]*(-1);
			break;
		}
	}*/
	
	$gn_time_end = microtime(true);
	$gn_time += $gn_time_end - $gn_time_start;
	
	if (intval($nastr)==1) 
	{
		$nstr='green';
		$nnstr="positive";
	}
	elseif (intval($nastr)==-1) 
	{
		$nstr='red';
		$nnstr="negative";
	}
	else 
	{
		$nstr='black';
		$nnstr="neutral";
	}
	$nick=$outcash['nick'][$key];
    $hn=parse_url($link);
    $hn=$hn['host'];
    $ahn=explode('.',$hn);
    $hn = $ahn[count($ahn)-2].'.'.$ahn[count($ahn)-1];
	$hh = $ahn[count($ahn)-2];

	if (!in_array($hn,$resorrr))
	{
		//fwrite($fpizdec, "123\n");
		continue;
	}

	$time=mktime(0,0,0,date('n',$time),date('j',$time),date('Y',$time));
	$isshow=1;
	$cc=0;
	//$fpizdec = fopen('debug.txt', 'a');
	//fwrite($fpizdec, $content);
	//getting nick for twitter by post
	/*if ($hn=='twitter.com') {
		list($tmp,$tmp,$tmp,$nick,$tmp)=explode("/",$link,5);
		$nick=ereg_replace("\n",'',$nick);
		$_COOKIE['writer']=ereg_replace("\n",'',$_COOKIE['writer']);
	}
	if ($hn=='livejournal.com')
	{
		$regex='/\/\/(?<nick>.*?)\.livejournal/is';
		preg_match_all($regex,$link,$outn);
		$nick=$outn['nick'][0];
	}*/
	//echo ereg_replace("\n",'',addslashes($nick))."|".array_search(ereg_replace("\n",'',addslashes($nick)), $metrics['speakers']['nick'])."<br>";
	/*if ((in_array(ereg_replace("\n",'',addslashes($nick)), $metrics['speakers']['nick'])) || (ereg_replace("\n",'',addslashes($nick))!=""))
	{
		$posit=array_search(ereg_replace("\n",'',addslashes($nick)), $metrics['speakers']['nick']);
	}
	else
	{
		$posit=-1;
	}
	if (ereg_replace("\n",'',addslashes($nick))=="")
	{
		$posit=-1;
	}*/
	//$ressp=$db->query("SELECT * from blog_spam WHERE order_id='".intval($_POST['order_id'])."'");
	//echo "SELECT * from blog_spam WHERE ord_id=".intval($_GET['order_id'])." AND spam_link=".$link." LIMIT 1";
	//while($ordersp = $db->fetch($ressp))
	//{
		//array_push($orderspams,$ordersp['post_link']);	
		//$orderspams[]=urldecode($ordersp['post_link']);
	//}
	//echo "|".$posit."|";
	//fwrite($fpizdec, "{".$nick."}-{".$_COOKIE['writer']."}\n");
	//echo "cool";	
	
	/*if (($_COOKIE['writer']!='')&&($_COOKIE['writer']!=$nick)&&($isshow==1))
	{
		$isshow=0;
	}*/
	
	/*if (((($_COOKIE[$nnstr])=='false') || empty($_COOKIE[$nnstr])) && ($isshow==1))
	{
		$isshow=0;
		//fwrite($fpizdec, " 0");
		//echo "cool";
	}*/
	
	// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
	
	/*if (((($_POST[$nnstr])=='false') || empty($_POST[$nnstr])) && ($isshow==1))
	{
		$isshow=0;
		//fwrite($fpizdec, " 0");
		//echo "cool";
	}
	/*if (($_GET['time']!=0) && ($isshow==1))
	{
		if ((intval($time)!=intval($_GET['time']))) 
		{
			$isshow=0;
			//fwrite($fpizdec, " 1");
			//echo "1";
		}
	}*/
	//-----------------------TIME----------------------
	/*$ntime=strtotime($_POST['ntime']);
	$etime=strtotime($_POST['etime']);
	if (($_POST['ntime']!='') && ($_POST['etime']!=''))
	{
		if (($time<$ntime) || ($time>$etime))
		{	
			//fwrite($fpizdec, $ntime." ".$time." ".$etime."\n"); 
			$isshow=0;
		}
	}
	//------------------------RESOURCES
	if (!in_array($hn,$resorrr))
	{
		//fwrite($fpizdec, "123\n");
		$isshow=0;
	}
	/*if ($_POST['res_other']==true)
	{
		if (in_array($hn,$othersources))
		{
			$isshow=1;
		}
	}*/
	//---------------------------------
	//elseif ($_POST[''])
	/*{
		//$ressp=$db->query("SELECT * from blog_spam WHERE ord_id=".intval($_GET['order_id'])." AND spam_link='".$link."' LIMIT 1");
		//echo "SELECT * from blog_spam WHERE ord_id=".intval($_GET['order_id'])." AND spam_link=".$link." LIMIT 1";
		//$ordersp = $db->fetch($ressp);
		if (in_array($link,$orderspams))
		{
			$isshow=0;
		}
	}*/
	/*if ((($makesocial==true) && ($social!=$hn)) && ($isshow==1))
	{
		$isshow=0;
		//fwrite($fpizdec, " 2");
		//echo "3";
	}
	else
	{
		$cc=1;
	}
	if ($cc==0)
	{
		//if (($_COOKIE[$hh]==false) && ($isshow==1))
		if ($_COOKIE['other']==false)
		{
			if (!in_array($hn,$sources) && ($isshow==1))
			{
				$isshow=0;
				//fwrite($fpizdec, " 3");
				//echo "5";
			}
		}
		else
		if ($_COOKIE['other']==true)
		{
			if (!in_array($hn,$sources) && ($isshow==1))
			{
				$isshow=0;
				//fwrite($fpizdec, " 4");
			}
		}
	}*/
	//echo "end";
	//fwrite($fpizdec, "\n");
	//echo $isshow;
	//fclose($fpizdec);
	//-------------------SPAM NOT SPAM ...--------------------
	/*if ($_POST['showmode']=="notspam")
	{
		if (in_array($link,$orderspams))
		{
			$isshow=0;
		}
		//-----------------------REP-----------------------
		if (in_array($content,$mas_rep))
		{
			$isshow=0;
		}
		else
		{
			$mas_rep[]=$content;
		}
		//-------------------------------------------------
	}
	else
	if ($_POST['showmode']=="showall")
	{
		//$isshow=1;
	}
	else
	if ($_POST['showmode']=="showfav")
	{
		//$resfav=$db->query("SELECT * from blog_fav WHERE order_id='".intval($_GET['order_id'])."' AND post_link='".$link."' LIMIT 1");
		//echo "SELECT * from blog_spam WHERE ord_id=".intval($_GET['order_id'])." AND spam_link=".$link." LIMIT 1";
		//$orderfav = $db->fetch($resfav);
		if (in_array($link,$orderfavs))
		{
			//$isshow=1;
		}
		else
		{
			$isshow=0;
		}
		if (in_array($link,$orderspams))
		{
			$isshow=0;
		}
		/*$ntime=strtotime($_POST['ntime']);
		$etime=strtotime($_POST['etime']);
		if (($_POST['ntime']!='') && ($_POST['etime']!=''))
		{
			if (($time<$ntime) || ($time>$etime))
			{	
				//fwrite($fpizdec, $ntime." ".$time." ".$etime."\n"); 
				$isshow=0;
			}
		}*/
	//}
	/*if ($_POST['snick']=='')
	{
		$isshow=get_isshow();
	}
	else
	{
		if ($nick!=$_POST['snick'])
		{
			$isshow=0;
		}
	}*/
	//----------------------------------------------
	//if ($isshow==1)
	//{
	//echo 
	/*
	$resfavers=$db->query("SELECT * from robot_blogs2 WHERE blog_login='".$nick."' AND blog_link='".$hn."' LIMIT 1");
	//echo $hh;
	$orderfavers = $db->fetch($resfavers);
	$colread="";
	if (intval($orderfavers['blog_readers'])!=0)
	{
		$colread=' подписчиков: '.intval($orderfavers['blog_readers']);
	}
	$colcom+=1;
	if (!in_array($hh,$resy))
	{
		$resy[]=$hh;
	}
	$colupom="";
	if (intval($metrics['speakers']['posts'][$posit])!=0)
	{
		$colupom=' упоминаний: '.intval($metrics['speakers']['posts'][$posit]);
	}*/
	$typep='<div style="float: right; margin: 0 15px 0 0;"><select id="my_select'.($wkey+1).'" onChange="var typ=$(\\\'#my_select'.($wkey+1).' option:selected\\\').val(); $.ajax({ type: \\\'POST\\\', url: \\\'/new/ajax\\\', data: \\\'order_id='.intval($_POST['order_id']).'&link='.addslashes($link).'&typep=\\\'+typ, success: function(msg1){  } });">';
    //$pis=array_search(urlencode($link),$mastype[0]);
    //$pis=$mastype[1][$pis];
    //fclose($fpizdec);
    for ($jj=0;$jj<count($mastexttype);$jj++)
    {
        if ($pis==$jj)
        {
            $typep.='<option value="'.$jj.'" SELECTED>'.$mastexttype[$jj].'</option>';
        }
        else
        {
            $typep.='<option value="'.$jj.'">'.$mastexttype[$jj].'</option>';
        }
    }
    $typep.='</select></div>';

	if (intval($isfav)==1) $favor='<a href="#"><div class="fav" onclick="';
	else $favor='<a href="#"><div class="fav2" onclick="';
	if (intval($isspam)==1) $spammor='<a href="#"><div class="spamm2" onclick="';
	else $spammor='<a href="#"><div class="spamm" onclick="';
	$wkey++;
	$nastr2='<a href="#"><div class="plus" onclick="var nastrbtn=this; $.ajax({ type: \\\'POST\\\', url: \\\'/new/ajax\\\', data: \\\'order_id='.$_POST['order_id'].'&positive='.urlencode($link).'\\\', success: function(msg1){ $(\\\'#nstrid'.($wkey-1).'\\\').attr(\\\'style\\\',\\\'color:\\\'+msg1); } }); return false;"></div></a><a href="#"><div class="neutral" onclick="var nastrbtn=this; $.ajax({ type: \\\'POST\\\', url: \\\'/new/ajax\\\', data: \\\'order_id='.$_POST['order_id'].'&neutral='.urlencode($link).'\\\', success: function(msg1){$(\\\'#nstrid'.($wkey-1).'\\\').attr(\\\'style\\\',\\\'color:\\\'+msg1);} }); return false;"></div></a><a href="#"><div class="minus" onclick="var nastrbtn=this; $.ajax({ type: \\\'POST\\\', url: \\\'/new/ajax\\\', data: \\\'order_id='.$_POST['order_id'].'&negative='.urlencode($link).'\\\', success: function(msg1){$(\\\'#nstrid'.($wkey-1).'\\\').attr(\\\'style\\\',\\\'color:\\\'+msg1);} }); return false;"></div></a>';
	//fwrite($fpizdec, $link);
	//fclose($fpizdec);
		if ($i>0) $html_out.= ',
';

		/*echo '[\''.ereg_replace("\n",'',addslashes($nick)).'\', \''.str_replace("\n",'',addslashes($content)).'\', \''.date("d.m.Y",$time).'\', \''.str_replace("\n",'',addslashes($link)).'\',\''.(file_exists('../img/social/'.$hh.'.png')?$hh.'.png':'wobot_logo.gif').'\',\''.$hn.'\',\''.$nstr.'\','.$i.',\''.$favor.' $.post(\\\'/ajax/\\\', { order_id: \\\'John\\\', fav: \\\''.$link.'\\\' } );"></div>\']';*/
		/*echo '[\''.ereg_replace("\n",'',addslashes($nick)).'\', \''.str_replace("\n",'',addslashes($content)).'\', \''.date("d.m.Y",$time).'\', \''.str_replace("\n",'',addslashes($link)).'\',\''.(file_exists('../img/social/'.$hh.'.png')?$hh.'.png':'wobot_logo.gif').'\',\''.$hn.'\',\''.$nstr.'\','.$i.',\''.$favor.' var favbtn=this; $.ajax({ type: \\\'POST\\\', url: \\\'/new/ajax\\\', data: \\\'order_id='.$_POST['order_id'].'&fav='.$link.'\\\', success: function(msg){ $(favbtn).attr(\\\'class\\\',msg); }});"></div>\']';*/
		$html_out .= '[\''.ereg_replace("\n",'',addslashes($nick)).'\', \''.str_replace("\n",'',addslashes($content)).'\', \''.date("d.m.Y",$time).$colupom.' '.$colread.' '.$typep.'\', \''.urlencode(str_replace("\n",'',addslashes($link))).'\',\''.(file_exists('../img/social/'.$hh.'.png')?$hh.'.png':'wobot_logo.gif').'\',\''.$hn.'\',\''.$nstr.'\','.$i.',\''.$nastr2.$spammor.' var spambtn=this; $.ajax({ type: \\\'POST\\\', url: \\\'/new/ajax\\\', data: \\\'order_id='.$_POST['order_id'].'&spam='.ereg_replace("\n",'',$link).'\\\', success: function(msg1){ $(spambtn).attr(\\\'class\\\',msg1); members['.($i+1).'][8]=\\\'\\\';}}); return false;"></div></a>'.$favor.' var favbtn=this; $.ajax({ type: \\\'POST\\\', url: \\\'/new/ajax\\\', data: \\\'order_id='.$_POST['order_id'].'&fav='.ereg_replace("\n",'',$link).'\\\', success: function(msg){ $(favbtn).attr(\\\'class\\\',msg); members['.($i+1).'][8]=\\\'\\\';}}); return false;"></div></a>\']';
		//$html_out .= '[\'\', \'\', \'\', \'\',\''.(file_exists('../img/social/'.$hh.'.png')?$hh.'.png':'wobot_logo.gif').'\',\''.$hn.'\',\''.$nstr.'\','.$i.',\''.$nastr2.$spammor.' var spambtn=this; $.ajax({ type: \\\'POST\\\', url: \\\'/new/ajax\\\', data: \\\'order_id='.$_POST['order_id'].'&spam='.ereg_replace("\n",'',$link).'\\\', success: function(msg1){ $(spambtn).attr(\\\'class\\\',msg1); members['.($i+1).'][8]=\\\'\\\';}});"></div></a>'.$favor.' var favbtn=this; $.ajax({ type: \\\'POST\\\', url: \\\'/new/ajax\\\', data: \\\'order_id='.$_POST['order_id'].'&fav='.ereg_replace("\n",'',$link).'\\\', success: function(msg){ $(favbtn).attr(\\\'class\\\',msg); members['.($i+1).'][8]=\\\'\\\';}});"></div></a>\']';
		$i++;
	
}
if (($i<10) || ($i>20))
{
	if (($i % 10)==1)
	{
		$text_i="упоминание";
	}
	else
	if ((($i % 10)==2) || (($i % 10)==3) || (($i % 10)==4))
	{
		$text_i="упоминания";
	}
	else
	{
		$text_i="упоминаний";
	}
}
else
{
	$text_i="упоминаний";
}
//$colres=count($resorrr);
if (($colres<10) || ($colres>20))
{
	if (($colres % 10)==1)
	{
		$text_res="ресурсе";
	}
	else
	{
		$text_res="ресурсах";
	}
}
else
{
	$text_res="ресурсах";
}
$html_out.= '		];
function setfav(i)
{
	document.getElementById(\'fav_nick\').value=members[i][0];
	document.getElementById(\'fav_content\').value=members[i][1];
	document.getElementById(\'fav_time\').value=members[i][2];
	document.getElementById(\'fav_link\').value=members[i][3];
	document.getElementById(\'fav_img\').value=members[i][0];
	document.getElementById(\'fav_hn\').value=members[i][1];
	document.getElementById(\'fav_nastr\').value=members[i][2];
	document.getElementById(\'fav_i\').value=members[i][3];
}

	function pageselectCallback(page_index, jq){
        // Get number of elements per pagionation page from form
        var items_per_page = 10;
        var max_elem = Math.min((page_index+1) * items_per_page, members.length);
        var newcontent = \'\';
        
        // Iterate through a selection of the content and build an HTML string
        for(var i=page_index*items_per_page;i<max_elem;i++)
        {
			newcontent += \'<div style="border: 1px solid #eee; -moz-border-radius: 5px; padding: 3px; margin: 3px; background: #fff;"><span class="sl rln2">\'+(members[i][7]+1)+\'. \'+members[i][2]+\' \'+(members[i][8])+\'</span><br><img src="/img/social/\'+members[i][4]+\'" title="\'+members[i][5]+\'" alt="\'+members[i][5]+\'"> <a href="/new/ajax?plink=\'+members[i][3]+\'&kword='.urlencode($order['order_keyword']).'" id="nstrid1\'+i+\'" target="_blank" style="color: \'+members[i][6]+\';"><font id="nstrid\'+i+\'"><u><b>\'+members[i][0]+\'</b> \'+members[i][1]+\'</u></font></a></div>\';
        }

        // Replace old content with new content
        $(\'#Searchresult\').html(newcontent);
        
        // Prevent click eventpropagation
        return false;
    }
	
    // When document has loaded, initialize pagination and form 

    function showcomment(){
		// Create pagination element with options from form
        //var optInit = getOptionsFromForm();
		var optInit = {callback: pageselectCallback};
		optInit[\'items_per_page\']=10;
		optInit[\'num_display_entries\']=10;
		optInit[\'num_edge_entries\']=2;
		optInit[\'prev_text\']=\'←\';
		optInit[\'next_text\']=\'→\';
        $("#Pagination").pagination(members.length, optInit);

    };

    $(document).ready(function(){
		showcomment();
		/*$(\'#colup\').attr(\'value\',\''.$i.'\');*/
		$(\'#colup\').html(\''.$i.' '.$text_i.'\');
		$(\'#colup1\').html(\''.(count($resorrr)).' '.$text_res.'\');
    });
    
</script>
<br>
';
/*if ($_COOKIE['writer']!='')
{
	echo 'По автору "'.$_COOKIE['writer'].'" (<a href="#" onclick="$.cookie(\'writer\', \'\'); loaditem(\'user/comment?order_id='.intval($_POST['order_id']).'\',\'#commentbox\', function() { showcomment(); } );">отменить</a>)';
}
if ($_COOKIE['showfav']=='1')
{
	echo 'Избранные (<a href="#" onclick="$.cookie(\'showfav\', \'\'); loaditem(\'user/comment?order_id='.intval($_POST['order_id']).'\',\'#commentbox\', function() { showcomment(); } );">отменить</a>)';
}*/
$html_out .='

	  <div class=\'pagination\' id=\'Pagination\' style=\'padding: 5px 10px; margin: 0px 10px; -moz-border-radius: 5px; border-radius: 5px; width: 750px; height: 28px; align: center;\'></div>
	  <dl id=\'Searchresult\'>
	    <dt>Результаты выборки</dt>
	  </dl>
	</div>
	</div>
';

$time_end = microtime(true);
$time = $time_end - $time_start;

$_COOKIE['writer']='';

stop_tpl();

//echo '<b>delay: '.$time.', getnastr delay: '.$gn_time.'</b>';
/*//
echo '<table width="100%"><td align="center">';
if ($pn>7) echo '←&nbsp;';
for ($i=0;$i<intval($j/(50+1)+1);$i++)
if (($i>$pn-8)&&($i<$pn+8))
echo ($pn==$i?'':'<a href="#" onclick="loaditem(\'user/comment?order_id='.intval($_GET['order_id']).'&time='.intval($_GET['time']).'000&p='.$i.'\',\'#commentbox\');return false;">').($i+1).($pn==$i?' ':'</a> ');
if ($pn<intval($j/(50+1)-7)) echo '→';
echo '</td></table>';
//*/
}
		//$res=$db->query('SELECT * FROM users');
		//echo 'Список пользователей:<br>';
		//$i=0;
		//while ($row = $db->fetch($res)) {
		//	$i++;
		//	echo $row['user_email'].' <a class="openform" href="/user/adduser?user_id='.$row['user_id'].'">редактировать</a> <a href="/user/keywords/'.$row['user_id'].'">услуги</a><br>';
		//}
		//if ($i==0) echo 'пользователи отсутствуют<br>';
		//echo'
//<p class="menuitem"><a href="#" class="userlnk" rel="user/admin">Обновить</a> <a class="openform" href="/user/adduser">Добавить</a></p>
//';
//}
?>
