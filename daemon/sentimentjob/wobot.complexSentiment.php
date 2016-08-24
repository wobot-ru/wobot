<?php
//if (!extension_loaded('svm')) die('skip');
//die("ok");



/*
 * CURRENT TODO:
 * + 1. допилить groupNewsDuplicates()
 * + 2. все что новости не ставим тональность
 * + 3. все что нет слов из словаря не ставим тональность
 * + 6. допилить с хостами чтобы не качать их каждый раз. Чтонить типа get_hosts
 * + 4. на оставшемся массиве прогоняем тональность (уточнить какой id) id =  736
 * + 5. смотрим метрики
 */



//$root = '/var/www/daemon-dev/';
require_once($root . 'sentimentjob/wobot.linguistics.php');
require_once($root . 'sentimentjob/wobot.NaiveBayes.php');
require_once($root . 'sentimentjob/duplicates/document_hash.php');
require_once($root . 'sentimentjob/duplicates/itemhashes_model.php');

//УБРАТЬ (это для дублей)
$db = new database();
$db->connect();

require_once('/var/www/bot/kernel.php');


class complexSentiment extends linguistics
{

    //TODO: перекинуть в класс выше
    var $corpora = array( /*post_id, 0 - post_content, 1 - post_host, 2 - post_nastr + 3 - post_theme, 4 - post_parent*/);
    var $order_id;
    var $tonedict = array( /* form => tone*/);

    var $hosts = array( /*host*/);
    var $hostsfile = "hosts.txt";

    //TODO: перекинуть в класс выше
    var $db;

    //TODO: в themes
    var $proxyarr = Array( "212.33.250.197:8080",
"91.219.99.9:3128",
"89.110.49.190:80",
"212.33.245.235:3128",
"195.191.158.88:8080",
"194.125.255.126:3128",
"83.242.223.24:8080",
"89.233.104.114:8080",
"217.26.14.18:3128",
"62.152.35.72:8080");

    //TODO: в themes
    var $smiArr = Array("Тема: Газеты",
                        "Тема: Журналы",
                        "Тема: Агентства деловой информации",
                        "Тема: Информационные агентства",
                        "Тема: Периодика",
                        "Тема: Радиостанции",
                        "Тема: СМИ",
                        "Тема: Телепередачи",
                        "Тема: Телестудии и телекомпании");

    //TODO: в themes
    var $socArr = Array("Тема: Сервисы для блоггеров",
                        "Тема: Социальные сети",
                        "Тема: Интернет");


    //TODO: перекинуть в класс выше
    function __construct($order_id)
    {
        parent::__construct();
        $this->db = new database();
        $this->db->connect();
        $this->order_id = $order_id;
        //$this->getCorpora($order_id);
        $this->hostfile = "hosts".$this->order_id.".txt";

    }


    private function getHostsFromFile()
    {

        $fh = fopen($this->hostsfile, 'r');
        while ($line = fgets($fh))
        {

            $tmp = explode("\t", $line);
            $host = $tmp[0];
            $topic = trim($tmp[1]);

            //$topic = "Тема: Газеты";

            if ($topic == '') {
                $this->hosts[$host] = '';
                continue;
            }
            elseif (in_array($topic, $this->smiArr))
            {
                $this->hosts[$host] = 'NEWS';
            }
            elseif (in_array($topic, $this->socArr))
            {
                $this->hosts[$host] = 'SOCIAL';
            }
            else $this->hosts[$host] = '';
        }
    }


    private function getHostThemesFromFile()
    {
        $fh = fopen($this->hostsfile, 'r');
        while ($line = fgets($fh))
        {

            $tmp = explode("\t", $line);
            $host = $tmp[0];
            $topic = trim($tmp[1]);
            $topicarr[$topic][] = $host;
            // print_r($host);print_r($topic);echo"<br>";
        }

        //$topicarr = array_unique($topicarr);
        echo "<pre>";
        print_r($topicarr);

    }


    private function saveHostsToFile()
    {

        print_r($this->hosts);
        $socstring = '';
        $smistring = '';
        $notfound = '';

        foreach ($this->hosts as $host => $theme)
        {
            $url = "http://bar-navig.yandex.ru/u?show=31&url=http://" . $host;
            //echo $url;

            //$content = parseURLproxy($url, $this->proxyarr[8]);
            $content = parseURL($url, $this->proxyarr[6]);
            //echo $content."\n";
            sleep(2);
            $mas = simplexml_load_string($content);
            $json = json_encode($mas);
            $mas = json_decode($json, true);

            print_r($mas);

            $topic = $mas['topics']['topic']['@attributes']['title'];
            //echo "\n!!!!!!!!".$topic."!!!!!!!!!\n";

            $string .= $host . "\t" . $topic . "\n";
            echo $string;

        }


        //echo $string;
        ob_start();
        echo $string;
        $data = ob_get_clean();
        $fp = fopen($this->hostsfile, "w");
        fwrite($fp, $data);
        fclose($fp);
    }

    //TODO: перекинуть в класс выше
    private function getCorpora($order_id)
    {
        //$sql = "SELECT post_id, post_content, post_host, post_nastr FROM blog_post WHERE order_id = $order_id LIMIT 2400, 200";

        $sql = "SELECT post_id, post_content, post_host, post_nastr, ful_com_post
                FROM blog_post as p
                LEFT JOIN blog_full_com AS f ON p.post_id=f.ful_com_post_id
                WHERE p.order_id=$order_id
                LIMIT 10";


        //echo "$sql";
        //die();

        //AND p.parent = 2799840";

        //        die($sql);

        $res = $this->db->query($sql);

        if (!$res) throw new Exception("Failed to get corpora from DB");

        while ($text = $this->db->fetch($res))
        {

            $this->corpora[$text['post_id']] = array($text['post_content'],
                                                     $text['post_host'],
                                                     0,
                //$text['post_nastr'],
                                                     null,
                                                     null,
                                                     $text['ful_com_post']);




            $this->hosts[$text['post_host']] = $text['post_host'];
        }
        $this->hosts = array_unique($this->hosts);
        //print_r($this->hosts);
        //print_r($this->corpora);

    }


    function makeIndex()
    {
        global $db;
        foreach ($this->corpora as $id => $text)
        {
            //echo $text[0]." -> ".$id."\n";
            if ($text[0] == '') continue;
            $ihmodel = new Itemhashes_Model($text[0], $id);
            if ($ihmodel==false) continue;
            $ihmodel->save();
        }
    }

    //todo: в другой класс (дубли)
    function groupNewsDuplicates()
    {

        foreach ($this->corpora as $id => $text)
        {


            if ($text[3] == 'NEWS') {

                //TODO:debug
                /*
                echo "id =" . $id . "<br>\n";
                echo $text[0] . "<br>";
                */

                $ihmodel = new Itemhashes_Model($text[0], $id, $this->order_id);
                $dup = $ihmodel->isDup();
                print_r($dup);


                //print_r($dup);echo"\n";
                //echo "COUNT dup10 = ".count(10)."\n";
                //echo "COUNT dup = ".count($dup)."\n\n";
                //continue;

                if ($dup == 0) {
                    //echo "дубли не найдены\n";
                    echo".";
                }
                elseif (!is_array($dup))
                {
                    $this->corpora[$dup][4] = $id;
                    $this->corpora[$id][4] = $id;
                }
                elseif (count($dup) > 0)
                {
                    foreach ($dup as $key => $idd)
                    {
                        $this->corpora[$idd][4] = $id;
                        $this->corpora[$id][4] = $id;
                        //echo " <new></new>";
                        //print_r($this->corpora[$idd][4]);
                    }

                }

                //echo "<hr>";
            }

        }

        foreach ($this->corpora as $id => $text)
        {


            if ($text[4] != '') continue;

            $ihmodel = new Itemhashes_Model($text[0], $id);
            $dup = $ihmodel->isDup();

            if ($dup == 0) {
                //echo "дубли не найдены\n";
                continue;
            }
            elseif (!is_array($dup))
            {
                echo "найдены дубли\n";
                if ($this->corpora[$dup][4] != '') {
                    $this->corpora[$id][4] = $this->corpora[$dup][4];
                }
                else
                {
                    $this->corpora[$dup][4] = $id;
                    $this->corpora[$id][4] = $id;
                }
                continue;
            }
            elseif (count($dup) > 0)
            {
                echo "найдены дубли\n";
                foreach ($dup as $key => $idd)
                {
                    if ($this->corpora[$idd][4] != '') {
                        $this->corpora[$id][4] = $this->corpora[$idd][4];
                    }
                    else
                    {
                        $this->corpora[$idd][4] = $id;
                        $this->corpora[$id][4] = $id;
                    }
                    //echo " <new></new>";
                    //print_r($this->corpora[$idd][4]);
                }

            }
        }

        /*
        foreach ($this->corpora as $id => $text)
        {
            if ($text[4] == '') continue;

            $duparr[$id]=$text[4];

        }


        //print_r($duparr);
        arsort($duparr);

        foreach($duparr as $idd => $parid)
        {
            $dupstr.= $this->corpora[$idd][0]."\n";
        }
        */


        //TODO: создаем массив парент+NEWS, проходим все сообщения с парентом и ставим text[2]=0
        //все у кого есть парент ньюс

        /*
            ob_start();
            print_r($dupstr);
            $data = ob_get_clean();
            $fp = fopen("duplicates_test . txt", "w");
            fwrite($fp, $data);
            fclose($fp);
        */

        //print_r($this->corpora);

        //индексирование в базу и определение дублей
    }

    function filterByNews()
    {
        foreach ($this->corpora as $id => $text)
        {
            if ($text[3] == "NEWS") {
                $newsids[] = $id;
                $this->corpora[$id][2] = 'f';
            }

        }
        foreach ($this->corpora as $id => $text)
        {
            if ($text[4] == '') continue;
            if (in_array($text[4], $newsids)) {
                $this->corpora[$id][2] = 'f';
            }
        }
        //print_r($this->corpora);
    }

    //todo: в другой класс (тематика)
    function getHostsTheme()
    {
        $socstring = '';
        $smistring = '';
        $notfound = '';

        foreach ($this->hosts as $host => $theme)
        {
            $url = "http://bar-navig.yandex.ru/u?show=31&url=http://" . $host;

            $content = parseURLproxy($url, $this->proxyarr[0]);
            sleep(1);
            $mas = simplexml_load_string($content);
            $json = json_encode($mas);
            $mas = json_decode($json, true);

            $topic = $mas['topics']['topic']['@attributes']['title'];
            echo $host . "\t" . $topic . "\n";


            //if ($i>100) break;

            if ($topic == '') {
                $notfound .= $host . "\t" . $topic . "\n";
                $this->hosts[$host] = '';
                continue;
            }
            elseif (in_array($topic, $this->smiArr))
            {
                $smistring .= $host . "\t" . $topic . "\n";
                $this->hosts[$host] = 'NEWS';
            }
            elseif (in_array($topic, $this->socArr))
            {
                $socstring .= $host . "\t" . $topic . "\n";
                $this->hosts[$host] = 'SOCIAL';
            }
            else $this->hosts[$host] = '';
            //if ($yacaArr[$host]=='' && $topic=='') continue;
            //$filestring.=$num."\t".$host."\t".$yacaArr[$host]."\t".$topic."\n";


        }

        //print_r($this->hosts);
        //определение списка хостов и занесение в $corpora
    }

    function setCorporaHostThemes()
    {
        foreach ($this->corpora as $id => $text)
        {
            $this->corpora[$id][3] = $this->hosts[$text[1]];
        }

        //print_r($this->corpora);
    }


    public function filterByWords($dict, $full)
    {

        $sql = "SET NAMES UTF8";
        $res = $this->db->query($sql);
        $sql = "SELECT w.id, w.form, w.tone, w.pos FROM dictionary AS d LEFT JOIN words AS w ON d.word_id = w.id WHERE tone!=0 AND d.theme_id = 1";
        //$sql = "SELECT w.id, w.form, w.tone, w.pos FROM dictionary AS d LEFT JOIN words AS w ON d.word_id = w.id WHERE tone!=0 ";
        $res = $this->db->query($sql);

        while ($tWord = $this->db->fetch($res)) {
            $tonedWords[] = $tWord['form'];
        }

        foreach ($this->corpora as $id => $text)
        {

            if ($full && $text[5] != '') $post_content = $text[5];
            else $post_content = $text[0];

            $post_content = $this->cleanText($post_content);
            $words = $this->getWords($post_content);
            $istoned = 0;
            while ($word = array_pop($words))
            {
                $first_base = $this->getFirstLemma($word);
                if (in_array($first_base, $tonedWords)) {
                    $istoned = 1;
                    //echo $first_base . "\n";
                }
            }

            if (!$istoned) {
                $this->corpora[$id][2] = 'f';
                //echo "F\n";
                continue;
            }
        }

    }


    function spellcheck()
    {
        $descriptorspec = array(
            0 => array("pipe", "r"), // stdin - канал, из которого дочерний процесс будет читать
            1 => array("pipe", "w"), // stdout - канал, в который дочерний процесс будет записывать
            2 => array("file", "error-output.txt", "w") // stderr - файл для записи
        );

        $cwd = '/spellcheck';
        $env = NULL;

        foreach ($this->corpora as $id => $text)
        {

            $spellwords = $this->getWords($text[0]);
            $tokenCount[$id] = count($spellwords);

            foreach ($spellwords as $word)
            {
                $spellstring[$id] .= $word . ' ';
            }
            print_r($spellstring);

        }

        //$env = array('some_option' => 'aeiou');

        $process = proc_open('aspell -a', $descriptorspec, $pipes, $cwd, $env);

        if (is_resource($process)) {
            // $pipes теперь выглядит так:
            // 0 => записывающий обработчик, подключенный к дочернему stdin
            // 1 => читающий обработчик, подключенный к дочернему stdout
            // Вывод сообщений об ошибках будет добавляться в /tmp/error-output.txt

            foreach ($spellstring as $id => $string)
            {
                fwrite($pipes[0], $string . "\n");
            }


            //fwrite($pipes[0], "рукэ n жопа головэ приложить\n");
            //fclose($pipes[0]);
            //fwrite($pipes[0], 'ногэ');
            fclose($pipes[0]);


            $result = stream_get_contents($pipes[1]);
            $result = explode("\n", $result);

            unset($result[0]);
            //print_r($result);


            fclose($pipes[1]);

            // Важно закрывать все каналы перед вызовом
            // proc_close во избежание мертвой блокировки
            $return_value = proc_close($process);

            echo "команда вернула $return_value\n";
        }

        foreach ($tokenCount as $id => $count)
        {
            $mistcount = 0;
            $mistakes[$id] = array_slice($result, 0, $count);
            array_splice($result, 0, $count + 1);

            foreach ($mistakes[$id] as $val)
            {
                $sub = substr($val, 0, 1);
                if ($sub == '&' || $sub == '#') $mistcount++;
            }

            $mistakespercent[$id] = $mistcount / $count;

        }

        print_r($mistakes);
        print_r($mistakespercent);

        foreach ($result as $res)
        {
            if (substr($res, 0, 1) == '&') echo "MISTAKE\n";
        }
    }


    public function countCorporaSentimentNaiveBayes($texts, $full)
    {
        global $db, $nb, $nbs;

        $poses = array('Г', 'ДЕЕПРИЧАСТИЕ', 'ИНФИНИТИВ', 'КР_ПРИЛ', 'КР_ПРИЧАСТИЕ', 'Н', 'П', 'ПРЕДК', 'ПРИЧАСТИЕ', 'С');

        foreach ($texts as $text)
        {
            $order_id = $text['order_id'];
            $post_id = $text['post_id'];
            if ($full && $text['ful_com_post'] != '') $text = $text['ful_com_post'];
            else $text = $text['post_content'];

            $string = '';
            $text = $this->cleanText($text);
            $words = $this->getWords($text);
            foreach ($words as $word)
            {
                $word_base = $this->getAllLemmas($word);
                //echo $word_base[1][0]." ".$word_base[0][0]." ";
                if (!in_array($word_base[1][0], $poses)) continue;
                $string .= $word_base[0][0] . " ";
            }
            //echo $string."<br><br>";

            $texts_processed[$post_id] = $string;


            //echo "<br>".$string."<br>";

        }

        foreach ($texts_processed as $id => $str)
        {

            // echo $str."<br>";
            $scores = $nb->categorize($str);
            //print_r($scores);
            //echo "<br><br>";
            if ($scores['NEG'] > $scores['POS'] /* && $scores['NEG']>$scores['NEU']*/) {
                //if ($scores['NEG'] > 0.9) $toneArr[$id]=0;
                //else
                $toneArr[$id] = -1;
            }
            elseif ($scores['POS'] > $scores['NEG'] /*&& $scores['POS']>$scores['NEU']*/)
            {
                //if ($scores['POS'] > 0.9) $toneArr[$id]=0;
                //else
                $toneArr[$id] = 1;
            }

        }

        //print_r($toneArr);
        return $toneArr;

    }

    public function countCorporaSentimentNaiveBayes2($full)
    {
        global $db;

        $poses = array('Г', 'ДЕЕПРИЧАСТИЕ', 'ИНФИНИТИВ', 'КР_ПРИЛ', 'КР_ПРИЧАСТИЕ', 'Н', 'П', 'ПРЕДК', 'ПРИЧАСТИЕ', 'С');
        $texts_processed = array();
        foreach ($this->corpora as $id => $text)
        {
            if ($text[2] === 'f') die();

            if ($full && $text[5] != '') $post_content = $text[5];
            else $post_content = $text[0];

            $string = '';
            $post_content = $this->cleanText($post_content);
            $words = $this->getWords($post_content);
            foreach ($words as $word)
            {
                $word_base = $this->getAllLemmas($word);
                //echo $word_base[1][0]." ".$word_base[0][0]." ";
                if (!in_array($word_base[1][0], $poses)) continue;
                $string .= $word_base[0][0] . " ";
            }
            //echo $string."<br><br>";
            $texts_processed[$id] = $string;
            //echo "<br>".$string."<br>";
        }


        $op = new NaiveBayes();
        $op->addToIndex('learn_NEG.txt', 'neg');
        $op->addToIndex('learn_POS.txt', 'pos');

        foreach ($texts_processed as $id => $str)
        {
            $class = $op->classify($str);
            if ($class == 'neg') {
                $this->corpora[$id][2] = -1;
            }
            elseif ($class == 'pos')
            {
                $this->corpora[$id][2] = 1;
            }

        }
        //print_r($this->corpora);

    }

    public function saveTone()
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
        $res = $this->db->query($sql);
        if (!$res) die("false");
        //$count = $db->fetch($res);
        //die("true");

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

    public function saveParentDuplicates()
    {
        foreach ($this->corpora as $id => $text)
        {
            if ($text[4] == '') continue;

            //TODO:UNCOMMENT PRODUCTION
            //$sql = "UPDATE blog_post SET parent = $text[4] WHERE post_id = $id AND order_id = $this->order_id";


            //echo $sql;
            //die();
            $res = $this->db->query($sql);
            //if (!$res) die ("сучка ошибка");

        }
    }

    public function duplicatesController()
    {
        //$this->saveHostsToFile();

        $this->hostsfile = "psbhosts.txt";
        $this->getHostsFromFile();
        $this->setCorporaHostThemes();


        $this->makeIndex();
        $this->groupNewsDuplicates();
        // $this->filterByNews();

        $this->saveParentDuplicates();
    }

    public function controller()
    {

        //$this->saveHostsToFile();
        //die();

        //TODO: сделать как фильтры


        //Угореть по последовательности, сделать проверки так чтобы не брались отфильтрованные или удалялись

echo "<pre>";
        echo count($this->corpora) . "========";
        $this->getHostsFromFile();
        // print_r($this->hosts);
        // die();

        $this->setCorporaHostThemes();

        echo "ТЕМЫ ПРОСТАВЛЕНЫ <br>";
                print_r($this->corpora);


        $this->makeIndex();
        $this->groupNewsDuplicates();
        $this->filterByNews();

        echo "Фильтры по новостям<br>";
        print_r($this->corpora);
        $this->cleanFiltered();
        /* */

        echo count($this->corpora);
        $this->filterByWords(1, 0);

        echo "Фильтры по словам<br>";
        print_r($this->corpora);
        $this->cleanFiltered();
        echo"========";
        echo count($this->corpora);
        echo "\n\n";
        $this->countCorporaSentimentNaiveBayes2(0);
        //$this->saveTone();

        print_r($this->corpora);
        die("OK");
        foreach ($this->corpora as $id => $text)
        {
            if ($text[4] != '') {
                echo $id . "\t";
                print_r($text);
            }
        }
    }

    public function hostController()
    {
        $this->getHostsFromFile();
        $this->getHostThemesFromFile();

    }

    public function spellController()
    {

        $this->spellcheck();
    }

    public function plotToBrowser()
    {
        foreach ($this->corpora as $id => $text)
        {
            $parentarr[$id] = $text[4];
        }

        arsort($parentarr);

        foreach ($parentarr as $id => $parent)
        {
            if ($id == $parent)
            echo "<span style=\"background:red\">p</span>";
            echo $id . " => parent:" . $parent . " | " . $this->corpora[$id][1];
            echo "<br>";
            echo $this->corpora[$id][0];
            echo "<hr>";
        }

    }

    public function emoticonsFilter()
    {
        //$pattern = "/[|<>]*[|:;8%=xXжЖ][-_oOоО0ЕE^,]*[|{LEFOfoSspPcC&\\/\[(]+|[(*]{2,}[\s?!]/isu";
        //OK! $pattern = "/([|><]*[|:;8%=xXжЖ]?[-_oOоО0^,']?[|{LEFOfoSspPcC&\\/\[()]+[\s?!.,])|[(!?]{2,}+/isu";
        //$pattern = "/[|><]{0,1}[|:;8%=xXжЖ]{1,1}[-_oOоО0^,']{0,1}[|{LEFOfoSspPcC&\\/\[(]+[\s?!.,]*/isu";
        //$pattern = "/[|><]?[|:;8%=xXжЖ]+[-_oOоО0^,']?[|{LEFoOоО0SspPcC&\\/\[(]+[!?.,]*[\s]+/isu";
        //$pattern = "/[|><]?[|:;8%=xXжЖ]+[-_oOоО0^,']?[|{LEFoOоО0SspPcC&\\/\[(]+[!?.,]*[\s]+/isu";
        //$pattern_neg_more = "/[\s]+[|><]?[8B][-_^,'~]?(o|O|о|О|[|{&\\/\[(])[\s]/isu";
        //$pattern_neg_more = "/[|><]?[8B][-_^,'~]?(o|O|о|О|[|{&\\/\[(]*)[\s]/isu";
        //$patternHARDCORE = "/[|><]?(([|:;8%=xXжЖ]+[-_oOоО^,']?[|{LEFoOоОSspPcC&\\/\[(]+[!?.,]*)|([|:;%=xXжЖ]+[-_oOоО0^,']?[|{LEFoOоО0SspPcC&\\/\[(]+[!?.,]*))[\s]+/isu";
        //$pattern = "/([|<>]*[|:;8%=xXжЖ][-_oOоО0^,']*[|{LEFOfoSspPcC&\\/\[(]+)[\s?!.,]+/isu";

        $urlpattern = "/(http|https|ftp)\://[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(:[a-zA-Z0-9]*)?/?([a-zA-Z0-9\-\._\?\,\'/\\\+&amp;%\$#\=~])*[^\.\,\)\(\s]/isu";
        $urlpattern2 = "/(https?:\/?\/?)?|(&quot;|&amp;|&gt;|&lt;|(&#[0-9]*;))/isu";
        $tagspattern =" /<.*?>/isu";
        $datepattern = "/[0-9]{1,2}[:][0-9]{2}/isu";

        $pattern_neg_common = "/[|><]?[|:;%=]+[-_oOоО0^,'~]?(L|E|F|o|O|о|О|0|S|s|p|P|c|C|[|{&\\/\[(])+[!?.,]*[\s]+/isu";
        $pattern_pos_common = "/[|><]?[|:;%=]+[-_oOоО0^,'~]?(D|d|д|Д|[}\])])+[!?.,]*[\s]+/isu";
        $pattern_neg_more = "/[\s][|><]?(([8][-_^,'~]?)|([BxXжЖхХ][-_^,'~]+))([oOоО]|(\|+|{+|&+|\\+|\/+|\[+|\(+|[pPрР]+))[\s]/isu";
        $pattern_pos_more = "/[\s][|><]?(([8][-_^,'~]?)|([BxXжЖхХ][-_^,'~]+))([дДdD]|(}+|\]+|\)+))[\s]/isu";
        $pattern_neg_long = "/[(]{2,}/isu";
        $pattern_pos_long = "/[)]{2,}/isu";
        $pattern_neu_long = "[!?]{2,}|[.]{4,}";

        echo "<pre>";

        $i = 0;
        foreach ($this->corpora as $id => $text)
        {
            //$countpos[$id] = 0;
            //$countneg[$id] = 0;

            $text[0]=$text[0]." ";
            $text[0] = mb_eregi_replace($urlpattern,'',$text[0]);
            $text[0] = preg_replace($urlpattern2,'',$text[0]);
            $text[0] = preg_replace($tagspattern,'',$text[0]);
            $text[0] = preg_replace($datepattern,'',$text[0]);

            if (preg_match_all($pattern_pos_more, $text[0]." ", $matches)) $countpos[$id]++;
            if (preg_match_all($pattern_pos_common, $text[0]." ", $matches)) $countpos[$id]++;
            if (preg_match_all($pattern_pos_long, $text[0]." ", $matches)) $countpos[$id]++;
            if (preg_match_all($pattern_neg_more, $text[0]." ", $matches)) $countneg[$id]++;
            if (preg_match_all($pattern_neg_common, $text[0]." ", $matches)) $countneg[$id]++;
            if (preg_match_all($pattern_neg_long, $text[0]." ", $matches)) $countneg[$id]++;
        }

        print_r($countneg);
        print_r($countpos);
        echo "FOUND: ".count($countneg)+count($countpos)."  / ALL: ".count($this->corpora)."\n";
    }

}

//$sent = new complexSentiment(736);
//$sent = new complexSentiment(759); 
//$sent->spellController();
//$sent->hostController();
//$sent->duplicatesController();
//$sent->controller();
//$sent->emoticonsFilter();

//$sent->plotToBrowser();

?>