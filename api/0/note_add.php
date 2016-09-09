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

//$_POST=$_GET;

if (($_POST['post_id']!='') && ($_POST['order_id']!='') && ($_POST['note_content']))
{
	$qisset=$db->query('SELECT order_id FROM blog_orders WHERE user_id='.$user['user_id'].' AND order_id='.intval($_POST['order_id']));
	if ($db->num_rows($qisset)==0)
	{
		$outmas['status']=2;
		echo json_encode($outmas);
		die();
	}
	if ($_POST['post_id']!='')
	{
		$mid=explode(',', trim($_POST['post_id']));
		foreach ($mid as $id)
		{
			$db->query('INSERT INTO blog_note (post_id,order_id,note_content,note_time,user_id) VALUES ('.intval($id).','.intval($_POST['order_id']).',\''.addslashes($_POST['note_content']).'\','.time().','.$user['user_id'].')');
			//echo 'INSERT INTO blog_note (post_id,order_id,note_content) VALUES ('.intval($_POST['post_id']).','.intval($_POST['order_id']).',\''.addslashes($_POST['note_content']).'\')';
			$db->query('UPDATE blog_post SET post_note_count=post_note_count+1 WHERE post_id='.intval($id).' AND order_id='.intval($_POST['order_id']));
			//echo 'UPDATE blog_post SET post_note_count=post_note_count+1 WHERE post_id='.intval($_POST['post_id']).' AND order_id='.intval($_POST['order_id']);
		}
		$outmas['status']='ok';
		echo json_encode($outmas);
		die();
	}
	else
	{
		$mid=explode(',', trim($_POST['parent_id']));
		foreach ($mid as $id)
		{
			$qposts=$db->query('SELECT post_id FROM blog_post WHERE parent='.intval($id).' AND order_id='.intval($_POST['order_id']));
			while ($post=$db->fetch($qposts))
			{
				$db->query('INSERT INTO blog_note (post_id,order_id,note_content,note_time,user_id) VALUES ('.intval($post['post_id']).','.intval($_POST['order_id']).',\''.addslashes($_POST['note_content']).'\','.time().','.$user['user_id'].')');
				//echo 'INSERT INTO blog_note (post_id,order_id,note_content) VALUES ('.intval($_POST['post_id']).','.intval($_POST['order_id']).',\''.addslashes($_POST['note_content']).'\')';
				$db->query('UPDATE blog_post SET post_note_count=post_note_count+1 WHERE post_id='.intval($post['post_id']).' AND order_id='.intval($_POST['order_id']));
				//echo 'UPDATE blog_post SET post_note_count=post_note_count+1 WHERE post_id='.intval($_POST['post_id']).' AND order_id='.intval($_POST['order_id']);
			}
		}
		$outmas['status']='ok';
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