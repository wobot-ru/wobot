<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html lang="ru-RU" xml:lang="ru-RU" xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <title>Утилиты для тестирования WOBOT &beta;</title>
    <meta content="" name="description" />
    <meta content="Wobot" name="keywords" />
    <meta content="Wobot media" name="author" />
    <meta content="all" name="robots" />
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  </head>
<body>
<?
//#!/usr/bin/php
header('X-Frame-Options: GOFORIT');
require_once('/var/www/userjob/com/config.php');
require_once('/var/www/userjob/com/func.php');
require_once('/var/www/userjob/com/db.php');
require_once('/var/www/userjob/bot/kernel.php');
error_reporting(E_ERROR);
ignore_user_abort(true);
set_time_limit(0);
ini_set('max_execution_time',0);
ini_set('default_charset','utf-8');
ob_implicit_flush();
$db = new database();
$db->connect();
echo '<table width="100%">';
if (isset($_POST) && ($_POST['loc']!=''))
{
	if ($_POST['loc']!='N/A'){
    $content_tw_loc=parseUrl('http://geocode-maps.yandex.ru/1.x/?geocode='.urlencode($_POST['loc']).'&key=ANpUFEkBAAAAf7jmJwMAHGZHrcKNDsbEqEVjEUtCmufxQMwAAAAAAAAAAAAvVrubVT4btztbduoIgTLAeFILaQ==&results=1');
	$regextw='/<pos>(?<loc_tw>.*?)<\/pos>/is';
	preg_match_all($regextw,$content_tw_loc,$out_tw);
	}
	else $out_tw['loc_tw'][0]='N/A';
	$rru=$db->query('UPDATE robot_location set loc_coord=\''.$out_tw['loc_tw'][0].'\' WHERE id='.intval($_POST['id']));
	echo '
	<tr><td bgcolor="#aaf">
	обновлено: <b>'.$_POST['loc'].'</b> ('.$_POST['id'].') <i>'.$out_tw['loc_tw'][0].'</i><br><br>
	</td></tr>';
}

$res=$db->query('SELECT *, count(*) as kolvo FROM robot_location WHERE loc_coord=\'\' AND loc!=\'\' LIMIT 1');
$item=$db->fetch($res);

//$item['loc']='iPhone: 60.597340,56.837982';

	$doc = new DOMDocument;

	// We don't want to bother with white spaces
	$doc->preserveWhiteSpace = false;

	$data=parseUrl('http://yandex.ru/yandsearch?text='.$item['loc']);
	//echo $data;
	$doc->loadHTML($data);

	$xpath = new DOMXPath($doc);

	$ent='';
	// We starts from the root element
	$query = '//html/body/div[3]/div/div/div/div[2]/ol/li/div/a/b';
	$entries = $xpath->query($query);
	foreach ($entries as $entry) {
	    $ent=$entry->nodeValue;
		break;
	}
	
	if (strlen($ent)==0)
	{
	$query = '//html/body/div[3]/div/div/div/div[2]/ol/li/div/a';
	$entries = $xpath->query($query);
	foreach ($entries as $entry) {
	    $ent=$entry->nodeValue;
		break;
	}
	}

echo '=='.$item['loc'].'==';
	//ÜT: -6.294457,106.876155
	if (mb_substr($item['loc'],0,4,'UTF-8')=='ÜT: ')
	{
		list($x,$y)=explode(',',$item['loc'],2);
		list($tmp,$x)=explode(' ',$x,2);
		$item['loc']=urlencode($x.', '.$y);
		if ((intval($y)>=102)&&(intval($y)<=110)&&(intval($x)>=-7)&&(intval($x)<=-3)) $ent='Индонезия';		
		$res2=$db->query('SELECT loc FROM robot_location WHERE loc_coord="'.$x.' '.$y.'" LIMIT 1');
		$item2=$db->fetch($res2);
		//if ($ent=='') $ent=$item2['loc'];
	}
	//iPhone: 48.437859,35.003319
	if (mb_substr($item['loc'],0,8,'UTF-8')=='iPhone: ')
	{
		list($x,$y)=explode(',',$item['loc'],2);
		list($tmp,$x)=explode(' ',$x,2);
		$item['loc']=urlencode($x.', '.$y);
		if ((intval($y)>=102)&&(intval($y)<=110)&&(intval($x)>=-7)&&(intval($x)<=-3)) $ent='Индонезия';
		$res2=$db->query('SELECT loc FROM robot_location WHERE loc_coord="'.$x.' '.$y.'" LIMIT 1');
		//echo 'SELECT loc FROM robot_location WHERE loc_coord="'.$y.' '.$x.'" LIMIT 1';
		$item2=$db->fetch($res2);
		//if ($ent=='') $ent=$item2['loc'];
	}
	//iphone:55.663942,37.780788
	if (mb_substr($item['loc'],0,7,'UTF-8')=='iphone:')
	{
		list($x,$y)=explode(',',$item['loc'],2);
		list($tmp,$x)=explode(':',$x,2);
		$item['loc']=urlencode($x.', '.$y);
		if ((intval($y)>=102)&&(intval($y)<=110)&&(intval($x)>=-7)&&(intval($x)<=-3)) $ent='Индонезия';
		$res2=$db->query('SELECT loc FROM robot_location WHERE loc_coord="'.$x.' '.$y.'" LIMIT 1');
		$item2=$db->fetch($res2);
		//if ($ent=='') $ent=$item2['loc'];
	}
	
//$gcoder=file_get_contents('http://maps.googleapis.com/maps/api/geocode/json?address='.$item['loc'].'&sensor=true&hl=ru');
$gcoder=file_get_contents('http://maps.google.com/maps/geo?output=json&q='.$item['loc'].'&hl=ru');
$gcod=json_decode($gcoder,true);
if ($ent=='')
{
	//list($ent,$country)=explode(',',$gcod['Placemark'][0]['address'],2);
	if (strlen($gcod['Placemark'][0]['AddressDetails']['Country']['Locality']['LocalityName'])>0) $ent=$gcod['Placemark'][0]['AddressDetails']['Country']['Locality']['LocalityName'];
	else
	if (strlen($gcod['Placemark'][0]['AddressDetails']['Country']['AdministrativeArea']['Locality']['LocalityName'])>0) $ent=$gcod['Placemark'][0]['AddressDetails']['Country']['AdministrativeArea']['Locality']['LocalityName'];
	else
	$ent=$gcod['Placemark'][0]['AddressDetails']['Country']['AdministrativeArea']['AdministrativeAreaName'];
}
//http://maps.google.com/maps/geo?output=json&q=G%C3%BCtersloh&hl=ru
	
echo '
	<tr><td bgcolor="#afa">
Кол-во пустых городов: <b>'.$item['kolvo'].'</b><br><br>
Указано в анкете: <b>'.$item['loc'].'</b> '.urldecode($item['loc']).'<br>
Искать Гугл: <a href="https://www.google.com/search?hl=ru&q='.urldecode($item['loc']).'" target="_blank">искать</a><br><br>
	</td></tr>
		</table>
<form method="POST">
Название города: <input type="text" name="loc" id="loc" value="'.$ent.'"><br>
<input type="hidden" name="id" value="'.$item['id'].'">
<input type="button" value="хлам" onclick="document.getElementById(\'loc\').value=\'N/A\'"> <input type="submit" value="применить">
</form>
<!--<iframe src ="http://yandex.ru/yandsearch?text='.$item['loc'].'" width="100%" height="200">
<p>Your browser does not support iframes.</p> </iframe>-->
	<!--<iframe src="http://maps.google.com/maps/geo?output=xml&q='.urlencode($item['loc']).'&hl=ru" width="100%" height="200"/>
	<p>Your browser does not support iframes.</p>
	</iframe>-->
<textarea cols="50" rows="8">';
//print_r($gcod);
echo'Определение Гуглом:
Адрес: '.$gcod['Placemark'][0]['address'].'
Город: '.$gcod['Placemark'][0]['AddressDetails']['Country']['AdministrativeArea']['Locality']['LocalityName'].'
Административный центр: '.$gcod['Placemark'][0]['AddressDetails']['Country']['AdministrativeArea']['AdministrativeAreaName'].'
Страна: '.$gcod['Placemark'][0]['AddressDetails']['Country']['CountryName'].'
Широта: '.$gcod['Placemark'][0]['Point']['coordinates'][0].'
Долгота: '.$gcod['Placemark'][0]['Point']['coordinates'][1].'
</textarea>
';
//$content_tw_loc=parseUrl('http://geocode-maps.yandex.ru/1.x/?geocode='.urlencode($user['location']).'&key=ANpUFEkBAAAAf7jmJwMAHGZHrcKNDsbEqEVjEUtCmufxQMwAAAAAAAAAAAAvVrubVT4btztbduoIgTLAeFILaQ==&results=1');

?>
</body>
</html>