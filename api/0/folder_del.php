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

if ($_POST['folder_id']!='')
{
	$qsth=$db->query('SELECT * FROM blog_folders WHERE user_id='.intval($user['user_id']).' AND folder_id='.intval($_POST['folder_id']));
	if ($db->num_rows($qsth)!=0)
	{
		//echo 'DELETE FROM blog_subthemes WHERE order_id='.intval($_POST['order_id']).' AND subtheme_id='.intval($_POST['subtheme_id']);
		$db->query('DELETE FROM blog_folders WHERE user_id='.intval($user['user_id']).' AND folder_id='.intval($_POST['folder_id']));
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