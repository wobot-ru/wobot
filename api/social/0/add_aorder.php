<?

require_once('com/db.php');
require_once('com/config.php');
require_once('bot/kernel.php');
require_once('com/func_gvk.php');

error_reporting(0);

$db = new database();
$db->connect();

if (($_GET['user_id']!='') && ($_GET['token']!='') && ($_GET['group']))
{
	$qus=$db->query('SELECT * FROM users WHERE user_id='.intval($_GET['user_id']));
	$user=$db->fetch($qus);
	if (md5($user['user_email'].$user['user_pass'])==$_GET['token'])
	{
		$rg='/\/club(?<id>\d+)$/isu';
		preg_match_all($rg,$_GET['group'],$out);
		if ($out['id'][0]=='')
		{
			$rg='/vk\.com\/(?<id>.*)$/isu';
			preg_match_all($rg,$_GET['group'],$out);
		}
		$db->query('INSERT INTO group_orders (group_link,group_start,group_end,user_id) VALUES (\'acc_'.addslashes($out['id'][0]).'\','.$_GET['start'].','.$_GET['end'].','.$user['user_id'].')');
		$outmas['group_id']=$db->insert_id();
		$outmas['status']='ok';
		//run_crawling($db->insert_id());
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