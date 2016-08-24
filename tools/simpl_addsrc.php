<?
//if (intval($_GET['order_id'])>0)
{
	$_GET['src']=escapeshellarg($_GET['src']);
	$fp = fopen('/var/www/project/crawler/addsrc.log', 'a');
	fwrite($fp, 'start: '.date('r')."\n");
	fclose($fp);

	$descriptorspec=array(
		0 => array("file","/dev/null","a"),
		1 => array("file","/dev/null","a"),
		2 => array("file","/dev/null","a")
		);

	$cwd='/var/www/project/crawler/';
	$end=array();
	echo 'php /var/www/project/crawler/rss_search.php '.($_GET['src']).' &';
	$process=proc_open('php /var/www/project/crawler/rss_search.php '.($_GET['src']).' &',$descriptorspec,$pipes,$cwd,$end);/* or {
		echo json_encode(array('status'=>'fail'), true);
		die();
	};*/
	//echo 'php /var/www/project/crawler/rss_search.php '.base64_encode($_GET['src']).' &';
	if (is_resource($process))
	{
		//echo 'return: '.$return_value=proc_close($process);
		if (intval($return_value=proc_close($process))==0) echo json_encode(array('status'=>'ok'), true);
	}
}
//else echo json_encode(array('status'=>'fail'), true);
?>