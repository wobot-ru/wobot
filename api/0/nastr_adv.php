<?

require_once('/var/www/new/com/config.php');
require_once('/var/www/new/com/func.php');
require_once('/var/www/new/com/db.php');
require_once('/var/www/new/bot/kernel.php');
require_once('/var/www/new/com/adv_func.php');
require_once('auth.php');

ignore_user_abort(true);

$db = new database();
$db->connect();

auth();
if (!$loged) die();

$redis=new Redis() or die("Can'f load redis module.");
$redis->connect('127.0.0.1');
set_log('nastr',$_POST);

//-------Права на проставления спама после шаринга------
if (($user['user_mid']!=0) && ($user['user_mid_priv']==3)) 
{
	$mas['status']='ok';
	echo json_encode($mas);
	die();
}
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

$_GET=$_POST;
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

if ($user['tariff_id']!=3)
{
	if ($_POST['adv_query']==0)
	{
		$rs=$db->query('UPDATE blog_post SET post_nastr='.intval($_GET['value']).' WHERE post_id='.intval($_GET['id']).' AND order_id='.intval($_GET['order_id']));
		$mas['status']='ok';
		echo json_encode($mas);
		die();
	}
	elseif ($_POST['adv_query']==1)
	{
		$query=query_maker($_POST,'p.post_nastr='.intval($_GET['value']),'update');
		$db->query($query);
		$mas['status']='ok';
		echo json_encode($mas);
		die();
	}
	else
	{
		$outmas['status']='fail';
		echo json_encode($outmas);
		die();
	}
}

?>