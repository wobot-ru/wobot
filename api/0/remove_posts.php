<?
require_once('/var/www/new/com/config.php');
require_once('/var/www/new/com/func.php');
require_once('/var/www/new/com/db.php');
require_once('/var/www/new/bot/kernel.php');
require_once('auth.php');

date_default_timezone_set ( 'Europe/Moscow' );

$db = new database();
$db->connect();

$redis=new Redis() or die("Can'f load redis module.");
$redis->connect('127.0.0.1');

auth();
if (!$loged) die();

$qorder=$db->query('SELECT * FROM blog_orders WHERE order_id='.intval($_POST['order_id']).' AND user_id='.$user['user_id'].' LIMIT 1');
$order=$db->fetch($qorder);
// print_r($order);
if ($db->num_rows($qorder)==0) 
{
	echo json_encode(array('status'=>'1'));
	die();
}

if (intval(strtotime($_POST['start']))>intval(strtotime($_POST['end']))) die(json_encode(array('status'=>'2')));
// if ((intval(strtotime($_POST['start']))<$order['order_start']) || (intval(strtotime($_POST['end']))>($order['order_end']+86400))) die(json_encode(array('status'=>'2')));

// echo 'DELETE FROM blog_post WHERE order_id='.intval($_POST['order_id']).' AND post_time>='.strtotime($_POST['start']).' AND post_time<'.(strtotime($_POST['end'])+86400).($_POST['remove_spam']==1?' AND post_spam=1':'');
$db->query('DELETE FROM blog_full_com USING blog_full_com,blog_post WHERE blog_full_com.ful_com_post_id=blog_post.post_id AND blog_post.order_id='.intval($_POST['order_id']).' AND blog_post.post_time>'.strtotime($_POST['start']).' AND blog_post.post_time<='.(strtotime($_POST['end'])+86400).($_POST['remove_spam']==1?' AND blog_post.post_spam=1':''));
$db->query('DELETE FROM blog_post WHERE order_id='.intval($_POST['order_id']).' AND post_time>='.strtotime($_POST['start']).' AND post_time<'.(strtotime($_POST['end'])+86400).($_POST['remove_spam']==1?' AND post_spam=1':''));
$redis->delete('orders_'.intval($_POST['order_id']));
file_get_contents('http://localhost/tools/cashjob.php?order_id='.intval($_POST['order_id']));

$qcount=$db->query('SELECT count(*) as cnt FROM blog_post WHERE order_id='.intval($_POST['order_id']));
$count=$db->fetch($qcount);
$quser_tariff=$db->query('SELECT * FROM blog_tariff WHERE tariff_id='.$user['tariff_id']);
$user_tariff=$db->fetch($quser_tariff);
if ($count['cnt']>$user_tariff['tariff_posts']) die(json_encode(array('status'=>'3')));
else $redis->delete('limitorder_'.intval($_POST['order_id']));

$outmas['status']='ok';
echo json_encode($outmas);

?>