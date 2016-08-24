<?
//Авто пересбор запросов с нулевыми последними 2мя днями
require_once('/var/www/com/config.php');
require_once('/var/www/com/config_server.php');
require_once('/var/www/daemon/com/func.php');
require_once('/var/www/com/db.php');
require_once('/var/www/daemon/bot/kernel.php');

error_reporting(E_ERROR);

$order_delta=$_SERVER['argv'][1];
sleep($order_delta);
$fp = fopen('/var/www/pids/freash'.$order_delta.'.pid', 'w');
fwrite($fp, getmypid());
fclose($fp);

$db = new database();
$db->connect();
while (1)
{
	sleep(1);
	echo '.';
	if (date('i') % 60!=0) continue;
	// sleep(10800);
	if (!$db->ping()) {
		echo "MYSQL disconnected, reconnect after 10 sec...\n";
		sleep(10);
		$db->connect();
		die();
	}
	//echo 'SELECT a.order_start,a.order_id FROM blog_orders as a LEFT JOIN users as b ON a.user_id=b.user_id LEFT JOIN user_tariff as c ON b.user_id=c.user_id WHERE a.order_end>'.(time()-86400).' AND a.user_id!=0 AND c.ut_date>'.time();
	//die();
	$i=0;
	echo 'SELECT order_id,user_id,ut_id,order_name,order_keyword,order_start,order_end,third_sources,order_lang,order_analytics,order_spam,order_settings FROM blog_orders WHERE user_id!=145 and third_sources!=0 and (order_end>='.mktime(0,0,0,date('n'),date('j')-1,date('Y')).') AND user_id!=0 AND ut_id!=0 ORDER BY order_id DESC'."\n";
	// die();
	$orders=$db->query('SELECT order_id,user_id,ut_id,order_name,order_keyword,order_start,order_end,third_sources,order_lang,order_analytics,order_spam,order_settings FROM blog_orders WHERE user_id!=145 and third_sources!=0 and (order_end>='.mktime(0,0,0,date('n'),date('j')-1,date('Y')).') AND user_id!=0 AND ut_id!=0 ORDER BY order_id DESC');
	while ($order=$db->fetch($orders))
	{
		// echo 'http://wobotparser.cloudapp.net/engine/api/addtask_yblogs.php?order_id='.$order['order_id'].'&host='.$config['server_ip']."\n";
		file_get_contents('http://128.199.44.230/engine/api/addtask_yblogs.php?order_id='.$order['order_id'].'&host='.$config_server['server_ip']);
	}
	sleep(1);
}
?>
