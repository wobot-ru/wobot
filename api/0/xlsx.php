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
require_once('/var/www/com/sent.php');
require_once('/var/www/new/com/porter.php');
require_once( '/var/www/new/com/phpmorphy/src/common.php');
// die(json_encode($_POST));
$dir = '/var/www/new/com/phpmorphy/dicts';
$lang = 'ru_RU';
$opts = array( 'storage' => PHPMORPHY_STORAGE_FILE );
$morphy = new phpMorphy($dir, $lang, $opts);
//error_reporting(0);

function engagesort($a, $b)
{
    if ($a['post_engage'] == $b['post_engage']) {
        return 0;
    }
    return ($a['post_engage'] < $b['post_engage']) ? 1 : -1;
}

function valuesort($a, $b)
{
    if ($a['blog_readers'] == $b['blog_readers']) {
        return 0;
    }
    return ($a['blog_readers'] < $b['blog_readers']) ? 1 : -1;
}

function postsort($a, $b)
{
    if ($a['count_post'] == $b['count_post']) {
        return 0;
    }
    return ($a['count_post'] < $b['count_post']) ? 1 : -1;
}

function valtagsort($a, $b)
{
    if ($a['val_tag'] == $b['val_tag']) {
        return 0;
    }
    return ($a['val_tag'] > $b['val_tag']) ? 1 : -1;
}

$word_stem=new Lingua_Stem_Ru();

date_default_timezone_set ( 'Europe/Moscow' );
/*
$_POST['order_id']='924';
$_POST['start']='01.07.2011';
$_POST['end']='01.07.2012';
$_POST['format']='xls';
*/
if (isset($_POST['start'])) $_POST['stime']=$_POST['start'];
if (isset($_POST['end'])) $_POST['etime']=$_POST['end'];

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
//ini_set("memory_limit", "16392M");
error_reporting(0);
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
// $user['tariff_id']=15;
// $user['user_id']=1187;
auth();
if (!$loged) die();

if ($user['tariff_id']==3)
{
	$infus=$db->query('SELECT order_id,user_id FROM blog_orders WHERE order_id='.$_POST['order_id'].' LIMIT 1');
	$usri=$db->fetch($infus);
	if ($usri['user_id']==61) $user['user_id']=61;
}
// $_POST['order_id']=2074;
// $_POST['start']='01.10.2012';
// $_POST['end']='11.11.2012';
// print_r($_POST);
// die();
//$_POST=$_GET;
if (intval($_POST['order_id'])==0) die();
//echo 'gg';
//print_r($_SESSION);

$res=$db->query("SELECT order_id,order_name,order_keyword,order_start,order_end,order_last,third_sources from blog_orders WHERE order_id=".intval($_POST['order_id'])." and user_id=".intval($user['user_id'])." LIMIT 1");
$order=$db->fetch($res);

$src=$order['order_src'];
$src=json_decode($src,true);
$data=$order['order_metrics'];
$metrics=json_decode($data,true);
$settings=json_decode($user['user_settings'],true);
if (intval($settings['export_digest'])==1) $outmas['export_digest']=1;
else $outmas['export_digest']=0;

$query1='SELECT * FROM blog_tag WHERE order_id='.intval($_POST['order_id']);
$respost1=$db->query($query1);
$iter=0;
while($tgl1 = $db->fetch($respost1))
{
	$tags_to_xlsx[$iter][]=$tgl1['tag_name'];
	$iter++;
	$tagsall[$tgl1['tag_tag']]=$tgl1['tag_name'];
	$tagsallrev[$tgl1['tag_name']]=$tgl1['tag_tag'];
	$mtag[$tgl1['tag_tag']]=$tgl1['tag_name'];
}
ksort($mtag);
ksort($tagsall);
$iter=0;
foreach ($tagsall as $tag_tag => $tag_name)
{
	$tags_to_xlsx2[$iter][]=$tag_name;
	$iter++;
}
$tags_to_xlsx=$tags_to_xlsx2;
$qqq=get_query();
// echo $qqq; die();
if ($user['tariff_id']==16) $qqq.=' LIMIT 100';
// echo $qqq;
// die();

if ($_POST['lang']==2)
{
	$assoc_type['res_micr']='микроблог';
	$assoc_type['soc_netw']='социальная сеть';
	$assoc_type['smi']='СМИ';
	$assoc_type['forabl']='форум или блог';
	$assoc_type['video_host']='видеохостинг';
	$assoc_type['other']='другое';
}
else
{
	$assoc_type['res_micr']='Microblog';
	$assoc_type['soc_netw']='Social network';
	$assoc_type['smi']='Media';
	$assoc_type['forabl']='Forum or blog';
	$assoc_type['video_host']='Video hosting';
	$assoc_type['other']='Others';
}

$qpost=$db->query($qqq);
while ($post=$db->fetch($qpost))
{
	$count_post++;
	if ($post['parent']!=0) $uniq_post[$post['parent']]++;
	if (!isset($uniq_blogs[$post['blog_id']])) 
	{
		// $uniq_blogs[$post['blog_id']]++;
		$audience+=$post['blog_readers'];
		$uniq_blogs[$post['blog_id']]=1;
	}
	$uniq_host[$post['post_host']]++;
	if (in_array($post['post_host'], $res_micr)) $type='res_micr';
	elseif (in_array($post['post_host'], $soc_netw)) $type='soc_netw';
	elseif (in_array($post['post_host'], $smi)) $type='smi';
	elseif (in_array($post['post_host'], $forabl)) $type='forabl';
	elseif (in_array($post['post_host'], $video_host)) $type='video_host';
	else $type='other';
	$mtype[$type]++;
	$engagement+=$post['post_engage'];
	$engagement_adv=json_decode($post['post_advengage'],true);
	$count_likes+=$engagement_adv['likes'];
	$count_comment+=$engagement_adv['comment'];
	$count_retweet+=$engagement_adv['retweet'];
	$count_reposts+=$engagement_adv['repost'];
	// if ($post['post_nastr']!=0)
	{
		$mnastr[$post['post_nastr']]++;
		$mnastr_host[$post['post_host']][$post['post_nastr']]++;
	}
	if ($post['blog_location']!='' && $wobot['destn1'][$post['blog_location']]!='') 
	{
		$mloc[$wobot['destn1'][$post['blog_location']]]++;
		$mloc_nastr[$wobot['destn1'][$post['blog_location']]][$post['post_nastr']]++;
	}
	else 
	{
		$mloc_null++;
		$mloc_null_nastr[$post['post_nastr']]++;
	}
	$mgen[$post['blog_gender']]++;
	$mgen_nastr[$post['blog_gender']][$post['post_nastr']]++;
	if ($post['blog_age']==0) 
	{
		$mage[0]++;
		$mage_nastr[0][$post['post_nastr']]++;
	}
	elseif ($post['blog_age']<=20)
	{
		$mage[1]++;
		$mage_nastr[1][$post['post_nastr']]++;
	}
	elseif ($post['blog_age']>=21 && $post['blog_age']<=30)
	{
		$mage[2]++;
		$mage_nastr[2][$post['post_nastr']]++;
	}
	elseif ($post['blog_age']>=31 && $post['blog_age']<=40)
	{
		$mage[3]++;
		$mage_nastr[3][$post['post_nastr']]++;
	}
	elseif ($post['blog_age']>40)
	{
		$mage[4]++;
		$mage_nastr[4][$post['post_nastr']]++;
	}
	$slice_time=mktime(0,0,0,date('n',$post['post_time']),date('j',$post['post_time']),date('Y',$post['post_time']));
	$din_post[$slice_time]++;
	if ($post['post_time']<$assoc_uniq_post[$post['parent']] && ($post['parent']!=0) && isset($assoc_uniq_post[$post['parent']])) $assoc_uniq_post[$post['parent']]=$slice_time;
	elseif ($post['parent']!=0) $assoc_uniq_post[$post['parent']]=$slice_time;
	$uniq_host_time[$slice_time][$post['post_host']]++;
	$mnastr_time[$slice_time][$post['post_nastr']]++;
	if (count($top_post_engage)<10)
	{
		$top_post_engage[]=$post;
		usort($top_post_engage, 'engagesort');
	}
	else
	{
		if (intval($post['post_engage'])>intval($top_post_engage[9]['post_engage']))
		{
			array_pop($top_post_engage);
			array_push($top_post_engage,$post);
			usort($top_post_engage, 'engagesort');
		}
	}
	if ($post['post_nastr']==-1)
	{
		if (count($top_post_value_negative)<10)
		{
			$top_post_value_negative[]=$post;
			usort($top_post_value_negative, 'valuesort');
		}
		else
		{
			if ($post['blog_readers']>$top_post_value_negative[9]['blog_readers'])
			{
				array_pop($top_post_value_negative);
				array_push($top_post_value_negative,$post);
				usort($top_post_value_negative, 'valuesort');
			}
		}
	}
	if ($post['post_nastr']==1)
	{
		if (count($top_post_value_positive)<10)
		{
			$top_post_value_positive[]=$post;
			usort($top_post_value_positive, 'valuesort');
		}
		else
		{
			if ($post['blog_readers']>$top_post_value_positive[9]['blog_readers'])
			{
				array_pop($top_post_value_positive);
				array_push($top_post_value_positive,$post);
				usort($top_post_value_positive, 'valuesort');
			}
		}
	}
	$all_post[0][]=$order['order_name'];
	$all_post[1][]=date('d.m.Y',$post['post_time']);
	$all_post[2][]=date('H:i:s',$post['post_time']);
	$all_post[3][]=$post['post_host'];
	$all_post[4][]=preg_replace('/\%/isu','',urldecode(urldecode($post['post_link'])));
	$all_post[5][]=$assoc_type[$type];
	$all_post[6][]=preg_replace('/[^а-яА-Яa-zA-Z0-9\.\,\/\?\!\+\-\+\_\'\"\:\;\&\(\)\*]/isu',' ',($post['ful_com_post']==''?$post['post_content']:$post['ful_com_post']));
	$all_post[7][]=($post['post_nastr']==0?'':($post['post_nastr']==1?'+':'-'));
	$all_post[8][]=($post['post_spam']==1?'+':'-');
	$all_post[9][]=($post['post_fav']==1?'+':'-');
	$all_post[10][]=$post['post_engage'];
	$all_post[11][]=$post['blog_nick'];
	$all_post[12][]=($post['blog_gender']==0?'-':($post['blog_gender']==1?'Ж':'М'));
	$all_post[13][]=intval($post['blog_age']);
	$all_post[14][]=intval($post['blog_readers']);
	$all_post[15][]=$wobot['destn1'][$post['blog_location']];
	$post_tags=explode(',', $post['post_tag']);
	// print_r($post_tag);
	$iter=0;
	$zap='';
	$text_tag='';
	foreach ($mtag as $ktag => $itag)
	{
		if (in_array($ktag, $post_tags))
		{
			$posts_tags[$iter][]=$itag;
			$text_tag.=$zap.$itag;
			$zap=',';
		}
		else
		{
			$posts_tags[$iter][]='';
		}
		$iter++;
	}
	$all_post[16][]=$text_tag;
	$all_post[17][]=mb_substr($post['post_content'],0,300,'UTF-8');
	foreach ($post_tags as $ktag => $itag)
	{
		if ($tagsall[$itag]=='') continue;
		$mtag_all[$tagsall[$itag]][intval($post['post_nastr'])]++;
		$mtag_all[$tagsall[$itag]]['val_tag']++;
		$mtag_all[$tagsall[$itag]]['tag_name']=$tagsall[$itag];
	}
	if (($post['blog_id']==0) || ($post['blog_nick']=='')) continue;
	if ($post['post_host']=='livejournal.com')
	{
		if ($post['blog_id']!=0) $blog_name=iconv("UTF-8", "UTF-8//IGNORE", 'http://'.$post['blog_login'].'.livejournal.com/');
		else
		{
			$regex='/http\:\/\/(?<nick>.*?)\./isu';
			preg_match_all($regex,$post['post_link'],$out);
			$blog_name=iconv("UTF-8", "UTF-8//IGNORE", 'http://'.$out['nick'][0].'.livejournal.com/');
		}
	}
	else
	if (($post['post_host']=='vk.com') || ($post['post_host']=='vkontakte.ru')) $blog_name='http://vk.com/'.($post['blog_login'][0]=='-'?'club'.mb_substr($post['blog_login'],1,mb_strlen($post['blog_login'],'UTF-8')-1,'UTF-8'):'id'.$post['blog_login']);
	elseif ($post['post_host']=='twitter.com') $blog_name='http://twitter.com/'.$post['blog_login'];
	elseif ($post['post_host']=='facebook.com')	$blog_name='http://facebook.com/'.$post['blog_login'];
	elseif ($post['post_host']=='mail.ru') $blog_name='http://blogs.'.$post['blog_link'].'/'.$post['blog_login'];
	elseif ($post['post_host']=='liveinternet.ru') $blog_name='http://liveinternet.ru/users/'.$post['blog_login'];
	elseif ($post['post_host']=='ya.ru') $blog_name='http://'.$post['blog_login'].'.ya.ru';
	elseif ($post['post_host']=='yandex.ru') $blog_name='http://'.$post['blog_login'].'.ya.ru';
	elseif ($post['post_host']=='rutwit.ru') $blog_name='http://rutwit.ru/'.$post['blog_login'];
	elseif ($post['post_host']=='rutvit.ru') $blog_name='http://rutwit.ru/'.$post['blog_login'];
	elseif ($post['post_host']=='babyblog.ru') $blog_name='http://www.babyblog.ru/user/info/'.$post['blog_login'];
	elseif ($post['post_host']=='blog.ru') $blog_name='http://'.$post['blog_login'].'.blog.ru/profile';
	elseif ($post['post_host']=='foursquare.com') $blog_name='https://ru.foursquare.com/'.$post['blog_login'];
	elseif ($post['post_host']=='kp.ru') $blog_name='http://blog.kp.ru/users/'.$post['blog_login'].'/profile/';
	elseif ($post['post_host']=='aif.ru') $blog_name='http://blog.aif.ru/users/'.$post['blog_login'].'/profile';
	elseif ($post['post_host']=='friendfeed.com') $blog_name='http://friendfeed.com/'.$post['blog_login'];
	elseif ($post['post_host']=='google.com') $blog_name='http://plus.google.com/'.$post['blog_login'].'/about';
	$blogs_all[$post['blog_id']]['blog_name']=$post['blog_nick'];
	$blogs_all[$post['blog_id']]['blog_link']=$blog_name;
	$blogs_all[$post['blog_id']]['count_post']++;
	$blogs_all[$post['blog_id']]['nastr'][$post['post_nastr']]++;
	$blogs_all[$post['blog_id']]['blog_host']=$post['blog_link'];
	$blogs_all[$post['blog_id']]['blog_gender']=($post['blog_gender']==0?'-':($post['blog_gender']==1?'Ж':'М'));
	$blogs_all[$post['blog_id']]['blog_age']=$post['blog_age'];
	$blogs_all[$post['blog_id']]['blog_location']=$wobot['destn1'][$post['blog_location']];
	$blogs_all[$post['blog_id']]['blog_readers']=$post['blog_readers'];
	$blogs_all[$post['blog_id']]['post_engage']+=$post['post_engage'];
}
// die();
// die(json_encode($mgen));

$iter=0;
for ($t=strtotime($_POST['start']);$t<=strtotime($_POST['end']);$t=mktime(0,0,0,date('n',$t),date('j',$t)+1,date('Y',$t)))
{
	if (intval($din_post[$t])==0) $din_post[$t]=0;
	if (intval($uniq_host_time[$t])==0) $uniq_host_time[$t]=array();
	if (intval($mnastr_time[$t])==0) $mnastr_time[$t]=array();
}
ksort($din_post);
ksort($uniq_host_time);
ksort($mnastr_time);
arsort($uniq_host);
arsort($mloc);
// print_r($uniq_host);

foreach ($assoc_uniq_post as $parent => $slice_time)
{
	$din_uniq_post[$slice_time]++;
}

// die(json_encode($din_post));

foreach ($din_post as $key => $item)
{
	$din_post2[0][]=date('d.m.Y',$key);
	$din_post2[1][]=(string)intval($item);
	$din_post2[2][]=(string)intval($din_uniq_post[$key]);
	$iter++;
}

$iter=1;
foreach ($uniq_host as $host => $count)
{
	if ($iter==4) break;
	// echo $host."\n";
	if ($iter==1) $din_top_host[0][]='Дата:';
	$din_top_host[$iter][]=$host;
	$iter_din_top_host=1;
	foreach ($uniq_host_time as $time => $time_info)
	{
		if ($iter==1) $din_top_host[0][]=date('d.m.Y',$time);
		$din_top_host[$iter][]=(string)intval($time_info[$host]);
		$iter_din_top_host++;
	}
	$iter++;
}
// die(json_encode($din_top_host));
$iter=1;
if ($_POST['lang']==2)
{
	$din_nastr[0][0]='Дата:';
	$din_nastr[1][0]='Негативные';
	$din_nastr[2][0]='Нейтральные';
	$din_nastr[3][0]='Позитивные';
	$din_nastr[4][0]='Негативные';
	$din_nastr[5][0]='Нейтральные';
	$din_nastr[6][0]='Позитивные';
}
else
{
	$din_nastr[0][0]='Days:';
	$din_nastr[1][0]='Negative';
	$din_nastr[2][0]='Neutral';
	$din_nastr[3][0]='Positive';
	$din_nastr[4][0]='Negative';
	$din_nastr[5][0]='Neutral';
	$din_nastr[6][0]='Positive';
}
foreach ($mnastr_time as $time => $time_info)
{
	$din_nastr[0][]=date('d.m.Y',$time);
	$din_nastr[1][]=(string)intval($time_info[-1]);
	$din_nastr[2][]=(string)intval($time_info[0]);
	$din_nastr[3][]=(string)intval($time_info[1]);
	$din_nastr[4][]=intval($time_info[-1]*100/($time_info[-1]+$time_info[0]+$time_info[1]));
	$din_nastr[5][]=intval($time_info[0]*100/($time_info[-1]+$time_info[0]+$time_info[1]));
	$din_nastr[6][]=intval($time_info[1]*100/($time_info[-1]+$time_info[0]+$time_info[1]));
	$iter++;
}

if ($_POST['lang']==2)
{
	$nastr_all[0][0]='';
	$nastr_all[1][0]='Кол-во';
	$nastr_all[0][1]='Нейтральные';
	$nastr_all[1][1]=(string)intval($mnastr[0]);
	$nastr_all[0][2]='Негативные';
	$nastr_all[1][2]=(string)intval($mnastr[-1]);
	$nastr_all[0][3]='Позитивные';
	$nastr_all[1][3]=(string)intval($mnastr[1]);
}
else
{
	$nastr_all[0][0]='';
	$nastr_all[1][0]='Count';
	$nastr_all[0][1]='Neutral';
	$nastr_all[1][1]=(string)intval($mnastr[0]);
	$nastr_all[0][2]='Negative';
	$nastr_all[1][2]=(string)intval($mnastr[-1]);
	$nastr_all[0][3]='Positive';
	$nastr_all[1][3]=(string)intval($mnastr[1]);
}

$iter=1;
if ($_POST['lang']==2)
{
	$mhost[0][]='Ресурс';
	$mhost[1][]='Кол-во';
	$mhost_proc_nastr[0][]='';
	$mhost_proc_nastr[1][]='Негативные';
	$mhost_proc_nastr[2][]='Нейтральные';
	$mhost_proc_nastr[3][]='Позитивные';
	$mhost_proc_nastr[4][]='Негативные';
	$mhost_proc_nastr[5][]='Нейтральные';
	$mhost_proc_nastr[6][]='Позитивные';
}
else
{
	$mhost[0][]='Resources:';
	$mhost[1][]='Count:';
	$mhost_proc_nastr[0][]='';
	$mhost_proc_nastr[1][]='Negative';
	$mhost_proc_nastr[2][]='Neutral';
	$mhost_proc_nastr[3][]='Positive';
	$mhost_proc_nastr[4][]='Negative';
	$mhost_proc_nastr[5][]='Neutral';
	$mhost_proc_nastr[6][]='Positive';
}
foreach ($uniq_host as $host => $count)
{
	if ($iter<=5)
	{
		$mhost_proc[0][]=$host;
		$mhost_proc[1][]=(string)intval($count*100/$count_post);
		$mhost_proc_nastr2[0][]=$host;
		$mhost_proc_nastr2[1][]=(string)intval($mnastr_host[$host][-1]);
		$mhost_proc_nastr2[2][]=(string)intval($mnastr_host[$host][0]);
		$mhost_proc_nastr2[3][]=(string)intval($mnastr_host[$host][1]);
		$mhost_proc_nastr2[4][]=(string)intval($mnastr_host[$host][-1]*100/($mnastr_host[$host][-1]+$mnastr_host[$host][0]+$mnastr_host[$host][1])).'%';
		$mhost_proc_nastr2[5][]=(string)intval($mnastr_host[$host][0]*100/($mnastr_host[$host][-1]+$mnastr_host[$host][0]+$mnastr_host[$host][1])).'%';
		$mhost_proc_nastr2[6][]=(string)intval($mnastr_host[$host][1]*100/($mnastr_host[$host][-1]+$mnastr_host[$host][0]+$mnastr_host[$host][1])).'%';
	}
	else
	{
		$count_other+=$count;
	}
	$mhost[0][]=$host;
	$mhost[1][]=$count;
	$iter++;
}
if ($_POST['lang']==2) $mhost_proc[0][]='Другие';
else $mhost_proc[0][]='Other';
$mhost_proc[1][]=(string)intval($count_other*100/$count_post);
$mhost_proc_nastr2[0]=array_reverse($mhost_proc_nastr2[0]);
$mhost_proc_nastr2[1]=array_reverse($mhost_proc_nastr2[1]);
$mhost_proc_nastr2[2]=array_reverse($mhost_proc_nastr2[2]);
$mhost_proc_nastr2[3]=array_reverse($mhost_proc_nastr2[3]);
$mhost_proc_nastr2[4]=array_reverse($mhost_proc_nastr2[4]);
$mhost_proc_nastr2[5]=array_reverse($mhost_proc_nastr2[5]);
$mhost_proc_nastr2[6]=array_reverse($mhost_proc_nastr2[6]);
foreach ($mhost_proc_nastr2[0] as $key => $item)
{
	$mhost_proc_nastr[0][]=$item;
	$mhost_proc_nastr[1][]=$mhost_proc_nastr2[1][$key];
	$mhost_proc_nastr[2][]=$mhost_proc_nastr2[2][$key];
	$mhost_proc_nastr[3][]=$mhost_proc_nastr2[3][$key];
	$mhost_proc_nastr[4][]=$mhost_proc_nastr2[4][$key];
	$mhost_proc_nastr[5][]=$mhost_proc_nastr2[5][$key];
	$mhost_proc_nastr[6][]=$mhost_proc_nastr2[6][$key];

}
// die(json_encode($mhost_proc_nastr));
$iter=1;
if ($_POST['lang']==2)
{
	$mtype_all[0][]='Тип ресурса';
	$mtype_all[1][]='%';
	$mtype_all[0][]='Социальная сеть';
	$mtype_all[1][]=(string)intval($mtype['soc_netw']*100/$count_post);
	$mtype_all[0][]='Микроблог';
	$mtype_all[1][]=(string)intval($mtype['res_micr']*100/$count_post);
	$mtype_all[0][]='СМИ';
	$mtype_all[1][]=(string)intval($mtype['smi']*100/$count_post);
	$mtype_all[0][]='Форум или блог';
	$mtype_all[1][]=(string)intval($mtype['forabl']*100/$count_post);
	$mtype_all[0][]='Видеохостинг';
	$mtype_all[1][]=(string)intval($mtype['video_host']*100/$count_post);
	$mtype_all[0][]='Другие';
	$mtype_all[1][]=(string)intval($mtype['other']*100/$count_post);
}
else
{
	$mtype_all[0][]='Types of sources';
	$mtype_all[1][]='%';
	$mtype_all[0][]='Social network';
	$mtype_all[1][]=(string)intval($mtype['soc_netw']*100/$count_post);
	$mtype_all[0][]='Microblog';
	$mtype_all[1][]=(string)intval($mtype['res_micr']*100/$count_post);
	$mtype_all[0][]='Media';
	$mtype_all[1][]=(string)intval($mtype['smi']*100/$count_post);
	$mtype_all[0][]='Forum or blog';
	$mtype_all[1][]=(string)intval($mtype['forabl']*100/$count_post);
	$mtype_all[0][]='Video hosting';
	$mtype_all[1][]=(string)intval($mtype['video_host']*100/$count_post);
	$mtype_all[0][]='Other';
	$mtype_all[1][]=(string)intval($mtype['other']*100/$count_post);
}

$iter=1;
if ($_POST['lang']==2)
{
	$mloc_all[0][]='Город';
	$mloc_all[1][]='Кол-во';
	$mloc_all_proc[0][]='Город';
	$mloc_all_proc[1][]='Кол-во';
	$mloc_all_proc[2][]='Позитивные';
	$mloc_all_proc[3][]='Негативные';
	$mloc_all_proc[4][]='Нейтральные';
}
else
{
	$mloc_all[0][]='City';
	$mloc_all[1][]='Count';
	$mloc_all_proc[0][]='City';
	$mloc_all_proc[1][]='Count';
	$mloc_all_proc[2][]='Positive';
	$mloc_all_proc[3][]='Negative';
	$mloc_all_proc[4][]='Neutral';
}
$mloc_all[0][]=1;
$mloc_all[1][]=1;
$mloc_all[0][]=1;
$mloc_all[1][]=1;
// die(json_encode($mloc_all));
foreach ($mloc as $loc => $count)
{
	if ($iter<11)
	{
		$mloc_all2[0][]=$loc;
		$mloc_all2[1][]=(string)intval($count);
	}
	else
	{
		$count_other_loc+=$count;
	}
	$mloc_all_proc[0][]=$loc;
	$mloc_all_proc[1][]=(string)intval($count);
	$mloc_all_proc[2][]=(string)intval($mloc_nastr[$loc][1]);
	$mloc_all_proc[3][]=(string)intval($mloc_nastr[$loc][-1]);
	$mloc_all_proc[4][]=(string)intval($mloc_nastr[$loc][0]);
	$iter++;
}
if ($_POST['lang']==2)
{
	$mloc_all[0][1]='Другие';
	$mloc_all[1][1]=(string)intval($count_other_loc);
	$mloc_all[0][2]='Не определено';
}
else
{
	$mloc_all[0][1]='Other';
	$mloc_all[1][1]=(string)intval($count_other_loc);
	$mloc_all[0][2]='Unknown';
}
$mloc_all[1][2]=(string)intval($mloc_null);
$mloc_all2[0]=array_reverse($mloc_all2[0]);
$mloc_all2[1]=array_reverse($mloc_all2[1]);
foreach ($mloc_all2[0] as $key => $item)
{
	$mloc_all[0][]=$item;
	$mloc_all[1][]=$mloc_all2[1][$key];
}
// die(json_encode($mloc_all2));
if ($_POST['lang']==2) $mloc_all_proc[0][]='Не определено';
else $mloc_all_proc[0][]='Unknown';
$mloc_all_proc[1][]=(string)intval($mloc_null);
$mloc_all_proc[2][]=(string)intval($mloc_null_nastr[1]);
$mloc_all_proc[3][]=(string)intval($mloc_null_nastr[-1]);
$mloc_all_proc[4][]=(string)intval($mloc_null_nastr[0]);

usort($mtag_all, 'valtagsort');
// die(json_encode($mtag_all));
if ($_POST['lang']==2)
{
	$mtag_all2[0][]='Теги';
	$mtag_all2[1][]='Негативные';
	$mtag_all2[2][]='Нейтральные';
	$mtag_all2[3][]='Позитивные';
	$mtag_all2[4][]='Всего';
}
else
{
	$mtag_all2[0][]='Tags';
	$mtag_all2[1][]='Negative';
	$mtag_all2[2][]='Neutral';
	$mtag_all2[3][]='Positive';
	$mtag_all2[4][]='Total';
}
foreach ($mtag_all as $ktag => $itag)
{
	$mtag_all2[0][]=$itag['tag_name'];
	$mtag_all2[1][]=intval($itag[-1]);
	$mtag_all2[2][]=intval($itag[0]);
	$mtag_all2[3][]=intval($itag[1]);
	$mtag_all2[4][]=intval($itag['val_tag']);
}
if (count($mtag_all2[0])==1)
{
	$mtag_all2[0][]='';
	$mtag_all2[1][]='';
	$mtag_all2[2][]='';
	$mtag_all2[3][]='';
	$mtag_all2[4][]='';
}

if ($_POST['lang']==2)
{
	$mgen_all[0][]='Пол';
	$mgen_all[1][]='Положительные';
	$mgen_all[2][]='Нейтральные';
	$mgen_all[3][]='Негативные';
	$mgen_all[4][]='Количество упоминаний';
	$mgen_all[0][]='Женщины';
	$mgen_all[1][]=(string)intval($mgen_nastr[1][1]);
	$mgen_all[2][]=(string)intval($mgen_nastr[1][0]);
	$mgen_all[3][]=(string)intval($mgen_nastr[1][-1]);
	$mgen_all[4][]=(string)intval($mgen[1]);
	$mgen_all[0][]='Мужчины';
	$mgen_all[1][]=(string)intval($mgen_nastr[2][1]);
	$mgen_all[2][]=(string)intval($mgen_nastr[2][0]);
	$mgen_all[3][]=(string)intval($mgen_nastr[2][-1]);
	$mgen_all[4][]=(string)intval($mgen[2]);
	$mgen_all[0][]='Неопределено';
	$mgen_all[1][]=(string)intval($mgen_nastr[0][1]);
	$mgen_all[2][]=(string)intval($mgen_nastr[0][0]);
	$mgen_all[3][]=(string)intval($mgen_nastr[0][-1]);
	$mgen_all[4][]=(string)intval($mgen[0]);

	$mage_all[0][]='Возраст';
	$mage_all[1][]='Положительные';
	$mage_all[2][]='Нейтральные';
	$mage_all[3][]='Негативные';
	$mage_all[4][]='Количество упоминаний';
	$mage_all[0][]='До 20 лет';
	$mage_all[1][]=(string)intval($mage_nastr[1][1]);
	$mage_all[2][]=(string)intval($mage_nastr[1][0]);
	$mage_all[3][]=(string)intval($mage_nastr[1][-1]);
	$mage_all[4][]=(string)intval($mage[1]);
	$mage_all[0][]='От 21 до 30 лет';
	$mage_all[1][]=(string)intval($mage_nastr[2][1]);
	$mage_all[2][]=(string)intval($mage_nastr[2][0]);
	$mage_all[3][]=(string)intval($mage_nastr[2][-1]);
	$mage_all[4][]=(string)intval($mage[2]);
	$mage_all[0][]='От 31 до 40 лет';
	$mage_all[1][]=(string)intval($mage_nastr[3][1]);
	$mage_all[2][]=(string)intval($mage_nastr[3][0]);
	$mage_all[3][]=(string)intval($mage_nastr[3][-1]);
	$mage_all[4][]=(string)intval($mage[3]);
	$mage_all[0][]='Старше 40 лет';
	$mage_all[1][]=(string)intval($mage_nastr[4][1]);
	$mage_all[2][]=(string)intval($mage_nastr[4][0]);
	$mage_all[3][]=(string)intval($mage_nastr[4][-1]);
	$mage_all[4][]=(string)intval($mage[4]);
	$mage_all[0][]='Неопределено';
	$mage_all[1][]=(string)intval($mage_nastr[0][1]);
	$mage_all[2][]=(string)intval($mage_nastr[0][0]);
	$mage_all[3][]=(string)intval($mage_nastr[0][-1]);
	$mage_all[4][]=(string)intval($mage[0]);
}
else
{
	$mgen_all[0][]='Gender';
	$mgen_all[1][]='Positive';
	$mgen_all[2][]='Neutral';
	$mgen_all[3][]='Negative';
	$mgen_all[4][]='Count mentions';
	$mgen_all[0][]='Female';
	$mgen_all[1][]=(string)intval($mgen_nastr[1][1]);
	$mgen_all[2][]=(string)intval($mgen_nastr[1][0]);
	$mgen_all[3][]=(string)intval($mgen_nastr[1][-1]);
	$mgen_all[4][]=(string)intval($mgen[1]);
	$mgen_all[0][]='Male';
	$mgen_all[1][]=(string)intval($mgen_nastr[2][1]);
	$mgen_all[2][]=(string)intval($mgen_nastr[2][0]);
	$mgen_all[3][]=(string)intval($mgen_nastr[2][-1]);
	$mgen_all[4][]=(string)intval($mgen[2]);
	$mgen_all[0][]='Unknown';
	$mgen_all[1][]=(string)intval($mgen_nastr[0][1]);
	$mgen_all[2][]=(string)intval($mgen_nastr[0][0]);
	$mgen_all[3][]=(string)intval($mgen_nastr[0][-1]);
	$mgen_all[4][]=(string)intval($mgen[0]);

	$mage_all[0][]='Age';
	$mage_all[1][]='Positive';
	$mage_all[2][]='Neutral';
	$mage_all[3][]='Negative';
	$mage_all[4][]='Count mentions';
	$mage_all[0][]='Under 20 years';
	$mage_all[1][]=(string)intval($mage_nastr[1][1]);
	$mage_all[2][]=(string)intval($mage_nastr[1][0]);
	$mage_all[3][]=(string)intval($mage_nastr[1][-1]);
	$mage_all[4][]=(string)intval($mage[1]);
	$mage_all[0][]='From 21 till 30 years';
	$mage_all[1][]=(string)intval($mage_nastr[2][1]);
	$mage_all[2][]=(string)intval($mage_nastr[2][0]);
	$mage_all[3][]=(string)intval($mage_nastr[2][-1]);
	$mage_all[4][]=(string)intval($mage[2]);
	$mage_all[0][]='From 31 till 40 years';
	$mage_all[1][]=(string)intval($mage_nastr[3][1]);
	$mage_all[2][]=(string)intval($mage_nastr[3][0]);
	$mage_all[3][]=(string)intval($mage_nastr[3][-1]);
	$mage_all[4][]=(string)intval($mage[3]);
	$mage_all[0][]='Over 40 years';
	$mage_all[1][]=(string)intval($mage_nastr[4][1]);
	$mage_all[2][]=(string)intval($mage_nastr[4][0]);
	$mage_all[3][]=(string)intval($mage_nastr[4][-1]);
	$mage_all[4][]=(string)intval($mage[4]);
	$mage_all[0][]='Unknown';
	$mage_all[1][]=(string)intval($mage_nastr[0][1]);
	$mage_all[2][]=(string)intval($mage_nastr[0][0]);
	$mage_all[3][]=(string)intval($mage_nastr[0][-1]);
	$mage_all[4][]=(string)intval($mage[0]);	
}
foreach ($top_post_engage as $post)
{
	$i++;
	$outmas[1]['top_post'][0][]=$i;
	$outmas[1]['top_post'][1][]=date('d.m.Y',$post['post_time']);
	$outmas[1]['top_post'][2][]=date('H:i:s',$post['post_time']);
	$outmas[1]['top_post'][3][]=preg_replace('/[^а-яА-Яa-zA-Z0-9\.\,\/\?\!\+\-\+\_]/isu',' ',$post['post_content']);
	$outmas[1]['top_post'][4][]=$post['post_link'];
	$outmas[1]['top_post'][5][]=intval($post['post_engage']);
	$outmas[1]['top_post'][6][]=intval($post['blog_readers']);
}

$i=0;
foreach ($top_post_value_positive as $post)
{
	$i++;
	$outmas[1]['top_post_positive'][0][]=$i;
	$outmas[1]['top_post_positive'][1][]=date('d.m.Y',$post['post_time']);
	$outmas[1]['top_post_positive'][2][]=date('H:i:s',$post['post_time']);
	$outmas[1]['top_post_positive'][3][]=preg_replace('/[^а-яА-Яa-zA-Z0-9\.\,\/\?\!\+\-\+\_]/isu',' ',$post['post_content']);
	$outmas[1]['top_post_positive'][4][]=$post['post_link'];
	$outmas[1]['top_post_positive'][5][]=intval($post['post_engage']);
	$outmas[1]['top_post_positive'][6][]=intval($post['blog_readers']);
}
if (count($outmas[1]['top_post_positive'][0])==0)
{
	$outmas[1]['top_post_positive'][0][]='';
	$outmas[1]['top_post_positive'][1][]='';
	$outmas[1]['top_post_positive'][2][]='';
	$outmas[1]['top_post_positive'][3][]='';
	$outmas[1]['top_post_positive'][4][]='';
	$outmas[1]['top_post_positive'][5][]='';
	$outmas[1]['top_post_positive'][6][]='';
}

$i=0;
foreach ($top_post_value_negative as $post)
{
	$i++;
	$outmas[1]['top_post_negative'][0][]=$i;
	$outmas[1]['top_post_negative'][1][]=date('d.m.Y',$post['post_time']);
	$outmas[1]['top_post_negative'][2][]=date('H:i:s',$post['post_time']);
	$outmas[1]['top_post_negative'][3][]=preg_replace('/[^а-яА-Яa-zA-Z0-9\.\,\/\?\!\+\-\+\_]/isu',' ',$post['post_content']);
	$outmas[1]['top_post_negative'][4][]=$post['post_link'];
	$outmas[1]['top_post_negative'][5][]=intval($post['post_engage']);
	$outmas[1]['top_post_negative'][6][]=intval($post['blog_readers']);
}
if (count($outmas[1]['top_post_negative'][0])==0)
{
	$outmas[1]['top_post_negative'][0][]='';
	$outmas[1]['top_post_negative'][1][]='';
	$outmas[1]['top_post_negative'][2][]='';
	$outmas[1]['top_post_negative'][3][]='';
	$outmas[1]['top_post_negative'][4][]='';
	$outmas[1]['top_post_negative'][5][]='';
	$outmas[1]['top_post_negative'][6][]='';
}

	// $blogs_all[$post['blog_id']]['blog_name']=$post['blog_name'];
	// $blogs_all[$post['blog_id']]['blog_link']=$blog_name;
	// $blogs_all[$post['blog_id']]['count_post']++;
	// $blogs_all[$post['blog_id']]['nastr'][$post['post_nastr']]++;
	// $blogs_all[$post['blog_id']]['blog_link']=$post['blog_link'];
	// $blogs_all[$post['blog_id']]['blog_gender']=$post['blog_gender'];
	// $blogs_all[$post['blog_id']]['blog_age']=$post['blog_age'];
	// $blogs_all[$post['blog_id']]['blog_location']=$wobot['destn1'][$post['blog_link']];
	// $blogs_all[$post['blog_id']]['blog_readers']=$post['blog_readers'];
	// $blogs_all[$post['blog_id']]['blog_engage']+=$post['post_engage'];

$iter=0;
usort($blogs_all, 'postsort');
foreach ($blogs_all as $blog_id => $blog)
{
	$iter++;
	$outmas[4]['blogs'][0][]=$iter;
	$outmas[4]['blogs'][1][]=$blog['blog_name'];
	$outmas[4]['blogs'][2][]=$blog['blog_link'];
	$outmas[4]['blogs'][3][]=$blog['count_post'];
	$outmas[4]['blogs'][4][]=intval($blog['nastr'][1]);
	$outmas[4]['blogs'][5][]=intval($blog['nastr'][-1]);
	$outmas[4]['blogs'][6][]=intval($blog['nastr'][0]);
	$outmas[4]['blogs'][7][]=$blog['blog_host'];
	$outmas[4]['blogs'][8][]=$blog['blog_gender'];
	$outmas[4]['blogs'][9][]=intval($blog['blog_age']);
	$outmas[4]['blogs'][10][]=$blog['blog_location'];
	$outmas[4]['blogs'][11][]=intval($blog['blog_readers']);
	$outmas[4]['blogs'][12][]=intval($blog['post_engage']);
}

$outmas[2]['din_post']=$din_post2;
$outmas[2]['din_top_host']=$din_top_host;
$outmas[2]['din_nastr']=$din_nastr;
$outmas[2]['nastr_all']=$nastr_all;
$outmas[2]['mhost_proc_nastr']=$mhost_proc_nastr;
$outmas[2]['mhost_proc']=$mhost_proc;
$outmas[2]['mhost']=$mhost;
$outmas[2]['mtype_all']=$mtype_all;
$outmas[2]['mloc_all']=$mloc_all;
$outmas[2]['mloc_all_proc']=$mloc_all_proc;
$outmas[2]['mtag_all']=$mtag_all2;
$outmas[2]['mgen_all']=$mgen_all;
$outmas[2]['mage_all']=$mage_all;
$outmas[1]['order_name']=$order['order_name'];
$outmas[1]['order_keyword']=$order['order_keyword'];
$outmas[1]['start']=$_POST['start'];
$outmas[1]['end']=$_POST['end'];
$outmas[1]['count_post']=$count_post;
$outmas[1]['uniq_post']=count($uniq_post);
$outmas[1]['count_blogs']=count($uniq_blogs);
$outmas[1]['post_per_day']=intval($count_post/((strtotime($_POST['end'])-strtotime($_POST['start']))/86400));
$outmas[1]['count_host']=count($uniq_host);
$outmas[1]['audience']=$audience;
$outmas[1]['engage']=$count_likes+$count_comment+$count_retweet;
$outmas[1]['count_likes']=$count_likes;
$outmas[1]['count_comment']=$count_comment;
$outmas[1]['count_retweet']=$count_retweet;
$outmas[1]['count_reposts']=$count_reposts;
$outmas[1]['engage']=$count_likes+$count_comment+$count_retweet+$count_reposts;
$outmas[1]['nastr']=$mnastr[-1]+$mnastr[1];
$outmas[1]['positive']=intval($mnastr[1]);
$outmas[1]['neutral']=intval($mnastr[0]);
$outmas[1]['negative']=intval($mnastr[-1]);
$outmas[3]['all_post']=$all_post;
$outmas[3]['posts_tags']=$posts_tags;
$outmas[3]['digest']=intval($settings['export_digest']);
$outmas[3]['tags_to_xlsx']=$tags_to_xlsx;
$outmas[3]['export_digest']=intval($settings['export_digest']);
$outmas['analytics']=$_POST['analytics'];
$outmas['mentions']=$_POST['mentions'];
$outmas['authors']=$_POST['authors'];
		// echo json_encode($outmas[3]);
		// die(json_encode($outmas[2]['mtag_all']));

		$descriptorspec=array(
			0 => array("pipe","r"),
			1 => array("pipe","w"),
			2 => array("file", "/tmp/error-output.txt", "a")
			);

		$cwd='/var/www/new/modules';
		$end=array('');
		//$pipes=json_encode($outmas);
		$process=proc_open('perl /var/www/project/excel/xlsx'.($_POST['lang']==2?'':'_en').'.pl',$descriptorspec,$pipes,$cwd,$end);
		//echo "\n".$row['post_link']."\n";
		if (is_resource($process))
		{
			fwrite($pipes[0], json_encode($outmas));
			fclose($pipes[0]);
			//echo $return_value;
			//print_r($pipes);
			$fulltext=stream_get_contents($pipes[1]);
			$return_value=proc_close($process);
			$file_name=preg_replace('/[^а-яА-Яa-zA-Z\-\_0-9]/isu','_',($order['order_name']!=''?$order['order_name']:$order['order_keyword']));
			$file_name=preg_replace('/\_+/isu','_',$file_name);
			if (mb_strlen($file_name,'UTF-8')>100)
			{
				$file_name=mb_substr($file_name,0,100,'UTF-8');
			}
			header("Content-type: application/vnd.ms-excel");
//echo preg_replace('/[^а-яА-Яa-zA-Z\-\_0-9]/isu','_',$order['order_name']);
//echo "Content-Disposition: attachment; filename=wobot_".date('dmy',strtotime($_POST['stime'])).'_'.date('dmy',strtotime($_POST['etime'])).'_'.$file_name.".xls";
//die();
			header("Content-Disposition: attachment; filename=wobot_".
date('dmy',strtotime($_POST['stime'])).'_'.date('dmy',strtotime($_POST['etime'])).'_'.$file_name.".xlsx");
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


?>
