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
    curl_close( $ch );
    return $content;
}

if ($_POST['action']=='adduser') 
{
    $cont=parseUrlpost('http://'.$_POST['server'].'/api/admin/adduser',$_POST);
    $mcont=json_decode($cont,true);
    if ($mcont['user_id']!='') 
    {
        header('Location: /admin/edituser/?user_id='.$mcont['user_id'].'&server='.$_POST['server']);
        die();
    }
}

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
                $("#user_to_date").datepicker();
                $("#user_to_date").datepicker("setDate","+2w");
            });
        </script>
      </head>
';
// print_r($_POST);
// print_r($_POST);
// $user_info=json_decode(file_get_contents('http://'.$_GET['server'].'/api/admin/getuser?user_id='.$_GET['user_id']),true);
foreach ($config['servers'] as $server)
{
    $clients_server=json_decode(file_get_contents('http://'.$server.'/api/admin/clients?search_user='.$_GET['search_user'].'&payed='.$_GET['payed']),true);
    foreach ($clients_server['user'] as $client)
    {
        $client['server']=$server;
        $clients[]=$client;
    }
}

echo '
  <body>
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
                <li><a href="/admin/edituser/?user_id='.$_GET['user_id'].'&server='.$_GET['server'].'">Редактир. пользователей</a></li>
                <!--<li><a href="#last_reg">Демо-кабинеты</a></li>-->
                <li class="active"><a href="#client_list">Список клиентов</a></li>
                <!--<li><a href="#new_res">Добавление ресурсов</a></li>
                <li><a href="#special_ord">Специальные предложения</a></li>
                <li><a href="#tariffs">Тарифы</a></li> -->
                <li><a href="/admin/bills/?user_id='.$_GET['user_id'].'&server='.$_GET['server'].'">Последние платежи</a></li>
                <!--<li><a href="#notaprooved">Не подтверж.</a></li>--><!--вкладки-->
            </ul>
<div class="row tab-pane active" id="new_user"> <!--добавление пользователя-->
                    <div class="inner-tab-container">
                                <form class="navbar-search pull-right" action="?user_id='.$_GET['user_id'].'#client_list"> 
                                    <input type="text" class="search-query" name="search_user" value="'.$_GET['search_user'].'" placeholder="Поиск"/>
                                    Клиеты: <input type="checkbox" name="payed" '.(intval($_GET['payed'])==1?'checked':'').' value="1"/>
                                </form> <!--панель поиска -->
                                <br><br>
                                        <table class="table table-striped alfa_table">
                                        <tr><td>E-mail</td><td>Дата окончания</td><td>Сервер</td><td>Действия</td></tr>';
                                        foreach ($clients as $client)
                                        {
                                            echo '<tr>
                                                <td >'.$client['user_email'].'</td><td>'.date('d.m.Y',$client['ut_date']).'</td><td>'.$client['server'].'</td><td style="width: 50px;"><a href="/admin/edituser/?user_id='.$client['user_id'].'&server='.$client['server'].'"><i class="icon-pencil" title="редактировать"></a></td>
                                            </tr>';
                                            // echo '<tr>
                                            //   <td >'.$client['user_email'].'</td><td>'.$client['ut_date'].'</td><td>'.$client['server'].'</td><td style="width: 50px;"><a href="/admin/edituser/?user_id='.$client['user_id'].'&server='.$client['server'].'"><i class="icon-pencil" title="редактировать"></a></td>
                                            // </tr>';
                                        }
                                        echo '
                                        </table>            

                    </div>
                </div>';

?>