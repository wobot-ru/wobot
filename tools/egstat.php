<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html lang="ru-RU" xml:lang="ru-RU" xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <title>Утилиты для тестирования WOBOT &beta;</title>
    <meta content="" name="description" />
    <meta content="Wobot" name="keywords" />
    <meta content="Wobot media" name="author" />
    <meta content="all" name="robots" />
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
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

require_once('/var/www/userjob/com/config.php');
require_once('/var/www/userjob/com/func.php');
require_once('/var/www/userjob/com/db.php');
require_once('/var/www/userjob/bot/kernel.php');
$db = new database();
$db->connect();
$deltatime=7;
$rru1=$db->query('SELECT * FROM blog_orders WHERE order_engage=1');
while($bo = $db->fetch($rru1))
{
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
}
echo 'All:	'.$count_all;
echo '<br>';
echo 'Without engagement or engagement=:	'.$without;
echo '<br>';
echo 'With engagement:	'.$with;
echo '<br>';
?>
<br>
15 sec auto refresh
	</body>
	</html>
