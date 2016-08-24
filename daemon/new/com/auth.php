<?
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
	global $db, $config, $loged, $user_email, $user_pass, $user;
$loged=0;
if (isset($_POST['user_login']))
{
	
	$user_email=$_POST['user_email'];
	$user_pass=$_POST['user_pass'];

	$res=$db->query('SELECT * FROM users WHERE user_email=\''.$user_email.'\' and user_pass=\''.md5($user_pass).'\' LIMIT 1');
	$row = $db->fetch($res);
	if (intval($row['user_id'])!=0)
	{
		$user_id=$row['user_id'];
		$_SESSION['user_email']=$user_email;
		$_SESSION['user_pass']=$user_pass;
		$loged=1;
		$user=$row;	
		$res=$db->query('INSERT INTO blog_log (user_id,log_ip,log_time) VALUES ('.$user_id.',\''.getRealIpAddr().'\','.time().')');
	//cabinet();
	}
	
	if (isset($_POST['admin_email']))
	{
		$res=$db->query('SELECT * FROM users WHERE user_email=\''.$_POST['admin_email'].'\' and user_pass=\''.md5($_POST['user_pass']).'\' LIMIT 1');
		$row = $db->fetch($res);
		if ($row['user_priv']==4)
		{
			$_SESSION['admin_email']=$_POST['admin_email'];
			$user_email=$_POST['user_email'];

			$res=$db->query('SELECT * FROM users WHERE user_email=\''.$user_email.'\' LIMIT 1');
			$row = $db->fetch($res);

			if (intval($row['user_id'])!=0)
			{
				$user_id=$row['user_id'];
				$_SESSION['user_email']=$user_email;
				//$_SESSION['user_pass']=$user_pass;
				$loged=1;
				$user=$row;
			}
		}
	}
}
elseif ((isset($_SESSION['user_email'])&&isset($_SESSION['user_pass']))||(isset($_SESSION['admin_email'])&&isset($_SESSION['user_pass'])))
{
/*	if (strlen($_SESSION['admin_email'])>0)
	{
		if ($_SESSION['admin_email']!=$_SESSION['user_email'])
		{
			$res=$db->query('SELECT * FROM users WHERE user_email=\''.$_SESSION['admin_email'].'\' and user_pass=\''.md5($_SESSION['user_pass']).'\' LIMIT 1');
			$row = $db->fetch($res);
			if ($row['user_priv']==4)
			{
				$user_email=$_SESSION['user_email'];
				//$user_pass=$_SESSION['user_pass'];

				$res=$db->query('SELECT * FROM users WHERE user_email=\''.$user_email.'\' LIMIT 1');
				$row = $db->fetch($res);

				if (intval($row['user_id'])!=0)
				{
					$user_id=$row['user_id'];
					$_SESSION['user_email']=$user_email;
					//$_SESSION['user_pass']=$user_pass;
					$loged=1;
					$user=$row;
				}
			}
		}
		else
		{
			$user_email=$_SESSION['user_email'];
			$user_pass=$_SESSION['user_pass'];

			$res=$db->query('SELECT * FROM users WHERE user_email=\''.$user_email.'\' and user_pass=\''.md5($user_pass).'\' LIMIT 1');
			$row = $db->fetch($res);

			if (intval($row['user_id'])!=0)
			{
				$user_id=$row['user_id'];
				$_SESSION['user_email']=$user_email;
				$_SESSION['user_pass']=$user_pass;
				$loged=1;
				$user=$row;
				if ($user['user_priv']==4)
				{
					$_SESSION['admin_email']=$user_email;
				}
			}
		}
	}
	else
	{*/	
		
		$user_email=$_SESSION['user_email'];
		$user_pass=$_SESSION['user_pass'];

		$res=$db->query('SELECT * FROM users WHERE user_email=\''.$user_email.'\' and user_pass=\''.md5($user_pass).'\' LIMIT 1');
		$row = $db->fetch($res);

		if (intval($row['user_id'])!=0)
		{
			$user_id=$row['user_id'];
			$_SESSION['user_email']=$user_email;
			$_SESSION['user_pass']=$user_pass;
			$loged=1;
			$user=$row;
			if ($user['user_priv']==4)
			{
				$_SESSION['admin_email']=$user_email;
			}
		}
		
		if (isset($_SESSION['admin_email']))
		{
			$res=$db->query('SELECT * FROM users WHERE user_email=\''.$_SESSION['admin_email'].'\' and user_pass=\''.md5($_SESSION['user_pass']).'\' LIMIT 1');
			$row = $db->fetch($res);
			if ($row['user_priv']==4)
			{
				$user_email=$_SESSION['user_email'];

				$res=$db->query('SELECT * FROM users WHERE user_email=\''.$user_email.'\' LIMIT 1');
				$row = $db->fetch($res);

				if (intval($row['user_id'])!=0)
				{
					$user_id=$row['user_id'];
					$_SESSION['user_email']=$user_email;
					//$_SESSION['user_pass']=$user_pass;
					$loged=1;
					$user=$row;
				}
			}
		}
//	}
}

// login information not set
if (!$loged)
{
if (isset($_POST['login'])) echo '<h1>login fail</h1>';

echo '
<html>
<head>
<style>
body { margin: 0; padding: 0 }
table { margin: 0; padding: 0 }
td { margin: 0; padding: 0 }
.login-form { padding: 40px 30px; margin: 40px 30px; background-image: url(\'/img/loginform.png\'); background-position: center; background-repeat: no-repeat; width: 350px; height: 456px; }
input { background: #eee; border: 0; border: 1px solid #eee; color: #444; font-size: 20px; text-align: center; padding: 5px; margin: 5px; width: 220px; }
.tip { z-order:999; color: #aaa; font-size: 10px; left: 50px; font-style: normal; width: 100px; }
</style>
</head>
<body style="background: #000000 url(\'/img/login-bg.jpg\') center no-repeat;">
<center>
<table height="100%">
<td>
<div class="login-form">
<form method="POST" style="padding: 40px 30px; margin: 40px 30px; position: relative; left: 280px; top: 60px;">
<input type="hidden" name="user_login" value="'.rand().'">
<table>
<tr>
<td>
<input type="text" name="user_email" id="user_email" value="'.$_POST['user_email'].'">
</td>
</tr>
<tr>
<td>
<input type="password" name="user_pass" id="password" value="">
</td>
</tr>
<tr>
<td>
<input type="submit" name="login" value="войти">
</td>
</tr>
</table>
</form>
</div>
</td>
</table>
</center>
</body>
</html>
';
die();
}
}

?>
