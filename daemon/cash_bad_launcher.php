<?
require_once('/var/www/daemon/new/com/config.php');
require_once('/var/www/daemon/new/com/func.php');
require_once('/var/www/daemon/new/com/db.php');
require_once('/var/www/daemon/new/bot/kernel.php');

// register_shutdown_function('handleShutdown');

// function mailerror($to, $message)
// {
//     //$to      = 'nobody@example.com';

//     $subject = 'error message';
//     //$message = 'hello';
//     $headers  = "From: noreply@wobot.ru\r\n";
//     $headers .= "Bcc: noreply@wobot.ru\r\n";
//     $headers .= "MIME-Version: 1.0" . "\r\n";
//     $headers .= "Content-type: text/html; charset=utf-8"."\r\n";

//     mail($to, $subject, $message, $headers);
// }

// function handleShutdown() {
//     $error = error_get_last();
//     if($error !== NULL){
//         $info = "[SHUTDOWN] file:".$error['file']." | ln:".$error['line']." | msg:".$error['message'] .PHP_EOL;
//         //echo "!!!!123\n";
//         mailerror("zmei123@yandex.ru", $info);
//         //mailerror("nikanorov@wobot.co, for.uki@gmail.com", $info);
//         //yourPrintOrMailFunction($info);
//     }
//     else{
//         mailerror("zmei123@yandex.ru", "Упал ,cash_launcher shutdown");

//         //yourPrintOrMailFunction("SHUTDOWN");
//     }
// }


$db=new database();
$db->connect();

error_reporting(0);

$redis=new Redis() or die("Can'f load redis module.");
$redis->connect('127.0.0.1');

$memcache = memcache_connect('localhost', 11211);

$qorders=$db->query('SELECT * FROM blog_orders WHERE (user_id=2039 AND ut_id=2036) OR (user_id=2064 AND ut_id=2061) OR (user_id=2403 AND ut_id=2401) OR (user_id=3698 AND ut_id=3699)');
while ($order=$db->fetch($qorders))
{
	$_GET['order_id']=$order['order_id'];
	$_GET['start']=$order['order_start'];
	$_GET['end']=$order['order_end'];
	print_r($_GET);

	$fp = fopen('/var/www/bot/cashjob-spec.log', 'a');
	fwrite($fp, 'start: '.date('r')."\n");
	fclose($fp);

	$descriptorspec=array(
		0 => array("file","/dev/null","a"),
		1 => array("file","/dev/null","a"),
		2 => array("file","/dev/null","a")
		);

	$cwd='/var/www/bot/';
	$end=array();
	
	$process=proc_open('php /var/www/cashjob/cashjob_bad_spec.php '.intval($_GET['order_id']).' '.(($_GET['start']!='')&&($_GET['end']!='')?$_GET['start'].' '.$_GET['end']:'').' &',$descriptorspec,$pipes,$cwd,$end);/* or {
		echo json_encode(array('status'=>'fail'), true);
		die();
	};*/
	
	if (is_resource($process))
	{
		//echo 'return: '.$return_value=proc_close($process);
		if (intval($return_value=proc_close($process))==0) echo json_encode(array('status'=>'ok'), true);
	}
	echo 'launch '.$_GET['order_id'].' '.date('r'.$_GET['start']).' '.date('r',$_GET['end'])."\n";
	sleep(30);
}

/*echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
$qorder=$db->query('SELECT order_id,order_keyword,order_name FROM blog_orders');
while ($order=$db->fetch($qorder))
{
	echo '<a href="?order_id='.$order['order_id'].'">'.($order['order_name']==''?$order['order_keyword']:$order['order_name']).'</a><br>';
}*/

?>