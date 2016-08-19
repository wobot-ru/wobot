<?
require_once('/var/www/new/com/config.php');
require_once('/var/www/new/com/func.php');
require_once('/var/www/new/com/db.php');
require_once('/var/www/new/bot/kernel.php');

$db=new database();
$db->connect();

if ($_GET['tp_id']!='')
{
	$fp = fopen('/var/www/bot/3rd_cs.log', 'a');
	fwrite($fp, 'start: '.date('r')."\n");
	fclose($fp);

	$descriptorspec=array(
		0 => array("file","/dev/null","a"),
		1 => array("file","/dev/null","a"),
		2 => array("file","/dev/null","a")
		);

	$cwd='/var/www/bot/';
	$end=array();
	
	$process=proc_open('php /var/www/daemon/3rd_cs/tp_job_cs_trg.php '.intval($_GET['tp_id']).' &',$descriptorspec,$pipes,$cwd,$end);	

	if (is_resource($process))
	{
		//echo 'return: '.$return_value=proc_close($process);
		if (intval($return_value=proc_close($process))==0) echo json_encode(array('status'=>'ok'), true);
	}
}

/*echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
$qorder=$db->query('SELECT order_id,order_keyword,order_name FROM blog_orders');
while ($order=$db->fetch($qorder))
{
	echo '<a href="?order_id='.$order['order_id'].'">'.($order['order_name']==''?$order['order_keyword']:$order['order_name']).'</a><br>';
}*/

?>