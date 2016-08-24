
<?
require_once('com/config.php');
require_once('com/func.php');
require_once('com/db.php');
require_once('bot/kernel.php');
//require_once('com/tmhOAuth.php');
//require_once('com/vkapi.class.php');
//require_once('get_vkontakte2.php');
//require_once('get_facebook.php');
//require_once('get_twitter.php');
//require_once('get_livejournal.php');
require_once('get_yaru_clubs.php');

//error_reporting(E_ERROR | E_WARNING | E_PARSE);
error_reporting(E_ERROR);
ignore_user_abort(true);
set_time_limit(0);
ini_set('max_execution_time',0);
ini_set('default_charset','utf-8');
ob_implicit_flush();
date_default_timezone_set ( 'Europe/Moscow' );
$deltatime=7;
$db = new database();
$db->connect();
$gg1=1;
while(1)
{
	if (!$db->ping()) {
		echo "MYSQL disconnected, reconnect after 10 sec...\n";
		sleep(10);
		$db->connect();
	}
$i=0;
$ressec=$db->query('SELECT * FROM `blog_post` WHERE `post_link` LIKE \'%clubs.ya.ru%\' AND blog_id=0 ORDER BY post_id DESC');
//$ressec=$db->query('SELECT * FROM robot_blogs2 WHERE blog_login=\'navalny\'');

while($blog=$db->fetch($ressec))
{
	echo $blog['post_id'].' '.$blog['post_link']."\n";
	/*if ($blog['blog_link']=='twitter.com')
	{
		$inf=get_twitter($blog['blog_login']);
		print_r($inf);
		echo $blog['blog_nick'].' '.$blog['blog_login'];
		$qw='UPDATE robot_blogs2 SET blog_location=\''.$inf['loc'].'\', blog_readers='.intval($inf['fol']).', blog_nick=\''.$blog['blog_nick'].'\', blog_login=\''.$blog['blog_login'].'\',blog_last_update=\''.time().'\' WHERE blog_id='.$blog['blog_id'];
		echo $qw;
		$rru=$db->query($qw);
		//$rru=$db->query('UPDATE robot_blogs2 SET blog_location=\''.$inf['loc'].'\', blog_readers='.intval($inf['fol']).', blog_nick=\''.$inf['nick'].'\', blog_login=\''.$inf['login'].'\',blog_last_update=\''.time().'\' WHERE blog_id='.$blog['blog_id']);
	}*/
	/*if ($blog['blog_link']=='vkontakte.ru')
	{
		$inf=get_vk($blog['blog_login']);
		print_r($inf);
		$qw='UPDATE robot_blogs2 SET blog_location=\''.$inf['loc'].'\',blog_gender='.intval($inf['gender']).', blog_readers='.intval($inf['fol']).', blog_nick=\''.$inf['name'].'\',blog_age='.intval($inf['age']).' , blog_login=\''.$inf['nick'].'\',blog_last_update=\''.time().'\' WHERE blog_id='.$blog['blog_id'];
		echo $qw;
		$rru=$db->query($qw);
	}*/
	//if (preg_match('/mail.ru\/.*?/isu',$blog['blog_link']))
	{
		$inf=get_yaruclubs($blog['post_link']);
		print_r($inf);
		if (($inf['nick']!='') && ($inf['name']!=''))
		{
			$iss=$db->query('SELECT * FROM robot_blogs2 WHERE blog_link=\'ya.ru\' AND blog_login=\''.$inf['nick'].'\'');
			if (mysql_num_rows($iss)==0)
			{
				echo 'INSERT INTO robot_blogs2 (blog_link,blog_login,blog_nick) VALUES (\'ya.ru\',\''.$inf['nick'].'\',\''.$inf['name'].'\')'."\n";
				$db->query('INSERT INTO robot_blogs2 (blog_link,blog_login,blog_nick) VALUES (\'ya.ru\',\''.$inf['nick'].'\',\''.$inf['name'].'\')');
				echo 'UPDATE blog_post SET blog_id='.$db->insert_id().' WHERE post_id='.$blog['post_id'].' AND order_id='.$blog['order_id'];
				$db->query('UPDATE blog_post SET blog_id='.$db->insert_id().' WHERE post_id='.$blog['post_id'].' AND order_id='.$blog['order_id']);
			}
			else
			{
				$us=$db->fetch($iss);
				if ($us['blog_nick']=='')
				{
					echo 'UPDATE robot_blogs2 SET blog_nick=\''.$inf['name'].'\' WHERE blog_id=\''.$us['blog_id'].'\''."\n";
					$db->query('UPDATE robot_blogs2 SET blog_nick=\''.$inf['name'].'\' WHERE blog_id=\''.$us['blog_id'].'\'');
				}
				echo 'UPDATE blog_post SET blog_id='.$us['blog_id'].' WHERE post_id='.$blog['post_id'].' AND order_id='.$blog['order_id']."\n";
				$db->query('UPDATE blog_post SET blog_id='.$us['blog_id'].' WHERE post_id='.$blog['post_id'].' AND order_id='.$blog['order_id']);
			}
		}
	}
	/*elseif ($blog['blog_link']=='facebook.com')
	{
		$inf=get_fb($blog['blog_login']);
		print_r($inf);
		$qw='UPDATE robot_blogs2 SET blog_location=\''.$inf['loc'].'\', blog_readers='.intval($inf['fol']).', blog_nick=\''.$inf['name'].'\', blog_login=\''.$inf['nick'].'\',blog_last_update=\''.time().'\' WHERE blog_id='.$blog['blog_id'];
		echo $qw;
		$rru=$db->query($qw);
	}
	elseif ($blog['blog_link']=='livejournal.com')
	{
		echo $blog['blog_link'];
		$inf=get_lj($blog['blog_login']);
		print_r($inf);
		//echo '|'.intval($inf['fol']).'|';
		$qw='UPDATE robot_blogs2 SET blog_location=\''.$inf['loc'].'\', blog_readers='.$inf['fol'].', blog_nick=\''.$inf['name'].'\',blog_age='.intval($inf['age']).', blog_login=\''.$inf['nick'].'\',blog_last_update=\''.time().'\' WHERE blog_id='.$blog['blog_id'];
		echo $qw;
		$rru=$db->query($qw);
	}*/
}
echo 'idle...';
sleep(1);

}
?>
