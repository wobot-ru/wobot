<?php


$root = '/var/www/daemon/';
require_once($root . "duplicatesjob/wobot.duplicatesNEW.php");

$redis=new Redis() or die("Can'f load redis module.");
$redis->connect('127.0.0.1');

$db = new database();
$db->connect();

const DEBUG = 0;
const DEBUG_ORDER_ID = 145;


register_shutdown_function('handleShutdown');

function mailerror($to, $message)
{
    //$to      = 'nobody@example.com';

    $subject = 'error message';
    //$message = 'hello';
    $headers = "From: noreply@wobot.ru\r\n";
    $headers .= "Bcc: noreply@wobot.ru\r\n";
    $headers .= "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type: text/html; charset=utf-8" . "\r\n";

    mail($to, $subject, $message, $headers);
}

function handleShutdown()
{
    $error = error_get_last();
    if ($error !== NULL) {
        $info = "[SHUTDOWN] file:" . $error['file'] . " | ln:" . $error['line'] . " | msg:" . $error['message'] . PHP_EOL;
        //echo "!!!!123\n";
        mailerror("nikanorov@wobot.co", $info);
        mailerror("zmei123@yandex.ru", $info);
        //mailerror("nikanorov@wobot.co, for.uki@gmail.com", $info);
        //yourPrintOrMailFunction($info);
    }
    else {
        // mailerror("nikanorov@wobot.co, for.uki@gmail.com", "Упал duplicatesjob shutdown");
        mailerror("nikanorov@wobot.co", "Упал duplicatesjob shutdown");
        mailerror("zmei123@yandex.ru", "Упал duplicatesjob shutdown");

        //yourPrintOrMailFunction("SHUTDOWN");
    }
}

$order_delta = $_SERVER['argv'][1];
$debug_mode = $_SERVER['argv'][2];
$fp = fopen('/var/www/pids/dj' . $order_delta . '.pid', 'w');
fwrite($fp, getmypid());
fclose($fp);
echo $order_delta;


while (1)
{


    if (!$db->ping()) {
        echo "MYSQL disconnected, reconnect after 10 sec...\n";
        sleep(10);
        $db->connect();
        continue;
    }
   // if (DEBUG) $condition = " AND user_id=" . DEBUG_ORDER_ID;
   // else $condition = " AND user_id!=" . DEBUG_ORDER_ID;

    //TODO: добавить в запрсо "AND MOD (order_id, $_SERVER['argv'][2]) = $_SERVER['argv'][1]) - второй параметр комм строки


    //$ressec = $db->query('SELECT * FROM blog_orders WHERE order_nastr!=0'.$condition);
    $ressec = $db->query('SELECT * FROM blog_orders WHERE similar_text!=0' . $condition . '
                        AND MOD (order_id, ' . $_SERVER['argv'][2] . ') = ' . $_SERVER['argv'][1] . ' AND user_id!=145 AND ut_id!=0 AND user_id!=0');
    $sql = 'SELECT * FROM blog_orders WHERE similar_text!=0' . $condition . ' AND MOD (order_id, ' . $_SERVER['argv'][2] . ') = ' . $_SERVER['argv'][1] . '';
    echo $sql . "\n";
    echo "\n";

    while ($blog = $db->fetch($ressec))
    {
        $filters=json_decode($blog['order_settings'],true);
        $qtariff=$db->query('SELECT * FROM user_tariff WHERE user_id='.$blog['user_id'].' AND ut_id='.$blog['ut_id']);
        $tariff=$db->fetch($qtariff);
        $var=$redis->get('orders_'.$blog['order_id']);
        $m_dinams=json_decode($var,true);
        if ($m_dinams['count_post']>$filters['new_limit'] && $filters['new_limit']!='') continue;
        elseif ($m_dinams['count_post']>$tariff['tariff_posts'] && $tariff['tariff_posts']!=0) continue;

        $start_time = microtime(true);
        //echo $blog['order_id']."\n";
        //continue;
        $date_today = date("d.m.y"); //присвоено 03.12.01
        $today[1] = date("H:i:s"); //присвоит 1 элементу массива 17:16:17
        echo("Текущее время: $today[1] и дата: $date_today .\n");

        echo "Обрабатываем отчет {$blog['order_name']}, id:{$blog['order_id']}\n";
        $dup = new duplicates($blog['order_id']);
        $dup->duplicatesControllerNew();
        $exec_time = microtime(true) - $start_time;
        echo "ВРЕМЯ ВЫПОЛНЕНИЯ: $exec_time; ";
        echo "Все сделано.\n\n";

    }

    // выполнение действий
    sleep(3600);
}

/*
$dup = new duplicates(2437);
$dup->duplicatesController();
*/

/*
TODO:
SELECT `post_id`, parent, COUNT(*), `post_content`, post_time FROM `blog_post` WHERE `order_id`=1623 GROUP BY parent ORDER BY parent, post_time DESC
этот запрос нужен для методов API
*/

?>