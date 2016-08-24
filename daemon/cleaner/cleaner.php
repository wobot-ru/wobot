<?

require_once('/var/www/com/db.php');
require_once('/var/www/com/config.php');
require_once('clean_func.php');

$redis=new Redis() or die("Can'f load redis module.");
$redis->connect('127.0.0.1');

$db=new database();
$db->connect();

error_reporting(0);
// echo 'SELECT order_id,user_id,ut_id,order_name,order_keyword,order_start,order_end,third_sources,order_lang,order_analytics,order_spam,order_settings FROM blog_orders as a LEFT JOIN user_tariff as b ON a.user_id=b.user_id LEFT JOIN blog_tariff as c ON b.tariff_id=c.tariff_id WHERE user_id!=145 AND third_sources!=0 AND (order_end>='.mktime(0,0,0,date('n'),date('j')-1,date('Y')).') AND user_id!=0 AND ut_id!=0 ORDER BY order_id DESC';

$qorders=$db->query('SELECT a.order_id,a.user_id,a.ut_id,order_start,order_end,c.tariff_id,c.tariff_posts FROM blog_orders as a LEFT JOIN user_tariff as b ON a.user_id=b.user_id LEFT JOIN blog_tariff as c ON b.tariff_id=c.tariff_id WHERE a.user_id!=145 AND a.third_sources!=0 AND (a.order_end>='.mktime(0,0,0,date('n'),date('j')-1,date('Y')).') AND a.user_id!=0 AND a.ut_id!=0 ORDER BY a.order_id DESC');
while ($order=$db->fetch($qorders))
{
	$info=$redis->get('orders_'.$order['order_id']);
	$info=json_decode($info,true);
	$count_post=intval($info['count_post']);
	if ($count_post>$order['tariff_posts']) 
	{
		echo $count_post.' '.$order['order_id'].' '.$order['tariff_id']."\n";
		$new_start_time=remove_hill($order);
		echo 'UPDATE blog_orders SET order_start='.$new_start_time.' WHERE order_id='.$order['order_id']."\n";
		for ($t=$order['order_start'];$t<$new_start_time;$t+=86400)
		{
			echo 'REMOVE order_'.$t."\n";
		}
		echo 'cash '.$order['order_id']."\n";
	}
}

?>