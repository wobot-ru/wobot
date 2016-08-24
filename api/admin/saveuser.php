<?

require_once('/var/www/com/config.php');
require_once('/var/www/com/db.php');
require_once('/var/www/com/auth.php');

$db = new database();
$db->connect();

// echo 'UPDATE users SET user_email=\''.addslashes($_POST['user_email']).'\''.($_POST['user_pass']!=''?',user_pass=\''.addslashes(md5($_POST['user_pass'])).'\'':'').',user_name=\''.addslashes($_POST['user_name']).'\',user_contact=\''.addslashes($_POST['user_contact']).'\',user_company=\''.addslashes($_POST['user_company']).'\',user_active=\''.intval($_POST['us_active']).'\' WHERE user_id='.$_POST['user_id'];
$quser=$db->query('SELECT * FROM users WHERE user_id='.$_POST['user_id'].' LIMIT 1');
$user=$db->fetch($quser);
$user_settings=json_decode($user['user_settings'],true);
$user_settings['comment']=$_POST['user_comment'];
$user_settings['user_reaction']=intval($_POST['user_reaction']);
$db->query('UPDATE users SET user_email=\''.addslashes($_POST['user_email']).'\''.($_POST['user_pass']!=''?',user_pass=\''.addslashes(md5($_POST['user_pass'])).'\'':'').',user_name=\''.addslashes($_POST['user_name']).'\',user_contact=\''.addslashes($_POST['user_contact']).'\',user_company=\''.addslashes($_POST['user_company']).'\',user_active=\''.intval($_POST['us_active']).'\',user_settings=\''.addslashes(json_encode($user_settings)).'\' WHERE user_id='.$_POST['user_id']);
$db->query('UPDATE user_tariff SET ut_date='.strtotime($_POST['ut_date']).',tariff_id='.$_POST['tariff_id'].' WHERE user_id='.$_POST['user_id']);
$db->query('UPDATE user_tariff SET ut_date='.strtotime($_POST['ut_date']).',tariff_id='.$_POST['tariff_id'].' WHERE user_mid='.$_POST['user_id']);

?>