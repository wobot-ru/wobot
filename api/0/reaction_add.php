<?

require_once('/var/www/new/com/config.php');
require_once('/var/www/new/com/func.php');
require_once('/var/www/new/com/db.php');
require_once('/var/www/new/bot/kernel.php');
require_once('auth.php');

date_default_timezone_set ( 'Europe/Moscow' );

$db = new database();
$db->connect();

auth();
if (!$loged) die();

$reaction_blog_info['reaction_blog_nick']=$_POST['reaction_blog_nick'];
$reaction_blog_info['reaction_blog_ico']=$_POST['reaction_blog_ico'];
$qinsert=$db->query('INSERT INTO blog_reaction (post_id,order_id,reaction_content,reaction_time,reaction_blog_login,reaction_blog_info) VALUES ('.$_POST['post_id'].','.$_POST['order_id'].',\''.addslashes($_POST['reaction_content']).'\','.time().',\''.addslashes($_POST['reaction_blog_login']).'\',\''.addslashes(json_encode($reaction_blog_info)).'\')');
echo json_encode(array('status'=>'ok'));
?>