<?

require_once('/var/www/com/db.php');
require_once('/var/www/com/config.php');
require_once('/var/www/bot/kernel.php');

$db=new database();
$db->connect();

if (trim($_POST['links'])!='' && $_POST['user_id']!='' && $_POST['order_id']!='')
{
	$order_id=$_POST['order_id'];
	$qorder=$db->query('SELECT * FROM blog_orders WHERE order_id='.intval($order_id).' LIMIT 1');
	$order=$db->fetch($qorder);
	$links=explode(',', trim($_POST['links']));
	if (count($links)>50) echo 'Слишком много ссылок!';
	else
	{
		// print_r($links);
		foreach ($links as $link)
		{
			echo '.';
			$link=trim($link);
			if (preg_match('/vk.com/isu', $link))
			{			
				// echo $link.'<br>';
				$regex='/wall(?<owner_id>\-?\d+)\_(?<post_id>\d+)/isu';
				preg_match_all($regex, $link, $out);
				$offset=0;
				unset($comments);
				do
				{
					// echo 'https://api.vkontakte.ru/method/wall.getComments?owner_id='.$out['owner_id'][0].'&post_id='.$out['post_id'][0].'&fields=followers_count,members_count&copy_history_depth=2'."<br>";
					$cont=parseUrl('https://api.vkontakte.ru/method/wall.getComments?owner_id='.$out['owner_id'][0].'&post_id='.$out['post_id'][0].'&count=100&offset='.$offset);
					$mcont=json_decode($cont,true);
					$offset+=100;
					foreach ($mcont['response'] as $key => $value)
					{
						if ($key==0) continue;
						$comments[]=array('text'=>$value['text'],'time'=>$value['date'],'owner_id'=>$value['from_id'],'link'=>preg_replace('/\_\d+/isu','_'.$value['cid'],$link));
					}
				}
				while (count($mcont['response'])!=1);
				// echo '<pre>';
				// print_r($comments);
				// echo '</pre>';
				foreach ($comments as $comment)
				{
					if ($comment['time']>$order['order_end']) continue;
					if ($comment['time']<$order['order_start']) continue;

					$qisset=$db->query('SELECT * FROM blog_post WHERE post_link=\''.addslashes(trim($comment['link'])).'\' AND order_id='.$order_id.' LIMIT 1');
					if ($db->num_rows($qisset)==0)
					{
						$blog_id=0;
						$qblog=$db->query('SELECT * FROM robot_blogs2 WHERE blog_link=\'vkontakte.ru\' AND blog_login=\''.addslashes($comment['owner_id']).'\' LIMIT 1');
						if ($db->num_rows($qblog)==0) 
						{
							$qinsert=$db->query('INSERT INTO robot_blogs2 (blog_link,blog_login) VALUES (\'vkontakte.ru\',\''.addslashes($comment['owner_id']).'\')');
							$blog_id=$db->insert_id($qinsert);
						}
						else
						{
							$blog=$db->fetch($qblog);
							$blog_id=$blog['blog_id'];
						}

						$qw='INSERT INTO blog_post (order_id,post_link,post_host,post_content,post_time,blog_id) VALUES ('.$order_id.',\''.addslashes(trim($comment['link'])).'\',\'vk.com\',\''.addslashes($comment['text']).'\','.$comment['time'].','.$blog_id.')';
						// echo '<br>'.$qw.'<br>';
						$qinsert=$db->query($qw);
						$post_id=$db->insert_id($qinsert);
						// echo '<br>INSERT INTO blog_full_com (ful_com_post_id,ful_com_order_id,ful_com_post) VALUES ('.$post_id.','.$_POST['orid'].',\''.addslashes($item['post']).'\')<br>';
						$db->query('INSERT INTO blog_full_com (ful_com_post_id,ful_com_order_id,ful_com_post) VALUES ('.$post_id.','.$order_id.',\''.addslashes($comment['text']).'\')');
						$count++;
					}
				}
			}
			elseif (preg_match('/facebook.com/isu', $link))
			{
				$regex='/\/posts\/(?<post_id>\d+)/isu';
				preg_match_all($regex, $link, $out);
				$offset=0;
				unset($comments);
				do
				{
					// echo 'https://api.vkontakte.ru/method/wall.getComments?owner_id='.$out['owner_id'][0].'&post_id='.$out['post_id'][0].'&fields=followers_count,members_count&copy_history_depth=2'."<br>";
					$cont=parseUrl('https://graph.facebook.com/'.$out['post_id'][0].'/comments?access_token=EAAJ68ASJQwQBAKGeXLfa5uNGEVppgP4MKEiZCq9eiqf4Mg85dcaZBy78XTXHJeMLm9d1U82a2UwCcLg7fSaKKafYLcWZBbrUUgZCdkBYN1lXVECtAKZCDSplaii5JCojwTOZAE6RbQe1W91njoPEGMqtLYMLroZA3EZD&count=75&offset='.$offset);
					$mcont=json_decode($cont,true);
					$offset+=75;
					foreach ($mcont['data'] as $key => $value)
					{
						$ids=explode('_', $value['id']);
						$comments[]=array('text'=>$value['message'],'time'=>strtotime($value['created_time']),'owner_id'=>$value['from']['id'],'link'=>$link.'?comment_id='.$ids[1]);
					}
				}
				while (count($mcont['data'])!=0);
				// echo '<pre>';
				// print_r($comments);
				// echo '</pre>';

				foreach ($comments as $comment)
				{
					if ($comment['time']>$order['order_end']) continue;
					if ($comment['time']<$order['order_start']) continue;

					$qisset=$db->query('SELECT * FROM blog_post WHERE post_link=\''.addslashes(trim($comment['link'])).'\' AND order_id='.$order_id.' LIMIT 1');
					if ($db->num_rows($qisset)==0)
					{
						$blog_id=0;
						$qblog=$db->query('SELECT * FROM robot_blogs2 WHERE blog_link=\'facebook.com\' AND blog_login=\''.addslashes($comment['owner_id']).'\' LIMIT 1');
						if ($db->num_rows($qblog)==0) 
						{
							$qinsert=$db->query('INSERT INTO robot_blogs2 (blog_link,blog_login) VALUES (\'facebook.com\',\''.addslashes($comment['owner_id']).'\')');
							$blog_id=$db->insert_id($qinsert);
						}
						else
						{
							$blog=$db->fetch($qblog);
							$blog_id=$blog['blog_id'];
						}

						$qw='INSERT INTO blog_post (order_id,post_link,post_host,post_content,post_time,blog_id) VALUES ('.$order_id.',\''.addslashes(trim($comment['link'])).'\',\'facebook.com\',\''.addslashes($comment['text']).'\','.$comment['time'].','.$blog_id.')';
						// echo '<br>'.$qw.'<br>';
						$qinsert=$db->query($qw);
						$post_id=$db->insert_id($qinsert);
						// echo '<br>INSERT INTO blog_full_com (ful_com_post_id,ful_com_order_id,ful_com_post) VALUES ('.$post_id.','.$_POST['orid'].',\''.addslashes($item['post']).'\')<br>';
						$db->query('INSERT INTO blog_full_com (ful_com_post_id,ful_com_order_id,ful_com_post) VALUES ('.$post_id.','.$order_id.',\''.addslashes($comment['text']).'\')');
						$count++;
					}
				}

			}
		}
	}
	echo '<br>Количество загруженных комментариев: '.$count.'<br>';
}

echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

<form method="GET" id="user_select">Кабинет: <select name="uid" onchange="document.getElementById(\'user_select\').submit();">';
$quser=$db->query('SELECT * FROM users ORDER BY user_id ASC');
while ($user=$db->fetch($quser))
{
	echo '<option '.($_GET['uid']==$user['user_id']?'selected':'').' value="'.$user['user_id'].'">'.$user['user_id'].' '.$user['user_email'].'</option>';
}
echo '</select></form>
';

if ($_GET['uid']!='')
{
	echo '<form method="POST" action="?uid='.$_GET['uid'].'">
	<input type="hidden" name="user_id" value="'.$_GET['uid'].'">
	Тема: <select name="order_id">';
	$qorder=$db->query('SELECT * FROM blog_orders as a LEFT JOIN users as b ON a.user_id=b.user_id WHERE a.user_id='.$_GET['uid'].' AND a.user_id!=0 AND a.ut_id!=0 ORDER BY order_id DESC');
	while ($order=$db->fetch($qorder))
	{
		echo '<option value="'.$order['order_id'].'">'.$order['order_id'].' '.$order['order_name'].'</option>';
	}
	echo '</select><br>
	Вставлять через запятую!
	<br>
	<textarea name="links" cols="100" rows="30"></textarea><br>
	<input type="submit" value="Добавить">
	';
}

?>