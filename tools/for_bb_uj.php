<?
require_once('/var/www/daemon/com/config.php');
//require_once('/var/www/daemon/com/func.php');
require_once('/var/www/daemon/com/db.php');
require_once('/var/www/daemon/bot/kernel.php');

$db = new database();
$db->connect();

$users=$db->query('SELECT post_id,post_link FROM blog_post WHERE blog_id=0 AND post_link LIKE \'%www.babyblog.ru/user/%\'');
while ($user=$db->fetch($users))
{
	$regex='/www\.babyblog\.ru\/user\/(?<user>.*?)\//isu';
	preg_match_all($regex,$user['post_link'],$out);
	$muser[$user['post_id']]['type']='list';
	$muser[$user['post_id']]['user']=$out['user'][0];
}
//echo 123;
//print_r($muser);
//die();

foreach ($muser as $key => $item)
{
	$qw=$db->query('SELECT * FROM robot_blogs2 WHERE blog_link=\'babyblog.ru\' AND blog_login=\''.$item['user'].'\' LIMIT 1');
	if (mysql_num_rows($qw)==0)
	{
		echo 'INSERT INTO robot_blogs2 (blog_link,blog_login) VALUES (\'babyblog.ru\',\''.$item['user'].'\')'."\n";
		$qins=$db->query('INSERT INTO robot_blogs2 (blog_link,blog_login) VALUES (\'babyblog.ru\',\''.$item['user'].'\')');
		$idus=$db->insert_id();
	}
	else
	{
		$uss=$db->fetch($qw);
		$idus=$uss['blog_id'];
	}
	echo 'UPDATE blog_post SET blog_id='.$idus.' WHERE post_id='.$key."\n";
	$db->query('UPDATE blog_post SET blog_id='.$idus.' WHERE post_id='.$key);
}
echo count($muser);
//print_r($muser);

?>