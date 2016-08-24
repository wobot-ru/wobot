<?

require_once('/var/www/com/config.php');
require_once('/var/www/com/db.php');
require_once('/var/www/com/auth.php');

$db = new database();
$db->connect();

$qtariff=$db->query('SELECT * FROM blog_tariff');
while ($tariff=$db->fetch($qtariff))
{
	// print_r($order);
	if (in_array($tariff['tariff_id'], array('12','13','14','15','16','17'))) $outmas['tariff'][]=$tariff;
}
die(json_encode($outmas));

?>