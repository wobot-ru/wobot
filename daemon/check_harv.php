<?

require_once('/var/www/com/db.php');
require_once('/var/www/com/config.php');

error_reporting(E_ERROR);

date_default_timezone_set('Europe/Moscow');

function parseUrlmail($url,$to,$subj,$body,$from)
{
$message=str_replace('\\\'','\'',urldecode($body));
$headers = "From: ".$from."\r\nReply-To: ".$from."\r\n";
$headers .= "Content-Type: text/html; charset=utf-8";
$theme = 'Заявка с сайта '.date("d.m.Y");
$mailst =mail($to,$subj, stripslashes($message),$headers);
}


$db=new database();
$db->connect();

$redis=new Redis() or die("Can'f load redis module.");
$redis->connect('127.0.0.1');

while (1)
{
	if (!$db->ping()) {
		echo "MYSQL disconnected, reconnect after 10 sec...\n";
		sleep(10);
		$db->connect();
	}
	//echo 123;
	$var=$redis->get('check_harv');
	$yet=json_decode($var,true);
	// print_r($yet);
	// die();
	$qorder=$db->query('SELECT * FROM `blog_orders` AS a LEFT JOIN users AS b ON a.user_id=b.user_id LEFT JOIN user_tariff AS c ON b.user_id=c.user_id WHERE b.user_active=2 AND c.tariff_id=3 ORDER BY order_id ASC');
	while($order=$db->fetch($qorder)){
		if ($order['order_last']==0) continue;
		if ($order['third_sources']<=2) continue;
		if (($order['order_id']<$muser[$order['user_id']]['order_id']) || (!isset($muser[$order['order_id']])))
		{
			$muser[$order['user_id']]['order_id']=$order['order_id'];
			$muser[$order['user_id']]['order_name']=$order['order_name'];
			$muser[$order['user_id']]['user_email']=$order['user_email'];
			$muser[$order['user_id']]['user_company']=$order['user_company'];
			$muser[$order['user_id']]['user_contact']=$order['user_contact'];
			$muser[$order['user_id']]['order_date']=$order['order_date'];
			$muser[$order['user_id']]['user_id']=$order['user_id'];
			$muser[$order['user_id']]['user_name']=$order['user_name'];
			$muser[$order['user_id']]['user_pass']=$order['user_pass'];
		}
		// if($order['order_last']!=0 && $order['third_sources']>2 && !isset($yet[$order['order_id']])){
		// 	//отправка имейла
		// 	echo '.'.$order['order_id'];
		// 	$yet[$order['order_id']]=1;
		// 	$redis->set( 'check_harv', json_encode($yet));
		// }
	}
	//print_r($muser);
	foreach ($muser as $key => $item)
	{
		if (!isset($yet[$muser[$key]['order_id']]))
		{
			echo $muser[$key]['order_name'].' '.$muser[$key]['order_id'].' complite...'."\n";
			//отсылка
			$subj='Тема '.$muser[$key]['order_name'].' для '.$muser[$key]['user_email'].' собрана'."\n";
			 $text='<div>Тема '.$muser[$key]['order_name'].' '.$muser[$key]['order_id'].' для '.$muser[$key]['user_email'].' собрана.</div>
			 		<div>
			 		<form style="display: inline;" method="post" action="http://production.wobot.ru/" target="_blank">
                                    <input type="hidden" name="token" value="'.(md5(mb_strtolower($muser[$key]['user_email'],'UTF-8').':'.$muser[$key]['user_pass'])).'">
                                    <input type="hidden" name="user_id" value="'.$muser[$key]['user_id'].'">
                                    <input class="btn" type="submit" value="Войти в production">
                                    </form></div>
			 		<div> <a href="http:188.120.239.225/admin/?user_id='.$muser[$key]['user_id'].'#mod_user">Перейти к редактированию данных пользователя</a></div>
			 		<div>Контактные данные пользователя:</div>
			 		<div>Email: '.$muser[$key]['user_email'].'</div>
			 		<div>Тема мониторинга:'.$muser[$key]['order_name'].'</div>
			 		<div>Контактное лицо: '.$muser[$key]['user_name'].'</div>
			 		<div>Компания: '.$muser[$key]['user_company'].'</div>
			 		<div>Телефон: '.$muser[$key]['user_contact'].'</div>
			 		<div>Время создания темы: '.(date('d.m.Y',$muser[$key]['order_date'])).'</div>
			 		<br>
			 		<div>Робот Wobot</div>';
			$from='noreply-robot@wobot.ru';
			//echo "Тема: ".$subj;
			//echo $text;
			parseUrlmail('http://wobot.ru/mail_send.php',"axestal.post@gmail.com",$subj,$text,$from);
			parseUrlmail('http://wobot.ru/mail_send.php',"r@wobot.co",$subj,$text,$from);
			$yet[$muser[$key]['order_id']]=1;
			$redis->set( 'check_harv', json_encode($yet));
		}
	}
	//die();
	echo 'idle...'."\n";
	sleep(1);
}
//$var=$redis->get('order_'.$row['order_id'].'_'.$t);

//$redis->set(($mode=='s'?'sub':'').'order_'.($mode=='s'?$orderr['subtheme_id']:$orderr['order_id']).'_'.$stime, json_encode($beta_data));
?>