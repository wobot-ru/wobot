<?
if (mb_strlen($_GET['link'],'UTF-8')>0)
{
	$fp = fopen('/var/www/project/crawler/crawler.log', 'a');
	fwrite($fp, 'start: '.date('r')."\n");
	fclose($fp);

	$descriptorspec=array(
		0 => array("file","/dev/null","a"),
		1 => array("file","/dev/null","a"),
		2 => array("file","/dev/null","a")
		);

	$cwd='/var/www/bot/';
	$end=array();
	
	$process=proc_open('php /var/www/project/crawler/simply_addsrc.php '.$_GET['link'].' &',$descriptorspec,$pipes,$cwd,$end);/* or {
		echo json_encode(array('status'=>'fail'), true);
		die();
	};*/
	
	if (is_resource($process))
	{
		//echo 'return: '.$return_value=proc_close($process);
		if (intval($return_value=proc_close($process))==0) echo json_encode(array('status'=>'ok'), true);
	}
}
else echo json_encode(array('status'=>'fail'), true);
?>