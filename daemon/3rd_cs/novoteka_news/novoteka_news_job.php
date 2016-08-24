<?

require_once('/var/www/daemon/com/db.php');
require_once('/var/www/daemon/com/config.php');
require_once('get_novoteka.php');

date_default_timezone_set('Europe/Moscow');

$db=new database();
$db->connect();

$redis=new Redis() or die("Can'f load redis module.");
$redis->connect('127.0.0.1');

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
	$end=$order['order_end'];
}
if ($start<$order['order_start']) $start=$order['order_start'];
if ($end>time()) $end=time();

$qmd5=$db->query('SELECT * FROM blog_tp WHERE order_id='.$order_id.' AND tp_type=\'novoteka_news\'');
while ($md5=$db->fetch($qmd5))
{
	$md5s[$md5['tp_id']]['last']=$md5['tp_last'];
	$md5s[$md5['tp_id']]['query']=$md5['gr_id'];
}

if (count($md5s)==0) die();
if (($order['user_id']==145) || ($order['user_id']==0) || ($order['ut_id']==0)) die();
print_r($md5s);

foreach ($md5s as $key => $item)
{
	$posts=get_novoteka($item['query'],$start,$end);
	print_r($posts);
	foreach ($posts['link'] as $key1 => $item1)
	{
		if ($posts['time'][$key1]>$order['order_end']+86400) continue;
		if ($posts['time'][$key1]<$order['order_start']) continue;
		// $qpost=$db->query('SELECT * FROM blog_post WHERE post_link=\''.$item1.'\' AND order_id='.$order_id.' LIMIT 1');
		// if ($db->num_rows($qpost)==0)
		{
			echo $key1.' ';
			$hn=parse_url($item1);
		    $hn=$hn['host'];
		    $ahn=explode('.',$hn);
		    $hn = $ahn[count($ahn)-2].'.'.$ahn[count($ahn)-1];
			$hh = $ahn[count($ahn)-2];
			unset($queue_item);
			$queue_item['order_id']=$order['order_id'];
			$queue_item['post_link']=$item1;
			$queue_item['post_host']=$hn;
			$queue_item['post_time']=$posts['time'][$key1];
			$queue_item['post_content']=$posts['content'][$key1];
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
			// $db->query('INSERT INTO blog_post (order_id,post_link,post_time,post_content,post_engage) VALUES ('.$order_id.',\''.addslashes($item).'\','.$posts['time'][$key].',\''.addslashes($posts['content'][$key]).'\',0)');
			// $db->query('INSERT INTO blog_post_prev (order_id,post_link,post_host,post_time,post_content,post_engage) VALUES ('.$order_id.',\''.addslashes($item1).'\',\''.$hn.'\','.$posts['time'][$key1].',\''.addslashes($posts['content'][$key1]).'\',0)');
			// echo 'INSERT INTO blog_post_prev (order_id,post_link,post_host,post_time,post_content,post_engage) VALUES ('.$order_id.',\''.addslashes($item).'\',\''.$hn.'\','.$posts['time'][$key].',\''.addslashes($posts['content'][$key]).'\',0)'."\n";
		}
	}
	if (count($posts['time'])==0) $failed[]=$key;
	echo 'UPDATE blog_tp SET tp_last='.time().' WHERE tp_id='.$key."\n";
	$db->query('UPDATE blog_tp SET tp_last='.time().' WHERE tp_id='.$key);
}

$headers  = "From: noreply2@wobot.ru\r\n"; 
$headers .= "Bcc: noreply2@wobot.ru\r\n";
$headers .= 'MIME-Version: 1.0' . "\r\n";
$headers .= 'Content-type: text/html; charset=utf-8'."\r\n";

if (date('H')==0) mail('zmei123@yandex.ru','novoteka_news',$order_id.' '.count($posts['time']),$headers);

?>