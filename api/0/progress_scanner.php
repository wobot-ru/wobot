<?php
error_reporting(0);
require_once('/var/www/new/com/config.php');
require_once('/var/www/new/com/db.php');
$db = new database();
$db->connect();

function getProgress($hash){
	//$my_url = 'http://ec2-54-228-37-208.eu-west-1.compute.amazonaws.com/';
	$my_url = 'http://188.120.239.225/upload/';//'http://54.228.247.2/';
	$ch = curl_init($my_url.$hash);
	curl_setopt($ch, CURLOPT_PORT, 8080);	
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  
	curl_setopt($ch, CURLOPT_HEADER, 0);          
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);  
	curl_setopt($ch, CURLOPT_ENCODING, "");       
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 300);
	curl_setopt($ch, CURLOPT_TIMEOUT, 120);       
	curl_setopt($ch, CURLOPT_MAXREDIRS, 10); 
	$result = curl_exec($ch);
	$json_result = json_decode($result,true);
	curl_close($handle);
	if($json_result['error']){
		return -1;
	}
	return intval($json_result['progress']);
}

$hash=$argv[1];
$sleeptime = 5;
$progress = 0;
$r1 = $db->fetch($db->query('SELECT export_id FROM blog_export WHERE hash_code="'.$hash.'"'));
$export_id = $r1['export_id'];
while($progress!=100){
	$db->query('UPDATE blog_export SET progress=100 WHERE export_id='.$export_id);
	die();
	sleep($sleeptime);
	$counter++;
	$oldprogress = $progress;
	$progress = getProgress($hash);
	echo '!'.$progress.'!';
	if($progress-$oldprogress>0)
		$db->query('UPDATE blog_export SET progress='.$progress.' WHERE export_id='.$export_id);
	if($progress==-1){
		$db->query('UPDATE blog_export SET progress='.$progress.' WHERE export_id='.$export_id);
		die();
	}
	if($counter>40) $sleeptime = 20;
	if($counter>70) $sleeptime = 60;
	if($counter>150) {
		//$db->query('UPDATE blog_export SET progress='.$progress.' WHERE export_id='.$export_id);
		die();
	}
}
?>