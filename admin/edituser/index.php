<?

require_once('../com/config.php');
require_once('../com/db.php');
require_once('../com/auth.php');

$db = new database();
$db->connect();

function datesort($a, $b)
{
    if ($a['user_ctime'] == $b['user_ctime']) {
        return 0;
    }
    return ($a['user_ctime'] < $b['user_ctime']) ? 1 : -1;
}

auth();
if (!$loged) 
{
    header('Location: /admin/index_dev.php');
    die();
}


function parseUrlpost($url,$params)
{
    $postvars=http_build_query($params);
    $ch = curl_init( $url );
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);   // возвращает веб-страницу
    curl_setopt($ch, CURLOPT_HEADER, 0);           // не возвращает заголовки
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);   // переходит по редиректам
    curl_setopt($ch, CURLOPT_POSTFIELDS    ,$postvars);
    curl_setopt($ch, CURLOPT_ENCODING, "");        // обрабатывает все кодировки
    curl_setopt($ch, CURLOPT_USERAGENT, $uagent);  // useragent
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 120); // таймаут соединения
    curl_setopt($ch, CURLOPT_TIMEOUT, 120);        // таймаут ответа
    curl_setopt($ch, CURLOPT_MAXREDIRS, 10);       // останавливаться после 10-ого редиректа
    $content = curl_exec( $ch );
    $err     = curl_errno( $ch );
    $errmsg  = curl_error( $ch );
    $header  = curl_getinfo( $ch );
    // echo $content;
    curl_close( $ch );
    return $content;
}

// print_r($_POST);
// if (($_GET['topipe']==1) && ($_POST['action']=='edituser')) echo parseUrlpost('http://'.$_GET['server'].'/api/admin/topipe',$_POST);
if ($_POST['action']=='edituser') parseUrlpost('http://'.$_GET['server'].'/api/admin/saveuser',$_POST);
elseif ($_POST['action']=='editorder') parseUrlpost('http://'.$_GET['server'].'/api/admin/saveorder',$_POST);
elseif ($_POST['action']=='addorder') 
{
    // echo parseUrlpost('http://'.$_GET['server'].'/api/admin/addorder',$_POST);
    $mcont=json_decode(parseUrlpost('http://'.$_GET['server'].'/api/admin/addorder',$_POST),true);
    // print_r($mcont);
    if ($mcont['order_id']=='') $fail_add=1;
    // echo $fail_add;
}
elseif ($_POST['action']=='blockorder') parseUrlpost('http://'.$_GET['server'].'/api/admin/blockorder',$_POST);
elseif ($_POST['action']=='unblockorder') parseUrlpost('http://'.$_GET['server'].'/api/admin/unblockorder',$_POST);
elseif ($_POST['action']=='selection') parseUrlpost('http://'.$_GET['server'].'/api/admin/randomorder',$_POST);
elseif ($_POST['action']=='deleteorder') parseUrlpost('http://'.$_GET['server'].'/api/admin/delorder',$_POST);
elseif ($_POST['action']=='refreshorder') parseUrlpost('http://'.$_GET['server'].'/api/admin/refreshorder',$_POST);
elseif ($_POST['action']=='retransf') echo parseUrlpost('http://'.$_POST['new_server'].'/api/admin/drover',$_POST);
elseif ($_POST['action']=='topd') parseUrlpost('http://'.$_POST['server'].'/api/admin/topd?toPD='.$_GET['user_id'],$_POST);
elseif ($_POST['action']=='topd2') parseUrlpost('http://'.$_POST['server'].'/api/admin/topd2?toPD='.$_GET['user_id'],$_POST);


echo '<!DOCTYPE html>
<html lang="en">
  <head>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
    <title>WOBOT &copy; Панель администратора (&beta;-version)</title>
    <meta name="description" content="" />
    <meta name="keywords" content="Wobot реклама анализ раскрутка баннер" />
    <meta name="author" content="Wobot media" />
    <meta name="robots" content="all" />

    <link type="text/css" href="../css/bootstrap.min.css" rel="stylesheet" />
    <style>
      body {
        padding-top: 85px; /* 60px to make the container go all the way to the bottom of the topbar */
      }
    </style>
    <link type="text/css" href="../css/bootstrap-responsive.css" rel="stylesheet" />
    <link type="text/css" href="../css/additional_styles.css?1" rel="stylesheet" />
    <link type="text/css" href="../css/jquery-ui-css110.css" rel="stylesheet" />

    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
    <script src="//code.jquery.com/ui/1.10.4/jquery-ui.js"></script>
    <script type="text/javascript" src="../js/jquery.ui.datepicker-ru.js"></script>
    

    <script src="../js/bootstrap.min.js"></script>
        <script type="text/javascript">
            $(document).ready(function(){ 
                $("input[name^=\'order_start\']").datepicker();
                $("input[name^=\'order_end\']").datepicker();
                $("input[name^=\'ut_date\']").datepicker();
                $(".rech").datepicker();
                ';
                if ($fail_add==1) echo 'alert("Ошибка запроса: Длина, Скобки или Пустые операторы!");';
                echo '
            });
        </script>
      </head>
';
// print_r($_POST);
$user_info=json_decode(file_get_contents('http://'.$_GET['server'].'/api/admin/getuser?user_id='.$_GET['user_id']),true);
$user_info_settings=json_decode($user_info['user']['user_settings'],true);

echo '
  <body>
    ';

    echo '
    <div class="navbar navbar-inverse navbar-fixed-top"> 
      <div class="navbar-inner">
        <div class="container">
          <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </a>
          <div class="nav-collapse collapse">
            <ul class="nav">
              <li><a target="_blank" href="/new/adminfaq" target="_blank">FAQ</a></li>
              <li><a target="_blank" href="http://wobot.ru/awstats/" target="_blank">Статистика wobot.ru</a></li>
              <li><a target="_blank" href="/tools/regi.php?hero=1" target="_blank">Статистика по демо кабинетам</a></li>
              <li><a target="_blank" href="http://bmstu.wobot.ru/social/export/" target="_blank">Выгрузка из групп</a></li>
              <!--<li><a href="https://www.facebook.com/dialog/oauth?client_id=158565747504200&redirect_uri=http%3A%2F%2F188.120.239.225%2Fnew%2F&state=e544c75112dc0e5eacd455208bf4a6e6">Зайти Facebook</a></li>-->
              <!--<li><a href="/tools/editmsg/editmsg.php">Отредактировать отправляемые на почту сообщения</a></li>-->
              <li><a target="_blank" href="/tools/editmsg/subscr_send.php">Рассылка</a></li>
              <li><a target="_blank" href="/tools/promo.php">PROMO</a></li>
              <li><a id="debug">Отладка</a></li>
              <li><a target="_blank" href="http://ec2-54-247-33-208.eu-west-1.compute.amazonaws.com/gpview/" target="_blank">GRAPH</a></li>
              <li><a target="_blank" href="http://146.185.183.12/tpf/test2.php?check=1" target="_blank">TPF</a></li>
              <li><a target="_blank" href="/tools/export_src.php" target="_blank">Источники</a></li>
            </ul>
          </div>
        </div>
      </div>
    </div> <!-- Верхняя панель -->
    <div class="container main_container">
      <div class="row tab_row">
        <span class="span12">
            <ul class="nav nav-tabs main_tabs" id="myTab"> <!--вкладки-->
                <li><a href="/admin/last/?user_id='.$_GET['user_id'].'&server='.$_GET['server'].'">Последние кабинеты <span id="num_inactive"></span></a></li>
                <li><a href="/admin/adduser/?user_id='.$_GET['user_id'].'&server='.$_GET['server'].'">Добавление пользователей</a></li>
                <li class="active"><a href="/admin/edituser/?user_id='.$_GET['user_id'].'&server='.$_GET['server'].'">Редактир. пользователей</a></li>
                <!--<li><a href="#last_reg">Демо-кабинеты</a></li>-->
                <li><a href="/admin/clients/?user_id='.$_GET['user_id'].'&server='.$_GET['server'].'">Список клиентов</a></li>
                <!--<li><a href="#new_res">Добавление ресурсов</a></li>
                <li><a href="#special_ord">Специальные предложения</a></li>
                <li><a href="#tariffs">Тарифы</a></li> -->
                <li><a href="/admin/bills/?user_id='.$_GET['user_id'].'&server='.$_GET['server'].'">Последние платежи</a></li>
                <!--<li><a href="#notaprooved">Не подтверж.</a></li>--><!--вкладки-->
            </ul>
            <div class="row tab-pane active" id="demo"> <!--Демо кабинеты -->
            <div class="inner-tab-container">
<div class="inner-tab-container"> <!--mod_user tab container-->
                                    <div style="margin-bottom: 10px;"><b>Редактирование пользователя: </b><!--[ <a href="?user_id=0">перейти к добавлению пользователя и созданию тарифов</a> ]--></div>
                                     
                                    <div style="height: 15px;"></div>
                                    ';
                                    if($user_info['user']['tariff_id']==16){
                                        echo '<a style="margin-top:-9px;" class="btn" href="/admin/mailing.php?user_id='.$_GET['user_id'].'&firsttheme=1" target="_blank">Отправить письмо </a>';
                                    }

                                    echo '
                                    <form style="display: inline;" id="main_formm" method="post" action="/admin/edituser/?user_id='.$_GET['user_id'].'&server='.$_GET['server'].'">

                                    <input type="hidden" name="action" value="edituser">
                                    <input type="hidden" name="server" value="'.$_GET['server'].'">
                                    <input type="hidden" name="user_id" value="'.$_GET['user_id'].'">
                                        <table>
                                        <tbody><tr>
                                        <td width="180px">E-mail <span style="color: red;">*</span> </td><td><input type="text" name="user_email" value="'.$user_info['user']['user_email'].'"></td>
                                        </tr>
                                        <tr>
                                        <td>Пароль: </td><td><input type="text" name="user_pass" value=""></td>
                                        </tr>
                                        <tr>
                                        <td>Контактное лицо <span style="color: red;">*</span> </td><td><input type="text" name="user_name" value="'.$user_info['user']['user_name'].'"></td>
                                        </tr>
                                        <tr>
                                        <td>Номер телефона <span style="color: red;">*</span> </td><td><input type="text" name="user_contact" value="'.$user_info['user']['user_contact'].'"></td>
                                        </tr>
                                        <tr>
                                        <td>Название компании <span style="color: red;">*</span> </td><td><input type="text" name="user_company" value="'.$user_info['user']['user_company'].'"></td>
                                        </tr>
                                        <tr>
                                        <td>Комментарии </td><td><input type="text" name="user_comment" value="'.$user_info_settings['comment'].'"></td>
                                        </tr>
                                        <tr>
                                        <td>Баланс счета </td><td><input type="text" name="user_money" value="'.$user_info['user']['user_money'].'"></td>
                                        </tr>
                                        <tr>
                                        <td>Дата регистрации:</td> <td>'.date('d.m.y H:i:s',$user_info['user']['user_ctime']).'</td>
                                        </tr>
                                        <tr>
                                        <td>Промо-код:</td> <td>'.$user_info['user']['user_promo'].'</td>
                                        </tr>
                                        <tr>
                                        <td>Реагирование: <input '.($user_info_settings['user_reaction']==1?'checked':'').' type="checkbox" name="user_reaction" value="1"></td>
                                        </tr>
                                        </tbody></table><br>
                                        <input type="hidden" name="ut_id" value="'.$user_info['user']['ut_id'].'">
                                        <input type="hidden" name="user_id" value="'.$user_info['user']['user_id'].'">
                                        <input type="hidden" name="user_active" value="'.$user_info['user']['user_active'].'">
                                        Тариф: <select name="tariff_id">
                                        ';
                                        // print_r($user_info);
                                        $mtariffs=json_decode(file_get_contents('http://'.$_GET['server'].'/api/admin/gettariffs'),true);
                                        // print_r($mtariffs);
                                        foreach ($mtariffs['tariff'] as $tariff)
                                        {
                                            echo '<option '.($tariff['tariff_id']==$user_info['user']['tariff_id']?'selected':'').' value="'.$tariff['tariff_id'].'">'.$tariff['tariff_name'].'</option>';
                                        }
                                        echo '<option value=""></option>
                                        </select>
                                        Действует до: 
                                        <input type="text" name="ut_date" id="to_date" value="'.date('d.m.Y',$user_info['user']['ut_date']).'">
                                        Активность:
                                        <select name="us_active">
                                        <option '.($user_info['user']['user_active']==0?'selected':'').' value="0">Внутренний</option>
                                        <option '.($user_info['user']['user_active']==1?'selected':'').' value="1">Не активированный</option>
                                        <option '.($user_info['user']['user_active']==2?'selected':'').' value="2">Рабочий</option>
                                        <option '.($user_info['user']['user_active']==3?'selected':'').' value="3">Просроченный</option>
                                        </select>

                                    <div style="height: 15px;"></div>   
                                    <input class="btn" type="submit" value="Применить">
                                    </form>
                                    <!--<input class="btn" type="button" value="==&gt;AmoCRM" onclick="document.getElementById(\'main_formm\').action=\'?user_id='.$user_info['user']['user_id'].'&server='.$_GET['server'].'&topipe=1#mod_user\'; document.getElementById(\'main_formm\').submit();">-->
                                    <form style="display: inline;" method="post" action="http://'.($config['host_servers'][array_search($_GET['server'],$config['servers'])]).'/" target="_blank">
                                    <input type="hidden" name="token" value="'.(md5(mb_strtolower($user_info['user']['user_email'],'UTF-8').':'.$user_info['user']['user_pass'])).'">
                                    <input type="hidden" name="user_id" value="'.$user_info['user']['user_id'].'">
                                    <input class="btn" type="submit" value="Войти в production">
                                    </form>
                                    <form style="display: inline;" method="post" action="/admin/edituser/?user_id='.$_GET['user_id'].'&server='.$_GET['server'].'">
                                    <input type="hidden" name="action" value="topd">
                                    <input type="hidden" name="server" value="'.$_GET['server'].'">
                                    <input type="hidden" name="user_id" value="'.$user_info['user']['user_id'].'">
                                    <input class="btn" type="submit" value="Импорт в CRM account-one">
                                    </form>
                                    <form style="display: inline;" method="post" action="/admin/edituser/?user_id='.$_GET['user_id'].'&server='.$_GET['server'].'">
                                    <input type="hidden" name="action" value="topd2">
                                    <input type="hidden" name="server" value="'.$_GET['server'].'">
                                    <input type="hidden" name="user_id" value="'.$user_info['user']['user_id'].'">
                                    <input class="btn" type="submit" value="Импорт в CRM account-two">
                                    </form>
                                    <br><br>
                                    <form method="POST" action="/admin/edituser/?user_id='.$_GET['user_id'].'&server='.$_GET['server'].'">
                                    <input type="hidden" name="action" value="retransf">
                                    <input type="hidden" name="user_id" value="'.$_GET['user_id'].'">
                                    <input type="hidden" name="server" value="'.$_GET['server'].'">
                                    Сервер: <select name="new_server">
                                    ';
                                    foreach ($config['servers'] as $item_server)
                                    {
                                        echo '<option '.($_GET['server']==$item_server?'selected':'').' value="'.$item_server.'">'.$item_server.'</option>';
                                    }
                                    echo '
                                    </select>
                                    <br><input class="btn" type="submit" value="Перебросить">
                                    </form>
                                    <!--<form style="display: inline;" method="post" action="/admin/edituser/?user_id='.$_GET['user_id'].'&server='.$_GET['server'].'" onsubmit="return confirm(\'Удалить кабинет?\')&amp;&amp;confirm(\'Точно удалить кабинет?\')&amp;&amp;confirm(\'Ну смари я тебя предупреждал!\')">
                                    <input type="hidden" name="action" value="deleteuser">
                                    <input type="hidden" name="server" value="'.$_GET['server'].'">
                                    <input type="hidden" name="ut_id" value="'.$user_info['user']['ut_id'].'">
                                    <input type="hidden" name="user_id" value="'.$user_info['user']['user_id'].'">
                                    <input class="btn" type="submit" value="Удалить кабинет">
                                    </form>
                                    <br><br>-->
                                    <!--<form style="display: inline;" method="post" action="?user_id=4217#mod_user" onsubmit="return confirm(\'Добавить тему?\');">
                                    <input type="hidden" name="action" value="add_demo_theme">
                                    <input type="hidden" name="user_id" value="'.$user_info['user']['user_id'].'">
                                    <input class="btn" type="submit" value="Скопировать демо тему">
                                    </form>-->
                                 <div style="height: 15px;"></div>
                                    <a target="_blank" style="margin-top: 10px;" href="http://'.$_GET['server'].'/tools/groups_tp.php?user_id='.$_GET['user_id'].'">[ Добавить группы из социальных сетей пользователю ]</a><br>
                                    <a target="_blank" style="margin-top: 10px;" href="http://'.$_GET['server'].'/tools/addnews.php?user_id='.$_GET['user_id'].'">[ Добавить выдачу с news.yandex.ru пользователю ]</a><br>
                                    <!--<a target="_blank" style="margin-top: 10px;" href="http://'.$_GET['server'].'/tools/add_post.php?uid='.$_GET['user_id'].'">[ Добавить упоминания с фильтрацией по запросу ]</a><br>-->
                                    <a target="_blank" style="margin-top: 10px;" href="http://'.$_GET['server'].'/tools/add_post_wv.php?uid='.$_GET['user_id'].'">[ Добавить упоминания без фильтрации по запросу ]</a><br>
                                    <div style="height: 15px;"></div>

                                             <div style="height: 15px;"></div>
                                                <b>Темы пользователя:</b>
                                                <div style="height: 15px;"></div>
                                                    <table class="table big_table">
                                                    <tbody><tr>
                                                    <td>Название</td>
                                                    <!--<td>Кеш/Выдача</td>-->
                                                    <td>Запрос</td><td>Начало</td><td>Конец</td><td style="display:none">Опции</td><td width="100">Тариф/Язык</td><td>Действия</td>
                                                    </tr>
                                                        
                                                        <form method="post" action="?user_id=4217#mod_user" id="refreshpost_6959"></form>
                                                            <input type="hidden" name="action" value="refreshpost">
                                                            <input type="hidden" name="order_id" value="6959">
                                                        
                                                        
                                                        ';
                                                        foreach ($user_info['orders'] as $order)
                                                        {
                                                            echo '<tr>
                                                        <td>

                                                        <form method="post" action="?user_id='.$_GET['user_id'].'&server='.$_GET['server'].'" id="refreshform_'.$order['order_id'].'">
                                                        <input type="hidden" name="action" value="refreshorder">
                                                        <input type="hidden" name="order_id" value="'.$order['order_id'].'">
                                                        </form>
                                                        <form method="post">
                                                        <input type="hidden" name="action" value="editorder">
                                                        <input type="hidden" name="order_id" value="'.$order['order_id'].'">
                                                        <p style="font-size: 12px; display: inline;">#'.$order['order_id'].'</p>
                                                        <br><input type="text" name="order_name" value="'.htmlspecialchars($order['order_name']).'" style="width: 15ex; height: 3em; padding: 1em 0 1em 0;"></td>
                                                        <td><textarea class="edit_keywords" name="order_keyword" cols="20" rows="3">'.htmlspecialchars($order['order_keyword']).'</textarea></td>
                                                        <td><input type="text" name="order_start" value="'.date('d.m.Y',$order['order_start']).'" style="width: 10ex; height: 3em; padding: 1em 0 1em 0;"></td>
                                                        <td><input type="text" name="order_end" class="ordertime" value="'.date('d.m.Y',$order['order_end']).'" style="width: 10ex; height: 3em; padding: 1em 0 1em 0;"></td>
                                                        <td>
                                                        <input type="checkbox" name="order_nastr" title="Автотональность" '.($order['order_nastr']==1?'checked':'').'>
                                                        <input type="hidden" name="ful_com" value="1" title="Полный текст" checked="">
                                                        <input type="hidden" name="order_engage" value="1" title="Engagement" checked="">
                                                        
                                                        </td>
                                                        <td>                                                        
                                                        <select name="ord_lan">
                                                            <!--<option value="0" >Дефолтный(Русский)</option>-->
                                                            <option '.($order['order_lang']==1?'selected':'').' value="1">Иностранный</option>
                                                            <option '.($order['order_lang']==2?'selected':'').' value="2">Русский</option>
                                                            <option '.($order['order_lang']==4?'selected':'').' value="4">Азербайджан.</option>
                                                        </select>
                                                        </td><td>
                                                        <input style="margin-top:20px; margin-bottom:10px" class="btn" type="submit" value="применить">
                                                        </form>
                                                    
                                                        <select onchange="if (this.selectedIndex) eval(this.value);">
                                                            <option value="return false;">'.($order['user_id']==0?'Заблок.':'').'</option>
                                                            '.($order['user_id']!=0?'<option '.($order['user_id']==0?'selected':'').' value="document.getElementById(\'block'.$order['order_id'].'\').click();">Заблок.</option>':'<option value="document.getElementById(\'unblock'.$order['order_id'].'\').click();">Разблок.</option>').'
                                                            <option value="document.getElementById(\'del'.$order['order_id'].'\').click();">Удалить</option>
                                                            '.($order['third_sources']<=2?'':'<option value="document.getElementById(\'refreshform_'.$order['order_id'].'\').submit();">Обновить</option>').'
                                                        </select>
                                                        <form style="display:none" method="post" action="/admin/edituser/?user_id='.$order['user_id'].'&server='.$_GET['server'].'" onsubmit="return confirm(\'Заблокировать тему?\')">
                                                        <input type="hidden" name="action" value="blockorder">
                                                        <input type="hidden" name="order_id" value="'.$order['order_id'].'">
                                                        <input type="hidden" name="user_id" value="'.$_GET['user_id'].'">
                                                        <input class="btn" type="submit" value="Заблок. " id="block'.$order['order_id'].'">
                                                        </form><br>

                                                        <form style="display:none" method="post" action="/admin/edituser/?user_id='.$_GET['user_id'].'&server='.$_GET['server'].'" onsubmit="return confirm(\'Разблокировать тему?\')">
                                                        <input type="hidden" name="action" value="unblockorder">
                                                        <input type="hidden" name="order_id" value="'.$order['order_id'].'">
                                                        <input type="hidden" name="user_id" value="'.$_GET['user_id'].'">
                                                        <input class="btn" type="submit" value="Разблок. " id="unblock'.$order['order_id'].'">
                                                        </form><br>

                                                        <form style="display:none" method="post" action="/admin/edituser/?user_id='.$_GET['user_id'].'&server='.$_GET['server'].'" onsubmit="return confirm(\'Удалить тему?\')&&confirm(\'Точно удалить тему?\')&&confirm(\'Точно-точно удалить тему?\');">
                                                        <input type="hidden" name="action" value="deleteorder">
                                                        <input type="hidden" name="order_id" value="'.$order['order_id'].'">
                                                        <input id="del'.$order['order_id'].'" class="btn" type="submit" value="Удалить">
                                                        </form><br>
                                                        </td>
                                                        </tr>';
                                                        }
                                                        echo '
                                              </tbody></table>
                                        <table style="margin-left: -12px;">
                                        <form method="post">
                                        <input type="hidden" name="action" value="addorder">
                                        <input type="hidden" name="user_id" value="'.$_GET['user_id'].'">
                                        <input type="hidden" name="ut_id" value="'.$user_info['user']['ut_id'].'">
                                        <input type="hidden" name="order_id" value="addorder">
                                        <tr>
                                        <td><input type="text" name="order_name" value="" style="width: 108px; height: 3em; padding: 1em 0 1em 0; margin-right: 14px;"></td>
                                        <td><textarea name="order_keyword"  class="edit_keywords"  margin-right: 15px;" rows="3"></textarea></td>
                                        <td><input type="text" name="order_start" class="ordertime" value="'.date("d.m.Y").'" style="width: 72px; margin-right: 14px; height: 3em; padding: 1em 0 1em 0;"></td>
                                        <td><input type="text" name="order_end" class="ordertime" value="'.date("d.m.Y").'" style="width: 72px; margin-right: 14px; height: 3em; padding: 1em 0 1em 0;"></td>
                                        <td>
                                        <input type="hidden" name="ful_com" value="1" checked title="Полный текст">
                                        <input type="hidden" name="order_engage" value="1" checked title="Engagement">
                                        <input type="checkbox" name="order_nastr" value="1" checked title="Автотональность">
                                        </td>
                                        <td>
                                        </td>
                                        <td><input type="submit" class="btn" value="добавить"></td>
                                        </tr>
                                        </form>
                                        </table>
                                        <div style="height: 15px;"></div>   
                                        <b>Отключенные темы:</b>
                                        <div style="height: 15px;"></div>
                                            <table class="table table-striped">
                                            </table>
                                        <div style="height: 15px;"></div><table>
                                        </table>

                                    <b>Пересобрать тему:</b>
                                    <form method="post" action="/admin/edituser/?user_id='.$_GET['user_id'].'&server='.$_GET['server'].'">
                                        <input type="text" name="order_id_id">
                                        <input type="text" name="os2" class="rech" value="'.date('d.m.Y',time()).'" style="width: 10ex; height: 3em; padding: 1em 0 1em 0;"> - <input type="text" name="oe2" class="rech" value="'.date('d.m.Y',time()).'" style="width: 10ex; height: 3em; padding: 1em 0 1em 0;">
                                        <input type="submit" value="Запустить" class="btn"/>
                                    </form>

                                    <b>Случайная выборка:</b>
                                    <form method="post" action="/admin/edituser/?user_id='.$_GET['user_id'].'&server='.$_GET['server'].'">
                                    <input type="hidden" name="action" value="selection">
                                    Номер темы: <input type="text" name="order_id"/> Размер выборки: <input type="text" name="selection_size"/> Без спама <input type="checkbox" name="notspam"/><br>
                                    Начало периода: <input type="text" class="ordertime" name="start_time"/> Конец периода: <input type="text" name="end_time"/> <input type="submit" value="Создать" class="btn"/>
                                    </form>
                                        
                                    <b>Последние заходы:</b>
                                        <div style="height: 15px;"></div>    
                                    <div style="height: 150px; overflow-y: scroll; width: 60%;">
                                        <table class="table table-striped add_res">
                                        <tbody>
                                        ';
                                        foreach ($user_info['log'] as $klog => $log)
                                        {
                                            echo '<tr><td>'.($klog+1).'</td><td>'.date('d.m.y H:i:s',$log['log_time']).'</td><td><a href="http://www.geoiptool.com/ru/?IP='.$log['log_ip'].'" target="_blank">'.$log['log_ip'].'</a></td></tr>';
                                        }
                                        echo '
                                        </tbody></table>
                                    </div>
                                    Всего заходов: <b>'.($klog+1).'</b><br><br>

                    
                                        
                                    <!--<b>Чтение подписки:</b>
                                        <div style="height: 15px;"></div>    
                                    <div style="height: 150px; overflow-y: scroll; width: 60%;">
                                        <table class="table table-striped add_res"><tbody><tr><td>Пользователь без подписки</td></tr>
                                        </tbody></table>
                                    </div>
                                    Всего действий: <b>0</b>-->
                                    <div style="height: 15px;"></div>
                                    
                                    <!-- в таблице не нужны теги р, нужно как в первых строках просто текст -->
                                    <!--<b>Реал-тайм собранные темы:</b>
                                    <div style="height: 15px;"></div>
                                    <table class="table table-striped">
                                    <tbody><tr class="dark_head">
                                    <td>ID</td>
                                    <td>Ключевые слова:</td>
                                    <td>Начало отчета</td>
                                    <td>Конец отчета</td>
                                    <td>Последнее время сбора</td>
                                    </tr><tr><td>6959</td><td>mosinzhproekt|Мосинжпроект|(("мос"|"mos") /+1 ("инж"|"inz") /+1 (proekt|проект))</td><td>04.03.2014</td><td>04.04.2017</td><td>04.04.14 15:46:22</td></tr>
                                        </tbody></table>-->
                                    <!--<div style="height: 15px;"></div>
                                    
                                    
                                    <a href="http://188.120.239.225/tools/tp_info.php" target="_blank">[ Статистика реал-тайм сбора ]</a>-->
                                    <div style="height: 15px;"></div>
                                        <b>История счетов</b>
                                    <div style="height: 15px;"></div>
                                    <table class="table table-striped" style="width: 70%;">
                                        <tbody><tr class="dark_head">
                                            <td>№</td><td>Время</td><td></td><td>Тариф</td></tr>
                                        ';
                                        $status_bil[-1]='Ошибка платежа';
                                        $status_bil[0]='Выставлен счет';
                                        $status_bil[1]='Ошибка платежа';
                                        $status_bil[2]='Проведен';
                                        foreach ($user_info['bil'] as $bil)
                                        {
                                            $bil_id++;
                                            echo '
                                            <tr>
                                                <td>'.$bil_id.'</td><td>'.date('d.m.y H:i:s',$bil['date']).'</td><td>'.$status_bil[$bil['status']].'</td><td>'.$bil['tariff_name'].'</td>
                                            </tr>';
                                        }
                                        echo '
                                    </tbody></table>
                                    
                                    <!--<div>
                                        <form method="post">
                                        <textarea name="user_response">

                                        </textarea>
                                        </form>
                                    </div>        -->            
                    
                        
                    </div>
        </span>
        </div>
    </div>';

?>