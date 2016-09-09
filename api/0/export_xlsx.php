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

define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');
require_once ('/var/www/project/excel/PHPExcel/Classes/PHPExcel.php');

$dir = '/var/www/new/com/phpmorphy/dicts';
$lang = 'ru_RU';
$opts = array( 'storage' => PHPMORPHY_STORAGE_FILE );
$morphy = new phpMorphy($dir, $lang, $opts);
//error_reporting(0);

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
$user['tariff_id']=15;
$user['user_id']=1187;
// auth();
// if (!$loged) die();

$objPHPExcel = new PHPExcel();
$objPHPExcel->getProperties()->setCreator("Wobot")
							 ->setLastModifiedBy("Wobot")
							 ->setTitle("Wobot_XLSX_expot")
							 ->setSubject("Wobot_XLSX_expot")
							 ->setDescription("Wobot_XLSX_expot")
							 ->setKeywords("Wobot_XLSX_expot")
							 ->setCategory("Export");

$objPHPExcel->setActiveSheetIndex(0);
$objPHPExcel->getActiveSheet()->setTitle('Показатели');
$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);

$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(8);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(8);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(8);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(60);

$objDrawing = new PHPExcel_Worksheet_Drawing();
$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
$objDrawing->setName('Paid');
$objDrawing->setDescription('Paid');
$objDrawing->setPath('/var/www/project/excel/logo.png');
$objDrawing->setCoordinates('A1');

$objPHPExcel->getActiveSheet()->setCellValue('D1', 'Wobot - мониторинг и аналитика социальных медиа и онлайн-СМИ');
$objPHPExcel->getActiveSheet()->getStyle('D1')->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->setCellValue('B8', 'Отчет по мониторингу социальных медиа и онлайн-СМИ');
$objPHPExcel->getActiveSheet()->getStyle('B8')->getFont()->setSize(20)->setBold(true);
$objPHPExcel->getActiveSheet()->setCellValue('B66', 'Источники упоминаний');
$objPHPExcel->getActiveSheet()->getStyle('B66')->getFont()->setSize(20)->setBold(true);

if ($user['tariff_id']==3)
{
	$infus=$db->query('SELECT order_id,user_id FROM blog_orders WHERE order_id='.$_POST['order_id'].' LIMIT 1');
	$usri=$db->fetch($infus);
	if ($usri['user_id']==61) $user['user_id']=61;
}
$_POST['order_id']=2074;
$_POST['start']='01.10.2012';
$_POST['end']='11.11.2012';
//print_r($_POST);
//$_POST=$_GET;
if (intval($_POST['order_id'])==0) die();
//echo 'gg';
//print_r($_SESSION);

$res=$db->query("SELECT order_id,order_name,order_keyword,order_start,order_end,order_last,third_sources from blog_orders WHERE order_id=".intval($_POST['order_id'])." and user_id=".intval($user['user_id'])." LIMIT 1");
$order=$db->fetch($res);

//------------ЛИСТ 1-----------------

$objPHPExcel->getActiveSheet()->setCellValue('B12', 'Тема:');
$objPHPExcel->getActiveSheet()->setCellValue('B13', 'Период:');
$objPHPExcel->getActiveSheet()->setCellValue('B14', 'Поисковый запрос:');
$objPHPExcel->getActiveSheet()->setCellValue('D12', $order['order_name']);
$objPHPExcel->getActiveSheet()->setCellValue('D13', date('d.m.Y',$order['order_start']).'-'.date('d.m.Y',$order['order_end']));
$objPHPExcel->getActiveSheet()->setCellValue('D14', $order['order_keyword']);
$objPHPExcel->getActiveSheet()->setCellValue('A33', 'email: mail@wobot.ru');
$objPHPExcel->getActiveSheet()->getStyle('A33')->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->setCellValue('D33', 'Служба поддрежки');
$objPHPExcel->getActiveSheet()->getStyle('D33')->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('D33')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
$objPHPExcel->getActiveSheet()->setCellValue('E33', '+7 968 531 79 73');
$objPHPExcel->getActiveSheet()->getStyle('E33')->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->setBreak( 'A34', PHPExcel_Worksheet::BREAK_ROW );

//------------------------------------

$src=$order['order_src'];
$src=json_decode($src,true);
$data=$order['order_metrics'];
$metrics=json_decode($data,true);
$settings=json_decode($user['user_settings'],true);
if (intval($settings['export_digest'])==1) $outmas['export_digest']=1;
else $outmas['export_digest']=0;

$query1='SELECT * FROM blog_tag WHERE order_id='.intval($_POST['order_id']);
   $respost1=$db->query($query1);
while($tgl1 = $db->fetch($respost1))
{
	$tagsall[$tgl1['tag_tag']]=$tgl1['tag_name'];
	$tagsallrev[$tgl1['tag_name']]=$tgl1['tag_tag'];
	$mtag[$tgl1['tag_tag']]=$tgl1['tag_name'];
}
$qqq=get_query();
// echo $qqq; die();
if ($user['tariff_id']==16) $qqq.=' LIMIT 100';
// echo $qqq;
// die();

$qpost=$db->query($qqq);
while ($post=$db->fetch($qpost))
{
	$count_post++;
	if (isset($uniq_blogs[$post['blog_id']])) $uniq_blogs[$post['blog_id']]++;
	{
		$audience+=$post['blog_readers'];
		$uniq_blogs[$post['blog_id']]=1;
	}
	$uniq_host[$post['post_host']]++;
	if (in_array($post['post_host'], $res_micr)) $mtype['res_micr']++;
	elseif (in_array($post['post_host'], $soc_netw)) $mtype['soc_netw']++;
	elseif (in_array($post['post_host'], $smi)) $mtype['smi']++;
	elseif (in_array($post['post_host'], $forabl)) $mtype['forabl']++;
	elseif (in_array($post['post_host'], $video_host)) $mtype['video_host']++;
	else $mtype['other']++;
	$engagement+=$post['post_engage'];
	$engagement_adv=json_decode($post['post_advengage'],true);
	$count_likes+=$engagement_adv['likes'];
	$count_comment+=$engagement_adv['comment'];
	$count_retweet+=$engagement_adv['retweet'];
	if ($post['post_nastr']!=0)
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
	elseif ($post['post_age']<=20)
	{
		$mage[1]++;
		$mage_nastr[1][$post['post_nastr']]++;
	}
	elseif ($post['post_age']>=21 && $post['post_age']<=30)
	{
		$mage[2]++;
		$mage_nastr[2][$post['post_nastr']]++;
	}
	elseif ($post['post_age']>=31 && $post['post_age']<=40)
	{
		$mage[3]++;
		$mage_nastr[3][$post['post_nastr']]++;
	}
	elseif ($post['post_age']>40)
	{
		$mage[4]++;
		$mage_nastr[4][$post['post_nastr']]++;
	}
	$slice_time=mktime(0,0,0,date('n',$post['post_time']),date('j',$post['post_time']),date('Y',$post['post_time']));
	$din_post[$slice_time]++;
	$uniq_host_time[$slice_time][$post['post_host']]++;
	$mnastr_time[$slice_time][$post['post_nastr']]++;
}

$iter=0;
ksort($din_post);
ksort($uniq_host_time);
ksort($mnastr_time);
arsort($uniq_host);
arsort($mloc);
print_r($uniq_host);
foreach ($din_post as $key => $item)
{
	$din_post2[$iter][0]=date('d.m.Y',$key);
	$din_post2[$iter][1]=(string)$item;
	$iter++;
}

$iter=1;
foreach ($uniq_host as $host => $count)
{
	if ($iter==4) break;
	echo $host."\n";
	$din_top_host[0][0]='Дата:';
	$din_top_host[0][$iter]=$host;
	$iter_din_top_host=1;
	foreach ($uniq_host_time as $time => $time_info)
	{
		$din_top_host[$iter_din_top_host][0]=date('d.m.Y',$time);
		$din_top_host[$iter_din_top_host][$iter]=(string)intval($time_info[$host]);
		$iter_din_top_host++;
	}
	$iter++;
}

$iter=1;
$din_nastr[0][0]='Дата:';
$din_nastr[0][1]='Негативные';
$din_nastr[0][2]='Нейтральные';
$din_nastr[0][3]='Позитивные';
$din_nastr[0][4]='Негативные';
$din_nastr[0][5]='Нейтральные';
$din_nastr[0][6]='Позитивные';
foreach ($mnastr_time as $time => $time_info)
{
	$din_nastr[$iter][0]=date('d.m.Y',$time);
	$din_nastr[$iter][1]=(string)intval($time_info[-1]);
	$din_nastr[$iter][2]=(string)intval($time_info[0]);
	$din_nastr[$iter][3]=(string)intval($time_info[1]);
	$din_nastr[$iter][4]=intval($time_info[-1]*100/($time_info[-1]+$time_info[0]+$time_info[1])).'%';
	$din_nastr[$iter][5]=intval($time_info[0]*100/($time_info[-1]+$time_info[0]+$time_info[1])).'%';
	$din_nastr[$iter][6]=intval($time_info[1]*100/($time_info[-1]+$time_info[0]+$time_info[1])).'%';
	$iter++;
}

$nastr_all[0][0]='';
$nastr_all[0][1]='Кол-во';
$nastr_all[1][0]='Нейтральные';
$nastr_all[1][1]=(string)intval($mnastr[0]);
$nastr_all[2][0]='Негативные';
$nastr_all[2][1]=(string)intval($mnastr[-1]);
$nastr_all[3][0]='Позитивные';
$nastr_all[3][1]=(string)intval($mnastr[1]);

$iter=1;
$mhost[0][0]='Ресурс';
$mhost[0][1]='Кол-во';
$mhost_proc_nastr[$iter][0]='';
$mhost_proc_nastr[$iter][1]='Негативные';
$mhost_proc_nastr[$iter][2]='Нейтральные';
$mhost_proc_nastr[$iter][3]='Позитивные';
$mhost_proc_nastr[$iter][4]='Негативные';
$mhost_proc_nastr[$iter][5]='Нейтральные';
$mhost_proc_nastr[$iter][6]='Позитивные';
foreach ($uniq_host as $host => $count)
{
	if ($iter<=6)
	{
		$mhost_proc[$iter][0]=$host;
		$mhost_proc[$iter][1]=(string)intval($count*100/$count_post);
		$mhost_proc_nastr[$iter][0]=$host;
		$mhost_proc_nastr[$iter][1]=(string)intval($mnastr_host[$host][-1]);
		$mhost_proc_nastr[$iter][2]=(string)intval($mnastr_host[$host][0]);
		$mhost_proc_nastr[$iter][3]=(string)intval($mnastr_host[$host][1]);
		$mhost_proc_nastr[$iter][4]=(string)intval($mnastr_host[$host][-1]*100/($mnastr_host[$host][-1]+$mnastr_host[$host][0]+$mnastr_host[$host][1])).'%';
		$mhost_proc_nastr[$iter][5]=(string)intval($mnastr_host[$host][0]*100/($mnastr_host[$host][-1]+$mnastr_host[$host][0]+$mnastr_host[$host][1])).'%';
		$mhost_proc_nastr[$iter][6]=(string)intval($mnastr_host[$host][1]*100/($mnastr_host[$host][-1]+$mnastr_host[$host][0]+$mnastr_host[$host][1])).'%';
	}
	else
	{
		$count_other+=$count;
	}
	$mhost[$iter][0]=$host;
	$mhost[$iter][1]=$count;
	$iter++;
}
$mhost_proc[6][0]=$host;
$mhost_proc[6][1]=(string)intval(($count_post-$count_other)*100/$count_post);

$iter=1;
$mtype_all[0][0]='Тип ресурса';
$mtype_all[0][1]='%';
$mtype_all[1][0]='Социльаная сеть';
$mtype_all[1][1]=(string)intval($mtype['soc_netw']*100/$count_post);
$mtype_all[2][0]='микроблог';
$mtype_all[2][1]=(string)intval($mtype['res_micr']*100/$count_post);
$mtype_all[3][0]='СМИ';
$mtype_all[3][1]=(string)intval($mtype['smi']*100/$count_post);
$mtype_all[4][0]='форум или блог';
$mtype_all[4][1]=(string)intval($mtype['forabl']*100/$count_post);
$mtype_all[5][0]='видеохостинг';
$mtype_all[5][1]=(string)intval($mtype['video_host']*100/$count_post);
$mtype_all[6][0]='другие';
$mtype_all[6][1]=(string)intval($mtype['other']*100/$count_post);

$iter=1;
$mloc_all[0][0]='Город';
$mloc_all[0][1]='Кол-во';
$mloc_all_proc[0][0]='Город';
$mloc_all_proc[0][1]='Кол-во';
$mloc_all_proc[0][2]='Позитивные';
$mloc_all_proc[0][3]='Негативные';
$mloc_all_proc[0][4]='Нейтральные';
foreach ($mloc as $loc => $count)
{
	if ($iter<11)
	{
		$mloc_all[$iter][0]=$loc;
		$mloc_all[$iter][1]=(string)intval($count);
	}
	else
	{
		$count_other_loc+=$count;
	}
	$mloc_all_proc[$iter][0]=$loc;
	$mloc_all_proc[$iter][1]=(string)intval($count);
	$mloc_all_proc[$iter][2]=(string)intval($mloc_nastr[$loc][1]);
	$mloc_all_proc[$iter][3]=(string)intval($mloc_nastr[$loc][-1]);
	$mloc_all_proc[$iter][4]=(string)intval($mloc_nastr[$loc][0]);
	$iter++;
}
$mloc_all[11][0]='Другие';
$mloc_all[11][1]=(string)intval($count_other_loc);
$mloc_all[12][0]='Неопределено';
$mloc_all[12][1]=(string)intval($mloc_null);
$mloc_all_proc[$iter][0]='Неопределено';
$mloc_all_proc[$iter][1]=(string)intval($mloc_null);
$mloc_all_proc[$iter][2]=(string)intval($mloc_null_nastr[1]);
$mloc_all_proc[$iter][3]=(string)intval($mloc_null_nastr[-1]);
$mloc_all_proc[$iter][4]=(string)intval($mloc_null_nastr[0]);

$mgen_all[0][0]='Пол';
$mgen_all[0][1]='Положительные';
$mgen_all[0][2]='Нейтральные';
$mgen_all[0][3]='Негативные';
$mgen_all[0][4]='Количество упоминаний';
$mgen_all[1][0]='Женщины';
$mgen_all[1][1]=(string)intval($mgen_nastr[1][1]);
$mgen_all[1][2]=(string)intval($mgen_nastr[1][0]);
$mgen_all[1][3]=(string)intval($mgen_nastr[1][-1]);
$mgen_all[1][4]=(string)intval($mgen[1]);
$mgen_all[2][0]='Мужчины';
$mgen_all[2][1]=(string)intval($mgen_nastr[2][1]);
$mgen_all[2][2]=(string)intval($mgen_nastr[2][0]);
$mgen_all[2][3]=(string)intval($mgen_nastr[2][-1]);
$mgen_all[2][4]=(string)intval($mgen[2]);
$mgen_all[3][0]='Неопределено';
$mgen_all[3][1]=(string)intval($mgen_nastr[0][1]);
$mgen_all[3][2]=(string)intval($mgen_nastr[0][0]);
$mgen_all[3][3]=(string)intval($mgen_nastr[0][-1]);
$mgen_all[3][4]=(string)intval($mgen[0]);

$mage_all[0][0]='Возраст';
$mage_all[0][1]='Положительные';
$mage_all[0][2]='Нейтральные';
$mage_all[0][3]='Негативные';
$mage_all[0][4]='Количество упоминаний';
$mage_all[1][0]='До 20 лет';
$mage_all[1][1]=(string)intval($mage_nastr[1][1]);
$mage_all[1][2]=(string)intval($mage_nastr[1][0]);
$mage_all[1][3]=(string)intval($mage_nastr[1][-1]);
$mage_all[1][4]=(string)intval($mage[1]);
$mage_all[2][0]='От 21 до 30 лет';
$mage_all[2][1]=(string)intval($mage_nastr[2][1]);
$mage_all[2][2]=(string)intval($mage_nastr[2][0]);
$mage_all[2][3]=(string)intval($mage_nastr[2][-1]);
$mage_all[2][4]=(string)intval($mage[2]);
$mage_all[3][0]='от 31 до 40 лет';
$mage_all[3][1]=(string)intval($mage_nastr[3][1]);
$mage_all[3][2]=(string)intval($mage_nastr[3][0]);
$mage_all[3][3]=(string)intval($mage_nastr[3][-1]);
$mage_all[3][4]=(string)intval($mage[3]);
$mage_all[4][0]='старше 40 лет';
$mage_all[4][1]=(string)intval($mage_nastr[4][1]);
$mage_all[4][2]=(string)intval($mage_nastr[4][0]);
$mage_all[4][3]=(string)intval($mage_nastr[4][-1]);
$mage_all[4][4]=(string)intval($mage[4]);
$mage_all[5][0]='Неопределено';
$mage_all[5][1]=(string)intval($mage_nastr[0][1]);
$mage_all[5][2]=(string)intval($mage_nastr[0][0]);
$mage_all[5][3]=(string)intval($mage_nastr[0][-1]);
$mage_all[5][4]=(string)intval($mage[0]);

// krsort($din_post);
print_r($din_post2);
print_r($din_top_host);
print_r($din_nastr);
print_r($nastr_all);

$objPHPExcel->getActiveSheet()->setCellValue('B35', 'Основные показатели');
$objPHPExcel->getActiveSheet()->getStyle('B35')->getFont()->setSize(20)->setBold(true);
$objPHPExcel->getActiveSheet()->setCellValue('D38', 'Кол-во упоминаний');
$objPHPExcel->getActiveSheet()->setCellValue('D39', 'Уникальных авторов');
$objPHPExcel->getActiveSheet()->setCellValue('D40', 'В среднем постов/сутки');
$objPHPExcel->getActiveSheet()->setCellValue('D41', 'Количество ресурсов');
$objPHPExcel->getActiveSheet()->setCellValue('D42', 'Охват');
$objPHPExcel->getActiveSheet()->setCellValue('D43', 'Вовлеченность, в том числе:');
$objPHPExcel->getActiveSheet()->setCellValue('D44', 'Количество лайков');
$objPHPExcel->getActiveSheet()->setCellValue('D45', 'Количество комментариев');
$objPHPExcel->getActiveSheet()->setCellValue('D46', 'Количество ретвитов');
$objPHPExcel->getActiveSheet()->setCellValue('D47', 'Эмоциональные сообщения');
$objPHPExcel->getActiveSheet()->setCellValue('D48', 'Позитивные');
$objPHPExcel->getActiveSheet()->setCellValue('D49', 'Негативные');
$objPHPExcel->getActiveSheet()->setCellValue('E38', $count_post);
$objPHPExcel->getActiveSheet()->setCellValue('E39', count($uniq_blogs));
$objPHPExcel->getActiveSheet()->setCellValue('E40', intval($count_post/((strtotime($_POST['etime'])-strtotime($_POST['stime']))/86400)));
$objPHPExcel->getActiveSheet()->setCellValue('E41', count($uniq_host));
$objPHPExcel->getActiveSheet()->setCellValue('E42', $audience);
$objPHPExcel->getActiveSheet()->setCellValue('E43', $engagement);
$objPHPExcel->getActiveSheet()->setCellValue('E44', $count_likes);
$objPHPExcel->getActiveSheet()->setCellValue('E45', $count_comment);
$objPHPExcel->getActiveSheet()->setCellValue('E46', $count_retweet);
$objPHPExcel->getActiveSheet()->setCellValue('E47', intval($mnastr[1])+intval($mnastr[-1]));
$objPHPExcel->getActiveSheet()->setCellValue('E48', intval($mnastr[1]));
$objPHPExcel->getActiveSheet()->setCellValue('E49', intval($mnastr[-1]));

// $objPHPExcel->getActiveSheet()->setCellValue('D50', );

$objPHPExcel->createSheet(1);
$objPHPExcel->setActiveSheetIndex(1);
$objPHPExcel->getActiveSheet()->setTitle('Данные для показателей');
$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
$objPHPExcel->getActiveSheet()->setCellValue('A1', 'Динамика упоминаний');
$objPHPExcel->getActiveSheet()->setCellValue('A2', 'Дата');
$objPHPExcel->getActiveSheet()->setCellValue('B2', 'Кол-во упоминаний');
$objPHPExcel->getActiveSheet()->setCellValue('D1', 'Динамика упоминаний на 3 основных ресурсах');
$objPHPExcel->getActiveSheet()->setCellValue('P2', 'Тональность упоминаний');
$objPHPExcel->getActiveSheet()->setCellValue('S2', 'Распределение упоминаний по ресурсам в процентах');
$objPHPExcel->getActiveSheet()->setCellValue('S12', 'Количество упоминаний на разных ресурсах');
$objPHPExcel->getActiveSheet()->setCellValue('V2', 'Распределение тональности по ресурсам');
$objPHPExcel->getActiveSheet()->setCellValue('AE2', 'Распределение упоминаний по типам ресурсов в процентах');
$objPHPExcel->getActiveSheet()->setCellValue('AH2', 'Распределение упоминаний по разным населенным пунктам');
$objPHPExcel->getActiveSheet()->setCellValue('AK2', 'Распределение упоминаний по разным населенным пунктам');
$objPHPExcel->getActiveSheet()->fromArray($din_post2, null, 'A3');
$objPHPExcel->getActiveSheet()->fromArray($din_top_host, null, 'D2');
$objPHPExcel->getActiveSheet()->fromArray($din_nastr, null, 'I2');
$objPHPExcel->getActiveSheet()->fromArray($nastr_all, null, 'P3');
$objPHPExcel->getActiveSheet()->fromArray($mhost_proc, null, 'S3');
$objPHPExcel->getActiveSheet()->fromArray($mhost, null, 'S13');
$objPHPExcel->getActiveSheet()->fromArray($mhost_proc_nastr, null, 'V3');
$objPHPExcel->getActiveSheet()->fromArray($mtype_all, null, 'AE3');
$objPHPExcel->getActiveSheet()->fromArray($mloc_all, null, 'AH3');
$objPHPExcel->getActiveSheet()->fromArray($mloc_all_proc, null, 'AK3');
$objPHPExcel->getActiveSheet()->fromArray($mgen_all, null, 'AR3');
$objPHPExcel->getActiveSheet()->fromArray($mage_all, null, 'AR9');

$objPHPExcel->setActiveSheetIndex(0);
$objWorksheet = $objPHPExcel->getActiveSheet();

$dataseriesLabels = array(
	new PHPExcel_Chart_DataSeriesValues('String', '\'Данные для показателей\'!$B$2', NULL, 1),	//	2010
);
$xAxisTickValues = array(
	new PHPExcel_Chart_DataSeriesValues('String', '\'Данные для показателей\'!$A$3:$A$'.(3+count($din_post)), NULL, count($din_post)),	//	Q1 to Q4
);
$dataSeriesValues = array(
	new PHPExcel_Chart_DataSeriesValues('Number', '\'Данные для показателей\'!$B$3:$B$'.(3+count($din_post)), NULL, count($din_post)),
);
$series = new PHPExcel_Chart_DataSeries(
	PHPExcel_Chart_DataSeries::TYPE_LINECHART,		// plotType
	PHPExcel_Chart_DataSeries::GROUPING_STACKED,	// plotGrouping
	range(0, count($dataSeriesValues)-1),			// plotOrder
	$dataseriesLabels,								// plotLabel
	$xAxisTickValues,								// plotCategory
	$dataSeriesValues								// plotValues
);
$plotarea = new PHPExcel_Chart_PlotArea(NULL, array($series));
$legend = new PHPExcel_Chart_Legend(PHPExcel_Chart_Legend::POSITION_TOPRIGHT, NULL, false);
$title = new PHPExcel_Chart_Title('Динамика упоминаний');
$yAxisLabel = new PHPExcel_Chart_Title('Количество упоминаний');
$chart = new PHPExcel_Chart(
	'chart1',		// name
	$title,			// title
	$legend,		// legend
	$plotarea,		// plotArea
	true,			// plotVisibleOnly
	0,				// displayBlanksAs
	NULL,			// xAxisLabel
	$yAxisLabel		// yAxisLabel
);
$chart->setTopLeftPosition('A50');
$chart->setBottomRightPosition('G65');
$objWorksheet->addChart($chart);


$dataseriesLabels1 = array(
	new PHPExcel_Chart_DataSeriesValues('String', '\'Данные для показателей\'!$S$2', NULL, 1),	//	2011
);
$xAxisTickValues1 = array(
	new PHPExcel_Chart_DataSeriesValues('String', '\'Данные для показателей\'!$S$3:$S$8', NULL, 6),	//	Q1 to Q4
);
$dataSeriesValues1 = array(
	new PHPExcel_Chart_DataSeriesValues('Number', '\'Данные для показателей\'!$T$3:$T$8', NULL, 6),
);
$series1 = new PHPExcel_Chart_DataSeries(
	PHPExcel_Chart_DataSeries::TYPE_PIECHART,				// plotType
	PHPExcel_Chart_DataSeries::GROUPING_STANDARD,			// plotGrouping
	range(0, count($dataSeriesValues1)-1),					// plotOrder
	$dataseriesLabels1,										// plotLabel
	$xAxisTickValues1,										// plotCategory
	$dataSeriesValues1										// plotValues
);
$layout1 = new PHPExcel_Chart_Layout();
$layout1->setShowVal(TRUE);
$layout1->setShowPercent(TRUE);

$plotarea1 = new PHPExcel_Chart_PlotArea($layout1, array($series1));
$legend1 = new PHPExcel_Chart_Legend(PHPExcel_Chart_Legend::POSITION_RIGHT, NULL, false);
$title1 = new PHPExcel_Chart_Title('Ресурсы');

//	Create the chart
$chart1 = new PHPExcel_Chart(
	'chart1',		// name
	$title1,		// title
	$legend1,		// legend
	$plotarea1,		// plotArea
	true,			// plotVisibleOnly
	0,				// displayBlanksAs
	NULL,			// xAxisLabel
	NULL			// yAxisLabel		- Pie charts don't have a Y-Axis
);

//	Set the position where the chart should appear in the worksheet
$chart1->setTopLeftPosition('A67');
$chart1->setBottomRightPosition('D82');
$chart1->setBottomRightXOffset(200);

//	Add the chart to the worksheet
$objWorksheet->addChart($chart1);


$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->setIncludeCharts(TRUE);
$objWriter->save('/var/www/api/0/example.xlsx');
print_r($_POST);
// $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
// $objWriter->save(str_replace('.php', '.xls', __FILE__));


?>
