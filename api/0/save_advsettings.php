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

$av['themeNotice']=1;
$av['messagesNotice']=1;
$av['mainNotice']=1;
$av['newresNotice']=1;
$av['compareNotice']=1;
$av['comparepageNotice']=1;
$av['perpage']=1;
$av['filter_spam']=0;

//echo $_COOKIE['user_id'];
auth();
//if (!$loged) die();
//print_r($_POST);
$ius=$db->query('SELECT * FROM users WHERE user_id='.intval($user['user_id']));
$user=$db->fetch($ius);
$settings=json_decode($user['user_settings'],true);
$settings[$_POST['name']]=$_POST['value'];
if (isset($av[$_POST['name']]))
{
	$res=$db->query('UPDATE users SET user_settings=\''.addslashes(json_encode($settings)).'\' WHERE user_id='.intval($user['user_id']));
	//echo 'UPDATE users SET user_settings=\''.addslashes(json_encode($settings)).'\' WHERE user_id='.intval($user['user_id']);
	$out['status']='ok';
	echo json_encode($out);
}
else
{
	$out['status']='fail';
	echo json_encode($out);	
}
?>