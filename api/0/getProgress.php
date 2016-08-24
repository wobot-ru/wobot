<?php
$hash_code = $_POST['hash'];
require_once('/var/www/new/com/config.php');
require_once('/var/www/new/com/db.php');
require_once('auth.php');
auth();
if (!$loged){
	echo '{"error":"authentication_error"}';
	die();
}
$db = new database();
$db->connect();
$query_result=$db->query('SELECT progress FROM blog_export WHERE hash_code="'.$hash_code.'"');
$result = $db->fetch($query_result);
if(!$result){
	echo '{"error":"incorrect or missing hash"}';
	die();
}
if ($result['progress']==100) {
	$server_url = 'http://54.228.247.2:8080/';	
	$result_array['dl_link']=$server_url."dl/".$hash_code;
}else{
	$result_array['progress']=$result['progress'];
}
echo(json_encode($result_array));
?>