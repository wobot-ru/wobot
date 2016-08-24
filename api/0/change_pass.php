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
//if (!$loged) die();
//print_r($_POST);
if ((trim($_POST['token'])!='') && (trim($_POST['uid'])!='') && (trim($_POST['new_pass'])!='') && (trim($_POST['old_pass'])!=''))
{
	$q_us=$db->query('SELECT * FROM users WHERE user_id='.intval($_POST['uid']).' LIMIT 1');
	$us=$db->fetch($q_us);
	//print_r($us);
	//echo md5($us['user_email'].':'.$us['user_pass']).' '.$_POST['token']; 
	if (md5($us['user_email'].':'.$us['user_pass'])==$_POST['token'])
	{
		if ($us['user_pass']==md5($_POST['old_pass']))
		{
			if ($_POST['new_pass']!=$_POST['old_pass'])
			{
				//echo ;
				$qupd=$db->query('UPDATE users SET user_pass=\''.md5($_POST['new_pass']).'\' WHERE user_id='.intval($_POST['uid']));
				$out['status']='ok';
				$out['token']=md5($us['user_email'].':'.md5($_POST['new_pass']));
				echo json_encode($out);
				die();
			}
			else
			{
				$out['status']=4;
				echo json_encode($out);
				die();
			}
		}
		else
		{
			$out['status']=1;
			echo json_encode($out);
			die();
		}
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

?>