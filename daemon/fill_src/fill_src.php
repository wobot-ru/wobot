<?

require_once('/var/www/daemon/com/db.php');
require_once('/var/www/daemon/com/config.php');

$db=new database();
$db->connect();

$filename = '/var/www/daemon/fill_src/last_fill_src_id.txt';
$handle = fopen($filename, "rb");
$last_id = trim(fread($handle, filesize($filename)));
fclose($handle);

$qpost=$db->query('SELECT post_id,post_host FROM blog_post WHERE post_id>'.$last_id.' GROUP BY post_host ORDER BY post_id DESC');
while ($post=$db->fetch($qpost))
{
	echo '.';
	$last_id=$post['post_id'];
	$qisset=$db->query('SELECT * FROM blog_src WHERE src_host=\''.addslashes($post['post_host']).'\' LIMIT 1');
	if ($db->num_rows($qisset)==0) $db->query('INSERT INTO blog_src (src_host,src_time) VALUES (\''.addslashes($post['post_host']).'\','.time().')');
}

$fp = fopen('/var/www/daemon/fill_src/last_fill_src_id.txt', 'w');
fwrite($fp, $last_id);
fclose($fp);

?>