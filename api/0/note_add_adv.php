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

//$_POST=$_GET;


$qisset=$db->query('SELECT order_id FROM blog_orders WHERE user_id='.$user['user_id'].' AND order_id='.intval($_POST['order_id']));
if ($db->num_rows($qisset)==0)
{
	$outmas['status']=2;
	echo json_encode($outmas);
	die();
}
if ($_POST['adv_query']==1)
{
	$query=query_maker($_POST,'p.post_note_count='.intval($_GET['value']),'select');
	// echo $query;
	// die();
	$qall_post=$db->query(trim($query));
	while ($post=$db->fetch($qall_post))
	{
		$_POST['post_id']=$post['post_id'];
		$db->query('INSERT INTO blog_note (post_id,order_id,note_content,note_time,user_id) VALUES ('.intval($_POST['post_id']).','.intval($_POST['order_id']).',\''.addslashes($_POST['note_content']).'\','.time().','.$user['user_id'].')');
		//echo 'INSERT INTO blog_note (post_id,order_id,note_content) VALUES ('.intval($_POST['post_id']).','.intval($_POST['order_id']).',\''.addslashes($_POST['note_content']).'\')';
		$db->query('UPDATE blog_post SET post_note_count=post_note_count+1 WHERE post_id='.intval($_POST['post_id']).' AND order_id='.intval($_POST['order_id']));
	}
	$outmas['status']='ok';
	echo json_encode($outmas);
	die();
}
else
{
	if (($_POST['post_id']!='') && ($_POST['order_id']!='') && ($_POST['note_content']))
	{
		$outmas['status']=1;
		echo json_encode($outmas);
		die();
	}
	$db->query('INSERT INTO blog_note (post_id,order_id,note_content,note_time,user_id) VALUES ('.intval($_POST['post_id']).','.intval($_POST['order_id']).',\''.addslashes($_POST['note_content']).'\','.time().','.$user['user_id'].')');
	//echo 'INSERT INTO blog_note (post_id,order_id,note_content) VALUES ('.intval($_POST['post_id']).','.intval($_POST['order_id']).',\''.addslashes($_POST['note_content']).'\')';
	$db->query('UPDATE blog_post SET post_note_count=post_note_count+1 WHERE post_id='.intval($_POST['post_id']).' AND order_id='.intval($_POST['order_id']));
	//echo 'UPDATE blog_post SET post_note_count=post_note_count+1 WHERE post_id='.intval($_POST['post_id']).' AND order_id='.intval($_POST['order_id']);
	$outmas['status']='ok';
	echo json_encode($outmas);
	die();
}

?>