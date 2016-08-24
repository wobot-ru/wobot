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
	<form action="/new/addusertariff" method="post" id="sendEmail">
	<input type="hidden" name="submitted" id="submitted" value="true" />
		<div class="forms">
			<div class="pcenter"><h1>Тарифы обновлены</h1></div>
		</div>
	</form>

</center>
</body>
</html>
<?

if ($_POST['ut_date']!=0)
{
list($d,$m,$y)=explode('.',$_POST['ut_date'],3);
$_POST['ut_date']=mktime(0,0,0,$m,$d,$y);
}
else $_POST['ut_date']=mktime(0,0,0,date('n'),date('J'),date('Y'));

if ($_POST['ut_id']==0)
{
	$res=$db->query('INSERT INTO user_tariff (user_id, tariff_id, ut_date) values ("'.$_POST['user_id'].'","'.$_POST['tariff_id'].'","'.$_POST['ut_date'].'")');
}elseif ($_POST['ut_id']>0)
{
	$res=$db->query('UPDATE user_tariff SET user_id="'.$_POST['user_id'].'", tariff_id="'.$_POST['tariff_id'].'", ut_date="'.$_POST['ut_date'].'" WHERE ut_id='.intval($_POST['ut_id']));
}

}
else
{
if ($_GET['user_id']>0)
{
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

		var subjectVal = $("#tariff_id").val();
		if(subjectVal == \'\') {
			$("#tariff_id").after(\'<span class="error">Заполните поле</span>\');
			hasError = true;
		}

		var subjectVal = $("#user_id").val();
		if(subjectVal == \'\') {
			$("#user_id").after(\'<span class="error">Заполните поле</span>\');
			hasError = true;
		}

		$.post("/new/addusertariff", 
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

        $(".servicelnk").click(function(event)
        {
	    $("#ut_id").attr(\'value\',this.rel);
	    //$("#tariff_id").attr(\'value\',$("#e_tariff_id"+this.rel).attr("value"));
	    $("#ut_date").attr(\'value\',$("#e_ut_date"+this.rel).attr("value"));
		$("select[id=\'tariff_id\'] option[value=\'"+$("#e_tariff_id"+this.rel).attr("value")+"\']").attr("selected", true);
        event.preventDefault();
        });

});
</script>
</head>
<body>
<center>
		<div class="forms">
';
	$res=$db->query('SELECT * FROM user_tariff as ut LEFT JOIN blog_tariff as b ON ut.tariff_id=b.tariff_id WHERE user_id='.intval($_GET['user_id']));
	while($row=$db->fetch($res))
	{
		echo '<div class="pcenter">'.$row['tariff_name'].' '.date("d.m.Y",$row['ut_date']).' <a href="#" rel="'.$row['ut_id'].'" class="servicelnk">редактировать</a></div>
<input type="hidden" id="e_tariff_id'.$row['ut_id'].'" value="'.$row['tariff_id'].'">
<input type="hidden" id="e_ut_date'.$row['ut_id'].'" value="'.date("d.m.Y",$row['ut_date']).'">';
	}
echo'
		</div>
</center>

	<form action="/new/addusertariff" method="post" id="sendEmail">
	<input type="hidden" name="submitted" id="submitted" value="true" />
	<input type="hidden" name="user_id" value="'.intval($_GET['user_id']).'" />
	<input type="hidden" name="ut_id" id="ut_id" value="" />
		<div class="forms">
			<div class="pcenter"><h1>Тариф пользователя</h1></div>

			<div class="pleft"><label for="tariff_id">Тариф</label></div>
			<div class="pright">
  <select name="tariff_id" id="tariff_id">
';
	$rs=$db->query('SELECT * FROM blog_tariff');
	while($rw=$db->fetch($rs))
	{
		echo '<option value="'.$rw['tariff_id'].'">'.$rw['tariff_name'].'</option>';
	}
echo'
   </select>
</div>
			<div class="pleft"><label for="ut_date">Дата оплаты</label></div>
			<div class="pright"><input type="text" class="text" name="ut_date" id="ut_date" value="" /></div>

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
}
//}
?>
