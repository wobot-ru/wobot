<?

require_once('/var/www/new/com/config.php');
require_once('/var/www/new/com/func.php');
require_once('/var/www/new/com/db.php');
require_once('/var/www/new/bot/kernel.php');
//require_once('auth.php');

$db = new database();
$db->connect();

$outmas['top_loc'][]='Москва';
$outmas['top_loc'][]='Санкт-петербург';
$outmas['top_loc'][]='Пермь';
$outmas['top_loc'][]='Новосибирск';
$outmas['top_loc'][]='Киев';
$outmas['top_loc'][]='Казань';
$outmas['top_loc'][]='Нижний Новгород';
$outmas['top_loc'][]='Самара';

foreach ($wobot['destn2'] as $key => $item)
{
	if (in_array($key, $outmas['top_loc'])) continue;
	$outmas['loc'][]=$key;
}

echo json_encode($outmas);

?>