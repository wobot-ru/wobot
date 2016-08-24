<?php
$fp = fopen('/var/www/daemon/logs/getlist.log', 'a');
fwrite($fp, date('r')."\n");
fclose($fp);

if ($_GET['type']=='')
{
	$memcache = memcache_connect("localhost", 11211);
	$issetrow=$memcache->get('proxylist');
	if ($issetrow!='') 
	{
		echo $issetrow;
		die();
	}
}
else
{
	switch ($_GET['type']) {
	    case 'vk':
	        $src='vk_delay';
	        break;
	    case 'tw':
	        $src='twitter_delay';
	        break;
	    case 'yb':
	        $src='yandex_delay';
	        break;
	}
}

require_once('/var/www/new/com/config.php');
require_once('/var/www/new/com/func.php');
require_once('/var/www/new/com/db.php');
require_once('/var/www/new/bot/kernel.php');
ini_set("memory_limit", "2048M");
date_default_timezone_set ( 'Europe/Moscow' );
$db = new database();
$db->connect();

if ($_GET['type']=='') $res=$db->query('SELECT proxy FROM tp_proxys WHERE valid=1 ORDER BY response_time ASC LIMIT 400');
elseif ($_GET['sort']=='') $res=$db->query('SELECT proxy FROM tp_proxys WHERE valid=1 AND '.$src.'!=0 ORDER BY RAND() ASC '.($_GET['full']==''?'LIMIT 10':''));
else $res=$db->query('SELECT proxy FROM tp_proxys WHERE valid=1 AND '.$src.'!=0 AND '.$src.'<2000 ORDER BY RAND() ASC '.($_GET['full']==''?'LIMIT 10':''));
while($row=$db->fetch($res))
{
	$proxy[]=$row['proxy'];
}
if ($_GET['type']=='') $memcache->set('proxylist', json_encode($proxy), MEMCACHE_COMPRESSED, 1800);
echo json_encode($proxy);

?>