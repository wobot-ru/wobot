<?

require_once('/var/www/com/config.php');
require_once('/var/www/com/db.php');
require_once('/var/www/com/auth.php');

$db = new database();
$db->connect();

$quser=$db->query('SELECT * FROM users as a LEFT JOIN user_tariff as b ON a.user_id=b.user_id LEFT JOIN blog_tariff as c ON b.tariff_id=c.tariff_id WHERE a.user_id='.$_GET['user_id']);
$user=$db->fetch($quser);
$outmas['user']=$user;
// echo 'SELECT * FROM blog_orders WHERE (user_id='.$user['user_id'].' AND ut_id='.$user['ut_id'].') OR (user_id=0 AND ut_id='.$user['ut_id'].')';
$qorder=$db->query('SELECT * FROM blog_orders WHERE (user_id='.$user['user_id'].' AND ut_id='.$user['ut_id'].') OR (user_id=0 AND ut_id='.$user['ut_id'].')');
while ($order=$db->fetch($qorder))
{
	// print_r($order);
	$outmas['orders'][]=$order;
}
$qlog=$db->query('SELECT * FROM blog_log WHERE user_id='.$user['user_id'].' ORDER BY log_time DESC');
while ($log=$db->fetch($qlog))
{
	$outmas['log'][]=$log;
}

$qbilling=$db->query('SELECT * FROM billing as a LEFT JOIN blog_tariff as b ON a.tariff_id=b.tariff_id WHERE a.user_id='.$_GET['user_id']);
while ($bil=$db->fetch($qbilling))
{
	$outmas['bil']=$bil;
}

die(json_encode($outmas));

?>