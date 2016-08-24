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
if ($user['tariff_id']==3)
{
	$mas['status']='fail';
	echo json_encode($mas);	
	die();
}
/*{
	$mas['status']='fail';
	echo json_encode($mas);	
	die();
}
if ((intval($_GET['order_id'])==0) || (intval($_GET['tag_id'])==0))
{
	$mas['status']='fail';
	echo json_encode($mas);	
	die();
}*/
//$_GET=$_POST;
//echo 'SELECT * FROM blog_tag WHERE user_id='.intval($user['user_id']).' AND tag_tag='.intval($_POST['tag_id']).' LIMIT 1';
$res=$db->query('SELECT * FROM blog_tag WHERE user_id='.intval($user['user_id']).' AND tag_tag='.intval($_POST['tag_id']).' LIMIT 1');
while ($row1 = $db->fetch($res)) 
{
	$row['id']=$row1['tag_id'];
}
//print_r($row);
if ($row['id']!='')
{
	//echo 'UPDATE blog_post SET post_tag=REPLACE(post_tag, \''.$_POST['tag_id'].'\', \'\') WHERE order_id='.intval($_POST['order_id']);
	//echo 'DELETE FROM blog_tag WHERE user_id='.intval($user['user_id']).' AND tag_tag='.intval($_POST['tag_id']);
	//die();
	$db->query('UPDATE blog_post SET post_tag=REPLACE(post_tag, \''.$_POST['tag_id'].'\', \'\') WHERE order_id='.intval($_POST['order_id']));
	$query='DELETE FROM blog_tag WHERE user_id='.intval($user['user_id']).' AND tag_tag='.intval($_POST['tag_id']);
	$db->query($query);
	$mas['status']='ok';
	echo json_encode($mas);
}
else
{
	$mas['status']='fail';
	echo json_encode($mas);
}

?>