<?

require_once('/var/www/new/com/config.php');
require_once('/var/www/new/com/func.php');
require_once('/var/www/new/com/db.php');
require_once('/var/www/new/bot/kernel.php');
require_once('auth.php');
require_once('graph_func.php');
require_once('/var/www/cashjob/com/func_spec.php');

$db = new database();
$db->connect();

//$_POST=$_GET;

/*$_GET['test_token']='06e82decff5d0eb94004c8d9c7bf1671';
$_GET['test_user_id']=1187;

$_POST['order_id']=2069;
$_POST['widget_id']=1;
$_POST['start_date']='01.01.2012';
$_POST['end_date']='01.12.2012';
$_POST['step']='day';*/

date_default_timezone_set ( 'Europe/Moscow' );

$redis=new Redis() or die("Can'f load redis module.");
$redis->connect('127.0.0.1');

auth();
if (!$loged) die();

$_POST['order_id']=intval($_POST['order_id']);
$_POST['suborder_id']=intval($_POST['suborder_id']);
$_POST['widget_id']=intval($_POST['widget_id']);
$_POST['start_date']=strtotime($_POST['start_date']);
$_POST['end_date']=strtotime($_POST['end_date']);

$av_step['hour']=1/24;
$av_step['day']=1;
$av_step['week']=6;
$av_step['month']=30;
$av_step['year']=365;

if (($_POST['order_id']!=0) && ($_POST['widget_id']!=0) && ($_POST['start_date']<=$_POST['end_date']) && ($_POST['start_date']!=0) && ($_POST['end_date']!=0) && ($av_step[$_POST['step']]!=''))
{
	if ($_POST['suborder_id']!=0)
	{
		$qsuborder=$db->query('SELECT * FROM blog_subthemes as a LEFT JOIN blog_orders as b ON a.order_id=b.order_id LEFT JOIN blog_sharing as c ON b.order_id=c.order_id WHERE a.subtheme_id='.$_POST['suborder_id'].' AND a.order_id='.$_POST['order_id'].' AND (b.user_id='.$user['user_id']).' OR c.user_id='.$user['user_id'].')';
		$suborder=$db->fetch($qsuborder);
		if (intval($suborder['subtheme_id'])>0)
		{
			$settings=json_decode($suborder['subtheme_settings'],true);
			$widget=$settings['widgets'][intval($_POST['widget_id'])-1];
			//print_r($_POST);
			if ($widget['type']=='linear')
			{
				foreach ($widget['data']['themes'] as $item)
				{
					$allorder[$item]=1;
					$outmas['themes'][$item]['data']=get_linear_data($item,$_POST['start_date'],$_POST['end_date'],$item['step'],$item['y']);
				}
				foreach ($widget['data']['sub_themes'] as $item)
				{
					$allorder[$item]=2;
					$outmas['sub_themes'][$item]['data']=get_linear_data('s'.$item,$_POST['start_date'],$_POST['end_date'],$item['step'],$item['y']);
				}
			}
			if ($widget['type']=='stack')
			{
				foreach ($widget['data']['themes'] as $item)
				{
					$allorder[$item]=1;
					$outmas['themes'][$item]['data']=get_stack_data($item,$_POST['start_date'],$_POST['end_date'],$item['step'],$item['y'],$item['split'],$item['x']);
				}
				foreach ($widget['data']['sub_themes'] as $item)
				{
					$allorder[$item]=2;
					$outmas['sub_themes'][$item]['data']=get_stack_data('s'.$item,$_POST['start_date'],$_POST['end_date'],$item['step'],$item['y'],$item['split'],$item['x']);
				}
			}
			if ($widget['type']=='pie')
			{
				foreach ($widget['data']['themes'] as $item)
				{
					$allorder[$item]=1;
					$outmas['themes'][$item]['data']=get_pie_data($item,$_POST['start_date'],$_POST['end_date'],$item['metric'],$item['split']);
				}
				foreach ($widget['data']['themes'] as $item)
				{
					$allorder[$item]=2;
					$outmas['sub_themes'][$item]['data']=get_pie_data('s'.$item,$_POST['start_date'],$_POST['end_date'],$item['metric'],$item['split']);
				}
			}
			if ($widget['type']=='dashboard')
			{
				foreach ($widget['data']['themes'] as $item)
				{
					$allorder[$item]=1;
					$outmas['themes'][$item]['data']=get_metric($item,$_POST['start_date'],$_POST['end_date']);
				}
				foreach ($widget['data']['themes'] as $item)
				{
					$allorder[$item]=2;
					$outmas['sub_themes'][$item]['data']=get_metric('s'.$item,$_POST['start_date'],$_POST['end_date']);
				}
			}
			foreach ($allorder as $key => $item)
			{
				if ($item==1)
				{
					$qth.=$zap1.$key;
					$zap1=',';
				}
				if ($item==2)
				{
					$qsth.=$zap2.$key;
					$zap2=',';
				}
			}
			$qth=$db->query('SELECT order_id,order_name FROM blog_orders WHERE order_id IN ('.$qth.')');
			while ($th=$db->fetch($qth))
			{
				$outmas['themes'][$th['order_id']]['name']=$th['order_name'];
			}
			$qth=$db->query('SELECT subtheme_id,subtheme_name FROM blog_subthemes WHERE subtheme_id IN ('.$qsth.')');
			while ($th=$db->fetch($qth))
			{
				$outmas['sub_themes'][$th['subtheme_id']]['name']=$th['order_name'];
			}
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
		$qorder=$db->query('SELECT a.order_id as order_id,b.order_id as s_order_id,a.order_settings FROM blog_orders as a LEFT JOIN blog_sharing as b ON a.order_id=b.order_id WHERE a.order_id='.$_POST['order_id'].' AND (a.user_id='.$user['user_id'].' OR a.user_id=61 OR b.user_id='.$user['user_id'].')');
		$order=$db->fetch($qorder);
		//print_r($order);
		if (intval($order['order_id'])>0)
		{
			$settings=json_decode($order['order_settings'],true);
			//print_r($settings);
			$widget=$settings['widgets'][intval($_POST['widget_id'])-1];
			if ($widget['type']=='linear')
			{
				foreach ($widget['data']['themes'] as $item)
				{
					$allorder[$item]=1;
					$outmas['themes'][$item]['data']=get_linear_data($item,$_POST['start_date'],$_POST['end_date'],$av_step[$_POST['step']],$widget['data']['y'],$widget['data']['x']);
				}
				foreach ($widget['data']['sub_themes'] as $item)
				{
					$allorder[$item]=2;
					$outmas['sub_themes'][$item]['data']=get_linear_data('s'.$item,$_POST['start_date'],$_POST['end_date'],$av_step[$_POST['step']],$widget['data']['y'],$widget['data']['x']);
				}
			}
			if ($widget['type']=='stack')
			{
				//$widget['data']['y']='post_count';
				//$widget['data']['split']='blog_location';
				foreach ($widget['data']['themes'] as $item)
				{
					$allorder[$item]=1;
					$outmas['themes'][$item]['data']=get_stack_data($item,$_POST['start_date'],$_POST['end_date'],$av_step[$_POST['step']],$widget['data']['y'],$widget['data']['split'],$widget['data']['x']);
				}
				foreach ($widget['data']['sub_themes'] as $item)
				{
					$allorder[$item]=2;
					$outmas['sub_themes'][$item]['data']=get_stack_data('s'.$item,$_POST['start_date'],$_POST['end_date'],$av_step[$_POST['step']],$widget['data']['y'],$widget['data']['split'],$widget['data']['x']);
				}
			}
			if ($widget['type']=='pie')
			{
				foreach ($widget['data']['themes'] as $item)
				{
					$allorder[$item]=1;
					$outmas['themes'][$item]['data']=get_pie_data($item,$_POST['start_date'],$_POST['end_date'],$widget['data']['metric'],$widget['data']['split']);
				}
				foreach ($widget['data']['sub_themes'] as $item)
				{
					$allorder[$item]=2;
					$outmas['sub_themes'][$item]['data']=get_pie_data('s'.$item,$_POST['start_date'],$_POST['end_date'],$widget['data']['metric'],$widget['data']['split']);
				}
			}
			if ($widget['type']=='dashboard')
			{
				foreach ($widget['data']['themes'] as $item)
				{
					$allorder[$item]=1;
					$outmas['themes'][$item]['data']=get_metric($item,$_POST['start_date'],$_POST['end_date']);
				}
				foreach ($widget['data']['sub_themes'] as $item)
				{
					$allorder[$item]=2;
					$outmas['sub_themes'][$item]['data']=get_metric('s'.$item,$_POST['start_date'],$_POST['end_date']);
				}
			}
			foreach ($allorder as $key => $item)
			{
				if ($item==1)
				{
					$qth.=$zap1.$key;
					$zap1=',';
				}
				if ($item==2)
				{
					$qsth.=$zap2.$key;
					$zap2=',';
				}
			}
			$qth=$db->query('SELECT order_id,order_name FROM blog_orders WHERE order_id IN ('.$qth.')');
			while ($th=$db->fetch($qth))
			{
				$outmas['themes'][$th['order_id']]['name']=$th['order_name'];
			}
			$qth=$db->query('SELECT subtheme_id,subtheme_name FROM blog_subthemes WHERE subtheme_id IN ('.$qsth.')');
			while ($th=$db->fetch($qth))
			{
				$outmas['sub_themes'][$th['subtheme_id']]['name']=$th['subtheme_name'];
			}
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
}
else
{
	$outmas['status']=1;
	echo json_encode($outmas);
	die();
}

?>