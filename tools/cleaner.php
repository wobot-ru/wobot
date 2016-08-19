<?

require_once('/var/www/com/db.php');
require_once('/var/www/com/config.php');

$db=new database();
$db->connect();

error_reporting(0);

$order_id=$_SERVER['argv'][1];
$qpost=$db->query('SELECT * FROM blog_post WHERE post_host=\'vk.com\' AND order_id='.$order_id);
while ($post=$db->fetch($qpost))
{
	echo '.';
	// usleep(200000);
	unset($delids);
	$qdublpost=$db->query('SELECT * FROM blog_post WHERE post_link=\''.addslashes($post['post_link']).'\'');
	while ($dublpost=$db->fetch($qdublpost))
	{
		if ($dublpost['post_id']!=$post['post_id']) $delids[]=$dublpost['post_id'];
	}
	if (count($delids)!=0) $db->query('DELETE FROM blog_post WHERE post_id IN ('.implode(',', $delids).') AND order_id='.$order_id);
}

?>