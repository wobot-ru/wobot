<?

require_once('/var/www/com/config.php');
require_once('/var/www/com/db.php');

$db=new database();
$db->connect();
// echo 'UPDATE blog_orders SET third_sources=2 WHERE order_id='.intval($_POST['order_id']);
$db->query('UPDATE blog_orders SET third_sources=2 WHERE order_id='.intval($_POST['order_id']));

?>