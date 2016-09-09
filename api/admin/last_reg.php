<?

require_once('/var/www/com/config.php');
require_once('/var/www/com/db.php');
require_once('/var/www/com/auth.php');

$db = new database();
$db->connect();

$res=$db->query('SELECT a.order_id,a.user_id AS user_id,a.order_name,a.order_keyword, b.user_active,b.user_email,b.user_pass,b.user_name,b.user_ctime,b.user_company,b.user_contact,b.user_promo, c.ut_date, c.tariff_id, COUNT( a.order_id ) AS tnum, b.user_id AS real_user_id,b.ref FROM blog_orders AS a LEFT JOIN users AS b ON a.user_id = b.user_id LEFT JOIN user_tariff AS c ON a.user_id=c.user_id GROUP BY a.user_id ORDER BY a.user_id DESC LIMIT 100');
while ($statuser=$db->fetch($res))
{
	if ($statuser['user_email']=='') continue;
	//print_r($statuser);
	if ($statuser['order_id']>intval($stus[$statuser['user_id']]['order_id']))
	{
		$stus[$statuser['user_id']]['order_id']=$statuser['order_id'];
		$stus[$statuser['user_id']]['user_id']=$statuser['user_id'];
		$stus[$statuser['user_id']]['order_name']=$statuser['order_name'];
		$stus[$statuser['user_id']]['order_keyword']=$statuser['order_keyword'];
		$stus[$statuser['user_id']]['user_active']=$statuser['user_active'];
		$stus[$statuser['user_id']]['user_email']=$statuser['user_email'];
		$stus[$statuser['user_id']]['token']=md5(mb_strtolower($statuser['user_email'],'UTF-8').':'.$statuser['user_pass']);
		$stus[$statuser['user_id']]['user_name']=$statuser['user_name'];
		$stus[$statuser['user_id']]['user_ctime']=$statuser['user_ctime'];
		$stus[$statuser['user_id']]['user_company']=$statuser['user_company'];
		$stus[$statuser['user_id']]['user_contact']=$statuser['user_contact'];
		$stus[$statuser['user_id']]['user_promo']=$statuser['user_promo'];
		$stus[$statuser['user_id']]['ut_date']=$statuser['ut_date'];
		$stus[$statuser['user_id']]['tnum']=$statuser['tnum'];
		$stus[$statuser['user_id']]['ref']=$statuser['ref'];
	}
}
$res=$db->query('SELECT * FROM users AS a LEFT JOIN user_tariff AS c ON a.user_id=c.user_id ORDER BY a.user_id DESC LIMIT 100');
while ($row=$db->fetch($res))
{
	if ($row['user_email']=='') continue;
	if (!isset($stus[$row['user_id']]))
	{
		$stus[$row['user_id']]['order_id']=0;
		$stus[$row['user_id']]['user_id']=$row['user_id'];
		$stus[$row['user_id']]['user_active']=$row['user_active'];
		$stus[$row['user_id']]['user_email']=$row['user_email'];
		$stus[$row['user_id']]['user_pass']=md5(mb_strtolower($row['user_email'],'UTF-8').':'.$row['user_pass']);
		$stus[$row['user_id']]['user_name']=$row['user_name'];
		$stus[$row['user_id']]['user_ctime']=$row['user_ctime'];
		$stus[$row['user_id']]['user_company']=$row['user_company'];
		$stus[$row['user_id']]['user_contact']=$row['user_contact'];
		$stus[$row['user_id']]['user_promo']=$row['user_promo'];
		$stus[$row['user_id']]['ut_date']=$row['ut_date'];
		$stus[$row['user_id']]['ref']=$row['ref'];
		$stus[$row['user_id']]['token']=md5(mb_strtolower($row['user_email'],'UTF-8').':'.$row['user_pass']);
	}
}
krsort($stus);

die(json_encode($stus));

?>