<?


$to  = '<zmei123@yandex.ru>'; // note the comma

// subject
$subject = 'Заявка на услугу';

// message
$message = '
<h1>Заявка на подключение новой услуги</h1><br>

Дата заявки: '.date("d.m.Y");

// To send HTML mail, the Content-type header must be set
$headers  = 'MIME-Version: 1.0' . "\r\n";
$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";

// Additional headers
$headers .= 'Wobot Team <zmei123@yandex.ru>' . "\r\n";
$headers .= 'From: WOBOT CP <noreply@wobot.ru>' . "\r\n";

// Mail it
//mail('rcpsec@gmail.com', $subject, $message, $headers);
mail($to, $subject, $message, $headers);

?>