<?php


//TODO: короче надо так переписать чтобы КОРПОРА был изначальный класс

/*
 * таблицы:
 * items_hashes
 * items_hashes_summary
 * post_sentiment
 * blog_host
 * words
 *
 * blog_post
 * blog_order
 */
$root = '/var/www/daemon/';
require_once("wobot.complexSentiment.php");


/* TODO:
* ++ Перенести "words" на production
* ++ прогнать, понять по скоросоти
* подумать что делать с дублями - добавить order_id в базу
*
*/




class prodSentiment extends complexSentiment
{

    var $mstart = 0;
    var $mend = 0;


    function __construct($order_id, $mstart = '', $mend = '')
    {
        parent::__construct($order_id);

        if ($mstart != '' && $mend != '') {
            // echo "DATES\n";
            $this->mstart = strtotime($mstart);
            $this->mend = strtotime($mend);
        }
        else
        {
            echo "NO DATES\n";
            $this->getLastCheckedDate();
        }
        $this->getCorpora();
        //    print_r($this->corpora);
        //die("\nEND");
    }

    private function checkConnection()
    {
        while (!$this->db->ping()) {
            echo "MYSQL disconnected, reconnect after 10 sec...\n";
            sleep(10);
            $this->db->connect();
        }
    }

    private function getCorpora()
    {
        /*
        echo $this->mstart;
        echo "\n<br>";
        echo $this->mend;
        echo "\n";

        */

        //die("THIS IS FUCKING NEW!");

        $this->checkConnection();
        $sql = "SELECT post_id, post_content, post_host, host_theme, post_nastr, ful_com_post
                 FROM blog_post as p
                 LEFT JOIN blog_full_com AS f ON p.post_id=f.ful_com_post_id
                 LEFT JOIN blog_host AS h ON p.post_host=h.host_name
                 WHERE p.order_id={$this->order_id}
                 AND post_time>" . $this->mstart . " AND post_time<" . ($this->mend + 86400) . "
                 ";

        //echo $sql . "\n";
        //die();
        $res = $this->db->query($sql);

        if (!$res) throw new Exception("Failed to get corpora from DB\n" . $sql . "\n\n");

        while ($text = $this->db->fetch($res))
        {

            $this->corpora[$text['post_id']] = array($text['post_content'],
                                                     $text['post_host'],
                                                     0,
                                                     null,
                                                     null,
                                                     null);
            //$text['ful_com_post']);
            $this->hosts[$text['post_host']] = $text['host_theme'];


        }
        //$this->hosts = array_unique($this->hosts);
    }

    private function getLastCheckedDate()
    {
        $this->checkConnection();
        $sql = 'SELECT order_nastr, order_start, order_end FROM blog_orders WHERE order_nastr!=0 AND order_id=' . $this->order_id . ' LIMIT 1';

        //echo "!!!!!!!!\n\n\n\n";
        //echo $sql . "\n";
        $res = $this->db->query($sql);
        // (!$res)
        $order = $this->db->fetch($res);
        if (empty($order)) die('DIE!EDDE!!!111');

        //print_r($order);
        //echo "!!!!!!!!\n\n\n\n";

        if ($order['order_nastr'] >= $order['order_start']) {
            if ($order['order_nastr'] != 0) {
                $this->mstart = $order['order_nastr'];
            }
            else
            {
                $this->mstart = $order['order_start'];
            }
        }
        else
        {
            $this->mstart = $order['order_start'];
        }
        if ($order['order_end'] >= @mktime(0, 0, 0, date("n"), date("j"), date("Y"))) {
            $this->mend = @mktime(0, 0, 0, date("n"), date("j") - 1, date("Y"));
        }
        else
        {
            if ($order['order_end'] != 0) {
                $this->mend = $order['order_end'];
            }
            else
            {
                $this->mend = mktime(0, 0, 0, date("n"), date("j") - 1, date("Y"));
            }
        }
    }


    private function checkHostThemes()
    {
        // php wobot.prodSentiment.php
        foreach ($this->hosts as $host => $theme)
        {

            if ($theme == '') {
                $url = "http://bar-navig.yandex.ru/u?show=31&url=http://" . $host;
                $content = parseURLproxy($url, $this->proxyarr[6]);
                sleep(2);
                $mas = simplexml_load_string($content);
                $json = json_encode($mas);
                $mas = json_decode($json, true);
                $topic = $mas['topics']['topic']['@attributes']['title'];
                $string .= $host . "\t" . $topic . "\n";
                //echo $string . "\n";
                if ($topic == '') $topic = 'n';

                $sql = "INSERT INTO blog_host (host_name, host_theme) VALUES ('$host', '$topic')";
                //echo $sql . "\n";

                $res = $this->db->query($sql);


            }

            if ($theme == 'n') {
                continue;
            }
            elseif (in_array($theme, $this->smiArr))
            {
                $this->hosts[$host] = 'NEWS';
            }
            elseif (in_array($theme, $this->socArr))
            {
                $this->hosts[$host] = 'SOCIAL';
            }
            else $this->hosts[$host] = 'OTHER';

            //echo $url;


            //$content = parseURLproxy($url, $this->proxyarr[8]);
            //echo $content."\n";

        }
    }

    private function saveHostToDb()
    {
        $sql = "SELECT * FROM blog_host";
        $res = $this->db->query($sql);

        while ($host = $this->db->fetch($res)) {
            $hosts[$host['host_name']] = $host['host_id'];
        }


    }

    private function getHostsFromDb()
    {
        //TODO: делается в getCorpora
    }


    public function prodController()
    {


        //print_r($this->hosts);

        echo "1. Всего постов для {$this->order_id}: " . count($this->corpora) . "\n";
        $this->checkHostThemes();
        $this->setCorporaHostThemes();

        //print_r($this->corpora);

        echo "2. Расставлены типы ресурсов \n";
        $this->makeIndex();
        $this->groupNewsDuplicates();
        $this->filterByNews();
        $this->cleanFiltered();

        echo "3. Убраны репосты новостей: " . count($this->corpora) . "\n";

        $this->filterByWords(1, 0);
        $this->cleanFiltered();

        echo "4. Убраны сообщения без тональных слов: " . count($this->corpora) . "\n";

        //echo "\n\n";
        $this->countCorporaSentimentNaiveBayes2(0);

        $this->saveTone();

        $this->checkConnection();
        $sql = "UPDATE blog_orders SET order_nastr=" . @mktime(0, 0, 0, date("n"), date("j"), date("Y")) . " WHERE order_id={$this->order_id}";
        $this->db->query($sql);



        //echo $sql . "\n";
        //die();


        //print_r($this->corpora);
    }

    public function testController()
    {


        echo "\n1. Всего постов для {$this->order_id}: " . count($this->corpora) . "\n";
        $this->checkHostThemes();
        $this->setCorporaHostThemes();

        //print_r($this->corpora);

        echo "2. Расставлены типы ресурсов \n";
        $this->makeIndex();
        $this->groupNewsDuplicates();
        $this->filterByNews();
        $this->cleanFiltered();

        echo "3. Убраны репосты новостей: " . count($this->corpora) . "\n";

        $this->filterByWords(1, 0);
        $this->cleanFiltered();

        echo "4. Убраны сообщения без тональных слов: " . count($this->corpora) . "\n";

        echo "\n\n";
        $this->countCorporaSentimentNaiveBayes2(0);


        $this->saveToneSafe();
        //        die("READY FOR SAVE !!!!!!!!!!!");

        //$sql = "UPDATE blog_orders SET order_nastr=".mktime(0,0,0,date("n"),date("j"),date("Y"))." WHERE order_id={$this->order_id}";
        //$this->db->query($sql);
        //echo $sql."\n";
    }

    public function saveTone()
    {
        //TODO: если уже проставлено не затирать
        foreach ($this->corpora as $id => $text)
        {
            if ($text[2] === 'f') continue;

            $tone = $text[2];

            $this->checkConnection();
            $sql = "UPDATE blog_post SET post_nastr = $tone WHERE order_id = {$this->order_id} AND post_id = $id";
            $this->db->query($sql);
            //echo $sql . "\n";
        }
        //echo $sql;
        //$res = $this->db->query($sql);
        //if (!$res) die("false");
        //
    }

    public function saveToneSafe()
    {

        $sql = "DELETE FROM post_sentiment WHERE oid = $this->order_id";
        echo $sql;
        //die();
        $res = $this->db->query($sql);

        $sql = "INSERT INTO post_sentiment (oid, pid, tone_manual, tone_auto) VALUES";
        $i = 0;
        $ii = count($this->corpora) - 1;
        //echo "<h1>$ii</h1>";
        foreach ($this->corpora as $id => $text)
        {
            if ($text[2] === 'f') continue;

            $ptone = $text[2];

            if ($i == $ii) $sql .= " ($this->order_id, $id, $ptone, $ptone) ";
            else $sql .= " ($this->order_id, $id, $ptone, $ptone), ";
            $i++;
            //echo $i."<br>";
            //$query="UPDATE post_sentiment SET tone_manual = $post_tone WHERE p ost_id = $post_id LIMIT 1 ";
        }
        echo $sql;

        $this->checkConnection();
        $res = $this->db->query($sql);
        if (!$res) die("false");
        //$count = $db->fetch($res);
        //die("true");

    }
}

const DEBUG = 0;
const DEBUG_ORDER_ID = 145;

register_shutdown_function('handleShutdown');

function mailerror($to, $message)
{
    //$to      = 'nobody@example.com';

    $subject = 'error message';
    //$message = 'hello';
    $headers  = "From: noreply@wobot.ru\r\n";
    $headers .= "Bcc: noreply@wobot.ru\r\n";
    $headers .= "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type: text/html; charset=utf-8"."\r\n";

    mail($to, $subject, $message, $headers);
}

function handleShutdown() {
    $error = error_get_last();
    if($error !== NULL){
        $info = "[SHUTDOWN] file:".$error['file']." | ln:".$error['line']." | msg:".$error['message'] .PHP_EOL;
        //echo "!!!!123\n";
        mailerror("nikanorov@wobot.co", $info);
        //mailerror("nikanorov@wobot.co, for.uki@gmail.com", $info);
        //yourPrintOrMailFunction($info);
    }
    else{
        mailerror("nikanorov@wobot.co, for.uki@gmail.com", "Упал sentimentjob shutdown");

        //yourPrintOrMailFunction("SHUTDOWN");
    }
}


$order_delta=$_SERVER['argv'][1];
$debug_mode=$_SERVER['argv'][2];
$fp = fopen('../pids/sj'.$order_delta.'.pid', 'w');
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
    if (DEBUG) $condition = " AND user_id=".DEBUG_ORDER_ID;
    else $condition = " AND user_id!=".DEBUG_ORDER_ID;

    //TODO: добавить в запрсо "AND MOD (order_id, $_SERVER['argv'][2]) = $_SERVER['argv'][1]) - второй параметр комм строки

    /*echo 'SELECT * FROM blog_orders WHERE order_nastr!=0'.$condition.'
                        AND MOD (order_id, '.$_SERVER['argv'][2].') = '.$_SERVER['argv'][1].'';
    die();*/

    //$ressec = $db->query('SELECT * FROM blog_orders WHERE order_nastr!=0'.$condition);
    $ressec = $db->query('SELECT * FROM blog_orders WHERE order_nastr!=0'.$condition.'
                        AND MOD (order_id, '.$_SERVER['argv'][2].') = '.$_SERVER['argv'][1].'');
    $sql = 'SELECT * FROM blog_orders WHERE order_nastr!=0'.$condition.'
                        AND MOD (order_id, '.$_SERVER['argv'][2].') = '.$_SERVER['argv'][1].'';
    echo $sql."\n";



    echo "\n";
    while ($blog = $db->fetch($ressec))
    {
        $start_time = microtime(true);
        //echo $blog['order_id']."\n";
        //continue;
        $date_today = date("d.m.y"); //присвоено 03.12.01
        $today[1] = date("H:i:s"); //присвоит 1 элементу массива 17:16:17
        echo("Текущее время: $today[1] и дата: $date_today .\n");

        echo "Обрабатываем отчет {$blog['order_name']}, id:{$blog['order_id']}\n";
        $sent = new prodSentiment($blog['order_id']);
        $sent->prodController();
        $exec_time = microtime(true) - $start_time;
        echo "ВРЕМЯ ВЫПОЛНЕНИЯ: $exec_time\n";
        echo "Все сделано.\n\n";

    }

    // выполнение действий

    sleep(600);
}

?>