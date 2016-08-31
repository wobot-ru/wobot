<?
//Отправка на почту списка кабинетов в которых за не одобрены темы(демо кабинеты)
require_once('/var/www/daemon/com/config.php');
require_once('/var/www/daemon/com/func.php');
require_once('/var/www/daemon/com/db.php');
require_once('/var/www/daemon/bot/kernel.php');

error_reporting(E_ERROR);

//while (1)
{
	$db = new database();
	$db->connect();
	//echo 'SELECT a.order_start,a.order_id FROM blog_orders as a LEFT JOIN users as b ON a.user_id=b.user_id LEFT JOIN user_tariff as c ON b.user_id=c.user_id WHERE a.order_end>'.(time()-86400).' AND a.user_id!=0 AND c.ut_date>'.time();
	//die();
	$i=0;
	$orders=$db->query('SELECT a.order_start,a.order_id,a.order_keyword,c.user_email FROM blog_orders as a LEFT JOIN user_tariff as b ON a.ut_id=b.ut_id LEFT JOIN users as c ON b.user_id=c.user_id WHERE a.order_end>'.(time()-86400).' AND a.user_id=0 AND a.user_id!=145 AND b.ut_date>'.time());
	while ($order=$db->fetch($orders))
	{
		$tnt.=$order['order_id']." ".$order['order_keyword']." ".$order['user_email']."<br>";
		//$ords[$order['order_id']]['start']=$order['order_start'];
		//$ords[$order['order_id']]['kw']=$order['order_keyword'];
		//$ords[$order['order_id']]['user']=$order['user_email'];
	}

	$headers  = "From: noreply2@wobot.ru\r\n"; 
	$headers .= "Bcc: noreply2@wobot.ru\r\n";
	$headers .= 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=utf-8'."\r\n";
	mail('r@wobot.co','Неодобренные темы',$tnt,$headers);
	mail('zmei123@yandex.ru','Неодобренные темы',$tnt,$headers);
	mail('account-one@wobot-research.com','Неодобренные темы',$tnt,$headers);
	//mail('andreygbox@gmail.com','Пустые темы',$tnt,$headers);
	//sleep(10800);
}
?>
