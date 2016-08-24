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




echo '
	<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
	<html>
	<head>
	<title>WOBOT &copy; Research - Личный кабинет (&beta;-version)</title>
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
	<script type="text/javascript" src="/js/jquery.blockUI.js"></script>
	<link type="text/css" href="/css/jquery-ui.css" rel="Stylesheet" />

	<script type="text/javascript" src="/js/jquery-ui.js"></script>
	<script type="text/javascript" src="/js/jquery.pagination.js"></script>
	<script src="/js/jquery.tipsy.js" type="text/javascript"></script>



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


	$(\'#payclick\').tipsy({fallback: "<font style=\'color: #aaa\'>онлайн оплата недоступна</font>", html: true });

	     $("#loading").hide("fast");


	     });

	    </script>

	</head>
	<body>
';



$db = new database();
$db->connect();

auth();
if (!$loged) die();

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

// login information set
//function cabinet()
//{
	global $db, $config, $user;
	if ($user['user_priv']&4)
	{
		$res=$db->query('SELECT * FROM users');
		echo 'Список пользователей:<br>';
		$i=0;
		while ($row = $db->fetch($res)) {
			$i++;
			echo $row['user_email'].' ('.$row['user_contact'].') <a href="#" onclick="loadmodal(\'adduser?user_id='.$row['user_id'].'\');return false;">редактировать</a> <a href="#" onclick="loadmodal(\'addusertariff?user_id='.$row['user_id'].'\');return false;">тарифы</a> <a href="#" onclick="loadmodal(\'addservice?user_id='.$row['user_id'].'\');return false;">услуги</a><br>';
		}
		if ($i==0) echo 'пользователи отсутствуют<br>';
		echo'
<p class="menuitem"><a href="#" onclick="loaditem(\'admin\',\'#cntnt\');return false;">Обновить</a> <a href="#" onclick="loadmodal(\'adduser\');return false;">Добавить</a></p>
';

		$res=$db->query('SELECT * FROM blog_tariff');
		echo 'Список тарифов:<br>';
		$i=0;
		while ($row = $db->fetch($res)) {
			$i++;
			echo $row['tariff_name'].' <a href="#" onclick="loadmodal(\'addtariff?tariff_id='.$row['tariff_id'].'\');return false;">редактировать</a><br>';
		}
		if ($i==0) echo 'тарифы отсутствуют<br>';
		echo'
<p class="menuitem"><a href="#" onclick="loaditem(\'admin\',\'#cntnt\');return false;">Обновить</a> <a href="#" onclick="loadmodal(\'addtariff\');return false;">Добавить</a></p>
';

echo '
</body>
</html>';
	}
//}
?>
