<?

require_once('/var/www/com/db.php');
require_once('/var/www/com/config.php');

function parseUrlmail($url,$to,$subj,$body,$from)
{
$uagent = "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13";
$keyword=$word;
$keyword=urlencode(iconv('utf-8','windows-1251',$keyword));
//$url='http://2mm.ru/forum/search.php?mode=result&start=15';
$postvars='mail='.$to.'&title='.$subj.'&content='.$body.'&from='.$from;
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

$db=new database();
$db->connect();

do
{
	echo '.';
	sleep(5);
	$qorder=$db->query('SELECT * FROM blog_orders as a LEFT JOIN users as b ON a.user_id=b.user_id WHERE order_id='.$_SERVER['argv'][1]);
	$order=$db->fetch($qorder);
	// if ($order['user_id']!=1187) die();
	// if ($order['user_id']!=2505) die();
	if ($order['user_id']==145) die();
	if (($order['third_sources']<=2) && ($order['order_end']>time())) continue;
	// if ($order['order_last']==0) continue;
	echo '*';
	if (intval(shell_exec('ps ax | grep service | grep '.$_SERVER['argv'][1].' | wc -l'))>3) continue; 
	$qprev=$db->query('SELECT * FROM blog_post_prev WHERE order_id='.$_SERVER['argv']);
	if ($db->num_rows($qprev)!=0) continue;
	echo '/';
	break;
}
while (1);

$text='
Уважаемый(-ая) '.$order['user_name'].',<br><br>Ваша тема "'.$order['order_name'].'" готова к просмотру. Для просмотра темы, пожалуйста, нажмите на кнопку 
<br>
<form action="http://production.wobot.ru" method="POST">
<input type="hidden" name="token" value="'.(md5(mb_strtolower($order['user_email'],'UTF-8').':'.$order['user_pass'])).'">
<input type="hidden" name="user_id" value="'.$order['user_id'].'">
<input type="hidden" name="order_id" value="'.$order['order_id'].'">
<input class="btn" type="submit" value="Войти в кабинет">
</form>
<br>
<div>С уважением,<br>Поддержка Wobot<br><a href="mailto:mail@wobot.ru">mail@wobot.ru</a><br><i><img src=\'http://www.wobot.ru/new/assets/logo.png\'></i></div><br>Это письмо было сгенерировано автоматически, пожалуйста, не отвечайте на него. Если у Вас возникли вопросы, пожалуйста, присылайте их на адрес <a href="mailto:mail@wobot.ru">mail@wobot.ru</a>.';

parseUrlmail('http://188.120.239.225/api/service/sendmail.php',$order['user_email'],urlencode("Тема  готова: ".$order['order_name']),urlencode($text),'noreply@wobot.ru');
if ($order['user_id']=='2403') parseUrlmail('http://188.120.239.225/api/service/sendmail.php','skatskiy@magicsmm.ru',urlencode("Тема  готова: ".$order['order_name']),urlencode($text),'noreply@wobot.ru');

?>