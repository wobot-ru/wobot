<?php
error_reporting(0);
require_once('/var/www/new/com/config.php');
require_once('/var/www/new/com/db.php');
require_once('auth.php');
date_default_timezone_set ( 'Europe/Moscow' );
function put_in_redis($key,$data){
	$redis=new Redis() or die("Can'f load redis module.");
	$redis->connect('127.0.0.1');
	$redis->set("export_".$key,$data);
}
header('Content-type: text/json');


if(isset($_POST['order_id']) && isset($_POST['start']) && isset($_POST['end']))
{
	$db = new database();
	$db->connect();
	auth();
	if (!$loged){
		echo '{"error":"authentication_error"}';
		die();
	}
	$_GET['test_user_id']=$user['user_id'];
	$_GET['test_token']=md5(mb_strtolower($user['user_email'],'UTF-8').':'.$user['user_pass']);
	$res=$db->fetch($db->query("SELECT * from blog_orders WHERE order_id=".intval($_POST['order_id'])." and user_id=".intval($user['user_id'])." LIMIT 1"));
	if(!$res){
		echo '{"error":"this order_id doesn\'t belong to this user"}';
		die();
	} // if order_id not belongs to current user
	$post_count=$db->fetch($db->query("select count(*) from blog_post where order_id =".intval($_POST['order_id'])));
	if(intval($post_count['count(*)'])==0){
		echo '{"error":"this order_id doesn\'t have any posts"}';
		die();
	}
	$hash = md5($_POST['order_id'].':'.$_POST['start'].':'.$_POST['end'].':'.$_GET['test_user_id'].':'.$_GET['test_token'].':'.time());
	echo '{"hash_code":"'.$hash.'"}';
	put_in_redis($hash,json_encode(array("GET" => $_GET,"POST" => $_POST)));
	$cmd = 'php export3_new.php '.$hash;
	// echo $cmd.' > /dev/null 2>/dev/null &';
	shell_exec($cmd.' > /dev/null 2>/dev/null &');
}else{	
	echo '{"error":"not enough parameters"}';
}
?>