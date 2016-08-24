<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
require_once('com/config.php');
require_once('com/func.php');
require_once('com/db.php');
require_once('bot/kernel.php');
require_once('com/auth.php');
require_once('modules/tcpdf/config/lang/rus.php');
require_once('modules/tcpdf/tcpdf.php');
//$_GET['order_id']=3;
//$_COOKIE['showfav']=1;
//$_GET['order_id']=3;
//print_r($_COOKIE);
//print_r($_GET);
//$_POST['order_id']=145;
ignore_user_abort(true);
set_time_limit(0);
ini_set('max_execution_time',0);
ob_implicit_flush();
//print_r($_POST);

$db = new database();
$db->connect();
//print_r($_POST);
//echo 'not loged';

auth();
if (!$loged) die();

if (intval($_POST['order_id'])==0) die();
$fn = "/var/www/data/blog/".intval($_POST['order_id']).".metrics";
$h = fopen($fn, "r");
$data = fread($h, filesize($fn));
$metricss=json_decode($data,true);
fclose($h);

unset($sources);
unset($data);
$fn = "/var/www/data/blog/".intval($order_id).".src";
$h = fopen($fn, "r");
$data = fread($h, filesize($fn));
$sources=json_decode($data, true);
$k=0;
foreach ($sources as $i => $source)
{
		$othert+=$source;
}

if ($_POST['format']=='excel') {

		header('Content-Type: text/xml, charset=windows-1251; enucoding=windows-1251');
		header('Expires: ' . gmdate('D, d M Y H:i:s') . ' GMT');
		header("Accept-Ranges: bytes");
		header("Content-Transfer-Encoding: binary");
		


		// IE needs specific headers

		$agent = $_SERVER['HTTP_USER_AGENT'];
		if(eregi("msie", $agent) && !eregi("opera", $agent))
		{
		    header('Content-Disposition: inline; filename="' . date('Y-m-d') . '.xls"');
		    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		    header('Pragma: public');
		}
		else
		{
		    header('Content-Disposition: attachment; filename="' . date('Y-m-d') . '.xls"');
		    header('Pragma: no-cache');
		}


}
elseif($_POST['format']=='word')
{
	header("Content-type: application/vnd.ms-word");
	header('Content-Disposition: attachment;Filename=wobot'.date('-dmy-his').'.doc');
	$echo_word.="<table>"."<tr><td>Дата</td><td>Ресурс</td><td>Ссылка</td><td>Избранное</td><td>Спам</td><td>Эмоциональность</td><td>Ник</td><td>Упоминание</td></tr>";
}


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

//print_r($resorrr);


if ($_POST['positive']==''&&$_POST['negative']==''&&$_POST['neutral']=='')
{
	$_POST['positive']='true';
	$_POST['negative']='true';
	$_POST['neutral']='true';
}

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
			/*
			//---------------------------------SPAM ADD------------------------------------
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
			while($orderfav = $db->fetch($resfav))
			{
				$orderfavs[]=urldecode($orderfav['post_link']);
			}

			unset($ordernastrs);
			unset($ordernastrs2);
			$resnastr=$db->query("SELECT * from blog_nastr WHERE order_id='".intval($_POST['order_id'])."'");
			while($ordernastr = $db->fetch($resnastr))
			{
				$ordernastrs[]=array($ordernastr['post_nastr'],$ordernastr['post_link']);
			}*/
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
$where=get_isshow2();
$query='SELECT * from blog_post as p LEFT JOIN robot_blogs2 AS b ON p.blog_id=b.blog_id WHERE p.order_id="'.intval($_POST['order_id']).'"'.((strlen($where)==0)?'':' AND ').$where.' ORDER BY p.post_time DESC';
//echo '<br><br>'.$query.'<br><br>';
//die();
$respost=$db->query($query);

//echo "SELECT * from blog_spam WHERE ord_id=".intval($_GET['order_id'])." AND spam_link=".$link." LIMIT 1";
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
	$ii++;
}
//print_r($outcash);
//die();
$pn=intval($_GET['p']);
if ($_GET['time']!=0) {
list($_GET['time'],$tmp)=explode(',',$_GET['time'],2);
$_GET['time']/=1000;
$_GET['time']=mktime(0,0,0,date('n',$_GET['time']),date('j',$_GET['time']),date('Y',$_GET['time']));
}
$i=0;
//array_multisort($outcash['time'],SORT_DESC,$outcash['link'],SORT_DESC,$outcash['content'],SORT_DESC,$outcash['nick'],SORT_DESC,$outcash['loc'],SORT_DESC);
unset($mas_rep);
$wkey=0;
$ckol=-1;
foreach ($outcash['link'] as $key => $llink)
{
	$link=urldecode($llink);
	$time=$outcash['time'][$key];
	$content=$outcash['content'][$key];
	$gn_time_start = microtime(true);
	$isfav=$outcash['isfav'][$key];
	if ($isfav==1) $isfav='+';
	else $isfav='-';
	$nastr=$outcash['nastr'][$key];
	if ($nastr==1) $nastr='+';
	else $nastr='-';
	$isspam=$outcash['isspam'][$key];
	if ($isspam==1) $isspam='+';
	else $isspam='-';
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
	
	if ($nastr==-1) 
	{
		$nstr='green';
		$nnstr="positive";
	}
	elseif ($nastr==1) 
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
		continue;
	}

	$time=mktime(0,0,0,date('n',$time),date('j',$time),date('Y',$time));
	$isshow=1;
	$cc=0;
	
	//if ($_POST['all_main']!='true')
	//{
		//$isshow=get_isshow();
	//}
	//echo $isshow;
	if ($nstr=='black') $nstr='';
	elseif ($nstr=='green') $nstr='+';
	elseif ($nstr=='red') $nstr='-';
//----------------------------------------------
	//if ($isshow==1)
	//{
		//$content_r=GetFullPost($link,mb_substr($content,3,mb_strlen($content,"UTF-8")-3,"UTF-8"));
		//echo "|".$link."| |".mb_substr($content,3,mb_strlen($content,"UTF-8")-3,"UTF-8")."| |";//.GetFullPost($link,mb_substr($content,2,mb_strlen($content,"UTF-8"),"UTF-8"))."|<br>";
		if ($content_r=="")
		{
			$content_r='*'.$content;
		}
		$content_r=str_replace("\n"," ",$content_r);
		$content_r=str_replace("\t"," ",$content_r);		
		if (!in_array($hh,$resy))
		{
			$resy[]=$hh;
		}
		$ckol++;
		if ($_POST['format']=='excel')
		{
             $csv_output.=date("d.m.Y",$time)."\t".str_replace("\n","",$hn)."\t".str_replace("\n","",$link)."\t".$isfav."\t".$isspam."\t".$nastr."\t".str_replace("\n","",$nick)."\t".str_replace("\n","",$content)."\n";
		}
		elseif ($_POST['format']=='pdf')
		{
			$html.='
			<style>
			a {		
				font-size: 20px;
				padding: 2px;
			}

			span {
				font-size: 18px;
				padding: 2px 2px 5px 2px;
			}
			</style>
			';
			//sleep(1);
			$html.='<a href="'.urlencode(/*substr(*/addslashes($link)/*,0,strlen($link)-1)*/).'" target="_blank">'./*urlencode*/($content_r).'</a><br><span class="sl rln">'.($ckol+1).' ('.date("d.m.Y",$time).')</span><br>';
			//echo '<a href="'.substr($link,0,strlen($link)-1).'" target="_blank">'.htmlentities($content,ENT_COMPAT,'utf-8').'</a><br><span class="sl rln">'.($ckol+1).' ('.date("d.m.Y",$time).')</span><br>';
		}
		elseif ($_POST['format']=='word')
		{
			$echo_word.="<tr><td>".date("d.m.Y",$time)."</td><td><a href=\"http://www.".$hn."\">".$hn."</a></td><td><a href=".$link.">".$link."</a></td><td>".$isfav."</td><td>".$isspam."</td><td>".$nstr."</td><td>".$nick."</td><td>".$content_r."</td></tr>";
		}
	//}
}
	if ($_POST['format']=='excel')
	{
		$csv_output='Упоминаний: '.$ckol."\t".'Ресурсов: '.count($resy)."\t".'Аудитория: '.$metricss['value']."\t".'Цитируемость: '.((intval((1/($othert/250))*100)+intval((1/($metricss['value']/1000))*100))/100)."\t".'Вовлеченность: '.(intval($othert/$metricss['value']*100)/100)."\t".'Доверие: 0 '."\n".'Дата'."\t".'Ресурс'."\t".'Ссылка'."\t".'Изб'."\t".'Спам'."\t".'Эмо'."\t".'Ник'."\t".'Текст упоминания'."\n".$csv_output;    
		//$csv_output .= "Дата\t Ресурс\t Изб\t Спам\t Эмо\t Ник\t Текст упоминания \n";
     print(iconv("UTF-8","WINDOWS-1251",$csv_output));
	}
	elseif ($_POST['format']=='pdf')
	{
		$html="<div style=\"font-size: 24px;\">Упоминаний: ".$ckol." Ресурсов: ".count($resy)." Аудитория: ".$metricss['value']." "." Цитируемость: ".((intval((1/($othert/250))*100)+intval((1/($metricss['value']/1000))*100))/100)." Вовлеченность: ".(intval($othert/$metricss['value']*100)/100)." Доверие: 0</div><br>".$html;
		$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
		$pdf->SetCreator(PDF_CREATOR);
		$pdf->SetAuthor('Wobot Research');
		$pdf->SetTitle('Экспорт сообщений');
		$pdf->SetSubject('Экспорт сообщений');
		$pdf->SetKeywords('Wobot Research');
		$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' за '.date('h:i:s d.m.Y'), PDF_HEADER_STRING);
		$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
		$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
		$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
		$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
		$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
		$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
		$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
		$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
		$pdf->setLanguageArray($l);
		$pdf->setFontSubsetting(true);
		$pdf->SetFont('dejavusans', '', 14, '', true);
		$pdf->AddPage();
		$pdf->writeHTMLCell($w=0, $h=0, $x='', $y='', $html, $border=0, $ln=1, $fill=0, $reseth=true, $align='', $autopadding=true);
		$pdf->lastPage();
		$pdf->Output('wobot'.date('-dmy-his').'.pdf', 'I');
	}
	elseif ($_POST['format']=='word')
	{
		echo "<html>"."<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\">"."<body><table>"."<tr><td>Упоминаний</td><td>Ресурсов</td><td>Аудитория</td><td>Цитируемость</td><td>Вовлеченность</td><td>Доверие</td></tr><tr><td>".$ckol."</td><td>".count($resy)."</td><td>".$metricss['value']."</td><td>".((intval((1/($othert/250))*100)+intval((1/($metricss['value']/1000))*100))/100)."</td><td>".(intval($othert/$metricss['value']*100)/100)."</td><td>0</td></tr></table>".$echo_word;
		echo "</table></body>"."</html>";
	}
}
?>
