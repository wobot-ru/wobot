<?

require_once('/var/www/com/db.php');
require_once('/var/www/com/config.php');

$db=new database();
$db->connect();

$order_id=2867;
$new_order_id=3804;
$start=mktime(0,0,0,5,17,2013);
$end=mktime(0,0,0,6,17,2013);
$offset=0;

do
{
	$qpost=$db->query('SELECT * FROM blog_post as a LEFT JOIN blog_full_com as b ON a.post_id=b.ful_com_post_id WHERE a.order_id='.$order_id.' AND a.post_time>='.$start.' AND a.post_time<'.$end.' LIMIT '.$offset.',100');
	while ($post=$db->fetch($qpost))
	{
		echo 'INSERT INTO blog_post (order_id,post_time,post_link,post_host,post_content,blog_id) VALUES ('.$new_order_id.','.$post['post_time'].',\''.addslashes($post['post_link']).'\',\''.addslashes($post['post_host']).'\',\''.addslashes($post['post_content']).'\','.$post['blog_id'].')'."\n";
		$db->query('INSERT INTO blog_post (order_id,post_time,post_link,post_host,post_content,blog_id) VALUES ('.$new_order_id.','.$post['post_time'].',\''.addslashes($post['post_link']).'\',\''.addslashes($post['post_host']).'\',\''.addslashes($post['post_content']).'\','.$post['blog_id'].')');
		echo 'INSERT INTO blog_full_com (ful_com_post_id,ful_com_order_id,ful_com_post) VALUES ('.$db->insert_id().','.$new_order_id.',\''.addslashes($post['ful_com_post']).'\')'."\n";
		$db->query('INSERT INTO blog_full_com (ful_com_post_id,ful_com_order_id,ful_com_post) VALUES ('.$db->insert_id().','.$new_order_id.',\''.addslashes($post['ful_com_post']).'\')');
	}
	$offset+=100;
}
while ($db->num_rows($qpost)!=0);
?>