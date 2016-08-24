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
set_log('att',$_POST);

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
	$outmas['status']=2;
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
	{
		//$user['user_id']=61;
		if ($_POST['adv_query']==1)
		{
			$query=query_maker($_POST,'p.post_fav2='.intval($_GET['value']),'select');
			// echo $query;
			// die();
			$qall_post=$db->query(trim($query));
			while ($post=$db->fetch($qall_post))
			{
				$_GET['post_id']=$post['post_id'];
				unset($mt);
				$zap='';
				$newt='';
				unset($yetfav);
				$lock=$db->query('LOCK TABLES blog_post WRITE');
				$rs=$db->query('SELECT * FROM blog_post WHERE order_id='.intval($_GET['order_id']).' AND post_id='.intval($_GET['post_id']).' LIMIT 1');
				$tt=$db->fetch($rs);
				if (intval($tt['post_id'])!=0)
				{
					$mt=explode(',',$tt['post_fav2']);
					$zap='';
					if ($_GET['fav_value']=='true')
					{
						$mt[]=$user['user_id'];
						sort($mt);
						foreach ($mt as $it)
						{
							if (trim($it)=='') continue;
							if (isset($yetfav[$it])) continue;
							$yetfav[$it]=1;
							$newt.=$zap.$it;
							$zap=',';
						}
						// print_r($user);
						// print_r($mt);
						$db->query('UPDATE blog_post SET post_fav2=\''.$newt.'\' WHERE post_id='.intval($_GET['post_id']).' AND order_id='.intval($_GET['order_id']));
						// echo 'UPDATE blog_post SET post_fav2=\''.$newt.'\' WHERE post_id='.intval($_GET['post_id']).' AND order_id='.intval($_GET['order_id']);
						$mas['status']='ok';
					}
					else
					{
						//print_r($mt);
						foreach ($mt as $item)
						{
							if ($item!=$user['user_id'])
							{
								$newt.=$zap.$item;
								$zap=',';
							}
						}
						$db->query('UPDATE blog_post SET post_fav2=\''.$newt.'\' WHERE post_id='.intval($_GET['post_id']).' AND order_id='.intval($_GET['order_id']));
						// echo 'UPDATE blog_post SET post_fav2=\''.$newt.'\' WHERE post_id='.intval($_GET['post_id']).' AND order_id='.intval($_GET['order_id']);
					
						$mas['status']='ok';
					}
					//$rs=$db->query('UPDATE blog_post SET post_tag=\''.$text_tag.'\' WHERE post_id='.intval($_GET['id']).' AND order_id='.intval($_GET['order_id']));
				}
				else
				{
					$mas['status']=4;
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
			if (($_POST['order_id']!='') && ($_POST['post_id']!='') && ($_GET['fav_value']!=''))
			{
				$outmas['status']=1;
				echo json_encode($outmas);
				die();
			}

			$lock=$db->query('LOCK TABLES blog_post WRITE');
			$rs=$db->query('SELECT * FROM blog_post WHERE order_id='.intval($_GET['order_id']).' AND post_id='.intval($_GET['post_id']).' LIMIT 1');
			$tt=$db->fetch($rs);
			if (intval($tt['post_id'])!=0)
			{
				$mt=explode(',',$tt['post_fav2']);
				$zap='';
				if ($_GET['fav_value']=='true')
				{
					$mt[]=$user['user_id'];
					sort($mt);
					foreach ($mt as $it)
					{
						if (trim($it)=='') continue;
						if (isset($yetfav[$it])) continue;
						$yetfav[$it]=1;
						$newt.=$zap.$it;
						$zap=',';
					}
					// print_r($user);
					// print_r($mt);
					$db->query('UPDATE blog_post SET post_fav2=\''.$newt.'\' WHERE post_id='.intval($_GET['post_id']).' AND order_id='.intval($_GET['order_id']));
					// echo 'UPDATE blog_post SET post_fav2=\''.$newt.'\' WHERE post_id='.intval($_GET['post_id']).' AND order_id='.intval($_GET['order_id']);
					$mas['status']='ok';
				}
				else
				{
					//print_r($mt);
					foreach ($mt as $item)
					{
						if ($item!=$user['user_id'])
						{
							$newt.=$zap.$item;
							$zap=',';
						}
					}
					$db->query('UPDATE blog_post SET post_fav2=\''.$newt.'\' WHERE post_id='.intval($_GET['post_id']).' AND order_id='.intval($_GET['order_id']));
					// echo 'UPDATE blog_post SET post_fav2=\''.$newt.'\' WHERE post_id='.intval($_GET['post_id']).' AND order_id='.intval($_GET['order_id']);
				
					$mas['status']='ok';
				}
				//$rs=$db->query('UPDATE blog_post SET post_tag=\''.$text_tag.'\' WHERE post_id='.intval($_GET['id']).' AND order_id='.intval($_GET['order_id']));
				echo json_encode($mas);
			}
			else
			{
				$mas['status']=4;
				echo json_encode($mas);
			}
			$lock=$db->query('UNLOCK TABLES');
		}
	}
	// else
	{
		// $outmas['status']=1;
		// echo json_encode($outmas);
		// die();
	}
}
else
{
	$outmas['status']=3;
	echo json_encode($outmas);
	die();
}
?>