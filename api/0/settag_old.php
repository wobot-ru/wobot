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
//print_r($_GET['mas_post_tags']);
if ($user['tariff_id']!=3)
{
	$_GET=$_POST;
	//$user['user_id']=61;
	$rs=$db->query('SELECT * FROM blog_post WHERE order_id='.intval($_GET['order_id']).' AND post_id='.intval($_GET['id']));
	$tt=$db->fetch($rs);
	if (intval($tt['post_id'])!=0)
	{
		$mt=explode(',',$tt['post_tag']);
		$zap='';
		if ($_GET['tag_value']=='true')
		{
			$mt[]=$_GET['tag_id'];
			sort($mt);
			foreach ($mt as $it)
			{
				$newt.=$zap.$it;
				$zap=',';
			}
			$db->query('UPDATE blog_post SET post_tag=\''.$newt.'\' WHERE post_id='.intval($_GET['id']).' AND order_id='.intval($_GET['order_id']));
			//echo 'UPDATE blog_post SET post_tag=\''.$newt.'\' WHERE post_id='.intval($_GET['id']).' AND order_id='.intval($_GET['order_id']);
			$mas['status']='ok';
		}
		else
		{
			//print_r($mt);
			foreach ($mt as $item)
			{
				if ($item!=$_GET['tag_id'])
				{
					$newt.=$zap.$item;
					$zap=',';
				}
			}
			$db->query('UPDATE blog_post SET post_tag=\''.$newt.'\' WHERE post_id='.intval($_GET['id']).' AND order_id='.intval($_GET['order_id']));
			//echo 'UPDATE blog_post SET post_tag=\''.$newt.'\' WHERE post_id='.intval($_GET['id']).' AND order_id='.intval($_GET['order_id']);
		
			$mas['status']='ok';
		}
		//$rs=$db->query('UPDATE blog_post SET post_tag=\''.$text_tag.'\' WHERE post_id='.intval($_GET['id']).' AND order_id='.intval($_GET['order_id']));
		echo json_encode($mas);
	}
	else
	{
		$mas['status']='fail';
		echo json_encode($mas);
	}
}
?>