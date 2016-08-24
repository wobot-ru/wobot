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
require_once('com/facebook.php');
require_once('/var/www/com/checker.php');

function query($url,$post)
{
	$ch = curl_init();

	curl_setopt($ch, CURLOPT_URL,$url);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS,$post);

	// receive server response ...
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

	$server_output = curl_exec ($ch);

	curl_close ($ch);

	return $server_output;
}

function validateURL($url)
{
$pattern = '/^(([\w]+:)?\/\/)?(([\d\w]|%[a-fA-f\d]{2,2})+(:([\d\w]|%[a-fA-f\d]{2,2})+)?@)?([\d\w][-\d\w]{0,253}[\d\w]\.)+[\w]{2,4}(:[\d]+)?(\/([-+_~.\d\w]|%[a-fA-f\d]{2,2})*)*(\?(&amp;?([-+_~.\d\w]|%[a-fA-f\d]{2,2})=?)*)?(#([-+_~.\d\w]|%[a-fA-f\d]{2,2})*)?$/';
return preg_match($pattern, $url);
}

function check_src($hn)
{
	global $db;
	$outmas['in_base']=0;
	$outmas['in_azure']=0;
	$qw=$db->query('SELECT * FROM blog_post WHERE post_host=\''.$hn.'\' LIMIT 1');
	//echo 'SELECT * FROM blog_post WHERE post_host=\''.$hn.'\' LIMIT 1';
	$count=$db->fetch($qw);
	if ($count>0)
	{
		$outmas['in_base']=1;
	}
	$cont=parseUrl('http://wobotrest.cloudapp.net/contains.aspx?domain='.$hn);
	if ($cont=='yes')
	{
		$outmas['in_azure']=1;
	}
	return $outmas;
}

$facebook = new Facebook(array(
  'appId'  => '158565747504200',
  'secret' => '5d574014dc4fc55ab814e6c804b967f9',
));

ignore_user_abort(true);
set_time_limit(0);
ini_set('max_execution_time',0);
ob_implicit_flush();


$db = new database();
$db->connect();

auth();
if (!$loged) die();

/*
Array
(
    [action] => adduser
    [user_email] => 123
    [user_pass] => 123
    [user_name] => 123
    [user_contact] => 123
    [user_company] => 123
    [user_money] => 
)
*/

echo '
	<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
	<html>
	<head>
	<title>WOBOT &copy; Панель администратора (&beta;-version)</title>
	<meta name="description" content="" />
	<meta name="keywords" content="Wobot реклама анализ раскрутка баннер" />
	<meta name="author" content="Wobot media" />
	<meta name="robots" content="all" />
	<meta content="text/html; charset=utf-8" http-equiv="Content-Type">
	<link rel="stylesheet" type="text/css" href="/css/style.css">
	<script type="text/javascript" src="/js/jquery.js"></script>
	<script type="text/javascript" src="/js/jquery.flot.js"></script>
	<script type="text/javascript" src="/js/jquery.cookie.js"></script>

	<script type="text/javascript" src="/js/jquery.flot.selection.js"></script>
	<script type="text/javascript" src="/js/jquery.fancybox-1.3.0.pack.js"></script>
	<link rel="stylesheet" type="text/css" href="/css/jquery.fancybox-1.3.0.css" media="screen" />
	<link type="text/css" href="/css/smoothness/jquery-ui-1.8.9.custom.css" rel="stylesheet" /> 
	<script type="text/javascript" src="/js/jquery-ui-1.8.9.custom.min.js"></script> 
	<script type="text/javascript" src="/js/jquery.blockUI.js"></script>

	<script type="text/javascript" src="/js/jquery-ui.js"></script>
	<script type="text/javascript" src="/js/jquery.pagination.js"></script>
	<script src="/js/jquery.tipsy.js" type="text/javascript"></script>
	<script type="text/javascript" src="/js/jquery.ui.datepicker-ru.js"></script>


	    <script type="text/javascript">

			var loading_status = 0;

			function preloadevent()
			{
				loading_status++;
				if (loading_status==1)
				{
					$(window).bind(\'unload\', function() {
				  		//alert(\'вы покинули страницу во время загрузки!\');
						return false;
					});
				}
			}

			function afterloadevent()
			{
				loading_status--;
				if (loading_status==0)
				{
					$(window).unbind(\'unload\');
				}
			}

	        function loaditem(href, block, clbk)
	        {
					preloadevent();
			//alert("/"+href+" "+block);
	                $(block).block({
	                        message: \'<img src="/img/ajax-loader.gif" alt="loading">\'
	                });
	                $(block).load("/new/"+href, function(response, status, xhr)
	                {
	                    if (status == "error")
	                    {
	                    var msg = "Sorry but there was an error: ";
	                    $("#error").html(msg + xhr.status + " " + xhr.statusText);
	                    }
	                    else
	                    {
				//alert(\'loaded\');
							$.cookie("page", 0);
							afterloadevent();
				if (clbk) clbk();
	                        //$(block).unblock();
	                    }
	                });
	        }

	        function loaditem2(href, block)
	        {
					preloadevent();
	                //alert("/"+href+" "+block);
	                $(block).block({
	                        message: \'<img src="/img/ajax-loader.gif" alt="loading">\'
	                });
	                $(block).load("/new/"+href, function(response, status, xhr)
	                {
	                    if (status == "error")
	                    {
	                    var msg = "Sorry but there was an error: ";
	                    $("#error").html(msg + xhr.status + " " + xhr.statusText);
	                    }
	                    else
	                    {
	                        //alert(\'loaded\');
				makegraph();
				makemap();
	                        $(block).unblock();
							afterloadevent();
	                    }
	                });
	        }

	        function loaditem3(href, block)
	        {
					preloadevent();
	                //alert("/"+href+" "+block);
	                $(block).block({
	                        message: \'<h1>Загрузка...</h1>\'
	                });
	                $(block).load("/new/"+href, function(response, status, xhr)
	                {
	                    if (status == "error")
	                    {
	                    var msg = "Sorry but there was an error: ";
	                    $("#error").html(msg + xhr.status + " " + xhr.statusText);
	                    }
	                    else
	                    {
	                        //alert(\'loaded\');
				makegraph2();
	                        $(block).unblock();
							afterloadevent();
	                    }
	                });
	        }


		function loadmodal(href,width,height)
		{
			if (!width) width=604;
			if (!height) height=400;
	                        $.fancybox({
				\'href\' : \'/new/\'+href,
	                        \'width\' : width,
	                        \'height\': height,
	                        \'scrolling\'           : \'no\',
	                        \'titleShow\'           : false,
	                            \'padding\'           : 0,
	                        \'autoScale\'           : false,
	                        \'transitionIn\'                : \'none\',
	                                \'transitionOut\'               : \'none\',
	                        type    : "iframe",
	                        });
		}

	     $(document).ready(function(){


	     });

	    </script>

	</head>
	<body><a href="http://188.120.239.225/new/adminfaq" target="_blank">FAQ по админке</a><br>
	<a href="http://wobot.ru/awstats/" target="_blank">Статистика сайта wobot.ru</a><br>
	<a href="http://188.120.239.225/tools/regi.php" target="_blank">Статистика по демо кабинетам</a><br>
	<a href="http://bmstu.wobot.ru/social/export/" target="_blank">Интерфейс для выгрузки по группам</a><br>
	<a href="http://188.120.239.225/tools/promo.php" target="_blank">Интерфейс для генерации промокодов</a>
';

// FACEBOOK login


$user_fb = $facebook->getUser();

if ($user_fb) {
  try {
    // Proceed knowing you have a logged in user who's authenticated.
    $user_profile = $facebook->api('/me');
  } catch (FacebookApiException $e) {
    error_log($e);
    $user_fb = null;
  }
}

// Login or logout url will be needed depending on current user state.
if ($user_fb) {
  $logoutUrl = $facebook->getLogoutUrl();
} else {
  $loginUrl = $facebook->getLoginUrl();
}

/*
echo'
<h3>Access token</h3>
<pre>'.$_SESSION['fb_158565747504200_access_token'].'</pre>
';
*/

echo '<div>';
if ($user_fb)
{
echo '
      <img src="https://graph.facebook.com/'.$user_fb.'/picture"> <a href="http://www.facebook.com/'.$user_profile['id'].'">'.$user_profile['name'].'</a>
';
	echo' ( <a href="'.$logoutUrl.'">Выйти</a> )';
}
else
{
	echo'
    <a href="'.$loginUrl.'">Зайти Facebook</a>
';
}
echo '</div>';

// EOF FACEBOOK login

if ($_POST['action']=='adduser')
{
	//echo intval(preg_match('/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/is',trim($_POST['user_email'])));
	if ((trim($_POST['user_email'])!='') && (preg_match('/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/is',trim($_POST['user_email']))))
	{
		if (($_POST['user_email']!='')&&($_POST['user_pass']!='')&&($_POST['user_name']!='')&&($_POST['user_contact']!='')&&($_POST['user_company']!=''))
		{
			$yet=$db->query('SELECT * FROM users WHERE user_email=\''.addslashes($_POST['user_email']).'\' LIMIT 1');
			if (mysql_num_rows($yet)==0)
			{
				$db->query('INSERT INTO users (user_email, user_pass, user_name, user_contact, user_company, user_money, user_ctime) values ("'.addslashes($_POST['user_email']).'","'.md5($_POST['user_pass']).'","'.addslashes($_POST['user_name']).'","'.addslashes($_POST['user_contact']).'","'.addslashes($_POST['user_company']).'","'.intval($_POST['user_money']).'", "'.time().'")');
				echo 'Пользователь добавлен<br>';
			}
			else
			{
				echo 'Такой пользователь уже существует<br>';
			}
		}
		else
		{
			echo 'Введены не все поля<br>';
		}
	}
	else
	{
		echo 'Логин для входа может быть только электронной почтой';
	}
}
elseif ($_POST['action']=='edituser')
{
	if ((intval($_POST['user_id'])!=0)&&($_POST['user_email']!='')&&($_POST['user_name']!='')&&($_POST['user_contact']!='')&&($_POST['user_company']!=''))
	{
		if ($_GET['topipe']==1)
		{
			$token='3e36afca3851ac8a611c9f62a34c7ab35b015fbd';
			$owner_id='71072';
			$user_company=$_POST['user_company'];
			$user_fio=$_POST['user_name'];
			$user_email=$_POST['user_email'];
			$user_phone=$_POST['user_contact'];
			$res=json_decode(query('https://api.pipedrive.com/v1/organizations?api_token='.$token,'name='.urlencode($user_company).'&owner_id='.intval($owner_id)),true);
			$org_id=$res['data']['id'];
			echo "org_id: ".$org_id."\n";

			//curl --data "name=username&owner_id=71072&email=test%40test.ru&phone=%2B79035386138&org_id=5" https://api.pipedrive.com/v1/persons?api_token=3e36afca3851ac8a611c9f62a34c7ab35b015fbd
			//{"success":true,"data":{"id":9}}
			$res=json_decode(query('https://api.pipedrive.com/v1/persons?api_token='.$token,'name='.urlencode($user_fio).'&owner_id='.intval($owner_id).'&email='.urlencode($user_email).'&phone='.urlencode($user_phone).'&org_id='.intval($org_id)),true);
			$person_id=$res['data']['id'];
			echo "person_id: ".$person_id."\n";

			//curl --data "title=ТЕСТКОМПАНИdeal&value=7500&currency=RUB&user_id=71072&person_id=9&org_id=5&visible_to=0" https://api.pipedrive.com/v1/deals?api_token=3e36afca3851ac8a611c9f62a34c7ab35b015fbd
			//{"success":true,"data":{"id":5}}
			$res=json_decode(query('https://api.pipedrive.com/v1/deals?api_token='.$token,'title='.urlencode($user_company.' deal').'&value=7500&currency=RUB&user_id='.intval($owner_id).'&person_id='.intval($person_id).'&org_id='.intval($org_id).'&visible_to=0'),true);
			$deal_id=$res['data']['id'];
			echo "deal_id: ".$deal_id."\n";
		}
		
		if (strlen($_POST['user_pass'])>0)
		$db->query('UPDATE users set user_email="'.addslashes($_POST['user_email']).'", user_pass="'.md5($_POST['user_pass']).'", user_name="'.addslashes($_POST['user_name']).'", user_contact="'.addslashes($_POST['user_contact']).'", user_company="'.addslashes($_POST['user_company']).'", user_money="'.intval($_POST['user_money']).'" WHERE user_id='.intval($_POST['user_id']));
		else
		$db->query('UPDATE users set user_email="'.addslashes($_POST['user_email']).'", user_name="'.addslashes($_POST['user_name']).'", user_contact="'.addslashes($_POST['user_contact']).'", user_company="'.addslashes($_POST['user_company']).'", user_money="'.intval($_POST['user_money']).'" WHERE user_id='.intval($_POST['user_id']));
		echo 'Пользователь обновлен<br>';
	}
	else
	{
		echo 'Введены не все поля<br>';
	}
}
elseif ($_POST['action']=='addut')
{
	/*
	Array
	(
	    [action] => addut
	    [ut_id] => addut
	    [user_id] => 108
	    [tariff_id] => 4
	)
	*/
	if ((intval($_POST['user_id'])!=0)&&(intval($_POST['tariff_id'])!=0))
	{
		$db->query('INSERT INTO user_tariff (user_id, tariff_id, ut_date) values ('.intval($_POST['user_id']).', '.intval($_POST['tariff_id']).', '.mktime(0,0,0,date('n')+1,date('j'),date('Y')).')');
		echo 'Тариф добавлен<br>';
	}
	else
	{
		echo 'Введены не все поля<br>';
	}
}
elseif ($_POST['action']=='editut')
{
	/*
	Array
	(
	    [action] => editut
	    [ut_id] => 67
	    [user_id] => 66
	    [tariff_id] => 4
	)
	*/
	if ((intval($_POST['ut_id'])!=0)&&(intval($_POST['user_id'])!=0)&&(intval($_POST['tariff_id'])!=0))
	{
		$db->query('UPDATE user_tariff set user_id='.intval($_POST['user_id']).', tariff_id='.intval($_POST['tariff_id']).', ut_date='.strtotime($_POST['ut_date']).' WHERE ut_id='.intval($_POST['ut_id']));
		if ($_POST['user_active']!=0)
		{
			$db->query('UPDATE users SET user_active=2 WHERE user_id='.intval($_POST['user_id']));
		}
		//echo 'UPDATE user_tariff set user_id='.intval($_POST['user_id']).', tariff_id='.intval($_POST['tariff_id']).', '.time().' WHERE ut_id='.intval($_POST['ut_id']);
		echo 'Тариф обновлен<br>';
	}
	else
	{
		echo 'Введены не все поля<br>';
	}
}
elseif ($_POST['action']=='deleteut')
{
	/*
	Array
	(
	    [action] => deleteut
	    [ut_id] => 67
	)
	*/
	if ((intval($_POST['ut_id'])!=0))
	{
		$db->query('DELETE FROM user_tariff WHERE ut_id='.intval($_POST['ut_id']));
		echo 'Тариф удален<br>';
	}
	else
	{
		echo 'Введены не все поля<br>';
	}
}
elseif ($_POST['action']=='deleteorder')
{
	// if (intval($_POST['order_id'])>0)
	// {
	// 	$db->query('DELETE FROM blog_orders WHERE order_id='.intval($_POST['order_id']));
	// 	$db->query('DELETE FROM blog_post WHERE order_id='.intval($_POST['order_id']));
	// 	$db->query('DELETE FROM blog_full_com WHERE ful_com_order_id='.intval($_POST['order_id']));
	// 	echo 'Тема удалена';
	// }
	// else
	// {
	// 	echo 'Тема не была удалена';
	// }
	//echo 'UPDATE blog_orders SET user_id=145,ut_id=153 WHERE order_id='.intval($_POST['order_id']);
	$db->query('UPDATE blog_orders SET user_id=145,ut_id=153,order_last='.mktime(0,0,0,date('n'),date('j'),date('Y')).' WHERE order_id='.intval($_POST['order_id']));
}
elseif ($_POST['action']=='addorder')
{
	/*
	Array
	(
    [action] => addorder
    [user_id] => 67
    [ut_id] => 3
    [order_id] => addorder
    [order_name] => 
    [order_keyword] => 
    [order_start] => 
    [order_end] => 
    [ful_com] => 1
    [order_engage] => 1
    [order_fb_rt] => 1
	)
	*/
	if (($_POST['order_name']!='')&&($_POST['order_start']!='')&&($_POST['order_end']!='')&&(intval($_POST['ut_id'])!=0)&&(intval(check_query($_POST['order_keyword']))==1))
	{
		$db->query('INSERT INTO blog_orders (order_date,user_id, ut_id, order_id, order_name, order_keyword, order_start, order_end, third_sources, ful_com, order_engage, order_nastr, youtube_last, order_lang) values ("'.time().'","'.intval($_POST['user_id']).'", "'.intval($_POST['ut_id']).'", "'.intval($_POST['order_id']).'", "'.addslashes($_POST['order_name']).'", "'.addslashes($_POST['order_keyword']).'", "'.strtotime($_POST['order_start']).'", "'.strtotime($_POST['order_end']).'", 1, "'.intval($_POST['ful_com']).'", "'.intval($_POST['order_engage']).'", "'.intval($_POST['order_nastr']).'", "'.intval($_POST['youtube_last']).'", 2)');
		echo parseUrl('http://188.120.239.225/tools/charge.php?order_id='.$db->insert_id());
		echo 'Тариф добавлен<br>';
	}
	else
	{
		echo 'Введены не все поля<br>';
	}
}
elseif ($_POST['action']=='editorder')
{
	/*
	Array
	(
    [action] => editorder
    [order_id] => 330
    [order_name] => Бауцентр | Бауцентор | Бауцентер
    [order_keyword] => Бауцентр | Бауцентор | Бауцентер
    [order_start] => 28.04.2011
    [order_end] => 19.05.2011
    [ut_id] => 3
	)
	*/
	if ((intval($_POST['order_id'])!=0)&&($_POST['order_name']!='')&&($_POST['order_keyword']!='')&&($_POST['order_start']!='')&&($_POST['order_end']!='')&&(intval($_POST['ut_id'])!=0))
	{
		list($date_d,$date_m,$date_y)=explode('.',$_POST['order_start']);
		$order_start=mktime(0,0,0,intval($date_m),intval($date_d),intval($date_y));
		list($date_d,$date_m,$date_y)=explode('.',$_POST['order_end']);
		$order_end=mktime(0,0,0,intval($date_m),intval($date_d),intval($date_y));		
		
		//echo 'UPDATE blog_orders SET order_name="'.addslashes($_POST['order_name']).'", order_keyword="'.addslashes($_POST['order_keyword']).'", order_start='.$order_start.', order_end='.$order_end.' WHERE order_id='.intval($_POST['order_id']);
		$db->query('UPDATE blog_orders SET order_name="'.addslashes($_POST['order_name']).'", order_keyword="'.addslashes($_POST['order_keyword']).'", order_start='.$order_start.', order_end='.$order_end.', ful_com="'.intval($_POST['ful_com']).'", order_engage="'.intval($_POST['order_engage']).'", order_nastr="'.intval($_POST['order_nastr']).'", youtube_last="'.intval($_POST['youtube_last']).'",order_lang="'.$_POST['ord_lan'].'" WHERE order_id='.intval($_POST['order_id']));
		$upd_ord=$db->query('SELECT order_start,order_end FROM blog_orders WHERE order_id='.intval($_POST['order_id']).' LIMIT 1');
		$ordd=$db->fetch($upd_ord);
		if (($_POST['order_start']!=$ordd['order_start']) || ($_POST['order_end']!=$ordd['order_end']))
		{
			$cont=parseUrl('http://188.120.239.225/tools/cashjob.php?order_id='.intval($_POST['order_id']));
		}
		echo 'Тариф обновлен<br>';
	}
	else
	{
		echo 'Введены не все поля<br>';
	}
}
elseif ($_POST['action']=='editactive')
{
	//echo 'UPDATE users SET user_active='.intval($_POST['us_active']).' WHERE user_id='.intval($_GET['user_id']);
	if ((intval($_POST['us_active'])>=0) && (intval($_GET['user_id'])>0))
	{
		$db->query('UPDATE users SET user_active='.intval($_POST['us_active']).' WHERE user_id='.intval($_GET['user_id']));
		echo 'Статус пользователя изменен';
	}
	else
	{
		echo 'Статус пользователя не удалось изменить';
	}
	//echo 'UPDATE blog_orders SET user_active='.intval($_POST['us_active']).' WHERE user_id='.intval($_GET['user_id']);
}
elseif ($_POST['action']=='recoverorder')
{
	//print_r($_POST);
	if ((intval($_POST['user_id'])>0) && (intval($_POST['order_id'])>0))
	{
		$db->query('UPDATE blog_orders SET user_id='.intval($_POST['user_id']).' WHERE order_id='.intval($_POST['order_id']));
		echo parseUrl('http://188.120.239.225/tools/charge.php?order_id='.intval($_POST['order_id']));
		echo 'Выбранная вами тема успешно разблокирована';
	}
	else
	{
		echo 'Выбранную вами тему разблокировать не удалось';
	}
	//echo 'UPDATE blog_orders SET user_id='.intval($_POST['user_id']).' WHERE order_id='.intval($_POST['order_id']);
	
}
elseif ($_POST['action']=='blockorder')
{
	if (intval($_POST['order_id'])>0)
	{
		$db->query('UPDATE blog_orders SET user_id=0 WHERE order_id='.intval($_POST['order_id']));
		echo 'Выбранная вами тема успешно заблокирована';
	}
	else
	{
		echo 'Выбранную вами тему заблокировать не удалось';
	}
	//echo 'UPDATE blog_orders SET user_id=0 WHERE order_id='.intval($_POST['order_id']);
}
elseif ($_POST['action']=='refreshcash')
{
	//print_r($_POST);
	$cont=parseUrl('http://188.120.239.225/tools/cashjob.php?order_id='.intval($_POST['order_id']));
	//echo 'http://bmstu.wobot.ru/tools/cashjob.php?order_id='.intval($_POST['order_id']);
	$mas=json_decode($cont,true);
	if ($mas['status']=='ok')
	{
		echo 'Обновление кеша запущено';
	}
	else
	{
		echo 'Обновление кеша не удалось запустить';
	}
}
elseif ($_POST['action']=='refreshpost')
{
	//print_r($_POST);
	$qorid=$db->query('UPDATE blog_orders SET third_sources=1 WHERE order_id='.$_POST['order_id']); 
	//echo 'UPDATE blog_orders SET third_sourcess=1 WHERE order_id='.$_POST['order_id'];
	echo 'Обновление запроса запущено';
}
elseif (($_POST['action']=='deleteuser') || (intval($_GET['delete_user_id'])!=0))
{
	//print_r($_POST);
	if ($_POST['action']=='deleteuser')
	{
		$qorid=$db->query('SELECT order_id FROM blog_orders as a LEFT JOIN user_tariff as b ON a.ut_id=b.ut_id LEFT JOIN users as c ON b.user_id=c.user_id WHERE c.user_id='.intval($_POST['user_id']));
		while ($orid=$db->fetch($qorid))
		{
			$db->query('UPDATE blog_orders SET user_id=145,ut_id=153 WHERE order_id='.intval($orid['order_id']));
			// $db->query('DELETE FROM blog_post WHERE order_id='.intval($orid['order_id']));
			// $db->query('DELETE FROM blog_full_com WHERE ful_com_order_id='.intval($orid['order_id']));
		}
		$db->query('DELETE FROM users WHERE user_id='.intval($_POST['user_id']));
		$db->query('DELETE FROM user_tariff WHERE user_id='.intval($_POST['user_id']));
	}
	elseif (intval($_GET['delete_user_id'])!=0)
	{
		$qorid=$db->query('SELECT order_id FROM blog_orders as a LEFT JOIN user_tariff as b ON a.ut_id=b.ut_id LEFT JOIN users as c ON b.user_id=c.user_id WHERE c.user_id='.intval($_GET['delete_user_id']));
		while ($orid=$db->fetch($qorid))
		{
			$db->query('UPDATE blog_orders SET user_id=145,ut_id=153 WHERE order_id='.intval($orid['order_id']));
			// $db->query('DELETE FROM blog_post WHERE order_id='.intval($orid['order_id']));
		}
		$db->query('DELETE FROM users WHERE user_id='.intval($_GET['delete_user_id']));
		$db->query('DELETE FROM user_tariff WHERE user_id='.intval($_GET['delete_user_id']));
	}
}
elseif ($_POST['action']=='addnewsrc')
{
	$msrc=explode(',',$_POST['srcs']);
	foreach ($msrc as $item)
	{
		$is_yet=0;
		$hn=parse_url($item);
		if ($hn['host']!='')
		{
			$url=($hn['scheme']==''?'http':$hn['scheme']).'://'.$hn['host'].'/';
			//echo $url;
			$hn=$hn['host'];
			$ahn=explode('.',$hn);
			$hn = $ahn[count($ahn)-2].'.'.$ahn[count($ahn)-1];
			$hh = $ahn[count($ahn)-2];
		}
		else
		{
			$url=($hn['scheme']==''?'http':$hn['scheme']).'://'.$hn['path'].'/';
			//echo $url;
			$hn=$hn['path'];
			$ahn=explode('.',$hn);
			$hn = $ahn[count($ahn)-2].'.'.$ahn[count($ahn)-1];
			$hh = $ahn[count($ahn)-2];
		}
		if (intval(validateURL(preg_replace('/[а-яё]/isu','w',$item)))==0)
		{
			continue;
		}
		$qw=$db->query('SELECT * FROM user_src WHERE hn=\''.$hn.'\' LIMIT 1');
		$yet=check_src($hn);
		if ((mysql_num_rows($qw)!=0) || ($yet['in_base']!=0) || ($yet['in_azure']!=0))
		{
			//continue;
			$is_yet=1;
		}
		//echo $item.' ';
		if (($hn!='') && ($hn!='.'))
		{
			//echo $item.' ';
			//echo 'INSERT INTO user_src (user_id,hn,fhn) VALUES (0,\''.$hn.'\',\''.$url.'\')';
			if ($is_yet==0)
			{
				$qw=$db->query('INSERT INTO user_src (user_id,hn,fhn) VALUES (0,\''.$hn.'\',\''.$url.'\')');
			}
			else
			{
				//echo 'YET!!!';
				//echo 'INSERT INTO user_src (user_id,hn,fhn,count,update) VALUES (0,\''.$hn.'\',\''.$url.'\',0,2)'."\n";
				$qw=$db->query('INSERT INTO user_src (`user_id`,`hn`,`fhn`,`count`,`update`) VALUES (0,\''.$hn.'\',\''.$url.'\',0,2)');
			}
			if ($is_yet==0)
			{
				parseUrl('http://188.120.239.225/tools/simpl_addsrc.php?src='.$db->insert_id());
			}
			//$mas['status']='ok';
			//echo json_encode($mas);
		}
	}
}

echo '<textarea style="width: 1200px; height: 150px;">';
print_r($_POST);
print_r($_GET);
print_r($_SESSION);
echo '</textarea><br>';
echo 'Добавление новых ресурсов(ресурсы вводим через запятую пример: <b>http://www.yandex.ru,http://www.google.com</b>)';
echo '<form method="post"><input type="hidden" name="action" value="addnewsrc"><textarea style="width: 1200px; height: 50px;" name="srcs"></textarea><input type="submit" value="Добавить"></form>';
echo 'Добавленные ресурсы:<br><div style="height: 100px; overflow: scroll;">';
$srclog=$db->query('SELECT * FROM user_src WHERE user_id=0 LIMIT 10');
$src_log_i=0;
while ($log_src=$db->fetch($srclog))
{
	//echo date('d.m.y H:i:s',$log['log_time']).' '.$log['log_ip'].'<br>';
	echo ($src_log_i+1).'. <i>'.$log_src['hn'].'</i> '.($log_src['update']==0?'Собирается':(($log_src['update']==2)?'Уже существует':($log_src['count']==0?'Нельзя добавить':'Добавлен'))).'<br>';
	$src_log_i++;
}
echo '</div><br>';
echo '
<script>

function afterload()
{

        $(".userlnk").click(function(event)
        {
            $(".userlnk").data("rel", this.rel); 
            $("#cntnt").fadeOut("fast", function()
            {
                $("#loading").show("fast");
                $("#cntnt").load("'.$config['html_root'].'new/"+$(".userlnk").data("rel"), function(response, status, xhr)
                {
                    if (status == "error")
                    {
                    var msg = "Sorry but there was an error: ";
                    $("#error").html(msg + xhr.status + " " + xhr.statusText);
                    }
                    else
                    {
                        $("#loading").hide("fast");
                        $("#cntnt").fadeIn("fast");
			afterload();
                    }
                })
            } )
        event.preventDefault();
        });

}

</script>

';
global $db, $config, $user;
if ($user['user_priv']&4)
{

if (intval($_GET['user_id'])>0)
{
	
echo '
<div style="width: 1190px; float: left; padding: 5px;">';
$res=$db->query('SELECT * FROM users WHERE user_id='.intval($_GET['user_id']).' LIMIT 1');
$reslog=$db->query('SELECT * FROM blog_log WHERE user_id='.intval($_GET['user_id']).' ORDER BY log_time DESC LIMIT 10');
$usr = $db->fetch($res);
echo'
Редактирование пользователя: [ <a href="?user_id=0">перейти к добавлению пользователя и созданию тарифов</a> ]
<form method="post" action="?user_id='.$usr['user_id'].'" id="submit_main_form">
<input type="hidden" name="action" value="edituser">
<input type="hidden" name="user_id" value="'.$usr['user_id'].'">
	<table>
	<tr>
	<td>E-mail*: </td><td><input type="text" name="user_email" value="'.$usr['user_email'].'"></td>
	</tr>
	<tr>
	<td>Пароль: </td><td><input type="text" name="user_pass" value=""></td>
	</tr>
	<tr>
	<td>Контактное лицо*: </td><td><input type="text" name="user_name" value="'.htmlspecialchars($usr['user_name']).'"></td>
	</tr>
	<tr>
	<td>Номер телефона*: </td><td><input type="text" name="user_contact" value="'.htmlspecialchars($usr['user_contact']).'"></td>
	</tr>
	<tr>
	<td>Название компании*: </td><td><input type="text" name="user_company" value="'.htmlspecialchars($usr['user_company']).'"></td>
	</tr>
	<tr>
	<td>Баланс счета: </td><td><input type="text" name="user_money" value="'.$usr['user_money'].'"></td>
	</tr>
	<tr>
	<td>Дата регистрации: '.($usr['user_ctime']!=0?date('d.m.y H:i:s',$usr['user_ctime']):'Неизвестно').'</td>
	</tr>
	<tr>
	<td>Промо-код: '.$usr['user_promo'].'</td>
	</tr>
	</table>
	
<input type="submit" value="Применить">
</form><!--getElementById-->
<input type="button" value="Перенести в PipeDRIVE" onclick="document.getElementById(\'submit_main_form\').action=\'?user_id='.$usr['user_id'].'&topipe=1\'; document.getElementById(\'submit_main_form\').submit();">
<br><br>
<form method="post" action="http://beta.wobot.ru/" target="_blank">
<input type="hidden" name="token" value="'.(md5(mb_strtolower($usr['user_email'],'UTF-8').':'.$usr['user_pass'])).'">
<input type="hidden" name="user_id" value="'.$usr['user_id'].'">
<input type="submit" value="Войти в beta">
</form>
<form method="post" action="http://production.wobot.ru/" target="_blank">
<input type="hidden" name="token" value="'.(md5(mb_strtolower($usr['user_email'],'UTF-8').':'.$usr['user_pass'])).'">
<input type="hidden" name="user_id" value="'.$usr['user_id'].'">
<input type="submit" value="Войти в production">
</form>
<form method="post" action="?user_id='.intval($_GET['user_id']).'" onsubmit="return confirm(\'Удалить кабинет?\')&&confirm(\'Точно удалить кабинет?\')&&confirm(\'Ну смари я тебя предупреждал!\')">
<input type="hidden" name="action" value="deleteuser">
<input type="hidden" name="ut_id" value="'.$usr['ut_id'].'">
<input type="hidden" name="user_id" value="'.$usr['user_id'].'">
<input type="submit" value="Удалить кабинет">
</form>
<form style="display: inline;" method="post" action="?user_id='.intval($_GET['user_id']).'">
<input type="hidden" name="action" value="editactive">
<input type="hidden" name="ut_id" value="'.$ut['ut_id'].'">
<input type="hidden" name="user_id" value="'.$ut['user_id'].'">
Активность:
<select name="us_active">
<option value="0" '.(($usr['user_active']==0)?' selected':'').'>Внутренний</option>
<option value="1" '.(($usr['user_active']==1)?' selected':'').'>Не активированный</option>
<option value="2" '.(($usr['user_active']==2)?' selected':'').'>Рабочий</option>
<option value="3" '.(($usr['user_active']==3)?' selected':'').'>Просроченный</option>
</select>
<input type="submit" value="изменить">
</form>
<br><br><a href="http://188.120.239.225/tools/groups_tp.php?user_id='.$_GET['user_id'].'">[ Добавить группы из социальных сетей пользователю ]</a><br>
';
$res=$db->query('SELECT * FROM blog_tariff');
while($tariffs[] = $db->fetch($res)) {};
$res=$db->query('SELECT * FROM user_tariff as ut LEFT JOIN blog_tariff as t ON ut.tariff_id=t.tariff_id WHERE ut.user_id='.intval($_GET['user_id']));
while($uts[] = $db->fetch($res)) {};

$res=$db->query('SELECT * FROM user_tariff as ut LEFT JOIN blog_tariff as t ON ut.tariff_id=t.tariff_id WHERE ut.user_id='.intval($_GET['user_id']));
while($ut = $db->fetch($res))
{
	echo '<br><br>
	<div style="border: 1px solid #aaa;">
	<form style="display: inline;" method="post" action="?user_id='.intval($_GET['user_id']).'">
	<input type="hidden" name="action" value="editut">
	<input type="hidden" name="ut_id" value="'.$ut['ut_id'].'">
	<input type="hidden" name="user_id" value="'.$ut['user_id'].'">
	<input type="hidden" name="user_active" value="'.$usr['user_active'].'">
	Тариф: <select name="tariff_id">';
	foreach($tariffs as $tariff)
	{
		if (intval($tariff['tariff_id'])!=0)
		echo '	<option value="'.$tariff['tariff_id'].'" '.(($tariff['tariff_id']==$ut['tariff_id'])?' selected':'').'>'.$tariff['tariff_name'].'</option>
		';
	}
	echo'
	</select>
	Действует до: <input type="text" name="ut_date" value="'.date("d.m.Y",$ut['ut_date']).'">
	<input type="submit" value="изменить">
	</form>

	<form style="display: inline;" method="post" action="?user_id='.intval($_GET['user_id']).'" onsubmit="return confirm(\'Удалить тариф?\');">
	<input type="hidden" name="action" value="deleteut">
	<input type="hidden" name="ut_id" value="'.$ut['ut_id'].'">
	<input type="submit" value="X">
	</form><br>
	Ключевые слова тарифа:
		<table>
		<tr>
		<td>Название</td><td>Кеш</td><td>Выдача</td><td>Запрос</td><td>Начало</td><td>Конец</td><td>Опции</td><td>Тариф</td><td>Язык</td><td>Действия</td>
		</tr>
		';
	//order_id 	order_date 	order_name 	order_keyword 	user_id 	order_finished 	order_start 	order_end 	order_last 	ut_id 	ful_com 	order_engage 	order_graph 	order_src 	order_metrics 	order_left 	order_left2
	$res2=$db->query('SELECT order_id,order_name,order_keyword,order_start,order_end,order_last,third_sources,order_engage,ful_com,order_nastr,order_lang FROM blog_orders WHERE ut_id='.intval($ut['ut_id']).' AND user_id!=0 ORDER BY order_id');
	$i=1;
	while($order = $db->fetch($res2))
	{
		if ($order['third_sources']!=0)
		{
			$mord[$order['order_id']]['start']=$order['order_start'];
			$mord[$order['order_id']]['end']=$order['order_end']==0?mktime(0,0,0,date('n'),date('j'),date('Y')):$order['order_end'];
			$mord[$order['order_id']]['tp']=$order['third_sources'];
			$mord[$order['order_id']]['kw']=$order['order_keyword'];
		}
		echo '
		<form method="post" action="?user_id='.intval($_GET['user_id']).'" id="refreshform_'.$order['order_id'].'">
		<input type="hidden" name="action" value="refreshcash">
		<input type="hidden" name="order_id" value="'.$order['order_id'].'">
		</form>
		<form method="post" action="?user_id='.intval($_GET['user_id']).'" id="refreshpost_'.$order['order_id'].'">
			<input type="hidden" name="action" value="refreshpost">
			<input type="hidden" name="order_id" value="'.$order['order_id'].'">
		</form>
		<form method="post">
		<input type="hidden" name="action" value="editorder">
		<input type="hidden" name="order_id" value="'.$order['order_id'].'">
		<tr>
		<td><p style="font-size: 8px; display: inline;">#'.intval($order['order_id']).'</p><input type="text" name="order_name" value="'.htmlspecialchars($order['order_name']).'" style="width: 20ex; height: 3em; padding: 1em 0 1em 0;"></td>
		<td><a href="#" onclick="document.getElementById(\'refreshform_'.$order['order_id'].'\').submit();">обновить</a></td>
		<td>'.($order['third_sources']==1?'собирается':'<a href="#" onclick="document.getElementById(\'refreshpost_'.$order['order_id'].'\').submit();">пересобрать</a>').'</td>
		<td><textarea name="order_keyword" cols="30" rows="3">'.$order['order_keyword'].'</textarea></td>
		<td><input type="text" name="order_start" id="ordertime" value="'.date("d.m.Y",$order['order_start']).'" style="width: 12ex; height: 3em; padding: 1em 0 1em 0;"></td>
		<td><input type="text" name="order_end" class="ordertime" value="'.date("d.m.Y",$order['order_end']).'" style="width: 12ex; height: 3em; padding: 1em 0 1em 0;"></td>
		<td>
		<input type="checkbox" name="ful_com" value="1" title="Полный текст"'.((intval($order['ful_com'])>0)?' checked':'').'>
		<input type="checkbox" name="order_engage" value="1" title="Engagement"'.((intval($order['order_engage'])>0)?' checked':'').'>
		<!--<input type="checkbox" name="order_fb_rt" value="'.((intval($order['order_fb_rt'])>0)?$order['order_fb_rt']:'1').'" title="Facebook realtime"'.((intval($order['order_fb_rt'])>0)?' checked':'').'>
		<input type="checkbox" name="google_plus_last" value="'.((intval($order['google_plus_last'])>0)?$order['google_plus_last']:'1').'" title="Google+ dont work"'.((intval($order['google_plus_last'])>0)?' checked':'').'>
		<input type="checkbox" name="youtube_last" value="'.((intval($order['youtube_last'])>0)?$order['youtube_last']:'1').'" title="Youtube dont work"'.((intval($order['youtube_last'])>0)?' checked':'').'>-->
		<input type="checkbox" name="order_nastr" value="1" title="Автотональность"'.((intval($order['order_nastr'])>0)?' checked':'').'>
		</td>
		<td>
		<select name="ut_id">';
		foreach($uts as $tariff)
		{
			if (intval($tariff['tariff_id'])!=0)
			echo '	<option value="'.$tariff['tariff_id'].'" '.(($tariff['tariff_id']==$ut['tariff_id'])?' selected':'').'>'.$tariff['tariff_name'].'</option>
			';
		}
		echo'
		</select>
		</td>
		<td>
		<select name="ord_lan">
			<!--<option value="0" '.($order['order_lang']==0?'selected':'').'>Дефолтный(Русский)</option>-->
			<option value="1" '.($order['order_lang']==1?'selected':'').'>Иностранный</option>
			<option value="2" '.(($order['order_lang']==2)||($order['order_lang']==0)?'selected':'').'>Русский</option>
			<option value="4" '.($order['order_lang']==4?'selected':'').'>Азербайджанский</option>
		</select>
		<input type="submit" value="применить"></form></td>
		<td>
		<form method="post" style="margin-bottom: 0;" action="?user_id='.intval($_GET['user_id']).'">
		<input type="hidden" name="action" value="blockorder">
		<input type="hidden" name="order_id" value="'.$order['order_id'].'">
		<input type="hidden" name="user_id" value="'.$usr['user_id'].'">
		<input type="submit" value="Заблокировать">
		</form>
		</td>
		<td>
		'.((strlen($_SESSION['fb_158565747504200_access_token'])>0)?'<a href="/project/facebook-manual.php?id='.$order['order_id'].'&token='.$_SESSION['fb_158565747504200_access_token'].'" target="_blank">FB</a>':'').'
		<form style="display: inline;" method="post" action="?user_id='.intval($_GET['user_id']).'" onsubmit="return confirm(\'Удалить тему?\')&&confirm(\'Точно удалить тему?\')&&confirm(\'Точно-точно удалить тему?\');">
		<input type="hidden" name="action" value="deleteorder">
		<input type="hidden" name="order_id" value="'.$order['order_id'].'">
		<input type="submit" value="X">
		</form></td>
		</tr>
		';
	}
	echo '
	</table>
	<table>
	<form method="post">
	<input type="hidden" name="action" value="addorder">
	<input type="hidden" name="user_id" value="'.$_GET['user_id'].'">
	<input type="hidden" name="ut_id" value="'.$ut['ut_id'].'">
	<input type="hidden" name="order_id" value="addorder">
	<tr>
	<td><input type="text" name="order_name" value="" style="width: 20ex; height: 3em; padding: 1em 0 1em 0;"></td>
	<td><textarea name="order_keyword" cols="60" rows="3"></textarea></td>
	<td><input type="text" name="order_start" class="ordertime" value="'.date("d.m.Y").'" style="width: 12ex; height: 3em; padding: 1em 0 1em 0;"></td>
	<td><input type="text" name="order_end" class="ordertime" value="'.date("d.m.Y").'" style="width: 12ex; height: 3em; padding: 1em 0 1em 0;"></td>
	<td>
	<input type="checkbox" name="ful_com" value="1" checked title="Полный текст">
	<input type="checkbox" name="order_engage" value="1" checked title="Engagement">
	<!--<input type="checkbox" name="order_fb_rt" value="1" title="Facebook realtime">
	<input type="checkbox" name="google_plus_last" value="1" title="Google+ dont work">
	<input type="checkbox" name="youtube_last" value="1" title="Youtube dont work">-->
	<input type="checkbox" name="order_nastr" value="1" title="Автотональность">
	</td>
	<td>
	';
	/*<!--<select name="ut_id">
	foreach($uts as $tariff)
	{
		if (intval($tariff['tariff_id'])!=0)
		echo '	<option value="'.$tariff['tariff_id'].'" '.(($tariff['tariff_id']==$ut['tariff_id'])?' selected':'').'>'.$tariff['tariff_name'].'</option>
		';
	}
	</select>-->*/
	echo'
	</td>
	<td><input type="submit" value="добавить"></td>
	</tr>
	</table>
	</form>
	Отключенные темы:<br>
	<table>';
	$res2=$db->query('SELECT order_id,order_name,order_keyword,order_start,order_end,order_last,third_sources,order_engage,ful_com,order_nastr,order_lang FROM blog_orders WHERE ut_id='.intval($ut['ut_id']).' AND user_id=0 ORDER BY order_id');
	$i=1;
	while($order = $db->fetch($res2))
	{
		if ($order['third_sources']!=0)
		{
			$mord[$order['order_id']]['start']=$order['order_start'];
			$mord[$order['order_id']]['end']=$order['order_end']==0?mktime(0,0,0,date('n'),date('j'),date('Y')):$order['order_end'];
			$mord[$order['order_id']]['tp']=$order['third_sources'];
			$mord[$order['order_id']]['kw']=$order['order_keyword'];
		}
		echo '
		<form method="post">
		<input type="hidden" name="action" value="editorder">
		<input type="hidden" name="order_id" value="'.$order['order_id'].'">
		<tr>
		<td><p style="font-size: 8px; display: inline;">#'.intval($order['order_id']).'</p><input type="text" name="order_name" value="'.htmlspecialchars($order['order_name']).'" style="width: 20ex; height: 3em; padding: 1em 0 1em 0;"></td>
		<td><textarea name="order_keyword" cols="65" rows="3">'.$order['order_keyword'].'</textarea></td>
		<td><input type="text" name="order_start" id="ordertime" value="'.date("d.m.Y",$order['order_start']).'" style="width: 12ex; height: 3em; padding: 1em 0 1em 0;"></td>
		<td><input type="text" name="order_end" class="ordertime" value="'.date("d.m.Y",$order['order_end']).'" style="width: 12ex; height: 3em; padding: 1em 0 1em 0;"></td>
		<td>
		<input type="checkbox" name="ful_com" value="1" title="Полный текст"'.((intval($order['ful_com'])>0)?' checked':'').'>
		<input type="checkbox" name="order_engage" value="1" title="Engagement"'.((intval($order['order_engage'])>0)?' checked':'').'>
		<!--<input type="checkbox" name="order_fb_rt" value="'.((intval($order['order_fb_rt'])>0)?$order['order_fb_rt']:'1').'" title="Facebook realtime"'.((intval($order['order_fb_rt'])>0)?' checked':'').'>
		<input type="checkbox" name="google_plus_last" value="'.((intval($order['google_plus_last'])>0)?$order['google_plus_last']:'1').'" title="Google+ dont work"'.((intval($order['google_plus_last'])>0)?' checked':'').'>
		<input type="checkbox" name="youtube_last" value="'.((intval($order['youtube_last'])>0)?$order['youtube_last']:'1').'" title="Youtube dont work"'.((intval($order['youtube_last'])>0)?' checked':'').'>-->
		<input type="checkbox" name="order_nastr" value="1" title="Автотональность"'.((intval($order['order_nastr'])>0)?' checked':'').'>
		</td>
		<td>
		<select name="ut_id">';
		foreach($uts as $tariff)
		{
			if (intval($tariff['tariff_id'])!=0)
			echo '	<option value="'.$tariff['tariff_id'].'" '.(($tariff['tariff_id']==$ut['tariff_id'])?' selected':'').'>'.$tariff['tariff_name'].'</option>
			';
		}
		echo'
		</select>
		</td>
		<td>
		<select name="ord_lan">
			<!--<option value="0" '.($order['order_lang']==0?'selected':'').'>Дефолтный(Русский)</option>-->
			<option value="1" '.($order['order_lang']==1?'selected':'').'>Иностранный</option>
			<option value="2" '.(($order['order_lang']==2)||($order['order_lang']==0)?'selected':'').'>Русский</option>
			<option value="4" '.($order['order_lang']==4?'selected':'').'>Азербайджанский</option>
		</select>
		<input type="submit" value="изменить">
		</form></td>
		<td>
		'.((strlen($_SESSION['fb_158565747504200_access_token'])>0)?'<a href="/project/facebook-manual.php?id='.$order['order_id'].'&token='.$_SESSION['fb_158565747504200_access_token'].'" target="_blank">FB</a>':'').'
		<form style="display: inline;" method="post" action="?user_id='.intval($_GET['user_id']).'" onsubmit="return confirm(\'Удалить тему?\')&&confirm(\'Точно удалить тему?\')&&confirm(\'Точно-точно удалить тему?\');">
		<input type="hidden" name="action" value="deleteorder">
		<input type="hidden" name="order_id" value="'.$order['order_id'].'">
		<input type="submit" value="X">
		</form></td>
		<td>
		<form method="post" style="margin-bottom: 0;" action="?user_id='.intval($_GET['user_id']).'">
		<input type="hidden" name="action" value="recoverorder">
		<input type="hidden" name="order_id" value="'.$order['order_id'].'">
		<input type="hidden" name="user_id" value="'.$usr['user_id'].'">
		<input type="submit" value="Восстановить">
		</form>
		</td>
		</tr>
		';
	}
	echo '
	</table>
	</div>';
}
echo'
	Добавить тариф пользователю:<br>
	<form method="post">
	<input type="hidden" name="action" value="addut">
	<input type="hidden" name="ut_id" value="addut">
	<input type="hidden" name="user_id" value="'.$usr['user_id'].'">
	<table>
	<tr>
	<td>
	<select name="tariff_id">';
	foreach($tariffs as $tariff)
	{
		if (intval($tariff['tariff_id'])!=0)
		echo '	<option value="'.$tariff['tariff_id'].'">'.$tariff['tariff_name'].'</option>
		';
	}
	echo'
	</select></td>
	<td><input type="submit" value="добавить"></td>
	</tr>
	</table>
	</form>
</div>
';
}
else
{
	echo '<a href="http://188.120.239.225/tools/editmsg/editmsg.php">Отредактировать отправляемые на почту сообщения</a><br>';
	echo '<a href="http://188.120.239.225/tools/editmsg/subscr_send.php">Список подписавшихся на рассылку</a>';
	echo'<div style="width: 990px; float: left; padding: 5px;">
	Добавление пользователя:
	<form method="post">
	<input type="hidden" name="action" value="adduser">
	<table>
	<tr>
	<td>E-mail*: </td><td><input type="text" name="user_email"></td>
	</tr>
	<tr>
	<td>Пароль*: </td><td><input type="text" name="user_pass"></td>
	</tr>
	<tr>
	<td>Контактное лицо*: </td><td><input type="text" name="user_name"></td>
	</tr>
	<tr>
	<td>Номер телефона*: </td><td><input type="text" name="user_contact"></td>
	</tr>
	<tr>
	<td>Название компании*: </td><td><input type="text" name="user_company"></td>
	</tr>
	<tr>
	<td>Баланс счета: </td><td><input type="text" name="user_money"></td>
	</tr>
	</table>
	<input type="submit" value="Добавить">
	</form>
	';
	
			$res=$db->query('SELECT * FROM blog_tariff');
			echo '
			
			<table>
			<tr>
			<td>Название тарифа</td><td>Описание</td><td>Цена</td><td>Ограничение</td><td>Действия</td>
			</tr>';
			$i=0;
			while ($row = $db->fetch($res)) {
				//tariff_id	tariff_name 	tariff_desc 	tariff_price 	tariff_quot
				$i++;
				echo '
				<tr>
				<form method="post">
				<input type="hidden" name="tariff_id" value="'.$row['tariff_id'].'">
				<td><input type="text" name="tariff_name" value="'.$row['tariff_name'].'"></td>
				<td><textarea name="tariff_desc">'.$row['tariff_desc'].'</textarea></td>
				<td><input type="text" name="tariff_price" value="'.$row['tariff_price'].'"></td>
				<td><input type="text" name="tariff_quot" value="'.$row['tariff_quot'].'"></td>
				<td><input type="submit" value="применить">
				</form>
				<form style="display: inline;" method="post" action="" onsubmit="return confirm(\'Удалить тариф?\');">		
				<input type="hidden" name="action" value="deletetariff">
				<input type="hidden" name="tariff_id" value="'.$row['tariff_id'].'">
				<input type="submit" value="удалить"></td>
				</form>
				</tr>';
			}
			if ($i==0) echo 'тарифы отсутствуют<br>';
			echo'
				<tr>
				<form method="post">
				<input type="hidden" name="tariff_id" value="addtariff">
				<td><input type="text" name="tariff_name" value=""></td>
				<td><textarea name="tariff_desc"></textarea></td>
				<td><input type="text" name="tariff_price" value=""></td>
				<td><input type="text" name="tariff_quot" value=""></td>
				<td><input type="submit" value="добавить"></td>
				</form>
				</tr>
			</table></div>
	';
	echo '<h1>Оплаты спецпредложений http://bit.ly/wobotpay750</h1>
	<table border="1"><tr><td><p style="font-size: 8px; display: inline;">№</p></td><td><p style="font-size: 8px; display: inline;">Время</p></td><td><p style="font-size: 8px; display: inline;">Статус</p></td><td><p style="font-size: 8px; display: inline;">Сумма</p></td></tr>';
	$qbilling=$db->query('SELECT * FROM billing WHERE user_id=0 and status>0');
	$bil_id=1;
	$status_bil[-1]='Ошибка платежа';
	$status_bil[0]='Выставлен счет';
	$status_bil[1]='Ошибка платежа';
	$status_bil[2]='Проведен';
	while ($bil=$db->fetch($qbilling))
	{
		echo '<tr><td><p style="font-size: 8px; display: inline;">905'.$bil['bill_id'].'</p></td><td><p style="font-size: 8px; display: inline;">'.date('d.m.y H:i:s',$bil['date']).'</p></td><td><p style="font-size: 8px; display: inline;">'.$status_bil[$bil['status']].'</p></td><td><p style="font-size: 8px; display: inline;">'.intval($bil['money']).'</p></td></tr>';
		$bil_id++;
	}
	echo '</table>';
}

// login information set
//function cabinet()
//{
	if (intval($_GET['user_id'])>0)
	{
		echo '<h3>Последние заходы:</h3><br>';
		echo '<div style="height: 150px; overflow: scroll;">';
		$log_i=0;
		while ($log=$db->fetch($reslog))
		{
			//echo date('d.m.y H:i:s',$log['log_time']).' '.$log['log_ip'].'<br>';
			echo ($log_i+1).'. <i>'.date('d.m.y H:i:s',$log['log_time']).'</i> <a href="http://www.maxmind.com/app/locate_demo_ip?ips='.$log['log_ip'].'" target="_blank">'.$log['log_ip'].'</a><br>';
			$log_i++;
		}
		echo '</div><br>Всего заходов: '.intval($log_i).'<br><br>';
		echo 'Реал-тайм собранные темы:<br><table border="1"><td><p style="font-size: 8px; display: inline;">ID</p></td><td><p style="font-size: 8px; display: inline;">Ключевые слова:</p></td><td><p style="font-size: 8px; display: inline;">Начало отчета</p></td><td><p style="font-size: 8px; display: inline;">Конец отчета</p></td><td><p style="font-size: 8px; display: inline;">Последнее время сбора</p></td>';
		foreach ($mord as $key => $item)
		{
			echo '<tr><td><p style="font-size: 8px; display: inline;">'.$key.'</p></td><td><p style="font-size: 8px; display: inline;">'.$item['kw'].'</p></td><td><p style="font-size: 8px; display: inline;">'.date('d.m.Y',$item['start']).'</p></td><td><p style="font-size: 8px; display: inline;">'.date('d.m.Y',$item['end']).'</p></td><td><p style="font-size: 8px; display: inline;">'.date('d.m.y H:i:s',$item['tp']).'</p></td></tr>';
		}
		echo '</table><br>';
		//print_r($mord);
		echo '<a href="http://188.120.239.225/tools/tp_info.php" target="_blank">[ Статистика реал-тайм сбора ]</a><br><br>';
		$status_bil[-1]='Ошибка платежа';
		$status_bil[0]='Выставлен счет';
		$status_bil[1]='Ошибка платежа';
		$status_bil[2]='Проведен';
		$bil_id=1;
		//echo 'SELECT * FROM billing as a LEFT JOIN blog_tariff as b ON a.tariff_id=b.tariff_id WHERE a.user_id='.$usr['user_id'];
		echo '<table border="1"><tr><td><p style="font-size: 8px; display: inline;">№</p></td><td><p style="font-size: 8px; display: inline;">Время</p></td><td><p style="font-size: 8px; display: inline;">Статус</p></td><td><p style="font-size: 8px; display: inline;">Тариф</p></td></tr>';
		$qbilling=$db->query('SELECT * FROM billing as a LEFT JOIN blog_tariff as b ON a.tariff_id=b.tariff_id WHERE a.user_id='.$usr['user_id']);
		while ($bil=$db->fetch($qbilling))
		{
			echo '<tr><td><p style="font-size: 8px; display: inline;">'.$bil_id.'</p></td><td><p style="font-size: 8px; display: inline;">'.date('d.m.y H:i:s',$bil['date']).'</p></td><td><p style="font-size: 8px; display: inline;">'.$status_bil[$bil['status']].'</p></td><td><p style="font-size: 8px; display: inline;">'.$bil['tariff_name'].'</p></td></tr>';
			$bil_id++;
		}
		echo '</table>';
	}
	echo '<h1>Последнии регистрации</h1><table border="1"><tr><td><p style="font-size: 8px; display: inline;">Статус</p></td><td><p style="font-size: 8px; display: inline;">Почта</p></td><td><p style="font-size: 8px; display: inline;">Телефон</p></td><td><p style="font-size: 8px; display: inline;">Контактное лицо</p></td><td><p style="font-size: 8px; display: inline;">Компания</p></td><td><p style="font-size: 8px; display: inline;">Дата регистрации</p></td><td><p style="font-size: 8px; display: inline;">Запрос:</p></td><td><p style="font-size: 8px; display: inline;">Промо-код</p></td><td></td><td></td></tr>';
	
	$status=array(1=>'не подтвержден',2=>'подтвержден',3=>'заблокирован');
	$res=$db->query('SELECT a.order_id,a.user_id,a.order_name,a.order_keyword,b.user_active,b.user_email,b.user_name,b.user_ctime,b.user_company,b.user_contact,b.user_promo FROM blog_orders AS a LEFT JOIN users AS b ON a.user_id = b.user_id WHERE b.user_active>0 LIMIT 10');
	while ($statuser=$db->fetch($res))
	{
		if ($statuser['order_id']>intval($stus[$statuser['user_id']]['order_id']))
		{
			$stus[$statuser['user_id']]['order_id']=$statuser['order_id'];
			$stus[$statuser['user_id']]['user_id']=$statuser['user_id'];
			$stus[$statuser['user_id']]['order_name']=$statuser['order_name'];
			$stus[$statuser['user_id']]['order_keyword']=$statuser['order_keyword'];
			$stus[$statuser['user_id']]['user_active']=$statuser['user_active'];
			$stus[$statuser['user_id']]['user_email']=$statuser['user_email'];
			$stus[$statuser['user_id']]['user_name']=$statuser['user_name'];
			$stus[$statuser['user_id']]['user_ctime']=$statuser['user_ctime'];
			$stus[$statuser['user_id']]['user_company']=$statuser['user_company'];
			$stus[$statuser['user_id']]['user_contact']=$statuser['user_contact'];
			$stus[$statuser['user_id']]['user_promo']=$statuser['user_promo'];
		}
	}
	$res=$db->query('SELECT * FROM users WHERE user_active>0 ORDER BY user_id DESC LIMIT 10');
	while ($row=$db->fetch($res))
	{
		if (!isset($stus[$row['user_id']]))
		{
			$stus[$row['user_id']]['order_id']=0;
			$stus[$row['user_id']]['user_id']=$row['user_id'];
			$stus[$row['user_id']]['user_active']=$row['user_active'];
			$stus[$row['user_id']]['user_email']=$row['user_email'];
			$stus[$row['user_id']]['user_name']=$row['user_name'];
			$stus[$row['user_id']]['user_ctime']=$row['user_ctime'];
			$stus[$row['user_id']]['user_company']=$row['user_company'];
			$stus[$row['user_id']]['user_contact']=$row['user_contact'];
			$stus[$row['user_id']]['user_promo']=$row['user_promo'];
		}
	}
	krsort($stus);
	//print_r($stus);
	foreach ($stus as $key => $item)
	{
		echo '<tr><td><p style="font-size: 8px; display: inline;">['.$status[$item['user_active']].']</p></td><td><p style="font-size: 8px; display: inline;">'.$item['user_email'].'</p></td><td><p style="font-size: 8px; display: inline;">'.$item['user_contact'].'</p></td><td><p style="font-size: 8px; display: inline;">'.$item['user_name'].'</p></td><td><p style="font-size: 8px; display: inline;">'.$item['user_company'].'</p></td><td><p style="font-size: 8px; display: inline;">'.($item['user_ctime']!=0?date('d.m.y H:i:s',$item['user_ctime']):'неизвестно').'</p></td><td><p style="font-size: 8px; display: inline;">'.(mb_strlen($item['order_keyword'],'UTF-8')>100?mb_substr($item['order_keyword'],0,100,'UTF-8'):$item['order_keyword']).'</p></td><td><p style="font-size: 8px; display: inline;">'.$item['user_promo'].'</p></td><td><p style="font-size: 8px; display: inline;"><a href="?user_id='.$item['user_id'].'">редактировать</a></p></td><td><p style="font-size: 8px; display: inline;"><a href="?delete_user_id='.$item['user_id'].'" onclick="return confirm(\'Удалить пользователя?\');">удалить</a></p></td></tr>';		
	}

	/*$res=$db->query('SELECT * FROM users WHERE user_active>0 ORDER BY user_id DESC');
	$i=0;
	while ($row = $db->fetch($res)) {
		echo '<tr><td><p style="font-size: 8px; display: inline;">['.$status[$row['user_active']].']</p></td><td><p style="font-size: 8px; display: inline;">'.$row['user_email'].'</p></td><td><p style="font-size: 8px; display: inline;">'.$row['user_contact'].'</p></td><td><p style="font-size: 8px; display: inline;">'.$row['user_name'].'</p></td><td><p style="font-size: 8px; display: inline;">'.$row['user_company'].'</p></td><td><p style="font-size: 8px; display: inline;">'.($row['user_ctime']!=0?date('d.m.y H:i:s',$row['user_ctime']):'неизвестно').'</p></td><td><p style="font-size: 8px; display: inline;"><a href="?user_id='.$row['user_id'].'">редактировать</a></p></td><td><p style="font-size: 8px; display: inline;"><a href="?delete_user_id='.$row['user_id'].'" onclick="return confirm(\'Удалить пользователя?\');">удалить</a></p></td></tr>';
	}*/	
	echo '</table>';
		$res=$db->query('SELECT * FROM users ORDER BY user_email LIMIT 10');
		$i=0;
		$fletter='';
		while ($row = $db->fetch($res)) {
			$i++;
			if (mb_strtoupper(mb_substr($row['user_email'], 0, 1, 'UTF-8'), 'UTF-8')!=$fletter) 
			{
				if ($fletter!='') echo '</div>';
				$fletter = mb_strtoupper(mb_substr($row['user_email'], 0, 1, 'UTF-8'), 'UTF-8');
				echo '<div style="float: left; width: 500px;">
				<h1 style="line-height:20%;" id="del_'.mb_strtolower($fletter,'UTF-8').'">'.$fletter.'</h1>';
			}
			echo '<p style="font-size: 8px; line-height:20%;"><b>'.$row['user_email'].'</b> ('.$row['user_contact'].') <a href="?user_id='.$row['user_id'].'">редактировать</a> <a href="?delete_user_id='.$row['user_id'].'#del_'.mb_strtolower($fletter,'UTF-8').'" onclick="return confirm(\'Удалить пользователя?\');">удалить</a></p>';
		}
		if ($i==0) echo 'пользователи отсутствуют<br>';
		echo'
		</div>
';

//<p class="menuitem"><a href="#" onclick="loaditem(\'admin\',\'#cntnt\');return false;">Обновить</a> <a href="#" onclick="loadmodal(\'adduser\');return false;">Добавить</a></p>

//<p class="menuitem"><a href="#" onclick="loaditem(\'admin\',\'#cntnt\');return false;">Обновить</a> <a href="#" onclick="loadmodal(\'addtariff\');return false;">Добавить</a></p>

echo'
</body>
</html>';
	}
//}
?>
