<?

require_once('/var/www/daemon/com/db.php');
require_once('/var/www/daemon/com/config.php');

$db=new database();
$db->connect();

$qtp=$db->query('SELECT b.order_id FROM blog_tp as a LEFT JOIN blog_orders as b ON a.order_id=b.order_id LEFT JOIN user_tariff as c ON b.user_id=c.user_id WHERE (tp_type=\'yandex_news\' OR tp_type=\'google_news\' OR tp_type=\'novoteka_news\') AND order_end>='.time().' AND b.user_id>0 AND b.ut_id>0 AND b.user_id!=145 AND c.ut_date>'.time().' GROUP BY a.order_id');
while ($tp=$db->fetch($qtp))
{
	//запуск yandex новостей
	$descriptorspec=array(
		0 => array("file","/dev/null","a"),
		1 => array("file","/dev/null","a"),
		2 => array("file","/dev/null","a")
		);

	$cwd='/var/www/bot/';
	$end=array();

	$process=proc_open('php /var/www/daemon/3rd_cs/yandex_news/yandex_news_job.php '.$tp['order_id'].' &',$descriptorspec,$pipes,$cwd,$end);
	
	if (is_resource($process))
	{
		//echo 'return: '.$return_value=proc_close($process);
		if (intval($return_value=proc_close($process))==0) echo json_encode(array('status'=>'ok'), true);
	}

	sleep(5);
	//запуск google новостей
	$descriptorspec=array(
		0 => array("file","/dev/null","a"),
		1 => array("file","/dev/null","a"),
		2 => array("file","/dev/null","a")
		);

	$cwd='/var/www/bot/';
	$end=array();

	$process=proc_open('php /var/www/daemon/3rd_cs/google_news/google_news_job.php '.$tp['order_id'].' &',$descriptorspec,$pipes,$cwd,$end);
	
	if (is_resource($process))
	{
		//echo 'return: '.$return_value=proc_close($process);
		if (intval($return_value=proc_close($process))==0) echo json_encode(array('status'=>'ok'), true);
	}

	sleep(5);
	//запуск novoteka новостей
	$descriptorspec=array(
		0 => array("file","/dev/null","a"),
		1 => array("file","/dev/null","a"),
		2 => array("file","/dev/null","a")
		);

	$cwd='/var/www/bot/';
	$end=array();

	$process=proc_open('php /var/www/daemon/3rd_cs/novoteka_news/novoteka_news_job.php '.$tp['order_id'].' &',$descriptorspec,$pipes,$cwd,$end);
	
	if (is_resource($process))
	{
		//echo 'return: '.$return_value=proc_close($process);
		if (intval($return_value=proc_close($process))==0) echo json_encode(array('status'=>'ok'), true);
	}

	sleep(30);
}

?>