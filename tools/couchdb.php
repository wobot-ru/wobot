<?
if (intval($_GET['order_id'])>0)
{
	$fp = fopen('/var/www/bot/couchdb.log', 'a');
	fwrite($fp, 'start: '.date('r')."\n");
	fclose($fp);

    $zip = new ZipArchive;
    $res = $zip->open('/home/wobot/import/'.intval($_GET['order_id']).'.zip');
    if ($res === TRUE) {
        $zip->extractTo('/home/wobot/import/');
        $zip->close();
        echo 'zip ok';
    } else {
        echo 'zip failed';
    }

	$descriptorspec=array(
		0 => array("file","/dev/null","a"),
		1 => array("file","/dev/null","a"),
		2 => array("file","/dev/null","a")
		);

	$cwd='/home/wobot/';
	$end=array();
	
	$process=proc_open('php /home/wobot/couchdb2.php '.intval($_GET['order_id']).' &',$descriptorspec,$pipes,$cwd,$end)/*; or {
		echo json_encode(array('status'=>'fail'), true);
		die();
	}*/;
	
	if (is_resource($process))
	{
		//echo 'return: '.$return_value=proc_close($process);
		if (intval($return_value=proc_close($process))==0) echo json_encode(array('status'=>'ok'), true);
	}

/*$handle = fopen("http://192.168.1.2/tools/cashjob.php?order_id=".intval($_GET['order_id']), "rb");
$contents = '';
while (!feof($handle)) {
  $contents .= fread($handle, 8192);
}
fclose($handle);
echo $contents;
*/
}
else echo json_encode(array('status'=>'fail'), true);
?>
