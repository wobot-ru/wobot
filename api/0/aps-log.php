<?

error_reporting(0);

require_once('/var/www/new/com/config.php');
require_once('/var/www/new/com/func.php');
require_once('/var/www/new/com/db.php');
require_once('/var/www/new/bot/kernel.php');

$db = new database();
$db->connect();

$res=$db->query('SELECT aps_value FROM aps_log ORDER BY aps_id DESC LIMIT 20');
while ($row=$db->fetch($res))
{
	echo $row['aps_value']."\n";
}
?>