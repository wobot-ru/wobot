<?
require_once('/var/www/new/com/config.php');
require_once('/var/www/new/com/func.php');
require_once('/var/www/new/com/db.php');
require_once('/var/www/new/bot/kernel.php');
//require_once('auth.php');

$db = new database();
$db->connect();

$fl=@fopen("payment.log","a+") or
          die("error");
fputs($fl,'success: '.json_encode($_REQUEST)."\n\n");
fclose($fl);

// регистрационная информация (пароль #1)
// registration info (password #1)
$mrh_pass1 = "r1o2m3a4";

// чтение параметров
// read parameters
$out_summ = $_REQUEST["OutSum"];
$inv_id = $_REQUEST["InvId"];
$shp_item = $_REQUEST["Shp_item"];
$crc = $_REQUEST["SignatureValue"];

$crc = strtoupper($crc);

$my_crc = strtoupper(md5("$out_summ:$inv_id:$mrh_pass1:Shp_item=$shp_item"));

// проверка корректности подписи
// check signature
/*
if ($my_crc != $crc)
{
  echo "bad sign\n";
  exit();
}
*/

//$inv_id=293;
//$out_summ='3';


// проверка наличия номера счета в истории операций
if (intval($user['user_id'])==0)
{
	//echo "<meta HTTP-EQUIV=\"REFRESH\" content=\"0; url=http://www.wobot.ru\" />Операция прошла успешно\n";
	echo'<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
	<html lang="ru-RU" xml:lang="ru-RU" xmlns="http://www.w3.org/1999/xhtml">
	  <head>
	    <title>Оплата спецпредложения WOBOT &beta;</title>
	    <meta content="Wobot" name="keywords" />
	    <meta content="Wobot media" name="author" />
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	  </head>
	  <body style="background: #fff;">
	  <h1>Ваш код: 905'.intval($inv_id).'</h1>
	  </body>
	</html>
';
}
echo "Operation of payment is successfully completed\n";
// check of number of the order info in history of operations
/*$f=@fopen("order.txt","r+") or die("error");

while(!feof($f))
{
  $str=fgets($f);

  $str_exp = explode(";", $str);
  if ($str_exp[0]=="order_num :$inv_id")
  { 
	echo "<meta HTTP-EQUIV=\"REFRESH\" content=\"0; url=http://beta.wobot.ru/themes_list.html?success\" />Операция прошла успешно\n";
	echo "Operation of payment is successfully completed\n";
  }
}
fclose($f);*/
?>


