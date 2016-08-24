<?

require_once('/var/www/com/config.php');
require_once('/var/www/com/db.php');
require_once('/var/www/com/auth.php');

$db = new database();
$db->connect();

// echo 'UPDATE blog_orders SET order_name=\''.addslashes($_POST['order_name']).'\',order_keyword=\''.addslashes($_POST['order_keyword']).'\',order_start='.strtotime($_POST['order_start']).',order_end='.strtotime($_POST['order_end']).',ful_com='.$_POST['ful_com'].',order_engage='.$_POST['order_engage'].',order_nastr='.($_POST['order_nastr']=='on'?'1':'0').',order_lang='.$_POST['ord_lan'].' WHERE order_id='.$_POST['order_id'];
$db->query('UPDATE blog_orders SET user_id='.$_POST['user_id'].' WHERE order_id='.$_POST['order_id']);
// $db->query('UPDATE user_tariff SET ut_date='.strtotime($_POST['ut_date']).',tariff_id='.$_POST['tariff_id'].' WHERE user_id='.$_POST['user_id'])

?>