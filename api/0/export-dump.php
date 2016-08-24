<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
//print_r($_POST);
//die();
require_once('/var/www/new/com/config.php');
require_once('/var/www/new/com/func.php');
require_once('/var/www/new/com/db.php');
require_once('/var/www/new/bot/kernel.php');
require_once('auth.php');
require_once('func_export.php');
require_once('/var/www/com/loc.php');
require_once('/var/www/new/modules/ods.php');
require_once('/var/www/new/modules/tcpdf/config/lang/rus.php');
require_once('/var/www/new/modules/tcpdf/tcpdf.php');
require_once('/var/www/new/com/porter.php');

$word_stem=new Lingua_Stem_Ru();

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

auth();
if (!$loged) die();
if ($user['tariff_id']==3)
{
	$user['user_id']=61;
}
//print_r($user);
$db = new database();
$db->connect();
//print_r($_POST);
//echo 'not loged';
$res_micr=array('mail.ru','twitter.com','mblogi.qip.ru','rutvit.ru','friendfeed.com','godudu.com','juick.com','jujuju.ru','sports.ru','mylife.ru','chikchirik.ru','chch.ru','f5.ru','zizl.ru','smsnik.com');
$soc_netw=array('1c-club.com','cjclub.ru','diary.ru','facebook.com','myspace.com','orkut.com','vkontakte.ru','stranamam.ru','dietadiary.com','ya.ru','vk.com');
$nov_res=array('km.ru','regnum.ru','akm.ru','arms-tass.su','annews.ru','itar-tass.com','interfax.ru','interfax-russia.ru','oreanda.ru','1prime.ru','rbc.ru','rbc.ru','ria.ru','rosbalt.ru','tasstelecom.ru','finmarket.ru','expert.ru','newtimes.ru','akzia.ru','aif.ru','argumenti.ru','bg.ru','vedomosti.ru','izvestia.ru','itogi.ru','kommersant.ru','kommersant.ru','kp.ru','mospravda.ru','mn.ru','mk.ru','ng.ru','novayagazeta.ru','newizv.ru','kommersant.ru','politjournal.ru','profile.ru','rbcdaily.ru','gosrf.ru','rodgaz.ru','rg.ru','russianews.ru','senat.org','sobesednik.ru','tribuna.ru','trud.ru','newstube.ru','vesti.ru','mir24.tv','ntv.ru','1tv.ru','rutv.ru','tvkultura.ru','tvc.ru','tvzvezda.ru','5-tv.ru','ren-tv.com','radiovesti.ru','govoritmoskva.ru','ruvr.ru','kommersant.ru','cultradio.ru','radiomayak.ru','radiorus.ru','rusnovosti.ru','msk.ru','infox.ru','lenta.ru','lentacom.ru','newsru.com','temadnya.ru','newsinfo.ru','rb.ru','utronews.ru','moscow-post.ru','apn.ru','argumenti.ru','wek.ru','vz.ru','gazeta.ru','grani.ru','dni.ru','evrazia.org','ej.ru','izbrannoe.ru','inopressa.ru','inosmi.ru','inforos.ru','kommersant.ru','kreml.org','polit.ru','pravda.ru','rabkor.ru','russ.ru','smi.ru','svpressa.ru','segodnia.ru','stoletie.ru','strana.ru','utro.ru','fedpress.ru','lifenews.ru','belrus.ru','pfrf.ru','rosculture.ru','kremlin.ru','gov.ru','rosnedra.com');
//auth();
//if (!$loged) die();

//$_POST['order_id']=309;
//$_POST['stime']='04.04.2011';
//$_POST['etime']='11.04.2011';
//print_r($_POST);
//$_POST=$_GET;
if (intval($_POST['order_id'])==0) die();
//echo 'gg';
//print_r($_SESSION);
//echo "SELECT * from blog_orders WHERE order_id=".intval($_POST['order_id'])." and user_id=".intval($user['user_id'])." LIMIT 1";
$res=$db->query("SELECT * from blog_orders WHERE order_id=".intval($_POST['order_id'])." and user_id=".intval($user['user_id'])." LIMIT 1");
$order=$db->fetch($res);
//print_r($order);
if ($order['order_end']==0)
{
	$order['order_end']=$order['order_last'];
}
$src=$order['order_src'];
$src=json_decode($src,true);
$data=$order['order_metrics'];
$metrics=json_decode($data,true);
//print_r($metrics['topwords']);
//print_r($metrics['location']);
$type_text="";
$query1='SELECT * FROM blog_tag WHERE user_id='.intval($user['user_id']);
   $respost1=$db->query($query1);
while($tgl1 = $db->fetch($respost1))
{
	$tagsall[$tgl1['tag_tag']]=$tgl1['tag_name'];
	$tagsallrev[$tgl1['tag_name']]=$tgl1['tag_tag'];
	$mtag[$tgl1['tag_tag']]=$tgl1['tag_name'];
}
$qqq=get_query();
//print_r($_POST);

//echo $qqq; die();
$qqq1=preg_replace('/SELECT \* FROM/is','SELECT count(*) as cnt FROM',$qqq);
$qqq1=preg_replace('/ORDER BY p\.post_time DESC/is','GROUP BY post_host',$qqq1);
$qqq2=preg_replace('/ORDER BY p\.post_time DESC/is','ORDER BY p.post_engage DESC LIMIT 10',$qqq);
//echo '<br>'.$qqq2.'<br>';
//die();
//echo $qqq1;
if ((intval(strtotime($_POST['ntime']))==0) && (intval(strtotime($_POST['etime']))==0))
{
	$countqposts=$db->query('SELECT count(*) as cnt from blog_post as p LEFT JOIN robot_blogs2 AS b ON p.blog_id=b.blog_id WHERE p.order_id="'.intval($_POST['order_id']).'" AND p.post_time>'.(intval($order['order_start'])-86400).' AND p.post_time<'.intval($order['order_end']+86400).' GROUP BY post_host');
}
else
{
	$countqposts=$db->query('SELECT count(*) as cnt from blog_post as p LEFT JOIN robot_blogs2 AS b ON p.blog_id=b.blog_id WHERE p.order_id="'.intval($_POST['order_id']).'" AND p.post_time>'.intval(strtotime($_POST['ntime'])).' AND p.post_time<'.intval(strtotime($_POST['etime'])+86400).' GROUP BY post_host');
}
//echo 'SELECT count(*) as cnt from blog_post as p LEFT JOIN robot_blogs2 AS b ON p.blog_id=b.blog_id WHERE p.order_id="'.intval($_POST['order_id']).'" AND p.post_time>'.intval(strtotime($_POST['ntime'])).' AND p.post_time<'.intval(strtotime($_POST['etime'])+86400).' GROUP BY post_host';
while ($count=$db->fetch($countqposts))
{
	$cnt+=$count['cnt'];
	$cnt_host++;
}


$out_age['9-14']['pos']=0;
$out_age['9-14']['neg']=0;
$out_age['9-14']['neu']=0;
$out_age['9-14']['count']=0;
$out_age['15-20']['pos']=0;
$out_age['15-20']['neg']=0;
$out_age['15-20']['neu']=0;
$out_age['15-20']['count']=0;
$out_age['21-26']['pos']=0;
$out_age['21-26']['neg']=0;
$out_age['21-26']['neu']=0;
$out_age['21-26']['count']=0;
$out_age['27-32']['pos']=0;
$out_age['27-32']['neg']=0;
$out_age['27-32']['neu']=0;
$out_age['27-32']['count']=0;
$out_age['33-38']['pos']=0;
$out_age['33-38']['neg']=0;
$out_age['33-38']['neu']=0;
$out_age['33-38']['count']=0;
$out_age['39-44']['pos']=0;
$out_age['39-44']['neg']=0;
$out_age['39-44']['neu']=0;
$out_age['39-44']['count']=0;
$out_age['45-50']['pos']=0;
$out_age['45-50']['neg']=0;
$out_age['45-50']['neu']=0;
$out_age['45-50']['count']=0;
$out_age['51-56']['pos']=0;
$out_age['51-56']['neg']=0;
$out_age['51-56']['neu']=0;
$out_age['51-56']['count']=0;
$out_age['57-62']['pos']=0;
$out_age['57-62']['neg']=0;
$out_age['57-62']['neu']=0;
$out_age['57-62']['count']=0;
	//echo $sqw;
	//print_r($word);
	//die();
	//print_r($_POST);
	$qpost=$db->query($qqq); // запрос на все посты $sqw='SELECT * from blog_post as p LEFT JOIN robot_blogs2 AS b ON p.blog_id=b.blog_id WHERE p.order_id="'.intval($_POST['order_id']).'" '.getisshow3().' ORDER BY p.post_time DESC';
	$page4_post=$db->query($qqq2); // $sqw_page4='SELECT * from blog_post as p LEFT JOIN robot_blogs2 AS b ON p.blog_id=b.blog_id WHERE p.order_id="'.intval($_POST['order_id']).'" '.getisshow3().' ORDER BY p.post_engage DESC LIMIT 10';
	//$countqposts=$db->query('SELECT count(*) as cnt from blog_post as p LEFT JOIN robot_blogs2 AS b ON p.blog_id=b.blog_id WHERE p.order_id="'.intval($_POST['order_id']).'" AND p.post_time>'.intval(strtotime($_POST['ntime'])).' AND p.post_time<'.intval(strtotime($_POST['etime'])).' GROUP BY post_host');
	while($pst = $db->fetch($qpost))
	{
		if (count($word)!=0)
		{
			$c=0;
			foreach ($word as $item)
			{
				//echo $item.' ';
				if ($_POST['words']=='selected')
				{
					//echo 123;
					if (preg_match('/[\s\t]'.$word_stem->stem_word($item).'/isu',$pst['post_content']))
					{
						$c++;
					}
				}
				elseif ($_POST['words']=='except')
				{
					if (!preg_match('/[\s\t]'.$word_stem->stem_word($item).'/isu',$pst['post_content']))
					{
						$c++;
					}
				}
			}
			if ($c==0) continue;
		}
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
	//print_r($outcash);
	//die();
	$kk=0;
	if ($_POST['format']=='xls')
	{
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
			/*$outmas['4'][$kk]['content']=str_replace("\n",'',$pst4['post_content']);
			$outmas['4'][$kk]['src']=$pst4['post_host'];
			$outmas['4'][$kk]['engage']=$pst4['post_engage'];
			$outmas['4'][$kk]['foll']=$pst4['blog_readers'];*/
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
			$outmas['4'][0][]=count($outmas['4'][0])+1;
			//if ((intval(date('H',$pst4['post_time']))>0)||(intval(date('i',$pst4['post_time']))>0)||(intval(date('s',$pst4['post_time']))>0)) $stime=date("H:i:s d.m.Y",$pst4['post_time']);
			//else $stime=date("d.m.Y",$pst4['post_time']);
			$stime=date("H:i:s d.m.Y",$pst4['post_time']);
			$ss_time=explode(' ',$stime);
			//$outmas['4'][1][]=$stime;
			//$outmas['4'][2][]=$stime;
			$outmas['4'][1][]=($ss_time[1]=='')?$ss_time[0]:$ss_time[1];
			$outmas['4'][2][]=($ss_time[1]=='')?'':$ss_time[0];
			$outmas['4'][3][]=str_replace('\"','',str_replace("\n",'',html_entity_decode($pst4['post_content'])));
			$outmas['4'][4][]=str_replace("\n","",$pst4['post_link']);
			if ($pst4['post_host']=='livejournal.com')
			{
				if ($pst4['blog_id']!=0)
				{
					$outmas['4'][5][]='http://'.$pst4['blog_login'].'.livejournal.com/';
				}
				else
				{
					$regex='/http\:\/\/(?<nick>.*?)\./isu';
					preg_match_all($regex,$pst4['post_link'],$out);
					$outmas['4'][5][]='http://'.$out['nick'][0].'.livejournal.com/';
				}
			}
			else
			if (($pst4['post_host']=='vk.com') || ($pst4['post_host']=='vkontakte.ru'))
			{
				$outmas['4'][5][]='http://vk.com/'.($pst4['blog_login'][0]=='-'?'club'.mb_substr($pst4['blog_login'],1,mb_strlen($pst4['blog_login'],'UTF-8')-1,'UTF-8'):'id'.$pst4['blog_login']);
			}
			else
			if ($pst4['post_host']=='twitter.com')
			{
				$outmas['4'][5][]='http://twitter.com/'.$pst4['blog_login'];
			}
			else
			if ($pst4['post_host']=='facebook.com')
			{
				$outmas['4'][5][]='http://facebook.com/'.$pst4['blog_login'];
			}
			else
			{
				$outmas['4'][5][]=$pst4['post_link'];
			}
			$outmas['4'][6][]=intval($pst4['post_engage']);
			$outmas['4'][7][]=intval($pst4['blog_readers']);
			//echo $pst4['blog_readers'].' ';
			$kk++;
		}
		
		//die();	
		foreach ($outcash['link'] as $key => $llink)
		{
			//echo $llink.'<br>';
			$link=urldecode($llink);
			$time=$outcash['time'][$key];
			$content=html_entity_decode($outcash['content'][$key]);
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
			if (($hn=='vk.com') || ($hn=='vkontakte.ru') || ($hn=='facebook.com'))
			{
				$count_likes_all++;
				if ($hn=='facebook.com')
				{
					$value_fb+=$comm;
				}
				else
				{
					$value_vk+=$comm;
				}
				//$outmas[10]['eng_mas'][0][0]++;
			}
			elseif ($hn=='livejournal.com')
			{
				$count_comment_all++;
				$value_lj+=$comm;
				//$outmas[10]['eng_mas'][1][0]++;
			}
			elseif ($hn=='twitter.com')
			{
				$count_retwits_all++;
				$value_tw+=$comm;
				//$outmas[10]['eng_mas'][2][0]++;
			}
			if ($hn!='.')
			{
				$mcount_res[$hn]++;
			}
			$count_top_res[mktime(0,0,0,date("n",$time),date("j",$time),date("Y",$time))][$hn]++;
			if (($nick!='') && ($hn!='.'))
			{
				//echo $hn.' '.$link.' <br>';
				$top_speakers[$nick]['login']=$login;
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
			if ($nastr==0) $nstr='';
			elseif ($nastr==1) $nstr='+';
			elseif ($nastr==-1) $nstr='-';
	
				$content=strip_tags($content);
				$content_r=$content;
				//$content_r=rtrim($content_r);
				$content_f='';
				$content_r=preg_replace('[^A-Za-z0-9_]', '', $content_r);
				if ($order['ful_com']=='1')
				{
					$content_f=$fcontent;
					$content_f=strip_tags($content_f);
					$content_f=html_entity_decode($content_f);
					$content_f=preg_replace('/[^а-яА-Яa-zA-ZёЁ\,\.\'\"\!\?0-9]/isu',' ',$content_f);
					//echo $content_f.'<br>';
					/*unset($outm);
					$pp['order_keyword']=preg_replace('/[^а-яА-Яa-zA-ZёЁ\ \-\=\']/isu','  ',$order['order_keyword']);
					$mkw=explode('  ',$pp['order_keyword']);
					foreach ($mkw as $kw)
					{
						if (mb_strlen($kw,'UTF-8')>3)
						{
							$regex='/\.(?<frase>[^\.]*?\.[^\.]*?\.[^\.]*?'.addslashes($kw).'\.[^\.]*?\.[^\.]*?\.)/isu';
							preg_match_all($regex,$content_f,$out);
							foreach ($out['frase'] as $item)
							{
								if (($item!='') && ($item!=' '))
								{
									$outm[]=$item;
								}
							}
						}
					}
					if ($outm[0]!='')
					{
						$content_f=$outm[0];
					}
					else
					{
						//$content_f=$pp['ful_com_post'];
					}*/
				}
				else
				{
					$tabful="";
					$content_f='';
					$fulpretext='';
				}
				//$content_f='';
				$outmas['header']['logo']='/var/www/img.png';
				$outmas['header']['contacts']='e-mail:  bma@wobot.ru    phone: +7 (901) 556-06-44';

				$outmas['1']['order_name']=$order['order_name'];
				$outmas['1']['order_time']=date('d.m.Y',strtotime($_POST['stime'])).'-'.((strtotime($_POST['etime'])==0)?date('d.m.Y',strtotime($_POST['etime'])):date('d.m.Y',strtotime($_POST['etime'])));
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
				$stime=date("H:i:s d.m.Y",$time);
				$ss_time=explode(' ',$stime);
				$outmas['3'][0][]=($ss_time[1]=='')?$ss_time[0]:$ss_time[1];
				$outmas['3'][1][]=($ss_time[1]=='')?'':$ss_time[0];
				$outmas['3'][2][]=$hn;
				$outmas['3'][3][]=$link;
				$outmas['3'][4][]=$type_re;
				$outmas['3'][5][]=($hn!='twitter.com')?preg_replace('/\s+/isu',' ',preg_replace('/[^а-яА-Яa-zA-ZёЁ\.\,\'\"0-9\!\?\s\/\@\:]/isu',' ',(($content_f=='')?$content:$content_f))):preg_replace('/[^а-яА-Яa-zA-ZёЁ\.\,\'\"0-9\!\?\s\/\@\:]/isu',' ',$content);
				$outmas['3'][6][]=$nstr;
				$outmas['3'][7][]=$isspam;
				$outmas['3'][8][]=$isfav;
				$outmas['3'][9][]=intval($eng);
				$outmas['3'][10][]=$nick;
				$outmas['3'][11][]=$gender;
				$outmas['3'][12][]=$age;
				$outmas['3'][13][]=$comm;
				$outmas['3'][14][]=$wobot['destn1'][$loc];
				$outmas['3'][15][]=$strtag;

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
				else
				{
					if ($nastr==1)
					{
						$count_wth_loc_pos++;
					}
					elseif ($nastr==0)
					{
						$count_wth_loc_neu++;
					}
					elseif ($nastr==-1)
					{
						$count_wth_loc_neg++;
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
					$top_pos['content'][]=str_replace("\n",'',((($content_f=='')?rtrim($content):rtrim($content_f))));
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
					$top_neg['content'][]=str_replace("\n",'',addslashes((($content_f=='')?trim($content):trim($content_f))));
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
		$outmas['8']['dinams']=$mas_din_8;
		$outmas['8']['all'][0][]=intval($count_pos);
		$outmas['8']['all'][0][]=intval($count_neg);
		$outmas['8']['all'][0][]=intval($count_neu);
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
		array_multisort($outmas['8']['dinams'][0],SORT_ASC,$outmas['8']['dinams'][1],SORT_ASC,$outmas['8']['dinams'][2],SORT_ASC,$outmas['8']['dinams'][3],SORT_ASC);
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
		ksort($count_top_res);
		$outmas['5']['top_count'][0][]