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
if ($user['tariff_id']!=3)
{
	$db->query('DELETE FROM blog_preset WHERE order_id='.intval($_GET['order_id']).' AND id='.intval($_GET['id']).' LIMIT 1');
	$mas['status']='ok';
	echo json_encode($mas);
}

?>