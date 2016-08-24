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

if ($_GET['toPD']!='') file_get_contents('http://'.$_GET['server'].'/api/admin/topd?toPD='.$_GET['toPD']);
if ($_GET['delete_user_id']!='') file_get_contents('http://'.$_GET['server'].'/api/admin/deluser?user_id='.$_GET['delete_user_id']);

auth();
if (!$loged) 
{
    header('Location: /admin/index_dev.php');
    die();
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
        </script>
      </head>

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
                <li class="active"><a href="/admin/last/">Последние кабинеты <span id="num_inactive"></span></a></li>
                <li><a href="/admin/adduser/?user_id='.$_GET['user_id'].'&server='.$_GET['server'].'">Добавление пользователей</a></li>
                <li><a href="/admin/edituser/?user_id='.$_GET['user_id'].'&server='.$_GET['server'].'">Редактир. пользователей</a></li>
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
                <table id="user_list" class="table table-striped centred_icons">
                <tr class="dark_head">
                    <td title="Статус">Ст.</td>
                    <td></td>
                    <td></td>
                    <td >Почта</td>
                    <td>Телефон</td>
                    <td>Контактное лицо</td>
                    <td>Компания</td>
                    <td title="Дата регистрации">Дат.рег.</td>
                    <td title="Дата окончания">Дат.ок.</td>
                    <td>Кол.Тем</td>
                    <td>Промо-код</td>
           
                </tr>';
                foreach ($config['servers'] as $server)
                {
                    $users_server=json_decode(file_get_contents('http://'.$server.'/api/admin/last_reg'),true);
                    foreach ($users_server as $k_user_server => $users_server)
                    {
                        $users_server['server']=$server;
                        $all_users[]=$users_server;
                    }
                }
                usort($all_users, 'datesort');
                $status=array(1=>'не подтвержден',2=>'подтвержден',3=>'заблокирован');
                $status_ic=array(1=>'time',2=>'ok',3=>'warning-sign');
                foreach ($all_users as $item)
                {
                    echo '
                        <tr style="height: 65px;">
                            <td><i class="icon-'.$status_ic[$item['user_active']].'" title="'.$status[$item['user_active']].'"></td>
                            <td><a href="/admin/last/?toPD='.$item['user_id'].'&user_id='.$item['user_id'].'&server='.$item['server'].'"><i class="icon-briefcase"'.((intval($item['ref'])==1)?' style="background-color: #00ff00;"':'').' title="Экспорт в AmoCRM" style="margin: 0px;"></i></a><br><a href="/admin/edituser/?user_id='.$item['user_id'].'&server='.$item['server'].'"><i class="icon-pencil" title="редактировать"></i></a></td>
                            <td><form style="display: inline;" method="post" id="loginform_'.$item['user_id'].'" action="http://91.218.246.79/production/" target="_blank"><input type="hidden" name="token" value="'.$item['token'].'">
                                    <input type="hidden" name="user_id" value="'.$item['user_id'].'"><i onclick="$(\'#loginform_'.$item['user_id'].'\').submit();" class="icon-circle-arrow-right" style="cursor: pointer;" title="редактировать"></i></a></form><br><a href="/admin/last/?delete_user_id='.$item['user_id'].'&server='.$item['server'].'" onclick="return confirm(\'Удалить пользователя?\')&&confirm(\'Точно удалить пользователя?\')&&confirm(\'Точно-точно удалить пользователя?\');"><i class="icon-remove" title="Удалить"></i></a></td>
                            <td><div class="email_cut">'.(mb_strlen($item['user_email'],'UTF-8')>50?mb_substr($item['user_email'],0,50,'UTF-8'):$item['user_email']).'</div></td>
                            <td>'.htmlspecialchars($item['user_contact']).'</td>
                            <td>'.htmlspecialchars(stripslashes(mb_strlen($item['user_name'],'UTF-8')>15?mb_substr($item['user_name'],0,15,'UTF-8'):$item['user_name'])).'</td>
                            <td>'.htmlspecialchars(stripslashes(mb_strlen($item['user_company'],'UTF-8')>15?mb_substr($item['user_company'],0,15,'UTF-8'):$item['user_company'])).'</td>
                            <td>'.($item['user_ctime']==0?'-':date('j.n.Y H:i',$item['user_ctime'])).'</td>
                            <td>'.($item['user_ctime']==0?'-':date('j.n.Y',$item['ut_date'])).'</td>
                            <td>'.$item['tnum'].'</td>
                            <td>'.htmlspecialchars(stripslashes(mb_strlen($item['user_promo'],'UTF-8')>10?mb_substr($item['user_promo'],0,10,'UTF-8'):$item['user_promo'])).'</td>
                     
                        </tr>';
                }                 

            echo '
                  </table>
            </div>
        </span>
        </div>
    </div>';

?>