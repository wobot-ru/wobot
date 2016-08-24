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
			echo $row['_email'].' <a href="/user/keywords/'.$row['user_id'].'">услуги</a><br>';
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
			<div class="pcenter"><h1>Услуги обновлены</h1></div>
		</div>
	</form>

</center>
</body>
</html>
<?
if ($_POST['order_start']!=0)
{
list($d,$m,$y)=explode('.',$_POST['order_start'],3);
$_POST['order_start']=mktime(0,0,0,$m,$d,$y);
}
if ($_POST['order_end']!=0)
{
list($d,$m,$y)=explode('.',$_POST['order_end'],3);
$_POST['order_end']=mktime(0,0,0,$m,$d,$y);
}

if ($_POST['order_id']==0)
{
	$res=$db->query('INSERT INTO blog_orders (user_id, order_name, order_keyword, order_start, order_end, ut_id) values ("'.$_POST['user_id'].'","'.addslashes($_POST['order_name']).'","'.addslashes($_POST['order_keyword']).'","'.$_POST['order_start'].'","'.$_POST['order_end'].'","'.$_POST['ut_id'].'")');
}elseif ($_POST['order_id']>0)
{
	$res=$db->query('UPDATE blog_orders SET order_name="'.$_POST['order_name'].'", order_keyword="'.$_POST['order_keyword'].'", order_start="'.$_POST['order_start'].'", order_end="'.$_POST['order_end'].'", ut_id="'.$_POST['ut_id'].'" WHERE order_id='.intval($_POST['order_id']));
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

		var subjectVal = $("#order_start").val();
		if(subjectVal == \'\') {
			$("#order_start").after(\'<span class="error">Заполните поле</span>\');
			hasError = true;
		}

		var subjectVal = $("#user_id").val();
		if(subjectVal == \'\') {
			$("#user_id").after(\'<span class="error">Заполните поле</span>\');
			hasError = true;
		}

		$.post("/new/addservice", 
			$("#sendEmail").serialize(),
			function(data){
				//if (data==\'Заявка отправлена\') alert(\'Заявка отправлена\');
                                if (hasError == false) {
	                            alert(\'Услуга обновлена\');
                                    parent.jQuery.fancybox.close();
                              }	
                        }
		);

		return false;
	});

        $(".servicelnk").click(function(event)
        {
	    $("#order_id").attr(\'value\',this.rel);
		$("#order_name").attr(\'value\',unescape($("#e_order_name"+this.rel).attr("value")));
	    $("#order_keyword").attr(\'value\',unescape($("#e_order_keyword"+this.rel).attr("value")));
	    $("#order_start").attr(\'value\',$("#e_order_start"+this.rel).attr("value"));
	    $("#order_end").attr(\'value\',$("#e_order_end"+this.rel).attr("value"));
		$("#ut_id").attr(\'value\',$("#e_ut_id"+this.rel).attr("value"));
        event.preventDefault();
        });

});
</script>
</head>
<body>
<center>
		<div class="forms">
		<div style="height: 100px; overflow-y: auto; overflow-x: hidden;">
';
	$res=$db->query('SELECT * FROM blog_orders WHERE user_id='.intval($_GET['user_id']));
	while($row=$db->fetch($res))
	{
		echo '<div class="pcenter">'.mb_substr($row['order_keyword'],0,50, 'UTF-8').' '.date("d.m.Y",$row['order_start']).' - '.($row['order_end']>0?date("d.m.Y",$row['order_end']):'текущая дата').' <a href="#" rel="'.$row['order_id'].'" class="servicelnk">редактировать</a></div>
<input type="hidden" id="e_order_name'.$row['order_id'].'" value="'.(urlencode($row['order_name'])).'">
<input type="hidden" id="e_order_keyword'.$row['order_id'].'" value="'.(urlencode($row['order_keyword'])).'">
<input type="hidden" id="e_ut_id'.$row['order_id'].'" value="'.$row['ut_id'].'">
<input type="hidden" id="e_order_start'.$row['order_id'].'" value="'.date("d.m.Y",$row['order_start']).'">
<input type="hidden" id="e_order_end'.$row['order_id'].'" value="'.($row['order_end']>0?date("d.m.Y",$row['order_end']):'0').'">';
	}
echo'
	</div>
		</div>
</center>

	<form action="/new/addservice" method="post" id="sendEmail">
	<input type="hidden" name="submitted" id="submitted" value="true" />
	<input type="hidden" name="user_id" value="'.intval($_GET['user_id']).'" />
	<input type="hidden" name="order_id" id="order_id" value="" />
		<div class="forms">
			<!--<div class="pcenter"><h1>Услуга</h1></div>-->

			<div class="pleft"><label for="order_name">Название</label></div>
			<div class="pright"><input type="text" class="text" name="order_name" id="order_name" value="" /></div>

			<div class="pleft"><label for="order_keyword">Ключевые слова</label></div>
			<div class="pright"><input type="text" class="text" name="order_keyword" id="order_keyword" value="" /></div>

			<div class="pleft"><label for="order_start">Начало мониторинга</label></div>
			<div class="pright"><input type="text" class="text" name="order_start" id="order_start" value="" /></div>

			<div class="pleft"><label for="order_end">Конец мониторинга</label></div>
			<div class="pright"><input type="text" class="text" name="order_end" id="order_end" value="" /></div>

			<div class="pleft"><label for="ut_id">Тариф</label></div>

						<div class="pright">
			  <select name="ut_id" id="ut_id">
			';
				$rs=$db->query('SELECT * FROM user_tariff as ut LEFT JOIN blog_tariff as bt ON ut.tariff_id=bt.tariff_id WHERE ut.user_id='.intval($_GET['user_id']));
				while($rw=$db->fetch($rs))
				{
					echo '<option value="'.$rw['ut_id'].'">'.$rw['tariff_name'].'</option>';
				}
			echo'
			   </select>
			</div>

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
