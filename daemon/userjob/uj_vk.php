
<?
require_once('com/config.php');
require_once('com/func.php');
require_once('com/db.php');
require_once('bot/kernel.php');
require_once('com/tmhOAuth.php');
require_once('com/vkapi.class.php');
require_once('get_vkontakte2.php');
require_once('get_facebook.php');
require_once('get_twitter.php');
require_once('get_livejournal.php');

$redis=new Redis() or die("Can'f load redis module.");
$redis->connect('127.0.0.1');

$fp = fopen('/var/www/pids/uservk'.$_SERVER['argv'][1].'.pid', 'w');
fwrite($fp, getmypid());
fclose($fp);

echo 123;
//error_reporting(E_ERROR | E_WARNING | E_PARSE);
error_reporting(E_ERROR);
ignore_user_abort(true);
set_time_limit(0);
ini_set('max_execution_time',0);
ini_set('default_charset','utf-8');
ob_implicit_flush();
date_default_timezone_set ( 'Europe/Moscow' );
$deltatime=3;
$db = new database();
$db->connect();
$gg1=1;
$m_access_token=json_decode($redis->get('at_vk'),true);
while(1)
{
	if (!$db->ping()) {
		echo "MYSQL disconnected, reconnect after 10 sec...\n";
		sleep(10);
		$db->connect();
		die();
	}
$i=0;
/*if (($gg1 % 2)==1)
{
$ressec=$db->query('SELECT blog_id,blog_login,blog_link,blog_nick FROM robot_blogs2 WHERE (blog_last_update=0 OR blog_last_update=1) AND blog_link=\'vkontakte\.ru\' AND MOD(blog_id,6)='.$_SERVER['argv'][1].' ORDER BY blog_id DESC');
if ($db->num_rows($ressec)==0) $ressec=$db->query('SELECT blog_id,blog_login,blog_link,blog_nick FROM robot_blogs2 WHERE (blog_last_update=0 OR blog_last_update=1) AND blog_link=\'vkontakte\.ru\' ORDER BY blog_id ASC');
//echo 'SELECT blog_id,blog_login,blog_link,blog_nick FROM robot_blogs2 WHERE blog_last_update=0 AND blog_link=\'vkontakte\.ru\' ORDER BY blog_id DESC'."\n";
}
else
{
$ressec=$db->query('SELECT blog_id,blog_login,blog_link,blog_nick FROM robot_blogs2 WHERE blog_last_update<'.(time()-$deltatime*86400).' AND blog_link=\'vkontakte\.ru\' AND MOD(blog_id,6)='.$_SERVER['argv'][1].' ORDER BY blog_last_update ASC LIMIT 100');
if ($db->num_rows($ressec)==0) $ressec=$db->query('SELECT blog_id,blog_login,blog_link,blog_nick FROM robot_blogs2 WHERE blog_last_update<'.(time()-$deltatime*86400).' AND blog_link=\'vkontakte\.ru\' AND MOD(blog_id,6)='.($_SERVER['argv'][1]+2).' ORDER BY blog_last_update ASC LIMIT 100');
//echo 'SELECT blog_id,blog_login,blog_link,blog_nick FROM robot_blogs2 WHERE blog_last_update<'.(time()-$deltatime*86400).' AND blog_link=\'vkontakte\.ru\' ORDER BY blog_last_update DESC LIMIT 100'."\n";
}*/
// echo 123;
$ressec=$db->query('SELECT blog_id,blog_login,blog_link,blog_nick FROM robot_blogs2 WHERE (blog_last_update=0 OR blog_last_update=1) AND blog_link=\'vkontakte\.ru\' ORDER BY blog_id ASC');
// echo 321;
$gg1++;
//$ressec=$db->query('SELECT * FROM robot_blogs2 WHERE blog_login=\'navalny\'');

while($blog=$db->fetch($ressec))
{
	$iter++;
	if ($iter%100==0) $m_access_token=json_decode($redis->get('at_vk'),true);
	$_SERVER['argv'][1]=$_SERVER['argv'][1]%count($m_access_token);
	//echo $blog['blog_nick'].' '.$blog['blog_login'].' '.$gg1.' '.($gg1 % 2)."\n";
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
	if (preg_match('/[^0-9\-]/isu', $blog['blog_login']))
	{
		$qw='UPDATE robot_blogs2 SET blog_last_update=\''.time().'\' WHERE blog_id='.$blog['blog_id'];
		echo $qw;
		$rru=$db->query($qw);
	}
	elseif ($blog['blog_link']=='vkontakte.ru')
	{
		$inf=get_vk($blog['blog_login']);
		print_r($inf);
		if ($inf['name']=='') $qw='UPDATE robot_blogs2 SET blog_last_update=\''.time().'\' WHERE blog_id='.$blog['blog_id'];
		$qw='UPDATE robot_blogs2 SET blog_location=\''.$inf['loc'].'\',blog_gender='.intval($inf['gender']).', blog_readers='.intval($inf['fol']).', blog_nick=\''.addslashes($inf['name']).'\',blog_age='.intval($inf['age']).' , blog_login=\''.addslashes($inf['nick']).'\',blog_last_update=\''.time().'\',blog_ico=\''.$inf['ico'].'\' WHERE blog_id='.$blog['blog_id'];
		//echo $qw;
		$rru=$db->query($qw);
		$logs[mktime(0,0,0,date('n'),date('j'),date('Y'))]++;
		echo $logs[mktime(0,0,0,date('n'),date('j'),date('Y'))]." пройдено за ".date('d.m.Y').' последний: '.$blog['blog_id']."\n";
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
sleep(10);

}
?>
