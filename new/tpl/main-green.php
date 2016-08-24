<?
/*
=====================================================================================================================================================

	WOBOT 2010 (с) http://www.wobot.ru
	
	MAIN TPL FILE
	Developer:	Yudin Roman
	Description:
	Managing templates for frontend gui.
	
	ОСНОВНОЙ ФАЙЛ ШАБЛОНОВ
	Разработка:	Юдин Роман
	Описание:
	Управление шаблонами, для интерфейса фронтенда.
	
=====================================================================================================================================================
*/

function start_tpl($jqready)
{
    global $html_out,$user,$config;
    $html_out.= '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
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

	<link type="text/css" href="/css/smoothness/jquery-ui-1.8.9.custom.css" rel="stylesheet" />
	<script type="text/javascript" src="/js/jquery-ui-1.8.9.custom.min.js"></script>
		<script type="text/javascript" src="/js/jquery.blockUI.js"></script>
	
	<script type="text/javascript" src="/js/jquery.pagination.js"></script>
	<script src="/js/jquery.tipsy.js" type="text/javascript"></script>
	<script src="/js/jquery.contextmenu.js" type="text/javascript"></script>
	<link rel="stylesheet" type="text/css" href="/css/jquery.contextmenu.css">

	<script type="text/javascript" src="/js/vtip.js"></script>
	<link rel="stylesheet" type="text/css" href="/css/vtip.css" />

	<script type="text/javascript" src="/js/highcharts.js"></script>
	<script type="text/javascript" src="/js/modules/exporting.js"></script>
	
    <script src="http://api-maps.yandex.ru/1.1/index.xml?key=ADjV0ksBAAAABHe4bwIA8vuRJCf7A41Xy9v4UgUuum1sgcUAAAAAAAAAAACtrrQoE2PffTogOjoQGnVP5QQ6dQ=="
	type="text/javascript"></script>

	    <script type="text/javascript">
	     $(document).ready(function(){
			'.$jqready.'
	     });

	    </script>

	</head>
	<body>
	<!-- DIALOGS -->

	<!-- EODIALOGS -->
	<center>
	<table width="1000" height="100%" align="center" cellpadding="0" cellspacing="0" border="0">
	<tr>
	    <td class="topbar">
	        <a id="logolnk" href="/new/"><img src="/img/wobot-logo.png" alt="WOBOT" title="WOBOT" class="wobot_logo" border="0"></a>
	    </td>

	    <td class="menubar">
		<div class="menublock">
	        <p class="itembar"><a href="/new/user/setup" class="menutopevent">Настройки</a></p>
'.($user['user_priv']&4?'        <p class="itembar"><a href="/new/admin" class="menutopevent">Администрирование</a></p>':'').'
	        <p class="itembar"><span class="rl">'.$user['user_email'].'</span> <a href="/new/logout" class="sl">выход</a></p>
		</div>
	    </td>
	</tr>
	<tr>
	    <td class="bottombar" colspan="2">
	    <table width="100%" height="100%" align="center" cellpadding="0" cellspacing="0" border="0">
	    <tr>
		<td style="vertical-align: top;">
		<div><div id="cntnt" class="cntnt">

';
    //menu();
}

function stop_tpl()
{
        global $html_out;
        global $ajax_out;
        $html_out.= '
			    </div></div>
			    </td>
			    </tr>
			    </table>
			</td>
			</tr>
			        <tr>
			        <td colspan="2" class="bottomcnt" align="center">
			<span class="bottomitem"><a href="#">Справка</a></span>
			<span class="bottomitem"><a href="mailto:avd@wobot.co?subject=Техподдержка личного кабинета">Заявка в службу техподдержки</a></span>

			<span class="bottomitem" style="font-size: 16px/18px;"><span class="rln">+7 (916) 833 0574</span></span>
			        </td>   
			        </tr>
			</table>
			</center>
			</body>
			</html>
';
    echo $html_out;
}

?>
