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

if (intval($_POST['order_id'])!=0)
{
	if ((intval($_POST['suborder_id'])!=0) && (intval($_POST['order_id'])!=0))
	{
		$qsuborder=$db->query('SELECT a.subtheme_id,b.order_id,a.subtheme_settings FROM blog_subthemes as a LEFT JOIN blog_orders as b ON a.order_id=b.order_id LEFT JOIN blog_sharing as c ON b.order_id=c.order_id WHERE b.subtheme_id='.intval($_POST['suborder_id']).' AND b.order_id='.intval($_POST['order_id']).' AND (a.user_id='.$user['user_id'].' OR c.user_id='.$user['user_id'].') LIMIT 1');
		$suborder=$db->fetch($qsuborder);
		if (intval($suborder['subtheme_id'])>0)
		{
			$settings=json_decode($suborder['subtheme_settings'],true);
			$outmas['widgets']=$settings['widgets'];
			$outmas['status']='ok';
			echo json_encode($outmas);
			die();
		}
		else
		{
			$outmas['status']=2;
			echo json_encode($outmas);
			die();
		}
	}
	elseif (intval($_POST['order_id'])!=0)
	{
		$qorder=$db->query('SELECT a.order_id,a.order_settings FROM blog_orders as a LEFT JOIN blog_sharing b ON a.order_id=b.order_id WHERE a.order_id='.intval($_POST['order_id']).' AND (a.user_id='.$user['user_id'].' OR b.user_id='.$user['user_id'].') LIMIT 1');
		$order=$db->fetch($qorder);
		if (intval($order['order_id'])>0)
		{
			$settings=json_decode($order['order_settings'],true);
			$outmas['widgets']=$settings['widgets'];
			$outmas['status']='ok';
			echo json_encode($outmas);
			die();
		}
		else
		{
			$outmas['status']=2;
			echo json_encode($outmas);
			die();
		}
	}
}
else
{
	$outmas['status']=1;
	echo json_encode($outmas);
	die();
}

?>