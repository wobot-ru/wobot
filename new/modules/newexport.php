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
$res_micr=array('mail.ru','twitter.com','mblogi.qip.ru','rutvit.ru','friendfeed.com','godudu.com','juick.com','jujuju.ru','sports.ru','mylife.ru','chikchirik.ru','chch.ru','f5.ru','zizl.ru','smsnik.com');
$soc_netw=array('1c-club.com','cjclub.ru','diary.ru','facebook.com','myspace.com','orkut.com','vkontakte.ru','stranamam.ru','dietadiary.com','ya.ru');
$nov_res=array('km.ru','regnum.ru','akm.ru','arms-tass.su','annews.ru','itar-tass.com','interfax.ru','interfax-russia.ru','oreanda.ru','1prime.ru','rbc.ru','rbc.ru','ria.ru','rosbalt.ru','tasstelecom.ru','finmarket.ru','expert.ru','newtimes.ru','akzia.ru','aif.ru','argumenti.ru','bg.ru','vedomosti.ru','izvestia.ru','itogi.ru','kommersant.ru','kommersant.ru','kp.ru','mospravda.ru','mn.ru','mk.ru','ng.ru','novayagazeta.ru','newizv.ru','kommersant.ru','politjournal.ru','profile.ru','rbcdaily.ru','gosrf.ru','rodgaz.ru','rg.ru','russianews.ru','senat.org','sobesednik.ru','tribuna.ru','trud.ru','newstube.ru','vesti.ru','mir24.tv','ntv.ru','1tv.ru','rutv.ru','tvkultura.ru','tvc.ru','tvzvezda.ru','5-tv.ru','ren-tv.com','radiovesti.ru','govoritmoskva.ru','ruvr.ru','kommersant.ru','cultradio.ru','radiomayak.ru','radiorus.ru','rusnovosti.ru','msk.ru','infox.ru','lenta.ru','lentacom.ru','newsru.com','temadnya.ru','newsinfo.ru','rb.ru','utronews.ru','moscow-post.ru','apn.ru','argumenti.ru','wek.ru','vz.ru','gazeta.ru','grani.ru','dni.ru','evrazia.org','ej.ru','izbrannoe.ru','inopressa.ru','inosmi.ru','inforos.ru','kommersant.ru','kreml.org','polit.ru','pravda.ru','rabkor.ru','russ.ru','smi.ru','svpressa.ru','segodnia.ru','stoletie.ru','strana.ru','utro.ru','fedpress.ru','lifenews.ru','belrus.ru','pfrf.ru','rosculture.ru','kremlin.ru','gov.ru','rosnedra.com');
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
				$assoc_resorrr[str_replace("_",".",substr($inddd,4))]=1;
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
	$sqw_page4='SELECT * from blog_post as p LEFT JOIN robot_blogs2 AS b ON p.blog_id=b.blog_id WHERE p.order_id="'.intval($_POST['order_id']).'" '.getisshow3().' ORDER BY p.post_engage DESC LIMIT 10';
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
	$sqw_page4='SELECT * from blog_post as p LEFT JOIN robot_blogs2 AS b ON p.blog_id=b.blog_id WHERE p.order_id="'.intval($_POST['order_id']).'" AND p.post_time>'.intval(strtotime($_POST['ntime'])).' AND p.post_time<'.intval(strtotime($_POST['etime'])).' ORDER BY p.post_engage DESC LIMIT 10';
	$countqposts=$db->query('SELECT count(*) as cnt from blog_post as p LEFT JOIN robot_blogs2 AS b ON p.blog_id=b.blog_id WHERE p.order_id="'.intval($_POST['order_id']).'" AND p.post_time>'.intval(strtotime($_POST['ntime'])).' AND p.post_time<'.intval(strtotime($_POST['etime'])).' GROUP BY post_host');
	while ($count=$db->fetch($countqposts))
	{
		$cnt+=$count['cnt'];
		$cnt_host++;
	}
}
	//echo $sqw;
	$qpost=$db->query($sqw); // проверка order_id
	$page4_post=$db->query($sqw_page4);
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
	$kk=0;
	while($pst4 = $db->fetch($page4_post))
	{
		/*$outmas['4'][$kk]['content']=str_replace("\n",'',$pst4['post_content']);
		$outmas['4'][$kk]['src']=$pst4['post_host'];
		$outmas['4'][$kk]['engage']=$pst4['post_engage'];
		$outmas['4'][$kk]['foll']=$pst4['blog_readers'];*/
		$outmas['4'][0][]=str_replace('\"','',str_replace("\n",'',$pst4['post_content']));
		$outmas['4'][1][]=$pst4['post_host'];
		$outmas['4'][2][]=$pst4['post_engage'];
		$outmas['4'][3][]=$pst4['blog_readers'];
		$kk++;
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
		$total_end+=$eng;
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
			if ($mtag[$item]!='')
			{
				$outmas1[mktime(0,0,0,date("n",$time),date("j",$time),date("Y",$time))][$mtag[$item]]++; //tags process page 2
			}
		}
		//$outmas['2']['table']['count'][mktime(0,0,0,date("n",$time),date("j",$time),date("Y",$time))]++;
		//$outmas['2']['table']['count']['date']=++;
		//$strtag=mb_substr($strtag,0,mb_strlen($strtag,"UTF-8")-2,"UTF-8");
		if ($isfav==1) $isfav='+';
		else $isfav='-';
		$nastr=$outcash['nastr'][$key];

		$isspam=$outcash['isspam'][$key];
		if ($isspam==1) $isspam='+';
		else $isspam='-';

		$gn_time_end = microtime(true);
		$gn_time += $gn_time_end - $gn_time_start;

		$nick=$outcash['nick'][$key];
		$uniq_mas[$nick]++;
		$login=$outcash['login'][$key];
	    $hn=parse_url($link);
	    $hn=$hn['host'];
	    $ahn=explode('.',$hn);
	    $hn = $ahn[count($ahn)-2].'.'.$ahn[count($ahn)-1];
		$hh = $ahn[count($ahn)-2];
		$mcount_res[$hn]++;
		$count_top_res[mktime(0,0,0,date("n",$time),date("j",$time),date("Y",$time))][$hn]++;
		if ($nick!='')
		{
			$top_speakers[$nick]['count']++;
			$top_speakers[$nick]['foll']=$comm;
			if ($nastr==1)
			{
				$top_speakers[$nick]['pos']++;
			}
			elseif ($nastr==-1)
			{
				$top_speakers[$nick]['neg']++;
			}
			else
			{
				$top_speakers[$nick]['neu']++;
			}
			$top_speakers[$nick]['src']=$hn;
			if ($hn=='twitter.com')
			{
				$top_speakers[$nick]['link']='http://twitter.com/'.$login;
			}
			else
			if ($hn=='livejournal.com')
			{
				$top_speakers[$nick]['link']='http://'.$login.'.livejournal.com';
			}
			else
			if ($hn=='vkontakte.ru')
			{
				$top_speakers[$nick]['link']='http://vkontakte.ru/id'.$login;
			}
			else
			if ($hn=='facebook.com')
			{
				$top_speakers[$nick]['link']='http://facebook.com/'.$login;
			}
		}

		
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

		if (isset($soc_netw[$hn]))
		{
			$type_re='социальная сеть';
		}
		else
		if (isset($nov_res[$hn]))
		{
			$type_re='новостной ресурс';
		}
		else
		if (isset($res_micr[$hn]))
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
			$outmas['header']['logo']='/var/www/img.png';
			$outmas['header']['contacts']='e-mail:  bma@wobot.ru    phone: +7 (901) 556-06-44';

			$outmas['1']['order_name']=$order['order_name'];
			$outmas['1']['order_time']=date('d:m:Y',$order['order_start']).'-'.(($order['order_end']==0)?date('d:m:Y',$order['order_last']):date('d:m:Y',$order['order_end']));
			$outmas['1']['order_keyword']=$order['order_keyword'];
			
			$outmas['2']['count_posts']=$cnt;
			$outmas['2']['uniq_auth']=count($uniq_mas);
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
			if (!isset($mas_value[$nick]))
			{
				$value_count+=$comm;
				$mas_value[$nick]=1;
			}
			$outmas['2']['post_in_day']=$midle_cnt;
			$outmas['2']['count_hosts']=$cnt_host;
			$outmas['2']['audience']=$value_count;
			$outmas['2']['period']=$_POST['sd'].'-'.$_POST['ed'];
			
			//$time_mas[$hn][]

			$outmas['3'][0][]=$stime;
			$outmas['3'][1][]=$hn;
			$outmas['3'][2][]=$link;
			$outmas['3'][3][]=$type_re;
			$outmas['3'][4][]=preg_replace('/[^а-яА-Яa-zA-Z\,\.\-\=\+\/\"\'\^\#\$\@\!\(\)]/is','',str_replace('\"','',str_replace("\n",'',addslashes((($content_f=='')?rtrim($content):rtrim($content_f))))));
			$outmas['3'][5][]=$nstr;
			$outmas['3'][6][]=$isspam;
			$outmas['3'][7][]=$isfav;
			$outmas['3'][8][]=intval($eng);
			$outmas['3'][9][]=$nick;
			$outmas['3'][10][]=$gender;
			$outmas['3'][11][]=$age;
			$outmas['3'][12][]=$comm;
			$outmas['3'][13][]=$wobot['destn1'][$loc];
			$outmas['3'][14][]=$strtag;

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

			if ($nastr==1)
			{
				$count_pos++;
				$din_nastr[mktime(0,0,0,date("n",$time),date("j",$time),date("Y",$time))]['pos']++;
				$din_nastr[mktime(0,0,0,date("n",$time),date("j",$time),date("Y",$time))]['neg']=intval($din_nastr[mktime(0,0,0,date("n",$time),date("j",$time),date("Y",$time))]['neg']);
				$din_nastr[mktime(0,0,0,date("n",$time),date("j",$time),date("Y",$time))]['neu']=intval($din_nastr[mktime(0,0,0,date("n",$time),date("j",$time),date("Y",$time))]['neu']);
				$top_pos['time'][]=$time;
				$top_pos['src'][]=$hn;
				$top_pos['link'][]=$link;
				$top_pos['type'][]=$type_re;
				$top_pos['content'][]=str_replace("\n",'',addslashes((($content_f=='')?rtrim($content):rtrim($content_f))));
				$top_pos['eng'][]=intval($eng);
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
				$count_neg++;
				$din_nastr[mktime(0,0,0,date("n",$time),date("j",$time),date("Y",$time))]['neg']++;
				$din_nastr[mktime(0,0,0,date("n",$time),date("j",$time),date("Y",$time))]['pos']=intval($din_nastr[mktime(0,0,0,date("n",$time),date("j",$time),date("Y",$time))]['pos']);
				$din_nastr[mktime(0,0,0,date("n",$time),date("j",$time),date("Y",$time))]['neu']=intval($din_nastr[mktime(0,0,0,date("n",$time),date("j",$time),date("Y",$time))]['neu']);
				$top_neg['time'][]=$time;
				$top_neg['src'][]=$hn;
				$top_neg['link'][]=$link;
				$top_neg['type'][]=$type_re;
				$top_neg['content'][]=str_replace("\n",'',addslashes((($content_f=='')?rtrim($content):rtrim($content_f))));
				$top_neg['eng'][]=intval($eng);
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
				$count_neu++;
				$din_nastr[mktime(0,0,0,date("n",$time),date("j",$time),date("Y",$time))]['neu']++;
				$din_nastr[mktime(0,0,0,date("n",$time),date("j",$time),date("Y",$time))]['pos']=intval($din_nastr[mktime(0,0,0,date("n",$time),date("j",$time),date("Y",$time))]['pos']);
				$din_nastr[mktime(0,0,0,date("n",$time),date("j",$time),date("Y",$time))]['neg']=intval($din_nastr[mktime(0,0,0,date("n",$time),date("j",$time),date("Y",$time))]['neg']);
			}
			if ($gender=='М')
			{
				$out_gen['М']['count']++;
				if ($nastr==1)
				{
					$out_gen['М']['pos']++;
				}
				elseif ($nastr==-1)
				{
					$out_gen['М']['neg']++;
				}
				else
				{
					$out_gen['М']['neu']++;
				}
			}
			elseif ($gender=='Ж')
			{
				$out_gen['Ж']['count']++;
				if ($nastr==1)
				{
					$out_gen['Ж']['pos']++;
				}
				elseif ($nastr==-1)
				{
					$out_gen['Ж']['neg']++;
				}
				else
				{
					$out_gen['Ж']['neu']++;
				}
			}
			if ($age!=0)
			{
				switch ($age)
				{
					case (($age>=9) && ($age<=14)):
					{
						$out_age['9-14']['count']++;
						if ($nastr==1)
						{
							$out_age['9-14']['pos']++;
						}
						else
						if ($nastr==-1)
						{
							$out_age['9-14']['neg']++;
						}
						else
						if ($nastr==0)
						{
							$out_age['9-14']['neu']++;
						}
					}
					break;
					case (($age>=15) && ($age<=20)):
					{
						$out_age['15-20']['count']++;
						if ($nastr==1)
						{
							$out_age['15-20']['pos']++;
						}
						else
						if ($nastr==-1)
						{
							$out_age['15-20']['neg']++;
						}
						else
						if ($nastr==0)
						{
							$out_age['15-20']['neu']++;
						}
					}
					break;
					case (($age>=21) && ($age<=26)):
					{
						$out_age['21-26']['count']++;
						if ($nastr==1)
						{
							$out_age['21-26']['pos']++;
						}
						else
						if ($nastr==-1)
						{
							$out_age['21-26']['neg']++;
						}
						else
						if ($nastr==0)
						{
							$out_age['21-26']['neu']++;
						}
					}
					break;
					case (($age>=27) && ($age<=32)):
					{
						$out_age['27-32']['count']++;
						if ($nastr==1)
						{
							$out_age['27-32']['pos']++;
						}
						else
						if ($nastr==-1)
						{
							$out_age['27-32']['neg']++;
						}
						else
						if ($nastr==0)
						{
							$out_age['27-32']['neu']++;
						}
					}
					break;
					case (($age>=33) && ($age<=38)):
					{
						$out_age['33-38']['count']++;
						if ($nastr==1)
						{
							$out_age['33-38']['pos']++;
						}
						else
						if ($nastr==-1)
						{
							$out_age['33-38']['neg']++;
						}
						else
						if ($nastr==0)
						{
							$out_age['33-38']['neu']++;
						}
					}
					break;
					case (($age>39) && ($age<=44)):
					{
						$out_age['39-44']['count']++;
						if ($nastr==1)
						{
							$out_age['39-44']['pos']++;
						}
						else
						if ($nastr==-1)
						{
							$out_age['39-44']['neg']++;
						}
						else
						if ($nastr==0)
						{
							$out_age['39-44']['neu']++;
						}
					}
					break;
					case (($age>=45) && ($age<=50)):
					{
						$out_age['45-50']['count']++;
						if ($nastr==1)
						{
							$out_age['45-50']['pos']++;
						}
						else
						if ($nastr==-1)
						{
							$out_age['45-50']['neg']++;
						}
						else
						if ($nastr==0)
						{
							$out_age['45-50']['neu']++;
						}
					}
					break;
					case (($age>=51) && ($age<=56)):
					{
						$out_age['51-56']['count']++;
						if ($nastr==1)
						{
							$out_age['51-56']['pos']++;
						}
						else
						if ($nastr==-1)
						{
							$out_age['51-56']['neg']++;
						}
						else
						if ($nastr==0)
						{
							$out_age['51-56']['neu']++;
						}
					}
					break;
					case (($age>=57) && ($age<=62)):
					{
						$out_age['57-62']['count']++;
						if ($nastr==1)
						{
							$out_age['57-62']['pos']++;
						}
						else
						if ($nastr==-1)
						{
							$out_age['57-62']['neg']++;
						}
						else
						if ($nastr==0)
						{
							$out_age['57-62']['neu']++;
						}
					}
					break;
					case (($age>=63) && ($age<=68)):
					{
						$out_age['63-68']['count']++;
						if ($nastr==1)
						{
							$out_age['63-68']['pos']++;
						}
						else
						if ($nastr==-1)
						{
							$out_age['63-68']['neg']++;
						}
						else
						if ($nastr==0)
						{
							$out_age['63-68']['neu']++;
						}
					}
					break;
				}
			}			
			
				/*$row   = new odsTableRow();
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
				$table->addRow($row);*/
		
	}
	//$outmas['8']['top_positive']=$out_top_pos_post;
	//$outmas['8']['top_negative']=$out_top_neg_post;
	foreach ($din_nastr as $key => $item)
	{
		if ((intval(date('H',$key))>0)||(intval(date('i',$key))>0)) $key_mas_din=date("H:i:s d.m.Y",$key);
		else $key_mas_din=date("d.m.Y",$key);
		$mas_din_8[0][]=mktime(0,0,0,date("n",$key),date("j",$key),date("Y",$key));
		$mas_din_8[1][]=$item['neu'];
		$mas_din_8[2][]=$item['neg'];
		$mas_din_8[3][]=$item['pos'];
	}
	$outmas['8']['dinams']=$mas_din_8;
	$outmas['8']['all']['count_pos']=$count_pos;
	$outmas['8']['all']['count_neg']=$count_neg;
	$outmas['8']['all']['count_neu']=$count_neu;
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
	for($t=strtotime($_POST['sd']);$t<=strtotime($_POST['ed']);$t=mktime(0,0,0,date("n",$t),date("j",$t)+1,date("Y",$t)))
	{
		//$outmas['2']['graph'][mktime(0,0,0,date("n",$t),date("j",$t),date("Y",$t))]['count']=intval($timet[date('Y',$t)][date('n',$t)][date('j',$t)]);
		$outmas['2']['graph'][mktime(0,0,0,date("n",$t),date("j",$t),date("Y",$t))]['count']=0;
		foreach ($count_top_res[mktime(0,0,0,date("n",$t),date("j",$t),date("Y",$t))] as $keyy => $itemm)
		{
			//echo $itemm.' ';
			$outmas['2']['graph'][mktime(0,0,0,date("n",$t),date("j",$t),date("Y",$t))]['count']+=$itemm;
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
		$outmas['2']['graph'][mktime(0,0,0,date("n",$t),date("j",$t),date("Y",$t))]['tags']=$strtags;
		if (!isset($din_nastr[mktime(0,0,0,date("n",$t),date("j",$t),date("Y",$t))]))
		{
			//echo mktime(0,0,0,date("n",$t),date("j",$t),date("Y",$t)).' ';
			/*$outmas['8']['dinams'][mktime(0,0,0,date("n",$t),date("j",$t),date("Y",$t))]['neu']=0;
			$outmas['8']['dinams'][mktime(0,0,0,date("n",$t),date("j",$t),date("Y",$t))]['neg']=0;
			$outmas['8']['dinams'][mktime(0,0,0,date("n",$t),date("j",$t),date("Y",$t))]['pos']=0;*/
			if ((intval(date('H',$t))>0)||(intval(date('i',$t))>0)) $stime_key_t=date("H:i:s d.m.Y",$t);
			else $stime_key_t=date("d.m.Y",$t);
			$outmas['8']['dinams'][0][]=mktime(0,0,0,date("n",$t),date("j",$t),date("Y",$t));
			$outmas['8']['dinams'][1][]=0;
			$outmas['8']['dinams'][2][]=0;
			$outmas['8']['dinams'][3][]=0;
		}
	}
	array_multisort($outmas['8']['dinams'][0],SORT_DESC,$outmas['8']['dinams'][1],SORT_DESC,$outmas['8']['dinams'][2],SORT_DESC,$outmas['8']['dinams'][3]);
	foreach ($outmas['8']['dinams'][0] as $key => $item)
	{
		if ((intval(date('H',$item))>0)||(intval(date('i',$item))>0)) $stime_key_t1=date("H:i:s d.m.Y",$item);
		else $stime_key_t1=date("d.m.Y",$item);
		$outmas['8']['dinams'][0][$key]=$stime_key_t1;
	}
	$graph_2=$outmas[2]['graph'];
	unset($outmas[2]['graph']);
	$i=0;
	foreach ($graph_2 as $key => $item)
	{
		if ((intval(date('H',$key))>0)||(intval(date('i',$key))>0)) $stime_key=date("H:i:s d.m.Y",$key);
		else $stime_key=date("d.m.Y",$key);
		$outmas['2']['graph'][0][]=$stime_key;
		$outmas['2']['graph'][1][]=$item['count'];
		$outmas['2']['graph'][2][]=$item['tags'];	
		$i++;
	}
	//array_multisort($outmas['3']['engage'],SORT_DESC,$outmas['content'],SORT_DESC,$outmas['foll'],SORT_DESC,$outmas['src'],SORT_DESC);
	$outmas['2']['engage']=intval($total_end);
	arsort($mcount_res);
	//print_r($mcount_res);
	$i=0;
	foreach ($mcount_res as $key => $item)
	{
		//$outmas['5']['count'][$key]=$item;
		$outmas['5']['count'][0][]=$key;
		$outmas['5']['count'][1][]=$item;
		if (intval($item/$cnt*100)>=1)
		{
			//$outmas['5']['proc'][$key]=intval($item/$cnt*100);
			$outmas['5']['proc'][0][]=$key;
			$outmas['5']['proc'][1][]=intval($item/$cnt*100);
		}
		else
		{
			$other_rs+=$item;
		}
		if ($i<5)
		{
			foreach ($count_top_res as $kk => $ii)
			{
				if ((intval(date('H',$kk))>0)||(intval(date('i',$kk))>0)) $kk_time=date("H:i:s d.m.Y",$kk);
				else $kk_time=date("d.m.Y",$kk);
				$outmas['5']['top_count'][0][]=$kk_time;
				$outmas['5']['top_count'][1][]=$key;
				$outmas['5']['top_count'][2][]=$ii[$key];
				//$outmas['5']['top_count'][$kk][$key]=$ii[$key];
			}
		}
		$i++;
	}
	//print_r($count_top_res);
	//$outmas['5']['proc']['другие']=intval($other_rs/$cnt*100);
	$outmas['5']['proc'][0][]='другие';
	$outmas['5']['proc'][1][]=intval($other_rs/$cnt*100);
	foreach ($top_speakers as $key => $item)
	{
		/*$top_speak[$item['count']]['nick']=$key;
		$top_speak[$item['count']]['neg']=$item['neg'];
		$top_speak[$item['count']]['pos']=$item['pos'];
		$top_speak[$item['count']]['neu']=$item['neu'];
		$top_speak[$item['count']]['foll']=$item['foll'];
		$top_speak[$item['count']]['src']=$item['src'];*/
		$top_speak['nick'][]=$key;
		$top_speak['neg'][]=$item['neg'];
		$top_speak['pos'][]=$item['pos'];
		$top_speak['neu'][]=$item['neu'];
		$top_speak['foll'][]=$item['foll'];
		$top_speak['src'][]=$item['src'];
		$top_speak['count'][]=$item['count'];
		$top_speak['link'][]=$item['link'];
	}
	$top_prom=$top_speak;
	array_multisort($top_speak['count'],SORT_DESC,$top_speak['nick'],SORT_DESC,$top_speak['pos'],SORT_DESC,$top_speak['neg'],SORT_DESC,$top_speak['neu'],SORT_DESC,$top_speak['foll'],SORT_DESC,$top_speak['src'],SORT_DESC,$top_speak['link'],SORT_DESC);
	foreach ($top_speak['count'] as $key => $item)
	{
		/*$t_speak[$top_speak['nick'][$key]]['count']=$top_speak['count'][$key];
		$t_speak[$top_speak['nick'][$key]]['neg']=intval($top_speak['neg'][$key]);
		$t_speak[$top_speak['nick'][$key]]['pos']=intval($top_speak['pos'][$key]);
		$t_speak[$top_speak['nick'][$key]]['neu']=intval($top_speak['neu'][$key]);
		$t_speak[$top_speak['nick'][$key]]['foll']=intval($top_speak['foll'][$key]);
		$t_speak[$top_speak['nick'][$key]]['src']=$top_speak['src'][$key];
		$t_speak[$top_speak['nick'][$key]]['link']=$top_speak['link'][$key];*/
		$t_speak[0][]=$top_speak['nick'][$key];
		$t_speak[1][]=intval($top_speak['foll'][$key]);
		$t_speak[2][]=intval($top_speak['neg'][$key])+intval($top_speak['pos'][$key])+intval($top_speak['neu'][$key]);
		$t_speak[3][]=intval($top_speak['neg'][$key]);
		$t_speak[4][]=intval($top_speak['pos'][$key]);
		$t_speak[5][]=intval($top_speak['neu'][$key]);
		$t_speak[6][]=$top_speak['src'][$key];
		//$t_speak[7][]=$top_speak['link'][$key];
	}
	array_multisort($top_prom['foll'],SORT_DESC,$top_prom['count'],SORT_DESC,$top_prom['nick'],SORT_DESC,$top_prom['pos'],SORT_DESC,$top_prom['neg'],SORT_DESC,$top_prom['neu'],SORT_DESC,$top_prom['src'],SORT_DESC,$top_prom['link'],SORT_DESC);
	array_multisort($top_pos['foll'],SORT_DESC,$top_pos['time'],SORT_DESC,$top_pos['src'],SORT_DESC,$top_pos['link'],SORT_DESC,$top_pos['type'],SORT_DESC,$top_pos['content'],SORT_DESC,$top_pos['eng'],SORT_DESC,$top_pos['nick'],SORT_DESC,$top_pos['gen'],SORT_DESC,$top_pos['age'],SORT_DESC,$top_pos['loc'],SORT_DESC,$top_pos['tags'],SORT_DESC);
	array_multisort($top_neg['foll'],SORT_DESC,$top_neg['time'],SORT_DESC,$top_neg['src'],SORT_DESC,$top_neg['link'],SORT_DESC,$top_neg['type'],SORT_DESC,$top_neg['content'],SORT_DESC,$top_neg['eng'],SORT_DESC,$top_neg['nick'],SORT_DESC,$top_neg['gen'],SORT_DESC,$top_neg['age'],SORT_DESC,$top_neg['loc'],SORT_DESC,$top_neg['tags'],SORT_DESC);
	$i=0;
	//print_r($top_neg['foll']);
	foreach ($top_pos['foll'] as $key => $item)
	{
		if ($i<10)
		{
			/*$out_top_pos_post[$i]['time']=$top_pos['time'][$key];
			$out_top_pos_post[$i]['src']=$top_pos['src'][$key];
			$out_top_pos_post[$i]['link']=$top_pos['link'][$key];
			$out_top_pos_post[$i]['type']=$top_pos['type'][$key];
			$out_top_pos_post[$i]['content']=$top_pos['content'][$key];
			$out_top_pos_post[$i]['eng']=$top_pos['eng'][$key];
			$out_top_pos_post[$i]['nick']=$top_pos['nick'][$key];
			$out_top_pos_post[$i]['gen']=$top_pos['gen'][$key];
			$out_top_pos_post[$i]['foll']=$top_pos['foll'][$key];
			$out_top_pos_post[$i]['loc']=$top_pos['loc'][$key];
			$out_top_pos_post[$i]['tags']=$top_pos['tags'][$key];*/
			$kkk=$top_pos['time'][$key];
			if ((intval(date('H',$kkk))>0)||(intval(date('i',$kkk))>0)) $kkk_time=date("H:i:s d.m.Y",$kkk);
			else $kkk_time=date("d.m.Y",$kkk);
			$out_top_pos_post[0][]=$kkk_time;
			$out_top_pos_post[1][]=$top_pos['src'][$key];
			$out_top_pos_post[2][]=$top_pos['link'][$key];
			$out_top_pos_post[3][]=$top_pos['type'][$key];
			$out_top_pos_post[4][]=$top_pos['content'][$key];
			$out_top_pos_post[5][]=$top_pos['eng'][$key];
			$out_top_pos_post[6][]=$top_pos['nick'][$key];
			$out_top_pos_post[7][]=$top_pos['gen'][$key];
			$out_top_pos_post[8][]=$top_pos['foll'][$key];
			$out_top_pos_post[9][]=$top_pos['loc'][$key];
			$out_top_pos_post[10][]=$top_pos['tags'][$key];
			
		}
		$i++;
	}
	$i=0;
	foreach ($top_neg['foll'] as $key => $item)
	{
		if ($i<10)
		{
			$kkk=$top_neg['time'][$key];
			if ((intval(date('H',$kkk))>0)||(intval(date('i',$kkk))>0)) $kkk_time=date("H:i:s d.m.Y",$kkk);
			else $kkk_time=date("d.m.Y",$kkk);
			$out_top_neg_post[0][]=$kkk_time;
			$out_top_neg_post[1][]=$top_neg['src'][$key];
			$out_top_neg_post[2][]=$top_neg['link'][$key];
			$out_top_neg_post[3][]=$top_neg['type'][$key];
			$out_top_neg_post[4][]=$top_neg['content'][$key];
			$out_top_neg_post[5][]=$top_neg['eng'][$key];
			$out_top_neg_post[6][]=$top_neg['nick'][$key];
			$out_top_neg_post[7][]=$top_neg['gen'][$key];
			$out_top_neg_post[8][]=$top_neg['foll'][$key];
			$out_top_neg_post[9][]=$top_neg['loc'][$key];
			$out_top_neg_post[10][]=$top_neg['tags'][$key];
		}
		$i++;
	}
	$outmas['8']['top_positive']=$out_top_pos_post;
	$outmas['8']['top_negative']=$out_top_neg_post;
	
	foreach ($top_prom['count'] as $key => $item)
	{
		/*$t_prom[$top_prom['nick'][$key]]['count']=$top_prom['count'][$key];
		$t_prom[$top_prom['nick'][$key]]['neg']=intval($top_prom['neg'][$key]);
		$t_prom[$top_prom['nick'][$key]]['pos']=intval($top_prom['pos'][$key]);
		$t_prom[$top_prom['nick'][$key]]['neu']=intval($top_prom['neu'][$key]);
		$t_prom[$top_prom['nick'][$key]]['foll']=intval($top_prom['foll'][$key]);
		$t_prom[$top_prom['nick'][$key]]['src']=$top_prom['src'][$key];
		$t_prom[$top_prom['nick'][$key]]['link']=$top_prom['link'][$key];*/

		$t_prom[0][]=$top_prom['nick'][$key];
		$t_prom[1][]=intval($top_prom['foll'][$key]);
		$t_prom[2][]=$top_prom['count'][$key];
		$t_prom[3][]=intval($top_prom['neg'][$key]);
		$t_prom[4][]=intval($top_prom['pos'][$key]);
		$t_prom[5][]=intval($top_prom['neu'][$key]);
		$t_prom[6][]=$top_prom['src'][$key];
		//$t_prom[7][]=$top_prom['link'][$key];

	}
	foreach ($top_loc as $key => $item)
	{
		$top_loc_tosort['loc'][]=$key;
		$top_loc_tosort['pos'][]=$item['pos'];
		$top_loc_tosort['neg'][]=$item['neg'];
		$top_loc_tosort['neu'][]=$item['neu'];
		$top_loc_tosort['count'][]=$item['count'];
	}
	array_multisort($top_loc_tosort['count'],SORT_DESC,$top_loc_tosort['pos'],SORT_DESC,$top_loc_tosort['neg'],SORT_DESC,$top_loc_tosort['neu'],SORT_DESC,$top_loc_tosort['loc'],SORT_DESC);
	foreach ($top_loc_tosort['count'] as $key => $item)
	{
		/*$outm_loc[$top_loc_tosort['loc'][$key]]['count']=$item;
		$outm_loc[$top_loc_tosort['loc'][$key]]['pos']=$top_loc_tosort['pos'][$key];
		$outm_loc[$top_loc_tosort['loc'][$key]]['neg']=$top_loc_tosort['neg'][$key];
		$outm_loc[$top_loc_tosort['loc'][$key]]['neu']=$top_loc_tosort['neu'][$key];*/
		$outm_loc[0][]=$top_loc_tosort['loc'][$key];
		$outm_loc[1][]=$item;
		$outm_loc[2][]=$top_loc_tosort['pos'][$key];
		$outm_loc[3][]=$top_loc_tosort['neg'][$key];
		$outm_loc[4][]=$top_loc_tosort['neu'][$key];
	}
	//ksort($top_speak);
	$outmas['6']=$t_speak;
	$outmas['7']=$t_prom;
	$outmas['9']=$outm_loc;
	foreach ($out_age as $key => $item)
	{
		$out_age1[0][]=$key;
		$out_age1[1][]=intval($item['count']);
		$out_age1[2][]=intval($item['pos']);
		$out_age1[3][]=intval($item['neu']);
		$out_age1[4][]=intval($item['neg']);
	}
	foreach ($out_gen as $key => $item)
	{
		$out_gen1[0][]=$key;
		$out_gen1[1][]=intval($item['count']);
		$out_gen1[2][]=intval($item['pos']);
		$out_gen1[3][]=intval($item['neu']);
		$out_gen1[4][]=intval($item['neg']);
	}
	$outmas['10']['age']=$out_age1;
	$outmas['10']['gen']=$out_gen1;
	//print_r($outmas1);
	//echo json_encode($outmas);
	$descriptorspec=array(
		0 => array("pipe","r"),
		1 => array("pipe","w"),
		2 => array("file", "/tmp/error-output.txt", "a")
		);

	$cwd='/var/www/new/modules';
	$end=array('');
	//$pipes=json_encode($outmas);
	$process=proc_open('perl /var/www/project/excel/excel.pl',$descriptorspec,$pipes,$cwd,$end);
	//echo "\n".$row['post_link']."\n";
	if (is_resource($process))
	{
		fwrite($pipes[0], json_encode($outmas));
		fclose($pipes[0]);
		//echo $return_value;
		//print_r($pipes);
		$fulltext=stream_get_contents($pipes[1]);
		$return_value=proc_close($process);

		header("Content-type: application/vnd.ms-excel");
		header("Content-Disposition: attachment; filename=wobot-export.xls");
		
		echo $fulltext;
		//echo (stream_get_contents($pipes[1]);
	}
	//sleep(1);
//print_r($outmas);
//print_r($timet);
?>
