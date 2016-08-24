<?

require_once('/var/www/bot/kernel.php');
require_once('/var/www/com/db.php');
require_once('/var/www/com/config.php');
require_once('func_fb.php');

$redis = new Redis();    
$redis->connect('127.0.0.1');

$db=new database();
$db->connect();

$mat=json_decode($redis->get('at_fb'),true);

$order_id=$_SERVER['argv'][1];

$qpost=$db->query('SELECT * FROM blog_post as a LEFT JOIN robot_blogs2 as b ON a.blog_id=b.blog_id WHERE order_id='.$order_id.' AND post_host=\'facebook.com\' AND blog_login=\'\'');
while ($post=$db->fetch($qpost))
{
	echo $post['post_link']."\n";
	if ($post['blog_login']=='')
	{
		$author=get_author($post['post_link']);
		if ($author['id']=='') continue;
		$qblog=$db->query('SELECT blog_id FROM robot_blogs2 WHERE blog_login=\''.addslashes($author['id']).'\' AND blog_link=\'facebook.com\' LIMIT 1');
		if ($db->num_rows($qblog)!=0)
		{
			$blog=$db->fetch($qblog);
			echo 'UPDATE blog_post SET blog_id='.$blog['blog_id'].' WHERE post_id='.$post['post_id']."\n";
			$db->query('UPDATE blog_post SET blog_id='.$blog['blog_id'].' WHERE post_id='.$post['post_id']);
		}
		else
		{
			$qinsert=$db->query('INSERT INTO robot_blogs2 (blog_link,blog_login) VALUES (\'facebook.com\',\''.addslashes($author['id']).'\')');
			$insert_id=$db->insert_id($qinsert);
			echo 'UPDATE blog_post SET blog_id='.$insert_id.' WHERE post_id='.$post['post_id']."\n";
			$db->query('UPDATE blog_post SET blog_id='.$insert_id.' WHERE post_id='.$post['post_id']);
		}
	}
}

?>