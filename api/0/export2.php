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
require_once('/var/www/new/com/porter.php');

error_reporting(0);

$word_stem=new Lingua_Stem_Ru();

$redis=new Redis() or die("Can'f load redis module.");
$redis->connect('127.0.0.1');
set_log('export_mail',$_POST);
		
function parseUrlmail($url,$to,$subj,$body,$from)
{
$uagent = "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13";
$keyword=$word;
$keyword=urlencode(iconv('utf-8','windows-1251',$keyword));
//$url='http://2mm.ru/forum/search.php?mode=result&start=15';
$postvars='mail='.$to.'&title='.$subj.'&content='.$body.'&from='.$from;
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
$res_micr=array('twitter.com','twimg.com','rutwit.ru','juick.com','rutvit.ru','voprosik.net');
$soc_netw=array('vk.com','vkontakte.ru','facebook.com','mamba.ru','foursquare.com','privet.ru','loveplanet.ru','instagram.com','comon.ru','instagr.am','twitpic.com','soratniki-online.ru','jujuju.ru','yfrog.com','posobie.info','vgorode.ru','smi2.ru','mylove.ru','nirvana.fm','myjulia.ru','4sq.com','24open.ru','lockerz.com','blogberg.ru','fanparty.ru','blox.ua','formspring.me','mylife.ru','fb.me');
$doska_objavl=array('molotok.ru','slando.ru','hh.ru','superjob.ru','autobay.ru','aukro.ua','avtopoisk.ua','bigbuzzi.ru','ria.ua','live-games.biz','darudar.org','alfa.kz','bigbuzzy.ru','slando.ua','slanet.by','hh.ua','emarket.ua','jooble.ru','carsar.ru','rabota.ua','adcontext.net','blogskidok.ru','irr.by','24au.ru','vsem.ru','rabsila.ru','biglion.ru','bicotender.ru','poputchik.ru','ataxa.ru','weblancer.net','livehh.ru','segol.ru','slando.by','ukrgo.com','tiu.ru','autorella.ru','rabotka.ru','work.ua','freelancer.ua','career.ru','slanet.ru','acoola.ru','superjob.ua','495ru.ru','krisiswork.ru','ollx.ru','vblizimetro.ru','hh.kz','fara.ru','job-mo.ru','price-altai.ru','zarplata.ru','bavun.ru','raskleischik.ru','free-lance.ru','vingrad.ru','business-top.info','job50.ru','jobs.ua','extra-m.ru','zor.uz','all-biz.info','job-in.ru');
$forabl=array('livejournal.com','blogspot.com','liveinternet.ru','baby.ru','diary.ru','blog.ru','babyblog.ru','cells.ru','mmm-tasty.ru','izhevsk.ru','wordpress.com','friendfeed.com','dia-club.ru','journals.ru','kuraev.ru','kharkovforum.com','ffclub.ru','askcar.ru','awd.ru','aviaforum.ru','audi100.ru','jazz-russia.ru','rustorka.com','community.livejournal.com','crimea-board.net','ebay-forum.ru','vw-golfclub.ru','rossia.org','krasnogorie.net','blogrus.ru','zoneland.ru','shophelp.ru','nnbt.org','gencosmo.ru','pomadaru.ru','hiblogger.net','oriforum.net','kosmru.ru','kosmetikaru.ru','cosmetru.ru','parfumeriaru.ru','mypage.ru','oszone.net','antichat.ru','protiv-putina.ru','gl-forum.ru','guns.ru','koggalan.ru','socgrad.ru','diets.ru','bmwpost.ru','unsorted.me','idiot.fm','abonentik.ru','teniru.ru','cosmeticsmoscow.ru','cosmetparfum.ru','makiiag.ru','narod.ru','bestpersons.ru','cyberforum.ru','cosmeticsset.ru','otzyv.ru','cosmeticcream.ru','fscream.ru','gsmforum.ru','bikepost.ru','emilzo.ru','PS3hits.ru','ru-board.com','adslclub.ru','biznet.ru','genskodegda.ru','msexcel.ru','kasperskyclub.ru','makiagru.ru','landscrona.ru','profescosmetics.ru','onlinecosmeticsru.ru','rychklad.ru','guitarplayer.ru','typepad.com','polusharie.com','omsk.com','shitnikovo.ru','maverickclub.ru','ladagranta.net','foundationcream.ru','seacosmeticsru.ru','helpix.ru','blogs.mail.ru','tucson-club.ru','bmwland.ru','www.liveinternet.ru','privetsochi.ru','npc-wagon.ru','forum42.ru','wp7forum.ru','octavia-club.ru','mediccosmetics.ru','renault-club.ru','mygorod48.ru','blog-mashnin.ru','zzima.com','pavshinka.ru','flyertalk.ru','nhouse.ru','forumrm.ru','anti-free.ru','uol.ru','lancerx.ru','polosedan.ru','narod2.ru','kgalantereia.ru','massovki.ru','sat-expert.com','kudc.info','chinamobil.ru','teron.ru','www.diary.ru','nevesta.info','luxperfumes.ru','velomania.ru','vse.kz','forumprosport.ru','ceedclub.ru','programmersforum.ru','trashbox.ru','moyblog.net','pesikot.org','bgforum.ru','forum-tvs.ru','aveoclub.ru','t30p.ru','livejournal.ru','equish.ru','iphones.ru','nissan-note.info','smart-lab.ru','rusforum.ca','niva4x4.ru','dearheart.ru','pchelpforum.ru','fordclub.org','mamochka.org','soup.io','regforum.ru','aquaforum.ua','opa.by','futurin.ru','rpod.ru','opentorrent.ru','rcforum.ru','forum-psn.ru','allan999.su','hip-hop.ru','mblogi.qip.ru','bogdanclub.ru','tes1tru.ru','kosmetista.ru','yuptalk.ru','nowa.cc','fisher02.ru','auto4life.ru','stepandstep.ru','win7mob.ru','vputilkovo.ru','rl-team.net','mobile-review.com','gliffer.ru','mz-tracker.net','cars48.ru','ma3da.ru','railwayclub.info','belor.biz','lkforum.ru','saransk.ru','chevy-niva.ru','automarketspb.ru','malenkiy.ru','avmalgin.livejournal.com','enrof.net','u-mama.ru','mysonata.ru','saechka.ru','youhtc.ru','agulife.ru','morehod.ru','myvuz.ru','wishestree.ru','mmgp.ru','horrorzone.ru','ps3club.ru','extreme.by','blogdetik.com','bmv-car.ru','horde.kz','tatfish.com','zavalinka.org','uranbator.ru','ladaportal.com','yatelefon.ru','fordclub.by','asusmobile.ru');
$video_host=array('youtube.com','twitube.org','rutube.ru');
$smi=array('mail.ru','google.com','ya.ru','e1.ru','yandex.ru','banki.ru','qip.ru','telesputnik.ru','kp.ru','rg.ru','aif.ru','finam.ru','hpc.ru','vesti.ru','na-svyazi.ru','pressdisplay.com','finance.ua','dw7.org','google.ru','russiaregionpress.ru','bezformata.ru','4pda.ru','vedomosti.ru','advis.ru','sports.ru','nanonewsnet.ru','news16.ru','investpalata.ru','qwas.ru','rb.ru','grandfin.ru','roem.ru','gc0.ru','kapital-rus.ru','bankir.ru','media-office.ru','searchengines.ru','siteua.org','fastworldnews.ru','smipressa.ru','t-l.ru','prokopievsk.ru','radiovesti.ru','nn.ru','mfd.ru','itar-tass.com','wnd.su','yvision.kz','balinfo.ru','ruboard.ru','novoteka.ru','centrr.com','beatles.ru','internetua.com','kirovnet.ru','nnm.ru','iguides.ru','molnet.ru','ruslentarss.ru','advertology.ru','shuud.mn','bobr.by','cnews.ru','ria.ru','arb.ru','consolelife.ru','ubr.ua','sinhronika.ru','tut.by','allinnet.ru','mosoblpress.ru','unian.net','rbc.ru','gipsyteam.ru','tvc.ru','profi-forex.org','forum.md','kp.ua','radiomv.ru','goosha.ru','properm.ru','ford-club.ru','arhnet.info','ruclubnews.ru','tomsk.ru','utro.ua','niann.ru','bigmir.net','zakon.kz','zakonia.ru','tochka.net','drdot.ru','kremlin.ru','itrn.ru','altapress.ru','ridus.ru','rbcdaily.ru','megaobzor.com','kharkov.ua','auto-pin.ru','content-review.com','last24.info','rtkorr.com','megapressa.ru','bratsk.org','mirtesen.ru','odintsovo.info','fincake.ru','regnum.ru','xn----btbgsdiiccrfcw.xn--p1ai','fedpress.ru','abireg.ru','infox.ru','onliner.by','sovsport.ru','actualcomments.ru','polit.ru','xakep.ru','ngs.ru','2sprightly.com','yuga.ru','whitechannel.ru','foodnewsweek.ru','sayanogorsk.info','mk.ru','pnp.ru','newsru.com','atrex.ru','kuncego.ru','1tv.ru','myprice74.ru','imperiya.by','kolesa.ru','kozhuhovo.com','gazeta.ru','city-n.ru','yola.ru','perm.ru','70rus.org','izvestia.ru','aviaport.ru','russian-consumer.ru','prm.ru','amic.ru','retailer.ru','moscow.gs','triabon.ru','inetchannel.ru','univer.ru','prodmagazin.ru','avto-ru.net','agroperspectiva.com','planetasmi.ru','moscow99.ru','tatar-inform.ru','magpoteh.ru','govoritmoskva.ru','uznayvse.ru','elitetrader.ru','bllogs.ru','oblvesti.ru','mbnews.ru','open.by','bcs-express.ru','1prime.ru','angi.ru','maemoworld.ru','transportweekly.com','web-bus.ru','cssart.ru','wec.ru','soccer.ru','bodymarker.info','compras.ru','rts.ru','avtosmotr.ru','plusworld.ru','pronowosti.ru','nstarikov.ru','podfm.ru','urup.ru','xn--80aagdtgcad2beoqjqa1b7c.xn--p1ai','ukragroconsult.com','manjunet.com','eparhia-saratov.ru','time.mk','8tv.ru','digit.ru','dostup1.ru','gosman.ru','turizmsaratova.ru','chechnyatoday.com','gosbook.ru','smartphone.ua','newsrider.ru','trend.az','puil.net','comments.ua','sobesednik.ru','shakhty.su','presuha.ru','trud.ru','ict-online.ru','intermedia.ru','newkaliningrad.ru','samaratoday.ru','segodnia.ru','forbes.ru','07kbr.ru','38rus.com','bigness.ru','liga.net','1soc.com','oilgasfield.ru','tks.ru','spravedlivo-online.ru','allmedia.ru','v102.ru','slon.ru','dmitrov.su','diver-sant.ru','pcweek.ru','kvaisa.ru','tlt.ru','naviny.by','todate.su','ua-football.com','gb.ru','f5.ru','obozrevatel.com','gorodbryansk.info','usinsk.eu','stringerpress.ru','tumentoday.ru','autominsk.by','krasnoe.tv','lenta.ru','carclub.ru','kavpolit.com','mywebs.su','pavlonews.info','ulgov.ru','glavred.info','avs.ru','w7phone.ru','russian-club.net','ysia.ru','argumenti.ru','ra-public.ru','novost-segodnya.ru','vlast16.ru','snob.ru','justmedia.ru','kavkazcenter.com','novo-city.ru','chitaitext.ru','1news.az','itar-tasskuban.ru','news2.ru','sport-express.ru','riamoda.ru','terrikon.com','news.tj','deepapple.com','gazeta.kz','veved.ru','footballmir.ru','omsk.net','high-ssociety.ru','championat.com','investgazeta.net','kotlin.ru','spy.kz','korrespondent.net','israelonline.ru','kavkaz-uzel.ru','mediazavod.ru','vitz.ru','vkirove.ru','mircreditov.com','solovei.info','carsguru.net','newsmsk.com','freetowns.ru','bashinform.ru','kommersant.ua','blindmen.ru','boufradji.com','crn.ru','i-ekb.ru','macdigger.ru','nr2.ru','okrizise.com','wsphone.ru','yesasia.ru','shgs.ru','ukr-ru.net');
$torg_plosh=array('expedea.ru','bank-top.ru','skylink.ru','kupongid.ru','kupi.org','theoryandpractice.ru','autoline-eu.ru','electrosvyaz.com','credit-on.ru','enes26.ru','turizm.ru','kuponator.ru','xgencia.com','krupon.info','karelia.pro','livemaster.ru','test-drive.ru','kartaturov.ru','icst.ru','turkey.ru','autonavigator.ru','buruki.ru','sotoguide.ru','sfedor.ru','turkompot.ru','idk.ru','ok-credit.ru','rucountry.ru','tripcheap.ru','bank-centr.ru','zakupki.ru','idknet.com','buryatia.org','topticketshop.ru','videomax.ru','avtobazar.ua','multiply.com','www2.amit.ru','biletyavia.tk','ferra.ru','prom.ua','istyle.su','rustirka.ru','vashdom.ru','artinvestment.ru','riarealty.ru','kassy.ru','wildberries.ru','restate.ru','svali.ru','multitender.ru');
$photo_host=array('lori.ru');
auth();
if (!$loged) die();
if ($user['tariff_id']==3)
{
	$infus=$db->query('SELECT order_id,user_id FROM blog_orders WHERE order_id='.$_POST['order_id'].' LIMIT 1');
	$usri=$db->fetch($infus);
	if ($usri['user_id']==61)
	{
		$user['user_id']=61;
	}
	else
	{
		
	}
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

$query1='SELECT * FROM blog_tag WHERE user_id='.intval($user['user_id']);
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
		$outcash['host'][$ii]=$pst['post_host'];
		$outcash['blog_id'][$ii]=$pst['blog_id'];
		$ii++;
		//$arroutcash['login']
	}
	$kk=0;
	if ($_POST['format']=='xls')
	{
		$outmas['1']=array();
		$outmas['2']=array();
		$outmas['3']=array();
		$outmas['4']=array();
		$outmas['5']=array();
		$outmas['6']=array();
		$outmas['7']=array();
		$outmas['8']=array();
		$outmas['9']=array();
		$outmas['10']=array();
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
			$outmas['4'][3][]=iconv("UTF-8", "UTF-8//IGNORE",str_replace('\"','',str_replace("\n",'',html_entity_decode($pst4['post_content']))));
			$outmas['4'][4][]=iconv("UTF-8", "UTF-8//IGNORE", str_replace("\n","",$pst4['post_link']));
			if ($pst4['post_host']=='livejournal.com')
			{
				if ($pst4['blog_id']!=0)
				{
					$outmas['4'][5][]=iconv("UTF-8", "UTF-8//IGNORE", 'http://'.$pst4['blog_login'].'.livejournal.com/');
				}
				else
				{
					$regex='/http\:\/\/(?<nick>.*?)\./isu';
					preg_match_all($regex,$pst4['post_link'],$out);
					$outmas['4'][5][]=iconv("UTF-8", "UTF-8//IGNORE", 'http://'.$out['nick'][0].'.livejournal.com/');
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
			if ($pst4['post_host']=='mail.ru')
			{
				$outmas['4'][5][]='http://blogs.'.$pst4['blog_link'].'/'.$pst4['blog_login'];
			}
			else
			if ($pst4['post_host']=='liveinternet.ru')
			{
				$outmas['4'][5][]='http://liveinternet.ru/users/'.$pst4['blog_login'];
			}
			else
			if ($pst4['post_host']=='ya.ru')
			{
				$outmas['4'][5][]='http://'.$pst4['blog_login'].'.ya.ru';
			}
			else
			if ($pst4['post_host']=='yandex.ru')
			{
				$outmas['4'][5][]='http://'.$pst4['blog_login'].'.ya.ru';
			}
			else
			if ($pst4['post_host']=='rutwit.ru')
			{
				$outmas['4'][5][]='http://rutwit.ru/'.$pst4['blog_login'];
			}
			else
			if ($pst4['post_host']=='rutvit.ru')
			{
				$outmas['4'][5][]='http://rutwit.ru/'.$pst4['blog_login'];
			}
			else
			if ($pst4['post_host']=='babyblog.ru')
			{
				$outmas['4'][5][]='http://www.babyblog.ru/user/info/'.$pst4['blog_login'];
			}
			else
			if ($pst4['post_host']=='blog.ru')
			{
				$outmas['4'][5][]='http://'.$pst4['blog_login'].'.blog.ru/profile';
			}
			else
			if ($pst4['post_host']=='foursquare.com')
			{
				$outmas['4'][5][]='https://ru.foursquare.com/'.$pst4['blog_login'];
			}
			else
			if ($pst4['post_host']=='kp.ru')
			{
				$outmas['4'][5][]='http://blog.kp.ru/users/'.$pst4['blog_login'].'/profile/';
			}
			else
			if ($pst4['post_host']=='aif.ru')
			{
				$outmas['4'][5][]='http://blog.aif.ru/users/'.$pst4['blog_login'].'/profile';
			}
			else
			if ($pst4['post_host']=='friendfeed.com')
			{
				$outmas['4'][5][]='http://friendfeed.com/'.$pst4['blog_login'];
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
			//if ($nick!='')
			//if ((trim($nick)!='') && ($hn!='.'))
			{
				//$uniq_mas[$nick.':'.$blog_id]++;
			}
			$login=$outcash['login'][$key];
		    $hn=parse_url($link);
		    $hn=$hn['host'];
		    $ahn=explode('.',$hn);
		    $fulhn=$hn;
		    $hn = $ahn[count($ahn)-2].'.'.$ahn[count($ahn)-1];
			$hh = $ahn[count($ahn)-2];
			if ($hn=='.')
			{
				$hn=$outcash['host'][$key];
			}
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
				$mcount_res[$hn]++;
			}
			$count_top_res[mktime(0,0,0,date("n",$time),date("j",$time),date("Y",$time))][$hn]++;
			if ((trim($nick)!='') && ($hn!='.'))
			{
				$uniq_mas[$nick.':'.$blog_id]++;
				//echo $hn.' '.$link.' <br>';
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
			}
			//print_r($top_speakers);
			//echo '<br>';
		
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

			if (in_array($hn,$soc_netw)||($fulhn=='plus.google.com'))
			{
				$type_re='социальная сеть';
			}
			else
			if (in_array($hn,$doska_objavl))
			{
				$type_re='доска объявлений';
			}
			else
			if (in_array($hn,$res_micr))
			{
				$type_re='микроблог';
			}
			elseif (in_array($hn, $forabl))
			{
				$type_re='форум или блог';
			}
			elseif (in_array($hn, $video_host))
			{
				$type_re='видеохостинг';
			}
			elseif (in_array($hn, $smi))
			{
				$type_re='СМИ';
			}
			elseif (in_array($hn, $torg_plosh))
			{
				$type_re='торговая площадка';
			}
			elseif (in_array($hn, $photo_host))
			{
				$type_re='фотохостинг';
			}
			else
			{
				$type_re='прочее';
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
				if (($order['ful_com']=='1') && (!in_array($hn,$exc_src)))
				{
					$content_f=$fcontent;
					$content_f=strip_tags($content_f);
					$content_f=html_entity_decode($content_f);
					$content_f=strip_tags(html_entity_decode(preg_replace('/[^а-яА-Яa-zA-ZёЁ\,\.\'\"\!\?0-9\/\@\:\(\)\“\_\-\&\;\#\<\>]/isu',' ',$content_f),ENT_QUOTES,'UTF-8'));
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

				$outmas['1']['order_name']=iconv("UTF-8", "UTF-8//IGNORE", $order['order_name']);
				$outmas['1']['order_time']=date('d.m.Y',strtotime($_POST['stime'])).'-'.((strtotime($_POST['etime'])==0)?date('d.m.Y',strtotime($_POST['etime'])):date('d.m.Y',strtotime($_POST['etime'])));
				$outmas['1']['order_keyword']=iconv("UTF-8", "UTF-8//IGNORE", $order['order_keyword']);
			
				$outmas['2']['count_posts']=intval($cnt);
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
				if (!isset($mas_value[$login.':'.$hn]) && ($outcash['blog_id'][$key]!=0) && ($outcash['nick'][$key]!=''))
				{
					$value_count+=$comm;
					$mas_value[$login.':'.$hn]=1;
					//echo $nick.' '.$hn.' '.$comm.' '.$value_count.'<br>';
				}
				$outmas['2']['post_in_day']=intval($cnt/((strtotime($_POST['etime'])-strtotime($_POST['stime'])+86400)/86400));//$midle_cnt;
				$outmas['2']['count_hosts']=intval($cnt_host);
				$outmas['2']['audience']=intval($value_count);
				$outmas['2']['uniq_auth']=count($mas_value);
				$outmas['2']['period']=$_POST['sd'].'-'.$_POST['ed'];
			
				//$time_mas[$hn][]
				$stime=date("H:i:s d.m.Y",$time);
				$ss_time=explode(' ',$stime);
				$outmas['3'][0][]=($ss_time[1]=='')?$ss_time[0]:$ss_time[1];
				$outmas['3'][1][]=($ss_time[1]=='')?'':$ss_time[0];
				$outmas['3'][2][]=iconv("UTF-8", "UTF-8//IGNORE", $hn);
				$outmas['3'][3][]=iconv("UTF-8", "UTF-8//IGNORE", $link);
				$outmas['3'][4][]=$type_re;
				$outmas['3'][5][]=iconv("UTF-8", "UTF-8//IGNORE", (($hn!='twitter.com')?preg_replace('/\s+/isu',' ',preg_replace('/[^а-яА-Яa-zA-ZёЁ\.\,\'\"0-9\!\?\s\/\@\:\(\)\“\_\-\&\;\#\<\>]/isu',' ',(($content_f=='')?$content:$content_f))):' '.preg_replace('/[^а-яА-Яa-zA-ZёЁ\.\,\'\"0-9\!\?\s\/\@\:\(\)\“\_\-\&\;\#\<\>]/isu',' ',$content)));
				$outmas['3'][6][]=$nstr;
				$outmas['3'][7][]=$isspam;
				$outmas['3'][8][]=$isfav;
				$outmas['3'][9][]=intval($eng);
				$outmas['3'][10][]=iconv("UTF-8", "UTF-8//IGNORE", $nick);
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
		//print_r($top_speakers);
		//die();
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
		$outmas['8']['all'][0][]=intval($count_neu);
		$outmas['8']['all'][0][]=intval($count_neg);
		$outmas['8']['all'][0][]=intval($count_pos);
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
			$keyinf=explode(':',$key);
			$top_speak['nick'][]=$keyinf[0];
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
			else
			if ($top_speak['src'][$key]=='mail.ru')
			{
				$t_speak[2][]=$top_speak['link'][$key];
			}
			else
			if ($top_speak['src'][$key]=='liveinternet.ru')
			{
				$t_speak[2][]=$top_speak['link'][$key];
			}
			else
			if ($top_speak['src'][$key]=='ya.ru')
			{
				$t_speak[2][]=$top_speak['link'][$key];
			}
			else
			if ($top_speak['src'][$key]=='yandex.ru')
			{
				$t_speak[2][]=$top_speak['link'][$key];
			}
			else
			if ($top_speak['src'][$key]=='rutwit.ru')
			{
				$t_speak[2][]=$top_speak['link'][$key];
			}
			else
			if ($top_speak['src'][$key]=='rutvit.ru')
			{
				$t_speak[2][]=$top_speak['link'][$key];
			}
			else
			if ($top_speak['src'][$key]=='babyblog.ru')
			{
				$t_speak[2][]=$top_speak['link'][$key];
			}
			else
			if ($top_speak['src'][$key]=='blog.ru')
			{
				$t_speak[2][]=$top_speak['link'][$key];
			}
			else
			if ($top_speak['src'][$key]=='foursquare.com')
			{
				$t_speak[2][]=$top_speak['link'][$key];
			}
			else
			if ($top_speak['src'][$key]=='kp.ru')
			{
				$t_speak[2][]=$top_speak['link'][$key];
			}
			else
			if ($top_speak['src'][$key]=='aif.ru')
			{
				$t_speak[2][]=$top_speak['link'][$key];
			}
			else
			if ($top_speak['src'][$key]=='friendfeed.com')
			{
				$t_speak[2][]=$top_speak['link'][$key];
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
			else
			if ($top_prom['src'][$key]=='mail.ru')
			{
				$t_prom[2][]=$top_prom['link'][$key];
			}
			else
			if ($top_prom['src'][$key]=='liveinternet.ru')
			{
				$t_prom[2][]=$top_prom['link'][$key];
			}
			else
			if ($top_prom['src'][$key]=='ya.ru')
			{
				$t_prom[2][]=$top_prom['link'][$key];
			}
			else
			if ($top_prom['src'][$key]=='yandex.ru')
			{
				$t_prom[2][]=$top_prom['link'][$key];
			}
			else
			if ($top_prom['src'][$key]=='rutwit.ru')
			{
				$t_prom[2][]=$top_prom['link'][$key];
			}
			else
			if ($top_prom['src'][$key]=='rutvit.ru')
			{
				$t_prom[2][]=$top_prom['link'][$key];
			}
			else
			if ($top_prom['src'][$key]=='babyblog.ru')
			{
				$t_prom[2][]=$top_prom['link'][$key];
			}
			else
			if ($top_prom['src'][$key]=='blog.ru')
			{
				$t_prom[2][]=$top_prom['link'][$key];
			}
			else
			if ($top_prom['src'][$key]=='foursquare.com')
			{
				$t_prom[2][]=$top_prom['link'][$key];
			}
			else
			if ($top_prom['src'][$key]=='kp.ru')
			{
				$t_prom[2][]=$top_prom['link'][$key];
			}
			else
			if ($top_prom['src'][$key]=='aif.ru')
			{
				$t_prom[2][]=$top_prom['link'][$key];
			}
			else
			if ($top_prom['src'][$key]=='friendfeed.com')
			{
				$t_prom[2][]=$top_prom['link'][$key];
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
		$outmas['2']['uniq_auth']=count($t_prom[0]);
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

			/*header("Content-type: application/vnd.ms-excel");
			header("Content-Disposition: attachment; filename=wobot-export.xls");*/
			//echo $fulltext;
			$idfls=date('s_i_G__n_j_Y',time());
			$fp = fopen('/var/www/production/export/'.$user['user_pass'].'_'.$idfls.'.xls', 'a');
			fwrite($fp, $fulltext);
			fclose($fp);
			$headers='noreply@wobot.ru';
			// $headers  = "From: noreply@wobot.ru\r\n"; 
			// $headers .= "Bcc: noreply@wobot.ru\r\n";
			// $headers .= "MIME-Version: 1.0" . "\r\n";
			// $headers .= "Content-type: text/html; charset=utf-8"."\r\n";
			$file_name=preg_replace('/[^а-яА-Яa-zA-Z\-\_0-9]/isu','_',($order['order_name']!=''?$order['order_name']:$order['order_keyword']));
			$file_name=preg_replace('/\_+/isu','_',$order['order_name']);
			if (mb_strlen($file_name,'UTF-8')>100)
			{
				$file_name=mb_substr($file_name,0,100,'UTF-8');
			}
			if ($user['user_email']!='')
			{
				// parseUrlmail('http://188.120.239.225/api/service/sendmail.php',$user['user_email'],'Команда Wobot',urlencode("<html><body>Спасибо за использование нашего сервиса, ваш отчет можно скачать по ссылке: <br><a href='http://production.wobot.ru/digest.php?token=".$user['user_pass']."&order_id=".$idfls."&type=".$_POST['format']."&name=wobot_".date('dmy',strtotime($_POST['stime'])).'_'.date('dmy',strtotime($_POST['etime'])).'_'.$file_name."'>ссылка</a></body></html>"),$headers);
				parseUrlmail('http://188.120.239.225/api/service/sendmail.php',$user['user_email'],'Экспорт отчета по теме: '.$order['order_name'],urlencode("<html><body>Уважаемый(-ая) ".$user['user_name'].",<br><br>Отчет по Вашей теме \"".$order['order_name']."\" готов к скачиванию: <a href='http://production.wobot.ru/digest.php?token=".$user['user_pass']."&order_id=".$idfls."&type=".$_POST['format']."&name=wobot_".date('dmy',strtotime($_POST['stime'])).'_'.date('dmy',strtotime($_POST['etime'])).'_'.$file_name."'>Скачать отчет</a><br><br> С уважением,<br>Поддержка Wobot<br><a href=\"mailto:mail@wobot.ru\">mail@wobot.ru</a><br><div><i><img src='http://www.wobot.ru/new/assets/logo.png'></i></div><br>Это письмо было сгенерировано автоматически, пожалуйста, не отвечайте на него. Если у Вас возникли вопросы, пожалуйста, присылайте их на адрес <a href=\"mailto:mail@wobot.ru\">mail@wobot.ru</a>.</body></html>"),$headers);
				// mail($user['user_email'], 'Команда Wobot', ('<html><body>Спасибо за использование нашего сервиса, ваш отчет можно скачать по ссылке: <br><a href="http://production.wobot.ru/digest.php?token='.$user['user_pass'].'&order_id='.$idfls.'&type='.$_POST['format'].'x&name=wobot_'.date('dmy',strtotime($_POST['stime'])).'_'.date('dmy',strtotime($_POST['etime'])).'_'.$file_name.'">ссылка</a></body></html>'), $headers); 
			}
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
			$pdf->Output('/var/www/production/export/'.$user['user_pass'].'_'.$idfls.'.pdf', 'F');
			$headers  = "noreply@wobot.ru\r\n"; 
			$headers .= "Bcc: noreply@wobot.ru\r\n";
			$headers .= 'MIME-Version: 1.0' . "\r\n";
			$headers .= 'Content-type: text/html; charset=utf-8'."\r\n";
			if ($user['user_email']!='')
			{
				parseUrlmail('http://wobot.ru/mail_send.php',$user['user_email'],'Команда Wobot',urlencode("<html><body>Спасибо за использование нашего сервиса, ваш отчет можно скачать по ссылке: <br><a href='http://production.wobot.ru/digest.php?token=".$user['user_pass']."&order_id=".$idfls."&type=".$_POST['format']."'>ссылка</a></body></html>"),$headers);
			}
			$fp = fopen('/var/www/production/export/'.$user['user_pass'].'_'.$idfls.'.xls', 'a');
		}
		elseif ($_POST['format']=='doc')
		{
			$exp.="<html>"."<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\">"."<body><table>"."<tr><td>Упоминаний</td><td>Ресурсов</td><td>Аудитория</td><td>Цитируемость</td><td>Вовлеченность</td><td>Доверие</td><td>Engagement</td></tr><tr><td>".$cnt."</td><td>".$cnt_host."</td><td>".$metrics['value']."</td><td>".((intval((1/($othert/250))*100)+intval((1/($metricss['value']/1000))*100))/100)."</td><td>".(intval($othert/$metricss['value']*100)/100)."</td><td>0</td><td>".$metrics['engagement']."</td></tr></table>".$echo_word;
			$exp.="</table></body>"."</html>";
			$idfls=date('s_i_G__n_j_Y',time());
			$fp = fopen('/var/www/production/export/'.$user['user_pass'].'_'.$idfls.'.doc', 'w');
			fwrite($fp, $exp);
			fclose($fp);
			$headers  = "noreply@wobot.ru\r\n"; 
			$headers .= "Bcc: noreply@wobot.ru\r\n";
			$headers .= 'MIME-Version: 1.0' . "\r\n";
			$headers .= 'Content-type: text/html; charset=utf-8'."\r\n";
			//echo $user['user_email'];
			if ($user['user_email']!='')
			{
				parseUrlmail('http://wobot.ru/mail_send.php',$user['user_email'],'Команда Wobot',urlencode("<html><body>Спасибо за использование нашего сервиса, ваш отчет можно скачать по ссылке: <br><a href='http://production.wobot.ru/digest.php?token=".$user['user_pass']."&order_id=".$idfls."&type=".$_POST['format']."'>ссылка</a></body></html>"),$headers);
			}
		}
		elseif ($_POST['format']=='odf')
		{
			$ods->addTable($table);
			//$ods->downloadOdsFile("wobot_".date('h:i:s d.m.Y').".ods");
			$idfls=date('s_i_G__n_j_Y',time());
			$ods->genOdsFile('/var/www/production/export/'.$user['user_pass'].'_'.$idfls.'.ods');
			$headers  = "noreply@wobot.ru\r\n"; 
			$headers .= "Bcc: noreply@wobot.ru\r\n";
			$headers .= 'MIME-Version: 1.0' . "\r\n";
			$headers .= 'Content-type: text/html; charset=utf-8'."\r\n";
			if ($user['user_email']!='')
			{
				parseUrlmail('http://wobot.ru/mail_send.php',$user['user_email'],'Команда Wobot',urlencode("<html><body>Спасибо за использование нашего сервиса, ваш отчет можно скачать по ссылке: <br><a href='http://production.wobot.ru/digest.php?token=".$user['user_pass']."&order_id=".$idfls."&type=ods'>ссылка</a></body></html>"),$headers);
			}
		}
		
	}
	//sleep(1);
//print_r($outmas);
//print_r($timet);
?>
