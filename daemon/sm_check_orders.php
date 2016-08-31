<?
//Отправка на почту списка кабинетов в которых за последний день 0 сообщений
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
	$orders=$db->query('SELECT a.order_start,a.order_id,a.order_keyword,b.user_email FROM blog_orders as a LEFT JOIN users as b ON a.user_id=b.user_id LEFT JOIN user_tariff as c ON b.user_id=c.user_id WHERE a.order_end>'.(time()-86400).' AND a.user_id!=0 AND a.user_id!=145 AND c.ut_date>'.time());
	while ($order=$db->fetch($orders))
	{
		$ords[$order['order_id']]['start']=$order['order_start'];
		$ords[$order['order_id']]['kw']=$order['order_keyword'];
		$ords[$order['order_id']]['user']=$order['user_email'];
	}

	foreach ($ords as $key => $item)
	{
		$cc=$db->query('SELECT post_id as cnt FROM blog_post WHERE order_id='.$key.' AND post_time>'.((time()-1*86400)<$ords[$key]['start']?$ords[$key]['start']:(time()-1*86400)));
		echo 'SELECT count(post_id) as cnt FROM blog_post WHERE order_id='.$key.' AND post_time>'.((time()-1*86400)<$ords[$key]['start']?$ords[$key]['start']:(time()-1*86400))."\n";
		//$count=$db->fetch($cc);
		$ts=((time()-1*86400)<$ords[$key]['start']?$ords[$key]['start']:(time()-1*86400));
		$te=((time()-1*86400)<$ords[$key]['start']?$ords[$key]['start']:(time()-1*86400))+86400;
		$ybl_cont=parseUrl('http://blogs.yandex.ru/search.rss?text='.urlencode($ords[$key]['kw']).'&ft=all&from_day='.intval(date('d',$ts)).'&date=on&from_month='.date('m',$ts).'&from_year='.date('Y',$ts).'&to_day='.intval(date('d',$te)).'&to_month='.date('m',$te).'&to_year='.date('Y',$te).'&holdres=mark&p='.intval($i).'&numdoc=100&rd=0&spcctx=doc');
		//echo 'http://blogs.yandex.ru/search.rss?text='.urlencode($ords[$key]['kw']).'&ft=all&from_day='.intval(date('d',$ts)).'&date=on&from_month='.date('m',$ts).'&from_year='.date('Y',$ts).'&to_day='.intval(date('d',$te)).'&to_month='.date('m',$te).'&to_year='.date('Y',$te).'&holdres=mark&p='.intval($i).'&numdoc=100&rd=0&spcctx=doc';
		$regex='/<yablogs\:count>(?<ybl_count>\d+)<\/yablogs\:count>/isu';
		preg_match_all($regex, $ybl_cont, $out);
		//print_r($out);
		print_r($count);
		if (($db->num_rows($cc)==0) && ($out['ybl_count'][0]!=0))
		{
			$not_posts[$key]['kw']=$ords[$key]['kw'];
			$not_posts[$key]['user']=$ords[$key]['user'];
		}
		sleep(1);
	}
	print_r($not_posts);
	$tnt="Пустые темы:<br>";
	$i=1;
	foreach ($not_posts as $key => $item)
	{
		$tnt.=$i." ".$not_posts[$key]['user']." ".$key." ".$not_posts[$key]['kw']."<br>";
		$i++;
	}
	$headers  = "From: noreply2@wobot.ru\r\n"; 
	$headers .= "Bcc: noreply2@wobot.ru\r\n";
	$headers .= 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=utf-8'."\r\n";
	mail('r@wobot.co','Пустые темы',$tnt,$headers);
	mail('zmei123@yandex.ru','Пустые темы',$tnt,$headers);
	mail('account-one@wobot-research.com','Пустые темы',$tnt,$headers);
	//mail('andreygbox@gmail.com','Пустые темы',$tnt,$headers);
	//sleep(10800);
}
?>