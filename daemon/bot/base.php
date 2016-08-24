<?
require_once('/var/www/com/config.php');
require_once('/var/www/com/func.php');
require_once('/var/www/com/db.php');
require_once('/var/www/bot/kernel.php');

error_reporting(E_ERROR | E_PARSE);
ignore_user_abort(true);
set_time_limit(0);
ini_set('max_execution_time',0);
ini_set('default_charset','utf-8');
ob_implicit_flush();

$db = new database();
$db->connect();

$ressec=$db->query('SELECT `post_link` FROM `blog_post`');
echo 'new orders to cache: '.mysql_num_rows($ressec)."\n";
$mode='wb';
while($blog=$db->fetch($ressec))
{
	$url=parse_url($blog['post_link']);
	$urls[$url['host']]++;
}

foreach ($urls as $url => $count)
{
	echo $url.' '.$count."\n";
}
?>