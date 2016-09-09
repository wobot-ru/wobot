<?
require_once('/var/www/new/com/config.php');
require_once('/var/www/new/com/func.php');
require_once('/var/www/new/com/db.php');
require_once('/var/www/new/bot/kernel.php');
require_once('auth.php');

date_default_timezone_set ( 'Europe/Moscow' );

$db = new database();
$db->connect();

//$_POST=$_GET;

//echo $_COOKIE['user_id'];
auth();
if (!$loged) die();

$redis=new Redis() or die("Can'f load redis module.");
$redis->connect('127.0.0.1');
set_log('groups_get',$_POST);

$qus=$db->query('SELECT user_id,ut_id FROM blog_orders WHERE order_id='.intval($_POST['order_id']));
$us=$db->fetch($qus);
if ($us['user_id']!=$user['user_id'])
{
	$out['status']=2;
	echo json_encode($out);
	die();	
}

//print_r($_POST);
if (intval($_POST['order_id'])==0)
{
	$out['status']=1;
	echo json_encode($out);	
	die();
}

$qgroup=$db->query('SELECT * FROM blog_tp WHERE order_id='.$_POST['order_id']);
while ($group_order=$db->fetch($qgroup))
{
	//print_r($group_order);
	if ($group_order['tp_type']=='fb')
	{
		$yet_group[$group_order['tp_id']]['link']='http://www.facebook.com/'.$group_order['gr_id'];
		$yet_group[$group_order['tp_id']]['name']=$group_order['tp_name'];
	}
	if ($group_order['tp_type']=='vk')
	{
		if (!preg_match('/[a-z\-\.]/isu', $group_order['gr_id']))
		{
			$yet_group[$group_order['tp_id']]['link']='http://www.vk.com/club'.$group_order['gr_id'];
			$yet_group[$group_order['tp_id']]['name']=$group_order['tp_name'];
		}
		else
		{
			$yet_group[$group_order['tp_id']]['link']='http://www.vk.com/'.$group_order['gr_id'];
			$yet_group[$group_order['tp_id']]['name']=$group_order['tp_name'];	
		}
	}
	if ($group_order['tp_type']=='vk_acc')
	{
		if (!preg_match('/[a-z\-\.]/isu', $group_order['gr_id']))
		{
			$yet_group[$group_order['tp_id']]['link']='http://www.vk.com/id'.$group_order['gr_id'];
			$yet_group[$group_order['tp_id']]['name']=$group_order['tp_name'];
		}
		else
		{
			$yet_group[$group_order['tp_id']]['link']='http://www.vk.com/'.$group_order['gr_id'];	
			$yet_group[$group_order['tp_id']]['name']=$group_order['tp_name'];
		}
	}
	if ($group_order['tp_type']=='vk_board')
	{
		$yet_group[$group_order['tp_id']]['link']='http://www.vk.com/board'.$group_order['gr_id'];	
		$yet_group[$group_order['tp_id']]['name']=$group_order['tp_name'];
	}
	if ($group_order['tp_type']=='banki_forum')
	{
		$mid=explode('_', $group_order['gr_id']);
		$yet_group[$group_order['tp_id']]['link']='http://www.banki.ru/forum/?PAGE_NAME=read&FID='.$mid[0].'&TID='.$mid[1];	
		$yet_group[$group_order['tp_id']]['name']=$group_order['tp_name'];
	}
	if ($group_order['tp_type']=='banki_friends')
	{
		$mid=explode('/', $group_order['gr_id']);
		$yet_group[$group_order['tp_id']]['link']='http://www.banki.ru/friends/group/'.$mid[0].'/forum/'.$mid[1].'/';	
		$yet_group[$group_order['tp_id']]['name']=$group_order['tp_name'];
	}
	if ($group_order['tp_type']=='banki_question')
	{
		$yet_group[$group_order['tp_id']]['link']='http://www.banki.ru/services/questions-answers/?id='.$group_order['gr_id'];	
		$yet_group[$group_order['tp_id']]['name']=$group_order['tp_name'];
	}
	if ($group_order['tp_type']=='vk_video')
	{
		if (!preg_match('/[a-z\-\.]/isu', $group_order['gr_id']))
		{
			$yet_group[$group_order['tp_id']]['link']='http://www.vk.com/videos'.$group_order['gr_id'];
			$yet_group[$group_order['tp_id']]['name']=$group_order['tp_name'];
		}
		else
		{
			$yet_group[$group_order['tp_id']]['link']='http://www.vk.com/videos'.$group_order['gr_id'];	
			$yet_group[$group_order['tp_id']]['name']=$group_order['tp_name'];
		}
	}
}

echo json_encode($yet_group);
?>