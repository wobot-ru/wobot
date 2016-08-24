<?php


$root = '/var/www/daemon/';
require_once($root . 'com/config.php');
require_once($root . 'com/db.php');
require_once($root . 'sentimentjob/wobot.linguistics.php');
//require_once($root . 'sentimentjob/wobot.NaiveBayes.php');
require_once($root . 'duplicatesjob/duplicates/document_hash.php');
require_once($root . 'duplicatesjob/duplicates/itemhashes_model.php');


$db = new database();
$db->connect();

require_once('/var/www/bot/kernel.php');


class duplicates extends linguistics
{


    var $corpora = array( /*post_id, 0 - post_content, 1 - post_host, 2 - post_nastr + 3 - post_theme, 4 - post_parent*/);
    var $order_id;


    var $db;

    var $mstart = 0;
    var $mend = 0;



    function __construct($order_id)
    {
        parent::__construct();

        $this->db = new database();
        $this->db->connect();
        $this->order_id = $order_id;

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

        //$this->getCorpora($order_id);
        //$this->hostfile = "hosts" . $this->order_id . ".txt";
    }


    private function getCorpora( /*$order_id*/)
    {
        $this->checkConnection();
        $sql = "SELECT post_id, post_content
                 FROM blog_post as p
                 WHERE p.order_id={$this->order_id}
                 AND post_time>" . $this->mstart . " AND post_time<" . ($this->mend + 86400) . "
                 ORDER BY post_time ASC
                 ";

        /*post_host, host_theme
         * ful_com_post
         * LEFT JOIN blog_full_com AS f ON p.post_id=f.ful_com_post_id
                 LEFT JOIN blog_host AS h ON p.post_host=h.host_name    
         * */
        //echo $sql;

        $res = $this->db->query($sql);

        if (!$res) throw new Exception("Failed to get corpora from DB\n" . $sql . "\n\n");

        while ($text = $this->db->fetch($res))
        {
            $this->corpora[$text['post_id']] = array($text['post_content'],
                                                     $text['post_id']);
        }

    }

/*
SELECT p.post_id, p.parent, p.post_content, f.ful_com_post, b.blog_nick, FROM_UNIXTIME(p.post_time), p.post_link
FROM blog_post as p
LEFT JOIN blog_full_com AS f ON f.ful_com_post_id=p.post_id
LEFT JOIN robot_blogs2 as b ON p.blog_id=b.blog_id
WHERE order_id=2504

id поста
id родителя
короткий текст
полный текст
дата и время поста
ник автора
ссылка на пост

*/
    function makeIndex()
    {
        global $db;
        foreach ($this->corpora as $id => $text)
        {
            //echo $text[0]." -> ".$id."\n";
            if ($text[0] == '') continue;
            $ihmodel = new Itemhashes_Model($text[0], $id, $this->order_id);
            if ($ihmodel == false) continue;
            $ihmodel->save();
        }
    }


    function countDuplicates()
    {
        $similar_text = $this->getSetting();
        $similar_text = 85;

        foreach ($this->corpora as $id => $text)
        {
            $ihmodel = new Itemhashes_Model($text[0], $id, $this->order_id, 5, $similar_text);
            $dup = $ihmodel->isDup();

            if ($dup == 0) {
                echo ".";
                //echo "дубли не найдены\n";
                continue;
            }
                /*elseif (!is_array($dup))
                {
                    echo "!";
                    if ($this->corpora[$dup][1] != '') {
                        $this->corpora[$id][1] = $this->corpora[$dup][4];
                    }
                    else
                    {
                        $this->corpora[$dup][1] = $id;
                        $this->corpora[$id][1] = $id;
                    }
                    continue;
                }*/
            elseif (count($dup) > 0)
            {
                echo "@";
                //  print_r($dup);
                foreach ($dup as $key => $idd)
                {
                    //echo " $idd ";
                    if ($this->corpora[$idd][1] != '') {
                        $this->corpora[$id][1] = $this->corpora[$idd][1];
                    }
                    else
                    {
                        $this->corpora[$idd][1] = $id;
                        $this->corpora[$id][1] = $id;
                    }
                    //echo " <new></new>";
                    //print_r($this->corpora[$idd][4]);
                }

            }
        }

    }

    function singleDuplicates($did)
    {

        $ihmodel = new Itemhashes_Model($this->corpora[$did][0], $did, $this->order_id);
        $ihmodel->save();
        $dup = $ihmodel->isDup();

        if ($dup == 0) {

            echo "дубли не найдены\n";
        }
            /*elseif (!is_array($dup))
            {
                echo "!";
                if ($this->corpora[$dup][1] != '') {
                    $this->corpora[$id][1] = $this->corpora[$dup][4];
                }
                else
                {
                    $this->corpora[$dup][1] = $id;
                    $this->corpora[$id][1] = $id;
                }
                continue;
            }*/
        elseif (count($dup) > 0)
        {
            echo "\nнайдены дубли\n";
            print_r($dup);
            foreach ($dup as $key => $idd)
            {
                //echo " $idd ";
                if ($this->corpora[$idd][1] != '') {
                    $this->corpora[$id][1] = $this->corpora[$idd][1];
                }
                else
                {
                    $this->corpora[$idd][1] = $id;
                    $this->corpora[$id][1] = $id;
                }
                //echo " <new></new>";
                //print_r($this->corpora[$idd][4]);
            }

        }

    }

    public function cleanFiltered()
    {
        foreach ($this->corpora as $id => $text)
        {
            if ($text[2] === 'f') {
                unset($this->corpora[$id]);
            }
        }
    }

    public function saveParent()
    {

        foreach ($this->corpora as $id => $text)
        {
            if ($text[1] == '') continue;

            $sql = "UPDATE blog_post SET parent = {$text[1]} WHERE post_id = $id AND order_id = $this->order_id";
            //echo $sql;
            $res = $this->db->query($sql);
            if (!$res) die ("db error");
            //die();

        }
    }

    public function plotCorpora()
    {
        print_r($this->corpora);
    }

    function groupDuplicates()
    {
        foreach ($this->corpora as $id => $text)
        {
            $duplicates[$text[1]][] = $text[0];
        }
        print_r($duplicates);
    }

    public function duplicatesController()
    {


        //die("UUEE");
        $this->checkConnection();
        $this->getCorpora();
        //$this->plotCorpora();
        $this->checkConnection();
        echo "Индексируем сообщения\n";
        $this->makeIndex();
        echo "Считаем дубли\n";
        $this->countDuplicates();
        //$this->singleDuplicates(10799192);
        
        //Это для вывода
        //$this->groupDuplicates();
        echo "Сохраняем";
        $this->saveParent();
        // print_r($this->corpora);

        $this->checkConnection();
        $sql = "UPDATE blog_orders SET similar_text=" . @mktime(0, 0, 0, date("n"), date("j"), date("Y")) . " WHERE order_id={$this->order_id}";
        $this->db->query($sql);

        //$this->saveParentDuplicates();
    }

    private function checkConnection()
    {
        while (!$this->db->ping()) {
            echo "MYSQL disconnected, reconnect after 10 sec...\n";
            sleep(10);
            $this->db->connect();
        }
    }


    private function getLastCheckedDate()
    {
        $this->checkConnection();
        $sql = 'SELECT similar_text, order_start, order_end FROM blog_orders WHERE similar_text!=0 AND order_id=' . $this->order_id . ' LIMIT 1';

        //echo "!!!!!!!!\n\n\n\n";
        //echo $sql . "\n";
        $res = $this->db->query($sql);
        // (!$res)
        $order = $this->db->fetch($res);
        if (empty($order)) die('DIE!EDDE!!!111');

        //print_r($order);
        //echo "!!!!!!!!\n\n\n\n";

        if ($order['similar_text'] >= $order['order_start']) {
            if ($order['similar_text'] != 0) {
                $this->mstart = $order['similar_text'];
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

    function getSetting()
    {
        $order = $this->db->query("SELECT order_id,order_settings FROM blog_orders WHERE order_id = {$this->order_id} ORDER BY order_id DESC");
        //echo 'SELECT order_id,order_settings FROM blog_orders WHERE order_id = {$this->order_id} ORDER BY order_id DESC';
        $orders=$this->db->fetch($order);
        //print_r($orders);
        $settings = json_decode($orders['order_settings'], true);
        //print_r($settings);
        //echo $settings['similar_text'];
        if ($settings['similar_text']<50) $settings['similar_text']=50;
        return $settings['similar_text'];
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
       // mailerror("nikanorov@wobot.co, for.uki@gmail.com", "Упал duplicatesjob shutdown");
        mailerror("nikanorov@wobot.co", "Упал duplicatesjob shutdown");

        //yourPrintOrMailFunction("SHUTDOWN");
    }
}

$order_delta=$_SERVER['argv'][1];
$debug_mode=$_SERVER['argv'][2];
$fp = fopen('../pids/dj'.$order_delta.'.pid', 'w');
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



    //$ressec = $db->query('SELECT * FROM blog_orders WHERE order_nastr!=0'.$condition);
    $ressec = $db->query('SELECT * FROM blog_orders WHERE similar_text!=0'.$condition.'
                        AND MOD (order_id, '.$_SERVER['argv'][2].') = '.$_SERVER['argv'][1].'');
    $sql = 'SELECT * FROM blog_orders WHERE similar_text!=0'.$condition.'
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
        $dup = new duplicates($blog['order_id']);
        $dup->duplicatesController();
        $exec_time = microtime(true) - $start_time;
        echo "ВРЕМЯ ВЫПОЛНЕНИЯ: $exec_time; ";
        echo "Все сделано.\n\n";

    }

    // выполнение действий

    sleep(600);
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