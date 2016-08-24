<?

require_once('/var/www/new/com/config.php');
require_once('/var/www/new/com/func.php');
require_once('/var/www/new/com/db.php');
require_once('/var/www/new/bot/kernel.php');
require_once('auth.php');

//$_POST=$_GET;
auth();

$db=new database();
$db->connect();

$av_tariff[3]=1;
$av_tariff[5]=1;
$av_tariff[6]=1;
$av_tariff[7]=1;
$av_tariff[10]=1;
$av_tariff[11]=1;

$qrt=$db->query('SELECT * FROM blog_tariff WHERE tariff_id='.$user['tariff_id'].' LIMIT 1');
$rt=$db->fetch($qrt);

if (($_POST['tariff_id']!='') && ($_POST['count']!=''))
{
	if (!isset($av_tariff[$_POST['tariff_id']]))
	{
		$outmas['status']=4;
		echo json_encode($outmas);
		die();
	}
	if ($_POST['count']==0)
	{
		$outmas['status']=5;
		echo json_encode($outmas);
		die();
	}
	$qnewtar=$db->query('SELECT * FROM blog_tariff WHERE tariff_id='.$_POST['tariff_id'].' LIMIT 1');
	$newtar=$db->fetch($qnewtar);
	if (intval($newtar['tariff_id'])!=0)
	{
		if ($newtar['tariff_quot']!=0)
		{
			$qorders=$db->query('SELECT order_id FROM blog_orders WHERE ut_id='.$user['ut_id']);
			$count_orders=$db->num_rows($qorders);
			if ($count_orders<=$newtar['tariff_quot'])
			{
				//echo 'UPDATE user_tariff SET tariff_id='.intval($_POST['tariff_id']).' WHERE ut_id='.$user['ut_id'];
				//$db->query('UPDATE user_tariff SET tariff_id='.intval($_POST['tariff_id']).' WHERE ut_id='.$user['ut_id']);
				$outmas['tariff_id']=$newtar['tariff_id'];
				$outmas['real_quot']=intval(($user['ut_date']-time())/86400);
				$outmas['new_quot']=intval(($user['ut_date']-time())/86400);
				if ($newtar['tariff_id']==10) $outmas['new_quot']=intval(($user['ut_date']-time())/86400)*100;

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
		elseif ($newtar['tariff_posts']!=0)
		{
			//echo 'UPDATE user_tariff SET tariff_id='.intval($_POST['tariff_id']).' WHERE ut_id='.$user['ut_id'];
			//$db->query('UPDATE user_tariff SET tariff_id='.intval($_POST['tariff_id']).' WHERE ut_id='.$user['ut_id']);
			$outmas['tariff_id']=$newtar['tariff_id'];
			$outmas['real_quot']=intval(($user['money']/$newtar['tariff_posts'])*$newtar['tariff_price']);
			$outmas['new_quot']=intval(($user['money']/$newtar['tariff_posts'])*$newtar['tariff_price']);
			$outmas['status']='ok';
			echo json_encode($outmas);
			die();
		}
	}
	else
	{
		$outmas['status']=3;
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