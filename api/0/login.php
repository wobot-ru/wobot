<?
require_once('/var/www/new/com/config.php');
require_once('/var/www/new/com/func.php');
require_once('/var/www/new/com/db.php');
require_once('/var/www/new/bot/kernel.php');
//require_once('auth.php');

$db = new database();
$db->connect();

if (($_GET['login']=='') && ($_GET['pass']=='')) $_GET=$_POST;

if (isset($_GET['login']) && isset($_GET['pass']))
{
	$res=$db->query('SELECT * FROM users as a LEFT JOIN user_tariff as b ON a.user_id=b.user_id WHERE a.user_email=\''.$_GET['login'].'\' AND a.user_pass=\''.md5($_GET['pass']).'\' LIMIT 1');
	$row = $db->fetch($res);
	//echo 'SELECT * FROM users as a LEFT JOIN user_tariff as b ON a.user_id=b.user_id WHERE a.user_id='.intval($_COOKIE['user_id']).' LIMIT 1';
	//echo intval($_COOKIE['user_id']);
	//print_r($row);
	//echo $_COOKIE['token'].' ';
	if (intval($row['user_id'])!=0)
	{
		$out['status']='ok';
		$out['user_id']=$row['user_id'];
		$out['token']=md5(mb_strtolower($row['user_email'],'UTF-8').':'.$row['user_pass']);
	}
	else
	{
		$out['status']='fail';
	}
	echo json_encode($out);
}
else
{
	$out['status']='fail';
	echo json_encode($out);
}
?>
