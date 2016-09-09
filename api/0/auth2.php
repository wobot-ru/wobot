<?
require_once('/var/www/new/com/config.php');
require_once('/var/www/new/com/func.php');
require_once('/var/www/new/com/db.php');
require_once('/var/www/new/bot/kernel.php');
//require_once('auth.php');

$db = new database();
$db->connect();
function getRealIpAddr()
{
    if (!empty($_SERVER['HTTP_CLIENT_IP']))   //check ip from share internet
    {
      $ip=$_SERVER['HTTP_CLIENT_IP'];
    }
    elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))   //to check ip is pass from proxy
    {
      $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    else
    {
      $ip=$_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}
function auth()
{
	global $db,$user,$loged,$memcache,$_SESSION;
	// echo '<pre>';
	// print_r($_SESSION);
	// print_r($_SERVER);
	// echo session_id();
	// echo '</pre>';
	// die();
//echo '234';
	if (isset($_GET['test_token'])&&isset($_GET['test_user_id'])) {
		$_COOKIE['user_id']=$_GET['test_user_id'];
		$_COOKIE['token']=$_GET['test_token'];
		$_COOKIE['user_email']=$_GET['test_user_email'];
		//$loged=1;
		//session_destroy();
	}
	//echo $_COOKIE['token'];
	$loged=0;
	//print_r($_COOKIE);
	if (isset($_COOKIE['token']))
	{
		if (!isset($memcache)) $memcache = memcache_connect('localhost', 11211);
		//echo 'order_'.$_POST['order_id'].'_'.$_POST['start'].'_'.$_POST['end'];
		if (preg_match('/http\:\/\/188\.120\.239\.225\/new\//isu', $_SERVER['HTTP_REFERER'])) $memcache->delete('user_session_'.session_id());
		$out=$memcache->get('user_session_'.session_id());
		// echo $out;
		// die();
		if ($out!='') $row=json_decode($out,true);
		else
		{
			$res=$db->query('SELECT * FROM users as a LEFT JOIN user_tariff as b ON a.user_id=b.user_id WHERE a.user_id='.intval($_COOKIE['user_id']).' ORDER BY b.ut_id DESC LIMIT 1');
			$row = $db->fetch($res);
		}
		//echo 'SELECT * FROM users as a LEFT JOIN user_tariff as b ON a.user_id=b.user_id WHERE a.user_id='.intval($_COOKIE['user_id']).' LIMIT 1';
		//echo intval($_COOKIE['user_id']);
		//print_r($row);
		//echo $_COOKIE['token'].' ';
		if (intval($row['user_id'])!=0)
		{
			//echo $row['user_email'].' '.$row['user_pass'].' '.md5($row['user_email'].':'.$row['user_pass']).' '.$_COOKIE['token'];
			if ((md5(mb_strtolower($row['user_email'],'UTF-8').':'.$row['user_pass']))==$_COOKIE['token'])
			{
				$memcache->set('user_session_'.session_id(), json_encode($row), MEMCACHE_COMPRESSED, 3600);
				if (($row['user_active']==0) || ($row['user_active']==2)||($row['user_active']==3))
				{
					$user=$row;
					$loged=1;
					//echo $loged;
				}
				else
				{
					$user=$row;
					//$loged=1;
				}
			}
		}
	}
}
?>
