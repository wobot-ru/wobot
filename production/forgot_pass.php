<?
require_once('/var/www/new/com/config.php');
require_once('/var/www/new/com/func.php');
require_once('/var/www/new/com/db.php');
require_once('/var/www/new/bot/kernel.php');
//require_once('/var/www/api/0/auth.php');
date_default_timezone_set ( 'Europe/Moscow' );
$db = new database();
$db->connect();

function parseUrlmail($url,$to,$subj,$body,$from)
{
$uagent = "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13";
$keyword=$word;
$keyword=urlencode(iconv('utf-8','windows-1251',$keyword));
//$url='http://2mm.ru/forum/search.php?mode=result&start=15';
$postvars='to='.$to.'&subject='.$subj.'&body='.$body.'&from='.$from;
$uagent = "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13";
$ch = curl_init( $url );
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);   // возвращает веб-страницу
curl_setopt($ch, CURLOPT_HEADER, 0);           // не возвращает заголовки
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);   // переходит по редиректам
curl_setopt($ch, CURLOPT_POSTFIELDS    ,$postvars);
curl_setopt($ch, CURLOPT_ENCODING, "");        // обрабатывает все кодировки
curl_setopt($ch, CURLOPT_USERAGENT, $uagent);  // useragent
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 120); // таймаут соединения
curl_setopt($ch, CURLOPT_TIMEOUT, 120);        // таймаут ответа
curl_setopt($ch, CURLOPT_MAXREDIRS, 10);       // останавливаться после 10-ого редиректа
$content = curl_exec( $ch );
$err     = curl_errno( $ch );
$errmsg  = curl_error( $ch );
$header  = curl_getinfo( $ch );
curl_close( $ch );
 /*$header['errno']   = $err;
 $header['errmsg']  = $errmsg;
  $header['content'] = $content;*/
  return $content;
}

function check_pass($pass)
{
	$val_zag='/[А-ЯA-Z]/u';
	$val_dig='/[0-9]/u';
	if (mb_strlen($pass,'UTF-8')<6) return false;
	if (!preg_match($val_zag,$pass)) return false;
	//if (!preg_match($val_dig,$pass)) return false;
	return true;
}

//print_r($_POST);
if (($_POST['pass']!='') && ($_POST['ver_pass']!=''))
{
	//echo 1;
	if ($_POST['pass']==$_POST['ver_pass'])
	{
		//echo 2;
		$i=$db->query('SELECT * FROM users WHERE user_id='.$_POST['uid'].' AND user_pass=\''.$_POST['token'].'\' LIMIT 1');
		$user=$db->fetch($i);
		if ($user['user_email']!='')
		{
			//echo 3;
			if (preg_match('/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/is',trim($user['user_email'])))
			{
				//echo 4;
				if (check_pass(trim($_POST['pass'])))
				{
					//echo 5;
					$headers  = "noreply@wobot.ru\r\n"; 
					$headers .= "Content-type: text/html\r\n";
					$i=$db->query('UPDATE users SET user_pass=\''.$_POST['token'].'\' WHERE user_id='.$_POST['uid'].' AND user_pass=\''.$_POST['token'].'\'');
					parseUrlmail('http://www.wobot.ru/mail_send.php',$user['user_email'],'Команда Wobot',urlencode('<html><body>Ваш пароль успешно изменен на: '.$_POST['pass'].'<br>Спасибо за использование нашего сервиса!</body></html>'),$headers);
					echo 'Поздравляю ваш пароль успешно изменен!';
				}
				else
				{
					echo '
						<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
						<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
					<form method="POST">Вы ввели некорректный пароль! Пожалуйста введите еще раз.<br>Изменение пароля:<br>Новый пароль: <input type="hidden" name="token" value="'.$_GET['token'].'"><input type="hidden" name="uid" value="'.$_GET['uid'].'"><input type="password" name="pass"><br>Подтверждение пароля: <input type="password" name="ver_pass"><br><input type="submit" value="Изменить"></form>';
					die();
				}
			}
			else
			{
				echo '
					<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
					<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
				<form method="POST">Вы ввели не почту! Пожалуйста введите еще раз.<br>Изменение пароля:<br>Новый пароль: <input type="hidden" name="token" value="'.$_GET['token'].'"><input type="hidden" name="uid" value="'.$_GET['uid'].'"><input type="password" name="pass"><br>Подтверждение пароля: <input type="password" name="ver_pass"><br><input type="submit" value="Изменить"></form>';
				die();
			}
		}
		else
		{
			echo '
				<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
				<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
			<form method="POST">Вы ввели пустой пароль! Пожалуйста введите еще раз.<br>Изменение пароля:<br>Новый пароль: <input type="hidden" name="token" value="'.$_GET['token'].'"><input type="hidden" name="uid" value="'.$_GET['uid'].'"><input type="password" name="pass"><br>Подтверждение пароля: <input type="password" name="ver_pass"><br><input type="submit" value="Изменить"></form>';
			die();
		}
	}
	else
	{
		echo '
			<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
			<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<form method="POST">Вы ввели разные пароли! Пожалуйста введите еще раз.<br>Изменение пароля:<br>Новый пароль: <input type="hidden" name="token" value="'.$_GET['token'].'"><input type="hidden" name="uid" value="'.$_GET['uid'].'"><input type="password" name="pass"><br>Подтверждение пароля: <input type="password" name="ver_pass"><br><input type="submit" value="Изменить"></form>';
		die();
	}
	die();
}
if (($_GET['token']!='') && ($_GET['uid']!=''))
{
	$i=$db->query('SELECT * FROM users WHERE user_pass=\''.$_GET['token'].'\' AND user_id='.$_GET['uid'].' LIMIT 1');
	$user=$db->fetch($i);
	if ($user['user_pass']!='')
	{
		echo '
			<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
			<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<form method="POST">Изменение пароля:<br>Новый пароль: <input type="hidden" name="token" value="'.$_GET['token'].'"><input type="hidden" name="uid" value="'.$_GET['uid'].'"><input type="password" name="pass"><br>Подтверждение пароля: <input type="password" name="ver_pass"><br><input type="submit" value="Изменить"></form>';
		die();
	}
}
if ($_POST['mail']!='')
{
	$i=$db->query('SELECT * FROM users WHERE user_email=\''.$_POST['mail'].'\' LIMIT 1');
	$user=$db->fetch($i);
	if ($user['user_email']!='')
	{
		$headers  = "noreply@wobot.ru\r\n"; 
		$headers .= "Content-type: text/html\r\n";
		parseUrlmail('http://www.wobot.ru/mail_send.php',$user['user_email'],'Команда Wobot',urlencode('<html><body>Пожалуйста перейдите по ссылке указанному в письме, для восстановления вашего пароля: <br>http://beta.wobot.ru/forgot_pass.php?token='.$user['user_pass'].'&uid='.$user['user_id'].'</body></html>'),$headers);
		echo '
			<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
			<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">		Вам на почту выслано сообщение.';
		die();
	}
}
echo '
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<form method="POST">Введите ваш e-mail: <input type="text" name="mail"><input type="submit" value="Изменить"></form>';

?>