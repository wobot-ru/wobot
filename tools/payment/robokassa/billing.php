<?
require_once('/var/www/new/com/config.php');
require_once('/var/www/new/com/func.php');
require_once('/var/www/new/com/db.php');
require_once('/var/www/new/bot/kernel.php');
//require_once('auth.php');

$db = new database();
$db->connect();

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

$rs=$db->query('UPDATE billing SET date='.time().', status=2, money="'.$out_summ.'" WHERE bill_id='.intval($inv_id));

$res=$db->query('SELECT * FROM billing WHERE bill_id='.intval($inv_id).' LIMIT 1');
$bill=$db->fetch($res);

$res2=$db->query('SELECT * FROM users WHERE user_id='.intval($bill['user_id']).' LIMIT 1');
//echo 'SELECT * FROM users WHERE user_id='.intval($bill['user_id']).' LIMIT 1';
$user=$db->fetch($res2);

$res3=$db->query('SELECT * FROM user_tariff WHERE user_id='.intval($bill['user_id']).' LIMIT 1');
$ut=$db->fetch($res3);

if (($ut['tariff_id']==3)||($ut['ut_date']==0))
{//С демо на платный
	$res=$db->query('INSERT INTO billing (user_id, money, date, status, tariff_id, months) values ('.$user['user_id'].', '.(intval($bill['money'])*(-1)).', '.time().',2,'.$bill['tariff_id'].','.$bill['months'].')');
	//echo 'INSERT INTO billing (user_id, money, date, status, tariff_id, months) values ('.$user['user_id'].', '.(intval($bill['money'])*(-1)).', '.time().',2,'.$bill['tariff_id'].','.$bill['months'].')';
	//echo "\n";
	$doit=$db->fetch($res);
	$res=$db->query('UPDATE user_tariff set ut_date='.mktime(0,0,0,date('n')+$bill['months'],date('j'),date('Y')).', tariff_id='.$bill['tariff_id'].' WHERE ut_id='.$ut['ut_id']);
	//echo $bill['months']."\n";
	//echo 'UPDATE user_tariff set ut_date='.mktime(0,0,0,date('n')+$bill['months'],date('j'),date('Y')).', tariff_id='.$bill['tariff_id'].' WHERE ut_id='.$ut['ut_id'];
	//echo "\n";
	$doit=$db->fetch($res);
	$res=$db->query('UPDATE users set user_active=2 WHERE user_id='.$user['user_id']);
	//echo 'UPDATE users set user_active=2 WHERE user_id='.$user['user_id'];
	//echo "\n";
	$doit=$db->fetch($res);
}

//Продление
//Замена с продлением


// проверка наличия номера счета в истории операций
echo "<meta HTTP-EQUIV=\"REFRESH\" content=\"0; url=http://beta.wobot.ru/themes_list.html?success\" />Операция прошла успешно\n";
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


