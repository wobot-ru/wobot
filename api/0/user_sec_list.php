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

$qsec_user=$db->query('SELECT * FROM user_tariff as a LEFT JOIN users as b ON a.user_id=b.user_id WHERE a.user_mid='.$user['user_id']);
// echo 'SELECT * FROM user_tariff as a LEFT JOIN users as b ON a.user_id=b.user_id WHERE a.user_mid='.$user['user_id'];
$iter=0;
while ($sec_user=$db->fetch($qsec_user))
{
	$out['users'][$iter]['user_email']=$sec_user['user_email'];
	$out['users'][$iter]['user_mid_priv']=$sec_user['user_mid_priv'];
	$out['users'][$iter]['user_id']=$sec_user['user_id'];
	$out['users'][$iter]['ut_id']=$sec_user['ut_id'];
	$iter++;
}

echo json_encode($out);

?>