<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
require_once('com/config.php');
require_once('com/func.php');
require_once('com/db.php');
require_once('bot/kernel.php');
require_once('com/auth.php');

ignore_user_abort(true);
set_time_limit(0);
ini_set('max_execution_time',0);
ob_implicit_flush();

$db = new database();
$db->connect();

auth();
if (!$loged) die();

// login information set
//function cabinet()
//{
	global $db, $config, $user;
	//if ($user['user_priv']&4)
	//{
		/*echo $user['user_email'].' вход выполнен<br>';
		$res=$db->query('SELECT * FROM users');
		echo 'Список пользователей:<br>';
		$i=0;
		while ($row = $db->fetch($res)) {
			$i++;
			echo $row['user_email'].' <a href="/user/keywords/'.$row['user_id'].'">услуги</a><br>';
		}
		if ($i==0) echo 'пользователи отсутствуют<br>';*/

if(isset($_POST['submitted'])) {

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title></title>
<meta name="robots" content="all" />
<meta content="text/html; charset=utf-8" http-equiv="Content-Type">
<link rel="stylesheet" type="text/css" href="style.css">
<style>
body { padding: 0; margin: 0; background: #83b226; width: 504px; }
h1 { color: #ffffff; font-size: 18px; margin: 0; padding: 0; }
.pright { width: 244px; height: 30px; padding: 3px; text-align: left; float: left; color: #ffffff; font-size: 12px; }
.pleft { width: 232px; height: 18px; padding: 9px; text-align: left; float: left; color: #ffffff; font-size: 12px; }
.pcenter { width: 482px; height: 18px; padding: 9px; text-align: center; float: left; color: #ffffff; font-size: 12px; }
.pclr { width: 482px; height: 18px; padding: 9px; text-align: left; float: left; color: #ffffff; font-size: 12px; }
.clear { float: none; width: 100%; }
.forms { width: 504px; padding-left: 50px; padding-right: 50px; padding-top: 5px; padding-bottom: 5px; margin: 0; }
.text { width: 150px; height: 30px; font-size: 12px; color: #000000; background: url('img/order_part.png') no-repeat; border: 0; padding: 9px; padding-bottom: 0px; }
.error { color: #ffffff; font-size: 8px; }
</style>
</head>
<body>
<center>
	<form action="/new/add" method="post" id="sendEmail">
	<input type="hidden" name="submitted" id="submitted" value="true" />
		<div class="forms">
			<div class="pcenter"><h1>Заявка отправлена</h1></div>
		</div>
	</form>
</center>
</body>
</html>
<?
$to  = 'Wobot Team <mail@wobot.ru>, DEMIDOW <avdengineer@gmail.com>, YUDIN <r@wobot.co>'; // note the comma

// subject
$subject = 'Заявка на услугу';

// message
$message = '
<h1>Заявка на подключение новой услуги</h1><br>
Пользователь: '.$_POST['user_name'].' (<b>'.$_POST['user_email'].' : id'.$_POST['user_id'].'</b>)<br>
Ключевые слова: '.$_POST['order_name'].'<br>
Начало: '.$_POST['order_start'].'<br>
Конец: '.$_POST['order_end'].'<br>
Тариф: '.$_POST['order_tariff'].'<br>
Дата заявки: '.date("d.m.Y");

// To send HTML mail, the Content-type header must be set
$headers  = 'MIME-Version: 1.0' . "\r\n";
$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";

// Additional headers
$headers .= 'To: Wobot Team <mail@wobot.ru>, Mike <bma@wobot.ru>, DEMIDOW <avdengineer@gmail.com>, YUDIN <r@wobot.co>' . "\r\n";
$headers .= 'From: WOBOT CP <noreply@wobot.ru>' . "\r\n";

// Mail it
//mail('rcpsec@gmail.com', $subject, $message, $headers);
mail($to, $subject, $message, $headers);
//echo'<script>alert("sended");</script>';
//Для Вовы
//$mailFrom = $_POST['email'];
/*$subject = 'Заявка на услугу';
$message = '
<h1>Заявка на подключение новой услуги</h1><br>
Пользователь: '.$user['user_name'].' (<b>'.$user['user_email'].' : id'.$user['user_id'].'</b>)<br>
Ключевые слова: '.$_POST['user_name'].'<br>
Начало: '.$_POST['user_email'].'<br>
Конец: '.$_POST['user_pass'].'<br>
Тариф: '.$_POST['user_company'].'<br>
Дата заявки: '.date("d.m.Y");

//mail('mail@wobot.ru', $subject, $message, "From: noreply@wobot.ru");
//mail('bma@wobot.ru', $subject, $message, "From: noreply@wobot.ru");
mail('zmei123@yandex.ru', $subject, $message, "From: noreply@wobot.ru");*/
}
else
{
/*if ($_GET['user_id']>0)
{
	$res=$db->query('SELECT * FROM users WHERE user_id='.intval($_GET['user_id']).' limit 1');
	$row=$db->fetch($res);
}*/
		echo'
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title></title>
<meta name="robots" content="all" />
<meta content="text/html; charset=utf-8" http-equiv="Content-Type">
<link rel="stylesheet" type="text/css" href="style.css">
<link rel="stylesheet" type="text/css" href="/css/jquery-ui.css">
<style>
body { padding: 0; margin: 0; background: #83b226; width: 504px; }
h1 { color: #ffffff; font-size: 18px; margin: 0; padding: 0; }
.pright { width: 244px; height: 30px; padding: 3px; text-align: left; float: left; color: #ffffff; font-size: 12px; }
.pleft { width: 232px; height: 18px; padding: 9px; text-align: left; float: left; color: #ffffff; font-size: 12px; }
.pcenter { width: 482px; height: 18px; padding: 9px; text-align: center; float: left; color: #ffffff; font-size: 12px; }
.pclr { width: 482px; height: 18px; padding: 9px; text-align: left; float: left; color: #ffffff; font-size: 12px; }
.clear { float: none; width: 100%; }
.forms { width: 504px; padding-left: 50px; padding-right: 50px; padding-top: 5px; padding-bottom: 5px; margin: 0; }
.text { width: 150px; height: 30px; font-size: 12px; color: #000000; background: url(\'/img/order_part.png\') no-repeat; border: 0; padding: 9px; padding-bottom: 0px; }
.error { color: #ffffff; font-size: 8px; }
</style>
<script type="text/javascript" src="/js/jquery.js"></script>
<script type="text/javascript" src="/js/jquery-ui.js"></script>
<script>
$(document).ready(function(){


                $(\'#order_start\').datepicker();
                $(\'#order_start\').datepicker(\'option\', {dateFormat: \'dd.mm.yy\', showAnim: \'show\' });
   	 	$(\'#order_start\').datepicker( "option", "monthNames", [\'Январь\',\'Февраль\',\'Март\',\'Апрель\',\'Май\',\'Июнь\',\'Июль\',\'Август\',\'Сентябрь\',\'Октябрь\',\'Ноябрь\',\'Декабрь\'] );
		$(\'#order_start\').datepicker( "option", "dayNamesMin", [\'Пн\', \'Вт\', \'Ср\', \'Чт\', \'Пт\', \'Сб\', \'Вс\'] );


                $(\'#order_end\').datepicker();
                $(\'#order_end\').datepicker(\'option\', {dateFormat: \'dd.mm.yy\', showAnim: \'show\' });
		$(\'#order_end\').datepicker( "option", "monthNames", [\'Январь\',\'Февраль\',\'Март\',\'Апрель\',\'Май\',\'Июнь\',\'Июль\',\'Август\',\'Сентябрь\',\'Октябрь\',\'Ноябрь\',\'Декабрь\'] );
                $(\'#order_end\').datepicker( "option", "dayNamesMin", [\'Пн\', \'Вт\', \'Ср\', \'Чт\', \'Пт\', \'Сб\', \'Вс\'] );


});
</script>
</head>
<body>
<center>
	<form action="http://www.wobot.ru/add.php" method="post" id="sendEmail">
	<input type="hidden" name="submitted" id="submitted" value="true" />
	<input type="hidden" name="user_id" value="'.intval($user['user_id']).'" />
	<input type="hidden" name="user_name" value="'.addslashes($user['user_name']).'" />
	<input type="hidden" name="user_email" value="'.addslashes($user['user_email']).'" />
		<div class="forms">
			<div class="pcenter"><h1>Заявка на услугу</h1></div>

			<div class="pleft"><label for="user_name">Ключевые слова</label></div>
			<div class="pright"><input type="text" class="text" name="order_name" id="order_name" value="" /></div>

			<div class="pleft"><label for="user_email">Начало мониторинга</label></div>
			<div class="pright"><input type="text" class="text" name="order_start" id="order_start" value="'.date("d.m.Y").'" /></div>

			<div class="pleft"><label for="user_pass">Конец мониторинга</label></div>
			<div class="pright"><input type="text" class="text" name="order_end" id="order_end" value="'.date("d.m.Y").'" /></div>

			<div class="pleft"><label for="user_company">Тариф</label></div>
			<div class="pright"><input type="text" class="text" name="order_tariff" id="order_tariff" value="Базовый" /></div>

			<div class="pcenter" style="height: 100px;"><a href="#" onclick="$(\'#sendEmail\').submit();
"><img src="/img/button_order.png" border="0" alt="Отправить" title="Отправить"></a></div>
		</div>
	</form>
</center>
</body>
</html>
';
}
/*
$(\'#sendEmail\').submit(function() {
	$(".error").hide();
	var hasError = false;
	//var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;

	//var emailFromVal = $("#email").val();
	//if(emailFromVal == \'\') {
	//	$("#email").after(\'<span class="error">Заполните поле</span>\');
	//	hasError = true;
	//} else if(!emailReg.test(emailFromVal)) {
	//	$("#email").after(\'<span class="error">Не верный email</span>\');
	//	hasError = true;
	//}

	var subjectVal = $("#user_email").val();
	if(subjectVal == \'\') {
		$("#user_email").after(\'<span class="error">Заполните поле</span>\');
		hasError = true;
	}


	var subjectVal = $("#user_pass").val();
	if(subjectVal == \'\') {
		$("#user_pass").after(\'<span class="error">Заполните поле</span>\');
		hasError = true;
	}

	var subjectVal = $("#user_name").val();
	if(subjectVal == \'\') {
		$("#user_name").after(\'<span class="error">Заполните поле</span>\');
		hasError = true;
	}

	var messageVal = $("#user_company").val();
	if(messageVal == \'\') {
		$("#user_company").after(\'<span class="error">Заполните поле</span>\');
		hasError = true;
	}


	$.post("/new/add", 
		$("#sendEmail").serialize(),
		function(data){
			//if (data==\'Заявка отправлена\') alert(\'Заявка отправлена\');
                            if (hasError == false) {
                            alert(\'Заявка отправлена\');
                                parent.jQuery.fancybox.close();
                          }	
                    }
	);

	return false;
});
*/
//}
//}
?>
