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
//$_GET=$_POST;
/*{
	$mas['status']='fail';
	echo json_encode($mas);	
	die();
}
if ((intval($_GET['order_id'])==0) || (intval($_GET['id'])==0))
{
	$mas['status']='fail';
	echo json_encode($mas);	
	die();
}*/
if (($user['ref']==1)&&($user['user_id']!=0))
{
	//echo 'UPDATE billing SET status='.intval($_GET['value']).' WHERE user_id='.intval($user['user_id']);
	$rs=$db->query('UPDATE billing SET status='.intval($_GET['value']).', money=7500 WHERE user_id='.intval($user['user_id']));
	$mas['status']='ok';
	echo json_encode($mas);
}
?>