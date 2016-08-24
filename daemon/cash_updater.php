<?
	require_once('/var/www/new/com/config.php');
	//require_once('/var/www/new/com/func.php');
	require_once('/var/www/new/com/db.php');
	//require_once('/var/www/new/bot/kernel.php');
	date_default_timezone_set('Europe/Moscow');

	error_reporting(0);

	$order_delta=$_SERVER['argv'][1];
	sleep($order_delta);
	$fp = fopen('/var/www/pids/cu'.$order_delta.'.pid', 'w');
	fwrite($fp, getmypid());
	fclose($fp);
	
	$db = new database();
	$db->connect();

	$days_back = 3;

	while(1){
		
		$today = mktime(0,0,0,date('n'),date('j'),date('Y'));
		$period = mktime(0,0,0,date('n'),date('j')-$days_back,date('Y'));
		$period_day = mktime(0,0,0,date('n'),date('j')-1,date('Y'));
		$now = mktime(date('H'),date('i'),date('s'),date('n'),date('j'),date('Y'));

		$new_themes = $db->query('SELECT * FROM blog_orders AS a LEFT JOIN user_tariff AS b ON a.user_id=b.user_id WHERE a.user_id!=145 AND b.ut_date>'.$today.' AND order_end>'.$today);

		while($res_new = $db->fetch($new_themes)){
			$order_id = $res_new['order_id'];
			$order_start = $res_new['order_start'];
			$order_end = $res_new['order_end'];
			$order_date = $res_new['order_date'];

			$now = mktime(date('H'),date('i'),date('s'),date('n'),date('j'),date('Y'));

			$descriptorspec=array(
				0 => array("file","/dev/null","a"),
				1 => array("file","/dev/null","a"),
				2 => array("file","/dev/null","a")
				);

			$cwd='/var/www/bot/';
			$end=array();

			if($order_date>$period){
				$from = $order_start;
			} else {
				$from = $period;
			}

		    $process=proc_open('php /var/www/cashjob/cashjob_spec.php '.$order_id.' '.$from.' '.$now ,$descriptorspec,$pipes,$cwd,$end);

			do{
				$status = proc_get_status($process);
				sleep(1);
			} while($status['running']);

			proc_close($process);

			echo "Realtime orders ".$order_id." cash finished, waiting for next... ".date('H:i:s')."\n";

		}


		$retro_theme = $db->query('SELECT * FROM blog_orders AS a LEFT JOIN user_tariff AS b ON a.user_id=b.user_id WHERE a.user_id!=145 AND b.ut_date>'.$today.' AND order_date>'.$period.' AND order_end<'.$now);

		while($res_retro = $db->fetch($retro_theme)){
			$order_id = $res_retro['order_id'];
			$order_start = $res_retro['order_start'];
			$order_end = $res_retro['order_end'];

			$descriptorspec=array(
				0 => array("file","/dev/null","a"),
				1 => array("file","/dev/null","a"),
				2 => array("file","/dev/null","a")
				);

			$cwd='/var/www/bot/';
			$end=array();

		    $process=proc_open('php /var/www/cashjob/cashjob_spec.php '.$order_id.' '.$order_start.' '.$order_end ,$descriptorspec,$pipes,$cwd,$end);

			do{
				$status = proc_get_status($process);
				sleep(1);
			} while($status['running']);

			proc_close($process);

			echo "Retrospective orders ".$order_id." cash finished, waiting for next... ".date('H:i:s')."\n";
		}

		echo "Cash complete, starting new cуcle...\n";
		//die();
		sleep(1);
	}


?>