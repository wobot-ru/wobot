<?

$redis = new Redis();    
$redis->connect('127.0.0.1');

echo 'PREV: '.$redis->scard('prev_queue').' TRANSF: '.$redis->scard('transf_queue');

?>