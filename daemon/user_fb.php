<?

require_once('com/db.php');
require_once('com/config.php');
require_once('fsearch3/ch.php');
require_once('com/users.php');
require_once('/var/www/new/com/func.php');

date_default_timezone_set('Europe/Moscow');

/*$order_delta=$_SERVER['argv'][1];
sleep($order_delta);
$fp = fopen('/var/www/daemon/pids/transf'.$order_delta.'.pid', 'w');
fwrite($fp, getmypid());
fclose($fp);
$refresh_cash=intval($order_delta)*2;*/
$db=new database();
$db->connect();
//while (1)
//{
$fb_accesstoken='AAACQNvcNtEgBAFxvAjog6ANpYEE5KL6L63pm5YM0TZBHLYV0xtgc11u27LQWqU6Fxx1D7dD5BpOTLZAMscgKoZBcGAUzl15PIaAtLwtdAbnE27GuwAv';
	$users=$db->query('SELECT user_id, user_email FROM users WHERE user_fb = "0" ORDER BY user_id DESC');
	while ($user=$db->fetch($users))
	{
//$user['user_email']='rcpsec@gmail.com';
		if (mb_strpos($user['user_email'], '@',0,'UTF-8')>0)
		{
			$fbinfo=file_get_contents('https://graph.facebook.com/search?q='.$user['user_email'].'&type=user&access_token='.$fb_accesstoken);
			$fbinfo=json_decode($fbinfo,true);
			$fbid=intval($fbinfo['data'][0]['id']);
			if ($fbid>0) {
				echo $user['user_email'].' '.$fbid."\n";
				$db->query('UPDATE users SET user_fb='.$fbid.' WHERE user_id='.$user['user_id']);
			}
			echo '.';
			sleep(1);
		}
		else echo '/';
	}
	//print_r($orders);
	//sleep(10);
//}

?>
