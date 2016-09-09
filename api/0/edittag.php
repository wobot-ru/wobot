<?

require_once('/var/www/new/com/config.php');
require_once('/var/www/new/com/func.php');
require_once('/var/www/new/com/db.php');
require_once('/var/www/new/bot/kernel.php');
require_once('auth.php');

$db = new database();
$db->connect();
auth();
if (!$loged)
{
	$mas['status']='fail';
	echo json_encode($mas);	
	die();
}
$_GET=$_POST;

$redis=new Redis() or die("Can'f load redis module.");
$redis->connect('127.0.0.1');
set_log('edittag',$_POST);

if ($user['tariff_id']==3)
{
	$mas['status']='fail';
	echo json_encode($mas);	
	die();
}
if ((intval($_GET['order_id'])==0) || (intval($_GET['tag_id'])==0))
{
	$mas['status']='fail';
	echo json_encode($mas);
	die();
}

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
if ($mpriv[$_POST['order_id']][$user['user_id']]==1)
{
	$outmas['status']='fail';
	echo json_encode($outmas);
	die();
}

//die();
$res=$db->query('SELECT * FROM blog_tag WHERE order_id='.intval($_GET['order_id']).' AND tag_tag='.intval($_GET['tag_id']));
while ($row1 = $db->fetch($res)) 
{
	$row['id']=$row1['tag_id'];
}
if ($row['id']!='')
{
	$db->query('UPDATE blog_tag SET tag_name=\''.addslashes($_GET['tag_name']).'\' WHERE order_id='.intval($_GET['order_id']).' AND tag_tag='.intval($_GET['tag_id']));
	if (!isset($memcache)) $memcache = memcache_connect('localhost', 11211);
	$qorder=$db->query('SELECT order_id,order_start,order_end FROM blog_orders WHERE order_id='.intval($_POST['order_id']).' LIMIT 1');
	$order=$db->fetch($qorder);
	if ($order['order_end']>time()) $order['order_end']=mktime(0,0,0,date('n'),date('j'),date('Y'));
	$memcache->delete('filters_'.$order['order_id'].'_'.$order['order_start'].'_'.$order['order_end']);
	$mas['status']='ok';
	echo json_encode($mas);
}
else
{
	$mas['status']='fail';
	echo json_encode($mas);
}

?>