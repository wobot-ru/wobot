<?
session_start();
require_once('com/config.php');
//require_once('com/func.php');
require_once('com/db.php');
require_once('bot/kernel.php');

$db = new database();
$db->connect();

if (($_POST['access_token']!='') && ($_POST['user_id']!='') && ($_POST['login']!='') && ($_POST['pass']!=''))
{
	//echo 6;
	$qus=$db->query('SELECT * FROM users WHERE user_email=\''.$_POST['login'].'\' AND user_pass=\''.md5($_POST['pass']).'\' LIMIT 1');
	$user=$db->fetch($qus);
	if (intval($user['user_id'])==0)
	{
		$db->query('INSERT INTO users (user_email,user_pass,vk_id,vk_token) VALUES (\''.$_POST['login'].'\',\''.md5($_POST['pass']).'\',\''.$_POST['user_id'].'\',\''.addslashes($_POST['access_token']).'\')');
	}
	else
	{
		$db->query('UPDATE users SET vk_id='.$_POST['user_id'].',vk_token=\''.addslashes($_POST['access_token']).'\' WHERE user_email=\''.$_POST['login'].'\' AND user_pass=\''.md5($_POST['pass']).'\'');
	}
	echo 'Спасибо за регистрацию!';
	die();
}

$appid='2785229';
$appsecret='OGycTbxYl9ImDBpf3bCH';

?>

<?

if ((strlen($_GET['code'])==0)&&(strlen($_SESSION['code'])==0))
{
	//echo 5;
	header('Location: http://oauth.vkontakte.ru/authorize?client_id='.$appid.'&scope=notify,friends,photos,audio,video,docs,notes,pages,offers,questions,wall,groups,notifications,ads,offline&redirect_uri='.urlencode('http://production.wobot.ru/api/social/0/vkontakte.php').'&response_type=code');
	die();
}
elseif (strlen($_SESSION['access_token'])==0)
{
	//echo 4;
	$code=$_GET['code'];
	$_SESSION['code']=$_GET['code'];
	$json=file_get_contents('https://oauth.vkontakte.ru/access_token?client_id='.$appid.'&client_secret='.$appsecret.'&code='.$code);
	$data=json_decode($json, true);
	$_SESSION['access_token']=$data['access_token'];
	$_SESSION['user_id']=$data['user_id'];
}
//echo 3;
$access_token=$_SESSION['access_token'];
$user_id=$_SESSION['user_id'];
//echo "token:".$access_token."\n<br>";
//echo "user".$user_id."\n<br>";
$qus=$db->query('SELECT * FROM users WHERE vk_id='.$user_id.' LIMIT 1');
$user=$db->fetch($qus);
if ($user['user_id']=='')
{
	header('Location: http://wobot.ru/registration3.php?vk_id='.$user_id.'&vk_token='.$access_token);
	//echo 2;
	echo '<form method="POST">
	Логин: <input type="text" name="login"><br>
	Пароль: <input type="text" name="pass">
	<input type="hidden" name="access_token" value="'.$access_token.'">
	<input type="hidden" name="user_id" value="'.$user_id.'">
	<input type="submit">
	</form>';
}
else
{
	//echo 1;
	$db->query('UPDATE users SET vk_token=\''.addslashes($access_token).'\' WHERE vk_id='.$user_id);
}
//echo 'INSERT INTO tp_keys (key,type) VALUES (\''.addslashes($access_token).'\',\'vk\')';
//$db->query('INSERT INTO tp_keys (`key`,`type`) VALUES (\''.addslashes($access_token).'\',\'vk\')');

$token=$access_token;


//echo 'http://oauth.vkontakte.ru/oauth/authorize?client_id='.$appid.'&scope=notify,friends,photos,audio,video,docs,notes,pages,offers,questions,wall,groups,notifications,ads,offline&redirect_uri='.urlencode('http://bmstu.wobot.ru/social/vkontakte.php').'&display=popup&response_type=token';
//echo 'http://oauth.vkontakte.ru/oauth/authorize?client_id='.$appid.'&scope=notify,friends,photos,audio,video,docs,notes,pages,offers,questions,wall,groups,notifications,ads,offline&redirect_uri=blank.html&display=popup&response_type=token';
//echo 'http://oauth.vkontakte.ru/oauth/authorize?client_id='.$appid.'&scope=notify,friends,photos,audio,video,docs,notes,pages,offers,questions,wall,groups,messages,notifications,ads,offline&redirect_uri='.urlencode('http://bmstu.wobot.ru/social/vkontakte.php').'&display=popup&response_type=token';
//echo $data;

/*list($a_t,$e_i,$u_i)=explode('&',$data,3);
$auth['token']=explode('=',$a_t,2);
$auth['expires']=explode('=',$e_i,2);
$auth['user_id']=explode('=',$u_i,2);*/
//print_r($auth);

?>
