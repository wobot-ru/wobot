<?

require_once('/var/www/new/com/db.php');
require_once('/var/www/new/com/config.php');
require_once('/var/www/daemon/com/users.php');
require_once('lib.php');

$db=new database();
$db->connect();

$order_id=$_SERVER['argv'][1];

if (intval(exec('ps ax | grep "recover.php '.$order_id.'" | wc -l'))>3) die();

$qorder=$db->query('SELECT a.ut_id,a.order_id,a.user_id,c.ut_id as old_ut_id FROM blog_orders as a LEFT JOIN users as b ON a.user_id=b.user_id LEFT JOIN user_tariff as c ON b.user_id=c.user_id WHERE a.order_id='.intval($order_id).' ORDER BY ut_date DESC LIMIT 1');
$order=$db->fetch($qorder);
print_r($order);
if (intval($order['ut_id'])!=0) die();
$db->query('UPDATE blog_orders SET ut_id='.$order['old_ut_id'].' WHERE order_id='.intval($order_id));

$dir='/var/www/tools/archiver/data/'.$order_id;

$filename=$dir.'/blog_post.sql';
$handle = fopen($filename, "rb");
$bp = fread($handle, filesize($filename));
fclose($handle);
echo $bp;
$db->query($bp);

$filename=$dir.'/blog_full_com.sql';
$handle = fopen($filename, "rb");
$bfc = fread($handle, filesize($filename));
fclose($handle);

$db->query($bfc);

$user=new users();

$qpost=$db->query('SELECT post_link,post_id,order_id,blog_id FROM blog_post WHERE order_id='.$order_id.' AND blog_id!=0');
while ($post=$db->fetch($qpost))
{
	$blog_id=$user->get_url($post['post_link']);
	if ($blog_id!=$post['blog_id']) $db->query('UPDATE blog_post SET blog_id='.$blog_id.' WHERE post_id='.$post['post_id'].' AND order_id='.$post['order_id']);
}

?>