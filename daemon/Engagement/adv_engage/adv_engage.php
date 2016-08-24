<?

require_once('/var/www/com/db.php');
require_once('/var/www/com/config.php');
require_once('../bot/kernel.php');
require_once('get_etwitter.php');

$db=new database();
$db->connect();

$order_delta=$_SERVER['argv'][1];
$debug_mode=$_SERVER['argv'][2];
$fp = fopen('/var/www/pids/egtw'.$order_delta.'.pid', 'w');
fwrite($fp, getmypid());
fclose($fp);
echo $order_delta;

while (1)
{
    if (!$db->ping()) {
        echo "MYSQL disconnected, reconnect after 10 sec...\n";
        sleep(10);
        $db->connect();
        die();
    }
	$qorder=$db->query('SELECT * FROM blog_orders as c LEFT JOIN users as a ON a.user_id=c.user_id LEFT JOIN user_tariff as b ON a.user_id=b.user_id WHERE b.tariff_id NOT IN (3,16) AND c.order_end>'.time().' AND b.ut_date>'.time().' AND MOD(order_id,'.$_SERVER['argv'][1].')='.$_SERVER['argv'][2]);
	while ($order=$db->fetch($qorder))
	{
		echo $order['order_id']."\n";
		$qpost=$db->query('SELECT post_id,post_link,post_host,post_content,post_time,post_engage FROM blog_post WHERE post_engage!=0 AND post_time>'.(time()-86400).' AND order_id='.$order['order_id'].' AND post_host=\'twitter.com\' ORDER BY post_id DESC LIMIT 100');
		while ($post=$db->fetch($qpost))
		{
			// print_r($post);
			sleep(1);
			echo $post['post_engage'].' '.$post['post_id'].' '.$post['post_link']."\n";
			$retweets=get_retweets(trim($post['post_link']));
			print_r($retweets);
			foreach ($retweets as $retweet)
			{
				if (mb_strtolower($post['post_link'],'UTF-8')==mb_strtolower($retweet,'UTF-8')) continue;
				// echo mb_strtolower($post['post_link'],'UTF-8').' '.mb_strtolower($retweet,'UTF-8')."\n";
				$regex='/\/(?<id>[^\/]*?)\/statuse?s?/isu';
				preg_match_all($regex, $retweet, $out);
				$id=$out['id'][0];
				$bb1['blog_id']=0;
				$chbb=$db->query('SELECT * from robot_blogs2 WHERE blog_login=\''.$id.'\' AND blog_link=\'twitter.com\' LIMIT 1');
				if (mysql_num_rows($chbb)==0)
				{
					$ins_bb=$db->query('INSERT INTO robot_blogs2 (blog_link,blog_login) VALUES (\'twitter\.com\',\''.$id.'\')');
					$bb1['blog_id']=$db->insert_id();
				}
				else
				{
					$bb1=$db->fetch($chbb);
				}
				$qisset=$db->query('SELECT * FROM blog_post WHERE post_link=\''.addslashes($retweet).'\' AND order_id='.$order['order_id'].' LIMIT 1');
				if ($db->num_rows($qisset)==0)
				{
					echo 'INSERT INTO blog_post (order_id,post_time,post_host,post_link,post_content,blog_id) VALUES ('.$order['order_id'].','.$post['post_time'].',\''.addslashes($post['post_host']).'\',\''.addslashes($retweet).'\',\''.addslashes($post['post_content']).'\','.$bb1['blog_id'].')'."\n";
					$qinsert=$db->query('INSERT INTO blog_post (order_id,post_time,post_host,post_link,post_content,blog_id) VALUES ('.$order['order_id'].','.$post['post_time'].',\''.addslashes($post['post_host']).'\',\''.addslashes($retweet).'\',\''.addslashes($post['post_content']).'\','.$bb1['blog_id'].')');
					$db->query('INSERT INTO blog_full_com (ful_com_post_id,ful_com_order_id,ful_com_post) VALUES (\''.$db->insert_id($qinsert).'\',\''.$order['order_id'].'\',\''.addslashes($post['post_content']).'\')');
				}
			}
		}
	}
	sleep(3600);
}

?>