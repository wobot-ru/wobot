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
	$qw=$db->query('SELECT * FROM blog_preset WHERE order_id='.intval($_POST['order_id']).' AND id='.intval($_POST['id']).' LIMIT 1');
	$inf=$db->fetch($qw);

	$out['preset']=json_decode($inf['preset_fields'],true);
	echo json_encode($out);
}
?>