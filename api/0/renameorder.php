<?
require_once('/var/www/new/com/config.php');
require_once('/var/www/new/com/func.php');
require_once('/var/www/new/com/db.php');
require_once('/var/www/new/bot/kernel.php');
require_once('auth.php');
require_once('/var/www/com/loc.php');

$redis=new Redis() or die("Can'f load redis module.");
$redis->connect('127.0.0.1');
set_log('renameorder',$_POST);

date_default_timezone_set ( 'Europe/Moscow' );
$db = new database();
$db->connect();

auth();

if (!$loged) die();

//$_POST=$_GET;

if ((mb_strlen($_POST['order_name'],'UTF-8')>3) && ($_POST['order_id']!=''))
{
	$qisset=$db->query('SELECT * FROM blog_orders WHERE order_id='.intval($_POST['order_id']).' AND (user_id='.$user['user_id'].' OR ut_id='.$user['ut_id'].')');
	if ($db->num_rows($qisset)!=0)
	{
		$qw='UPDATE blog_orders SET order_name=\''.addslashes(preg_replace('/[^а-яА-Яa-zA-ZёЁ0-9\s]/isu','',$_POST['order_name'])).'\' WHERE order_id='.intval($_POST['order_id']).' AND user_id='.$user['user_id'];
		//echo $qw;
		//die();
		$db->query($qw);
		$mas['status']='ok';
		echo json_encode($mas);
		die();
	}
	else
	{
		$outmas['status']=2;
		echo json_encode($outmas);
		die();
	}
}
else
{
	$outmas['status']=1;
	echo json_encode($outmas);
	die();
}

?>