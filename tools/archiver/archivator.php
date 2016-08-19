<?

require_once('/var/www/com/db.php');
require_once('/var/www/com/config.php');

$db=new database();
$db->connect();

$mnot_user=array('1126','3322');

echo 'SELECT * FROM user_tariff as a LEFT JOIN blog_orders as b ON a.user_id=b.user_id WHERE a.ut_date<'.mktime(0,0,0,date('n')-1,date('j'),date('Y'));
$quser=$db->query('SELECT * FROM user_tariff WHERE ut_date<'.mktime(0,0,0,date('n')-1,date('j'),date('Y')).' AND ut_date!=0 OR user_id=145 ORDER BY user_id ASC');
// echo $db->num_rows($quser);
while ($user=$db->fetch($quser))
{
	if (in_array($user['user_id'],$mnot_user)) continue;
	if ($user['user_id']!=145) $qorder=$db->query('SELECT * FROM blog_orders WHERE user_id='.$user['user_id'].' AND ut_id!=0');
	else $qorder=$db->query('SELECT * FROM blog_orders WHERE user_id=145 AND ut_id=153 AND order_end<'.mktime(0,0,0,date('n'),date('j')-14,date('Y')));
	while ($order=$db->fetch($qorder))
	{
		echo $order['order_id'].' '.$user['user_id']."\n";
		do
		{
			echo 'idle...'."\n";
			if ($count_ps==2) sleep(1);
			else sleep(5);
		}
		while (shell_exec('ps ax | grep "dumper.php" | wc -l')>5);
		$count_ps=shell_exec('ps ax | grep "dumper.php" | wc -l');
		echo 'count = '.$count_ps."\n";
		$descriptorspec=array(
			0 => array("file","/dev/null","a"),
			1 => array("file","/dev/null","a"),
			2 => array("file","/dev/null","a")
		);

		$cwd='/var/www/bot/';
		$end=array();
		$process=proc_open('php /var/www/tools/archiver/dumper.php '.$order['order_id'].' &',$descriptorspec,$pipes,$cwd,$end);/* or {
			echo json_encode(array('status'=>'fail'), true);
			die();
		};*/

		if (is_resource($process))
		{
			//echo 'return: '.$return_value=proc_close($process);
			if (intval($return_value=proc_close($process))==0) $c=1;
		}
		// sleep(10);
		$count++;
	}
}
echo $count;

?>