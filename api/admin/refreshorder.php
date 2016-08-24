<?

require_once('/var/www/com/config.php');
require_once('/var/www/com/db.php');
require_once('/var/www/com/auth.php');
require_once('/var/www/com/checker.php');

$db = new database();
$db->connect();

$db->query('UPDATE blog_orders SET third_sources=2 WHERE order_id='.$_POST['order_id']);

?>