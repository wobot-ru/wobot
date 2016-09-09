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

$redis=new Redis() or die("Can'f load redis module.");
$redis->connect('127.0.0.1');
set_log('addtag',$_POST);

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
	$query='INSERT INTO blog_tag (user_id,order_id,tag_name,tag_tag) VALUES ('.intval($user['user_id']).','.intval($_POST['order_id']).',\''.addslashes($_POST['name_tag']).'\','.$mtt3[0].')';
	$respost=$db->query($query);
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