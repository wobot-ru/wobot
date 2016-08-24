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

//-------Права на проставления спама после шаринга------
$memcache = memcache_connect('localhost', 11211);
$priv=$memcache->get('blog_sharing');
$mpriv=json_decode($priv,true);
if ($priv=='')
{
	$qshare=$db->query('SELECT * FROM blog_sharing');
	while ($share=$db->fetch($qshare))
	{
		$mpriv[$share['order_id']][$share['user_id']]=$share['sharing_priv'];
	}
}

if (($mpriv[$_POST['order_id']][$user['user_id']]!=3) && (isset($mpriv[$_POST['order_id']][$user['user_id']])))
{
	$outmas['status']='fail';
	echo json_encode($outmas);
	die();
}

if ((intval($_POST['order_id'])!=0) && (intval(json_decode($_POST['widgets'],true))!=0))
{
	if ((intval($_POST['suborder_id'])!=0) && (intval($_POST['order_id'])!=0))
	{
		$qsuborder=$db->query('SELECT a.subtheme_id,b.order_id,a.subtheme_settings FROM blog_subthemes as a LEFT JOIN blog_orders as b ON a.order_id=b.order_id WHERE b.subtheme_id='.intval($_POST['suborder_id']).' AND b.order_id='.intval($_POST['order_id']).' AND a.user_id='.$user['user_id'].' LIMIT 1');
		$suborder=$db->fetch($qsuborder);
		if (intval($suborder['subtheme_id'])>0)
		{
			$settings=json_decode($suborder['subtheme_settings'],true);
			$settings['widgets']=json_decode($_POST['widgets'],true);
			$db->query('UPDATE blog_subthemes SET subtheme_settings=\''.addslashes(json_encode($settings)).'\' WHERE subtheme_id='.intval($_POST['subtheme_id']).' AND order_id='.intval($_POST['suborder_id']));
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
		$qorder=$db->query('SELECT order_id,order_settings FROM blog_orders WHERE order_id='.intval($_POST['order_id']).' AND user_id='.$user['user_id'].' LIMIT 1');
		$order=$db->fetch($qorder);
		if (intval($order['order_id'])>0)
		{
			$settings=json_decode($order['order_settings'],true);
			$settings['widgets']=json_decode($_POST['widgets'],true);
			$db->query('UPDATE blog_orders SET order_settings=\''.addslashes(json_encode($settings)).'\' WHERE order_id='.intval($_POST['order_id']).' AND user_id='.$user['user_id']);
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