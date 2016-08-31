<?
require_once('/var/www/daemon/com/db.php');
require_once('/var/www/daemon/com/config.php');

$db=new database();
$db->connect();

/*$order_delta = $_SERVER['argv'][1];
$debug_mode = $_SERVER['argv'][2];
$fp = fopen('/var/www/pids/alert' . $order_delta . '.pid', 'w');
fwrite($fp, getmypid());
fclose($fp);
*/
error_reporting(0);

/*$order_id = 6930;
$user_id=4200;*/
$period = 4;
$average = 7;
while (1)
{
	$res = $db->query('SELECT user_id, user_settings FROM users WHERE user_settings LIKE \'%themeAlert%\'');
	$i=0;
	while ($temp=$db->fetch($res))
		    {
		    	$user_settings = json_decode($temp['user_settings'],true);
		    	if($user_settings['themeAlert']==1){
		    		$users[$i]['user_id']=$temp['user_id'];
		    		$i++;
		    	}
		    }
	unset($temp);
	foreach ($users as $key => $value) {
		//echo 'SELECT order_id FROM blog_orders WHERE user_id='.$value['user_id']."\n";
		$order_ids = $db->query('SELECT order_id FROM blog_orders WHERE user_id='.$value['user_id']);
		//var_dump($order_ids);
		while ($temp = $db->fetch($order_ids))
		    {
		    	$day_start=mktime(date('H'),date('i'),date('s'),date('n'),date('j')-$average,date('Y'));
				$day_end=mktime(date('H'),date('i'),date('s'),date('n'),date('j'),date('Y'));

				$period_start=mktime(date('H')-$period,date('i'),date('s'),date('n'),date('j'),date('Y'));
				$period_end=mktime(date('H'),date('i'),date('s'),date('n'),date('j'),date('Y'));

				//var_dump($period_end);

				$day_posts = $db->query('SELECT post_id FROM blog_post WHERE order_id='.$temp['order_id'].' AND post_time > '.$day_start.' AND post_time < '.$day_end);
				$period_posts = $db->query('SELECT post_id FROM blog_post WHERE order_id='.$temp['order_id'].' AND post_time > '.$period_start.' AND post_time < '.$period_end);
				$day_count = ($db->num_rows($day_posts)*$period)/(24*$average);
				$period_count = $db->num_rows($period_posts);
				echo "order_id ".$temp['order_id']." day_count ".$day_count." period_count ".$period_count."\n";
				if($day_count<$period_count){
					echo "SEND ALERT \n";

					//add cash update
				}
		    }
	}
	die();
	echo 'sleep...';
	sleep(3600);
}

?>