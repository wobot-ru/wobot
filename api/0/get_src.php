<?
require_once('/var/www/new/com/config.php');
require_once('/var/www/new/com/func.php');
require_once('/var/www/new/com/db.php');
require_once('/var/www/new/bot/kernel.php');
require_once('auth.php');

date_default_timezone_set ( 'Europe/Moscow' );

$db = new database();
$db->connect();

//$_POST=$_GET;

auth();
if (!$loged) die();

$redis=new Redis() or die("Can'f load redis module.");
$redis->connect('127.0.0.1');
set_log('get_src',$_POST);

$inf=$db->query('SELECT * FROM user_src WHERE user_id='.$user['user_id']);
$kk=0;
while ($src=$db->fetch($inf))
{
	$outmas['src'][$kk]['link']=$src['hn'];
	if (intval($src['count'])!=0)
	{
		$outmas['src'][$kk]['status']=0;
	}
	else
	{
		if (intval($src['update'])!=0)
		{
			$outmas['src'][$kk]['status']=1;//нельзя подключить
		}
		else
		{
			$outmas['src'][$kk]['status']=2;//не собрано
		}
	}
	$kk++;
}
echo json_encode($outmas);
?>