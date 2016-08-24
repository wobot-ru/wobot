<?

require_once('/var/www/new/com/config.php');
require_once('/var/www/new/com/func.php');
require_once('/var/www/new/com/db.php');
require_once('/var/www/new/bot/kernel.php');
require_once('auth.php');

$db = new database();
$db->connect();

auth();
if (!$loged) die();

$qneworder=$db->query('SELECT order_id FROM blog_orders WHERE order_id='.$_POST['new_order_id'].' AND user_id='.$user['user_id'].' LIMIT 1');
if ($db->num_rows($qneworder)==0) die(json_encode(array('status'=>1)));
$qoldorder=$db->query('SELECT order_id FROM blog_orders WHERE order_id='.$_POST['old_order_id'].' AND user_id='.$user['user_id'].' LIMIT 1');
if ($db->num_rows($qoldorder)==0) die(json_encode(array('status'=>1)));

$qtag=$db->query('SELECT * FROM blog_tag WHERE order_id='.$_POST['new_order_id'].' AND user_id='.$user['user_id']);
if ($db->num_rows($qtag)==0)
{
	$qoldtag=$db->query('SELECT * FROM blog_tag WHERE order_id='.$_POST['old_order_id'].' AND user_id='.$user['user_id']);
	while ($oldtag=$db->fetch($qoldtag))
	{
		$db->query('INSERT INTO blog_tag (user_id,order_id,tag_name,tag_tag,tag_auto,tag_kw,tag_sw,tag_akw) VALUES ('.intval($user['user_id']).','.intval($_POST['new_order_id']).',\''.addslashes($oldtag['tag_name']).'\','.$oldtag['tag_tag'].','.($oldtag['tag_auto']!=0?1:0).',\''.addslashes($oldtag['tag_kw']).'\',\''.addslashes($oldtag['tag_sw']).'\',\''.addslashes($oldtag['tag_akw']).'\')');
	}
	die(json_encode(array('status'=>'ok')));
}
else
{
	die(json_encode(array('status'=>2)));
}

?>