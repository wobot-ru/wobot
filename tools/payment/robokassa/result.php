<?
require_once('/var/www/new/com/config.php');
require_once('/var/www/new/com/func.php');
require_once('/var/www/new/com/db.php');
require_once('/var/www/new/bot/kernel.php');
//require_once('auth.php');

$db = new database();
$db->connect();

//print_r($user);

// регистрационная информация (пароль #2)
// registration info (password #2)
$mrh_pass2 = "r1o2m3a4";

//установка текущего времени
//current date
$tm=getdate(time()+9*3600);
$date="$tm[year]-$tm[mon]-$tm[mday] $tm[hours]:$tm[minutes]:$tm[seconds]";

// чтение параметров
// read parameters
$out_summ = $_REQUEST["OutSum"];
$inv_id = $_REQUEST["InvId"];
$shp_item = $_REQUEST["Shp_item"];
$crc = $_REQUEST["SignatureValue"];

$crc = strtoupper($crc);

$my_crc = strtoupper(md5("$out_summ:$inv_id:$mrh_pass2:Shp_item=$shp_item"));

// проверка корректности подписи
// check signature
if ($my_crc !=$crc)
{
  echo "bad sign\n";
  exit();
}

// признак успешно проведенной операции
// success
echo "OK$inv_id\n";

// запись в файл информации о прведенной операции
// save order info to file
$f=@fopen("order.txt","a+") or
          die("error");
fputs($f,"order_num :$inv_id; Item: $shp_item;Summ :$out_summ;Date :$date\n");
fclose($f);

$rs=$db->query('UPDATE billing SET date='.time().', status=1, money='.$out_summ.' WHERE bill_id='.intval($inv_id));

$rs2=$db->query('UPDATE users SET user_money=user_money+'.intval($out_summ).' WHERE user_id='.intval($shp_item));
?>


