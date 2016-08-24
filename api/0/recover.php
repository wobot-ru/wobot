<?
require_once('/var/www/new/com/config.php');
require_once('/var/www/new/com/func.php');
require_once('/var/www/new/com/db.php');
require_once('/var/www/new/bot/kernel.php');
//require_once('auth.php');

$db = new database();
$db->connect();

//$_POST=$_GET;

function check_pass($pass)
{
	$val_zag='/[А-ЯA-Z]/u';
	$val_dig='/[0-9]/u';
	if (mb_strlen($pass,'UTF-8')<6) return false;
	if (!preg_match($val_zag,$pass)) return false;
	//if (!preg_match($val_dig,$pass)) return false;
	return true;
}

$tarfs[3]='Демо';
$tarfs[5]='Базовый';
$tarfs[6]='Расширенный';
$tarfs[7]='Профессиональный';

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
 $header['errno']   = $err;
 $header['errmsg']  = $errmsg;
  $header['content'] = $content;
  return $content;
}

$memcache_obj = new Memcache;

$memcache_obj->connect('localhost', 11211);
$out1=intval($memcache_obj->get('recover_'.$_POST['mail']));
if ($out1>5)
{
	$out['status']=6;
	echo json_encode($out);
	die();
}
else
{
	$out1++;
	$memcache_obj->set('recover_'.$_POST['mail'], $out1,0,30);
}

//echo $memcache_obj->get('recover_'.$_POST['uid']);

if (trim($_POST['mail'])!='')
{
	$i=$db->query('SELECT * FROM users WHERE user_email=\''.$_POST['mail'].'\' LIMIT 1');
	$user=$db->fetch($i);
	if ($user['user_email']!='')
	{
		$headers  = "From: noreply@wobot.ru\r\n"; 
		$headers .= "Bcc: noreply@wobot.ru\r\n";
		$headers .= 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=utf-8'."\r\n";
		mail($user['user_email'],'Команда Wobot',('<html><body>Здравствуйте! <br>Вы запросили восстановление пароля к своему личному кабинету в системе Wobot. Следуйте <a href=\'http://wobot.ru/auth/recover?token='.$user['user_pass'].'&uid='.$user['user_id'].'\'>далее по ссылке</a></body></html>'),$headers);
		$out['status']='ok';
		echo json_encode($out);
		die();
	}
	else
	{
		$out['status']=1;
		echo json_encode($out);
		die();
	}
}
elseif ((trim($_POST['pass'])!='') && (trim($_POST['token'])!='') && (intval($_POST['uid'])!=0))
{
	$i=$db->query('SELECT * FROM users WHERE user_id='.$_POST['uid'].' AND user_pass=\''.$_POST['token'].'\' LIMIT 1');
	$user=$db->fetch($i);
	if ($user['user_email']!='')
	{
		if ($user['user_pass']==md5(trim($_POST['pass'])))
		{
			$out['status']=5;
			echo json_encode($out);
			die();
		}
		if (check_pass(trim($_POST['pass'])))
		{
			$headers  = "From: noreply@wobot.ru\r\n"; 
			$headers .= "Bcc: noreply@wobot.ru\r\n";
			$headers .= 'MIME-Version: 1.0' . "\r\n";
			$headers .= 'Content-type: text/html; charset=utf-8'."\r\n";
			$i=$db->query('UPDATE users SET user_pass=\''.md5($_POST['pass']).'\' WHERE user_id='.$_POST['uid'].' AND user_pass=\''.$_POST['token'].'\'');
			mail($user['user_email'],'Команда Wobot',('<html><body>Ваш пароль успешно изменен на: '.$_POST['pass'].'<br>Спасибо за использование нашего сервиса!</body></html>'),$headers);
			$out['user_email']=$user['user_email'];
			$out['status']='ok';
			echo json_encode($out);
			die();
		}
		else
		{
			$out['status']=2;
			echo json_encode($out);
			die();
		}
	}
	else
	{
		$out['status']=3;
		echo json_encode($out);
		die();
	}
}
$out['status']=4;
echo json_encode($out);
die();

?>
