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
	if ($user['user_priv']&4)
	{
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
	<form action="/new/adduser" method="post" id="sendEmail">
	<input type="hidden" name="submitted" id="submitted" value="true" />
		<div class="forms">
			<div class="pcenter"><h1>Анкета обновлена</h1></div>
		</div>
	</form>

</center>
</body>
</html>
<?
if ($_POST['user_id']==0)
{
	$res=$db->query('INSERT INTO users (user_email, user_pass, user_name, user_company, user_contact, user_money) values ("'.$_POST['user_email'].'","'.md5($_POST['user_pass']).'","'.$_POST['user_name'].'","'.$_POST['user_company'].'","'.$_POST['user_contact'].'","'.intval($_POST['user_money']).'")');
}elseif ($_POST['user_id']>0)
{
	$res=$db->query('UPDATE users SET user_email="'.$_POST['user_email'].'", user_pass="'.md5($_POST['user_pass']).'", user_name="'.$_POST['user_name'].'", user_company="'.$_POST['user_company'].'", user_contact="'.$_POST['user_contact'].'", user_money="'.intval($_POST['user_money']).'" WHERE user_id='.intval($_POST['user_id']));
}

}
else
{
if ($_GET['user_id']>0)
{
	$res=$db->query('SELECT * FROM users WHERE user_id='.intval($_GET['user_id']).' limit 1');
	$row=$db->fetch($res);
}
		echo'
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
.text { width: 150px; height: 30px; font-size: 12px; color: #000000; background: url(\'/img/order_part.png\') no-repeat; border: 0; padding: 9px; padding-bottom: 0px; }
.error { color: #ffffff; font-size: 8px; }
</style>
<script type="text/javascript" src="/js/jquery.js"></script>
<script>
$(document).ready(function(){

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

		var messageVal = $("#user_contact").val();
		if(messageVal == \'\') {
			$("#user_contact").after(\'<span class="error">Заполните поле</span>\');
			hasError = true;
		}

		$.post("/new/adduser", 
			$("#sendEmail").serialize(),
			function(data){
				//if (data==\'Заявка отправлена\') alert(\'Заявка отправлена\');
                                if (hasError == false) {
	                            alert(\'Анкета обновлена\');
                                    parent.jQuery.fancybox.close();
                              }	
                        }
		);

		return false;
	});
});
</script>
</head>
<body>
<center>
	<form action="/new/adduser" method="post" id="sendEmail">
	<input type="hidden" name="submitted" id="submitted" value="true" />
	<input type="hidden" name="user_id" value="'.intval($row['user_id']).'" />
		<div class="forms">
			<div class="pcenter"><h1>Пользователь</h1></div>

			<div class="pleft"><label for="user_name">Контактное лицо (ФИО)</label></div>
			<div class="pright"><input type="text" class="text" name="user_name" id="user_name" value="'.$row['user_name'].'" /></div>

			<div class="pleft"><label for="user_email">Email</label></div>
			<div class="pright"><input type="text" class="text" name="user_email" id="user_email" value="'.$row['user_email'].'" /></div>

			<div class="pleft"><label for="user_pass">Пароль</label></div>
			<div class="pright"><input type="password" class="text" name="user_pass" id="user_pass" value="" /></div>

			<div class="pleft"><label for="user_company">Компания</label></div>
			<div class="pright"><input type="text" class="text" name="user_company" id="user_company" value="'.$row['user_company'].'" /></div>

			<div class="pleft"><label for="user_contact">Контактная информация</label></div>
			<div class="pright"><input type="text" class="text" name="user_contact" id="user_contact" value="'.$row['user_contact'].'" /></div>

                        <div class="pleft"><label for="user_money">Баланс</label></div>
                        <div class="pright"><input type="text" class="text" name="user_money" id="user_money" value="'.$row['user_money'].'" /></div>

			<div class="pcenter" style="height: 100px;"><a href="#" onclick="$(\'#sendEmail\').submit();
"><img src="/img/button_order.png" border="0" alt="Отправить" title="Отправить"></a></div>
		</div>
	</form>
</center>
</body>
</html>
';
}
}
//}
?>
