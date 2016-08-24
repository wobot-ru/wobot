<?

require_once('/var/www/com/db.php');
require_once('/var/www/com/config.php');

$db=new database();
$db->connect();

$qtoken=$db->query('SELECT * FROM tp_keys WHERE type=\''.$_GET['type'].'\' AND in_use=3 ORDER BY RAND()');
while ($token=$db->fetch($qtoken))
{
	if ($_GET['type']!='tw') $outmas[]=$token['key'];
	else $outmas[]=json_decode($token['key'],true);
}
shuffle($outmas);
echo json_encode($outmas);

?>