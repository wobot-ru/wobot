<?

require_once('/var/www/com/db.php');
require_once('/var/www/com/config.php');

error_reporting(0);

$redis=new Redis() or die("Can'f load redis module.");
$redis->connect('127.0.0.1');

$db=new database();
$db->connect();

$cache=$redis->get('cacher');
$mcache=json_decode($cache,true);
$tm=mktime(date('H'),date('i')-1,0,date('n'),date('j'),date('Y'));
$td=mktime(0,0,0,date('n'),date('j')-1,date('Y'));
$q=$db->query('SELECT blog_id FROM robot_blogs2 WHERE blog_last_update<'.mktime(0,0,0,date('n'),date('j')-7,date('Y')).' AND blog_last_update!=0');
$mcache['account_toprocess']['hour'][$tm]=$db->num_rows($q);
$q=$db->query('SELECT blog_id FROM robot_blogs2 WHERE blog_last_update=0');
$mcache['account_null']['hour'][$tm]=$db->num_rows($q);
$q=$db->query('SELECT blog_id FROM robot_blogs2 WHERE blog_last_update>='.$tm.' AND blog_last_update<'.($tm+60));
$mcache['account']['hour'][$tm]=$db->num_rows($q);
$q=$db->query('SELECT blog_id FROM robot_blogs2 WHERE blog_last_update>='.($td+86400));
$mcache['account']['day'][$td+86400]=$db->num_rows($q);
if ((date('H')==0) && (!isset($mcache['account']['day'][$td])))
{
	$q=$db->query('SELECT blog_id FROM robot_blogs2 WHERE blog_last_update>='.$td.' AND blog_last_update<'.($td+86400));
	$mcache['account']['day'][$td]=$db->num_rows($q);
}
// print_r($mcache);
$cache_eng=$redis->get('cacher_eng');
$mcache_eng=json_decode($cacher_eng,true);
$eng_cache=shell_exec('tail -n 2 /var/www/daemon/logs/et_cashlog_*.log');
$meng_cache=explode("\n", $eng_cache);
foreach ($meng_cache as $item)
{
	$regex='/(?<id>\d+)\s(?<time>\d+)\s(?<count>\d+)/isu';
	preg_match_all($regex, $item, $out);
	echo $out['time'][0].' '.$tm."\n";
	if (!isset($mcache_eng[$out['id'][0]][$out['time'][0]]))
	{
		$mcache['engage']['hour'][$out['time'][0]]+=$out['count'][0];
		$mcache_eng[$out['id'][0]][$out['time'][0]]=$out['count'][0];
	}
}
$redis->set('cacher_eng', json_encode($mcache_eng));
$cache_ful=$redis->get('cacher_ful');
$mcache_ful=json_decode($cache_ful,true);
$fj_cache=shell_exec('tail -n 2 /var/www/daemon/logs/fj_cashlog_*.log');
$mfj_cache=explode("\n", $fj_cache);
foreach ($mfj_cache as $item)
{
	$regex='/(?<id>\d+)\s(?<time>\d+)\s(?<count>\d+)/isu';
	preg_match_all($regex, $item, $out);
	if (!isset($mcache_ful[$out['id'][0]][$out['time'][0]]))
	{
		$mcache['fulljob']['hour'][$out['time'][0]]+=$out['count'][0];
		$mcache_ful[$out['id'][0]][$out['time'][0]]=$out['count'][0];
	}
}
unset($out);
$q_rt_order=$db->query('SELECT order_id,user_pass,user_email,a.user_id,third_sources FROM blog_orders AS a LEFT JOIN users AS b ON a.user_id=b.user_id WHERE order_end>'.mktime(0,0,0,date('n'),date('j'),date('Y')).' AND a.user_id!=145 AND a.user_id!=0');
while ($rt_order=$db->fetch($q_rt_order))
{
	$count_post_all=0;
	for ($t=mktime(0,0,0,date('n'),date('j')-5,date('Y'));$t<=mktime(0,0,0,date('n'),date('j'),date('Y'));$t+=86400)
	{
		// echo $t.' ';
		$info_user[$rt_order['order_id']]['user_email']=$rt_order['user_email'];
		$info_user[$rt_order['order_id']]['user_pass']=$rt_order['user_pass'];
		$info_user[$rt_order['order_id']]['user_id']=$rt_order['user_id'];
		$info_user[$rt_order['order_id']]['third_sources']=$rt_order['third_sources'];
		$var=$redis->get('order_'.$rt_order['order_id'].'_'.$t);
		$m_dinams=json_decode($var,true);
		$out['graph'][$rt_order['order_id']][$t]=intval($m_dinams['count_post']);
		$count_post_all+=intval($m_dinams['count_post']);
		$last_day=intval($m_dinams['count_post']);
	}
	$t-=86400;
	$avg_count_post_all=intval($count_post_all/6);
	if ((intval($last_day*100/$avg_count_post_all)<10)&&($count_post_all!=0)&&($avg_count_post_all>5))
	{
		$out['crit_graph'][$rt_order['order_id']]['order_id']=$rt_order['order_id'];
		$out['crit_graph'][$rt_order['order_id']]['user_email']=$info_user[$rt_order['order_id']]['user_email'];
		$out['crit_graph'][$rt_order['order_id']]['user_pass']=$info_user[$rt_order['order_id']]['user_pass'];
		$out['crit_graph'][$rt_order['order_id']]['third_sources']=$info_user[$rt_order['order_id']]['third_sources'];
		$out['crit_graph'][$rt_order['order_id']]['user_id']=$info_user[$rt_order['order_id']]['user_id'];
		$out['crit_graph'][$rt_order['order_id']]['values']=$out['graph'][$rt_order['order_id']];
		// print_r($out['graph'][$rt_order['order_id']]);
		// echo $rt_order['order_id'].'|';
		// echo $avg_count_post_all.'|';
		// echo intval($last_day*100/$avg_count_post_all).'|'.$out['graph'][$rt_order['order_id']][$t].' ';
	}
	elseif ((intval($last_day*100/$avg_count_post_all)<50)&&($count_post_all>10))
	{
		$out['warning_graph'][$rt_order['order_id']]['order_id']=$rt_order['order_id'];
		$out['warning_graph'][$rt_order['order_id']]['user_email']=$info_user[$rt_order['order_id']]['user_email'];
		$out['warning_graph'][$rt_order['order_id']]['user_pass']=$info_user[$rt_order['order_id']]['user_pass'];
		$out['warning_graph'][$rt_order['order_id']]['third_sources']=$info_user[$rt_order['order_id']]['third_sources'];
		$out['warning_graph'][$rt_order['order_id']]['user_id']=$info_user[$rt_order['order_id']]['user_id'];
		$out['warning_graph'][$rt_order['order_id']]['values']=$out['graph'][$rt_order['order_id']];
	}
	else
	{
		$out['norm_graph'][$rt_order['order_id']]['order_id']=$rt_order['order_id'];
		$out['norm_graph'][$rt_order['order_id']]['user_email']=$info_user[$rt_order['order_id']]['user_email'];
		$out['norm_graph'][$rt_order['order_id']]['user_pass']=$info_user[$rt_order['order_id']]['user_pass'];
		$out['norm_graph'][$rt_order['order_id']]['third_sources']=$info_user[$rt_order['order_id']]['third_sources'];
		$out['norm_graph'][$rt_order['order_id']]['user_id']=$info_user[$rt_order['order_id']]['user_id'];
		$out['norm_graph'][$rt_order['order_id']]['values']=$out['graph'][$rt_order['order_id']];
	}
}
// print_r($out['crit_graph']);
$mcache['crit_order']=$out['crit_graph'];
$mcache['warning_order']=$out['warning_graph'];
$mcache['norm_order']=$out['norm_graph'];
$redis->set('cacher_ful', json_encode($mcache_ful));
$redis->set('cacher', json_encode($mcache));

?>