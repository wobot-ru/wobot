<?

require_once('/var/www/com/config.php');
require_once('/var/www/com/func.php');
require_once('/var/www/com/db.php');
require_once('/var/www/bot/kernel.php');

$db = new database();
$db->connect();

$isset=$db->query('SELECT post_id FROM blog_post WHERE post_host=\''.addslashes($_GET['src']).'\' LIMIT 1');

if ($db->num_rows($isset)==0) echo 'нет';
else echo 'есть';

?>