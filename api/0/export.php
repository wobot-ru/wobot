<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
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

function parseUrlmail($url,$to,$subj,$body,$from)
{
$uagent = "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13";
$keyword=$word;
$keyword=urlencode(iconv('utf-8','windows-1251',$keyword));
//$url='http://2mm.ru/forum/search.php?mode=result&start=15';
$postvars='to='.$to.'&subject='.$subj.'&body='.$body.'&from='.$from;
$uagent = "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13";
$ch = curl_init( $url );
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);   // возвращает веб-страницу
curl_setopt($ch, CURLOPT_HEADER, 0);           // не возвращает заголовки
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);   // переходит по редиректам
curl_setopt($ch, CURLOPT_POSTFIELDS    ,$postvars);
curl_setopt($ch, CURLOPT_ENCODING, "");        // обрабатывает все кодировки
curl_setopt($ch, CURLOPT_USERAGENT, $uagent);  // useragent
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 120); // таймаут соединения
curl_setopt($ch, CURLOPT_TIMEOUT, 120);        // таймаут ответа
curl_setopt($ch, CURLOPT_MAXREDIRS, 10);       // останавливаться после 10-ого редиректа
$content = curl_exec( $ch );
$err     = curl_errno( $ch );
$errmsg  = curl_error( $ch );
$header  = curl_getinfo( $ch );
curl_close( $ch );
 /*$header['errno']   = $err;
 $header['errmsg']  = $errmsg;
  $header['content'] = $content;*/
  return $content;
}


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
//print_r($_GET);


$db = new database();
$db->connect();
//print_r($_POST);
//echo 'not loged';
$res_micr=array('mail.ru','twitter.com','mblogi.qip.ru','rutvit.ru','friendfeed.com','godudu.com','juick.com','jujuju.ru','sports.ru','mylife.ru','chikchirik.ru','chch.ru','f5.ru','zizl.ru','smsnik.com');
$soc_netw=array('1c-club.com','cjclub.ru','diary.ru','facebook.com','myspace.com','orkut.com','vkontakte.ru','stranamam.ru','dietadiary.com','ya.ru','vk.com');
$nov_res=array('km.ru','regnum.ru','akm.ru','arms-tass.su','annews.ru','itar-tass.com','interfax.ru','interfax-russia.ru','oreanda.ru','1prime.ru','rbc.ru','rbc.ru','ria.ru','rosbalt.ru','tasstelecom.ru','finmarket.ru','expert.ru','newtimes.ru','akzia.ru','aif.ru','argumenti.ru','bg.ru','vedomosti.ru','izvestia.ru','itogi.ru','kommersant.ru','kommersant.ru','kp.ru','mospravda.ru','mn.ru','mk.ru','ng.ru','novayagazeta.ru','newizv.ru','kommersant.ru','politjournal.ru','profile.ru','rbcdaily.ru','gosrf.ru','rodgaz.ru','rg.ru','russianews.ru','senat.org','sobesednik.ru','tribuna.ru','trud.ru','newstube.ru','vesti.ru','mir24.tv','ntv.ru','1tv.ru','rutv.ru','tvkultura.ru','tvc.ru','tvzvezda.ru','5-tv.ru','ren-tv.com','radiovesti.ru','govoritmoskva.ru','ruvr.ru','kommersant.ru','cultradio.ru','radiomayak.ru','radiorus.ru','rusnovosti.ru','msk.ru','infox.ru','lenta.ru','lentacom.ru','newsru.com','temadnya.ru','newsinfo.ru','rb.ru','utronews.ru','moscow-post.ru','apn.ru','argumenti.ru','wek.ru','vz.ru','gazeta.ru','grani.ru','dni.ru','evrazia.org','ej.ru','izbrannoe.ru','inopressa.ru','inosmi.ru','inforos.ru','kommersant.ru','kreml.org','polit.ru','pravda.ru','rabkor.ru','russ.ru','smi.ru','svpressa.ru','segodnia.ru','stoletie.ru','strana.ru','utro.ru','fedpress.ru','lifenews.ru','belrus.ru','pfrf.ru','rosculture.ru','kremlin.ru','gov.ru','rosnedra.com');
auth();
if (!$loged) die();
if ($user['tariff_id']==3)
{
	$user['user_id']=61;
}
//$_POST['order_id']=309;
//$_POST['stime']='04.04.2011';
//$_POST['etime']='11.04.2011';
//print_r($_POST);
//$_POST=$_GET;
if (intval($_POST['order_id'])==0) die();
//echo 'gg';
//print_r($_SESSION);

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
$qqq=get_query();
//echo $qqq; die();
$qqq1=preg_replace('/SELECT \* FROM/is','SELECT count(*) as cnt FROM',$qqq);
$qqq1=preg_replace('/ORDER BY p\.post_time DESC/is','GROUP BY post_host',$qqq1);
$qqq2=preg_replace('/ORDER BY p\.post_time DESC/is','ORDER BY p.post_engage DESC LIMIT 10',$qqq);
//echo '<br>'.$qqq2.'<br>';
//echo $qqq1;
$countqposts=$db->query($qqq1);//'SELECT count(*) as cnt from blog_post as p LEFT JOIN robot_blogs2 AS b ON p.blog_id=b.blog_id WHERE p.order_id="'.intval($_POST['order_id']).'" AND p.post_time>'.intval(strtotime($_POST['ntime'])).' AND p.post_time<'.intval(strtotime($_POST['etime'])).' GROUP BY post_host');
while ($count=$db->fetch($countqposts))
{
	$cnt+=$count['cnt'];
	$cnt_host++;
}



	//echo $sqw;
	$qpost=$db->query($qqq); // запрос на все посты $sqw='SELECT * from blog_post as p LEFT JOIN robot_blogs2 AS b ON p.blog_id=b.blog_id WHERE p.order_id="'.intval($_POST['order_id']).'" '.getisshow3().' ORDER BY p.post_time DESC';
	$page4_post=$db->query($qqq2); // $sqw_page4='SELECT * from blog_post as p LEFT JOIN robot_blogs2 AS b ON p.blog_id=b.blog_id WHERE p.order_id="'.intval($_POST['order_id']).'" '.getisshow3().' ORDER BY p.post_engage DESC LIMIT 10';
	//$countqposts=$db->query('SELECT count(*) as cnt from blog_post as p LEFT JOIN robot_blogs2 AS b ON p.blog_id=b.blog_id WHERE p.order_id="'.intval($_POST['order_id']).'" AND p.post_time>'.intval(strtotime($_POST['ntime'])).' AND p.post_time<'.intval(strtotime($_POST['etime'])).' GROUP BY post_host');
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
					//$strtag.=$item;
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
			if ($nick!='')
			{
				$uniq_mas[$nick.':'.$hn]++;
			}
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
					if (!isset($mvalue_src[$nick.':'.$hn]) && ($nick!=''))
					{
						$value_fb+=$comm;
						$mvalue_src[$nick.':'.$hn]=1;
					}
				}
				else
				{
					if (!isset($mvalue_src[$nick.':'.$hn]) && ($nick!=''))
					{
						$value_vk+=$comm;
						$mvalue_src[$nick.':'.$hn]=1;
					}
				}
				//$outmas[10]['eng_mas'][0][0]++;
			}
			elseif ($hn=='livejournal.com')
			{
				$count_comment_all++;
				if (!isset($mvalue_src[$nick.':'.$hn]) && ($nick!=''))
				{
					$value_lj+=$comm;
					$mvalue_src[$nick.':'.$hn]=1;
				}
				//$outmas[10]['eng_mas'][1][0]++;
			}
			elseif ($hn=='twitter.com')
			{
				$count_retwits_all++;
				if (!isset($mvalue_src[$nick.':'.$hn]) && ($nick!=''))
				{
					$value_tw+=$comm;
					$mvalue_src[$nick.':'.$hn]=1;
				}
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
					$content_f=html_entity_decode(preg_replace('/[^а-яА-Яa-zA-ZёЁ\,\.\'\"\!\?0-9\/\@\:\(\)\“\_\-\&\;]/isu',' ',$content_f),ENT_QUOTES,'UTF-8');
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
				if (!isset($mas_value[$nick.':'.$hn]) && ($nick!=''))
				{
					$value_count+=$comm;
					$mas_value[$nick.':'.$hn]=1;
				}
				$outmas['2']['post_in_day']=intval($cnt/((strtotime($_POST['etime'])-strtotime($_POST['stime']))/86400));//$midle_cnt;
				$outmas['2']['count_hosts']=$cnt_host;
				$outmas['2']['audience']=$value_count;
				$outmas['2']['uniq_auth']=count($mas_value);
				$outmas['2']['period']=$_POST['sd'].'-'.$_POST['ed'];
			
				//$time_mas[$hn][]
				$stime=date("H:i:s d.m.Y",$time);
				$ss_time=explode(' ',$stime);
				$outmas['3'][0][]=($ss_time[1]=='')?$ss_time[0]:$ss_time[1];
				$outmas['3'][1][]=($ss_time[1]=='')?'':$ss_time[0];
				$outmas['3'][2][]=$hn;
				$outmas['3'][3][]=$link;
				$outmas['3'][4][]=$type_re;
				$outmas['3'][5][]=(($hn!='twitter.com')?preg_replace('/\s+/isu',' ',preg_replace('/[^а-яА-Яa-zA-ZёЁ\.\,\'\"0-9\!\?\s\/\@\:\(\)\“\_\-\&\;]/isu',' ',(($content_f=='')?$content:$content_f))):' '.preg_replace('/[^а-яА-Яa-zA-ZёЁ\.\,\'\"0-9\!\?\s\/\@\:\(\)\“\_\-\&\;]/isu',' ',$content));
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
					$top_pos['content'][]=(($hn!='twitter.com')?preg_replace('/\s+/isu',' ',preg_replace('/[^а-яА-Яa-zA-ZёЁ\.\,\'\"0-9\!\?\s\/\@\:\(\)\“\_\-\&\;]/isu',' ',(($content_f=='')?$content:$content_f))):preg_replace('/[^а-яА-Яa-zA-ZёЁ\.\,\'\"0-9\!\?\s\/\@\:\(\)\“\-\_\&\;]/isu',' ',$content));//str_replace("\n",'',((($content_f=='')?rtrim($content):rtrim($content_f))));
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
					$top_neg['content'][]=(($hn!='twitter.com')?preg_replace('/\s+/isu',' ',preg_replace('/[^а-яА-Яa-zA-ZёЁ\.\,\'\"0-9\!\?\s\/\@\:\(\)\“\_\-\&\;]/isu',' ',(($content_f=='')?$content:$content_f))):preg_replace('/[^а-яА-Яa-zA-ZёЁ\.\,\'\"0-9\!\?\s\/\@\:\(\)\“\_\-\&\;]/isu',' ',$content));//str_replace("\n",'',addslashes((($content_f=='')?trim($content):trim($content_f))));
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
		$outmas['5']['top_count'][0][]='Дата:';
		//print_r($mcount_res);
		foreach ($mcount_res as $key => $item)
		{
			//$outmas['5']['count'][$key]=$item;
			$outmas['5']['count'][0][]=$key;
			$outmas['5']['count'][1][]=$item;
			//if ((intval($item/$cnt*100)>=1) && (count($outmas['5']['proc'][0])<9))
			{
				//$outmas['5']['proc'][$key]=intval($item/$cnt*100);
				$p5_all_p+=$item;
				//$outmas['5']['proc'][0][]=$key;
				//$outmas['5']['proc'][1][]=round($item/$cnt*100);
			}
			//else
			{
				//echo $key.'<br>';
				//$p5_all_p+=$item;
				//$other_rs+=$item;
			}
			if ($i<5)
			{
				$j=0;
				$outmas['5']['top_count'][$i+1][]=$key;
				foreach ($count_top_res as $kk => $ii)
				{
					if ((intval(date('H',$kk))>0)||(intval(date('i',$kk))>0)) $kk_time=date("H:i:s d.m.Y",$kk);
					else $kk_time=date("d.m.Y",$kk);
					//$outmas['5']['top_count'][0][]=$kk_time;
					//$outmas['5']['top_count'][1][]=$key;
					//$outmas['5']['top_count'][2][]=$ii[$key];
					if (!in_array($kk_time,$outmas['5']['top_count'][0]))
					{
						$outmas['5']['top_count'][0][]=$kk_time;
					}
					//if ($j==0)
					{
						//$outmas['5']['top_count'][$i+1][$j]=$key;
					}
					//else
					{
						$outmas['5']['top_count'][$i+1][]=intval($ii[$key]);
					}
					$j++;
					//$outmas['5']['top_count'][$kk][$key]=$ii[$key];
				}
			}
			$i++;
		}
		foreach ($mcount_res as $key => $item)
		{
			if ((intval($item/$p5_all_p*100)>=1) && (count($outmas['5']['proc'][0])<9))
			{
				//$outmas['5']['proc'][$key]=intval($item/$cnt*100);
				//$p5_all_p+=$item;
				$outmas['5']['proc'][0][]=$key;
				$outmas['5']['proc'][1][]=round($item/$p5_all_p*100);
			}
			else
			{
				$other_rs+=$item;
			}
		}
		//echo $p5_all_p.' '.$other_rs.' '.$cnt;
		$outmas['5']['tc2']=$count_top_res;
		//print_r($count_top_res);
		//$outmas['5']['proc']['другие']=intval($other_rs/$cnt*100);
		$outmas['5']['proc'][0][]='другие';
		$outmas['5']['proc'][1][]=round($other_rs/$p5_all_p*100);
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
			$top_speak['login'][]=$item['login'];
		}
		$top_prom=$top_speak;
		array_multisort($top_speak['count'],SORT_DESC,$top_speak['nick'],SORT_DESC,$top_speak['pos'],SORT_DESC,$top_speak['neg'],SORT_DESC,$top_speak['neu'],SORT_DESC,$top_speak['foll'],SORT_DESC,$top_speak['src'],SORT_DESC,$top_speak['link'],SORT_DESC,$top_speak['login'],SORT_DESC);
		//print_r($top_speak);
		//echo json_encode($top_speak);
		//die();
		foreach ($top_speak['count'] as $key => $item)
		{
			/*$t_speak[$top_speak['nick'][$key]]['count']=$top_speak['count'][$key];
			$t_speak[$top_speak['nick'][$key]]['neg']=intval($top_speak['neg'][$key]);
			$t_speak[$top_speak['nick'][$key]]['pos']=intval($top_speak['pos'][$key]);
			$t_speak[$top_speak['nick'][$key]]['neu']=intval($top_speak['neu'][$key]);
			$t_speak[$top_speak['nick'][$key]]['foll']=intval($top_speak['foll'][$key]);
			$t_speak[$top_speak['nick'][$key]]['src']=$top_speak['src'][$key];
			$t_speak[$top_speak['nick'][$key]]['link']=$top_speak['link'][$key];*/
			$t_speak[0][]=$key+1;
			$t_speak[1][]=$top_speak['nick'][$key];
			//echo '| '.$top_speak['src'][$key].' |';
			if ($top_speak['src'][$key]=='livejournal.com')
			{
				if ($top_speak['login'][$key]!='')
				{
					$t_speak[2][]='http://'.$top_speak['login'][$key].'.livejournal.com/';
				}
				else
				{
					$regex='/http\:\/\/(?<nick>.*?)\./isu';
					preg_match_all($regex,$top_speak['nick'][$key],$out);
					$t_speak[2][]='http://'.$out['nick'][0].'.livejournal.com/';
				}
			}
			else
			if (($top_speak['src'][$key]=='vk.com') || ($top_speak['src'][$key]=='vkontakte.ru'))
			{
				$t_speak[2][]='http://vk.com/'.($top_speak['login'][$key][0]=='-'?'club'.mb_substr($top_speak['login'][$key],1,mb_strlen($top_speak['login'][$key],'UTF-8'),'UTF-8'):'id'.$top_speak['login'][$key]);//'http://vk.com/id'.$top_speak['login'][$key];
			}
			else
			if ($top_speak['src'][$key]=='twitter.com')
			{
				$t_speak[2][]='http://twitter.com/'.$top_speak['login'][$key];
			}
			else
			if ($top_speak['src'][$key]=='facebook.com')
			{
				$t_speak[2][]='http://facebook.com/'.$top_speak['login'][$key];
			}
			$t_speak[3][]=intval($top_speak['foll'][$key]);
			$t_speak[4][]=intval($top_speak['neg'][$key])+intval($top_speak['pos'][$key])+intval($top_speak['neu'][$key]);
			$t_speak[5][]=intval($top_speak['neg'][$key]);
			$t_speak[6][]=intval($top_speak['pos'][$key]);
			$t_speak[7][]=intval($top_speak['neu'][$key]);
			$t_speak[8][]=$top_speak['src'][$key];
			//$t_speak[7][]=$top_speak['link'][$key];
		}
		//echo json_encode($t_speak);
		//die();
		array_multisort($top_prom['foll'],SORT_DESC,$top_prom['count'],SORT_DESC,$top_prom['nick'],SORT_DESC,$top_prom['pos'],SORT_DESC,$top_prom['neg'],SORT_DESC,$top_prom['neu'],SORT_DESC,$top_prom['src'],SORT_DESC,$top_prom['link'],SORT_DESC,$top_prom['login'],SORT_DESC);
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
				$ss_time=explode(' ',$kkk_time);
				//$outmas['3'][0][]=($ss_time[1]=='')?$ss_time[0]:$ss_time[1];
				//$outmas['3'][1][]=($ss_time[1]=='')?'':$ss_time[0];
				$out_top_pos_post[0][]=($ss_time[1]=='')?$ss_time[0]:$ss_time[1];
				$out_top_pos_post[1][]=($ss_time[1]=='')?'':$ss_time[0];
				$out_top_pos_post[2][]=$top_pos['src'][$key];
				$out_top_pos_post[3][]=$top_pos['link'][$key];
				$out_top_pos_post[4][]=$top_pos['type'][$key];
				$out_top_pos_post[5][]=strip_tags($top_pos['content'][$key]);
				$out_top_pos_post[6][]=$top_pos['eng'][$key];
				$out_top_pos_post[7][]=$top_pos['nick'][$key];
				$out_top_pos_post[8][]=$top_pos['gen'][$key];
				$out_top_pos_post[9][]=$top_pos['foll'][$key];
				$out_top_pos_post[10][]=$top_pos['loc'][$key];
				$out_top_pos_post[11][]=$top_pos['tags'][$key];
			
			}
			$i++;
		}
		if (count($out_top_pos_post[0])==0)
		{
			$out_top_pos_post[0][]='';
			$out_top_pos_post[1][]='';
			$out_top_pos_post[2][]='';
			$out_top_pos_post[3][]='';
			$out_top_pos_post[4][]='';
			$out_top_pos_post[5][]='';
			$out_top_pos_post[6][]='';
			$out_top_pos_post[7][]='';
			$out_top_pos_post[8][]='';
			$out_top_pos_post[9][]='';
			$out_top_pos_post[10][]='';
			$out_top_pos_post[11][]='';
		}
		$i=0;
		foreach ($top_neg['foll'] as $key => $item)
		{
			if ($i<10)
			{
				$kkk=$top_neg['time'][$key];
				if ((intval(date('H',$kkk))>0)||(intval(date('i',$kkk))>0)) $kkk_time=date("H:i:s d.m.Y",$kkk);
				else $kkk_time=date("d.m.Y",$kkk);
				$ss_time=explode(' ',$kkk_time);
				$out_top_neg_post[0][]=($ss_time[1]=='')?$ss_time[0]:$ss_time[1];
				$out_top_neg_post[1][]=($ss_time[1]=='')?'':$ss_time[0];
				$out_top_neg_post[2][]=$top_neg['src'][$key];
				$out_top_neg_post[3][]=$top_neg['link'][$key];
				$out_top_neg_post[4][]=$top_neg['type'][$key];
				$out_top_neg_post[5][]=strip_tags($top_neg['content'][$key]);
				$out_top_neg_post[6][]=$top_neg['eng'][$key];
				$out_top_neg_post[7][]=$top_neg['nick'][$key];
				$out_top_neg_post[8][]=$top_neg['gen'][$key];
				$out_top_neg_post[9][]=$top_neg['foll'][$key];
				$out_top_neg_post[10][]=$top_neg['loc'][$key];
				$out_top_neg_post[11][]=$top_neg['tags'][$key];
			}
			$i++;
		}
		if (count($out_top_neg_post[0])==0)
		{
			$out_top_neg_post[0][]='';
			$out_top_neg_post[1][]='';
			$out_top_neg_post[2][]='';
			$out_top_neg_post[3][]='';
			$out_top_neg_post[4][]='';
			$out_top_neg_post[5][]='';
			$out_top_neg_post[6][]='';
			$out_top_neg_post[7][]='';
			$out_top_neg_post[8][]='';
			$out_top_neg_post[9][]='';
			$out_top_neg_post[10][]='';
			$out_top_neg_post[11][]='';
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

			$t_prom[0][]=$key+1;
			$t_prom[1][]=$top_prom['nick'][$key];
			if ($top_prom['src'][$key]=='livejournal.com')
			{
				if ($top_prom['login'][$key]!='')
				{
					$t_prom[2][]='http://'.$top_prom['login'][$key].'.livejournal.com/';
				}
				else
				{
					$regex='/http\:\/\/(?<nick>.*?)\./isu';
					preg_match_all($regex,$top_prom['nick'][$key],$out);
					$t_prom[2][]='http://'.$out['nick'][0].'.livejournal.com/';
				}
			}
			else
			if (($top_prom['src'][$key]=='vk.com') || ($top_prom['src'][$key]=='vkontakte.ru'))
			{
				$t_prom[2][]='http://vk.com/'.($top_prom['login'][$key][0]=='-'?'club'.mb_substr($top_prom['login'][$key],1,mb_strlen($top_prom['login'][$key],'UTF-8'),'UTF-8'):'id'.$top_prom['login'][$key]);
			}
			else
			if ($top_prom['src'][$key]=='twitter.com')
			{
				$t_prom[2][]='http://twitter.com/'.$top_prom['login'][$key];
			}
			else
			if ($top_prom['src'][$key]=='facebook.com')
			{
				$t_prom[2][]='http://facebook.com/'.$top_prom['login'][$key];
			}
			$t_prom[3][]=intval($top_prom['foll'][$key]);
			$t_prom[4][]=$top_prom['count'][$key];
			$t_prom[5][]=intval($top_prom['neg'][$key]);
			$t_prom[6][]=intval($top_prom['pos'][$key]);
			$t_prom[7][]=intval($top_prom['neu'][$key]);
			$t_prom[8][]=$top_prom['src'][$key];
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
		$iter=0;
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
		$outm_toploc[0][]='Другие';
		$outm_toploc[1][]=intval($count_othr_l_oc);
		$outm_loc[0][]='Неопределено';
		$outm_loc[1][]=intval($count_wth_loc_pos)+intval($count_wth_loc_neu)+intval($count_wth_loc_neg);
		$outm_loc[2][]=intval($count_wth_loc_pos);
		$outm_loc[3][]=intval($count_wth_loc_neg);
		$outm_loc[4][]=intval($count_wth_loc_neu);
		//ksort($top_speak);
		if (count($t_speak)==0)
		{
			$t_speak[0][]='';
			$t_speak[1][]='';
			$t_speak[2][]='';
			$t_speak[3][]='';
			$t_speak[4][]='';
			$t_speak[5][]='';
			$t_speak[6][]='';
			$t_speak[7][]='';
			$t_speak[8][]='';
		}
		if (count($t_prom)==0)
		{
			$t_prom[0][]='';
			$t_prom[1][]='';
			$t_prom[2][]='';
			$t_prom[3][]='';
			$t_prom[4][]='';
			$t_prom[5][]='';
			$t_prom[6][]='';
			$t_prom[7][]='';
			$t_prom[8][]='';
		}
		$outmas['6']=$t_speak;
		$outmas['7']=$t_prom;
		$outmas['9']['loc']=$outm_loc;
		$outmas['9']['top_loc']=$outm_toploc;
		if (count($out_age)!=0)
		{
			$out_age['09-14']=$out_age['9-14'];
		}
		ksort($out_age);
		unset($out_age['9-14']);
		if (count($out_age)!=0)
		{
			foreach ($out_age as $key => $item)
			{
				$out_age1[0][]=$key;
				$out_age1[1][]=intval($item['count']);
				$out_age1[2][]=intval($item['pos']);
				$out_age1[3][]=intval($item['neg']);
				$out_age1[4][]=intval($item['neu']);
			}
		}
		else
		{
			$out_age1[0][]='-';
			$out_age1[1][]=0;
			$out_age1[2][]=0;
			$out_age1[3][]=0;
			$out_age1[4][]=0;
		}
		foreach ($out_gen as $key => $item)
		{
			$out_gen1[0][]=$key;
			$out_gen1[1][]=intval($item['count']);
			$out_gen1[2][]=intval($item['pos']);
			$out_gen1[3][]=intval($item['neg']);
			$out_gen1[4][]=intval($item['neu']);
		}
		if (count($out_gen)==0)
		{
			$out_gen1[0][]='М';
			$out_gen1[1][]=0;
			$out_gen1[2][]=0;
			$out_gen1[3][]=0;
			$out_gen1[4][]=0;
			$out_gen1[0][]='Ж';
			$out_gen1[1][]=0;
			$out_gen1[2][]=0;
			$out_gen1[3][]=0;
			$out_gen1[4][]=0;
		}
		$outmas['10']['age']=$out_age1;
		$outmas['10']['gen']=$out_gen1;
		$outmas['10']['value_mas'][0][0]='Ресурс';
		$outmas['10']['value_mas'][0][1]='vk.com';
		$outmas['10']['value_mas'][0][2]='livejournal.com';
		//$outmas['10']['value_mas'][0][3]='facebook.com';
		$outmas['10']['value_mas'][0][3]='twitter.com';
		$outmas['10']['value_mas'][1][0]='Охват';
		$outmas['10']['value_mas'][1][1]=intval($value_vk);
		$outmas['10']['value_mas'][1][2]=intval($value_lj);
		//$outmas['10']['value_mas'][1][3]=intval$value_fb;
		$outmas['10']['value_mas'][1][3]=intval($value_tw);
		//$outmas['10']['eng_mas'][0][0]='Название';
		$outmas['10']['eng_mas'][0][0]='Количество лайков';
		$outmas['10']['eng_mas'][0][1]='Количество комментариев';
		$outmas['10']['eng_mas'][0][2]='Количество ретвитов';
		//$outmas['10']['eng_mas'][1][0]='Количество';
		$outmas['10']['eng_mas'][1][0]=intval($count_likes_all);
		$outmas['10']['eng_mas'][1][1]=intval($count_comment_all);
		$outmas['10']['eng_mas'][1][2]=intval($count_retwits_all);
		//print_r($outmas1);
		//echo json_encode($outmas);
		//die();
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
			header("Content-Disposition: attachment; filename=wobot".date('dmy',$_POST['stime']).date('dmy',$_POST['etime']).$order['order_name']."export.xls");
			echo $fulltext;
			/*$idfls=date('s_i_G__n_j_Y',time());
			$fp = fopen('/var/www/beta/export/'.$user['user_pass'].'_'.$idfls.'.xls', 'a');
			fwrite($fp, $fulltext);
			fclose($fp);
			$headers  = "noreply@wobot.ru\r\n"; 
			$headers .= "Bcc: noreply@wobot.ru\r\n";
			$headers .= 'MIME-Version: 1.0' . "\r\n";
			$headers .= 'Content-type: text/html; charset=utf-8'."\r\n";
			if ($user['user_email']!='')
			{
				parseUrlmail('http://wobot.ru/mail_send.php',$user['user_email'],'Команда Wobot',urlencode("<html><body>Спасибо за использование нашего сервиса, ваш отчет можно скачать по ссылке: <br><a href='http://beta.wobot.ru/digest.php?token=".$user['user_pass']."&order_id=".$idfls."&type=".$_POST['format']."'>ссылка</a></body></html>"),$headers);
			}*/
			//echo ;
			//echo (stream_get_contents($pipes[1]);
		}
	}
	else
	{
		if($_POST['format']=='doc')
		{
			//header("Content-type: application/vnd.ms-word");
			//header('Content-Disposition: attachment;Filename=wobot'.date('-dmy-his').'.doc');
			$echo_word.="<table>"."<tr><td>Дата</td><td>Ресурс</td><td>Ссылка</td><td>Тип ресурса</td><td>Избранное</td><td>Спам</td><td>Эмоциональность</td>".(($order['order_engage']=='1')?"<td>Engagement</td>":"")."<td>Теги</td><td>Ник</td><td>Аудитория</td><td>Геотрагетинг</td><td>Упоминание</td>".(($order['ful_com']=='1')?"<td>Полное упоминание</td>":"")."</tr>";
		}
		if ($_POST['format']=='odf')
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
				elseif ($_POST['format']=='doc')
				{
					$echo_word.="<tr><td>".$stime."</td><td><a href=\"http://www.".$hn."\">".$hn."</a></td><td><a href=".$link.">".$link."</a></td><td>".$type_re."</td><td>".$isfav."</td><td>".$isspam."</td><td>".$nstr."</td>".(($order['order_engage']=='1')?"<td>".$eng."</td>":"")."<td>".$strtag."</td><td>".$nick."</td><td>".$comm."</td><td>".$wobot['destn1'][$loc]."</td><td>".$content_r."</td><td>".$content_f."</td></tr>";
				}
				elseif ($_POST['format']=='odf')
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
			$idfls=date('s_i_G__n_j_Y',time());
			/*$pdf->Output('/var/www/beta/export/'.$user['user_pass'].'_'.$idfls.'.pdf', 'F');
			$headers  = "noreply@wobot.ru\r\n"; 
			$headers .= "Bcc: noreply@wobot.ru\r\n";
			$headers .= 'MIME-Version: 1.0' . "\r\n";
			$headers .= 'Content-type: text/html; charset=utf-8'."\r\n";
			if ($user['user_email']!='')
			{
				parseUrlmail('http://wobot.ru/mail_send.php',$user['user_email'],'Команда Wobot',urlencode("<html><body>Спасибо за использование нашего сервиса, ваш отчет можно скачать по ссылке: <br><a href='http://beta.wobot.ru/digest.php?token=".$user['user_pass']."&order_id=".$idfls."&type=".$_POST['format']."'>ссылка</a></body></html>"),$headers);
			}
			$fp = fopen('/var/www/beta/export/'.$user['user_pass'].'_'.$idfls.'.xls', 'a');*/
			$pdf->Output('wobot-export.pdf', 'I');
		}
		elseif ($_POST['format']=='doc')
		{
			$exp.="<html>"."<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\">"."<body><table>"."<tr><td>Упоминаний</td><td>Ресурсов</td><td>Аудитория</td><td>Цитируемость</td><td>Вовлеченность</td><td>Доверие</td><td>Engagement</td></tr><tr><td>".$cnt."</td><td>".$cnt_host."</td><td>".$metrics['value']."</td><td>".((intval((1/($othert/250))*100)+intval((1/($metricss['value']/1000))*100))/100)."</td><td>".(intval($othert/$metricss['value']*100)/100)."</td><td>0</td><td>".$metrics['engagement']."</td></tr></table>".$echo_word;
			$exp.="</table></body>"."</html>";
			header("Content-Type: application/vnd.ms-word");
			header("content-disposition: attachment;filename=wobot-export.doc");
			echo $exp;
			/*$idfls=date('s_i_G__n_j_Y',time());
			$fp = fopen('/var/www/beta/export/'.$user['user_pass'].'_'.$idfls.'.doc', 'w');
			fwrite($fp, $exp);
			fclose($fp);
			$headers  = "noreply@wobot.ru\r\n"; 
			$headers .= "Bcc: noreply@wobot.ru\r\n";
			$headers .= 'MIME-Version: 1.0' . "\r\n";
			$headers .= 'Content-type: text/html; charset=utf-8'."\r\n";
			//echo $user['user_email'];
			if ($user['user_email']!='')
			{
				parseUrlmail('http://wobot.ru/mail_send.php',$user['user_email'],'Команда Wobot',urlencode("<html><body>Спасибо за использование нашего сервиса, ваш отчет можно скачать по ссылке: <br><a href='http://beta.wobot.ru/digest.php?token=".$user['user_pass']."&order_id=".$idfls."&type=".$_POST['format']."'>ссылка</a></body></html>"),$headers);
			}*/
		}
		elseif ($_POST['format']=='odf')
		{
			$ods->addTable($table);
			$ods->downloadOdsFile("wobot-export.ods");
			/*$idfls=date('s_i_G__n_j_Y',time());
			$ods->genOdsFile('/var/www/beta/export/'.$user['user_pass'].'_'.$idfls.'.ods');
			$headers  = "noreply@wobot.ru\r\n"; 
			$headers .= "Bcc: noreply@wobot.ru\r\n";
			$headers .= 'MIME-Version: 1.0' . "\r\n";
			$headers .= 'Content-type: text/html; charset=utf-8'."\r\n";
			if ($user['user_email']!='')
			{
				parseUrlmail('http://wobot.ru/mail_send.php',$user['user_email'],'Команда Wobot',urlencode("<html><body>Спасибо за использование нашего сервиса, ваш отчет можно скачать по ссылке: <br><a href='http://beta.wobot.ru/digest.php?token=".$user['user_pass']."&order_id=".$idfls."&type=ods'>ссылка</a></body></html>"),$headers);
			}*/
		}
		
	}
	//sleep(1);
//print_r($outmas);
//print_r($timet);
?>
