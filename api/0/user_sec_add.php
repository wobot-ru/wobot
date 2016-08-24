<?

require_once('/var/www/new/com/config.php');
require_once('/var/www/new/com/func.php');
require_once('/var/www/new/com/db.php');
require_once('/var/www/new/bot/kernel.php');
require_once('/var/www/daemon/com/users.php');
require_once('auth.php');

$db = new database();
$db->connect();

auth();
if (!$loged) die();

$redis=new Redis() or die("Can'f load redis module.");
$redis->connect('127.0.0.1');

if (($user['user_mid']!=0) && ($user['user_mid_priv']!=1)) 
{
	$mas['status']='fail';
	echo json_encode($mas);
	die();	
}

$regex='/\@(?<part>.*)/isu';
preg_match_all($regex, $_POST['user_email'], $out_post);
preg_match_all($regex, $user['user_email'], $out_user);
if ($out_post['part'][0]!=$out_user['part'][0]) die(json_encode(array('status'=>1)));
if ($user['user_email']==$_POST['user_email']) die(json_encode(array('status'=>2)));

$qisset_user=$db->query('SELECT * FROM users WHERE user_email=\''.addslashes($_POST['user_email']).'\' LIMIT 1');
if ($db->num_rows($qisset_user)==0)
{
	$qinsert=$db->query('INSERT INTO users (user_email,user_pass) VALUES (\''.addslashes($_POST['user_email']).'\',\''.addslashes(md5($_POST['user_pass'])).'\')');
	$user_last_insert_id=$db->insert_id($qinsert);
	$db->query('INSERT INTO user_tariff (user_id,user_mid,user_mid_priv,tariff_id,ut_date) VALUES ('.$user_last_insert_id.','.$user['user_id'].','.$_POST['user_priv'].','.$user['tariff_id'].','.$user['ut_date'].')');
	$qus_out=$db->query('SELECT * FROM users as a LEFT JOIN user_tariff as b ON a.user_id=b.user_id WHERE a.user_id='.$user_last_insert_id.' LIMIT 1');
	$us_out=$db->fetch($qus_out);
	$out['user_email']=$us_out['user_email'];
	$out['user_mid_priv']=$us_out['user_mid_priv'];
	$out['user_id']=$us_out['user_id'];
	$out['ut_id']=$us_out['ut_id'];
	die(json_encode($out));
}
else
{
	$isset_user=$db->fetch($qisset_user);
	$db->query('UPDATE users SET user_email=\''.addslashes($_POST['user_email']).'\''.($_POST['user_pass']!=''?',user_pass=\''.md5($_POST['user_pass']).'\'':'').' WHERE user_id='.$isset_user['user_id']);
	$qisset_ut_id=$db->query('SELECT * FROM user_tariff WHERE user_id='.$isset_user['user_id'].' LIMIT 1');
	if ($db->num_rows($qisset_ut_id)!=0) $db->query('UPDATE user_tariff SET user_mid_priv='.$_POST['user_priv'].' WHERE user_id='.$isset_user['user_id']);
	else $db->query('INSERT INTO user_tariff (user_id,user_mid,user_mid_priv,tariff_id,ut_date) VALUES ('.$isset_user['user_id'].','.$user['user_id'].','.$_POST['user_priv'].','.$user['tariff_id'].','.$user['ut_date'].')');
	$qus_out=$db->query('SELECT a.user_email as user_email,a.user_id as user_id,b.user_mid_priv as user_mid_priv,b.ut_id as ut_id FROM users as a LEFT JOIN user_tariff as b ON a.user_id=b.user_id WHERE a.user_id='.$isset_user['user_id'].' LIMIT 1');
	$us_out=$db->fetch($qus_out);
	$out['user_email']=$us_out['user_email'];
	$out['user_mid_priv']=$us_out['user_mid_priv'];
	$out['user_id']=$us_out['user_id'];
	$out['ut_id']=$us_out['ut_id'];
	die(json_encode($out));
}

?>