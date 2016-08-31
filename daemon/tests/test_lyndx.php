<?

require_once('/var/www/com/db.php');
require_once('/var/www/com/config.php');

$db=new database();
$db->connect();

$mfiles=scandir('/var/www/stroik/lyndx/data');
if ($_SERVER['argv'][1]=='') $qorder=$db->query('SELECT * FROM blog_orders WHERE order_date>='.mktime(0,0,0,date('n'),date('j')-1,date('Y')));
else $qorder=$db->query('SELECT * FROM blog_orders WHERE order_id='.intval($_SERVER['argv'][1]).' LIMIT 1');
while ($order=$db->fetch($qorder))
{
	$count_fail=0;
	$count_success=0;
	// print_r($order);
	foreach ($mfiles as $file)
	{
		// echo $file."\n";
		if (!preg_match('/'.$order['order_id'].'\-[\d]+/isu', $file)) continue;
		// echo 'gg';
		$filename = '/var/www/stroik/lyndx/data/'.$file;
		// echo $filename;
		$handle = fopen($filename, "r");
		$contents = fread($handle, filesize($filename));
		fclose($handle);
		// echo $contents;
		if (preg_match('/<div class=\"b\-captcha\">/isu',$contents)) $count_fail++;
		else $count_success++;
		// if (preg_match('/<div class=\"b\-captcha\">/isu',$contents) && !in_array($order['order_id'], $fail_order)) $fail_order[]=$order['order_id'];
	}
	if (($count_fail!=0) && !in_array($order['order_id'], $fail_order))
	{
		$fail_order[]=$order['order_id'].' succ: '.$count_success.' fail: '.$count_fail;
	}
	echo $count_fail.' '.$count_success.' '.$order['order_id']."\n";
}

if (count($fail_order)!=0) mail('zmei123@yandex.ru','fail_captcha_lyndx',json_encode($fail_order),'From: noreply2@wobot.ru');

?>