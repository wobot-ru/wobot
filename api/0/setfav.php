<?

require_once('/var/www/new/com/config.php');
require_once('/var/www/new/com/func.php');
require_once('/var/www/new/com/db.php');
require_once('/var/www/new/bot/kernel.php');
require_once('auth.php');
// $_POST['id']='28164298,28164293';
// $_POST['order_id']=4700;
// $_POST['value']=0;
// $_GET['test_token']='06e82decff5d0eb94004c8d9c7bf1671';
// $_GET['test_user_id']=1187;
$db = new database();
$db->connect();

auth();
if (!$loged) die();

$redis=new Redis() or die("Can'f load redis module.");
$redis->connect('127.0.0.1');
set_log('fav',$_POST);

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
	if ($_GET['parent_id']!='')
	{
		$mid=explode(',', trim($_GET['parent_id']));
		foreach ($mid as $id)
		{
			if (intval(trim($id))==0) continue;
			$rs=$db->query('UPDATE blog_post SET post_fav='.intval($_GET['value']).' WHERE parent='.intval($id).' AND order_id='.intval($_GET['order_id']));
		}
		$mas['status']='ok';
		echo json_encode($mas);
	}
	else
	{
		// echo $_G
		$mid=explode(',', trim($_GET['id']));
		// print_r($mid);
		foreach ($mid as $id)
		{
			// echo 'UPDATE blog_post SET post_fav='.intval($_GET['value']).' WHERE post_id='.intval($id).' AND order_id='.intval($_GET['order_id'])."\n";
			$rs=$db->query('UPDATE blog_post SET post_fav='.intval($_GET['value']).' WHERE post_id='.intval($id).' AND order_id='.intval($_GET['order_id']));
		}
		$mas['status']='ok';
		echo json_encode($mas);
	}
}
?>