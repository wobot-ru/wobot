
<?
require_once('/var/www/daemon/com/config.php');
require_once('com/func.php');
require_once('/var/www/daemon/com/db.php');
require_once('bot/kernel.php');
require_once('com/tmhOAuth.php');
require_once('com/vkapi.class.php');
//require_once('get_vkontakte.php');
require_once('get_facebook.php');
//require_once('get_twitter.php');
//require_once('get_livejournal.php');

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
$gg1=0;
while(1)
{
	if (!$db->ping()) {
		echo "MYSQL disconnected, reconnect after 10 sec...\n";
		sleep(10);
		$db->connect();
	}
$i=0;
$gg1++;
if (($gg1 % 2)==1)
{
$ressec=$db->query('SELECT blog_id,blog_login,blog_link FROM robot_blogs2 WHERE blog_last_update=0 AND blog_link=\'facebook.com\' ORDER BY blog_id DESC');
}
else
{
$ressec=$db->query('SELECT blog_id,blog_login,blog_link FROM robot_blogs2 WHERE blog_last_update<'.(time()-$deltatime*86400).' AND blog_link=\'facebook.com\' ORDER BY blog_last_update DESC LIMIT 100');
}
//$ressec=$db->query('SELECT * FROM robot_blogs2 WHERE blog_login=\'navalny\'');

while($blog=$db->fetch($ressec))
{
	//echo $blog['blog_nick'].' '.$blog['blog_login'];
	/*if ($blog['blog_link']=='twitter.com')
	{
		$inf=get_twitter($blog['blog_login']);
		print_r($inf);
		echo $blog['blog_nick'].' '.$blog['blog_login'];
		$qw='UPDATE robot_blogs2 SET blog_location=\''.$inf['loc'].'\', blog_readers='.intval($inf['fol']).', blog_nick=\''.$blog['blog_nick'].'\', blog_login=\''.$blog['blog_login'].'\',blog_last_update=\''.time().'\' WHERE blog_id='.$blog['blog_id'];
		echo $qw;
		$rru=$db->query($qw);
		//$rru=$db->query('UPDATE robot_blogs2 SET blog_location=\''.$inf['loc'].'\', blog_readers='.intval($inf['fol']).', blog_nick=\''.$inf['nick'].'\', blog_login=\''.$inf['login'].'\',blog_last_update=\''.time().'\' WHERE blog_id='.$blog['blog_id']);
	}
	elseif ($blog['blog_link']=='vkontakte.ru')
	{
		$inf=get_vk($blog['blog_login']);
		print_r($inf);
		$qw='UPDATE robot_blogs2 SET blog_location=\''.$inf['loc'].'\', blog_readers='.intval($inf['fol']).', blog_nick=\''.$inf['name'].'\',blog_age='.intval($inf['age']).' , blog_login=\''.$inf['nick'].'\',blog_last_update=\''.time().'\' WHERE blog_id='.$blog['blog_id'];
		echo $qw;
		$rru=$db->query($qw);
	}*/
	if ($blog['blog_link']=='facebook.com')
	{
		$inf=get_fb($blog['blog_login']);
		print_r($inf);
		$qw='UPDATE robot_blogs2 SET blog_location=\''.$inf['loc'].'\', blog_readers='.intval($inf['fol']).', blog_nick=\''.addslashes($inf['name']).'\', blog_login=\''.addslashes($inf['nick']).'\',blog_last_update=\''.time().'\' WHERE blog_id='.$blog['blog_id'];
		//echo $qw;
		$rru=$db->query($qw);
		$logs[mktime(0,0,0,date('n'),date('j'),date('Y'))]++;
		echo $logs[mktime(0,0,0,date('n'),date('j'),date('Y'))]." пройдено за ".date('d.m.Y')."\n";
	}
	/*elseif ($blog['blog_link']=='livejournal.com')
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
sleep(60);

}
?>
