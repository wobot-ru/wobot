<?

$fp = fopen('cache_load.log', 'a');
fwrite($fp, date('r')."\n");
fclose($fp);

$memcache = memcache_connect('localhost', 11211);

$cache=json_decode(base64_decode($_POST['cache']),true);

foreach ($cache as $key => $item)
{
	$memcache->set($key, $item, MEMCACHE_COMPRESSED, 86400);
}

?>