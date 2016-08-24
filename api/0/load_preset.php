<?
header("Cache-control: public");

require_once('/var/www/new/com/config.php');
require_once('/var/www/new/com/func.php');
require_once('/var/www/new/com/db.php');
require_once('/var/www/new/bot/kernel.php');
require_once('auth.php');
$db = new database();
$db->connect();

auth();
if (!$loged) die();
$i=0;
$out=array();
if ($user['tariff_id']!=3)
{
	$p=$db->query('SELECT id,name FROM blog_preset WHERE order_id='.$_POST['order_id']);
	while ($pp=$db->fetch($p))
	{
		$out[$i]['id']=$pp['id'];
		$out[$i]['name']=$pp['name'];
	}

	echo json_encode($out);
}
?>