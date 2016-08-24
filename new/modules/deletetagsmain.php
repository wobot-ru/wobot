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
//print_r($_GET);
ignore_user_abort(true);
set_time_limit(0);
ini_set('max_execution_time',0);
ob_implicit_flush();
$db = new database();
$db->connect();
//print_r($_GET);
auth();
if (!$loged) die();

// login information set
//function cabinet()
//{
if ($_GET['submitted']==true)
{
	foreach ($_GET as $inddd => $posttt)
	{
		if ((substr($inddd, 0, 7)=='del_tag'))
		{
			$dtags[]=$posttt;
		}
	}
	//print_r($dtags);
	$res=$db->query('SELECT * FROM blog_orders WHERE user_id='.$_GET['orid']);
	while ($row = $db->fetch($res)) 
	{
		$udids.='order_id='.$row['order_id'].' OR ';
	}
	$udids=substr($udids,0,strlen($udids)-4);
	foreach ($dtags as $ttg)
	{
		$res=$db->query('UPDATE blog_post SET post_tag=REPLACE(post_tag, \''.$ttg.'\', \'\') WHERE order_id='.$udids);
		//echo 'DELETE FROM `wobot`.`blog_tag` WHERE `tag_tag` = '.$ttg.' AND user_id='.$_GET['orid'];
		$res=$db->query('DELETE FROM blog_tag WHERE tag_tag = '.$ttg.' AND user_id='.$_GET['orid']);
	}
}
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
.pcenter { width: 482px; height: 18px; padding: 9px; text-align: center; float: left; color: #ffffff; font-size: 12px; }
.pclr { width: 482px; height: 18px; padding: 9px; text-align: left; float: left; color: #ffffff; font-size: 12px; }
.clear { float: none; width: 100%; }
.forms { width: 504px; padding-left: 50px; padding-right: 50px; padding-top: 5px; padding-bottom: 5px; margin: 0; }
.text { width: 150px; height: 30px; font-size: 12px; color: #000000; background: url(\'/img/order_part.png\') no-repeat; border: 0; padding: 9px; padding-bottom: 0px; }
.error { color: #ffffff; font-size: 8px; }
</style>
<script type="text/javascript" src="/js/jquery.js"></script>
<script type="text/javascript" src="/js/jquery-ui.js"></script>
<script>';
if ($_GET['submitted']==true)
{
	echo 'parent.jQuery.fancybox.close();';
}
echo '
$(document).ready(function(){
});
</script>
</head>
<body>
<center>
	<form action="order/'.$_GET['oorid'].'" method="POST" id="sendem">
	</form>
	<form action="/new/deletetagsmain" method="GET" id="sendEmail">
	<input type="hidden" name="submitted" id="submitted" value="true" />';
	echo '
	<input type="hidden" name="orid" value="'.intval($_GET['orid']).'" />
	<input type="hidden" name="oorid" value="'.intval($_GET['oorid']).'" />
		<div class="forms">
			<div class="pcenter"><h1>Удалить теги:</h1></div>
			<br>';
			$res=$db->query('SELECT * FROM blog_tag WHERE user_id='.$_GET['orid']);
			while ($row = $db->fetch($res)) 
			{
				echo'
				<div class="pleft"><label for="user_name">'.$row['tag_name'].' </label></div>
				<div class="pright"><input type="checkbox" class="text" name="del_tag'.$row['tag_id'].'" id="user_name" value="'.$row['tag_tag'].'" /></div>';
			}
			
			echo'

			<div class="pcenter" style="height: 100px;"><a href="#" onclick="$(\'#sendEmail\').submit();
"><img src="/img/button_order.png" border="0" alt="Отправить" title="Отправить"></a></div>
		</div>
	</form>
</center>
</body>
</html>
';
}

?>
