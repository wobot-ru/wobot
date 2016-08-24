<?

require_once('/var/www/com/db.php');
require_once('/var/www/com/config.php');

$db=new database();
$db->connect();

$quser=$db->query('SELECT * FROM users as a LEFT JOIN user_tariff as b ON a.user_id=b.user_id WHERE ut_date<'.mktime(0,0,0,date('n'),date('j'),date('Y')-1).' AND tariff_id=3 AND a.user_id!=145 AND a.user_email NOT LIKE \'%_DELETED\' ');
while ($user=$db->fetch($quser))
{
	$muser[$user['user_id']]['count']++;
	$muser[$user['user_id']]['user_email']=$user['user_email'];
	$muser[$user['user_id']]['user_id']=$user['user_id'];
}

// print_r($muser);

foreach ($muser as $key => $item)
{
	if ($muser[$key]['count']>1) continue;
	echo 'UPDATE users SET user_email=CONCAT(user_email,\'_DELETED\') WHERE user_id='.$key."\n";
}

?>