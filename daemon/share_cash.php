<?

require_once('/var/www/com/config.php');
require_once('/var/www/com/db.php');

$db=new database();
$db->connect();

$qshare=$db->query('SELECT * FROM blog_sharing');
while ($share=$db->fetch($qshare))
{
	$mshare[$share['order_id']][$share['user_id']]=$share['sharing_priv'];
}

$memcache = memcache_connect('localhost', 11211);
$memcache->set('blog_sharing', json_encode($mshare), MEMCACHE_COMPRESSED, 86400);
//print_r($mshare);

?>