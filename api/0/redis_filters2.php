<?

require_once('/var/www/new/com/config.php');
require_once('/var/www/new/com/func.php');
require_once('/var/www/new/com/db.php');
require_once('/var/www/new/bot/kernel.php');
require_once('auth.php');
require_once('rfunc.php');
require_once( '/var/www/new/com/phpmorphy/src/common.php');

$dir = '/var/www/new/com/phpmorphy/dicts';
$lang = 'ru_RU';
$opts = array( 'storage' => PHPMORPHY_STORAGE_FILE );
$morphy = new phpMorphy($dir, $lang, $opts);
ini_set("memory_limit", "2048M");
date_default_timezone_set ( 'Europe/Moscow' );
error_reporting(0);
$db = new database();
$db->connect();

$redis=new Redis() or die("Can'f load redis module.");
$redis->connect('127.0.0.1');

auth();
if (!$loged) die();
set_log('filters',$_POST);
$memcache = memcache_connect('localhost', 11211);
//echo 'filters_'.$_POST['order_id'].'_'.strtotime($_POST['start']).'_'.strtotime($_POST['end']);
$out1=$memcache->get('filters_'.$_POST['order_id'].'_'.strtotime($_POST['start']).'_'.strtotime($_POST['end']));
//echo 'order_'.$_POST['order_id'].'_'.strtotime($_POST['start']).'_'.strtotime($_POST['end']);
if ($out1!='')
{
	// echo $out1;
	// die();
}

//$user['user_id']=61;
$k=0;
$kk=0;
$kkk=0;
$kkkk=0;
/*$_GET['order_id']=793;
$_GET['start']='25.05.2012';
$_GET['end']='27.05.2012';
$user['user_id']=145;
$user['tariff_id']=3;*/
if (!isset($_GET['order_id']))
{
	$_GET=$_POST;
}
if ($user['tariff_id']==3)
{
	$infus=$db->query('SELECT order_id,user_id FROM blog_orders WHERE order_id='.$_GET['order_id'].' LIMIT 1');
	$usri=$db->fetch($infus);
	if ($usri['user_id']==61)
	{
		$user['user_id']=61;
	}
	else
	{
		
	}
}
//print_r($user);
//echo 123;
$res=$db->query('SELECT * FROM blog_orders WHERE order_id='.intval($_GET['order_id']).' AND (user_id='.intval($user['user_id']).' OR user_id='.intval($user['user_mid']).')');
//echo 'SELECT * FROM blog_orders WHERE order_id='.intval($_GET['order_id']).' AND user_id='.intval($user['user_id']);
while ($row = $db->fetch($res)) 
{
	if ((($_GET['start']=='null') && ($_GET['end']=='null')) || (($_GET['start']=='') && ($_GET['end']=='')))
	{
		$_GET['start']=date('d.m.Y',$row['order_start']);
		$_GET['end']=date('d.m.Y',$row['order_end']>time()?mktime(0,0,0,date('n'),date('j'),date('Y')):$row['order_end']);
	}
	if ((strtotime($_GET['start'])!=0) && (strtotime($_GET['end'])!=0))
	{
		if ($row['order_beta']!='')
		{
			$m_dinams=json_decode($row['order_beta'],true);
		}
		else
		{
			$memcache = memcache_connect('localhost', 11211);
			$m_dinams = json_decode(memcache_get($memcache, 'order_'.$row['order_id']),true);
			/*$m1 = new Memcached();
			$m1->addServer('localhost', 11211);
			$m1->setOption(Memcached::OPT_COMPRESSION, false);
			$m_dinams=json_decode($m1->get(('order_'.$row['order_id'])),true);*/
		}
	}
	$out=get_filters_cash($row,$_GET['start'],$_GET['end']);
	$out['start']=date('d.m.Y',$row['order_start']);
	$out['end']=date('d.m.Y',($row['order_end']==0)?$row['order_last']:($row['order_end']>time()?time():$row['order_end']));
	$out['order_name']=$row['order_name'];
	$out['dup']=($row['similar_text']<=1?0:1);
}

echo json_encode($out);

?>