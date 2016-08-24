
<?
require_once('com/config.php');
require_once('com/func.php');
require_once('com/db.php');
require_once('bot/kernel.php');
require_once('com/tmhOAuth.php');
require_once('com/vkapi.class.php');
//require_once('get_vkontakte.php');
require_once('get_facebook.php');
require_once('../fulljob/adv_src_func.php');
require_once('get_twitter2.php');
require_once('get_livejournal.php');
require_once('get_li.php');
require_once('get_mail.php');
require_once('get_rutwit.php');
require_once('get_yaru.php');
require_once('get_plus_google.php');
require_once('parsers/babyblog/get_babyblog.php');
require_once('parsers/foursquare/get_foursquare.php');
require_once('parsers/friendfeed/get_friendfeed.php');
require_once('parsers/kp/get_kp.php');

$fp = fopen('/var/www/pids/uj'.$_SERVER['argv'][1].'.pid', 'w');
fwrite($fp, getmypid());
fclose($fp);

//error_reporting(E_ERROR | E_WARNING | E_PARSE);
error_reporting(E_ERROR);
ignore_user_abort(true);
set_time_limit(0);
ini_set('max_execution_time',0);
ini_set('default_charset','utf-8');
ob_implicit_flush();
date_default_timezone_set ( 'Europe/Moscow' );
$deltatime=7;
// $db = new database();
// $db->connect();

$redis = new Redis();    
$redis->connect('127.0.0.1');

$gg1=1;
while(1)
{
	if (!$db->ping()) {
		echo "MYSQL disconnected, reconnect after 10 sec...\n";
		sleep(10);
		$db->connect();
		die();
	}
	$i=0;
	unset($mproxy);
	$iter=0;
	// $cmproxy=file_get_contents('http://localhost/api/service/getlist.php');
	// $mproxy=json_decode($cmproxy,true);
	$mproxy=json_decode($redis->get('proxy_list'),true);

	shuffle($mproxy);
	unset($blogs);
	//if ((($gg1 % 2)==1)||(not_update()==1))
	{
		for ($i=0;$i<50;$i++)
		{
			$task=$redis->sPop('blogs_free_queue');
			if ($task=='') break;
			$blogs[]=json_decode($task,true);
		}
		// $ressec=$db->query('SELECT blog_id,blog_login,blog_link FROM robot_blogs2 WHERE blog_last_update=0 AND MOD(blog_id,'.$_SERVER['argv'][2].')='.$_SERVER['argv'][1].' AND blog_link!=\'vkontakte.ru\' ORDER BY blog_id DESC');
		// echo 'SELECT blog_id,blog_login,blog_link FROM robot_blogs2 WHERE blog_last_update=0 AND MOD(blog_id,'.$_SERVER['argv'][2].')='.$_SERVER['argv'][1].' AND blog_link!=\'vkontakte.ru\' ORDER BY blog_id DESC';
		// if ($db->num_rows($ressec)==0) $ressec=$db->query('SELECT blog_id,blog_login,blog_link FROM robot_blogs2 WHERE blog_last_update=0 AND blog_link!=\'vkontakte.ru\' ORDER BY blog_id DESC');
	}
	/*else
	{
		for ($i=0;$i<50;$i++)
		{
			$task=$redis->sPop('blogs_queue');
			if ($task=='') break;
			$blogs[]=json_decode($task,true);
		}
		// $ressec=$db->query('SELECT blog_id,blog_login,blog_link FROM robot_blogs2 WHERE blog_last_update<'.(time()-$deltatime*86400).' AND MOD(blog_id,'.$_SERVER['argv'][2].')='.$_SERVER['argv'][1].' AND blog_link!=\'vkontakte.ru\' ORDER BY blog_last_update ASC LIMIT 50');
		// echo 'SELECT blog_id,blog_login,blog_link FROM robot_blogs2 WHERE blog_last_update<'.(time()-$deltatime*86400).' AND MOD(blog_id,'.$_SERVER['argv'][2].')='.$_SERVER['argv'][1].' AND blog_link!=\'vkontakte.ru\' ORDER BY blog_last_update ASC LIMIT 50';
	}*/
	$gg1++;
	//$ressec=$db->query('SELECT * FROM robot_blogs2 WHERE blog_login=\'navalny\'');

	// while($blog=$db->fetch($ressec))
	foreach ($blogs as $blog)
	{
		unset($inf);
		print_r($blog);
		switch ($blog['blog_link']) 
		{
		    case 'liveinternet.ru':
		        $inf=get_liveinternet($blog['blog_login']);
		        break;
		    case 'livejournal.com':
		        $inf=get_lj($blog['blog_login']);
		        break;
		    case 'rutwit.ru':
		        $inf=get_rutwit($blog['blog_login']);
		        break;
		    case 'twitter.com':
		        $inf=get_twitter($blog['blog_login']);
		        break;
		    case 'ya.ru':
		        $inf=get_yaru($blog['blog_login']);
		        break;
		    case 'babyblog.ru':
		        $inf=get_babyblog($blog['blog_login']) ;
		        break;
		    case 'foursquare.com':
		        $inf=get_foursquare($blog['blog_login']);
		        break;
		    case 'friendfeed.com':
		        $inf=get_ff($blog['blog_login']);
		        break;
		    case 'kp.ru':
		        $inf=get_kp($blog['blog_login']);
		        break;
		    case 'plus.google.com':
		        $inf=get_google_plus($blog['blog_login']);
		        break;
		    case 'facebook.com':
		    	$inf=get_fb($blog['blog_login']);
		    	break;
		}
		if (preg_match('/mail\.ru\/.*/isu',$blog['blog_link']))
		{
			$regex='/mail\.ru\/(?<type>.*)/isu';
			preg_match_all($regex, $blog['blog_link'], $out);
			$inf=get_mail($blog['blog_login'],$out['type'][0]);
		}
		print_r($inf);
		//continue;
		if ($blog['blog_link']=='plus.google.com') $qw='UPDATE robot_blogs2 SET blog_last_update=\''.time().'\',blog_location=\''.addslashes($inf['loc']).'\',blog_gender=\''.addslashes($inf['gender']).'\' WHERE blog_id='.$blog['blog_id'];
		elseif (trim($inf['name'])=='') $qw='UPDATE robot_blogs2 SET blog_nick=\''.addslashes($blog['blog_login']).'\' ,blog_last_update=\''.time().'\' WHERE blog_id='.$blog['blog_id'];
		else $qw='UPDATE robot_blogs2 SET blog_location=\''.$inf['loc'].'\', blog_readers='.intval($inf['fol']).', blog_nick=\''.addslashes($inf['name']).'\', blog_last_update=\''.time().'\',blog_ico=\''.addslashes($inf['ico']).'\' WHERE blog_id='.$blog['blog_id'];
		$redis->publish('update_query', $qw);
		echo $qw;
		// $rru=$db->query($qw);
		$logs[mktime(0,0,0,date('n'),date('j'),date('Y'))]++;
		echo $logs[mktime(0,0,0,date('n'),date('j'),date('Y'))]." пройдено за ".date('d.m.Y')."\n";
	}
	echo 'idle...';
	sleep(10);

}
?>
