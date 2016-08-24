<?

require_once('/var/www/new/com/config.php');
require_once('/var/www/new/com/func.php');
require_once('/var/www/new/com/db.php');
require_once('/var/www/new/bot/kernel.php');
require_once('auth.php');

$db = new database();
$db->connect();

// $_POST['order_id']=4700;
// $_POST['id']='28164298,28164293,28164285';
// $_POST['tag_value']='true';
// $_POST['tag_id']=1;
// $_GET['test_user_id']=1187;
// $_GET['test_token']='06e82decff5d0eb94004c8d9c7bf1671';

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
	// $lock=$db->query('LOCK TABLES blog_post WRITE');
	if ($_POST['type']=='parent')
	{
		$mid=explode(',', trim($_GET['parent_id']));
		$mid=array_unique($mid);
		foreach ($mid as $id)
		{
			if (intval(trim($id))==0) continue;
			$rs=$db->query('SELECT post_id,post_tag,parent FROM blog_post WHERE order_id='.intval($_GET['order_id']).' AND parent='.intval($id));
			while ($tt=$db->fetch($rs))
			{
				$mtagpost=explode(',', $tt['post_tag']);
				if ($_GET['tag_value']=='true')
				{
					if (!in_array($_GET['tag_id'], $mtagpost)) $db->query('UPDATE blog_post SET post_tag = post_tag | '.pow(2,intval($_GET['tag_id'])-1).' WHERE post_id='.intval($tt['post_id']).' AND order_id='.intval($_GET['order_id']));
				}
				elseif ($_GET['tag_value']=='false')
				{
					if (in_array($_GET['tag_id'], $mtagpost)) $db->query('UPDATE blog_post SET post_tag = post_tag ^ '.pow(2,intval($_GET['tag_id'])-1).' WHERE post_id='.intval($tt['post_id']).' AND order_id='.intval($_GET['order_id']));
				}
				// $db->query('UPDATE blog_post SET post_tag = post_tag '.($_GET['tag_value']=='true'?'|':'^').' '.pow(2,intval($_GET['tag_id'])-1).' WHERE parent='.intval($tt['parent']).' AND order_id='.intval($_GET['order_id']));
				// echo 'UPDATE blog_post SET post_tag = post_tag '.($_GET['tag_value']=='true'?'|':'^').' '.pow(2,intval($_GET['tag_id'])-1).' WHERE parent='.intval($tt['parent']).' AND order_id='.intval($_GET['order_id']);
			}
		}
		$mas['status']='ok';
		echo json_encode($mas);
		die();
	}
	else
	{
		$mid=explode(',', trim($_GET['id']));
		$mid=array_unique($mid);
		// print_r($mid);
		foreach ($mid as $id)
		{
			// echo 'SELECT * FROM blog_post WHERE order_id='.intval($_GET['order_id']).' AND post_id='.intval($id).' LIMIT 1';
			$rs=$db->query('SELECT * FROM blog_post WHERE order_id='.intval($_GET['order_id']).' AND post_id='.intval($id).' LIMIT 1');
			$tt=$db->fetch($rs);
			if (intval($tt['post_id'])!=0)
			{
				// echo 'UPDATE blog_post SET post_tag = post_tag '.($_GET['tag_value']=='true'?'|':'^').' '.pow(2,intval($_GET['tag_id'])-1).' WHERE post_id='.intval($id).' AND order_id='.intval($_GET['order_id']);
				// $db->query('UPDATE blog_post SET post_tag = post_tag '.($_GET['tag_value']=='true'?'|':'^').' '.pow(2,intval($_GET['tag_id'])-1).' WHERE post_id='.intval($id).' AND order_id='.intval($_GET['order_id']));
				$mtagpost=explode(',', $tt['post_tag']);
				if ($_GET['tag_value']=='true')
				{
					if (!in_array($_GET['tag_id'], $mtagpost)) $db->query('UPDATE blog_post SET post_tag = post_tag | '.pow(2,intval($_GET['tag_id'])-1).' WHERE post_id='.intval($tt['post_id']).' AND order_id='.intval($_GET['order_id']));
				}
				elseif ($_GET['tag_value']=='false')
				{
					if (in_array($_GET['tag_id'], $mtagpost)) $db->query('UPDATE blog_post SET post_tag = post_tag ^ '.pow(2,intval($_GET['tag_id'])-1).' WHERE post_id='.intval($tt['post_id']).' AND order_id='.intval($_GET['order_id']));
				}
			}
			$mas['status']='ok';
		}
	}
	// $lock=$db->query('UNLOCK TABLES');
	echo json_encode($mas);
	die();
}
?>