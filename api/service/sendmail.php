<?

$redis=new Redis() or die("Can'f load redis module.");
$redis->connect('127.0.0.1');

// $_POST['mail']='zmei123@yandex.ru';
// $_POST['title']='тест';
// $_POST['content']='тест';
// $_POST['from']='noreply@wobot.ru';

function parseUrlmail($to,$subj,$body,$from)
{
	$message=str_replace('\\\'','\'',urldecode($body));
	$headers = "From: ".$from."\r\nReply-To: ".$from."\r\n";
	$headers .= "Content-Type: text/html; charset=utf-8";
	$theme = 'Заявка с сайта '.date("d.m.Y");
	$mailst =mail($to,$subj, stripslashes($message),$headers);
}

$data['mail']=$_POST['mail'];
$data['title']=$_POST['title'];
$data['content']=$_POST['content'];
$data['from']=$_POST['from'];

$fp = fopen('/var/www/bot/mail_send.log', 'a');
fwrite($fp, date('r').' '.json_encode($data)."\n");
fclose($fp);

$redis->rPush('mail_queue', json_encode($data));
// echo $redis->lPop('mail_queue', json_encode($data));

// $redis->rPush('key1', 'AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA');
// $redis->rPush('key1', 'B');
// $redis->rPush('key1', 'C'); /* key1 => [ 'A', 'B', 'C' ] */
// echo $redis->lPop('key1'); /* key1 => [ 'B', 'C' ] */
// parseUrlmail($_POST['mail'],$_POST['title'],$_POST['content'],$_POST['from']);

?>