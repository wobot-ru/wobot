<?
require_once('/var/www/new/com/config.php');
require_once('/var/www/new/com/func.php');
require_once('/var/www/new/com/db.php');
require_once('/var/www/new/bot/kernel.php');
require_once('auth.php');

date_default_timezone_set ( 'Europe/Moscow' );

$db = new database();
$db->connect();

//$_POST=$_GET;

//echo $_COOKIE['user_id'];
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
if ($mpriv[$_POST['order_id']][$user['user_id']]==1)
{
	$outmas['status']='fail';
	echo json_encode($outmas);
	die();
}

$redis=new Redis() or die("Can'f load redis module.");
$redis->connect('127.0.0.1');
set_log('groups_del',$_POST);

$qus=$db->query('SELECT user_id,ut_id FROM blog_orders WHERE order_id='.intval($_POST['order_id']));
$us=$db->fetch($qus);
if ($us['user_id']!=$user['user_id'])
{
	$out['status']=2;
	echo json_encode($out);
	die();	
}

//print_r($_POST);
if (intval($_POST['order_id'])==0)
{
	$out['status']=1;
	echo json_encode($out);	
	die();
}

$qgroup=$db->query('DELETE FROM blog_tp WHERE order_id='.$_POST['order_id'].' AND tp_id='.intval($_POST['id']));

$out['status']='ok';
echo json_encode($out);	
?>