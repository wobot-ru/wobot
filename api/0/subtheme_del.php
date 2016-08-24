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

if (($_POST['order_id']!='') && ($_POST['subtheme_id']!=''))
{
	$qisset=$db->query('SELECT order_id FROM blog_orders WHERE user_id='.$user['user_id'].' AND order_id='.intval($_POST['order_id']));
	if ($db->num_rows($qisset)==0)
	{
		$outmas['status']=3;
		echo json_encode($outmas);
		die();
	}
	$qsth=$db->query('SELECT * FROM blog_subthemes WHERE order_id='.intval($_POST['order_id']).' AND subtheme_id='.intval($_POST['subtheme_id']));
	if ($db->num_rows($qsth)!=0)
	{
		//echo 'DELETE FROM blog_subthemes WHERE order_id='.intval($_POST['order_id']).' AND subtheme_id='.intval($_POST['subtheme_id']);
		$db->query('DELETE FROM blog_subthemes WHERE order_id='.intval($_POST['order_id']).' AND subtheme_id='.intval($_POST['subtheme_id']));
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