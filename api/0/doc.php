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
// die(json_encode($_POST));

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
$qqq=get_query();
// echo $qqq; die();
if ($user['tariff_id']==16) $qqq.=' LIMIT 100';
// echo $qqq;
// die();

$assoc_type['res_micr']='микроблог';
$assoc_type['soc_netw']='социальная сеть';
$assoc_type['smi']='СМИ';
$assoc_type['forabl']='форум или блог';
$assoc_type['video_host']='видеохостинг';
$assoc_type['other']='другое';

$word_content.='<table border="1"><tr><td><p style="font-family: Calibri;">Дата</p></td><td><p style="font-family: Calibri;">Время</p></td><td><p style="font-family: Calibri;">Ресурс</p></td><td><p style="font-family: Calibri;">Текст сообщения</p></td><td><p style="font-family: Calibri;">Тональность</p></td><td><p style="font-family: Calibri;">Вовлеченность</p></td><td><p style="font-family: Calibri;">Охват</p></td></tr>';
$qpost=$db->query($qqq);
while ($post=$db->fetch($qpost))
{
	$word_content.='<tr><td><p style="font-family: Calibri;">'.date('d.m.Y',$post['post_time']).'</p></td><td><p style="font-family: Calibri;">'.date('H:i:s',$post['post_time']).'</p></td><td><p style="font-family: Calibri;">'.$post['post_host'].'</p></td><td><p style="font-family: Calibri;"><a href="'.$post['post_link'].'">'.$post['post_content'].'</a></p></td><td><p style="font-family: Calibri;">'.($post['post_nastr']==0?'':($post['post_nastr']==1?'+':'-')).'</p></td><td><p style="font-family: Calibri;">'.intval($post['post_engage']).'</p></td><td><p style="font-family: Calibri;">'.intval($post['blog_readers']).'</p></td></tr>';
}
$word_content.='</table>';

$exp.="<html>"."<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\">"."<body style=\"font-family:Calibri;\">".$word_content;
$exp.="</body>"."</html>";
// die($exp);
header("Content-Type: application/vnd.ms-word");
header("content-disposition: attachment;filename=wobot-export.doc");
echo $exp;

?>
