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
ini_set("memory_limit", "2048M");
$db = new database();
$db->connect();

$redis=new Redis() or die("Can'f load redis module.");
$redis->connect('127.0.0.1');

auth();
set_log('wnsi',$_POST);
if ((!$loged) && ($user['tariff_id']==3)) die();
if ($_POST['start']=='') $_POST['start']=$_POST['stime'];
if ($_POST['end']=='') $_POST['end']=$_POST['etime'];
$query=get_query();
// echo $query;
//------------Подсчет доли ресурса------------
// $query=preg_replace('/SELECT\s\*/isu', 'SELECT count(post_host) as cnt,post_host', $query);
// $query=preg_replace('/ORDER BY/isu', 'GROUP BY post_host ORDER BY', $query);
// // echo $query;
// $qpost=$db->query($query);
// while ($post=$db->fetch($qpost))
// {
// 	$mcount_hosts[$post['post_host']]+=$post['cnt'];
// 	$mcount_hosts2[$post['post_host']]+=$post['cnt'];
// 	$count_posts++;
// }
// foreach ($mcount_hosts as $key => $item)
// {
// 	$mcount_hosts[$key]/=$count_posts;
// }
//------------------------------------------

//---------Подсчет NSI,доли ресурсов-------------

$query=preg_replace('/SELECT \*/isu', 'SELECT post_host,post_nastr', $query);
$qpost=$db->query($query);
while ($post=$db->fetch($qpost))
{
	if (!isset($mnsi[$post['post_host']])) $mnsi[$post['post_host']]=0;
	switch ($post['post_nastr']) 
	{
	    case 0:
	        $mnsi[$post['post_host']]++;
	        break;
	    case -1:
	        $mnsi[$post['post_host']]--;
	        break;
	    case 1:
	        $mnsi[$post['post_host']]++;
	        break;
	}
	// if ($post['post_host']=='twimg.com') print_r($mnsi);
	$mcount_hosts[$post['post_host']]++;
	$count_posts++;
}
foreach ($mnsi as $key => $item)
{
	$mnsi[$key]/=$mcount_hosts[$key];
}
foreach ($mcount_hosts as $key => $item)
{
	$mcount_hosts[$key]/=$count_posts;
}

//-------------------------------

//-----------Получение значимости ресурса--------

$qweight_host=$db->query('SELECT * FROM blog_host_weight WHERE order_id='.$_POST['order_id']);
while ($weight_host=$db->fetch($qweight_host))
{
	$mweight_host[$weight_host['host_name']]=$weight_host['host_weight'];
}

//-----------------------------------------------

foreach ($mcount_hosts as $key => $item)
{
	$sum_wnsi_chisl+=$mnsi[$key]*$mcount_hosts[$key]*($mweight_host[$key]==''?50:$mweight_host[$key]);
	$sum_wnsi_znam+=$mcount_hosts[$key]*($mweight_host[$key]==''?50:$mweight_host[$key]);
}

echo json_encode(array('wnsi'=>intval($sum_wnsi_chisl*100/$sum_wnsi_znam)/100));

?>