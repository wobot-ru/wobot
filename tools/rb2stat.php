<html>
<head>
<script type="text/JavaScript">
<!--
function timedRefresh(timeoutPeriod) {
	setTimeout("location.reload(true);",timeoutPeriod);
}
//   -->
</script>
</head>
<body onload="JavaScript:timedRefresh(15000);">
<?
date_default_timezone_set ( 'Europe/Moscow' );
require_once('/var/www/com/config.php');
require_once('/var/www/com/func.php');
require_once('/var/www/com/db.php');
require_once('/var/www/bot/kernel.php');
$db = new database();
$db->connect();
$deltatime=7;
echo 'robot_blogs2 stats:<br>';
$rru=$db->query('SELECT count(blog_id) as cnt FROM robot_blogs2');
while($pst = $db->fetch($rru))
{
	echo 'All:	'.$pst['cnt'];
	echo '<br>';
}
$rru=$db->query('SELECT count(blog_id) as cnt FROM robot_blogs2 WHERE blog_last_update>'.(time()-$deltatime*86400));
while($pst = $db->fetch($rru))
{
	echo 'Actual:	'.$pst['cnt'];
	echo '<br>';
}
$rru=$db->query('SELECT count(blog_id) as cnt FROM robot_blogs2 WHERE blog_last_update<'.(time()-$deltatime*86400).' and blog_last_update!=0');
while($pst = $db->fetch($rru))
{
	echo 'Non actual/Old:	<font color="red">'.($pst['cnt']-$k).'</font>';
	echo '<br>';
}
$rru=$db->query('SELECT count(blog_id) as cnt FROM robot_blogs2 WHERE blog_last_update=0');
while($pst = $db->fetch($rru))
{
	echo 'Non processed:	<font color="red">'.$pst['cnt'].'</font>';
	$k=$pst['cnt'];
	echo '<br><br>';
}
echo 'SRC UPDATE:<br>';
$rru=$db->query('SELECT count(blog_id) as cnt,blog_link as cnt1 FROM `robot_blogs2` WHERE `blog_last_update`<'.(time()-$deltatime*86400).' AND `blog_last_update`!=0 GROUP BY blog_link');
while($pst = $db->fetch($rru))
{
	echo $pst['cnt1'].'  <font color="red">'.$pst['cnt'].'</font>';
	echo '<br>';
}
echo '<br>location stats:<br>';
$rru=$db->query('SELECT count(blog_id) as cnt FROM robot_blogs2 WHERE blog_location!=0');
while($pst = $db->fetch($rru))
{
	echo 'Geo detected:	'.$pst['cnt'];
	$k=$pst['cnt'];
	echo '<br>';
}
$rru=$db->query('SELECT count(blog_id) as cnt FROM robot_blogs2 WHERE blog_location=\'\' and blog_last_update!=0');
while($pst = $db->fetch($rru))
{
	echo 'Geo non detected:	<font color="red">'.$pst['cnt'].'</font>';
	$k=$pst['cnt'];
	echo '<br>';
}
echo '<br>';
//echo 'Engagment stats:<br>';
$kk=0;
$wh_orid.='(order_id=';
$rru1=$db->query('SELECT order_id FROM blog_orders WHERE order_engage=1');
while($bo = $db->fetch($rru1))
{
	$kk++;
	if ($kk>1)
	{
		$wh_orid.=' OR order_id=';
	}
	$wh_orid.=$bo['order_id'];
}
$wh_orid.=')';
//echo $wh_orid;
//$rru=$db->query('SELECT count(*) as cnt FROM blog_post WHERE '.$wh_orid.' AND (post_host=\'twitter.com\' OR post_host=\'livejournal.com\' OR post_host=\'vkontakte.ru\' OR post_host=\'facebook.com\')');
/*$rru=$db->query('SELECT count(post_id) as cnt FROM blog_post as a left join blog_orders as b on a.order_id=b.order_id where b.order_engage!=0 and  (post_host=\'twitter.com\' OR post_host=\'livejournal.com\' OR post_host=\'vkontakte.ru\' OR post_host=\'vk.com\' OR post_host=\'facebook.com\')');
//echo 'SELECT count(*) as cnt FROM blog_post WHERE '.$wh_orid.' AND (post_host=\'twitter.com\' OR post_host=\'livejournal.com\' OR post_host=\'vkontakte.ru\' OR post_host=\'facebook.com\')';
while($pst = $db->fetch($rru))
{
	$count_all+=$pst['cnt'];
}
//$rru=$db->query('SELECT count(*) as cnt FROM blog_post WHERE '.$wh_orid.' AND engage_update!=0 AND (post_host=\'twitter.com\' OR post_host=\'livejournal.com\' OR post_host=\'vkontakte.ru\' OR post_host=\'facebook.com\')');
$rru=$db->query('SELECT count(post_id) as cnt FROM blog_post as a left join blog_orders as b on a.order_id=b.order_id where a.engage_update!=0 and b.order_engage!=0 and  (post_host=\'twitter.com\' OR post_host=\'livejournal.com\' OR post_host=\'vkontakte.ru\' OR post_host=\'vk.com\' OR post_host=\'facebook.com\')');
while($pst = $db->fetch($rru))
{
	//echo 'All:	'.$pst['cnt'];
	//echo '<br>';
	$without+=$pst['cnt'];
}*/
/*
$rru=$db->query('SELECT count(*) as cnt FROM blog_post WHERE order_id='.$bo['order_id']);
while($pst = $db->fetch($rru))
{
	//echo 'All:	'.$pst['cnt'];
	//echo '<br>';
	$count_all+=$pst['cnt'];
}
$rru=$db->query('SELECT count(*) as cnt FROM blog_post WHERE post_engage=0 AND order_id='.$bo['order_id']);
while($pst = $db->fetch($rru))
{
	//echo 'Without engagement or engagement=:	'.$pst['cnt'];
	//echo '<br>';
	$without+=$pst['cnt'];
}
$rru=$db->query('SELECT count(*) as cnt FROM blog_post WHERE post_engage!=0 AND order_id='.$bo['order_id']);
while($pst = $db->fetch($rru))
{
	//echo 'With engagement:	'.($pst['cnt']);
	//echo '<br>';
	$with+=$pst['cnt'];
}
*/
/*echo 'All:	'.$count_all;
echo '<br>';
echo 'With engagement:	'.$without;
echo '<br>';
echo 'Without engagement:	'.($count_all-$without);
echo '<br>';*/
$rru=$db->query('SELECT * from blog_log as p LEFT JOIN users AS b ON p.user_id=b.user_id ORDER BY log_time DESC LIMIT 50');
while($pst = $db->fetch($rru))
{
	$mas[]['time']=$pst['log_time'];
	$mas[count($mas)-1]['ip']=$pst['log_ip'];
	$mas[count($mas)-1]['uid']=$pst['user_id'];
	$mas[count($mas)-1]['email']=$pst['user_email'];
	$mas1[$pst['user_email']]++;
}
//print_r($mas);
echo '<br>Entering to LK:<br>';
foreach ($mas as $key => $item)
{
	echo ($key+1).'. <i>'.date('d.m.y H:i:s',$item['time']).'</i> <a href="http://www.geoiptool.com/en/?IP='.$item['ip'].'">'.$item['ip'].'</a> '.$item['email'].'<br>';
}
arsort($mas1);
echo '<br>Top Entries<br>';
$k=0;
foreach ($mas1 as $key => $item)
{
	if ($k<10)
	{
		$k++;
		echo ($k).'. '.$key.' '.$item.'<br>';
	}
}
?>
<br>
15 sec auto refresh
	</body>
	</html>
