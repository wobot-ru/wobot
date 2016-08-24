<?

require_once('/var/www/com/config.php');
require_once('/var/www/com/db.php');
require_once('/var/www/com/auth.php');

$db = new database();
$db->connect();

$db->query('UPDATE blog_orders SET user_id=145,ut_id=153 WHERE order_id='.intval($_POST['order_id']));

?>