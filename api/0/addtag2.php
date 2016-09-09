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

$redis=new Redis() or die("Can'f load redis module.");
$redis->connect('127.0.0.1');
set_log('addtag',$_POST);

if (($user['user_mid']!=0) && ($user['user_mid_priv']==3)) 
{
	$mas['status']='ok';
	echo json_encode($mas);
	die();	
}

if ($user['tariff_id']==3)
{
	$mas['status']='fail';
	echo json_encode($mas);
	die();
}
/*{
	$mas['status']='fail';
	echo json_encode($mas);	
	die();
}
if ((intval($_GET['order_id'])==0) || (intval($_GET['id'])==0))
{
	$mas['status']='fail';
	echo json_encode($mas);	
	die();
}*/

$query='SELECT * FROM blog_tag WHERE order_id='.intval($_POST['order_id']);
//echo $query;
$mtt=array('1','2','3','4','5','6','7','8','9','10','11','12','13','14','15','16','17','18','19','20','21','22','23','24','25','26','27','28','29','30','31','32','33','34','35','36','37','38','39','40','41','42','43','44','45','46','47','48','49','50');
$respost=$db->query($query);
while ($rpp=$db->fetch($respost))
{
	$mtt2[]=$rpp['tag_tag'];
}
//print_r($mtt2);
foreach ($mtt as $item)
{
	if (!in_array($item,$mtt2))
	{
		$mtt3[]=$item;
	}
}
if (count($mtt3)!=0)
{
	$query='INSERT INTO blog_tag (user_id,order_id,tag_name,tag_tag) VALUES ('.intval($_POST['user_id']).','.intval($_POST['order_id']).',\''.addslashes($_POST['name_tag']).'\','.$mtt3[0].')';
	$respost=$db->query($query);
	if (!isset($memcache)) $memcache = memcache_connect('localhost', 11211);
	$qorder=$db->query('SELECT order_id,order_start,order_end FROM blog_orders WHERE order_id='.intval($_POST['order_id']).' LIMIT 1');
	$order=$db->fetch($qorder);
	if ($order['order_end']>time()) $order['order_end']=mktime(0,0,0,date('n'),date('j'),date('Y'));
	$memcache->delete('filters_'.$order['order_id'].'_'.$order['order_start'].'_'.$order['order_end']);
	$mas['id']=$mtt3[0];
	$mas['status']='ok';
	echo json_encode($mas);
}
else
{
	$mas['status']='fail';
	echo json_encode($mas);
}

?>