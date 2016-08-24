<? 
 
//publish.php    
require_once('/var/www/daemon/com/config.php');
require_once('/var/www/daemon/com/db.php');

$db = new database();
$db->connect();

$fp = fopen('/var/www/pids/updater'.$_SERVER['argv'][1].'.pid', 'w');
fwrite($fp, getmypid());
fclose($fp);

ini_set('default_socket_timeout', -1);

$redis = new Redis();    
$redis->connect('127.0.0.1');
// $redis->pconnect();

function f($redis, $chan, $msg) {
	global $db;
	$db->query($msg);
	echo $msg."\n";
}

$redis->subscribe(array('update_query'), 'f'); // subscribe to 3 chans

?>