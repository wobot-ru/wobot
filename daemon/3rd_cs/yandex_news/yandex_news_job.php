<?

require_once('/var/www/daemon/com/db.php');
require_once('/var/www/daemon/com/config.php');
require_once('/var/www/daemon/bot/kernel.php');
require_once('get_yandex_news.php');

date_default_timezone_set('Europe/Moscow');

$db=new database();
$db->connect();

$redis=new Redis() or die("Can'f load redis module.");
$redis->connect('127.0.0.1');

// $token='9ecc28acb11d45029df59025d604d713';
$token='6930826f664245dca92b9811bdc6fbea';

$order_id=$_SERVER['argv'][1];
$start=$_SERVER['argv'][2];
$end=$_SERVER['argv'][3];

$qorder=$db->query('SELECT * FROM blog_orders WHERE order_id='.intval($order_id).' LIMIT 1');
$order=$db->fetch($qorder);
$qtariff=$db->query('SELECT * FROM user_tariff WHERE user_id='.$order['user_id']);
$tariff=$db->fetch($qtariff);
if (($start=='')||($end==''))
{
	$start=$order['order_start'];
	$end=$order['order_end']+86400;
}
if ($start<$order['order_start']) $start=$order['order_start'];
if ($end>time()) $end=time();

$qmd5=$db->query('SELECT * FROM blog_tp WHERE order_id='.$order_id.' AND tp_type=\'yandex_news\'');
while ($md5=$db->fetch($qmd5))
{
	$md5s[$md5['tp_id']]['last']=$md5['tp_last'];
	$md5s[$md5['tp_id']]['md5']=$md5['gr_id'];
}

if (count($md5s)==0) die();
if (($order['user_id']==145) || ($order['user_id']==0) || ($order['ut_id']==0)) die();
print_r($md5s);

foreach ($md5s as $key1 => $item1)
{
	$posts=get_news($item1['md5'],$token);
	print_r($posts);
	foreach ($posts['link'] as $key => $item)
	{
		if ($posts['time'][$key]>$end) continue;
		if ($posts['time'][$key]<$start) continue;
		// $qpost=$db->query('SELECT * FROM blog_post WHERE post_link=\''.$item.'\' AND order_id='.$order_id.' LIMIT 1');
		// if ($db->num_rows($qpost)==0)
		{
			$hn=parse_url($item);
			$hn=$hn['host'];
			$ahn=explode('.',$hn);
			$hn = $ahn[count($ahn)-2].'.'.$ahn[count($ahn)-1];
			$hh = $ahn[count($ahn)-2];
			// $db->query('INSERT INTO blog_post (order_id,post_link,post_time,post_content,post_engage) VALUES ('.$order_id.',\''.addslashes($item).'\','.$posts['time'][$key].',\''.addslashes($posts['content'][$key]).'\',0)');
			unset($queue_item);
			$queue_item['order_id']=$order['order_id'];
			$queue_item['post_link']=$item;
			$queue_item['post_host']=$hn;
			$queue_item['post_time']=$posts['time'][$key];
			$queue_item['post_content']=$posts['content'][$key];
			$queue_item['post_engage']=0;
			$queue_item['post_advengage']='';
			$queue_item['blog_id']=0;
			$queue_item['post_spam']=intval($isspam);
			$queue_item['post_ful_com']='';
			$queue_item['order_keyword']=$order['order_keyword'];
			$queue_item['order_name']=$order['order_name'];
			$queue_item['order_start']=$order['order_start'];
			$queue_item['order_end']=$order['order_end'];
			$queue_item['order_settings']=$order['order_settings'];
			$queue_item['user_id']=$order['user_id'];
			$queue_item['tariff_id']=$tariff['tariff_id'];
			$queue_item['ut_date']=$tariff['ut_date'];
			$queue_item['ut_id']=$tariff['ut_id'];
			print_r($queue_item);
			$redis->sAdd('prev_queue',json_encode($queue_item));
			// $db->query('INSERT INTO blog_post_prev (order_id,post_link,post_host,post_time,post_content,post_engage) VALUES ('.$order_id.',\''.addslashes($item).'\',\''.$hn.'\','.$posts['time'][$key].',\''.addslashes($posts['content'][$key]).'\',0)');
			// echo 'INSERT INTO blog_post_prev (order_id,post_link,post_time,post_content,post_engage) VALUES ('.$order_id.',\''.addslashes($item).'\','.$posts['time'][$key].',\''.addslashes($posts['content'][$key]).'\',0)'."\n";
		}
	}
	if (count($posts['time'])==0) $failed[]=$key1;
}

$headers  = "From: noreply2@wobot.ru\r\n"; 
$headers .= "Bcc: noreply2@wobot.ru\r\n";
$headers .= 'MIME-Version: 1.0' . "\r\n";
$headers .= 'Content-type: text/html; charset=utf-8'."\r\n";

if (date('H')==0) mail('zmei123@yandex.ru','yandex_news',$order_id.' '.count($posts['time']),$headers);

?>