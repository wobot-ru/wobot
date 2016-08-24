<?
if ($_GET['status']=='webrole')
{
	$fp = fopen('datastart_azure.txt', 'a');
	fwrite($fp, 'start: '.date('r')."\n");
	fclose($fp);
	echo json_encode(array('status'=>'ok'), true);
	$descriptorspec=array(
		0 => array("file","/var/www/azurejob/gg.log","a"),
		1 => array("file","/var/www/azurejob/gg.log","a"),
		2 => array("file","/var/www/azurejob/gg.log","a")
		);

	$cwd='/var/www/azurejob';
	$end=array();
	
	$process=proc_open('php /var/www/azurejob/azurejob.php &',$descriptorspec,$pipes,$cwd,$end);
	
	if (is_resource($process))
	{
		$return_value=proc_close($process);
		//echo $return_value;
	}
}
if ($_GET['status']=='test')
{
	$fp = fopen('datastart_azure.txt', 'a');
	fwrite($fp, 'start: '.date('r')."\n");
	fclose($fp);
	echo json_encode(array('status'=>'ok'), true);
	$descriptorspec=array(
		0 => array("file","/var/www/azurejob/gg.log","a"),
		1 => array("file","/var/www/azurejob/gg.log","a"),
		2 => array("file","/var/www/azurejob/gg.log","a")
		);

	$cwd='/var/www/azurejob';
	$end=array();
	
	$process=proc_open('php /var/www/azurejob/azurejob-test.php &',$descriptorspec,$pipes,$cwd,$end);
	
	if (is_resource($process))
	{
		$return_value=proc_close($process);
		//echo $return_value;
	}
}
?>