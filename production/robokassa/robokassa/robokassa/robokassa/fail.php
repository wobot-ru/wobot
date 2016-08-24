<?
require_once('/var/www/new/com/config.php');
require_once('/var/www/new/com/func.php');
require_once('/var/www/new/com/db.php');
require_once('/var/www/new/bot/kernel.php');
//require_once('auth.php');

$db = new database();
$db->connect();

$inv_id = $_REQUEST["InvId"];

$rs=$db->query('UPDATE billing SET date='.time().', status=-1 WHERE bill_id='.intval($inv_id));

echo "<meta HTTP-EQUIV=\"REFRESH\" content=\"0; url=http://beta.wobot.ru\" />Вы отказались от оплаты. Заказ# $inv_id\n";
echo "You have refused payment. Order# $inv_id\n";


?>


