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

//$res=$db->query('UPDATE blog_tariff SET tariff_name="Создатель", tariff_desc="Доступен только создателям Wobot", tariff_price="10000000", tariff_quot="10000000" WHERE tariff_id=1');

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
			<div class="pcenter"><h1>Тариф обновлен</h1></div>
		</div>
	</form>

</center>
</body>
</html>
<?
if ($_POST['tariff_id']==0)
{
	$res=$db->query('INSERT INTO blog_tariff (tariff_name, tariff_desc, tariff_price, tariff_quot) values ("'.$_POST['tariff_name'].'","'.$_POST['tariff_desc'].'","'.$_POST['tariff_price'].'","'.$_POST['tariff_quot'].'")');
}elseif ($_POST['tariff_id']>0)
{
	$res=$db->query('UPDATE blog_tariff SET tariff_name="'.$_POST['tariff_name'].'", tariff_desc="'.$_POST['tariff_desc'].'", tariff_price="'.$_POST['tariff_price'].'", tariff_quot="'.$_POST['tariff_quot'].'" WHERE tariff_id='.intval($_POST['tariff_id']));
}

}
else
{
if ($_GET['tariff_id']>0)
{
	$res=$db->query('SELECT * FROM blog_tariff WHERE tariff_id='.intval($_GET['tariff_id']).' limit 1');
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

		var subjectVal = $("#tariff_name").val();
		if(subjectVal == \'\') {
			$("#tariff_name").after(\'<span class="error">Заполните поле</span>\');
			hasError = true;
		}


		var subjectVal = $("#tariff_desc").val();
		if(subjectVal == \'\') {
			$("#tariff_desc").after(\'<span class="error">Заполните поле</span>\');
			hasError = true;
		}

		var subjectVal = $("#tariff_price").val();
		if(subjectVal == \'\') {
			$("#tariff_price").after(\'<span class="error">Заполните поле</span>\');
			hasError = true;
		}

		var messageVal = $("#tariff_quot").val();
		if(messageVal == \'\') {
			$("#tariff_quot").after(\'<span class="error">Заполните поле</span>\');
			hasError = true;
		}

		$.post("/new/addtariff", 
			$("#sendEmail").serialize(),
			function(data){
				//if (data==\'Заявка отправлена\') alert(\'Заявка отправлена\');
                                if (hasError == false) {
	                            alert(\'Тариф обновлен\');
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
	<form action="/new/addtariff" method="post" id="sendEmail">
	<input type="hidden" name="submitted" id="submitted" value="true" />
	<input type="hidden" name="tariff_id" value="'.intval($row['tariff_id']).'" />
		<div class="forms">
			<div class="pcenter"><h1>Тариф</h1></div>

			<div class="pleft"><label for="tariff_name">Название</label></div>
			<div class="pright"><input type="text" class="text" name="tariff_name" id="tariff_name" value="'.$row['tariff_name'].'" /></div>

			<div class="pleft"><label for="tariff_desc">Описание</label></div>
			<div class="pright"><input type="text" class="text" name="tariff_desc" id="tariff_desc" value="'.$row['tariff_desc'].'" /></div>

			<div class="pleft"><label for="tariff_price">Цена</label></div>
			<div class="pright"><input type="text" class="text" name="tariff_price" id="tariff_price" value="'.$row['tariff_price'].'" /></div>

			<div class="pleft"><label for="tariff_quot">Кол-во тем</label></div>
			<div class="pright"><input type="text" class="text" name="tariff_quot" id="tariff_quot" value="'.$row['tariff_quot'].'" /></div>

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
