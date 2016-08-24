<?
$descriptorspec=array(
	0 => array("file","/dev/null","a"),
	1 => array("file","/dev/null","a"),
	2 => array("file","/dev/null","a")
	);

$cwd='/var/www/bot/';
$end=array();

$process=proc_open('php /var/www/daemon/sync.php '.intval($_POST['order_id']).' &',$descriptorspec,$pipes,$cwd,$end);

if (is_resource($process))
{
	//echo 'return: '.$return_value=proc_close($process);
	if (intval($return_value=proc_close($process))==0) echo json_encode(array('status'=>'ok'), true);
	else echo json_encode(array('status'=>'fail'));
}

?>