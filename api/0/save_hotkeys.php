<?
require_once('/var/www/new/com/config.php');
require_once('/var/www/new/com/func.php');
require_once('/var/www/new/com/db.php');
require_once('/var/www/new/bot/kernel.php');
require_once('auth.php');

date_default_timezone_set ( 'Europe/Moscow' );

$db = new database();
$db->connect();

//$_POST=$_GET;

auth();
if (!$loged) die();

$redis=new Redis() or die("Can'f load redis module.");
$redis->connect('127.0.0.1');
set_log('hotkeys',$_POST);

$settings=json_decode($user['user_settings'],true);
$hotkeys=json_decode($settings['hotkeys'],true);

foreach ($_POST as $key => $item)
{
	$hotkeys[$key]=$item;
}
$settings['hotkeys']=json_encode($hotkeys);
$db->query('UPDATE users SET user_settings=\''.addslashes(json_encode($settings)).'\' WHERE user_id='.$user['user_id']);
echo json_encode(array('status'=>'ok'));

?>