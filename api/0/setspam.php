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
//$_GET=$_POST;
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
	if ($_POST['value']=='false')
	{
		$vl=1;
	}
	else
	{
		$vl=0;
	}
	if ($_POST['type']=='post')
	{
		$rs=$db->query('UPDATE blog_post SET post_spam='.intval($vl).' WHERE post_id='.intval($_POST['id']).' AND order_id='.intval($_POST['order_id']));
		//echo 'UPDATE blog_post SET post_spam='.intval($vl).' WHERE post_id='.intval($_POST['id']).' AND order_id='.intval($_POST['order_id']);
		$mas['status']='ok';
		echo json_encode($mas);
	}
	elseif ($_POST['type']=='host')
	{
		$r1=$db->query('SELECT post_host FROM blog_post WHERE post_id='.intval($_POST['id']).' AND order_id='.intval($_POST['order_id']).' LIMIT 1');
		$inf=$db->fetch($r1);
		$rs=$db->query('UPDATE blog_post SET post_spam='.intval($vl).' WHERE post_host=\''.($inf['post_host']).'\' AND order_id='.intval($_POST['order_id']));
		//echo 'UPDATE blog_post SET post_spam='.intval($vl).' WHERE post_host=\''.($inf['post_host']).'\' AND order_id='.intval($_POST['order_id']);
		$mas['status']='ok';
		echo json_encode($mas);
	}
	elseif ($_POST['type']=='author')
	{
		$r1=$db->query('SELECT blog_id FROM blog_post WHERE post_id='.intval($_POST['id']).' AND order_id='.intval($_POST['order_id']).' LIMIT 1');
		$inf=$db->fetch($r1);
		//print_r($inf);
		$rs=$db->query('UPDATE blog_post SET post_spam='.intval($vl).' WHERE blog_id='.intval($inf['blog_id']).' AND order_id='.intval($_POST['order_id']));
		//echo 'UPDATE blog_post SET post_spam='.intval($vl).' WHERE blog_id='.intval($inf['blog_id']).' AND order_id='.intval($_POST['order_id']);
		$mas['status']='ok';
		echo json_encode($mas);
	}
	else
	{
		$mas['status']='fail';
		echo json_encode($mas);
	}
}

?>