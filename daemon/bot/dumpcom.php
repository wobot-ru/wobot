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

//echo $where;
//die();
$ressec=$db->query('SELECT * FROM blog_post where order_id=310');
echo 'new orders to cache: '.mysql_num_rows($ressec)."\n";
$mode='wb';
while($blog=$db->fetch($ressec))
{
	echo '("'.addslashes($blog['post_link']).'", "'.addslashes($blog['post_time']).'", "'.addslashes(str_replace("\n\n","\n",$blog['post_content'])).'"),'."\n";
}
?>
