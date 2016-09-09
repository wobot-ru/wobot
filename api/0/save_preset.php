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
if ($user['tariff_id']!=3)
{
	$pres_inf=$db->query('INSERT INTO blog_preset (order_id,name,preset_fields) VALUES ('.intval($_POST['order_id']).',\''.preg_replace('/\-\-/is','-',$_POST['name']).'\',\''.addslashes(json_encode($_POST)).'\')');
	//echo 'INSERT INTO blog_preset (order_id,name,preset_fields) VALUES ('.intval($_POST['order_id']).','.preg_replace('/\-\-/is','-',$_POST['name']).','.addslashes(json_encode($_POST)).')';
		$mas['status']='ok';
		$mas['preset_id']=$db->insert_id();
		$mas['preset_name']=$_POST['name'];
		$mas['order_id']=$_POST['order_id'];
		echo json_encode($mas);
}
?>