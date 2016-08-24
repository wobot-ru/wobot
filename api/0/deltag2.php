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
set_log('deltag',$_POST);

if ($user['tariff_id']==3)
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

/*{
	$mas['status']='fail';
	echo json_encode($mas);	
	die();
}
if ((intval($_GET['order_id'])==0) || (intval($_GET['tag_id'])==0))
{
	$mas['status']='fail';
	echo json_encode($mas);	
	die();
}*/
//$_GET=$_POST;
//echo 'SELECT * FROM blog_tag WHERE user_id='.intval($user['user_id']).' AND tag_tag='.intval($_POST['tag_id']).' LIMIT 1';
$res=$db->query('SELECT * FROM blog_tag WHERE order_id='.intval($_POST['order_id']).' AND tag_tag='.intval($_POST['tag_id']).' LIMIT 1');
while ($row1 = $db->fetch($res)) 
{
	$row['id']=$row1['tag_id'];
}
//print_r($row);
if ($row['id']!='')
{
	//echo 'UPDATE blog_post SET post_tag=REPLACE(post_tag, \''.$_POST['tag_id'].'\', \'\') WHERE order_id='.intval($_POST['order_id']);
	//echo 'DELETE FROM blog_tag WHERE user_id='.intval($user['user_id']).' AND tag_tag='.intval($_POST['tag_id']);
	//die();
	// $db->query('UPDATE blog_post SET post_tag=REPLACE(post_tag, \''.$_POST['tag_id'].'\', \'\') WHERE order_id='.intval($_POST['order_id']));
	// echo 'UPDATE blog_post SET post_tag=post_tag ^ '.pow(2,intval($_POST['tag_id'])-1).' WHERE order_id='.intval($_POST['order_id']).' AND FIND_IN_SET(\''.$_POST['tag_id'].'\', post_tag)';
	$db->query('UPDATE blog_post SET post_tag=post_tag ^ '.pow(2,intval($_POST['tag_id'])-1).' WHERE order_id='.intval($_POST['order_id']).' AND FIND_IN_SET(\''.$_POST['tag_id'].'\', post_tag)');
	$query='DELETE FROM blog_tag WHERE order_id='.intval($_POST['order_id']).' AND tag_tag='.intval($_POST['tag_id']);
	$db->query($query);
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