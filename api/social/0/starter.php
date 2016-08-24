<?

require_once('com/db.php');
require_once('com/config.php');
require_once('bot/kernel.php');
require_once('com/func_gvk.php');

error_reporting(0);

$db = new database();
$db->connect();

while (1)
{
	//общая очередь
	$qus=$db->query('SELECT * FROM group_orders as a LEFT JOIN users as b ON a.user_id=b.user_id WHERE a.group_finished=1 AND b.vk_token=\'\' LIMIT 1');
	$order=$db->fetch($qus);
	if (intval($order['id'])==0)
	{
		$qus=$db->query('SELECT * FROM group_orders as a LEFT JOIN users as b ON a.user_id=b.user_id WHERE a.group_finished=0 AND b.vk_token=\'\' LIMIT 1');
		$order_go=$db->fetch($qus);
		print_r($order_go);
		if (intval($order_go['id'])!=0)
		{
			if (preg_match('/acc_/isu',$order_go['group_link']))
			{
				run_crawling($order_go['id'],'acc');
			}
			else
			{
				run_crawling($order_go['id'],'grp');
			}
		}
	}
	//continue;
	//с токенами
	$qus=$db->query('SELECT * FROM users WHERE vk_token!=\'\'');
	while ($user=$db->fetch($qus))
	{
		$qord=$db->query('SELECT * FROM group_orders WHERE user_id='.$user['user_id'].' AND group_finished=1');
		$order_lc=$db->fetch($qord);
		if (intval($order_lc['id'])==0)
		{
			$qord1=$db->query('SELECT * FROM group_orders WHERE user_id='.$user['user_id'].' AND group_finished=0 LIMIT 1');
			$og=$db->fetch($qord1);
			print_r($og);
			if (intval($og['id'])!=0)
			{
				echo '!'.$og['id'].'! ';
				if (preg_match('/acc_/isu',$og['group_link']))
				{
					run_crawling($og['id'],'acc');
				}
				else
				{
					run_crawling($og['id'],'grp');
				}
			}
		}
	}

	echo 'idle...';
	sleep(5);
}

?>