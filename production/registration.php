<?
require_once('/var/www/com/config.php');
require_once('/var/www/com/func.php');
require_once('/var/www/com/db.php');
require_once('/var/www/bot/kernel.php');

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

//error_reporting(E_ERROR | E_WARNING | E_PARSE);
date_default_timezone_set ( 'Europe/Moscow' );

$db = new database();
$db->connect();

if ($_GET['token']!='')
{
	$qw1=$db->query('SELECT * FROM users WHERE user_verify=\''.$_GET['token'].'\' LIMIT 1');
	//echo 'SELECT * FROM users WHERE user_verify=\''.$_GET['token'].'\' LIMIT 1';
	$us=$db->fetch($qw1);
	if ($us['user_id']!='')
	{
		$qw2=$db->query('UPDATE users SET user_active=2 WHERE user_id='.$us['user_id']);
		//echo 'UPDATE users SET user_active=1 WHERE user_id='.$us['user_id'];
		$qw1='INSERT INTO user_tariff (user_id,tariff_id,ut_date) VALUES ('.$us['user_id'].',3,'.mktime(0,0,0,date('n'),date('j'),date('Y')).')';
		//echo $qw1;
		$db->query($qw1);
		echo '
			<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
			<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		Ваша учетная запись активирована';
		$msg=$db->query('SELECT * FROM msg_tpl WHERE id=6');
		$ms=$db->fetch($msg);
		$text=$ms['message'];
		$title=$ms['gl'];
		parseUrlmail('http://www.wobot.ru/mail_send.php',$us['user_email'],$title,$text,'noreply@wobot.ru');
	}
}
else
{
	if ((trim($_POST['user_email'])!='') && (mb_strlen(trim($_POST['user_pass']),'UTF-8')>6) && ($_POST['user_name']!='') && ($_POST['user_contact']!=''))// && ($_POST['user_company']!='')
	{
		echo '
			<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
			<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		Спасибо за регистрацию';
		$yet=$db->query('SELECT * FROM users WHERE user_email=\''.$_POST['user_email'].'\' LIMIT 1');
		//echo 'SELECT * FROM users WHERE user_email=\''.$_POST['user_email'].'\' LIMIT 1';
		if (mysql_num_rows($yet)==0)
		{
			$qw='INSERT INTO users (user_email,user_pass,user_verify,user_active,user_name,user_company,user_contact,user_mails) VALUES (\''.addslashes($_POST['user_email']).'\',\''.addslashes(md5($_POST['user_pass'])).'\',\''.md5($_POST['user_email'].md5($_POST['user_pass'])).'\',1,\''.addslashes($_POST['user_name']).'\',\''.addslashes($_POST['user_company']).'\',\''.addslashes($_POST['user_contact']).'\',\''.addslashes($_POST['user_email']).'\')';
			$db->query($qw);
			parseUrlmail('http://www.wobot.ru/mail_send.php',$_POST['user_email'],'Регистрация','Спасибо за регистрацию, для завершения регистрации просьба перейти по ссылке http://beta.wobot.ru/registration.php?token='.md5($_POST['user_email'].md5($_POST['user_pass'])),'noreply@wobot.ru');
			//print_r($_POST);
		}
	}
	else
	{
		//$qw1=$db->query('SELECT * FROM blog_tariff WHERE tariff_id!=1');
		echo '
		<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	Добавление пользователя:
	<form method="post">
	<input type="hidden" name="action" value="adduser">
	<table>
	<tr>
	<td>E-mail*: </td><td><input type="text" name="user_email"></td>
	</tr>
	<tr>
	<td>Пароль*: </td><td><input type="text" name="user_pass"></td>
	</tr>
	<tr>
	<td>Контактное лицо*: </td><td><input type="text" name="user_name"></td>
	</tr>
	<tr>
	<td>Номер телефона*: </td><td><input type="text" name="user_contact"></td>
	</tr>
	<tr>
	<td>Название компании*: </td><td><input type="text" name="user_company"></td>
	</tr>
	<tr>
	<td>Баланс счета: </td><td><input type="text" name="user_money"></td>
	</tr>
	<tr>
	<td>
	<select name="tariff">';
	//while ($tar=$db->fetch($qw1))
	{
		echo '<option value="1">Демо</option>';
	}
	echo '
	<select>
	</td>
	</tr>
	</table>
	<input type="submit" value="Добавить">
	</form>';
	}
}
?>