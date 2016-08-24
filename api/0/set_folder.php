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
if ($mpriv[$_POST['order_id']][$user['user_id']]==1)
{
	$outmas['status']='fail';
	echo json_encode($outmas);
	die();
}

//$_POST=$_GET;

if (($_POST['order_id']!='') && ($_POST['folder_id']!=''))
{
	$qsth=$db->query('SELECT * FROM blog_folders WHERE user_id='.intval($user['user_id']).' AND folder_id=\''.intval($_POST['folder_id']).'\'');
	if ($db->num_rows($qsth)!=0)
	{
		//echo 'UPDATE blog_orders SET folder_id='.intval($_POST['folder_id']).' WHERE order_id='.intval($_POST['order_id']).' AND user_id='.intval($user['user_id']);
		$db->query('UPDATE blog_orders SET folder_id='.intval($_POST['folder_id']).' WHERE order_id='.intval($_POST['order_id']).' AND user_id='.intval($user['user_id']));
		//$outmas['folder_id']=$db->insert_id();
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
else
{
	$outmas['status']=1;
	echo json_encode($outmas);
	die();
}

?>