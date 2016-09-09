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

$query='SELECT * FROM blog_tag WHERE user_id='.intval($_POST['user_id']);
//echo $query;
$mtt=array('1','2','3','4','5','6','7','8','9','10','11','12','13','14','15','16','17','18','19','20');
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
	$query='INSERT INTO blog_tag (user_id,tag_name,tag_tag) VALUES ('.intval($_POST['user_id']).',\''.addslashes($_POST['name_tag']).'\','.$mtt3[0].')';
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