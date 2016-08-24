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

if (($_POST['post_id']!='') && ($_POST['order_id']!=''))
{
	$i=0;
	$qnote=$db->query('SELECT * FROM blog_note as a LEFT JOIN users as b ON a.user_id=b.user_id WHERE post_id='.intval($_POST['post_id']).' AND order_id='.intval($_POST['order_id']));
	while ($note=$db->fetch($qnote))
	{
		$outmas['notes'][$i]['content']=$note['note_content'];
		$outmas['notes'][$i]['time']=$note['note_time']+13*60;
		$outmas['notes'][$i]['id']=$note['note_id'];
		$outmas['notes'][$i]['owner']=$note['user_email'];
		$i++;
	}
	//echo 'UPDATE blog_post SET post_note_count=post_note_count+1 WHERE post_id='.intval($_POST['post_id']).' AND order_id='.intval($_POST['order_id']);
	$outmas['status']='ok';
	echo json_encode($outmas);
	die();
}
else
{
	$outmas['status']=1;
	echo json_encode($outmas);
	die();
}

?>