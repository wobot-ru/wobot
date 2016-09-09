<?
require_once('/var/www/new/com/config.php');
require_once('/var/www/new/com/func.php');
require_once('/var/www/new/com/db.php');
require_once('/var/www/new/bot/kernel.php');
require_once('auth.php');
require_once('rfunc.php');

date_default_timezone_set ( 'Europe/Moscow' );
error_reporting(E_ERROR | E_PARSE);
ignore_user_abort(true);
set_time_limit(0);
ini_set('max_execution_time',0);
ini_set('default_charset','utf-8');
ob_implicit_flush();

$db = new database();
$db->connect();

$redis=new Redis() or die("Can'f load redis module.");
$redis->connect('127.0.0.1');



//print_r($_POST);

//auth();
//if (!$loged) die();
$k=0;
$kk=0;
$kkk=0;
$kkkk=0;
$arr_nau['vk.com']=1;
$arr_nau['vkontakte.ru']=1;
$arr_nau['facebook.com']=1;
$arr_nau['livejournal.com']=1;
$arr_nau['liveinternet.ru']=1;
$arr_nau['mail.ru']=1;
$arr_nau['ya.ru']=1;
$arr_nau['yandex.ru']=1;
$arr_nau['rutwit.ru']=1;
$arr_nau['rutvit.ru']=1;
$arr_nau['babyblog.ru']=1;
$arr_nau['blog.ru']=1;
$arr_nau['foursquare.com']=1;
$arr_nau['kp.ru']=1;
$arr_nau['aif.ru']=1;
$arr_nau['friendfeed.ru']=1;
//$user['user_id']=61;
//auth();

$user['user_id']=145;
$user['tariff_id']=1;
$_POST['order_id']=722;
$_POST['start']='27.03.2012';
$_POST['end']='15.04.2012';

//print_r($user);
//echo $loged;
//if (!$loged) die();
//if ((!$loged) && ($user['tariff_id']==3)) die();
$memcache = memcache_connect('localhost', 11211);
/*$out1=$memcache->get('order_'.$_POST['order_id'].'_'.strtotime($_POST['start']).'_'.strtotime($_POST['end']));
//echo 'order_'.$_POST['order_id'].'_'.strtotime($_POST['start']).'_'.strtotime($_POST['end']);
if ($out1!='')
{
	echo $out1;
	die();
}*/
if (!isset($_GET['order_id']))
{
	$_GET=$_POST;
}
//echo 123;
//print_r($_GET);
$c=0;

function promosort($a, $b)
{
    if ($a['count'] == $b['count']) {
        return 0;
    }
    return ($a['count'] < $b['count']) ? 1 : -1;
}

if ((strtotime($_GET['start'])!=0) && (strtotime($_GET['end'])!=0))
{
	if ($user['tariff_id']==3)
	{
		$res=$db->query('SELECT * FROM blog_orders WHERE order_id='.intval($_GET['order_id']).' AND (user_id=61 or user_id='.intval($user['user_id']).')');
	}
	else
	{
		$res=$db->query('SELECT * FROM blog_orders WHERE order_id='.intval($_GET['order_id']).' AND user_id='.intval($user['user_id']));
	}
}
else
{
	if ($user['tariff_id']==3)
	{
		$res=$db->query('SELECT * FROM blog_orders WHERE order_id='.intval($_GET['order_id']).' AND (user_id=61 or user_id='.intval($user['user_id']).')');
	}
	else
	{
		$res=$db->query('SELECT * FROM blog_orders WHERE order_id='.intval($_GET['order_id']).' AND user_id='.intval($user['user_id']));
	}
}
//echo 'SELECT order_id,order_name,order_keyword,order_start,order_end,order_last,third_sources,order_metrics,order_src,order_graph FROM blog_orders WHERE order_id='.intval($_GET['order_id']).' AND user_id='.intval($user['user_id']);
while ($row = $db->fetch($res)) 
{
	if ($row['order_end']>mktime(0,0,0,date('n'),date('j'),date('Y')))
	{
		$row['order_end']=mktime(0,0,0,date('n'),date('j'),date('Y'));
	}
	if ((strtotime($_GET['end'])==0) && (strtotime($_GET['start'])==0))
	{
		$_GET['start']=date('j.n.Y',$row['order_start']);
		$_GET['end']=date('j.n.Y',$row['order_end']);
	}
	/*$out1=$memcache->get('order_'.$_POST['order_id'].'_'.strtotime($_GET['start']).'_'.strtotime($_GET['end']));
	if ($out1!='')
	{
		echo $out1;
		die();
	}*/
	$out=get_order_cash($row,$_GET['start'],$_GET['end']);
}
if (intval($egage)>0)
{
	$out['engage']=1;
}
else
{
	$out['engage']=0;
}
/*if (!isset($out['speakers']))
{
	$out['speakers']=array();
}
if (!isset($out['promotions']))
{
	$out['promotions']=array();
}
if (!isset($out['sources']))
{
	$out['sources']=array();
}
if (!isset($out['city']))
{
	$out['city']=array();
}
if (!isset($out['value_mdin']))
{
	$out['value_mdin']=array();
}
if (!isset($out['eng_mdin']))
{
	$out['eng_mdin']=array();
}
if (!isset($out['words']))
{
	$out['words']=array();
}*/
//unset($out['eng_mdin']);
//print_r($yet_uniq);
//print_r($metrics);
/*$out['order_id']=$_GET['order_id'];
$out['posts']=5098;
$out['uniq']=562;
$out['src']=282;
$out['value']=277181;
$out['engage']=7812;
$out['start']='15.08.2010';
$out['end']='15.09.2010';

$out['posts_dyn']=10;
$out['uniq_dyn']=32;
$out['src_dyn']=15;
$out['value_dyn']=-22;
$out['engage_dyn']=40;

$out['sources'][0]['name']='twitter.com'; $out['sources'][0]['count']=1872;
$out['sources'][1]['name']='livejournal.com'; $out['sources'][1]['count']=532;
$out['sources'][2]['name']='hpc.ru'; $out['sources'][2]['count']=290;
$out['sources'][3]['name']='google.com'; $out['sources'][3]['count']=136;
$out['sources'][4]['name']='ya.ru'; $out['sources'][4]['count']=130;
$out['sources'][5]['name']='4pda.ru'; $out['sources'][5]['count']=125;
$out['sources'][6]['name']='alfa.kz'; $out['sources'][6]['count']=86;
$out['sources'][7]['name']='4htc.ru'; $out['sources'][7]['count']=81;
$out['sources'][8]['name']='android-no.info'; $out['sources'][8]['count']=79;
$out['sources'][9]['name']='molotok.ru'; $out['sources'][9]['count']=68;
$out['sources'][10]['name']='com.ua'; $out['sources'][10]['count']=63;
$out['sources'][11]['name']='youhtc.ru'; $out['sources'][11]['count']=62;
$out['sources'][12]['name']='aukro.ua'; $out['sources'][12]['count']=60;
$out['sources'][13]['name']='ixbt.com'; $out['sources'][13]['count']=39;
$out['sources'][14]['name']='androidsecretsmagazine.com'; $out['sources'][14]['count']=38;
$out['sources'][15]['name']='asusmobile.ru'; $out['sources'][15]['count']=31;
$out['sources'][16]['name']='juick.com'; $out['sources'][16]['count']=30;
$out['sources'][17]['name']='blogspot.com'; $out['sources'][17]['count']=29;
$out['sources'][18]['name']='ukrgo.com'; $out['sources'][18]['count']=27;
$out['sources'][19]['name']='mail.ru'; $out['sources'][19]['count']=25;
$out['sources'][20]['name']='kharkovforum.com'; $out['sources'][20]['count']=25;
$out['sources'][21]['name']='habrahabr.ru'; $out['sources'][21]['count']=24;
$out['sources'][22]['name']='google.ru'; $out['sources'][22]['count']=23;
$out['sources'][23]['name']='net.ua'; $out['sources'][23]['count']=23;

$out['city'][0]['name']='Москва'; $out['city'][0]['count']=238;
$out['city'][1]['name']='Россия'; $out['city'][1]['count']=91;
$out['city'][2]['name']='Санкт-петербург'; $out['city'][2]['count']=78;
$out['city'][3]['name']='Украина'; $out['city'][3]['count']=70;
$out['city'][4]['name']='Тольятти'; $out['city'][4]['count']=42;
$out['city'][5]['name']='Нью-Йорк'; $out['city'][5]['count']=28;
$out['city'][6]['name']='Минск'; $out['city'][6]['count']=24;
$out['city'][7]['name']='Киев'; $out['city'][7]['count']=23;
$out['city'][8]['name']='Челябинск'; $out['city'][8]['count']=20;
$out['city'][9]['name']='Дубна'; $out['city'][9]['count']=18;
$out['city'][10]['name']='Рига'; $out['city'][10]['count']=15;
$out['city'][11]['name']='Тюмень'; $out['city'][11]['count']=14;
$out['city'][12]['name']='Волгоград'; $out['city'][12]['count']=14;
$out['city'][13]['name']='Архангельск'; $out['city'][13]['count']=10;
$out['city'][14]['name']='Ростов-на-Дону'; $out['city'][14]['count']=10;
$out['city'][15]['name']='Пенза'; $out['city'][15]['count']=10;
$out['city'][16]['name']='Харьков'; $out['city'][16]['count']=10;
$out['city'][17]['name']='Омск'; $out['city'][17]['count']=10;
$out['city'][18]['name']='Саранск'; $out['city'][18]['count']=9;
$out['city'][19]['name']='Смоленск'; $out['city'][19]['count']=9;

$out['promotions'][0]['nick']='Marfitsin'; $out['promotions'][0]['count']=42;
$out['promotions'][1]['nick']='isegals'; $out['promotions'][1]['count']=36;
$out['promotions'][2]['nick']='technewsru'; $out['promotions'][2]['count']=27;
$out['promotions'][3]['nick']='mrozentsvayg'; $out['promotions'][3]['count']=27;
$out['promotions'][4]['nick']='artranceit'; $out['promotions'][4]['count']=27;
$out['promotions'][5]['nick']='Alexandrlaw'; $out['promotions'][5]['count']=27;
$out['promotions'][6]['nick']='FOR_HTC'; $out['promotions'][6]['count']=20;
$out['promotions'][7]['nick']='bigarando'; $out['promotions'][7]['count']=19;
$out['promotions'][8]['nick']='android_ua'; $out['promotions'][8]['count']=19;
$out['promotions'][9]['nick']='velolive'; $out['promotions'][9]['count']=18;
$out['promotions'][10]['nick']='andrewbabkin'; $out['promotions'][10]['count']=18;
$out['promotions'][11]['nick']='YouHTC'; $out['promotions'][11]['count']=18;
$out['promotions'][12]['nick']='Xill47'; $out['promotions'][12]['count']=18;
$out['promotions'][13]['nick']='Marrkyboy'; $out['promotions'][13]['count']=18;
$out['promotions'][14]['nick']='HTC_Ru'; $out['promotions'][14]['count']=12;
$out['promotions'][15]['nick']='thieplh'; $out['promotions'][15]['count']=11;
$out['promotions'][16]['nick']='novosteycom'; $out['promotions'][16]['count']=11;
$out['promotions'][17]['nick']='VirusPanin'; $out['promotions'][17]['count']=11;
$out['promotions'][18]['nick']='zhenyek'; $out['promotions'][18]['count']=10;
$out['promotions'][19]['nick']='pravdzivyj'; $out['promotions'][19]['count']=10;
$out['promotions'][20]['nick']='onton_a'; $out['promotions'][20]['count']=10;
$out['promotions'][21]['nick']='itrus'; $out['promotions'][21]['count']=10;
$out['promotions'][22]['nick']='drlyvsy'; $out['promotions'][22]['count']=10;
$out['promotions'][23]['nick']='dimka48'; $out['promotions'][23]['count']=10;
$out['promotions'][24]['nick']='chibisanton'; $out['promotions'][24]['count']=10;
$out['promotions'][25]['nick']='borutskiy'; $out['promotions'][25]['count']=10;
$out['promotions'][26]['nick']='andrewf'; $out['promotions'][26]['count']=10;
$out['promotions'][27]['nick']='SRG222'; $out['promotions'][27]['count']=10;
$out['promotions'][28]['nick']='Miliaev'; $out['promotions'][28]['count']=10;

$out['speakers'][0]['nick']='Marfitsin'; $out['speakers'][0]['count']=42;
$out['speakers'][1]['nick']='isegals'; $out['speakers'][1]['count']=36;
$out['speakers'][2]['nick']='technewsru'; $out['speakers'][2]['count']=27;
$out['speakers'][3]['nick']='mrozentsvayg'; $out['speakers'][3]['count']=27;
$out['speakers'][4]['nick']='artranceit'; $out['speakers'][4]['count']=27;
$out['speakers'][5]['nick']='Alexandrlaw'; $out['speakers'][5]['count']=27;
$out['speakers'][6]['nick']='FOR_HTC'; $out['speakers'][6]['count']=20;
$out['speakers'][7]['nick']='bigarando'; $out['speakers'][7]['count']=19;
$out['speakers'][8]['nick']='android_ua'; $out['speakers'][8]['count']=19;
$out['speakers'][9]['nick']='velolive'; $out['speakers'][9]['count']=18;
$out['speakers'][10]['nick']='andrewbabkin'; $out['speakers'][10]['count']=18;
$out['speakers'][11]['nick']='YouHTC'; $out['speakers'][11]['count']=18;
$out['speakers'][12]['nick']='Xill47'; $out['speakers'][12]['count']=18;
$out['speakers'][13]['nick']='Marrkyboy'; $out['speakers'][13]['count']=18;
$out['speakers'][14]['nick']='HTC_Ru'; $out['speakers'][14]['count']=12;
$out['speakers'][15]['nick']='thieplh'; $out['speakers'][15]['count']=11;
$out['speakers'][16]['nick']='novosteycom'; $out['speakers'][16]['count']=11;
$out['speakers'][17]['nick']='VirusPanin'; $out['speakers'][17]['count']=11;
$out['speakers'][18]['nick']='zhenyek'; $out['speakers'][18]['count']=10;
$out['speakers'][19]['nick']='pravdzivyj'; $out['speakers'][19]['count']=10;
$out['speakers'][20]['nick']='onton_a'; $out['speakers'][20]['count']=10;
$out['speakers'][21]['nick']='itrus'; $out['speakers'][21]['count']=10;
$out['speakers'][22]['nick']='drlyvsy'; $out['speakers'][22]['count']=10;
$out['speakers'][23]['nick']='dimka48'; $out['speakers'][23]['count']=10;
$out['speakers'][24]['nick']='chibisanton'; $out['speakers'][24]['count']=10;
$out['speakers'][25]['nick']='borutskiy'; $out['speakers'][25]['count']=10;
$out['speakers'][26]['nick']='andrewf'; $out['speakers'][26]['count']=10;
$out['speakers'][27]['nick']='SRG222'; $out['speakers'][27]['count']=10;
$out['speakers'][28]['nick']='Miliaev'; $out['speakers'][28]['count']=10;

$out['words'][0]['word']='desire'; $out['words'][0]['count']=705;
$out['words'][1]['word']='новый'; $out['words'][1]['count']=553;
$out['words'][2]['word']='продам'; $out['words'][2]['count']=552;
$out['words'][3]['word']='продаю'; $out['words'][3]['count']=522;
$out['words'][4]['word']='android'; $out['words'][4]['count']=394;
$out['words'][5]['word']='куплю'; $out['words'][5]['count']=321;
$out['words'][6]['word']='touch'; $out['words'][6]['count']=318;
$out['words'][7]['word']='меня'; $out['words'][7]['count']=295;
$out['words'][8]['word']='hero'; $out['words'][8]['count']=242;
$out['words'][9]['word']='есть'; $out['words'][9]['count']=235;
$out['words'][10]['word']='google'; $out['words'][10]['count']=223;
$out['words'][11]['word']='коммуникаторы'; $out['words'][11]['count']=202;
$out['words'][12]['word']='wildfire'; $out['words'][12]['count']=171;
$out['words'][13]['word']='планшет'; $out['words'][13]['count']=164;
$out['words'][14]['word']='москва'; $out['words'][14]['count']=152;
$out['words'][15]['word']='diamond'; $out['words'][15]['count']=148;
$out['words'][16]['word']='смартфон'; $out['words'][16]['count']=146;
$out['words'][17]['word']='телефон'; $out['words'][17]['count']=143;
$out['words'][18]['word']='сегодня'; $out['words'][18]['count']=135;
$out['words'][19]['word']='samsung'; $out['words'][19]['count']=128;
$out['words'][20]['word']='windows'; $out['words'][20]['count']=126;
$out['words'][21]['word']='санкт'; $out['words'][21]['count']=112;
$out['words'][22]['word']='если'; $out['words'][22]['count']=112;
$out['words'][23]['word']='объявление'; $out['words'][23]['count']=110;
$out['words'][24]['word']='iphone'; $out['words'][24]['count']=108;
$out['words'][25]['word']='legend'; $out['words'][25]['count']=106;
$out['words'][26]['word']='мобильный'; $out['words'][26]['count']=104;
$out['words'][27]['word']='видео'; $out['words'][27]['count']=99;

$out['graph']['1343678400']=28;
$out['graph']['1344542400']=22;
$out['graph']['1344628800']=15;
$out['graph']['1344715200']=37;
$out['graph']['1344801600']=54;
$out['graph']['1344888000']=21;
$out['graph']['1344974400']=9;
$out['graph']['1345060800']=89;
$out['graph']['1345147200']=67;
$out['graph']['1345233600']=38;
$out['graph']['1345320000']=60;
$out['graph']['1345406400']=23;
$out['graph']['1345492800']=123;
$out['graph']['1345579200']=29;
$out['graph']['1345665600']=76;
$out['graph']['1345752000']=67;
$out['graph']['1345838400']=58;
$out['graph']['1345924800']=40;
$out['graph']['1346011200']=35;
$out['graph']['1346097600']=31;

*/
//$out['value_dyn']=0;
//$out['engage_dyn']=0;
//$out['value_dyn']=0;
//$out['src_dyn']=0;
//$out['uniq_dyn']=0;
//$out['posts_dyn']=0;
//print_r(get_mas(736,1334865600));

//$m->setMulti(array('cashorder_'.md5(json_encode($_POST))=>json_encode($out)),time()+86400);
//print_r($_GET);
echo json_encode($out);
?>