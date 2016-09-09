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
	if ((preg_match('/^\([^\&]*?\).*/isu',$kw)) && (count($mcount_open)==2) && (count($mcount_close)==2))
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
		// print_r($outmas);
		return $outmas;
	}
	else
	{
		unset($outmas);
		$outmas['mkw'][0]=$kw;
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
if ($user['user_mid']==0)
{
	if ($us['ut_id']!=$user['ut_id'])
	{
		$out['status']=2;
		echo json_encode($out);
		die();	
	}
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
if (!preg_match('/\@\d+\@/isu',$order['order_keyword'])) $order['order_settings']['order_keyword']=check_order_kw($order['order_keyword']);
else
{
	$regex='/\@(?<obj_id>\d+)\@/isu';
	// echo $order['order_keyword'];
	preg_match_all($regex, $order['order_keyword'], $out);
	// print_r($out);
	$qobject=$db->query('SELECT * FROM blog_object WHERE object_id='.$out['obj_id'][0].' LIMIT 1');
	$object=$db->fetch($qobject);
	$object['length']=mb_strlen($object['object_keyword'],'UTF-8');
	$order['order_settings']['order_keyword']['mko']=$object;
}
//$order['order_settings']['order_keyword']=$order['order_keyword'];
$order['order_settings']['order_name']=$order['order_name'];
$order['order_settings']['auto_nastr']=(intval($order['order_nastr'])==0?0:(intval($order['order_nastr'])==1?2:1));
$order['order_settings']['disable_theme']=($order['user_id']==0?1:0);
$order['order_settings']['order_start']=$order['order_start'];
$order['order_settings']['order_end']=$order['order_end'];
if ($order['order_settings']['cou']==null) $order['order_settings']['cou']=array('loc_Россия','loc_зарубежье');
$authors['type']=$order['order_settings']['author_type'];
$authors['data']=($order['order_settings']['author']==null?null:$order['order_settings']['author']);
$srcs['type']=$order['order_settings']['res_type'];
$srcs['data']=($order['order_settings']['res']==null?null:$order['order_settings']['res']);
$order['order_settings']['author_type']=$authors;
$order['order_settings']['res_type']=$srcs;
//$order['order_settings']['loc']=$srcs;
echo json_encode($order['order_settings']);

?>
