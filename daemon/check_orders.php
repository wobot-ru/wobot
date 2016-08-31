<?
//Авто пересбор запросов с нулевыми последними 2мя днями
require_once('/var/www/daemon/com/config.php');
require_once('/var/www/daemon/com/func.php');
require_once('/var/www/daemon/com/db.php');
require_once('/var/www/daemon/bot/kernel.php');

error_reporting(E_ERROR);

$order_delta=$_SERVER['argv'][1];
sleep($order_delta);
$fp = fopen('/var/www/pids/checkorders'.$order_delta.'.pid', 'w');
fwrite($fp, getmypid());
fclose($fp);

$db = new database();
$db->connect();
while (1)
{
	sleep(10800);
	if (!$db->ping()) {
		echo "MYSQL disconnected, reconnect after 10 sec...\n";
		sleep(10);
		$db->connect();
	}
	//echo 'SELECT a.order_start,a.order_id FROM blog_orders as a LEFT JOIN users as b ON a.user_id=b.user_id LEFT JOIN user_tariff as c ON b.user_id=c.user_id WHERE a.order_end>'.(time()-86400).' AND a.user_id!=0 AND c.ut_date>'.time();
	//die();
	$i=0;
	$orders=$db->query('SELECT a.order_start,a.order_id,a.order_keyword FROM blog_orders as a LEFT JOIN users as b ON a.user_id=b.user_id LEFT JOIN user_tariff as c ON b.user_id=c.user_id WHERE a.order_end>'.(time()-86400).' AND a.third_sources!=0 AND a.user_id!=145 AND a.user_id!=0 AND c.ut_date>'.time());
	while ($order=$db->fetch($orders))
	{
		$ords[$order['order_id']]['order_start']=$order['order_start'];
		$ords[$order['order_id']]['order_keyword']=$order['order_keyword'];
	}

	$tm='';
	foreach ($ords as $key => $item)
	{
		$cc=$db->query('SELECT count(post_id) as cnt FROM blog_post WHERE order_id='.$key.' AND post_time>'.((time()-2*86400)<$ords[$key]['order_start']?$ords[$key]['order_start']:(time()-2*86400)));
		$count=$db->fetch($cc);

		$ts=((time()-2*86400)<$ords[$key]['order_start']?$ords[$key]['order_start']:(time()-2*86400));
		$te=((time()-2*86400)<$ords[$key]['order_start']?$ords[$key]['order_start']:(time()-2*86400))+86400*2;
		$i=0;
		$ybl_cont=parseUrl('http://blogs.yandex.ru/search.rss?text='.urlencode($ords[$key]['order_keyword']).'&ft=all&from_day='.intval(date('d',$ts)).'&date=on&from_month='.date('m',$ts).'&from_year='.date('Y',$ts).'&to_day='.intval(date('d',$te)).'&to_month='.date('m',$te).'&to_year='.date('Y',$te).'&holdres=mark&p='.intval($i).'&numdoc=100&rd=0&spcctx=doc');
		//echo 'http://blogs.yandex.ru/search.rss?text='.urlencode($ords[$key]['kw']).'&ft=all&from_day='.intval(date('d',$ts)).'&date=on&from_month='.date('m',$ts).'&from_year='.date('Y',$ts).'&to_day='.intval(date('d',$te)).'&to_month='.date('m',$te).'&to_year='.date('Y',$te).'&holdres=mark&p='.intval($i).'&numdoc=100&rd=0&spcctx=doc';
		$regex='/<yablogs\:count>(?<ybl_count>\d+)<\/yablogs\:count>/isu';
		preg_match_all($regex, $ybl_cont, $out);
		print_r($count);
		// print_r($out);
		if (intval($out['ybl_count'][0])<=10) 
		{
			echo 'continue...'."\n";
			continue;
		}

		//print_r($count);
		echo "\n".'perc='.$count['cnt'].' '.$out['ybl_count'][0].' '.(($count['cnt']/$out['ybl_count'][0])*100)."\n";
		if (($count['cnt']==0)||(($count['cnt']/$out['ybl_count'][0])*100<20))
		{
			$tm.='<br>'.$key.' '.$count['cnt'].' '.$out['ybl_count'][0].' '.($count['cnt']/$out['ybl_count'][0])*100;
			echo $key."\n";
			echo 'UPDATE blog_orders SET third_sources=2 WHERE order_id='.$key."\n";
			$db->query('UPDATE blog_orders SET third_sources=2 WHERE order_id='.$key);
		}
		sleep(5);
	}
	if ($tm!='')
	{
		$headers  = "From: noreply2@wobot.ru\r\n"; 
		$headers .= "Bcc: noreply2@wobot.ru\r\n";
		$headers .= 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=utf-8'."\r\n";
		mail('zmei123@yandex.ru','Перезапуск',$tm,$headers);
	}
}
?>
