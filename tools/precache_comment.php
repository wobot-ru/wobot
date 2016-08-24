<?
die();
if ($_SERVER['argv'][1]==2877) die();
require_once('/var/www/api/0/rfunc.php');
require_once('/var/www/new/com/config.php');
require_once('/var/www/new/com/db.php');
require_once('/var/www/new/com/func.php');
require_once( '/var/www/new/com/phpmorphy/src/common.php');

$dir = '/var/www/new/com/phpmorphy/dicts';
$lang = 'ru_RU';
$opts = array( 'storage' => PHPMORPHY_STORAGE_FILE );
$morphy = new phpMorphy($dir, $lang, $opts);

ini_set("memory_limit", "2048M");
date_default_timezone_set ( 'Europe/Moscow' );
error_reporting(0);

$memcache = memcache_connect('localhost', 11211);

function get_post_resp($url,$query)
{
	$ch = curl_init();

	//set the url, number of POST vars, POST data
	curl_setopt($ch,CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_POST, 1);
	 
	//теперь curl вернет нам ответ, а не выведет
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch,CURLOPT_POSTFIELDS, $query);

	//execute post
	$result = curl_exec($ch);

	//close connection
	curl_close($ch);
	return $result;
}

$memcache = memcache_connect('localhost', 11211);

$db = new database();
$db->connect();

$redis=new Redis() or die("Can'f load redis module.");
$redis->connect('127.0.0.1');

$qorder=$db->query('SELECT order_id,order_start,order_end,a.user_id,b.user_email,b.user_pass,b.user_settings FROM blog_orders as a LEFT JOIN users as b ON a.user_id=b.user_id WHERE order_id='.$_SERVER['argv'][1].' LIMIT 1');
$order=$db->fetch($qorder);

$settings=json_decode($order['order_settings'],true);

if ($settings['perpage']=='') $perpage=10;
else $perpage=$settings['perpage'];

$user_id=$order['user_id'];
$token=md5(mb_strtolower($order['user_email'],'UTF-8').':'.$order['user_pass']);

if ($order['order_end']>time()) $order['order_end']=mktime(0,0,0,date('n'),date('j'),date('Y'));

$param['order_id']=$_SERVER['argv'][1];
$param['page']='0';
$param['stime']=date('d.m.Y',$order['order_start']);
$param['etime']=date('d.m.Y',$order['order_end']);
$param['sort']='null';
$param['positive']='true';
$param['negative']='true';
$param['neutral']='true';
$param['post_type']='null';
$param['md5']='';
$param['perpage']=(string)$perpage;
$param['Promotions']='selected';
$param['words']='selected';
$param['tags']='selected';

// echo http_build_query($param);

$out=get_post_resp('http://production.wobot.ru/api/0/comment?test_user_id='.$user_id.'&test_token='.$token,http_build_query($param));

$memcache->set('comment_'.md5(json_encode($param)), $out, MEMCACHE_COMPRESSED, 300);
echo 'comment_'.json_encode($param);
// echo 'precache_filter_'.$order['order_id'].'_'.$order['order_start'].'_'.$order['order_end'];
// $out1=$memcache->get('filters_'.$order['order_id'].'_'.$order['order_start'].'_'.$order['order_end']);
// echo $out1;

?>