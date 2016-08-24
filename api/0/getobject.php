<?
require_once('/var/www/new/com/config.php');
require_once('/var/www/new/com/func.php');
require_once('/var/www/new/com/db.php');
require_once('/var/www/new/bot/kernel.php');
require_once('/var/www/com/checker.php');
require_once('auth.php');
require_once('/var/www/com/loc.php');

$redis=new Redis() or die("Can'f load redis module.");
$redis->connect('127.0.0.1');
set_log('getobject',$_POST);

date_default_timezone_set ( 'Europe/Moscow' );
$db = new database();
$db->connect();
auth();
if (!$loged) die();

// $_POST['query']='(("сим"|"sim"|simauto|("sim" /+1 auto))&(автозапчасти|(авто /+1 (запчасти|дилер|салон))|дилер|автодилер|диллер|автодиллер|автосалон))';

$_POST['query']=preg_replace('/[^а-яa-zё0-9]/isu',' ',$_POST['query']);
$mkw=explode(' ', $_POST['query']);
// print_r($mkw);
foreach ($mkw as $keyword)
{
	if (mb_strlen($keyword,'UTF-8')>2) 
	{
		$query_like.=$or.' object_keyword LIKE \'%'.$keyword.'%\'';
		$or=' OR ';
	}
}
$qobject=$db->query('SELECT * FROM blog_object WHERE '.$query_like);
// echo 'SELECT * FROM blog_object WHERE '.$query_like;
while ($object=$db->fetch($qobject))
{
	// print_r($object);
	$object['length']=mb_strlen($object['object_keyword'],'UTF-8');
	$outmas[]=$object;
}

echo json_encode($outmas);

?>