<?

require_once('/var/www/com/config.php');
require_once('/var/www/com/db.php');
require_once('/var/www/com/auth.php');

$db = new database();
$db->connect();

if (intval($_GET['payed'])==0) 
{
	$quser=$db->query('SELECT * FROM users as a LEFT JOIN user_tariff as b ON a.user_id=b.user_id WHERE user_email LIKE \'%'.$_GET['search_user'].'%\' ORDER BY user_email');
	while ($user=$db->fetch($quser))
	{
		$outmas['user'][]=$user;
	}
}
else
{
	$quser=$db->query('SELECT * FROM users as a LEFT JOIN user_tariff as b ON a.user_id=b.user_id WHERE user_email LIKE \'%'.$_GET['search_user'].'%\' AND b.tariff_id!=16 AND b.ut_date>'.mktime(0,0,0,date('n'),date('j'),date('Y')).' ORDER BY user_email');
	while ($user=$db->fetch($quser))
	{
		$outmas['user'][]=$user;
	}
}

die(json_encode($outmas));

?>