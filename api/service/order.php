<?

require_once('/var/www/com/db.php');
require_once('/var/www/com/config.php');

$db=new database();
$db->connect();

$qorder=$db->query('SELECT * FROM blog_orders WHERE order_id='.$_GET['order_id'].' LIMIT 1');
$order=$db->fetch($qorder);
echo json_encode($order);

?>