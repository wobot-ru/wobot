#!/usr/bin/php
<?
require_once('/var/www/com/config.php');
require_once('/var/www/com/func.php');
require_once('/var/www/com/db.php');
require_once('/var/www/com/memcache.php');
require_once('/var/www/bot/kernel.php');

error_reporting(E_ERROR | E_WARNING | E_PARSE);
ignore_user_abort(true);
set_time_limit(0);
ini_set('max_execution_time',0);
ini_set('default_charset','utf-8');
ob_implicit_flush();

function convert($size)
 {
    $unit=array('b','kb','mb','gb','tb','pb');
    return @round($size/pow(1024,($i=floor(log($size,1024)))),2).' '.$unit[$i];
 }

echo 'memory: '.convert(memory_get_usage(true))."\n";

$db = new database();
$db->connect();

$memcache = memcache_connect('localhost', 11211);

//$memcache->delete(md5("mysql_query" . 'SELECT * FROM blog_post LIMIT 1000'));
//memcache_add($memcache, md5("mysql_query" . 'SELECT * FROM blog_post LIMIT 100000'), 'test variable', false, 30);
/*
set value of item with key 'var_key'
using 0 as flag value, compression is not used
expire time is 30 seconds
*/
//memcache_set($memcache, md5("mysql_query" . 'SELECT * FROM blog_post LIMIT 100000'), 'some variable', false, 30);
//print_r(memcache_get($memcache,md5("mysql_query" . 'SELECT * FROM blog_post LIMIT 100000')));
//$ressec=$db->query('SELECT * FROM blog_post');
//echo 'new orders to parse: '.mysql_num_rows($ressec)."\n";
//$mode='wb';

$time_start = microtime(true);
$sql='SELECT * from blog_post as p LEFT JOIN robot_blogs2 AS b ON p.blog_id=b.blog_id WHERE p.order_id="310" ORDER BY p.post_time DESC LIMIT 10';
$rSlowQuery = $db->query($sql);
//$rows = mysql_num_rows($rSlowQuery);
$i=0;
while($row=$db->fetch($rSlowQuery))
{
	$rows[$i]=$row;
	$i++;
}
echo "rows: ".count($rows)."\n";
echo 'memory: '.convert(memory_get_usage(true))."\n";
$time_end = microtime(true);
$time = $time_end - $time_start;
echo "time: $time seconds\n";

unset($rSlowQuery);
unset($rows);
unset($row);
unset($i);

$time_start = microtime(true);
$sql='SELECT * from blog_post as p LEFT JOIN robot_blogs2 AS b ON p.blog_id=b.blog_id WHERE p.order_id="310" ORDER BY p.post_time DESC LIMIT 10';
$rSlowQuery = mysql_query_cache($sql);
$rows = count($rSlowQuery);
//var_dump($rSlowQuery);
echo "rows: $rows\n";
echo 'memory: '.convert(memory_get_usage(true))."\n";
$time_end = microtime(true);
$time = $time_end - $time_start;

echo "time: $time seconds\n";
?>