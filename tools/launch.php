<?
require_once('/var/www/new/com/config.php');
require_once('/var/www/new/com/func.php');
require_once('/var/www/new/com/db.php');
require_once('/var/www/new/bot/kernel.php');

$db=new database();
$db->connect();

$redis=new Redis() or die("Can'f load redis module.");
$redis->connect('127.0.0.1');

$qprev=$db->query('SELECT id FROM tp_proxys WHERE valid=1');
$count_proxy=$db->num_rows($qprev);
$avcount['stop']=1;
$avcount[20]=1;
$avcount[50]=1;
if ($count_proxy>150) $avcount[100]=1;
if ($count_proxy>250) $avcount[200]=1;

$ass_launch['eng_job']='et';
$ass_launch['multi_ft']='fulljob';
$ass_launch['re_transf']='transf';
$ass_launch['tp_job3']='fs';
$ass_launch['multi_uj']='uj';

if (($_GET['act']=='launch') && (isset($avcount[$_GET['count']])))
{
	$fp = fopen('/var/www/bot/dashboard-spec.log', 'a');
	fwrite($fp, 'start: '.date('r').' '.json_encode($_GET)."\n");
	fclose($fp);

	$descriptorspec=array(
		0 => array("file","/dev/null","a"),
		1 => array("file","/dev/null","a"),
		2 => array("file","/dev/null","a")
		);

	$cwd='/var/www/bot/';
	$end=array();

	$process=proc_open('php /var/www/daemon/launcher.php '.$ass_launch[$_GET['name']].' '.($_GET['count']=='stop'?'':'start').' '.$_GET['count'].' &',$descriptorspec,$pipes,$cwd,$end);/* or {
		echo json_encode(array('status'=>'fail'), true);
		die();
	};*/
	if (is_resource($process))
	{
		//echo 'return: '.$return_value=proc_close($process);
		if (intval($return_value=proc_close($process))==0) echo json_encode(array('status'=>'ok'), true);
	}
	//sleep(3);
}

/*echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
$qorder=$db->query('SELECT order_id,order_keyword,order_name FROM blog_orders');
while ($order=$db->fetch($qorder))
{
	echo '<a href="?order_id='.$order['order_id'].'">'.($order['order_name']==''?$order['order_keyword']:$order['order_name']).'</a><br>';
}*/

?>