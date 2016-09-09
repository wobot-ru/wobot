<?
require_once('/var/www/new/com/config.php');
require_once('/var/www/new/com/func.php');
require_once('/var/www/new/com/db.php');
require_once('/var/www/new/bot/kernel.php');
require_once('auth.php');

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
	if (!preg_match($val_dig,$pass)) return false;
	return true;
}

date_default_timezone_set ( 'Europe/Moscow' );

$db = new database();
$db->connect();

//$_POST=$_GET;

//echo $_COOKIE['user_id'];
auth();

$redis=new Redis() or die("Can'f load redis module.");
$redis->connect('127.0.0.1');
set_log('tag',$_POST);

//if (!$loged) die();
//print_r($_POST);
if (($_POST['fio']!='') && (mb_strlen($_POST['fio'],'UTF-8')<=3))
{
	$outmas['status']=1;
	echo json_encode($outmas);
	die();
}
elseif ($_POST['fio']!='')
{
	$qw.=$zap.'user_name=\''.addslashes($_POST['fio']).'\'';
	$zap=',';
}
/*if ($_POST['contact']!='')
{
	$outmas['status']=1;
	echo json_encode($outmas);
	die();
}
else*/
if ($_POST['contact']!='')
{
	//$zap='';
	if (preg_match('/[^\d]/isu', $_POST['contact']) || (mb_strlen($_POST['contact'],'UTF-8')<10) || (mb_strlen($_POST['contact'],'UTF-8')>11))
	{
		$outmas['status']=7;
		echo json_encode($outmas);
		die();
	}
	$qw.=$zap.'user_contact=\''.addslashes($_POST['contact']).'\'';
	$zap=',';
}
if (($_POST['company']!='') && (mb_strlen($_POST['company'],'UTF-8')<=3))
{
	$outmas['status']=2;
	echo json_encode($outmas);
	die();
}
elseif ($_POST['company']!='')
{
	$qw.=$zap.'user_company=\''.addslashes($_POST['company']).'\'';
	$zap=',';
}

/*if ((($_POST['pass']!='') || ($_POST['ver_pass']!='')) && ((md5($_POST['pass'])!=$user['user_pass']) && (md5($_POST['ver_pass'])!=$user['user_pass'])))
{
	if (check_pass($_POST['pass']) && check_pass($_POST['ver_pass']))
	{
		if ($_POST['pass']!=$_POST['ver_pass'])
		{
			$outmas['status']=4;
			echo json_encode($outmas);
			die();
		}
		else
		{
			//$qw.=$zap.'user_pass=\''.md5($_POST['pass']).'\'';
			//$zap=',';
			if (preg_match('/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/is',trim($user['user_email'])))
			{
				$headers  = "From: noreply@wobot.ru\r\n"; 
				$headers .= "Bcc: noreply@wobot.ru\r\n";
				$headers .= "MIME-Version: 1.0" . "\r\n";
				$headers .= "Content-type: text/html; charset=utf-8"."\r\n";
				//setcookie("token", md5($user['user_email'].':'.md5($_POST['pass'])));
				mail($user['user_email'],'Команда Wobot',("Ваш пароль был изменен, для подтверждения пройдите по <a href='http://beta.wobot.ru/accept_pass.php?token=".md5($_POST['pass'])."&uid=".$user['user_id']."&lst=".$user['user_pass']."'>ссылке</a><div><i><img src='http://wobot.ru/new/assets/logo.png'></i></div>"),$headers);
				$outmas['status']=6;
				echo json_encode($outmas);
				//die();
				//print_r($_COOKIE);
				//echo 123;
				//die();
			}
		}
	}
	else
	{
		$outmas['status']=3;
		echo json_encode($outmas);
		die();
	}
}*/

$us_mails=explode(',',$user['user_mails']);
//print_r($us_mails);
if ($_POST['emails']!='')
{
	$mails=explode(',',$_POST['emails']);
	foreach ($mails as $mail)
	{
		if (preg_match('/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/is',trim($mail)))
		{
			if (!in_array($mail,$us_mails))
			{
				//echo trim($mail);
				$headers  = "From: noreply@wobot.ru\r\n"; 
				$headers .= "Bcc: noreply@wobot.ru\r\n";
				$headers .= "MIME-Version: 1.0" . "\r\n";
				$headers .= "Content-type: text/html; charset=utf-8"."\r\n";
				$db->query('INSERT INTO mail_verify (email,h_email,user_id) VALUES (\''.trim($mail).'\',\''.md5(trim($mail)).'\','.$user['user_id'].')');
				mail(trim($mail),'Команда Wobot',("Вас добавили в список рассылки, пожалуйста для подтверждения пройдите по ссылке <a href='http://wobot.ru/accept_dgst.php?token=".md5(trim($mail))."'>ссылка</a><div><i><img src='http://wobot.ru/new/assets/logo.png'></i></div>"),$headers);
			}
			else
			{
				$mails_upd[]=trim($mail);
				$qw_mail.=$zp.trim($mail);
				$zp=',';
			}
		}
		else
		{
			$outmas['status']=5;
			echo json_encode($outmas);
			die();
		}
	}
	$qw.=$zap.'user_mails=\''.addslashes($qw_mail).'\'';
	$zap=',';
}
if (($_POST['user_lang']!='') || ($_POST['user_timezone']!=''))
{
	$json_settings=json_decode($user['user_settings'],true);
	$json_settings['user_lang']=intval($_POST['user_lang']);
	$json_settings['user_timezone']=($_POST['user_timezone']);
	$qw.=$zap.'user_settings=\''.addslashes(json_encode($json_settings)).'\'';
	$zap=',';
}
if ($_POST['user_position']!='')
{
	$qw.=$zap.'user_position='.preg_replace('/[^а-яА-ЯёЁa-zA-Z\s\-]/isu','',$_POST['user_position']);
}
//$inf=$db->query('SELECT * FROM users WHERE user_id='.intval($user['user_id']));
//$us=$db->fetch($inf);
//$db->query();
if (isset($_POST['freq']))
{
	$qw.=$zap.'user_freq=\''.intval($_POST['freq']).'\'';
	$zap=',';
}
//echo 'UPDATE users SET '.$qw.' WHERE user_id='.intval($user['user_id']);
$db->query('UPDATE users SET '.$qw.' WHERE user_id='.intval($user['user_id']));
if ($outmas['status']!=6)
{
	$outmas['status']='ok';
	echo json_encode($outmas);
}

?>