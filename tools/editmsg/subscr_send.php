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
$db = new database();
$db->connect();

echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>WOBOT &copy; Панель администратора (&beta;-version)</title>
<meta name="description" content="" />
<meta name="keywords" content="Wobot реклама анализ раскрутка баннер" />
<meta name="author" content="Wobot media" />
<meta name="robots" content="all" />
<meta content="text/html; charset=utf-8" http-equiv="Content-Type">
<script type="text/javascript" src="ckeditor.js"></script>
<script src="sample.js" type="text/javascript"></script>
<link href="sample.css" rel="stylesheet" type="text/css" />

</head>
';
$headers  = "noreply@wobot.ru\r\n"; 
$headers .= "Content-Type: text/html; charset=UTF-8;\r\n";
//print_r($_POST);
$im=$db->query('SELECT * FROM blog_subscribs');
echo 'Список всех подписчиков: <br><b>';
$i=1;
while ($msg=$db->fetch($im))
{
	if ($_POST['text']!='')
	{
		parseUrlmail('http://www.wobot.ru/mail_send.php',$msg['mail'],'Команда Wobot',urlencode($_POST['text']),$headers);
	}
	echo $zap.$i.'). '.$msg['mail'];
	$zap='<br>';
	$i++;
}
{
	echo '</b><br><form action="http://bmstu.wobot.ru/tools/editmsg/subscr_send.php" method="POST"><textarea id="editor" rows="5" cols="45" name="text"><br>Чтобы отписаться от подписки пройдите по <i><a href="http://wobot.ru/cancel_subscr.php?token='.base64_encode($msg['mail']).'&uid='.$msg['id'].'">ссылке</a></i><br><img src="http://wobot.ru/assets/logo.png"></textarea><input type="submit" value="Отправить"></form>';
	echo'<br>
	<script type="text/javascript">
	//<![CDATA[

		CKEDITOR.replace( \'editor\',
			{
				skin : \'kama\'
			});

	//]]>
	</script>
	
	';
}

?>