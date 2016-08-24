<?

require_once('/var/www/daemon/com/db.php');
require_once('/var/www/daemon/com/config.php');

$db=new database();
$db->connect();

$redis=new Redis() or die("Can'f load redis module.");
$redis->connect('127.0.0.1');

$fp = fopen('/var/www/pids/utasker'.$_SERVER['argv'][1].'.pid', 'w');
fwrite($fp, getmypid());
fclose($fp);

$i=0;
while (1)
{
	$i++;
	if ($i % 20 == 0) echo 'blogs_queue: '.$redis->sCard('blogs_queue').' blogs_free_queue: '.$redis->sCard('blogs_free_queue')."\n";
	// if ($i % 700 == 0)
	// {
	// 	$ii=0;
	// 	$qblog=$db->query('SELECT blog_id,blog_login,blog_link FROM robot_blogs2 WHERE blog_last_update<'.(time()-86400*7).' AND blog_last_update!=0 AND blog_link!=\'vkontakte.ru\'');
	// 	echo 'toUpdate...'.intval($db->num_rows($qblog))."\n";
	// 	while ($blog=$db->fetch($qblog))
	// 	{
	// 		$ii++;
	// 		if ($ii % 1000 == 0) echo ($ii/1000)."\n";
	// 		$redis->sAdd('blogs_queue' , json_encode($blog));
	// 	}
	// }

	$iii=0;
	$qblog=$db->query('SELECT blog_id,blog_login,blog_link FROM robot_blogs2 WHERE blog_last_update=0 AND blog_link!=\'vkontakte.ru\'');
	echo 'toHarv...'.intval($db->num_rows($qblog))."\n";
	while ($blog=$db->fetch($qblog))
	{
		$iii++;
		if ($iii % 1000 == 0) echo ($iii/1000)."\n";
		$redis->sAdd('blogs_free_queue' , json_encode($blog));
	}
	sleep(60);
}

?>