<?

require_once('com/db.php');
require_once('com/config.php');
require_once('bot/kernel.php');
require_once('com/func_gvk.php');

error_reporting(0);

$db = new database();
$db->connect();

if (($_GET['token']!='') && ($_GET['user_id']!='') && ($_GET['gr_id']))
{
	$qus=$db->query('SELECT * FROM users as a LEFT JOIN group_orders as b ON a.user_id=b.user_id WHERE a.user_id='.intval($_GET['user_id']).' AND b.id='.intval($_GET['gr_id']));
	//echo 'SELECT * FROM users as a LEFT JOIN group_orders as b ON a.user_id=b.user_id WHERE a.user_id='.intval($_GET['user_id']).' AND b.id='.intval($_GET['gr_id']);
	$user=$db->fetch($qus);
	if (md5($user['user_email'].$user['user_pass'])==$_GET['token'])
	{
		//print_r($user);
		//$outmas['123']=123;
		//echo json_encode($outmas);
		//$outmas=json_decode($user['group_json'],true);
		//echo json_encode($outmas);
		if ($user['group_json']!='')
		{
			echo $user['group_json'];
		}
		else
		{
			$outmas['status']='fail';
			echo json_encode($outmas);
		}
		//print_r($outmas);
		//echo stripslashes($user['group_json']);
	}
	else
	{
		$outmas['status']='fail';
		echo json_encode($outmas);
	}
}
else
{
	$outmas['status']='fail';
	echo json_encode($outmas);
}

?>