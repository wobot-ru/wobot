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

//$_POST=$_GET;

if (intval($_POST['order_id'])!=0)
{
	$qsubth=$db->query('SELECT b.subtheme_id,b.subtheme_name FROM blog_orders as a LEFT JOIN blog_subthemes as b ON a.order_id=b.order_id WHERE a.order_id='.intval($_POST['order_id']).' AND a.user_id='.$user['user_id']);
	while ($subth=$db->fetch($qsubth))
	{
		$outmas[$subth['subtheme_id']]=$subth['subtheme_name'];
	}
	echo json_encode($outmas);
	die();
}
else
{
	$outmas['status']=1;
	echo json_encode($outmas);
}

?>