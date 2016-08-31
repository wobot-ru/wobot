<?

$redis=new Redis() or die("Can'f load redis module.");
$redis->connect('127.0.0.1');

error_reporting(0);

register_shutdown_function('handleShutdown');

function mailerror($to, $message)
{
    //$to      = 'nobody@example.com';

    $subject = 'error message';
    //$message = 'hello';
    $headers  = "From: noreply@wobot.ru\r\n";
    $headers .= "Bcc: noreply@wobot.ru\r\n";
    $headers .= "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type: text/html; charset=utf-8"."\r\n";

    mail($to, $subject, $message, $headers);
}

function handleShutdown() {
    $error = error_get_last();
    if($error !== NULL){
        $info = "[SHUTDOWN] file:".$error['file']." | ln:".$error['line']." | msg:".$error['message'] .PHP_EOL;
        //echo "!!!!123\n";
        mailerror("zmei123@yandex.ru", $info);
        //mailerror("nikanorov@wobot.co, for.uki@gmail.com", $info);
        //yourPrintOrMailFunction($info);
    }
    else{
        mailerror("zmei123@yandex.ru", "Упал ,mail_sender shutdown");

        //yourPrintOrMailFunction("SHUTDOWN");
    }
}


function send_mail_post($mail,$title,$content,$from)
{
	$curl = curl_init();
 
	//уcтанавливаем урл, к которому обратимся
	curl_setopt($curl, CURLOPT_URL, 'http://wobot.ru/send_mail.php');
	//включаем вывод заголовков
	//curl_setopt($curl, CURLOPT_HEADER, 1);
	 
	//передаем данные по методу post
	curl_setopt($curl, CURLOPT_POST, 1);
	 
	//теперь curl вернет нам ответ, а не выведет
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 5); // таймаут соединения
  	curl_setopt($curl, CURLOPT_TIMEOUT, 5);        // таймаут ответа 
	//переменные, которые будут переданные по методу post
	curl_setopt($curl, CURLOPT_POSTFIELDS, 'mail='.urlencode($mail).'&title='.urlencode($title).'&content='.urlencode($content).'&from='.$from);
	//я не скрипт, я браузер опера
	curl_setopt($curl, CURLOPT_USERAGENT, 'Opera 10.00');
	
	$content = curl_exec( $curl );
	$err     = curl_errno( $curl );
	$errmsg  = curl_error( $curl );
	$header  = curl_getinfo( $curl );
	curl_close( $curl );
	$fp = fopen('/var/www/tools/epikur/apache.log', 'a');
	fwrite($fp, $content."\n");
	fclose($fp);
	echo '|'.$content.'|';
	return 1;
	// return intval($content);
}

function send_mail_post_aws($mail,$title,$content,$from)
{
	$curl = curl_init();
 
	//уcтанавливаем урл, к которому обратимся
	curl_setopt($curl, CURLOPT_URL, 'http://localhost/api/service/aws/aws/send_mail.php');
	//включаем вывод заголовков
	//curl_setopt($curl, CURLOPT_HEADER, 1);
	 
	//передаем данные по методу post
	curl_setopt($curl, CURLOPT_POST, 1);
	 
	//теперь curl вернет нам ответ, а не выведет
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 5); // таймаут соединения
  	curl_setopt($curl, CURLOPT_TIMEOUT, 5);        // таймаут ответа 
	//переменные, которые будут переданные по методу post
	curl_setopt($curl, CURLOPT_POSTFIELDS, 'mail='.urlencode($mail).'&title='.urlencode($title).'&content='.urlencode($content).'&from='.$from);
	echo 'mail='.urlencode($mail).'&title='.urlencode($title).'&content='.urlencode($content).'&from='.$from;
	//я не скрипт, я браузер опера
	curl_setopt($curl, CURLOPT_USERAGENT, 'Opera 10.00');
	
	$content = curl_exec( $curl );
	$err     = curl_errno( $curl );
	$errmsg  = curl_error( $curl );
	$header  = curl_getinfo( $curl );
	curl_close( $curl );
	echo '|'.$content.'|';
	$mcontent=json_decode($content,true);
	print_r($mcontent);
	echo '|||';
	// die();
	return $mcontent['status'];
	// return intval($content);
}


function parseUrlmail($to,$subj,$body,$from)
{
	$message=str_replace('\\\'','\'',urldecode($body));
	$headers = "From: ".$from."\r\nReply-To: ".$from."\r\n";
	$headers .= "Content-Type: text/html; charset=utf-8";
	$theme = 'Заявка с сайта '.date("d.m.Y");
	$mailst =mail($to,$subj, stripslashes($message),$headers);
	if ($mailst) echo 'GOOD!'."\n";
	else echo 'FUCK!'."\n";
}

while (1)
{
	// echo 'Listening...'."\n";
	$data=$redis->lPop('mail_queue');
	if ($data!='') echo $data."\n";
	else 
	{
		sleep(2);
		continue;
	}
	$_POST=json_decode($data,true);
	print_r($_POST);
	// $_POST['mail']='zmei123@yandex.ru';
	// $_POST['title']='HELLO';
	// $_POST['content']='<h1>123213213123</h1>';
	// parseUrlmail($_POST['mail'],$_POST['title'],$_POST['content'],$_POST['from']);
	$res=send_mail_post_aws($_POST['mail'],$_POST['title'],$_POST['content'],$_POST['from']);
		// if (intval($res)==0) send_mail_post($_POST['mail'],$_POST['title'],$_POST['content'],$_POST['from']);
	// if ($_POST['mail']=='sento2013@list.ru') 
	// {
	// 	echo file_get_contents('http://wobot.ru/send_mail.php');
	// 	echo '!!!!!!!!!!!!!!!!*******!!!!!!!!!!!!!!!'."\n";
	// 	send_mail_post($_POST['mail'],$_POST['title'],$_POST['content'],$_POST['from']);
	// 	echo '------------------------'."\n";
	// }
	sleep(2);
}

?>