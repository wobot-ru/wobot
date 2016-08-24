<?
require_once('/var/www/new/com/config.php');
require_once('/var/www/new/com/func.php');
require_once('/var/www/new/com/db.php');
require_once('/var/www/new/bot/kernel.php');
require_once('auth.php');

date_default_timezone_set ( 'Europe/Moscow' );

function check_order_kw($kw)
{
	$mcount_open=explode('(', $kw);
	$mcount_close=explode(')', $kw);
	if ((preg_match('/\(.*?\).*/isu',$kw)) && (count($mcount_open)==2) && (count($mcount_close)==2))
	{
		$regex='/\((?<mkw>.*?)\)/isu';
		preg_match_all($regex, $kw, $out);
		$mw_temp=explode('|', $out['mkw'][0]);
		foreach ($mw_temp as $item)
		{
			if (trim($item)=='') continue;
			$mw[]=trim($item);
		}
		$regex='/\~+(?<mew>[^\~]*)/isu';
		preg_match_all($regex, $kw, $out);
		foreach ($out['mew'] as $item)
		{
			if (trim($item)=='') continue;
			$mew[]=trim($item);
		}
		$regex='/\)(?<mnw>.*?)\~/isu';
		preg_match_all($regex, $kw, $out);
		//print_r($out);
		if (isset($out['mnw'][0]))
		{
			$mnw_temp=explode('&&', $out['mnw'][0]);
			foreach ($mnw_temp as $item)
			{
				if (trim($item)=='') continue;
				$mnw[]=trim($item);
			}
		}
		else
		{
			$regex='/\)(?<mnw>.*)/isu';
			preg_match_all($regex, $kw, $out);
			$mnw_temp=explode('&&', $out['mnw'][0]);
			foreach ($mnw_temp as $item)
			{
				if (trim($item)=='') continue;
				$mnw[]=trim($item);
			}
		}
		$outmas['mw']=$mw;
		$outmas['mnw']=$mnw;
		$outmas['mew']=$mew;
		return $outmas;
	}
	else
	{
		unset($outmas);
		$outmas['mkw']=$kw;
		return $outmas;
	}
}

$db = new database();
$db->connect();

//$_POST=$_GET;

$av['remove_spam']=1;

//echo $_COOKIE['user_id'];
auth();
if (!$loged) die();

$redis=new Redis() or die("Can'f load redis module.");
$redis->connect('127.0.0.1');
set_log('get_theme_settings',$_POST);
		
//print_r($_POST);

$qus=$db->query('SELECT user_id,ut_id FROM blog_orders WHERE order_id='.intval($_POST['order_id']));
$us=$db->fetch($qus);
//print_r($us);
//print_r($users);
//$user['user_id']=1103;
//$us['user_id']=1103;
if ($us['ut_id']!=$user['ut_id'])
{
	$out['status']=2;
	echo json_encode($out);
	die();	
}

if (intval($_POST['order_id'])==0)
{
	$out['status']=1;
	echo json_encode($out);	
	die();
}

$qorder=$db->query('SELECT order_id,order_settings,order_keyword,order_name,order_nastr,user_id,order_start,order_end FROM blog_orders WHERE order_id='.$_POST['order_id']);
$order=$db->fetch($qorder);
$order['order_settings']=json_decode($order['order_settings'],true);
$order['order_settings']['order_keyword']=check_order_kw($order['order_keyword']);
//$order['order_settings']['order_keyword']=$order['order_keyword'];
$order['order_settings']['order_name']=$order['order_name'];
$order['order_settings']['auto_nastr']=intval($order['order_nastr']);
$order['order_settings']['disable_theme']=($order['user_id']==0?1:0);
$order['order_settings']['order_start']=$order['order_start'];
$order['order_settings']['order_end']=$order['order_end'];
echo json_encode($order['order_settings']);

?>