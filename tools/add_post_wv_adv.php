<?

require_once('/var/www/com/db.php');
require_once('/var/www/com/config.php');
require_once('/var/www/daemon/com/users.php');

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

// print_r($_POST);

foreach ($_POST as $key => $item)
{
	$regex='/(?<type>[^0-9]+)(?<pos>\d+)/isu';
	preg_match_all($regex, $key, $out);
	if ($out['type']=='') continue;
	if ($out['pos']=='') continue;
	$mpost[$out['pos'][0]][$out['type'][0]]=$item;
}
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
		echo '->';
		$qisset=$db->query('SELECT * FROM blog_post WHERE post_link=\''.addslashes($item['link']).'\' AND order_id='.$_POST['orid'].' LIMIT 1');
		if ($db->num_rows($qisset)==0)
		{
			$hn=parse_url($item['link']);
			$hn=$hn['host'];
			$ahn=explode('.',$hn);
			$hn = $ahn[count($ahn)-2].'.'.$ahn[count($ahn)-1];
			$hh=$ahn[count($ahn)-2];
			$blog_id=$user->get_url($item['link']);
			// $queue_item['order_id']=$_POST['orid'];
			// $queue_item['post_link']=$item['link'];
			// $queue_item['post_host']=$hn;
			// $queue_item['post_time']=strtotime($item['date'].' '.$item['time']);
			// $queue_item['post_content']=$item['post'];
			// $queue_item['post_engage']=($eng_sources[$hn]==1?-1:0);
			// $queue_item['post_advengage']='';
			// $queue_item['blog_id']=$blog_id;
			// $queue_item['post_spam']=intval($isspam);
			// $queue_item['post_ful_com']='';
			// $queue_item['order_keyword']=$order_ins['order_keyword'];
			// $queue_item['order_name']=$order_ins['order_name'];
			// $queue_item['order_start']=$order_ins['order_start'];
			// $queue_item['order_end']=$order_ins['order_end'];
			// $queue_item['order_settings']=$order_ins['order_settings'];
			// $queue_item['user_id']=$order_ins['user_id'];
			// $queue_item['tariff_id']=$user_ins['tariff_id'];
			// $queue_item['ut_date']=$user_ins['ut_date'];
			// $queue_item['ut_id']=$user_ins['ut_id'];
			// echo '<pre>';
			// print_r($queue_item);
			// echo '</pre>';
			// $redis->sAdd('prev_queue',json_encode($queue_item));
			$engage=intval($item['like']+$item['repost']+$item['retweet']+$item['comment']);
			if ($hn=='vk.com') $adv_engage=json_encode(array('comment'=>$item['comment'],'likes'=>$item['like'],'repost'=>$item['repost']));
			elseif ($hn=='twitter.com') $adv_engage=json_encode(array('retweet'=>$item['retweet']));
			elseif ($hn=='livejournal.com') $adv_engage=json_encode(array('comment'=>$item['comment']));
			elseif ($hn='facebook.com') $adv_engage=json_encode(array('likes'=>$item['like'],'comment'=>$item['comment']));
			else $adv_engage='';

			if ($hn=='facebook.com')
			{

				$rgx='/facebook\.com\/(?<id_acc>[^\/\?\&]*)/is';
				preg_match_all($rgx,$item['link'],$acc_id);
				// print_r($acc_id);

				if (!preg_match('/^.*\.php$/isu', $acc_id['id_acc'][0]))
				{
					// echo 'GG';
					$chbb=$db->query('SELECT * from robot_blogs2 WHERE blog_login=\''.$acc_id['id_acc'][0].'\' AND blog_link=\'facebook.com\' LIMIT 1');
					if (mysql_num_rows($chbb)==0)
					{
						$ins_bb=$db->query('INSERT INTO robot_blogs2 (blog_link,blog_login) VALUES (\'facebook\.com\',\''.$acc_id['id_acc'][0].'\')');
						$bb1['blog_id']=$db->insert_id();
					}
					else
					{
						$bb1=$db->fetch($chbb);
					}
				}
				if (intval($item['value'])!=0) $db->query('UPDATE robot_blogs2 SET blog_readers='.intval($item['value']).' WHERE blog_id='.$bb1['blog_id']);
				$blog_id=$bb1['blog_id'];

			}

			$qw='INSERT INTO blog_post (order_id,post_link,post_host,post_content,post_time,blog_id,post_engage,post_advengage) VALUES ('.$_POST['orid'].',\''.addslashes($item['link']).'\',\''.$hn.'\',\''.addslashes($item['post']).'\','.(strtotime($item['date'].' '.$item['time'])+7200).','.$blog_id.','.intval($engage).',\''.addslashes($adv_engage).'\')';
			// echo '<br>'.$qw.'<br>';
			$qinsert=$db->query($qw);
			$post_id=$db->insert_id($qinsert);
			// echo '<br>INSERT INTO blog_full_com (ful_com_post_id,ful_com_order_id,ful_com_post) VALUES ('.$post_id.','.$_POST['orid'].',\''.addslashes($item['post']).'\')<br>';
			$db->query('INSERT INTO blog_full_com (ful_com_post_id,ful_com_order_id,ful_com_post) VALUES ('.$post_id.','.$_POST['orid'].',\''.addslashes($item['post']).'\')');
			$cc++;
		}
	}
}
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
	echo 'Лайки: <input type="text" name="like'.$i.'">';
	echo 'Комментарии: <input type="text" name="comment'.$i.'">';
	echo 'Репосты: <input type="text" name="repost'.$i.'">';
	echo 'Ретвиты: <input type="text" name="retweet'.$i.'">';
	echo 'Охват: <input type="text" name="value'.$i.'">';
	echo 'Время: <input type="date" name="date'.$i.'">';
	echo '<input type="time" name="time'.$i.'">';
}
echo '<br><input type="submit" value="ADD">';
echo '</form>';

?>