<?

error_reporting(0);

require_once('/var/www/new/com/config.php');
require_once('/var/www/new/com/func.php');
require_once('/var/www/new/com/db.php');
require_once('/var/www/new/bot/kernel.php');

$db = new database();
$db->connect();

$payload=@file_get_contents('php://input');
$security='';
if ($_REQUEST['Sign']!=sha1($payload.'ApsWobotAps'))
{
	$security=" UNSECURE!!!";
}

/*if ($fp=@fopen('/var/www/api/0/aps.log', 'a'))
{
	@fwrite($fp, date('r').$security.' '.json_encode(array('REQUEST'=>$_REQUEST,'PAYLOAD'=>$payload))."\n");
	@fclose($fp);
}*/

$db->query('INSERT INTO aps_log (aps_value) VALUES (\''.addslashes(date('r').$security.' '.json_encode(array('REQUEST'=>$_REQUEST,'PAYLOAD'=>$payload))).'\')');

$pyl=json_decode($payload,true);

if ($_GET['command']=='loginvalidator')
{
	$user=$db->query('SELECT user_email FROM users WHERE LOWER(user_email)="'.addslashes(mb_strtolower($pyl['user_email'], 'UTF-8')).'"');
	$count=@mysql_num_rows($user);
	if (intval($count)==0) echo 'Can be registered.';
	else echo 'User already exist.';
	//'{"user_email":"'.fetch_env_var("SETTINGS_user_email").'"}';
}

if ($_GET['command']=='registration')
{
	$user=$db->query('SELECT user_email FROM users WHERE LOWER(user_email)="'.addslashes(mb_strtolower($pyl['user_email'], 'UTF-8')).'"');
	$count=@mysql_num_rows($user);
	if (intval($count)==0) 
	{
		$db->query('INSERT INTO users (user_email,user_pass,user_active,user_promo,user_ctime) VALUES (\''.addslashes($pyl['user_email']).'\',\''.addslashes(md5($pyl['user_pass'])).'\',2,"APS",'.time().')');
		$db->query('INSERT INTO user_tariff (user_id,ut_date, tariff_id) VALUES ('.$db->insert_id().','.mktime(0,0,0,date('n'),date('j')-1,date('Y')).', 9)');
		//'{"user_email":"'.fetch_env_var("SETTINGS_user_email").'","user_pass":"'.fetch_env_var("SETTINGS_user_pass").'"}';
		echo 'Successfully created.';
	}
	else echo 'User already exist.';
}

if ($_GET['command']=='enable')
{
	$quser=$db->query('SELECT user_id FROM users WHERE LOWER(user_email)=\''.addslashes(mb_strtolower($pyl['user_email'],'UTF-8')).'\' and user_promo="APS" LIMIT 1');
	$user=$db->fetch($quser);
	if (intval($user['user_id'])>0)	$db->query('UPDATE user_tariff SET ut_date='.mktime(0,0,0,date('n')+1,date('j'),date('Y')).' WHERE user_id='.intval($user['user_id']).' and tariff_id=9');
	//'{"user_email":"'.fetch_env_var("SETTINGS_user_email").'"}';
	echo 'Successfully made paid.';
}

if ($_GET['command']=='disable')
{
	$quser=$db->query('SELECT user_id FROM users WHERE LOWER(user_email)=\''.addslashes(mb_strtolower($pyl['user_email'],'UTF-8')).'\' and user_promo="APS" LIMIT 1');
	$user=$db->fetch($quser);
	if (intval($user['user_id'])>0)	$db->query('UPDATE user_tariff SET ut_date='.mktime(0,0,0,date('n'),date('j')-1,date('Y')).' WHERE user_id='.$user['user_id'].' and tariff_id=9');
	//'{"user_email":"'.fetch_env_var("SETTINGS_user_email").'"}';
	echo 'Successfully made not paid.';
}

if ($_GET['command']=='delete')
{
	$quser=$db->query('SELECT user_id FROM users WHERE LOWER(user_email)=\''.addslashes(mb_strtolower($pyl['user_email'],'UTF-8')).'\' and user_promo="APS" LIMIT 1');
	$user=$db->fetch($quser);
	if (intval($user['user_id'])>0)
	{
		$db->query('UPDATE user_tariff SET ut_date='.mktime(0,0,0,date('n'),date('j')-1,date('Y')).' WHERE user_id='.$user['user_id'].' and tariff_id=9');
		//'{"user_email":"'.fetch_env_var("SETTINGS_user_email").'"}';
		$db->query('UPDATE users SET user_email="'.addslashes($pyl['user_email']).'_DELETED_'.intval($user['user_id']).'" WHERE LOWER(user_email)=\''.addslashes(mb_strtolower($pyl['user_email'],'UTF-8')).'\'');
		echo 'Successfully deleted.';
	}
	else
	{
		echo 'Invalid user.';
	}
}

if ($_GET['command']=='remove')
{
	//"s":"aps","command":"remove","user_email":"test@wobot.ru"
	$quser=$db->query('SELECT user_id FROM users WHERE LOWER(user_email)=\''.addslashes(mb_strtolower($_GET['user_email'],'UTF-8')).'\' and user_promo="APS" LIMIT 1');
	$user=$db->fetch($quser);
	if (intval($user['user_id'])>0)
	{
		$db->query('UPDATE user_tariff SET ut_date='.mktime(0,0,0,date('n'),date('j')-1,date('Y')).' WHERE user_id='.$user['user_id'].' and tariff_id=9');
		//'{"user_email":"'.fetch_env_var("SETTINGS_user_email").'"}';
		$db->query('UPDATE users SET user_email="'.urldecode($_GET['user_email']).'_DELETED_'.intval($user['user_id']).'" WHERE LOWER(user_email)=\''.addslashes(mb_strtolower($_GET['user_email'],'UTF-8')).'\'');
		echo 'Successfully deleted.';
	}
	else
	{
		echo 'Invalid user.';
	}
}

if ($_GET['command']=='edit')
{
	$quser=$db->query('SELECT user_id FROM users WHERE LOWER(user_email)=\''.addslashes(mb_strtolower($pyl['user_old_email'],'UTF-8')).'\' and user_promo="APS" LIMIT 1');
	$user=$db->fetch($quser);
	if (intval($user['user_id'])>0)
	{
		$db->query('UPDATE users SET user_email="'.addslashes($pyl['user_email']).'",user_pass="'.md5($pyl['user_pass']).'" WHERE user_id='.intval($user['user_id']).' and user_promo="APS"');
		//'{"user_old_email":"'.fetch_env_var("OLDSETTINGS_user_email").'","user_email":"'.fetch_env_var("SETTINGS_user_email").'","user_pass":"'.fetch_env_var("SETTINGS_user_pass").'"}';
		echo 'Successfully changed.';
	}
	else
	{
		echo 'User not exists.';
	}
}

?>