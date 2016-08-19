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

function get_mas($id,$day)
{
	//$day=$day+0*86400;
	global $db;
	$ressec=$db->query('SELECT * FROM blog_post WHERE order_id='.$id.' AND (post_time>='.($day).' AND post_time<'.($day+86400).')');
	//echo 'SELECT * FROM blog_post WHERE order_id='.$id.' AND (post_time>'.$day.' AND post_time<'.($day+86400).')';
	for ($i=0;$i<23;$i++)
	{
		$outmas['time'][$i]=0;
	}
	while($blog=$db->fetch($ressec))
	{
		$outmas['time'][intval(date("H",$blog['post_time']))]++;
	}
	ksort($outmas['time']);
	print_r($outmas);
}
get_mas(525,1317931200);
?>
