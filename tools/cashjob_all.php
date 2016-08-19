<?
require_once('/var/www/new/com/config.php');
require_once('/var/www/new/com/func.php');
require_once('/var/www/new/com/db.php');
require_once('/var/www/new/bot/kernel.php');

$db=new database();
$db->connect();

$redis=new Redis() or die("Can'f load redis module.");
$redis->connect('127.0.0.1');

if ($_GET['order_id']!='')
{
	$data['order_id']=$_GET['order_id'];
	$data['start']=$_GET['start'];
	$data['end']=$_GET['end'];
	print_r($data);
	$redis->rPush('cash_queue', json_encode($data));
	/*$fp = fopen('/var/www/bot/cashjob-spec.log', 'a');
	fwrite($fp, 'start: '.date('r')."\n");
	fclose($fp);

	$descriptorspec=array(
		0 => array("file","/dev/null","a"),
		1 => array("file","/dev/null","a"),
		2 => array("file","/dev/null","a")
		);

	$cwd='/var/www/bot/';
	$end=array();
	
	$process=proc_open('php /var/www/cashjob/cashjob.php '.intval($_GET['order_id']).' '.(($_GET['start']!='')&&($_GET['end']!='')?$_GET['start'].' '.$_GET['end']:'').' &',$descriptorspec,$pipes,$cwd,$end);/* or {
		echo json_encode(array('status'=>'fail'), true);
		die();
	};*/
	
	/*if (is_resource($process))
	{
		//echo 'return: '.$return_value=proc_close($process);
		if (intval($return_value=proc_close($process))==0) echo json_encode(array('status'=>'ok'), true);
	}*/
}

// echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
$qorder=$db->query('SELECT order_id,order_keyword,order_name FROM blog_orders WHERE order_end>'.mktime(0,0,0,1,1,2016).' ORDER BY order_id DESC');
while ($order=$db->fetch($qorder))
{
	file_get_contents('http://localhost/tools/cashjob.php?order_id='.$order['order_id']);
	echo '<a href="?order_id='.$order['order_id'].'">'.($order['order_name']==''?$order['order_keyword']:$order['order_name']).'</a><br>';
}

?>