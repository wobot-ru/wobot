<?

require_once('/var/www/com/config.php');
require_once('/var/www/com/func.php');
require_once('/var/www/com/db.php');
require_once('/var/www/bot/kernel.php');

date_default_timezone_set('Europe/Moscow');
error_reporting(E_ERROR);
ignore_user_abort(true);
set_time_limit(0);
ini_set('max_execution_time',0);
ini_set('default_charset','utf-8');
ob_implicit_flush();

$db = new database();
$db->connect();

$filename = 'last_id.txt';
$handle = fopen($filename, "rb");
$last_id = intval(trim(fread($handle, filesize($filename))));
fclose($handle);

do
{
	$qyt=$db->query('SELECT post_id,a.order_id,post_link FROM blog_post as a LEFT JOIN blog_orders AS b ON a.order_id=b.order_id WHERE post_host=\'youtube.com\' AND post_id>'.$last_id.' AND b.order_end>'.time().' ORDER BY post_id ASC LIMIT 100');
	while ($yt=$db->fetch($qyt))
	{
		$last_id=$yt['post_id'];
        $fp = fopen('last_id.txt', 'w');
        fwrite($fp, $last_id);
        fclose($fp);
		// echo $yt['post_link']."\n";
		$regex='/[\?\&]v\=(?<id>[a-z0-9\-\_]*)/isu';
		preg_match_all($regex, $yt['post_link'], $out);
		if ($out['id'][0]=='') 
		{
			echo $yt['post_link'];
			continue;
		}
		// print_r($out);
		echo 'SELECT * FROM youtube_id WHERE ind=\''.$out['id'][0].'\' AND ord_id='.$yt['order_id']."\n";
		$count_pst++;
		$isset=$db->query('SELECT * FROM youtube_id WHERE ind=\''.$out['id'][0].'\' AND ord_id='.$yt['order_id']);
		if ($db->num_rows($isset)==0) $db->query('INSERT INTO youtube_id (ind,ord_id) VALUES (\''.$out['id'][0].'\','.$yt['order_id'].')');
	}
}	
while ($db->num_rows($qyt)!=0);

$headers  = "From: noreply2@wobot.ru\r\n"; 
$headers .= "Bcc: noreply2@wobot.ru\r\n";
$headers .= 'MIME-Version: 1.0' . "\r\n";
$headers .= 'Content-type: text/html; charset=utf-8'."\r\n";
mail('zmei123@yandex.ru','Сборщик youtube collection',$count_pst,$headers);

?>