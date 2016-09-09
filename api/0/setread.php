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
set_log('spam',$_POST);

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

//$_POST=$_GET;
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
	if ($_POST['id']!='')
	{
		$ids=explode(',',$_POST['id']);
		foreach ($ids as $id)
		{
			//echo 'UPDATE blog_post SET post_read=1 WHERE post_id='.intval($id).' AND order_id='.intval($_POST['order_id']);
			$rs=$db->query('UPDATE blog_post SET post_read='.intval($_POST['value']).' WHERE post_id='.intval($id).' AND order_id='.intval($_POST['order_id']));
		}
		$mas['status']='ok';
		echo json_encode($mas);
		die();
	}
	else
	{
		$ids=explode(',',$_POST['parent_id']);
		foreach ($ids as $id)
		{
			if (intval(trim($id))==0) continue;
			//echo 'UPDATE blog_post SET post_read=1 WHERE post_id='.intval($id).' AND order_id='.intval($_POST['order_id']);
			$rs=$db->query('UPDATE blog_post SET post_read='.intval($_POST['value']).' WHERE parent='.intval($id).' AND order_id='.intval($_POST['order_id']));
		}
		$mas['status']='ok';
		echo json_encode($mas);
		die();
	}
}
else
{
	$outmas['status']='fail';
	echo json_encode($outmas);
	die();
}

?>