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

$qsec_user=$db->query('DELETE FROM user_tariff WHERE user_id='.$_POST['user_id'].' AND user_mid='.$user['user_id']);

echo json_encode(array('status'=>'ok'));

?>