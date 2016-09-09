<?php
$order_id = $_POST['order_id'];
require_once('/var/www/new/com/config.php');
require_once('/var/www/new/com/db.php');
require_once('auth.php');
auth();
if (!$loged){
	echo '{"error":"authentication_error"}';
	die(); // if not loged in , die
}
if (!isset($_POST['order_id'])){
	echo '{"error":"missing the order_id parameter"}';
	die(); // if not loged in , die
}
$db = new database();
$db->connect();
$res=$db->fetch($db->query("SELECT * from blog_orders WHERE order_id=".intval($order_id)." and user_id=".intval($user['user_id'])." LIMIT 1"));
if(!$res){
	echo '{"error":"this order_id doesn\'t belong to this user"}';
	die();
} // if order_id not belongs to current user
$serv_url = 'http://188.120.239.225/project/xlsxexportpy/data/';//'http://54.228.247.2:8080/';
$query_result=$db->query('SELECT * FROM blog_export WHERE order_id='.$order_id.' order by export_time DESC LIMIT 5');
while ($result = $db->fetch($query_result))
{
	$tresult = array(export_time => $result['export_time'],
					 start_time  => $result['start_time'],
					 end_time    => $result['end_time']);
	if($result['progress']==100){
		$tresult['dl_link']=$serv_url.$result['hash_code'];
	}else{
		$tresult['hash_code'] = $result['hash_code'];
		$tresult['progress'] = $result['progress'];
	}
	$results[]=$tresult;
}
if(!isset($results)){
	echo '{"error":"this order_id doesn\'t have any exports"}';
}
else{
	echo(json_encode($results));
}
?>