<?

$redis=new Redis() or die("Can'f load redis module.");
$redis->connect('127.0.0.1');

// print_r(json_decode(base64_decode($_POST['data']),true));

$redis->sAdd('engine_queue' , base64_decode($_POST['data']));
// $fp = fopen('/var/www/api/service/reciver.php', 'w');
// fwrite($fp, json_encode($_POST));
// fclose($fp);

?>