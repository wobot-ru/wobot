<?

require_once('/var/www/com/config.php');
require_once('/var/www/com/db.php');
require_once('/var/www/com/auth.php');

$db = new database();
$db->connect();

$last_bills_q="SELECT b.user_id, user_email, money, months, FROM_UNIXTIME( DATE ) as date, t.tariff_name FROM  `billing` AS b LEFT JOIN blog_tariff AS t ON b.tariff_id = t.tariff_id LEFT JOIN users AS u ON u.user_id = b.user_id WHERE  `status`=2 ORDER BY b.`bill_id` DESC LIMIT ".$_GET['num_bills'];
$lb_res=$db->query($last_bills_q);
while ($bil=$db->fetch($lb_res))
{
	$outmas['bill'][]=$bil;
	// echo '<tr><td>905'.$bil['bill_id'].'</td><td>'.date('d.m.y H:i:s',$bil['date']).'</td><td>'.$status_bil[$bil['status']].'</td><td>'.intval($bil['money']).'</td></tr>';
	// $bil_id++;
}


die(json_encode($outmas));

?>