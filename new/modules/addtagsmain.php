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
//print_r($_GET);
$db = new database();
$db->connect();
//print_r(json_decode(urldecode($_GET['tags']),true));
auth();
if (!$loged) die();
$mast=json_decode(urldecode($_GET['tags']),true);

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
	//print_r($_POST);
	$query='SELECT * FROM blog_tag WHERE user_id='.$user['user_id'];
	//echo $query;
	$mtt=array('1','2','3','4','5','6','7','8','9','10');
	$respost=$db->query($query);
   while ($rpp=$db->fetch($respost))
{
	$mtt2[]=$rpp['tag_tag'];
}
print_r($mtt2);
foreach ($mtt as $item)
{
	if (!in_array($item,$mtt2))
	{
		$mtt3[]=$item;
	}
}
	$query='INSERT INTO blog_tag (user_id,tag_name,tag_tag) VALUES ('.$user['user_id'].',\''.$_POST['user_tag'].'\','.$mtt3[0].')';
	//echo $query;
   $respost=$db->query($query);
}
if ($ttttt==1){
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title></title>
<meta name="robots" content="all" />
<meta content="text/html; charset=utf-8" http-equiv="Content-Type">
<script type="text/javascript" src="/js/jquery.js"></script>
<script type="text/javascript" src="/js/jquery-ui.js"></script>
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
	<form action="/new/order/<? echo $_POST['orid']; ?>" method="post" id="sendEmail" target="_parent">
	<input type="hidden" name="submitted" id="submitted" value="true" />
	<?
	foreach ($_POST as $key => $item)
	{
		echo '<input type="hidden" name="'.$key.'" value="'.$item.'" />';
	}
	?>
		<div class="forms">
			<div class="pcenter"><h1>Тэг создан</h1></div>
		</div>
					<div class="pcenter" style="height: 100px; margin-left: 50px; margin-top: 10px;"><a href="#" onclick="$('#sendEmail').submit();
		"><img src="/img/button_order.png" border="0" alt="Отправить" title="Отправить"></a></div>
	</form>
</center>
</body>
</html>
<?

}
else
{
	$mas=json_decode(($_GET['pos']),true);
	//print_r($mas);
	//echo 1;
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
		    <link href=\'/css/wobot_lk.css\' rel=\'stylesheet\' type=\'text/css\' /> 
		    <link href=\'/img/favicon_lk.gif\' rel=\'shortcut icon\' /> 
		    <link href=\'/css/details_lk.css\' rel=\'stylesheet\' type=\'text/css\' /> 
<link href=\'/css/old_details_lk.css\' rel=\'stylesheet\' type=\'text/css\' />
<style>
body { padding: 0; margin: 0; background: #83b226; width: 504px; }
h1 { color: #ffffff; font-size: 18px; margin: 0; padding: 0; }
.pright { width: 244px; height: 30px; padding: 3px; text-align: left; float: left; color: #ffffff; font-size: 12px; }
.pleft { width: 232px; height: 18px; padding: 9px; text-align: left; float: left; color: #ffffff; font-size: 15px; }
.pcenter { width: 482px; height: 18px; padding: 9px; margin: 10 0 0 0; text-align: center; float: left; color: #ffffff; font-size: 12px; }
.pclr { width: 482px; height: 18px; padding: 9px; text-align: left; float: left; color: #ffffff; font-size: 12px; }
.clear { float: none; width: 100%; }
.forms { width: 504px; padding-left: 50px; padding-right: 50px; padding-top: 5px; padding-bottom: 5px; margin: 0; }
.text { width: 150px; height: 30px; font-size: 12px; color: #000000; background: url(\'/img/order_part.png\') no-repeat; border: 0; padding: 9px; padding-bottom: 0px; }
.error { color: #ffffff; font-size: 8px; }
</style>
<script type="text/javascript" src="/js/jquery.js"></script>
<script type="text/javascript" src="/js/jquery-ui.js"></script>
<script>';
if(isset($_POST['submitted'])) {
	echo 'parent.jQuery.fancybox.close();';
}
echo '

$(document).ready(function(){
});
</script>
</head>
<body>
<center>
	<form action="/new/addtagsmain" method="post" id="sendEmail">
	<input type="hidden" name="submitted" id="submitted" value="true" />';
	foreach (json_decode(($_GET['pos']),true) as $key => $item)
	{
		echo '<input type="hidden" name="'.$key.'" value="'.$item.'" />';
	}
	echo '
	<input type="hidden" name="orid" value="'.intval($_GET['orid']).'" />
		<div class="forms">
			<div class="pcenter"><h1>Добавить тег</h1></div>
			<br>
			<div class="pleft"><label for="user_name">Название тега: </label></div>
			<div class="pright"><input type="text" class="text" onkeydown="if ($(\'#user_name\').attr(\'value\').length>10) {$(\'#user_name\').attr(\'value\',$(\'#user_name\').attr(\'value\').substr(0,$(\'#user_name\').attr(\'value\').length-1));}" name="user_tag" id="user_name" value="" /></div>
			<div id="err" style="margin: 5 0 0 0;">'.$err.'</div>	
			<div class="pcenter" style="height: 100px;"><a href="#"  onclick="$(\'#sendEmail\').submit();"><img src="/img/button_order.png" border="0" alt="Отправить" title="Отправить"></a></div>
		</div>
	</form>
</center>
</body>
</html>
';
}

?>
