<?
require_once('com/config.php');
require_once('com/func.php');
require_once('com/db.php');
require_once('bot/kernel.php');
require_once('com/auth.php');
//$fpizdec = fopen('debug.txt', 'a');
//fwrite($fpizdec,$_POST['order_id']." ".$_POST['fav']."\n");
auth();
if (!$loged) die();
if (($_GET['plink']!='') && ($_GET['kword']!=''))
{
	$ktext=getkeyword(urldecode(urldecode($_GET['kword'])),urldecode(urldecode($_GET['plink'])));
	if (($ktext)!='')
	{
		echo '<base href="http://'.parse_url(urldecode(urldecode($_GET['plink'])),PHP_URL_HOST).'/">'.$ktext;
	}
	else
	{
		echo '
		<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
		<html>
		<head>
		<title>WOBOT &copy; Research - Страница не доступна</title>
		<meta content="text/html; charset=utf-8" http-equiv="Content-Type">
		
		<style>
		a:link {color:#3e3f43; text-decoration: underline; padding: 0; margin: 0; }
		a:visited {color:#3e3f43; text-decoration: underline; }
		a:hover {color:#3e3f43; text-decoration: none; }
		a:active {color:#83b226; text-decoration: none; }
		
		</style>
		</head>
		
		<body style="min-width: 1000px; max-width: 1000px;margin: 0px;padding: 0px; font-family: tahoma, helvetica, verdana; background: 		#ffffff url(\'/img/bg.jpg\') no-repeat center;">
		<center>
		<table align="center" valign="center" height="100%">
		<td>
		<img src="/img/wobot_logo.png" alt="wobot">
		<h1>Извините выбранный вами сервис недоступен</h1>
		<p><a href="'.urldecode(urldecode($_GET['plink'])).'">'.urldecode(urldecode($_GET['plink'])).'</a></p>
		</td>
		</table>
		</center>
		</body>
		</html>';
		
	}
}
if (($_POST['plink']!='') && ($_POST['phrase']!=''))
{
	echo GetFullPost($_POST['plink'],$_POST['phrase']);
}
if ((intval($_POST['order_id'])!=0) && ($_POST['typep']!=''))
{
   echo $_POST['typep'];
   $db->query("UPDATE blog_post SET post_type=".$_POST['typep']." WHERE order_id='".$_POST['order_id']."' AND post_link='".addslashes($_POST['link'])."'");
}
if (intval($_POST['order_id'])!=0)
{
			$res=$db->query("SELECT * from blog_orders WHERE order_id=".intval($_POST['order_id'])." and user_id=".intval($user['user_id'])." LIMIT 1"); // проверка order_id
		if (mysql_num_rows($res)==0) die();

			//---------------------------------SPAM ADD------------------------------------
			/*if ($_POST['spam']!='')
			{
				$resspam1=$db->query("SELECT * from blog_spam WHERE order_id='".intval($_POST['order_id'])."' AND post_link='".$_POST['spam']."' LIMIT 1");
				$orderspam1 = $db->fetch($resspam1);
				if (intval($orderspam1['spam_id'])==0)
				{
				$resspam2=$db->query("INSERT INTO  `blog_spam` (`spam_link` ,`order_id`) VALUES ('".$_POST['spam']."' , '".$_POST['order_id']."');");
				}
				$_POST['spam']='';
			}*/
			//-----------------------------------------------------------------------------

			 //---------------------------------SPAM ADD------------------------------------
			if ($_POST['spam']!='')
			{
				$resspam1=$db->query("SELECT * from blog_post WHERE order_id='".intval($_POST['order_id'])."' AND post_link='".addslashes($_POST['spam'])."' LIMIT 1");
				$orderspam1 = $db->fetch($resspam1);
					$resspam2=$db->query("UPDATE `blog_post` set post_spam=".(intval($orderspam1['post_spam'])==1?0:1)." WHERE  order_id='".intval($_POST['order_id'])."' AND post_link='".addslashes($_POST['spam'])."' LIMIT 1");
				if (intval($orderspam1['post_spam'])==0) echo "spamm2";
				else echo "spamm";
				
				$_POST['spam']='';
			}
			//-----------------------------------------------------------------------------		

			//---------------------------------FAV ADD------------------------------------
			if ($_POST['fav']!='')
			{
				$resfav1=$db->query("SELECT * from blog_post WHERE order_id='".intval($_POST['order_id'])."' AND post_link='".addslashes($_POST['fav'])."' LIMIT 1");
				$orderfav1 = $db->fetch($resfav1);
				//if (intval($orderfav1['fav_id'])==0)
				//{
					$resfav2=$db->query("UPDATE `blog_post` set post_fav=".(intval($orderfav1['post_fav'])==1?0:1)." WHERE order_id='".intval($_POST['order_id'])."' AND post_link='".addslashes($_POST['fav'])."' LIMIT 1");
					//fwrite($fpizdec,"fav");
					//echo "fav";
				//}
				if (intval($orderfav1['post_fav'])==0) echo "fav";
				else echo "fav2";
				$_POST['fav']='';
				//fwrite($fpizdec,"fav2");
			}


			if ($_POST['positive']!='')
			{
				//$resfav1=$db->query("SELECT * from blog_post WHERE order_id='".intval($_POST['order_id'])."' AND post_link='".addslashes($_POST['positive'])."' LIMIT 1");
				//$orderfav1 = $db->fetch($resfav1);
				$resfav2=$db->query("UPDATE blog_post SET post_nastr=1 WHERE order_id='".intval($_POST['order_id'])."' AND post_link='".addslashes($_POST['positive'])."' LIMIT 1");
				echo "green";
			}
			if ($_POST['negative']!='')
			{
				//$resfav1=$db->query("SELECT * from blog_post WHERE order_id='".intval($_POST['order_id'])."' AND post_link='".addslashes($_POST['negative'])."' LIMIT 1");
				//$orderfav1 = $db->fetch($resfav1);
				$resfav2=$db->query("UPDATE blog_post SET post_nastr=-1 WHERE order_id='".intval($_POST['order_id'])."' AND post_link='".addslashes($_POST['negative'])."' LIMIT 1");
				echo "red";
			}
			if ($_POST['neutral']!='')
			{
				//$resfav1=$db->query("SELECT * from blog_post WHERE order_id='".intval($_POST['order_id'])."' AND post_link='".addslashes($_POST['neutral'])."' LIMIT 1");
				//$orderfav1 = $db->fetch($resfav1);
				$resfav2=$db->query("UPDATE blog_post SET post_nastr=0 WHERE order_id='".intval($_POST['order_id'])."' AND post_link='".addslashes($_POST['neutral'])."' LIMIT 1");
				echo "black";
			}
			//echo 123;
			//-----------------------------------------------------------------------------		
}
//fclose($fpizdec);
?>
