<?
require_once('/var/www/new/com/config.php');
require_once('/var/www/new/com/func.php');
require_once('/var/www/new/com/db.php');
require_once('/var/www/new/bot/kernel.php');
require_once('auth.php');
require_once('rfunc.php');
require_once('func_export.php');
require_once( '/var/www/new/com/phpmorphy/src/common.php');

$dir = '/var/www/new/com/phpmorphy/dicts';
$lang = 'ru_RU';
$opts = array( 'storage' => PHPMORPHY_STORAGE_FILE );
$morphy = new phpMorphy($dir, $lang, $opts);
//ini_set("memory_limit", "2048M");
date_default_timezone_set ( 'Europe/Moscow' );
ignore_user_abort(true);
set_time_limit(0);
ini_set('max_execution_time',0);
ob_implicit_flush();
ini_set("memory_limit", "8192M");
error_reporting(0);
$db = new database();
$db->connect();

$redis=new Redis() or die("Can'f load redis module.");
$redis->connect('127.0.0.1');

// error_reporting(0);
//die();
//print_r($_POST);

$k=0;
$kk=0;
$kkk=0;
$kkkk=0;
$arr_nau['vk.com']=1;
$arr_nau['vkontakte.ru']=1;
$arr_nau['facebook.com']=1;
$arr_nau['livejournal.com']=1;
$arr_nau['liveinternet.ru']=1;
$arr_nau['mail.ru']=1;
$arr_nau['ya.ru']=1;
$arr_nau['yandex.ru']=1;
$arr_nau['rutwit.ru']=1;
$arr_nau['rutvit.ru']=1;
$arr_nau['babyblog.ru']=1;
$arr_nau['blog.ru']=1;
$arr_nau['foursquare.com']=1;
$arr_nau['kp.ru']=1;
$arr_nau['aif.ru']=1;
$arr_nau['friendfeed.ru']=1;
//$user['user_id']=61;
if (($_POST['start']>1000000000000) && ($_POST['end']>1000000000000))//ТРОЛОЛОЛОШЕЧКИ
{
	$_POST['start']='null';
	$_POST['end']='null';
}
auth();
set_log('order',$_POST);
//$user['user_id']=145;
//$user['tariff_id']=1;
//$_POST['order_id']=722;
//$_POST['start']='27.03.2012';
//$_POST['end']='15.04.2012';
//print_r($user);
//echo $loged;
//if (!$loged) die();
if ((!$loged) && ($user['tariff_id']==3)) die();
if (!isset($_GET['order_id']))
{
	$_GET=$_POST;
}
//echo 123;
//print_r($_GET);
$c=0;

function promosort($a, $b)
{
    if ($a['count'] == $b['count']) {
        return 0;
    }
    return ($a['count'] < $b['count']) ? 1 : -1;
}

if ((strtotime($_GET['start'])!=0) && (strtotime($_GET['end'])!=0))
{
	if ($user['tariff_id']==3)
	{
		$res=$db->query('SELECT * FROM blog_orders WHERE order_id='.intval($_GET['order_id']).' AND (user_id=61 or user_id='.intval($user['user_id']).' or user_id='.$user['user_mid'].')');
		//echo 'SELECT * FROM blog_orders WHERE order_id='.intval($_GET['order_id']).' AND (user_id=61 or user_id='.intval($user['user_id']).')';
	}
	else
	{
		$res=$db->query('SELECT * FROM blog_orders WHERE order_id='.intval($_GET['order_id']).' AND (user_id='.intval($user['user_id']).' OR user_id='.$user['user_mid'].')');
		// echo 'SELECT * FROM blog_orders WHERE order_id='.intval($_GET['order_id']).' AND (user_id='.intval($user['user_id']).' OR user_id='.$user['user_mid'].')';
	}
}
else
{
	if ($user['tariff_id']==3)
	{
		$res=$db->query('SELECT * FROM blog_orders WHERE order_id='.intval($_GET['order_id']).' AND (user_id=61 or user_id='.intval($user['user_id']).' or user_id='.$user['user_mid'].')');
		//echo 'SELECT * FROM blog_orders WHERE order_id='.intval($_GET['order_id']).' AND (user_id=61 or user_id='.intval($user['user_id']).')';
	}
	else
	{
		$res=$db->query('SELECT * FROM blog_orders WHERE order_id='.intval($_GET['order_id']).' AND (user_id='.intval($user['user_id']).' OR user_id='.$user['user_mid'].')');
		//echo 'SELECT * FROM blog_orders WHERE order_id='.intval($_GET['order_id']).' AND user_id='.intval($user['user_id']);
	}
}
//echo 'SELECT order_id,order_name,order_keyword,order_start,order_end,order_last,third_sources,order_metrics,order_src,order_graph FROM blog_orders WHERE order_id='.intval($_GET['order_id']).' AND user_id='.intval($user['user_id']);
while ($row = $db->fetch($res)) 
{
	if ($row['order_end']>mktime(0,0,0,date('n'),date('j'),date('Y'))) $row['order_end']=mktime(0,0,0,date('n'),date('j'),date('Y'));
	if ((strtotime($_GET['end'])==0) && (strtotime($_GET['start'])==0))
	{
		$_GET['start']=date('j.n.Y',$row['order_start']);
		$_GET['end']=date('j.n.Y',$row['order_end']);
	}
	if ($_SERVER['HTTP_REFERER']=='http://'.$_SERVER['HTTP_HOST'].'/messages_list.html')
	{
		$out['start']=date('d.m.Y',$row['order_start']);
		$out['end']=date('d.m.Y',($row['order_end']==0)?$row['order_last']:($row['order_end']>time()?time():$row['order_end']));
		$out['order_name']=$row['order_name'];
		for($t=strtotime($_GET['start']);$t<=strtotime($_GET['end']);$t=mktime(0,0,0,date("n",$t),date("j",$t)+1,date("Y",$t)))
		{
			$var=$redis->get('order_'.$row['order_id'].'_'.$t);
			$m_dinams=json_decode($var,true);
			$count_post_per+=$m_dinams['count_post'];
		}
		$out['posts']=formatint($count_post_per);
		echo json_encode($out);
		die();
	}
	$memcache = memcache_connect('localhost', 11211);
	if (!is_nulled_filer()) $out1=$memcache->get('order_'.$_POST['order_id'].'_'.$_POST['start'].'_'.$_POST['end']);
	elseif ((strtotime($_POST['start'])==$row['order_start']) && (strtotime($_POST['end'])==$row['order_end'])) $out1=$memcache->get('order_'.$_POST['order_id'].'_null_null');

	if ($out1!='') die($out1);

	if (is_nulled_filer()) $out=get_order_cash($row,$_GET['start'],$_GET['end']);
	else
	{
		// echo 123;
		$query=get_query();
		$out=get_statistics($query);
	}
}

echo json_encode($out);
?>