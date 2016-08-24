<?

require_once('/var/www/new/com/db.php');
require_once('/var/www/new/com/config.php');
require_once('lib.php');

$db=new database();
$db->connect();

ini_set('memory_limit', '8192M');

$order_id=$_SERVER['argv'][1];

if (intval(exec('ps ax | grep "dumper.php" | wc -l'))>8) die();
if (intval(exec('ps ax | grep "recover.php '.$order_id.'" | wc -l'))>3) die();

// $order_id=135;
$qorder=$db->query('SELECT * FROM blog_orders WHERE order_id='.intval($order_id).' LIMIT 1');
$order=$db->fetch($qorder);
if ($order['order_id']==0) die();
if ($order['ut_id']==0) die();

$db->query('UPDATE blog_orders SET ut_id=0 WHERE user_id='.$order['user_id'].' AND order_id='.$order_id);
echo 'UPDATE blog_orders SET ut_id=0 WHERE user_id='.$order['user_id'].' AND order_id='.$order_id."\n";

$bp=get_blog_post($order_id);
$bfc=get_blog_full_com($order_id);

// echo $bfc;
// die();

$dir='/var/www/tools/archiver/data/'.$order_id;
if (!is_dir($dir))
{
	if (!mkdir($dir, 0777, true)) 
	{
    	die('Не удалось создать директории...');
	}
}

$fp = fopen($dir.'/blog_post.sql', 'w');
fwrite($fp, $bp);
fclose($fp);

$fp = fopen($dir.'/blog_full_com.sql', 'w');
fwrite($fp, $bfc);
fclose($fp);

if (file_exists($dir.'/blog_post.sql')&&file_exists($dir.'/blog_full_com.sql'))
{
	$db->query('DELETE FROM blog_post WHERE order_id='.$order_id);
	echo 'DELETE FROM blog_post WHERE order_id='.$order_id."\n";
	$db->query('DELETE FROM blog_full_com WHERE ful_com_order_id='.$order_id);
	echo 'DELETE FROM blog_full_com WHERE ful_com_order_id='.$order_id."\n";
}

?>