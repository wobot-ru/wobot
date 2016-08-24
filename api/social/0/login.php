<?

require_once('com/db.php');
require_once('com/config.php');
require_once('bot/kernel.php');
require_once('com/func_gvk.php');

error_reporting(0);

$db = new database();
$db->connect();

if (($_GET['login']!='') && ($_GET['pass']!=''))
{
	$qus=$db->query('SELECT * FROM users WHERE user_email=\''.addslashes($_GET['login']).'\' AND user_pass=\''.addslashes(md5($_GET['pass'])).'\'');
	$user=$db->fetch($qus);
	//print_r($user);
	if ($user['user_id']!='')
	{
		$outmas['status']='ok';
		$outmas['user_id']=$user['user_id'];
		$outmas['token']=md5($user['user_email'].$user['user_pass']);
		echo json_encode($outmas);
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