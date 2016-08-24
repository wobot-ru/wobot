<?

require_once('/var/www/com/config.php');
require_once('/var/www/com/db.php');
require_once('/var/www/com/auth.php');

$db = new database();
$db->connect();

$qorid=$db->query('SELECT order_id FROM blog_orders as a LEFT JOIN user_tariff as b ON a.ut_id=b.ut_id LEFT JOIN users as c ON b.user_id=c.user_id WHERE c.user_id='.intval($_GET['user_id']));
while ($orid=$db->fetch($qorid))
{
	$db->query('DELETE FROM blog_post WHERE order_id='.intval($orid['order_id']));
	echo 'DELETE FROM blog_post WHERE order_id='.intval($orid['order_id']);
	$db->query('DELETE FROM blog_full_com WHERE ful_com_order_id='.intval($orid['order_id']));
	echo 'DELETE FROM blog_full_com WHERE ful_com_order_id='.intval($orid['order_id']);
}
$db->query('DELETE FROM blog_orders WHERE user_id='.intval($_GET['user_id']));
echo 'DELETE FROM blog_orders WHERE user_id='.intval($_POST['user_id']);
$db->query('DELETE FROM users WHERE user_id='.intval($_GET['user_id']));
echo 'DELETE FROM users WHERE user_id='.intval($_POST['user_id']);
$db->query('DELETE FROM user_tariff WHERE user_id='.intval($_GET['user_id']));
$db->query('DELETE FROM blog_tag WHERE user_id='.intval($_GET['user_id']));
$db->query('DELETE FROM blog_tp WHERE user_id='.intval($_GET['user_id']));
echo 'DELETE FROM user_tariff WHERE user_id='.intval($_POST['user_id']);
$db->query('UPDATE users set user_email=concat(user_email, \'_deleted\') WHERE user_id='.intval($_GET['user_id']));

// die(json_encode($outmas));

?>