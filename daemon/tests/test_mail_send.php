<?

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

send_mail_post('zmei123@yandex.ru','Тест почты','Тест почты','noreply2@wobot.ru');

?>