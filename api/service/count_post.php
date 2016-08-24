<?

$redis=new Redis() or die("Can'f load redis module.");
$redis->connect('127.0.0.1');

$info=json_decode($redis->get('orders_'.$_GET['order_id']),true);
echo json_encode(array('count'=>$info['count_post']));

?>