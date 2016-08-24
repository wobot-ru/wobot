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
if (!$loged) die();

$redis=new Redis() or die("Can'f load redis module.");
$redis->connect('127.0.0.1');
set_log('spam',$_POST);

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
	if ($_POST['adv_query']==1)
	{
		$query=query_maker($_POST,'p.post_spam='.intval($vl),'update');
		// echo $query;
		$db->query($query);
		$mas['status']='ok';
		echo json_encode($mas);
		die();
	}
	if ($_POST['type']=='post')
	{
		$ids=explode(',',$_POST['id']);
		foreach ($ids as $id)
		{
			$rs=$db->query('UPDATE blog_post SET post_spam='.intval($vl).' WHERE post_id='.intval($id).' AND order_id='.intval($_POST['order_id']));
			//echo 'UPDATE blog_post SET post_spam='.intval($vl).' WHERE post_id='.intval($id).' AND order_id='.intval($_POST['order_id']);
		}
		$mas['status']='ok';
		echo json_encode($mas);
		die();
	}
	elseif ($_POST['type']=='host')
	{
		$r1=$db->query('SELECT post_host FROM blog_post WHERE post_id='.intval($_POST['id']).' AND order_id='.intval($_POST['order_id']).' LIMIT 1');
		$inf=$db->fetch($r1);
		$rs=$db->query('UPDATE blog_post SET post_spam='.intval($vl).' WHERE post_host=\''.($inf['post_host']).'\' AND order_id='.intval($_POST['order_id']));
		//echo 'UPDATE blog_post SET post_spam='.intval($vl).' WHERE post_host=\''.($inf['post_host']).'\' AND order_id='.intval($_POST['order_id']);
		$mas['status']='ok';
		echo json_encode($mas);
		die();
	}
	elseif ($_POST['type']=='author')
	{
		if (trim($_POST['id'])!='')
		{
			$ids=explode(',',$_POST['id']);
			foreach ($ids as $id)
			{
				$spam_info=$db->query('SELECT order_spam FROM blog_orders WHERE order_id='.intval($_POST['order_id']).' LIMIT 1');
				$spm=$db->fetch($spam_info);
				$spam=json_decode($spm['order_spam'],true);
				$r1=$db->query('SELECT blog_id FROM blog_post WHERE post_id='.intval($id).' AND order_id='.intval($_POST['order_id']).' LIMIT 1');
				$inf=$db->fetch($r1);
				if ($inf['blog_id']==0) continue;
				if ($vl==1)
				{
					$spam[$inf['blog_id']]=1;
				}
				elseif ($vl==0)
				{
					unset($spam[$inf['blog_id']]);
				}
				$db->query('UPDATE blog_orders SET order_spam=\''.json_encode($spam).'\' WHERE order_id='.intval($_POST['order_id']));
				//print_r($inf);
				$rs=$db->query('UPDATE blog_post SET post_spam='.intval($vl).' WHERE blog_id='.intval($inf['blog_id']).' AND order_id='.intval($_POST['order_id']));
				//echo 'UPDATE blog_post SET post_spam='.intval($vl).' WHERE blog_id='.intval($inf['blog_id']).' AND order_id='.intval($_POST['order_id']);
			}
		}
		elseif (trim($_POST['blog_id'])!='')
		{
			$ids=explode(',',trim($_POST['blog_id']));
			foreach ($ids as $id)
			{
				$spam_info=$db->query('SELECT order_spam FROM blog_orders WHERE order_id='.intval($_POST['order_id']).' LIMIT 1');
				$spm=$db->fetch($spam_info);
				$spam=json_decode($spm['order_spam'],true);
				if ($vl==1)
				{
					$spam[$id]=1;
				}
				elseif ($vl==0)
				{
					unset($spam[$id]);
				}
				$db->query('UPDATE blog_orders SET order_spam=\''.json_encode($spam).'\' WHERE order_id='.intval($_POST['order_id']));
				//print_r($inf);
				$rs=$db->query('UPDATE blog_post SET post_spam='.intval($vl).' WHERE blog_id='.intval($id).' AND order_id='.intval($_POST['order_id']));
				//echo 'UPDATE blog_post SET post_spam='.intval($vl).' WHERE blog_id='.intval($inf['blog_id']).' AND order_id='.intval($_POST['order_id']);
			}
		}
		$mas['status']='ok';
		echo json_encode($mas);
		die();
	}
	else
	{
		$mas['status']='fail';
		echo json_encode($mas);
	}
}

?>