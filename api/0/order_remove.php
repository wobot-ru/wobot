<?
require_once('/var/www/new/com/config.php');
require_once('/var/www/new/com/func.php');
require_once('/var/www/new/com/db.php');
require_once('/var/www/new/bot/kernel.php');
require_once('auth.php');

date_default_timezone_set ( 'Europe/Moscow' );

$db = new database();
$db->connect();

auth();
if (!$loged) die();

if (($user['user_mid']!=0) && ($user['user_mid_priv']==3)) 
{
	$mas['status']='ok';
	echo json_encode($mas);
	die();	
}

$qorder=$db->query('SELECT * FROM blog_orders WHERE order_id='.intval($_POST['order_id']).' LIMIT 1');
$order=$db->fetch($qorder);
if ($db->num_rows($qorder)==0) 
{
	echo json_encode(array('status'=>'1'));
	die();
}
//----какой пользователь удалил----
$settings=json_decode($order['order_settings'],true);
$settings['old_user']=$user['user_id'];

$db->query('UPDATE blog_orders SET user_id=145,ut_id=153,order_settings=\''.addslashes(json_encode($settings)).'\' WHERE user_id='.$order['user_id'].' AND order_id='.intval($_POST['order_id']));

$outmas['status']='ok';
echo json_encode($outmas);

?>