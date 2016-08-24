<html>
<body>
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

if (($_POST['keyword1']!='')/*&&($_POST['keyword2']!='')*/)
{
$keyword1=$_POST['keyword1'];
$keyword2=$_POST['keyword2'];
$interval=$_POST['interval'];

$db->query('UPDATE riw_setup set setup_keyword1="'.($keyword1).'", setup_keyword2="'.($keyword2).'", setup_interval="'.($interval).'" WHERE setup_id=1');
}
$res=$db->query('SELECT * FROM riw_setup WHERE setup_id=1');
$row=$db->fetch($res);

$keyword1=$row['setup_keyword1'];
$keyword2=$row['setup_keyword2'];
$interval=$row['setup_interval'];

echo '
	<form method="post">
	Ключевое слово 1: <input type="text" name="keyword1" value="'.$keyword1.'"><br>
	Ключевое слово 2: <input type="text" name="keyword2" value="'.$keyword2.'"><br>
	Интервал: <input type="text" name="interval" value="'.$interval.'"><br>
	<input type="submit" value="применить">
	</form>
';

?>

</body>
</html>