<?
require_once('/var/www/com/config.php');
require_once('/var/www/com/func.php');
require_once('/var/www/com/db.php');
require_once('/var/www/bot/kernel.php');

$db = new database();
$db->connect();

$m = new Memcached();
$m->addServer('localhost', 11211);
$m->setOption(Memcached::OPT_COMPRESSION, false);

$q=$db->query('SELECT order_id,order_src FROM blog_orders LIMIT 1');
while ($order=$db->fetch($q))
{
	foreach ($order[''])
	$m_dinams=json_decode($m->get('order_'.$row['order_id']),true);
}
print_r(json_decode($order['order_src'],true));

?>