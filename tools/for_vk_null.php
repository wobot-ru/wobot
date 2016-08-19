<?
require_once('/var/www/daemon/com/config.php');
//require_once('/var/www/daemon/com/func.php');
require_once('/var/www/daemon/com/db.php');
require_once('/var/www/daemon/bot/kernel.php');
require_once('/var/www/daemon/com/users.php');

$db = new database();
$db->connect();

$qpost=$db->query('SELECT * FROM blog_post WHERE post_host=\'vk.com\' AND blog_id=0 ORDER BY post_id DESC');
while ($post=$db->fetch($qpost))
{
	//print_r($post);
	$user=new users();
	//echo $user->get_url('http://vk.com/photo-32788766_290200943?list=b3dd48157d650a1c66&og=1');
	echo 'UPDATE blog_post SET blog_id='.$user->get_url($post['post_link']).' WHERE post_id='.$post['post_id'].' AND order_id='.$post['order_id']."\n";
	$db->query('UPDATE blog_post SET blog_id='.$user->get_url($post['post_link']).' WHERE post_id='.$post['post_id'].' AND order_id='.$post['order_id']);
}

//print_r($muser);

?>