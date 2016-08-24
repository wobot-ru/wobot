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
if (!$loged) 
{
	$mas['status']='fail';
	echo json_encode($mas);
	die();
}

$redis=new Redis() or die("Can'f load redis module.");
$redis->connect('127.0.0.1');
set_log('tag',$_POST);

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

/*Коллизия апдейтов, необходим механизм локов
mysql> LOCK TABLES trans READ, customer WRITE;
mysql> SELECT SUM(value) FROM trans WHERE customer_id=some_id;
mysql> UPDATE customer SET total_value=sum_from_previous_statement
        WHERE customer_id=some_id;
mysql> UNLOCK TABLES;
*/
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
	if ($_POST['adv_query']==1)
	{
		$query=query_maker($_POST,'p.post_nastr='.intval($_GET['value']),'select');
		// echo $query;
		// die();
		$qall_post=$db->query(trim($query));
		while ($post=$db->fetch($qall_post))
		{
			$lock=$db->query('LOCK TABLES blog_post WRITE');
			// echo $post['post_id'].' ';
			$_GET['id']=$post['post_id'];
			unset($mt);
			$newt='';
			$zap='';
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
			}
			else
			{
				$mas['status']='fail';
				echo json_encode($mas);
				die();
			}
			$lock=$db->query('UNLOCK TABLES');
		}
		echo json_encode($mas);
		die();
	}
	else
	{
		$lock=$db->query('LOCK TABLES blog_post WRITE');
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
		$lock=$db->query('UNLOCK TABLES');
	}
}
?>