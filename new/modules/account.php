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
//echo "123";
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
	<body>';

	echo '<h1>Кабинеты</h1><table border="1"><tr><td><p style="font-size: 8px; display: inline;">Почта</p></td><td><p style="font-size: 8px; display: inline;">Телефон</p></td><td><p style="font-size: 8px; display: inline;">Контактное лицо</p></td><td><p style="font-size: 8px; display: inline;">Компания</p></td><td><p style="font-size: 8px; display: inline;">Тариф</p></td><td><p style="font-size: 8px; display: inline;">Дата регистрации</p></td><td><p style="font-size: 8px; display: inline;">Дата завершения</p></td><td><p style="font-size: 8px; display: inline;">Запрос:</p></td><td><p style="font-size: 8px; display: inline;">Промо-код</p></td></tr>';
	
	$status=array(1=>'не подтвержден',2=>'подтвержден',3=>'заблокирован');
	//echo 'SELECT a.order_id,a.user_id,a.order_name,a.order_keyword,b.user_active,b.user_email,b.user_name,b.user_ctime,b.user_company,b.user_contact,b.user_promo FROM blog_orders AS a LEFT JOIN users AS b ON a.user_id = b.user_id WHERE b.user_active>0';
	$res=$db->query('SELECT DISTINCT a.order_id,a.user_id,a.order_name,a.order_keyword,b.user_active,b.user_email,b.user_name,b.user_ctime,b.user_company,b.user_contact,b.user_promo, c.ut_date, d.tariff_name, d.tariff_price FROM blog_orders AS a, users AS b, user_tariff AS c, blog_tariff AS d where a.user_id = b.user_id and a.user_id = c.user_id and c.tariff_id=d.tariff_id and c.ut_date>'.time().' and d.tariff_id!=3 and b.user_company!="wobotTEST" GROUP BY a.user_id');
	//$res=$db->query('SELECT a.order_id,a.user_id,a.order_name,a.order_keyword,b.user_active,b.user_email,b.user_name,b.user_ctime,b.user_company,b.user_contact,b.user_promo FROM blog_orders AS a LEFT JOIN users AS b ON a.user_id = b.user_id LEFT JOIN user_tariff WHERE b.user_active>0');
	while ($statuser=$db->fetch($res))
	{
		echo '<tr><td><p style="font-size: 8px; display: inline;">'.$statuser['user_email'].'</p></td><td><p style="font-size: 8px; display: inline;">'.$statuser['user_contact'].'</p></td><td><p style="font-size: 8px; display: inline;">'.$statuser['user_name'].'</p></td><td><p style="font-size: 8px; display: inline;">'.$statuser['user_company'].'</p></td><td><p style="font-size: 8px; display: inline;">'.$statuser['tariff_name'].'<br>'.intval($statuser['tariff_price']).' руб.</p></td><td><p style="font-size: 8px; display: inline;">'.($statuser['user_ctime']!=0?date('d.m.y H:i:s',$statuser['user_ctime']):'неизвестно').'</p></td><td style="font-size: 8px; display: inline;">'.date('d.m.y',$statuser['ut_date']).'</td><td><p style="font-size: 8px; display: inline;">'.(mb_strlen($statuser['order_keyword'],'UTF-8')>100?mb_substr($statuser['order_keyword'],0,100,'UTF-8'):$statuser['order_keyword']).'</p></td><td><p style="font-size: 8px; display: inline;">'.$statuser['user_promo'].'</p></td></tr>';		
	}


//<p class="menuitem"><a href="#" onclick="loaditem(\'admin\',\'#cntnt\');return false;">Обновить</a> <a href="#" onclick="loadmodal(\'adduser\');return false;">Добавить</a></p>

//<p class="menuitem"><a href="#" onclick="loaditem(\'admin\',\'#cntnt\');return false;">Обновить</a> <a href="#" onclick="loadmodal(\'addtariff\');return false;">Добавить</a></p>

echo'
</body>
</html>';
	
//}
?>
