<?

require_once('/var/www/new/com/config.php');
require_once('/var/www/new/com/func.php');
require_once('/var/www/new/com/db.php');
require_once('/var/www/new/bot/kernel.php');
require_once('auth.php');

$db = new database();
$db->connect();

auth();
if (!$loged) die();
//$user['user_id']=61;
$k=0;
$kk=0;
$kkk=0;
$kkkk=0;
//$_GET['order_id']=303;
if ($user['tariff_id']==3)
{
	$user['user_id']=61;
}
if (!isset($_GET['order_id']))
{
	$_GET=$_POST;
}
//print_r($user);
$res=$db->query('SELECT * FROM blog_orders WHERE order_id='.intval($_GET['order_id']).' AND user_id='.intval($user['user_id']));
while ($row = $db->fetch($res)) 
{
	$out['params']['speakers']=array();
	$out['params']['promotions']=array();
	$out['params']['city']=array();
	$out['params']['city_tree']=array();
	$out['params']['promotions']=array();
	$out['params']['source_tree']=array();
	$out['params']['full_com']=intval($row['ful_com']);
	$pres_inf=$db->query('SELECT * FROM blog_preset WHERE order_id='.intval($_GET['order_id']));
	while ($pres=$db->fetch($pres_inf))
	{
		$mas['params']['presets'][]=$pres['name'];
	}
	$out['params']['tags']=array();
	$tags_info=$db->query('SELECT * FROM blog_tag WHERE user_id='.intval($user['user_id']));
	while ($tag=$db->fetch($tags_info))
	{
		//$out['params']['tags'][$tag['tag_tag']]=$tag['tag_name'];
		$out['params']['tags'][$tag['tag_tag']]=str_replace('.', '', mb_substr($tag['tag_name'],0,23,'UTF-8'));
	}
	$metrics=json_decode($row['order_metrics'],true);
	$mas_res=json_decode($row['order_src'],true);
	$res_count=count($mas_res);
	$coll=0;
	foreach ($mas_res as $ind => $item)
	{
		$out['params']['sources'][$k]['name']=$ind;
		$out['params']['sources'][$k]['count']=$item;
		$out['params']['source_tree'][mb_substr($ind,0,1,"UTF-8")][$ind]=$item;
		$k++;
		$coll+=$item;
	}
	ksort($out['params']['source_tree']);
	$kk=0;
	foreach ($metrics['location'] as $key => $item)
	{
		if ($key!='')
		{
			$out['params']['city'][$kk]['name']=(($key=='')?'Не определено':$key);
			$out['params']['city'][$kk]['count']=$item;
			if (($wobot['destn3'][$key]!='') && ($wobot['destn3'][$key]!=' '))
			{
				if (isset($m[$key]))
				{
					$out['params']['city_tree'][$wobot['destn3'][$key]][$m[$key]][$key]=$item;
				}
				else
				{
					$out['params']['city_tree'][$wobot['destn3'][$key]][$key]=$item;
				}
			}
			$kk++;
		}
	}
	foreach ($metrics['speakers']['nick'] as $key => $item)
	{
		//echo $metrics['speakers']['site'][$key].' |';
		if (($metrics['speakers']['site'][$key]!='vkontakte.ru') && ($metrics['speakers']['site'][$key]!='facebook.com') && ($metrics['speakers']['site'][$key]!='vk.com'))
		{
			$out['params']['speakers'][$kkk]['nick']=$metrics['speakers']['nick'][$key];
			$out['params']['speakers'][$kkk]['count']=$metrics['speakers']['posts'][$key];
			$out['params']['speakers'][$kkk]['link']=$metrics['speakers']['site'][$key];		
			$out['params']['speakers'][$kkk]['id']=intval($metrics['speakers']['id'][$key]);		
		}
		else
		{
			$out['params']['speakers'][$kkk]['nick']=$metrics['speakers']['rnick'][$key];
			$out['params']['speakers'][$kkk]['count']=$metrics['speakers']['posts'][$key];		
			$out['params']['speakers'][$kkk]['link']=$metrics['speakers']['site'][$key];		
			$out['params']['speakers'][$kkk]['id']=intval($metrics['speakers']['id'][$key]);		
		}
		$kkk++;
	}
	//print_r($metrics['promotion']);
	foreach ($metrics['promotion']['nick'] as $key => $item)
	{
		//echo $metrics['promotion']['link'][$key].' |';
		if (($metrics['promotion']['site'][$key]!='vkontakte.ru') && ($metrics['promotion']['site'][$key]!='facebook.com') && ($metrics['promotion']['site'][$key]!='vk.com'))
		{
			$out['params']['promotions'][$kkkk]['nick']=$metrics['promotion']['nick'][$key];
			$out['params']['promotions'][$kkkk]['count']=intval($metrics['promotion']['readers'][$key]);		
			$out['params']['promotions'][$kkkk]['link']=$metrics['promotion']['site'][$key];		
			$out['params']['promotions'][$kkkk]['id']=intval($metrics['promotion']['id'][$key]);		
		}
		else
		{
			$out['params']['promotions'][$kkkk]['nick']=$metrics['promotion']['rnick'][$key];
			$out['params']['promotions'][$kkkk]['count']=intval($metrics['promotion']['readers'][$key]);		
			$out['params']['promotions'][$kkkk]['link']=$metrics['promotion']['site'][$key];		
			$out['params']['promotions'][$kkkk]['id']=intval($metrics['promotion']['id'][$key]);		
		}
		$kkkk++;
	}
	$k=0;
	foreach ($metrics['topwords'] as $key => $item)
	{
		$out['params']['words'][$k]['word']=preg_replace('/[^а-яА-Яa-zA-Z]/isu','',$key);
		$out['params']['words'][$k]['count']=$item;
		$k++;
	}
	$out['params']['start']=date('d.m.Y',$row['order_start']);
	$out['params']['end']=date('d.m.Y',($row['order_end']==0)?$row['order_last']:$row['order_end']);
	
	$i++;
}

echo json_encode($out);
/*$mas['params']['start']='12.03.2011';
$mas['params']['end']='12.04.2011';

$mas['params']['sources'][0]['name']='twitter.com'; $mas['params']['sources'][0]['count']=1872;
$mas['params']['sources'][1]['name']='livejournal.com'; $mas['params']['sources'][1]['count']=532;
$mas['params']['sources'][2]['name']='hpc.ru'; $mas['params']['sources'][2]['count']=290;
$mas['params']['sources'][3]['name']='google.com'; $mas['params']['sources'][3]['count']=136;
$mas['params']['sources'][4]['name']='ya.ru'; $mas['params']['sources'][4]['count']=130;
$mas['params']['sources'][5]['name']='4pda.ru'; $mas['params']['sources'][5]['count']=125;
$mas['params']['sources'][6]['name']='alfa.kz'; $mas['params']['sources'][6]['count']=86;
$mas['params']['sources'][7]['name']='4htc.ru'; $mas['params']['sources'][7]['count']=81;
$mas['params']['sources'][8]['name']='android-no.info'; $mas['params']['sources'][8]['count']=79;
$mas['params']['sources'][9]['name']='molotok.ru'; $mas['params']['sources'][9]['count']=68;
$mas['params']['sources'][10]['name']='com.ua'; $mas['params']['sources'][10]['count']=63;
$mas['params']['sources'][11]['name']='youhtc.ru'; $mas['params']['sources'][11]['count']=62;
$mas['params']['sources'][12]['name']='aukro.ua'; $mas['params']['sources'][12]['count']=60;
$mas['params']['sources'][13]['name']='ixbt.com'; $mas['params']['sources'][13]['count']=39;
$mas['params']['sources'][14]['name']='androidsecretsmagazine.com'; $mas['params']['sources'][14]['count']=38;
$mas['params']['sources'][15]['name']='asusmobile.ru'; $mas['params']['sources'][15]['count']=31;
$mas['params']['sources'][16]['name']='juick.com'; $mas['params']['sources'][16]['count']=30;
$mas['params']['sources'][17]['name']='blogspot.com'; $mas['params']['sources'][17]['count']=29;
$mas['params']['sources'][18]['name']='ukrgo.com'; $mas['params']['sources'][18]['count']=27;
$mas['params']['sources'][19]['name']='mail.ru'; $mas['params']['sources'][19]['count']=25;
$mas['params']['sources'][20]['name']='kharkovforum.com'; $mas['params']['sources'][20]['count']=25;
$mas['params']['sources'][21]['name']='habrahabr.ru'; $mas['params']['sources'][21]['count']=24;
$mas['params']['sources'][22]['name']='google.ru'; $mas['params']['sources'][22]['count']=23;
$mas['params']['sources'][23]['name']='net.ua'; $mas['params']['sources'][23]['count']=23;

$mas['params']['city_tree']['Россия']['Московская область']['Москва']=238;
$mas['params']['city_tree']['Россия']['Московская область']['Подольск']=12;
$mas['params']['city_tree']['Россия']['Московская область']['Дубна']=22;
$mas['params']['city_tree']['Россия']['Нижегородская область']['Нижний Новгород']=124;
$mas['params']['city_tree']['Россия']['Брянская область']['Брянск']=432;
$mas['params']['city_tree']['Россия']['Липецкая область']['Липецк']=23;
$mas['params']['city_tree']['Россия']['Пермский край']['Пермь']=98;
$mas['params']['city_tree']['Украина']['Киев']=41;
$mas['params']['city_tree']['Украина']['Львов']=232;
$mas['params']['city_tree']['Беларусь']['Минск']=118;
$mas['params']['city_tree']['Эстония']['Таллин']=168;

$mas['params']['source_tree']['a']['alasldad.com']=213;
$mas['params']['source_tree']['b']['bfdffsdf.com']=123;
$mas['params']['source_tree']['c']['casdad.com']=2534;
$mas['params']['source_tree']['d']['dasssss.com']=2653;
$mas['params']['source_tree']['e']['edgfgdgfd.com']=7313;
$mas['params']['source_tree']['f']['fdfsvcvx.com']=3243;
$mas['params']['source_tree']['g']['gasdaqw.com']=27;
$mas['params']['source_tree']['h']['hdsfdsfds.com']=813;
$mas['params']['source_tree']['i']['isdfsdfs.com']=93;

$mas['params']['city'][0]['name']='Москва'; $mas['params']['city'][0]['count']=238;
$mas['params']['city'][1]['name']='Россия'; $mas['params']['city'][1]['count']=91;
$mas['params']['city'][2]['name']='Санкт-петербург'; $mas['params']['city'][2]['count']=78;
$mas['params']['city'][3]['name']='Украина'; $mas['params']['city'][3]['count']=70;
$mas['params']['city'][4]['name']='Тольятти'; $mas['params']['city'][4]['count']=42;
$mas['params']['city'][5]['name']='Нью-Йорк'; $mas['params']['city'][5]['count']=28;
$mas['params']['city'][6]['name']='Минск'; $mas['params']['city'][6]['count']=24;
$mas['params']['city'][7]['name']='Киев'; $mas['params']['city'][7]['count']=23;
$mas['params']['city'][8]['name']='Челябинск'; $mas['params']['city'][8]['count']=20;
$mas['params']['city'][9]['name']='Дубна'; $mas['params']['city'][9]['count']=18;
$mas['params']['city'][10]['name']='Рига'; $mas['params']['city'][10]['count']=15;
$mas['params']['city'][11]['name']='Тюмень'; $mas['params']['city'][11]['count']=14;
$mas['params']['city'][12]['name']='Волгоград'; $mas['params']['city'][12]['count']=14;
$mas['params']['city'][13]['name']='Архангельск'; $mas['params']['city'][13]['count']=10;
$mas['params']['city'][14]['name']='Ростов-на-Дону'; $mas['params']['city'][14]['count']=10;
$mas['params']['city'][15]['name']='Пенза'; $mas['params']['city'][15]['count']=10;
$mas['params']['city'][16]['name']='Харьков'; $mas['params']['city'][16]['count']=10;
$mas['params']['city'][17]['name']='Омск'; $mas['params']['city'][17]['count']=10;
$mas['params']['city'][18]['name']='Саранск'; $mas['params']['city'][18]['count']=9;
$mas['params']['city'][19]['name']='Смоленск'; $mas['params']['city'][19]['count']=9;

$mas['params']['promotions'][0]['nick']='Marfitsin'; $mas['params']['promotions'][0]['count']=42; $mas['params']['promotions'][0]['link']='livejournal.com';
$mas['params']['promotions'][1]['nick']='isegals'; $mas['params']['promotions'][1]['count']=36; $mas['params']['promotions'][1]['link']='livejournal.com';
$mas['params']['promotions'][2]['nick']='technewsru'; $mas['params']['promotions'][2]['count']=27; $mas['params']['promotions'][2]['link']='livejournal.com';
$mas['params']['promotions'][3]['nick']='mrozentsvayg'; $mas['params']['promotions'][3]['count']=27; $mas['params']['promotions'][3]['link']='livejournal.com';
$mas['params']['promotions'][4]['nick']='artranceit'; $mas['params']['promotions'][4]['count']=27; $mas['params']['promotions'][4]['link']='livejournal.com';
$mas['params']['promotions'][5]['nick']='Alexandrlaw'; $mas['params']['promotions'][5]['count']=27; $mas['params']['promotions'][5]['link']='livejournal.com';
$mas['params']['promotions'][6]['nick']='FOR_HTC'; $mas['params']['promotions'][6]['count']=20; $mas['params']['promotions'][6]['link']='livejournal.com';
$mas['params']['promotions'][7]['nick']='bigarando'; $mas['params']['promotions'][7]['count']=19; $mas['params']['promotions'][7]['link']='livejournal.com';
$mas['params']['promotions'][8]['nick']='android_ua'; $mas['params']['promotions'][8]['count']=19; $mas['params']['promotions'][8]['link']='facebook.com';
$mas['params']['promotions'][9]['nick']='velolive'; $mas['params']['promotions'][9]['count']=18; $mas['params']['promotions'][9]['link']='facebook.com';
$mas['params']['promotions'][10]['nick']='andrewbabkin'; $mas['params']['promotions'][10]['count']=18; $mas['params']['promotions'][10]['link']='facebook.com';
$mas['params']['promotions'][11]['nick']='YouHTC'; $mas['params']['promotions'][11]['count']=18; $mas['params']['promotions'][11]['link']='facebook.com';
$mas['params']['promotions'][12]['nick']='Xill47'; $mas['params']['promotions'][12]['count']=18; $mas['params']['promotions'][12]['link']='facebook.com';
$mas['params']['promotions'][13]['nick']='Marrkyboy'; $mas['params']['promotions'][13]['count']=18; $mas['params']['promotions'][13]['link']='facebook.com';
$mas['params']['promotions'][14]['nick']='HTC_Ru'; $mas['params']['promotions'][14]['count']=12; $mas['params']['promotions'][14]['link']='facebook.com';
$mas['params']['promotions'][15]['nick']='thieplh'; $mas['params']['promotions'][15]['count']=11; $mas['params']['promotions'][15]['link']='facebook.com';
$mas['params']['promotions'][16]['nick']='novosteycom'; $mas['params']['promotions'][16]['count']=11; $mas['params']['promotions'][16]['link']='facebook.com';
$mas['params']['promotions'][17]['nick']='VirusPanin'; $mas['params']['promotions'][17]['count']=11; $mas['params']['promotions'][17]['link']='vkontakte.ru';
$mas['params']['promotions'][18]['nick']='zhenyek'; $mas['params']['promotions'][18]['count']=10; $mas['params']['promotions'][18]['link']='twitter.com';
$mas['params']['promotions'][19]['nick']='pravdzivyj'; $mas['params']['promotions'][19]['count']=10; $mas['params']['promotions'][19]['link']='twitter.com';
$mas['params']['promotions'][20]['nick']='onton_a'; $mas['params']['promotions'][20]['count']=10; $mas['params']['promotions'][20]['link']='vkontakte.ru';
$mas['params']['promotions'][21]['nick']='itrus'; $mas['params']['promotions'][21]['count']=10; $mas['params']['promotions'][21]['link']='vkontakte.ru';
$mas['params']['promotions'][22]['nick']='drlyvsy'; $mas['params']['promotions'][22]['count']=10; $mas['params']['promotions'][22]['link']='vkontakte.ru';
$mas['params']['promotions'][23]['nick']='dimka48'; $mas['params']['promotions'][23]['count']=10; $mas['params']['promotions'][23]['link']='vkontakte.ru';
$mas['params']['promotions'][24]['nick']='chibisanton'; $mas['params']['promotions'][24]['count']=10; $mas['params']['promotions'][24]['link']='vkontakte.ru';
$mas['params']['promotions'][25]['nick']='borutskiy'; $mas['params']['promotions'][25]['count']=10; $mas['params']['promotions'][25]['link']='vkontakte.ru';
$mas['params']['promotions'][26]['nick']='andrewf'; $mas['params']['promotions'][26]['count']=10; $mas['params']['promotions'][26]['link']='vkontakte.ru';
$mas['params']['promotions'][27]['nick']='SRG222'; $mas['params']['promotions'][27]['count']=10; $mas['params']['promotions'][27]['link']='vkontakte.ru';
$mas['params']['promotions'][28]['nick']='Miliaev'; $mas['params']['promotions'][28]['count']=10; $mas['params']['promotions'][28]['link']='vkontakte.ru';

$mas['params']['speakers'][0]['nick']='Marfitsin'; $mas['params']['speakers'][0]['count']=42; $mas['params']['speakers'][0]['link']='livejournal.com';
$mas['params']['speakers'][1]['nick']='isegals'; $mas['params']['speakers'][1]['count']=36; $mas['params']['speakers'][1]['link']='livejournal.com';
$mas['params']['speakers'][2]['nick']='technewsru'; $mas['params']['speakers'][2]['count']=27; $mas['params']['speakers'][2]['link']='livejournal.com';
$mas['params']['speakers'][3]['nick']='mrozentsvayg'; $mas['params']['speakers'][3]['count']=27; $mas['params']['speakers'][3]['link']='vkontakte.ru';
$mas['params']['speakers'][4]['nick']='artranceit'; $mas['params']['speakers'][4]['count']=27; $mas['params']['speakers'][4]['link']='twitter.com';
$mas['params']['speakers'][5]['nick']='Alexandrlaw'; $mas['params']['speakers'][5]['count']=27; $mas['params']['speakers'][5]['link']='livejournal.com';
$mas['params']['speakers'][6]['nick']='FOR_HTC'; $mas['params']['speakers'][6]['count']=20; $mas['params']['speakers'][6]['link']='twitter.com';
$mas['params']['speakers'][7]['nick']='bigarando'; $mas['params']['speakers'][7]['count']=19; $mas['params']['speakers'][7]['link']='livejournal.com';
$mas['params']['speakers'][8]['nick']='android_ua'; $mas['params']['speakers'][8]['count']=19; $mas['params']['speakers'][8]['link']='livejournal.com';
$mas['params']['speakers'][9]['nick']='velolive'; $mas['params']['speakers'][9]['count']=18; $mas['params']['speakers'][9]['link']='livejournal.com';
$mas['params']['speakers'][10]['nick']='andrewbabkin'; $mas['params']['speakers'][10]['count']=18; $mas['params']['speakers'][10]['link']='livejournal.com';
$mas['params']['speakers'][11]['nick']='YouHTC'; $mas['params']['speakers'][11]['count']=18; $mas['params']['speakers'][11]['link']='facebook.com';
$mas['params']['speakers'][12]['nick']='Xill47'; $mas['params']['speakers'][12]['count']=18; $mas['params']['speakers'][12]['link']='livejournal.com';
$mas['params']['speakers'][13]['nick']='Marrkyboy'; $mas['params']['speakers'][13]['count']=18; $mas['params']['speakers'][13]['link']='facebook.com';
$mas['params']['speakers'][14]['nick']='HTC_Ru'; $mas['params']['speakers'][14]['count']=12; $mas['params']['speakers'][14]['link']='livejournal.com';
$mas['params']['speakers'][15]['nick']='thieplh'; $mas['params']['speakers'][15]['count']=11; $mas['params']['speakers'][15]['link']='livejournal.com';
$mas['params']['speakers'][16]['nick']='novosteycom'; $mas['params']['speakers'][16]['count']=11; $mas['params']['speakers'][16]['link']='livejournal.com';
$mas['params']['speakers'][17]['nick']='VirusPanin'; $mas['params']['speakers'][17]['count']=11; $mas['params']['speakers'][17]['link']='livejournal.com';
$mas['params']['speakers'][18]['nick']='zhenyek'; $mas['params']['speakers'][18]['count']=10; $mas['params']['speakers'][18]['link']='livejournal.com';
$mas['params']['speakers'][19]['nick']='pravdzivyj'; $mas['params']['speakers'][19]['count']=10; $mas['params']['speakers'][19]['link']='twitter.com';
$mas['params']['speakers'][20]['nick']='onton_a'; $mas['params']['speakers'][20]['count']=10; $mas['params']['speakers'][20]['link']='livejournal.com';
$mas['params']['speakers'][21]['nick']='itrus'; $mas['params']['speakers'][21]['count']=10; $mas['params']['speakers'][21]['link']='livejournal.com';
$mas['params']['speakers'][22]['nick']='drlyvsy'; $mas['params']['speakers'][22]['count']=10; $mas['params']['speakers'][22]['link']='twitter.com';
$mas['params']['speakers'][23]['nick']='dimka48'; $mas['params']['speakers'][23]['count']=10; $mas['params']['speakers'][23]['link']='livejournal.com';
$mas['params']['speakers'][24]['nick']='chibisanton'; $mas['params']['speakers'][24]['count']=10; $mas['params']['speakers'][24]['link']='twitter.com';
$mas['params']['speakers'][25]['nick']='borutskiy'; $mas['params']['speakers'][25]['count']=10; $mas['params']['speakers'][25]['link']='livejournal.com';
$mas['params']['speakers'][26]['nick']='andrewf'; $mas['params']['speakers'][26]['count']=10; $mas['params']['speakers'][26]['link']='livejournal.com';
$mas['params']['speakers'][27]['nick']='SRG222'; $mas['params']['speakers'][27]['count']=10; $mas['params']['speakers'][27]['link']='vkontakte.ru';
$mas['params']['speakers'][28]['nick']='Miliaev'; $mas['params']['speakers'][28]['count']=10; $mas['params']['speakers'][28]['link']='livejournal.com';

$mas['params']['words'][0]['word']='desire'; $mas['params']['words'][0]['count']=705;
$mas['params']['words'][1]['word']='новый'; $mas['params']['words'][1]['count']=553;
$mas['params']['words'][2]['word']='продам'; $mas['params']['words'][2]['count']=552;
$mas['params']['words'][3]['word']='продаю'; $mas['params']['words'][3]['count']=522;
$mas['params']['words'][4]['word']='android'; $mas['params']['words'][4]['count']=394;
$mas['params']['words'][5]['word']='куплю'; $mas['params']['words'][5]['count']=321;
$mas['params']['words'][6]['word']='touch'; $mas['params']['words'][6]['count']=318;
$mas['params']['words'][7]['word']='меня'; $mas['params']['words'][7]['count']=295;
$mas['params']['words'][8]['word']='hero'; $mas['params']['words'][8]['count']=242;
$mas['params']['words'][9]['word']='есть'; $mas['params']['words'][9]['count']=235;
$mas['params']['words'][10]['word']='google'; $mas['params']['words'][10]['count']=223;
$mas['params']['words'][11]['word']='коммуникаторы'; $mas['params']['words'][11]['count']=202;
$mas['params']['words'][12]['word']='wildfire'; $mas['params']['words'][12]['count']=171;
$mas['params']['words'][13]['word']='планшет'; $mas['params']['words'][13]['count']=164;
$mas['params']['words'][14]['word']='москва'; $mas['params']['words'][14]['count']=152;
$mas['params']['words'][15]['word']='diamond'; $mas['params']['words'][15]['count']=148;
$mas['params']['words'][16]['word']='смартфон'; $mas['params']['words'][16]['count']=146;
$mas['params']['words'][17]['word']='телефон'; $mas['params']['words'][17]['count']=143;
$mas['params']['words'][18]['word']='сегодня'; $mas['params']['words'][18]['count']=135;
$mas['params']['words'][19]['word']='samsung'; $mas['params']['words'][19]['count']=128;
$mas['params']['words'][20]['word']='windows'; $mas['params']['words'][20]['count']=126;
$mas['params']['words'][21]['word']='санкт'; $mas['params']['words'][21]['count']=112;
$mas['params']['words'][22]['word']='если'; $mas['params']['words'][22]['count']=112;
$mas['params']['words'][23]['word']='объявление'; $mas['params']['words'][23]['count']=110;
$mas['params']['words'][24]['word']='iphone'; $mas['params']['words'][24]['count']=108;
$mas['params']['words'][25]['word']='legend'; $mas['params']['words'][25]['count']=106;
$mas['params']['words'][26]['word']='мобильный'; $mas['params']['words'][26]['count']=104;
$mas['params']['words'][27]['word']='видео'; $mas['params']['words'][27]['count']=99;

echo json_encode($mas);*/

?>