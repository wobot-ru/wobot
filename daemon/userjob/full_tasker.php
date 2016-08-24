<?

require_once('/var/www/new/com/db.php');
require_once('/var/www/new/com/config.php');

$db=new database();
$db->connect();

$redis=new Redis() or die("Can'f load redis module.");
$redis->connect('127.0.0.1');

$ii=0;
$qblog=$db->query('SELECT blog_id,blog_login,blog_link FROM robot_blogs2 WHERE blog_last_update<'.(time()-86400*7).' AND blog_last_update!=0 AND blog_link!=\'vkontakte.ru\'');
echo 'toUpdate...'.intval($db->num_rows($qblog))."\n";
while ($blog=$db->fetch($qblog))
{
	$ii++;
	if ($ii % 1000 == 0) echo ($ii/1000)."\n";
	$redis->sAdd('blogs_queue' , json_encode($blog));
}


?>