<?

require_once('/var/www/admin/com/config_origin.php');
require_once('/var/www/admin/com/db_origin.php');
require_once('/var/www/admin/com/config.php');
require_once('/var/www/admin/com/db.php');
require_once('/var/www/daemon/com/users.php');

// print_r($config);
$db=new database();
$db->connect();

$config_origin['db']['host']=$_SERVER['argv'][2];
// print_r($config_origin);
$db_origin=new database_origin();
$db_origin->connect();


// $q=$db->query('SELECT * FROM users WHERE user_id=4185');
// $qw=$db->fetch($q);
// print_r($qw);
// $q2=$db_origin->query('SELECT * FROM users WHERE user_id=4185');
// $qw2=$db_origin->fetch($q2);
// print_r($qw2);
// die();

$quser=$db_origin->query('SELECT * FROM users WHERE user_id='.$_SERVER['argv'][1].' LIMIT 1');
$user=$db_origin->fetch($quser);
$quser_tariff=$db_origin->query('SELECT * FROM user_tariff WHERE user_id='.$_SERVER['argv'][1].' LIMIT 1');
$user_tariff=$db_origin->fetch($quser_tariff);
// print_r($user_tariff);
// die();
foreach ($user as $key => $value)
{
	if ($key=='user_id') continue;
	$qkeys.=$zapkey.$key;
	$qvalue.=$zapvalue.'\''.addslashes($value).'\'';
	$zapvalue=',';
	$zapkey=',';
}
// echo 'INSERT INTO users ('.$qkeys.') VALUES ('.$qvalue.')'."\n";
$qinsert_user_id=$db->query('INSERT INTO users ('.$qkeys.') VALUES ('.$qvalue.')');
$insert_user_id=$db->insert_id($qinsert_user_id);
// echo 'INSERT INTO user_tariff (user_id,ut_date,tariff_id) VALUES ('.$insert_user_id.','.$user_tariff['ut_date'].','.$user_tariff['tariff_id'].')'."\n";
$qinsert_ut_id=$db->query('INSERT INTO user_tariff (user_id,ut_date,tariff_id) VALUES ('.$insert_user_id.','.$user_tariff['ut_date'].','.$user_tariff['tariff_id'].')');
$insert_ut_id=$db->insert_id($qinsert_ut_id);
$qorders=$db_origin->query('SELECT * FROM blog_orders WHERE user_id='.$_SERVER['argv'][1]);
while ($order=$db_origin->fetch($qorders))
{
	$qkeys='';
	$qvalue='';
	$zapvalue='';
	$zapkey='';
	foreach ($order as $key => $value)
	{
		if ($key=='order_id') continue;
		if ($key=='user_id')
		{
			$qkeys.=$zapkey.addslashes('user_id');
			$qvalue.=$zapvalue.'\''.addslashes($insert_user_id).'\'';
			$zapvalue=',';
			$zapkey=',';
			continue;
		}
		if ($key=='ut_id')
		{
			$qkeys.=$zapkey.addslashes('ut_id');
			$qvalue.=$zapvalue.'\''.addslashes($insert_ut_id).'\'';
			$zapvalue=',';
			$zapkey=',';
			continue;
		}
		$qkeys.=$zapkey.addslashes($key);
		$qvalue.=$zapvalue.'\''.addslashes($value).'\'';
		$zapvalue=',';
		$zapkey=',';
	}
	// echo 'INSERT INTO blog_orders ('.$qkeys.') VALUES ('.$qvalue.')'."\n";
	$qinsert_order=$db->query('INSERT INTO blog_orders ('.$qkeys.') VALUES ('.$qvalue.')');
	$insert_order_id=$db->insert_id($qinsert_order);
	$qpost=$db_origin->query('SELECT * FROM blog_post as a LEFT JOIN blog_full_com as b ON a.post_id=b.ful_com_post_id WHERE order_id='.$order['order_id']);
	while ($post=$db_origin->fetch($qpost))
	{
		$user=new users();
		$blog_id=$user->get_nick($post['post_link']);
		// echo 'INSERT INTO blog_post (order_id,post_time,post_host,post_content,post_link,blog_id,post_nastr,post_spam,post_fav,post_tag) VALUES ('.$insert_order_id.',\''.addslashes($post['post_time']).'\',\''.addslashes($post['post_host']).'\',\''.addslashes($post['post_content']).'\',\''.addslashes($post['post_link']).'\',\''.addslashes($blog_id).'\',\''.addslashes($post['post_nastr']).'\',\''.addslashes($post['post_spam']).'\',\''.addslashes($post['post_fav']).'\',\''.addslashes($post['post_tag']).'\')'."\n";
		$qinsert=$db->query('INSERT INTO blog_post (order_id,post_time,post_host,post_content,post_link,blog_id,post_nastr,post_spam,post_fav,post_tag) VALUES ('.$insert_order_id.',\''.addslashes($post['post_time']).'\',\''.addslashes($post['post_host']).'\',\''.addslashes($post['post_content']).'\',\''.addslashes($post['post_link']).'\',\''.addslashes($blog_id).'\',\''.addslashes($post['post_nastr']).'\',\''.addslashes($post['post_spam']).'\',\''.addslashes($post['post_fav']).'\',\''.addslashes($post['post_tag']).'\')');
		$insert_post_id=$db->insert_id($qinsert);
		$db->query('INSERT INTO blog_full_com (ful_com_post_id,ful_com_order_id,ful_com_post) VALUES (\''.addslashes($insert_post_id).'\',\''.addslashes($insert_order_id).'\',\''.addslashes($post['ful_com_post']).'\')');
	}
	$qtag=$db_origin->query('SELECT * FROM blog_tag WHERE user_id='.$_SERVER['argv'][1].' AND order_id='.$order['order_id']);
	while ($tag=$db_origin->fetch($qtag))
	{
		$qkeys='';
		$qvalue='';
		$zapvalue='';
		$zapkey='';
		foreach ($tag as $key => $value)
		{
			if ($key=='tag_id') continue;
			if ($key=='order_id')
			{
				$qkeys.=$zapkey.addslashes('order_id');
				$qvalue.=$zapvalue.'\''.addslashes($insert_order_id).'\'';
				$zapvalue=',';
				$zapkey=',';	
				continue;
			}
			if ($key=='user_id')
			{
				$qkeys.=$zapkey.addslashes('user_id');
				$qvalue.=$zapvalue.'\''.addslashes($insert_user_id).'\'';
				$zapvalue=',';
				$zapkey=',';	
				continue;
			}
			$qkeys.=$zapkey.addslashes($key);
			$qvalue.=$zapvalue.'\''.addslashes($value).'\'';
			$zapvalue=',';
			$zapkey=',';	
		}
		$db->query('INSERT INTO blog_tag ('.$qkeys.') VALUES ('.$qvalue.')');	
		// echo 'INSERT INTO blog_tag ('.$qkeys.') VALUES ('.$qvalue.')'."\n";
	}
	$qkeys='';
	$qvalue='';
	$zapvalue='';
	$zapkey='';
	$qtp=$db_origin->query('SELECT * FROM blog_tp WHERE order_id='.$order['order_id']);
	while ($tp=$db_origin->fetch($qtp))
	{
		foreach ($tp as $key => $value)
		{
			if ($key=='tp_id') continue;
			if ($key=='order_id')
			{
				$qkeys.=$zapkey.addslashes('order_id');
				$qvalue.=$zapvalue.'\''.addslashes($insert_order_id).'\'';
				$zapvalue=',';
				$zapkey=',';	
				continue;
			}
			$qkeys.=$zapkey.addslashes($key);
			$qvalue.=$zapvalue.'\''.addslashes($value).'\'';
			$zapvalue=',';
			$zapkey=',';	
		}
		$db->query('INSERT INTO blog_tp ('.$qkeys.') VALUES ('.$qvalue.')');	
		// echo 'INSERT INTO blog_tp ('.$qkeys.') VALUES ('.$qvalue.')'."\n";
	}
}
file_get_contents('http://'.$_SERVER['argv'][2].'/api/admin/deluser?user_id='.$_SERVER['argv'][1]);

?>