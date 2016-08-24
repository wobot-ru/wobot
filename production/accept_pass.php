<?
require_once('/var/www/new/com/config.php');
require_once('/var/www/new/com/func.php');
require_once('/var/www/new/com/db.php');
require_once('/var/www/new/bot/kernel.php');

$db = new database();
$db->connect();

$us=$db->query('SELECT * FROM users WHERE user_id='.intval($_GET['uid']).' AND user_pass=\''.$_GET['lst'].'\' LIMIT 1');
$us_i=$db->fetch($us);
//print_r($us_i);
if ($us_i['user_id']!='')
{
	//print_r($us_i);
	$db->query('UPDATE users SET user_pass=\''.$_GET['token'].'\' WHERE user_id='.intval($us_i['user_id']).' AND user_email=\''.$us_i['user_email'].'\'');
	//echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	//Ваш пароль успешно изменен!';
	setcookie("token", md5($us_i['user_email'].':'.$_GET['token']));
	header('Location: http://wobot.ru/accept_pass.php');
	//print_r($_COOKIE);
	//echo '-----<br>';
	//print_r($_COOKIE);
	//echo 'UPDATE users SET user_pass=\''.$_GET['token'].'\' WHERE user_id='.intval($us_i['user_id']).' AND user_email=\''.$us_i['user_email'].'\'';
	/*$upd_u=$db->query('SELECT * FROM users WHERE user_id='.intval($us_i['user_id']));
	$user=$db->fetch($upd_u);
	//print_r($user);
	$mails=explode(',',$user['user_mails']);
	//print_r($mails);
	if (!in_array($us_i['email'],$mails))
	{
		$mails[]=$us_i['email'];
		foreach ($mails as $item)
		{
			$tm.=$zap.$item;
			$zap=',';
		}
		//echo 'UPDATE users SET user_mails=\''.$tm.'\' WHERE user_id='.$us_i['user_id'];
		$db->query('UPDATE users SET user_mails=\''.$tm.'\' WHERE user_id='.$us_i['user_id']);
		$db->query('DELETE FROM mail_verify WHERE id='.$us_i['id']);
		echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		Ваша почта успешно добавлена в список рассылки!';
	}*/
}

?>