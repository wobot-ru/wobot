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

if ($_POST['folder_name']!='')
{
	$qsth=$db->query('SELECT * FROM blog_folders WHERE user_id='.intval($user['user_id']).' AND folder_name=\''.addslashes($_POST['folder_name']).'\'');
	if ($db->num_rows($qsth)==0)
	{
		//echo 'INSERT INTO blog_folders (order_id,folder_name) VALUES ('.intval($_POST['order_id']).',\''.addslashes($_POST['folder_name']).'\')';
		$db->query('INSERT INTO blog_folders (user_id,folder_name) VALUES ('.intval($user['user_id']).',\''.addslashes($_POST['folder_name']).'\')');
		$outmas['folder_id']=$db->insert_id();
		$outmas['status']='ok';
		echo json_encode($outmas);
		die();
	}
	else
	{
		$outmas['status']=2;
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