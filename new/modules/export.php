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
require_once('ods.php');
//require_once('modules/tcpdf/config/lang/rus.php');
//require_once('modules/tcpdf/tcpdf.php');
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
ini_set("memory_limit", "2048M");
//max_execution_time(0);
//print_r($_POST);

$db = new database();
$db->connect();
//print_r($_POST);
//echo 'not loged';

auth();
if (!$loged) die();

if (intval($_POST['order_id'])==0) die();
//print_r($_SESSION);
//print_r($_POST);
$res=$db->query("SELECT * from blog_orders WHERE order_id=".intval($_POST['order_id'])." and user_id=".intval($user['user_id'])." LIMIT 1");
$order=$db->fetch($res);
$src=$order['order_src'];
$src=json_decode($src,true);
$data=$order['order_metrics'];
$metrics=json_decode($data,true);
//print_r($metrics['topwords']);
//print_r($metrics['location']);
//print_r($order);
$type_text="";
foreach ($_POST as $inddd => $posttt)
{
	if ((substr($inddd, 0, 4)=='res_'))
	{
		if ($posttt=='on')
		{
			if (mb_strlen(substr($inddd, 4),'UTF-8')!=1)
			{
				$resorrr[]=str_replace("_",".",substr($inddd,4));
			}
		}
	}
	if ((substr($inddd, 0, 4)=='tags'))
	{
		if (substr($inddd, 4)!='')
		{
			$tgv[]=substr($inddd, 4);
		}
		else
		{
			$tgv[]='no';
		}
	}
	if ((substr($inddd, 0, 7)=='cities_'))
	{
		if (isset($wobot['destn2'][str_replace('_',' ',substr($inddd, 7))]))
		{
			$loc[]=str_replace('_',' ',substr($inddd,7));
		}
		if (substr($inddd, 7)=='не_определено')
		{
			$loc[]='na';
		}
	}
	if ((substr($inddd, 0, 16)=='promouters_popup'))
	{
		$prom[]=$posttt;
	}
	if ((substr($inddd, 0, 14)=='speakers_popup'))
	{
		$speak[]=$posttt;
	}
	if ((substr($inddd, 0, 6)=='words_'))
	{
		$kww[]=substr($inddd, 6);
	}
}
$query1='SELECT * FROM blog_tag WHERE user_id='.intval($user['user_id']);
   $respost1=$db->query($query1);
while($tgl1 = $db->fetch($respost1))
{
	$tagsall[$tgl1['tag_tag']]=$tgl1['tag_name'];
	$tagsallrev[$tgl1['tag_name']]=$tgl1['tag_tag'];
	$mtag[$tgl1['tag_tag']]=$tgl1['tag_name'];
}

if (isset($_SESSION[$_POST['hashq']]))
{
	$sqw='SELECT * from blog_post as p LEFT JOIN robot_blogs2 AS b ON p.blog_id=b.blog_id WHERE p.order_id="'.intval($_POST['order_id']).'" '.getisshow3().' ORDER BY p.post_time DESC';
	$countqposts=$db->query('SELECT count(*) as cnt from blog_post as p LEFT JOIN robot_blogs2 AS b ON p.blog_id=b.blog_id WHERE p.order_id="'.intval($_POST['order_id']).'" '.getisshow3().' GROUP BY post_host');
	while ($count=$db->fetch($countqposts))
	{
		$cnt+=$count['cnt'];
		$cnt_host++;
	}
}
else
{
	$sqw='SELECT * from blog_post as p LEFT JOIN robot_blogs2 AS b ON p.blog_id=b.blog_id WHERE p.order_id="'.intval($_POST['order_id']).'" AND p.post_time>'.intval(strtotime($_POST['ntime'])).' AND p.post_time<'.intval(strtotime($_POST['etime'])).' ORDER BY p.post_time DESC';
	$countqposts=$db->query('SELECT count(*) as cnt from blog_post as p LEFT JOIN robot_blogs2 AS b ON p.blog_id=b.blog_id WHERE p.order_id="'.intval($_POST['order_id']).'" AND p.post_time>'.intval(strtotime($_POST['ntime'])).' AND p.post_time<'.intval(strtotime($_POST['etime'])).' GROUP BY post_host');
	while ($count=$db->fetch($countqposts))
	{
		$cnt+=$count['cnt'];
		$cnt_host++;
	}
}
	//echo $sqw;
	$qpost=$db->query($sqw); // проверка order_id
	while($pst = $db->fetch($qpost))
	{
		$outcash['link'][$ii]=str_replace("\n","",$pst['post_link']);
		$outcash['time'][$ii]=$pst['post_time'];
		$outcash['content'][$ii]=$pst['post_content'];
		$outcash['fcontent'][$ii]=$pst['ful_com_post'];
		$outcash['isfav'][$ii]=$pst['post_fav'];
		$outcash['nastr'][$ii]=$pst['post_nastr'];
		$outcash['isspam'][$ii]=$pst['post_spam'];
		$outcash['nick'][$ii]=$pst['blog_nick'];
		$outcash['tag'][$ii]=$pst['post_tag'];
		$outcash['comm'][$ii]=$pst['blog_readers'];
		$outcash['eng'][$ii]=$pst['post_engage'];
		$outcash['loc'][$ii]=$pst['blog_location'];
		$outcash['gender'][$ii]=$pst['blog_gender'];
		$outcash['age'][$ii]=$pst['blog_age'];
		$outcash['login'][$ii]=$pst['blog_login'];
		$ii++;
	}
	if ($_POST['format']=='Excel') {

			header('Content-Type: text/xml, charset=windows-1251; encoding=windows-1251');
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
	elseif($_POST['format']=='Word')
	{
		header("Content-type: application/vnd.ms-word");
		header('Content-Disposition: attachment;Filename=wobot'.date('-dmy-his').'.doc');
		$echo_word.="<table>"."<tr><td>Дата</td><td>Ресурс</td><td>Ссылка</td><td>Тип ресурса</td><td>Избранное</td><td>Спам</td><td>Эмоциональность</td>".(($order['order_engage']=='1')?"<td>Engagement</td>":"")."<td>Теги</td><td>Ник</td><td>Аудитория</td><td>Геотрагетинг</td><td>Упоминание</td>".(($order['ful_com']=='1')?"<td>Полное упоминание</td>":"")."</tr>";
	}
	if ($_POST['format']=='OpenOffice')
	{
		$data=$order['order_metrics'];
		$metrics=json_decode($data,true);
		$res=$order['order_src'];
		$sources=json_decode($res, true);
		$src_count=count($sources);
		foreach ($sources as $i => $source)
		{
				$other+=$source;
		}
		$ods  = new ods();
		$table = new odsTable('utf8');
		$row   = new odsTableRow();
		$row->addCell( new odsTableCellString("Упоминания:") );
		$row->addCell( new odsTableCellString("Ресурсов:") );
		$row->addCell( new odsTableCellString("Аудитория:") );
		$row->addCell( new odsTableCellString("Engagement:") );
		$table->addRow($row);
		$row   = new odsTableRow();
		$row->addCell( new odsTableCellString($cnt) );
		$row->addCell( new odsTableCellString($cnt_host) );
		$row->addCell( new odsTableCellString($metrics['value']) );
		$row->addCell( new odsTableCellString($metrics['engagement']) );
		$table->addRow($row);
		$row   = new odsTableRow();
		$row->addCell( new odsTableCellString("Дата") );
		$row->addCell( new odsTableCellString("Ресурс") );
		$row->addCell( new odsTableCellString("Ссылка") );
		$row->addCell( new odsTableCellString("Тип") );
		$row->addCell( new odsTableCellString("Избранное") );
		$row->addCell( new odsTableCellString("Спам") );
		$row->addCell( new odsTableCellString("Эмоциональность") );
		if ($order['order_engage']==1)
		{
			$row->addCell( new odsTableCellString("Engagement") );
		}
		$row->addCell( new odsTableCellString("Теги") );
		$row->addCell( new odsTableCellString("Ник") );
		$row->addCell( new odsTableCellString("Аудитория") );
		$row->addCell( new odsTableCellString("Геотаргетинг") );
		$row->addCell( new odsTableCellString("Упоминание") );
		if ($order['ful_com']=='1')
		{
			$row->addCell( new odsTableCellString("Полное упоминание") );
		}
		$table->addRow($row);
	}
	foreach ($outcash['link'] as $key => $llink)
	{
		$link=urldecode($llink);
		$time=$outcash['time'][$key];
		$content=$outcash['content'][$key];
		$fcontent=$outcash['fcontent'][$key];
		$comm=intval($outcash['comm'][$key]);
		$gn_time_start = microtime(true);
		$isfav=$outcash['isfav'][$key];
		$tag=$outcash['tag'][$key];
		$rtag=explode(',',$tag);
		$eng=$outcash['eng'][$key];
		$loc=$outcash['loc'][$key];
		$gender=$outcash['gender'][$key];
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
		$ll=-1;
		foreach ($rtag as $item)
		{
			$ll++;
			if ($ll>0)
			{
				$strtag.=', ';
			}
			if ($item!='')
			{
				$strtag.=$mtag[$item];
			}
		}
		//$strtag=mb_substr($strtag,0,mb_strlen($strtag,"UTF-8")-2,"UTF-8");
		if ($isfav==1) $isfav='+';
		else $isfav='-';
		$nastr=$outcash['nastr'][$key];
		/*if ($nastr==1) $nastr='+';
		elseif ($nastr==-1) $nastr='-';
		else $nastr='0';*/
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

		/*if ($nastr==-1) 
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
		}*/
		$nick=$outcash['nick'][$key];
		$login=$outcash['login'][$key];
	    $hn=parse_url($link);
	    $hn=$hn['host'];
	    $ahn=explode('.',$hn);
	    $hn = $ahn[count($ahn)-2].'.'.$ahn[count($ahn)-1];
		$hh = $ahn[count($ahn)-2];
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
		if ((intval(date('H',$time))>0)||(intval(date('i',$time))>0)) $stime=date("H:i:s d.m.Y",$time);
		else $stime=date("d.m.Y",$time);
		$isshow=1;
		$cc=0;

		if (($hn=="facebook.com") || ($hn=="vkontakte.ru"))
		{
			$type_re='социальная сеть';
		}
		else
		if ($hn=="lenta.ru")
		{
			$type_re='новостной ресурс';
		}
		else
		if (($hn=="twitter.com") || ($hn=="rutvit.ru"))
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
		if ($nastr==0) $nstr='';
		elseif ($nastr==1) $nstr='+';
		elseif ($nastr==-1) $nstr='-';
	//----------------------------------------------
		//if ($isshow==1)
		//{
			//$content_r=GetFullPost($link,mb_substr($content,3,mb_strlen($content,"UTF-8")-3,"UTF-8"));
			//echo "|".$link."| |".mb_substr($content,3,mb_strlen($content,"UTF-8")-3,"UTF-8")."| |";//.GetFullPost($link,mb_substr($content,2,mb_strlen($content,"UTF-8"),"UTF-8"))."|<br>";
			/*if ($content_r=="")
			{
				$content_r='*'.$content;
			}*/		
			$content_r=$content;
			//$content_r=rtrim($content_r);
			$content_f='';
			$content_r=preg_replace('[^A-Za-z0-9_]', '', $content_r);
			if ($order['ful_com']=='1')
			{
				$content_f=$fcontent;
				$content_f=preg_replace('[^A-Za-z0-9_]', '', $content_f);
				$fulpretext='Полный текст'."\t";
				$content_f=strip_tags($content_f);	
				$content_f=iconv("UTF-8", "UTF-8//IGNORE", $content_f);
				$content_f=str_replace("\n",'',$content_f);
				//$content=str_replace("\n",'',$content);
				$content_f=str_replace("\t",'',$content_f);
				$content_f=str_replace('','',$content_f);
				$content_f=preg_replace('/ +/i', ' ', $content_f);
				$content_f=preg_replace('/\s/i', ' ', $content_f);
				$tabful="\t";
				/*unset($outmm);
				unset($outmm1);
				foreach ($kword as $ittem)
				{
					if (($ittem!='') && ($ittem!=' '))
					{
						$regex1='/\s(?<str>.{1,100}'.mb_strtolower($ittem,"UTF-8").'.{1,200})[\.\s]/isu';
						$regex='/\.(?<str>.*?'.mb_strtolower($ittem,"UTF-8").'.*?)\./isu';
						preg_match_all($regex,$content_f,$out);
						preg_match_all($regex1,$content_f,$out1);
						foreach ($out['str'] as $kk)
						{
							$outmm[]=$kk;
						}
						foreach ($out1['str'] as $kk)
						{
							$outmm1[]=$kk;
						}
					}
				}
				foreach ($outmm as $itt)
				{
					if ($itt!='')
					{
						$tf=$itt;
						break;
					}
				}
				foreach ($outmm1 as $itt)
				{
					if ($itt!='')
					{
						$tf1=$itt;
						break;
					}
				}
				if ($tf>$tf1)
				{
					$fftext=$tf;
				}
				else
				{
					$fftext=$tf1;
				}
				$tf='';
				$tf1='';*/
				if ($content_f=='')
				{
					$content_f=preg_replace('/\s+/is',' ',$content);
				}
				unset($outmm1);
				unset($outmm);
				//$content_f=$fftext;
				//$fftext='';
				//$content_f=iconv("UTF-8", "UTF-8//IGNORE", $content_f);
				$content_f=htmlentities($content_f,ENT_QUOTES,'UTF-8');
				$content_f=htmlspecialchars_decode($content_f, ENT_NOQUOTES);
				$content_f=preg_replace('/amp;/is','',$content_f);
				$content_f=preg_replace('/laquo;/is','',$content_f);
				$content_f=preg_replace('/nbsp;/is','',$content_f);
				$content_f=preg_replace('/nbsp;/is','',$content_f);
				$content_f=preg_replace('/&/is','',$content_f);
				//if (mb_detect_encoding($content_f, 'auto')=='UTF-8')
				{
					//$content_f=iconv('UTF-8','windows-1251',$content_f);
					//if ($content_f=='')
					{
						//$content_f='&'.$fcontent.'|||||';
					}
					//$content_f.='gg';
				}
				//$content_f.=mb_detect_encoding($content_f, 'auto').mb_detect_encoding($content_f, 'windows-1251');
			}
			else
			{
				$tabful="";
				$content_f='';
				$fulpretext='';
			}
			//$fcontent='';
			if (!in_array($hh,$resy))
			{
				$resy[]=$hh;
			}
			$ckol++;
			if ($_POST['format']=='Excel')
			{
				//echo $content_r."\t";
				preg_match_all("/[а-яА-Я]/is",$content_r,$ouu);
				if ($ouu[0][0]!='')
				{
	        		$csv_output.=$stime."\t".str_replace("\n","",$hn)."\t".iconv("UTF-8","windows-1251",str_replace("\n","",$link))."\t".iconv("UTF-8","windows-1251",$type_re)."\t".$isfav."\t".$isspam."\t".$nastr."\t".$eng.iconv("UTF-8","windows-1251",str_replace("\n","",$nick))."\t".iconv("UTF-8","windows-1251",$gender)."\t".iconv("UTF-8","windows-1251",$age)."\t".iconv("UTF-8","windows-1251",str_replace("\n","",$comm))."\t".iconv('UTF-8','windows-1251',$wobot['destn1'][$loc])."\t".str_replace("\t","",str_replace("\n","",rtrim(iconv("UTF-8","windows-1251",$content_r)))).$tabful.str_replace("\t","",str_replace("\n","",rtrim(iconv("UTF-8","windows-1251","\t".$content_f))))."\t".iconv("UTF-8","windows-1251",$strtag)."\n";
	        	}
	        	else
	        	{
					$content_r=str_replace("\n"," ",$content_r);
					$content_r=str_replace("\t"," ",$content_r);
	        		$csv_output.=$stime."\t".str_replace("\n","",$hn)."\t".str_replace("\n","",$link)."\t".iconv("UTF-8","windows-1251",$type_re)."\t".$isfav."\t".$isspam."\t".$nastr."\t".$eng.iconv("UTF-8","windows-1251",str_replace("\n","",$nick))."\t".iconv("UTF-8","windows-1251",$gender)."\t".iconv("UTF-8","windows-1251",$age)."\t".$comm."\t".iconv('UTF-8','windows-1251',$wobot['destn1'][$loc])."\t".$content_r.(($order['ful_com']=='1')?"\t".$content_f:"").iconv("UTF-8","windows-1251",$strtag)."\n";
	        	}
	        	/*$csv_output.="http://sbm.x0.com/2010/08/0%e6%99%82%e3%81%ae%e3%83%98%e3%83%83%e3%83%89%e3%83%a9%e3%82%a4%e3%83%b3-169/
	\t";*/
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
				$html.='<a href="'.urlencode(/*substr(*/addslashes($link)/*,0,strlen($link)-1)*/).'" target="_blank">'./*urlencode*/($content_r).'</a><br><span class="sl rln">'.($ckol+1).' ('.$stime.')</span><br>';
				//echo '<a href="'.substr($link,0,strlen($link)-1).'" target="_blank">'.htmlentities($content,ENT_COMPAT,'utf-8').'</a><br><span class="sl rln">'.($ckol+1).' ('.date("d.m.Y",$time).')</span><br>';
			}
			elseif ($_POST['format']=='Word')
			{
				$echo_word.="<tr><td>".$stime."</td><td><a href=\"http://www.".$hn."\">".$hn."</a></td><td><a href=".$link.">".$link."</a></td><td>".$type_re."</td><td>".$isfav."</td><td>".$isspam."</td><td>".$nstr."</td>".(($order['order_engage']=='1')?"<td>".$eng."</td>":"")."<td>".$strtag."</td><td>".$nick."</td><td>".$comm."</td><td>".$wobot['destn1'][$loc]."</td><td>".$content_r."</td><td>".$content_f."</td></tr>";
			}
			elseif ($_POST['format']=='OpenOffice')
			{
				$row   = new odsTableRow();
				$row->addCell( new odsTableCellString($stime) );
				$row->addCell( new odsTableCellString($hn) );
				$row->addCell( new odsTableCellStringUrl($link) );
				$row->addCell( new odsTableCellString($type_re) );
				$row->addCell( new odsTableCellString($isfav) );
				$row->addCell( new odsTableCellString($isspam) );
				$row->addCell( new odsTableCellString($nstr) );
				if ($order['order_engage']=='1')
				{
					$row->addCell( new odsTableCellString($eng) );
				}
				$row->addCell( new odsTableCellString($strtag) );
				$row->addCell( new odsTableCellString($nick) );
				$row->addCell( new odsTableCellString($comm) );
				$row->addCell( new odsTableCellString($wobot['destn1'][$loc]) );
				$row->addCell( new odsTableCellString(str_replace("\n","",rtrim($content_r))) );
				if ($order['ful_com']=='1')
				{
					$row->addCell( new odsTableCellString(str_replace("\n","",rtrim($content_f))) );
				}
				$table->addRow($row);
			}
		//}
	}
	if ($_POST['format']=='Excel')
	{
		$csv_output=iconv('UTF-8','windows-1251',('Упоминаний: '.$cnt."\t".'Ресурсов: '.$cnt_host."\t".'Аудитория: '.$metrics['value']."\t".'Цитируемость: '.((intval((1/($othert/250))*100)+intval((1/($metricss['value']/1000))*100))/100)."\t".'Вовлеченность: '.(intval($othert/$metricss['value']*100)/100)."\t".(($order['order_engage']==1)?'Engagement: '.$metrics['engagement']:'')."\t"."\n".'Дата'."\t".'Ресурс'."\t".'Ссылка'."\t".'Тип ресурса'."\t".'Изб'."\t".'Спам'."\t".'Эмо'."\t".$engtext.'Ник'."\t".'Пол'."\t".'Возраст'."\t".'Аудитория'."\t".'Геотаргетинг'."\t".'Текст упоминания'."\t".$fulpretext.'Теги'."\n")).$csv_output;    
		//$csv_output .= "Дата\t Ресурс\t Изб\t Спам\t Эмо\t Ник\t Текст упоминания \n";
     print(/*iconv*/(/*"UTF-8","WINDOWS-1251",*/$csv_output));
     //echo iconv("UTF-8","WINDOWS-1251",$csv_output);
	}
	elseif ($_POST['format']=='pdf')
	{
		$html="<div style=\"font-size: 24px;\">Упоминаний: ".$cnt." Ресурсов: ".$cnt_host." Аудитория: ".$metricss['value']." "." Цитируемость: ".((intval((1/($othert/250))*100)+intval((1/($metricss['value']/1000))*100))/100)." Вовлеченность: ".(intval($othert/$metricss['value']*100)/100)." Доверие: 0</div><br>".$html;
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
	elseif ($_POST['format']=='Word')
	{
		echo "<html>"."<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\">"."<body><table>"."<tr><td>Упоминаний</td><td>Ресурсов</td><td>Аудитория</td><td>Цитируемость</td><td>Вовлеченность</td><td>Доверие</td><td>Engagement</td></tr><tr><td>".$cnt."</td><td>".$cnt_host."</td><td>".$metrics['value']."</td><td>".((intval((1/($othert/250))*100)+intval((1/($metricss['value']/1000))*100))/100)."</td><td>".(intval($othert/$metricss['value']*100)/100)."</td><td>0</td><td>".$metrics['engagement']."</td></tr></table>".$echo_word;
		echo "</table></body>"."</html>";
	}
	elseif ($_POST['format']=='OpenOffice')
	{
		$ods->addTable($table);
		$ods->downloadOdsFile("wobot_".date('h:i:s d.m.Y').".ods");
	}

?>
