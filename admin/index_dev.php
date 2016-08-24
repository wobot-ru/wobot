<?

require_once('com/config.php');
require_once('com/db.php');
require_once('com/auth.php');

$db = new database();
$db->connect();

auth();
if (!$loged) die();
else header('Location: /admin/last/');

?>