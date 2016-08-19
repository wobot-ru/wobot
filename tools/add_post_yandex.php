<?

require_once('/var/www/com/db.php');
require_once('/var/www/com/config.php');
require_once('/var/www/daemon/com/users.php');
require_once('/var/www/daemon/bot/kernel.php');

date_default_timezone_set('Europe/Moscow');

$db=new database();
$db->connect();

$redis=new Redis() or die("Can'f load redis module.");
$redis->connect('127.0.0.1');

$eng_sources['twitter.com']=1;
$eng_sources['livejournal.com']=1;
$eng_sources['facebook.com']=1;
$eng_sources['vk.com']=1;
$eng_sources['vkontakte.ru']=1;

echo '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">';
$proxys=json_decode($redis->get('proxy_list'),true);

function chech_yandex_content($cont)
{
	return intval(preg_match('/\<rss xmlns\:yablogs\=\"urn\:yandex\-blogs\" xmlns\:wfw\=\"http\:\/\/wellformedweb\.org\/CommentAPI\/" version\=\"2\.0\">/isu',$cont));
}

if ($_SERVER['argv'][1]=='') $qorder=$db->query('SELECT * FROM blog_orders WHERE order_end>'.time().' AND user_id!=0 AND ut_id!=0 AND user_id!=145');//order_id='.$_SERVER['argv'][1]);
else $qorder=$db->query('SELECT * FROM blog_orders WHERE order_id='.$_SERVER['argv'][1]);
while ($order=$db->fetch($qorder))
{
	unset($mpost);
	$ts=mktime(0,0,0,date('n'),date('j')-10,date('Y'));
	$te=mktime(0,0,0,date('n'),date('j'),date('Y'));
	$_POST['orid']=$order['order_id'];
	$_GET['uid']=$order['user_id'];
	$text=$order['order_keyword'];
	echo $_POST['orid'].' '.$text."\n";
	for ($i=0;$i<10;$i++)
	{
		do
		{
			$cont=parseURLproxy_yandex('https://blogs.yandex.ru/search.rss?text='.urlencode($text).'&ft=all&from_day='.intval(date('d',$ts)).'&date=on&from_month='.date('m',$ts).'&from_year='.date('Y',$ts).'&to_day='.intval(date('d',$te)).'&to_month='.date('m',$te).'&to_year='.date('Y',$te).'&holdres=mark&p='.intval($i).'&numdoc=100&rd=0&spcctx=doc&full=1',$proxys[intval($i_proxy)]);
			if (chech_yandex_content($cont)==0) $i_proxy++;
		}
		while ((chech_yandex_content($cont)==0) && ($i_proxy<count($proxys)));
		$mas=simplexml_load_string($cont);
		$json = json_encode($mas);
		$mas= json_decode($json,true);
		// print_r($mas);
		echo '.';
		foreach ($mas['channel']['item'] as $item)
		{
			$mpost[]=array('link'=>$item['link'],'date'=>date('d.m.Y',strtotime($item['pubDate'])),'time'=>date('H.i.s',strtotime($item['pubDate'])),'post'=>strip_tags($item['description']));
		}
		// print_r($mpost);
	}
	// echo $cont;
	// die();
	// print_r($_POST);

	// foreach ($_POST as $key => $item)
	// {
	// 	$regex='/(?<type>[^0-9]+)(?<pos>\d+)/isu';
	// 	preg_match_all($regex, $key, $out);
	// 	if ($out['type']=='') continue;
	// 	if ($out['pos']=='') continue;
	// 	$mpost[$out['pos'][0]][$out['type'][0]]=$item;
	// }
	$cc=0;

	$user=new users();

	if ($_POST['orid']!='')
	{
		$qorder_ins=$db->query('SELECT * FROM blog_orders WHERE order_id='.$_POST['orid'].' LIMIT 1');
		$order_ins=$db->fetch($qorder_ins);
		$quser_ins=$db->query('SELECT * FROM user_tariff WHERE user_id='.$_GET['uid'].' LIMIT 1');
		$user_ins=$db->fetch($quser_ins);
	}

	foreach ($mpost as $item)
	{
		if (($item['post']!='')&&($item['link']!='')&&($item['date'].' '.$item['time']!=' '))
		{
			$hn=parse_url($item['link']);
			$hn=$hn['host'];
			$ahn=explode('.',$hn);
			$hn = $ahn[count($ahn)-2].'.'.$ahn[count($ahn)-1];
			$hh=$ahn[count($ahn)-2];
			$blog_id=$user->get_url($item['link']);
			$queue_item['order_id']=$_POST['orid'];
			$queue_item['post_link']=$item['link'];
			$queue_item['post_host']=$hn;
			$queue_item['post_time']=strtotime($item['date'].' '.$item['time']);
			$queue_item['post_content']=$item['post'];
			$queue_item['post_engage']=($eng_sources[$hn]==1?-1:0);
			$queue_item['post_advengage']='';
			$queue_item['blog_id']=$blog_id;
			$queue_item['post_spam']=intval($isspam);
			$queue_item['post_ful_com']=$item['post'];
			$queue_item['order_keyword']=$order_ins['order_keyword'];
			$queue_item['order_name']=$order_ins['order_name'];
			$queue_item['order_start']=$order_ins['order_start'];
			$queue_item['order_end']=$order_ins['order_end'];
			$queue_item['order_settings']=$order_ins['order_settings'];
			$queue_item['user_id']=$order_ins['user_id'];
			$queue_item['tariff_id']=$user_ins['tariff_id'];
			$queue_item['ut_date']=$user_ins['ut_date'];
			$queue_item['ut_id']=$user_ins['ut_id'];
			echo '<pre>';
			print_r($queue_item);
			echo '</pre>';
			$redis->sAdd('prev_queue',json_encode($queue_item));
			// $qw='INSERT INTO blog_post_prev (order_id,post_link,post_host,post_content,post_time,blog_id,post_engage) VALUES ('.$_POST['orid'].',\''.addslashes($item['link']).'\',\''.$hn.'\',\''.addslashes($item['post']).'\','.strtotime($item['date'].' '.$item['time']).','.$blog_id.',0)';
			//echo '<br>'.$qw.'<br>';
			// $db->query($qw);
			$cc++;
		}
	}
}
die();
if ($cc>0) echo '<br/>Добавлено сообщений: '.$cc.'<br/>';

// print_r($mpost);

echo '<form method="GET" id="user_select"><select name="uid" onchange="document.getElementById(\'user_select\').submit();">';
$quser=$db->query('SELECT user_id,user_email FROM users ORDER BY user_id DESC');
if ($_GET['uid']=='') echo '<option value=""></option>';
while ($user=$db->fetch($quser))
{
	echo '<option '.($user['user_id']==$_GET['uid']?'selected':'').' value="'.$user['user_id'].'">'.$user['user_id'].' '.$user['user_email'].'</option>';
}
echo '</select></form>';
echo '<form method="POST">';
if ($_GET['uid'])
{
	echo '<select name="orid">';
	$qorder=$db->query('SELECT * FROM blog_orders WHERE user_id='.intval($_GET['uid']));
	while ($order=$db->fetch($qorder))
	{
		echo '<option '.($_POST['orid']==$order['order_id']?'selected':'').' value="'.$order['order_id'].'">'.$order['order_id'].' '.$order['order_name'].'</option>';
	}
	echo '</select>';
}
for ($i=0;$i<10;$i++)
{
	echo '<hr>Текст: <input type="text" style="width: 600px;" name="post'.$i.'"><br>';
	echo 'Ссылка: <input type="link" style="width: 400px;" name="link'.$i.'"><br>';
	echo 'Время: <input type="date" name="date'.$i.'">';
	echo '<input type="time" name="time'.$i.'">';
}
echo '<br><input type="submit" value="ADD">';
echo '</form>';

?>
