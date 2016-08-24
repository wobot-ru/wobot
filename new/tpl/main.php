<?

require_once('/var/www/new/tpl/translate.php');
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
$_lang='ru';
function start_tpl($jqready,$css,$params)
{
	//print_r($_GET);
    global $html_out,$user,$config,$db, $_word,$_lang, $order;
	if ($order['order_name']!='')
	{
		$text_theme=$order['order_name'];
	}
	else
	{
		$text_theme=$order['order_keyword'];
	}
	if (mb_strlen($text_theme,'UTF-8')>17)
	{
		$tm=$text_theme;
		$text_theme=mb_substr($text_theme,0,17,'UTF-8').'...';
	}
	$llen=14;
	$restariff=$db->query("SELECT * from user_tariff as UT LEFT JOIN blog_tariff as BT ON UT.tariff_id=BT.tariff_id WHERE UT.user_id=".intval($user['user_id'])." LIMIT 1");
	$tariff = $db->fetch($restariff);
    $html_out.= '
		<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
	<html>
	    <head>
	        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	        <title></title>
	        <!-- Framework CSS -->
	        <link rel="stylesheet" href="/new_css/css/blueprint/screen.css" type="text/css" media="screen, projection">
	        <link rel="stylesheet" href="/new_css/css/blueprint/print.css" type="text/css" media="print">
	<!--        <link rel="stylesheet" type="text/css" href="/new_css/files/example.css"></link>-->

	          <!--[if lt IE 8]>
	            <link rel="stylesheet" href="/new_css/css/blueprint/ie.css" type="text/css" media="screen, projection">
	          <![endif]-->

	        <link rel="stylesheet" href="/new_css/css/style.css" type="text/css">
	        <link rel="stylesheet" href="/new_css/css/form.css" type="text/css">

	        <script type="text/javascript" src="/new_js/js/jquery.js"></script>
			<script type="text/javascript" src="/new_js/js/ZeroClipboard.js"></script>
			<script type="text/javascript" src="/new_js/js/jquery.zclip.js"></script>
	        <script type="text/javascript" src="/new_js/js/jquery.corner.js"></script>
	        '.(($_GET['s']!='comment')?'<script type="text/javascript" src="/new_js/js/jimpl_cloud.js"></script>':'<script type="text/javascript" src="/new_js/library/jquery-ui-1.8.12.custom/js/jquery-ui-1.8.12.custom.min.js"></script>
	        <link rel="stylesheet" type="text/css"
	              href="/new_js/library/jquery-ui-1.8.12.custom/css/smoothness/jquery-ui-1.8.12.custom.css"/>

	        <script type="text/javascript" src="/new_js/js/jquery.checkboxtree.js"></script>
	        <link rel="stylesheet" type="text/css" href="/new_css/css/jquery.checkboxtree.css"/>
	        <!-- end checkboxTree configuration -->').'
			<script type="text/javascript">
			'.$params.'
			</script>

	        <!-- start fancybox configuration -->
	        <script type="text/javascript" src="/new_css/fancybox/jquery.fancybox-1.3.4.pack.js"></script> 
		<script type="text/javascript" src="/new_css/fancybox/jquery.easing-1.3.pack.js"></script> 
		<script type="text/javascript" src="/new_css/fancybox/jquery.mousewheel-3.0.4.pack.js"></script> 
		<link rel="stylesheet" href="/new_css/fancybox/jquery.fancybox-1.3.4.css" type="text/css" media="screen" /> 
	        <!-- end fancybox configuration -->

	        
			'.(($_GET['s']!='comment')?'<script type="text/javascript" src="/new_js/js/highcharts/highcharts.js"></script>

			<!-- 1a) Optional: add a theme file -->
			<!--
				<script type="text/javascript" src="../js/themes/gray.js"></script>
			-->
			<!-- 1b) Optional: the exporting module -->
			<script type="text/javascript" src="/new_js/js/highcharts/exporting.js"></script>
			<!-- 2. Add the JavaScript to initialize the chart on document ready -->':'').'


	        <script type="text/javascript" src="/new_js/js/begscript.js"></script>
	        <script type="text/javascript" src="/new_js/js/for_all.js"></script>
	        <script type="text/javascript" src="/new_js/js/datepicker.js"></script>
	        <script type="text/javascript" src="/new_js/js/date.js"></script>
	        <script type="text/javascript" src="/new_js/js/custom-form-elements.js"></script>
	        <script type="text/javascript" src="/new_js/js/MyFunc.js"></script>

    		'.(($_GET['s']!='comment')?'<script type="text/javascript" src="/new_js/js/graph.js"></script>
	        <script type="text/javascript" src="/new_js/js/mainpagescript.js"></script>':'<script type="text/javascript" src="/new_js/js/mainscript.js"></script>').'

	        <link href="/new_css/css/demo.css"       rel="stylesheet" type="text/css" />
	        <link href="/new_css/css/datepicker.css" rel="stylesheet" type="text/css" />
			<script type="text/javascript" src="/new_js/js/vtip.js"></script>
			<link rel="stylesheet" type="text/css" href="/new_css/css/vtip.css" />
			<script type="text/javascript">'.$css.'</script>
	    </head>
	    <body>


	        <div class="header-size" id="headertop"></div>
	        <div class="header-size" id="headerbottom"></div>
	        <div class="center" id="headercenter"></div>

	        <div class="container"> 
	            <div id="header">
	          <div class="span-24 header-size">  
	              <div class="text-white span-9 text-18">
	                  <div class="row clear"></div>
	                  <p class="top bottom">
	                      '.$_word[$_lang]['tariff'].': <a href=\'#\' class="vtip" title="'.nl2br(addslashes($tariff['tariff_desc'])).'">'.$tariff['tariff_name'].'</a>
	                  </p>
	                  <div class="row clear"></div>
	                  <p class="top bottom">
	                      Пользователь: '.(strlen($user['user_email'])>$llen?'<a href="#" class="vtip" title="'.$user['user_email'].'" onclick="return false;">'.substr($user['user_email'],0,$llen).'...</a>':$user['user_email']).' ('.(($user['user_priv']&4)?'<a href="/new/admin">A</a>|':'').'<a href=\'/new/logout\'>'.$_word[$_lang]['logout'].'</a>)
	                  </p>
	              </div>
	              <div class="span-6">
	                  <div class="row clear"></div>
	                  <img id="logo" class="center" src="/img/logo.jpg"/>
	              </div>
	               <div class="text-white span-9 last text-right text-18">
	                  <div class="row clear"></div>
	                  <p class="top bottom right">
	                      '.(($user['user_priv']&4)?'<a class="span-3 last" href="'.$config['html_root'].'admin">в админку</a>':'').'<a class="span-3 last" onclick="loadmodal(\'/new/setup\',390,230);">настройки</a>
	                  </p>
	                  <div class="row clear"></div>
	                      <a id="need_help" class="dottedwhite bold">Нужна помощь?</a>
	              </div>
	              </div>
	

	                <div id="help" class="span-5 last text-white hide">
	                    <div class="row span-4"></div>
	                    <a id="hide_help" class="span-1 last text-right"><img src="/img/images/help/up.png" /></a>
	                    <div class="span-1 last">
	                       <img src="/img/images/help/faq.png"/>
	                       <img src="/img/images/help/phone.png"/>
	                       <img src="/img/images/help/mail.png"/>
	                    </div>
	                    <div class="span-4">
	                        <a class="bold text-18">FAQ</a>
	                        <p>+7 (916) 833 0574</p>
	                        <a>help@wobot.ru</a>
	                    </div>

	<!--                    <a class="dottedwhite" id="hide_help">свернуть</a>-->
	                </div>

	              <div class="span-24 header-size">  
	              <div class="span-9 text-18">
	                  <div class="row clear"></div>
	                  <div class="row clear"></div>
	              </div>
	              <div class="span-6 text-18">
	                  <p class="top bottom text-center">
	                      <a href="'.$config['html_root'].'" class="text-grey">к списку тем</a>
	                  </p>
	              </div>
	               <div class="span-9 last">
	              </div>

	              <div class="span-16">  
	                  <h1 class="top bottom">
	                      '.$text_theme.'
	                  </h1>
	              </div>

	              '.(($_GET['s']=='')?'':'<div class="span-3 '.((substr($_GET['s'],0,5)=='order')?'thispage':'').' text-center">
	                  <div class="row clear"></div>
	                  <a href="'.$config['html_root'].'order/'.$order['order_id'].'" class="top bottom text-18 '.((substr($_GET['s'],0,5)=='order')?'text-white':'text-black').'">
	                      Главная
	                  </a>
	              </div>
	              <div class="span-5 last '.(($_GET['s']=='comment')?'thispage':'').' text-center">
	                  <div class="row clear"></div>
	                  <a class="top bottom text-18 '.(($_GET['s']=='comment')?'text-white':'text-black').'" href="#" onclick="'.(($_GET['s']=='comment')?'return false;':'document.getElementById(\'submform\').submit();').'">
	                      Просмотр упоминаний
	                  </a>
	              </div>').'
	          </div>
	           </div>
';
    //menu();
}

function stop_tpl()
{
        global $html_out, $user, $_word,$_lang;
        global $ajax_out;
        $html_out.= '
			          <div id="footer" class="span-24 text-lightgrey text-center">
			              <div class="rows-2 clear"></div>
			              <div class="rows-2 clear"></div>
			              <div class=\'hide\'>
			                  <div id="footer1" class="span-16">
			                      <h3>Политика конфиденциальности</h3>
			                      <div class="row clear"></div>
			                      <p>Ваши персональные данные позволяют ООО "Вобот" выполнять ваши заказы и, по вашему желанию, информировать вас о выпуске новых обновлений, специальных предложениях и особых событиях. Мы не передаём ваши данные третьим компаниям, административным и государственным органам.
			ООО "Вобот" осуществляет политику защиты ваших персональных данных и их хранения в полученном, неизменном виде. В связи с этим доступ к базе персональных данных пользователей находится под постоянным наблюдением и контролем. Мы постараемся предпринять все возможные меры с целью сохранения безопасности ваших персональных данных третьими лицами, которым могут быть переданы ваши персональные данные. В свою очередь, мы призываем вас к тому, чтобы полученная он нас информация была ограничена рамками соглашения, полученных нашей и вашей сторонами.
			 Вопросы, жалобы и предложения направляйте нам по электронной почте на адрес: mail@wobot.ru</p>
			                </div></div>
			              <p class="top bottom text-18 fancypopup"><a>Условия соглашения</a> | <a href="#footer1">Политика конфиденциальности</a> | 
			                  <a>Добавить ресурс в каталог</a> | <a>Продлить со скидкой</a></p>
			              <div class="row clear"></div>
			              <p class="top bottom">ООО &#171;Вобот&#187; 2010-2011</p>
			              <div class="row clear"></div>
			          </div>
			      </div>
			    </body>
			</html>
';
    echo $html_out;
}

?>
