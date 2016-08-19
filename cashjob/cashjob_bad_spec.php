<?
require_once('com/config.php');
require_once('com/db.php');
require_once('com/func_spec.php');
require_once('com/geo.php');
require_once('bot/kernel.php');
require_once('/var/www/api/0/rfunc.php');
require_once('/var/www/new/com/func.php');
require_once( '/var/www/new/com/phpmorphy/src/common.php');

$dir = '/var/www/new/com/phpmorphy/dicts';
$lang = 'ru_RU';
$opts = array( 'storage' => PHPMORPHY_STORAGE_FILE );
$morphy = new phpMorphy($dir, $lang, $opts);

proc_nice(10);

date_default_timezone_set ( 'Europe/Moscow' );
error_reporting(E_ERROR | E_PARSE);
ignore_user_abort(true);
set_time_limit(0);
ini_set('max_execution_time',0);
ini_set('default_charset','utf-8');
ob_implicit_flush();

$db = new database();
$db->connect();

$redis=new Redis() or die("Can'f load redis module.");
$redis->connect('127.0.0.1');
$memcache = memcache_connect('localhost', 11211);

//-------максимальное количество кешей в 1 момент-----
// $max_count_cash=3;
// do
// {
// 	sleep(1);
// 	$count_cash=intval($redis->get('count_cash'));
// 	echo 'repeat...'.$count_cash."\n";
// }
// while ($count_cash>=$max_count_cash);
// $redis->incr('count_cash');
//----------------------------------------------------

//$redis->set('order_712', $var);
//$var=$redis->get('order_712');
if ($_SERVER['argv'][1][0]!='s')
{
	$qorder=$db->query('SELECT order_id,order_name,order_keyword,order_start,order_end,order_settings,cash_update,ful_com,similar_text,user_id FROM blog_orders WHERE order_id='.intval($_SERVER['argv'][1]).' LIMIT 1');
	$order=$db->fetch($qorder);
}
else
{
	$mid=explode('s', $_SERVER['argv'][1]);
	$suborder_id=$mid[1];
	$qorder=$db->query('SELECT a.order_id,order_name,order_keyword,order_start,order_end,ful_com,order_settings,similar_text,b.cash_update,b.subtheme_settings,b.subtheme_id,a.user_id FROM blog_orders as a LEFT JOIN blog_subthemes as b ON a.order_id=b.order_id WHERE b.subtheme_id='.intval($suborder_id).' LIMIT 1');
	$order=$db->fetch($qorder);
}

// if (($order['user_id']==2039) || ($order['user_id']==2064)) die();

if (!isset($_SERVER['argv'][2]))
{
	$start=$order['order_start'];
}
else
{
	$start=$_SERVER['argv'][2];
}
if (!isset($_SERVER['argv'][3]))
{
	$end=($order['order_end']==0?time():$order['order_end']);
}
else
{
	$end=$_SERVER['argv'][3];
}
if ($end>time()) $end=time();
if (($_SERVER['argv'][1][0]=='s') && ($order['cash_update'])==0)
{
	$subtheme_settings=json_decode($order['subtheme_settings'],true);
	$start=$subtheme_settings['suborder_start'];
	$end=$subtheme_settings['suborder_end'];
}
echo $start.' '.$end."\n";
$_GET['order_id']=$_SERVER['argv'][1];
$_GET['start']=date('n.j.Y',$start);
$_GET['end']=date('n.j.Y',$end);
for($t=$start;$t<=$end;$t=mktime(0,0,0,date("n",$t),date("j",$t)+1,date("Y",$t)))
{
	get_cash($order,$t,($suborder_id!=''?'s':''),json_decode($order['subtheme_settings'],true));
	//$var=$redis->get('order_'.$_SERVER['argv'][1].'_'.$t);
	//echo $var;
	echo intval((($t-$start)/($end-$start))*100).'% '.$t.' done'."\n";
}

get_orders_cash(($_SERVER['argv'][1][0]=='s'?$suborder_id:$_SERVER['argv'][1]),($suborder_id!=''?'s':''));
$order['cash_update']=time();
$db->query('UPDATE blog_orders SET cash_update='.$order['cash_update'].' WHERE order_id='.intval($_SERVER['argv'][1]));
if ($_SERVER['argv'][1][0]=='s') $db->query('UPDATE blog_subthemes SET cash_update='.$order['cash_update'].' WHERE subtheme_id='.intval($suborder_id));
//refresh_memcash($_SERVER['argv'][1],$start,$end);
$out=get_order_cash($order,date('d.m.Y',$order['order_start']),date('d.m.Y',($order['order_end']>time()?mktime(0,0,0,date('n'),date('j'),date('Y')):$order['order_end'])));
echo $out['cash_update'];
$memcache->set('order_'.$order['order_id'].'_null_null', json_encode($out), MEMCACHE_COMPRESSED, 86400);

$out=get_filters_cash($order,date('d.m.Y',$order['order_start']),date('d.m.Y',($order['order_end']>time()?mktime(0,0,0,date('n'),date('j'),date('Y')):$order['order_end'])));

$out['start']=date('d.m.Y',$order['order_start']);
$out['end']=date('d.m.Y',($order['order_end']==0)?$order['order_last']:($order['order_end']>time()?time():$order['order_end']));
$out['order_name']=$order['order_name'];
$out['dup']=($order['similar_text']<=1?0:1);

if ($order['order_end']>time()) $order['order_end']=mktime(0,0,0,date('n'),date('j'),date('Y'));
$memcache->set('filters_'.$order['order_id'].'_'.$order['order_start'].'_'.$order['order_end'], json_encode($out), MEMCACHE_COMPRESSED, 86400);

// $redis->decr('count_cash');

//echo 'order_'.$order['order_id'].'_'.($order['order_start']*1000).'_'.(($order['order_end']>time()?mktime(0,0,0,date('n'),date('j'),date('Y')):$order['order_end'])*1000);
//echo date('d.m.Y',$order['order_start']).' '.date('d.m.Y',($order['order_end']>time()?mktime(0,0,0,date('n'),date('j'),date('Y')):$order['order_end']));
?>