<?
require_once('/var/www/new/com/config.php');
require_once('/var/www/new/com/func.php');
require_once('/var/www/new/com/db.php');
require_once('/var/www/new/bot/kernel.php');

$fp = fopen('/var/www/pids/cash'.$order_delta.'.pid', 'w');
fwrite($fp, getmypid());
fclose($fp);

register_shutdown_function('handleShutdown');

function mailerror($to, $message)
{
    //$to      = 'nobody@example.com';

    $subject = 'error message';
    //$message = 'hello';
    $headers  = "From: noreply@wobot.ru\r\n";
    $headers .= "Bcc: noreply@wobot.ru\r\n";
    $headers .= "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type: text/html; charset=utf-8"."\r\n";

    mail($to, $subject, $message, $headers);
}

function handleShutdown() {
    $error = error_get_last();
    if($error !== NULL){
        $info = "[SHUTDOWN] file:".$error['file']." | ln:".$error['line']." | msg:".$error['message'] .PHP_EOL;
        //echo "!!!!123\n";
        mailerror("zmei123@yandex.ru", $info);
        //mailerror("nikanorov@wobot.co, for.uki@gmail.com", $info);
        //yourPrintOrMailFunction($info);
    }
    else{
        mailerror("zmei123@yandex.ru", "Упал ,cash_launcher shutdown");

        //yourPrintOrMailFunction("SHUTDOWN");
    }
}


$db=new database();
$db->connect();

error_reporting(0);

$redis=new Redis() or die("Can'f load redis module.");
$redis->connect('127.0.0.1');

$memcache = memcache_connect('localhost', 11211);

while (1)
{
	$count_cash=exec('ps ax | grep cashjob_spec.php | wc -l');
	if ($count_cash>20)
	{
		echo '.';
		sleep(1);
		continue;
	}
	for ($i=0;$i<10;$i++)
	{
		$data=$redis->lPop('cash_queue');
		if ($data=='') {
			sleep(1);
		} else {
			echo "data";
			print_r($data);
		}
		$_GET=json_decode($data,true);
		if ($_GET['order_id']=='') 
		{
			echo '|';
			// sleep(1);
			continue;
		}
		/*$output=$memcache->get('cash_launch_'.$_GET['order_id'].'_'.$_GET['start'].'_'.$_GET['end']);
		echo ' oustput: ';
		print_r($output);
		if (is_numeric($output)) 
		{
			echo '*';
			$redis->rPush('cash_queue', $data);
			continue;
		}*/
		print_r($_GET);
		if ((intval($_GET['start'])!=0) && ($_GET['start']<1000000)) continue;

		// $fp = fopen('/var/www/bot/cashjob-spec.log', 'a');
		// fwrite($fp, 'start: '.date('r')."\n");
		// fclose($fp);

		$descriptorspec=array(
			0 => array("file","/dev/null","a"),
			1 => array("file","/dev/null","a"),
			2 => array("file","/dev/null","a")
			);

		$cwd='/var/www/bot/';
		$end=array();
		
		$process=proc_open('php /var/www/cashjob/cashjob.php '.intval($_GET['order_id']).' '.(($_GET['start']!='')&&($_GET['end']!='')?$_GET['start'].' '.$_GET['end']:'').' &',$descriptorspec,$pipes,$cwd,$end);/* or {
			echo json_encode(array('status'=>'fail'), true);
			die();
		};*/
		
		if (is_resource($process))
		{
			//echo 'return: '.$return_value=proc_close($process);
			if (intval($return_value=proc_close($process))==0) echo json_encode(array('status'=>'ok'), true);
		}
		echo 'launch '.$_GET['order_id'].' '.date('r'.$_GET['start']).' '.date('r',$_GET['end'])."\n";
		$memcache->set('cash_launch_'.$_GET['order_id'].'_'.$_GET['start'].'_'.$_GET['end'], 1, MEMCACHE_COMPRESSED, 60);
	}
	usleep(500000);
}

/*echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
$qorder=$db->query('SELECT order_id,order_keyword,order_name FROM blog_orders');
while ($order=$db->fetch($qorder))
{
	echo '<a href="?order_id='.$order['order_id'].'">'.($order['order_name']==''?$order['order_keyword']:$order['order_name']).'</a><br>';
}*/

?>