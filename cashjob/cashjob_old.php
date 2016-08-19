<?
require_once('com/config.php');
require_once('com/db.php');
require_once('com/func.php');
require_once('com/geo.php');
require_once('bot/kernel.php');
require_once('/var/www/api/0/rfunc.php');
require_once('/var/www/new/com/func.php');

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
//$redis->set('order_712', $var);
//$var=$redis->get('order_712');
$qorder=$db->query('SELECT order_id,order_name,order_keyword,order_start,order_end,order_settings,cash_update FROM blog_orders WHERE order_id='.intval($_SERVER['argv'][1]));
$order=$db->fetch($qorder);

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
echo $start.' '.$end."\n";
for($t=$start;$t<=$end;$t=mktime(0,0,0,date("n",$t),date("j",$t)+1,date("Y",$t)))
{
	get_cash($order,$t);
	//$var=$redis->get('order_'.$_SERVER['argv'][1].'_'.$t);
	//echo $var;
	echo intval((($t-$start)/($end-$start))*100).'% '.$t.' done'."\n";
}
get_orders_cash($_SERVER['argv'][1]);
$order['cash_update']=time();
$db->query('UPDATE blog_orders SET cash_update='.$order['cash_update'].' WHERE order_id='.intval($_SERVER['argv'][1]));
//refresh_memcash($_SERVER['argv'][1],$start,$end);
$out=get_order_cash($order,date('d.m.Y',$order['order_start']),date('d.m.Y',($order['order_end']>time()?mktime(0,0,0,date('n'),date('j'),date('Y')):$order['order_end'])));
echo $out['cash_update'];
$memcache->set('order_'.$order['order_id'].'_null_null', json_encode($out), MEMCACHE_COMPRESSED, 86400);
//echo 'order_'.$order['order_id'].'_'.($order['order_start']*1000).'_'.(($order['order_end']>time()?mktime(0,0,0,date('n'),date('j'),date('Y')):$order['order_end'])*1000);
//echo date('d.m.Y',$order['order_start']).' '.date('d.m.Y',($order['order_end']>time()?mktime(0,0,0,date('n'),date('j'),date('Y')):$order['order_end']));
?>