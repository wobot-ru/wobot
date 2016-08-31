<?

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
	//cabinet();
	}
}
elseif (isset($_SESSION['user_email'])&&isset($_SESSION['user_pass']))
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
	//cabinet();
	}
}

// login information not set
if (!$loged)
{
if (isset($_POST['login'])) echo '<h1>login fail</h1>';

echo '
<center>
<form method="POST">
<input type="hidden" name="user_login" value="'.rand().'">
<input type="text" name="user_email" value="'.$_POST['user_email'].'">
<input type="password" name="user_pass" value="">
<input type="submit" name="login" value="войти">
</form>
</center>
';
die();
}
}

?>
