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
$av_tariff[10]=2;
$av_tariff[11]=2;

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
		if ($av_tariff[$newtar['tariff_id']]==$av_tariff[$rt['tariff_id']])
		{
			$outmas['tariff_id']=$newtar['tariff_id'];
			if ($newtar['tariff_posts']!=0)
			{
				$outmas['real_quot']=$user['ut_date'];
				$outmas['new_quot']=intval($user['ut_date']*$rt['convert_standart']/$newtar['convert_standart']);
			}
			else
			{
				$qorders=$db->query('SELECT order_id FROM blog_orders WHERE ut_id='.$user['ut_id']);
				$count_orders=$db->num_rows($qorders);
				if ($count_orders<=$newtar['tariff_quot'])
				{
					$outmas['real_quot']=intval(($user['ut_date']-time())/86400);
					$outmas['new_quot']=intval(intval(($user['ut_date']-time())/86400)*$rt['convert_standart']/$newtar['convert_standart']);
				}
				else
				{
					unset($outmas);
					$outmas['status']=2;
					echo json_encode($outmas);
					die();
				}
			}
			$outmas['status']='ok';
			echo json_encode($outmas);
			die();
		}
		else
		{
			$outmas['tariff_id']=$newtar['tariff_id'];
			if ($newtar['tariff_posts']!=0)
			{
				$outmas['real_quot']=$user['ut_date'];
				$outmas['new_quot']=intval($user['ut_date']*$rt['convert_standart']/$newtar['convert_substandart']);
			}
			else
			{
				$qorders=$db->query('SELECT order_id FROM blog_orders WHERE ut_id='.$user['ut_id']);
				$count_orders=$db->num_rows($qorders);
				if ($count_orders<=$newtar['tariff_quot'])
				{
					$outmas['real_quot']=intval(($user['ut_date']-time())/86400);
					$outmas['new_quot']=intval(intval(($user['ut_date']-time())/86400)*$rt['convert_standart']/$newtar['convert_substandart']);
				}
				else
				{
					unset($outmas);
					$outmas['status']=2;
					echo json_encode($outmas);
					die();
				}
			}
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