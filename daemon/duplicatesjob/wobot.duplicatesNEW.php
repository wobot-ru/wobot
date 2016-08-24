<?php

error_reporting(0);

$root = '/var/www/daemon/';
require_once($root . 'com/config.php');
require_once($root . 'com/db.php');
require_once($root . 'sentimentjob/wobot.linguistics.php');
//require_once($root . 'sentimentjob/wobot.NaiveBayes.php');
require_once($root . 'duplicatesjob/duplicates/document_hash.php');
//require_once($root . 'duplicatesjob/duplicates/itemhashes_model.php');
$memcache = memcache_connect('localhost', 11211);

$db = new database();
$db->connect();

require_once('/var/www/bot/kernel.php');


class duplicates extends linguistics
{

    var $debug = '', $debug_count = 0;
    var $corpora = array( /*post_id, 0 - post_parent, 1 - post_filter*/);
    var $order_id;
    var $hash_array = array( /*[word_hash][order_id][post_id]*/),
    $fullhash_array = array( /*[fullhash][post_id]*/),
    $post_cache = array( /* [post_id][lenght, filter, time]*/);

    var $db;

    var $mstart = 0,
    $mend = 0,
    $lastprocessed = 0; //последний обработанный при условии что кол-во сообщений в отчете > 15000
    var $timearr = array();


    private function logDebug($msg)
    {
        return;
        if (is_array($msg)) {
            ob_start();
            print_r($msg);
            $msg = ob_get_contents();
            ob_end_clean();
        }
        $this->debug .= "$this->debug_count | $msg\n";
        $this->debug_count++;
    }


    function __construct($order_id, $start, $end)
    {
        parent::__construct();

        //$this->timearr['check'][$func] = microtime(true);
        // тело скрипта


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
            //$this->mstart = $start;
            //$this->mend = $end;
            echo "NO DATES\n";
            $this->getLastCheckedDate();
        }
        $this->getCorpora();

        $this->logDebug($this->corpora);

        //$this->getCorpora($order_id);
        //$this->hostfile = "hosts" . $this->order_id . ".txt";
    }


    private function countTime($func, $start)
    {
        if ($start) {
            $this->timearr['check'][$func] = microtime(true);
        }
        else
        {
            $this->timearr[$func] += microtime(true) - $this->timearr['check'][$func];

        }
        //echo 'Время выполнения скрипта: '.(microtime(true) - $this->timearr['start']).' сек.';
    }

    private function getCorpora( /*$order_id*/)
    {
        $this->checkConnection();
        $sql = "SELECT post_id, post_content, post_time, ful_com_post
                 FROM blog_post as p
                 LEFT JOIN blog_full_com as f ON p.post_id=f.ful_com_post_id
                 WHERE p.order_id={$this->order_id} 
                 AND post_time>" . $this->mstart . " AND post_time<" . ($this->mend + 86400) . "
                 AND parent=0
                 ORDER BY post_time ASC";


        //                 LIMIT 1000";
        /*post_host, host_theme
         * ful_com_post
         * LEFT JOIN blog_full_com AS f ON p.post_id=f.ful_com_post_id
                 LEFT JOIN blog_host AS h ON p.post_host=h.host_name
         * */
        echo $sql;

        $res = $this->db->query($sql);

        if (!$res) throw new Exception("Failed to get corpora from DB\n" . $sql . "\n\n");

        while ($text = $this->db->fetch($res))
        {
            $this->corpora[$text['post_id']] = array($text['post_content'].' '.$text['ful_com_post'],
                                                     '');
            $this->lastprocessed = $text['post_time'];
            //$text['post_id']);
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

    function updateHashes()
    {
        global $memcache;
        $finalarr = array();

        //fullhash / posthash
        //забираем хеши из базы

        $sql = "SELECT full_hash, post_cache FROM items_hashes_summary WHERE order_id={$this->order_id} LIMIT 1";

        $this->logDebug("SQL BEFORE UPDATE HASHES: $sql");
        $result = $this->db->query($sql);


        //die("ZHI=".$this->db->num_rows($result));
        //если есть для этого ордера
        if ($this->db->num_rows($result) > 0) {
            $row = $this->db->fetch($result);
            if ($row['full_hash'] != '') $fullhash_basearr = json_decode($row['full_hash'], true);
            if (is_array($this->fullhash_array) && is_array($fullhash_basearr)) {
                //TODO: сдесь поменять объединение
                //$finalfullhash = $this->fullhash_array + $fullhash_basearr;
                $finalfullhash = $this->array_merge_recursive_distinct($this->fullhash_array, $fullhash_basearr);
            }


            if ($row['post_cache'] != '') $postcache_basearr = json_decode($row['post_cache'], true);
            if (is_array($this->post_cache) && is_array($postcache_basearr)) {

                //$finalpostcache = $this->post_cache + $postcache_basearr;
                $finalpostcache = $this->array_merge_recursive_distinct($this->post_cache, $postcache_basearr);
            }

            $finalpostcache = json_encode($finalpostcache);
            $finalfullhash = json_encode($finalfullhash);

            $usql = "UPDATE items_hashes_summary SET  full_hash = '{$finalfullhash}', post_cache = '{$finalpostcache}' WHERE  order_id =  '{$this->order_id}'";
            $ures = $this->db->query($usql);

        }
        else
        {
            //die ("ZHI NET   ");
            $finalpostcache = json_encode($this->post_cache);
            $finalfullhash = json_encode($this->fullhash_array);
            $usql = "INSERT INTO items_hashes_summary (order_id,full_hash,post_cache) VALUES ({$this->order_id},'{$finalfullhash}', '{$finalpostcache}')";
            $ures = $this->db->query($usql);
        }

        $this->logDebug("SQL FOR UPDATE HASHES: $usql");

        if (!$ures) die("CANT SAVE JSON" . $usql);


        //хеши слов
        $this->countTime('update_hashes', 1);
        foreach ($this->hash_array as $id => $arr)
        {
            //TODO:тут оптимизировать (невозможно поидее)
            $isindb = intval($memcache->add('isset_duplicates_' . $id, '1', MEMCACHE_COMPRESSED, 86400));

            echo $isindb;


            if ($isindb) {
                $sql = "SELECT * FROM items_hashes_json WHERE word_hash = $id LIMIT 1";
                $res = $this->db->query($sql);
                $numrows = $this->db->num_rows($res);
            }
            else {$numrows = 1;}



            //
            if ($numrows > 0) {
                echo "U";
                //$row = $this->db->fetch($res);
                //if ($row['json'] != '') $basearr = json_decode($row['json'], true);
               // if (is_array($arr) && is_array($basearr)) {
                    //$this->countTime('plus_arrays', 1);
                    //TODO: дописать
                   // $finalarr = $arr + $basearr;
                    //$this->countTime('plus_arrays', 0);

                    $this->countTime('json_encode_arrays', 1);
                    $json = json_encode($arr);
                    $this->countTime('json_encode_arrays', 0);

                    $this->countTime('update_hashes', 1);
                    $usql = "UPDATE items_hashes_json SET  json = '{$json}' WHERE  word_hash =  '{$id}'";
                    $ures = $this->db->query($usql);
                    $this->countTime('update_hashes', 0);
                //}
            }
            else
            {
                echo "I";
                $finalarr = $arr;
                $this->countTime('json_encode_arrays', 1);
                $json = json_encode($arr);
                $this->countTime('json_encode_arrays', 0);

                $this->countTime('update_hashes', 1);
                $usql = "INSERT INTO items_hashes_json (word_hash, json) VALUES ('$id',  '$json')";
                $ures = $this->db->query($usql);
                $this->countTime('update_hashes', 0);
            }
            if (!$ures) die("CANT SAVE JSON" . $usql);
            $this->countTime('update_hashes', 0);
        }
    }

    function makeOrderIndex()
    {
        echo "start making indexes\n";

        $this->countTime('make_index_inmemory', 1);

        foreach ($this->corpora as $id => $text)
        {
            $hash = new Document_Hash($text[0]);

            //полные хеши
            $this->fullhash_array[$hash->docMD5][$id] = 0;

            //параметры поста (lenght/filter/time) - родительский|дочерний|новый
            //filter = p | c | 0
            $this->post_cache[$id] = array($hash->length, 0, 0);

            //хэщи слов в посте
            $hasharr = $hash->getCrc32array();
            foreach ($hasharr as $val)
            {
                //$this->hash_array[$val][$this->order_id][$id] = array(0, 0);
                $this->hash_array[$val][$this->order_id][$id] = 0;
                //в дальнейшем array (0,0) можно убрать, задумывалось для хранения параметров поста в кеше.
                //Вместо этого сделан отдельный массив с параметрами постов
            }

        }
        echo "end index\n";

        $this->logDebug($this->post_cache);
        $this->logDebug($this->hash_array);
        $this->logDebug($this->fullhash_array);

        $this->countTime('make_index_inmemory', 0);
    }


    function loadHashArray($hash)
    {
        $this->countTime('load_hash_array', 1);
        //TODO: если хэш уже загружен - не грузим
        //echo "=================loadhash============================\n";
        $this->countTime('load_hash_sql', 1);
        $sql = "SELECT * FROM items_hashes_json WHERE word_hash = '$hash' LIMIT 1";
        $res = $this->db->query($sql);
        $this->countTime('load_hash_sql', 0);

        if ($this->db->num_rows($res) > 0) {

            $this->logDebug("LOADING HASH ARRAY FROM DB: $hash");
            $row = $this->db->fetch($res);
            $arr = json_decode($row['json'], true);

            //print_r($arr);

            if (@count($arr[$this->order_id]) > 0) {
                //TODO: сделать нормальное плюсование
                //$this->hash_array[$hash][$this->order_id] = $this->hash_array[$hash][$this->order_id] + $arr[$this->order_id];
                $this->countTime('load_hash_array_merge', 1);
                foreach ($this->hash_array[$hash][$this->order_id] as $post_id)
                {
                    // print_r($arr[$this->order_id][$post_id]);
                    // die();
                    $arr[$this->order_id][$post_id] = array(0, 0);
                }
                $this->hash_array[$hash][$this->order_id] = $arr[$this->order_id];

                //$this->hash_array[$hash][$this->order_id] = $this->array_merge_recursive_distinct($this->hash_array[$hash][$this->order_id], $arr[$this->order_id]);
                $this->countTime('load_hash_array_merge', 0);
                //print_r($this->hash_array);

            }

        }
        elseif (count($this->hash_array[$hash][$this->order_id]) > 0) ;
        else die("WRONG HASH ID");
        $this->countTime('load_hash_array', 0);
        return $this->hash_array[$hash][$this->order_id];

    }


    function loadFullhash()
    {
        $this->countTime('load_fullhash', 1);

        //TODO: если хэш уже загружен - не грузим
        //echo "=================loadhash============================\n";
        $sql = "SELECT * FROM items_hashes_summary WHERE order_id = '{$this->order_id}' LIMIT 1";
        $res = $this->db->query($sql);

        if ($this->db->num_rows($res) > 0) {
            $row = $this->db->fetch($res);
            $arr = json_decode($row['full_hash'], true);


            $this->logDebug("TWO ARRAYS TO UNION. FIRS IS FROM DB:");
            $this->logDebug($arr);
            $this->logDebug("SECOND IS FROM MEM:");
            $this->logDebug($this->fullhash_array);

            $resarr = $this->array_merge_recursive_distinct($arr, $this->fullhash_array);
            //print_r($resarr); die();
            //$resarr = $arr + $this->fullhash_array;
            $this->fullhash_array = $resarr;
            //$this->fullhash_array = $this->fullhash_array + $arr;

            $arr = json_decode($row['post_cache'], true);
            //$this->post_cache = $this->array_merge_recursive_distinct($arr, $this->post_cache);
            $this->logDebug("TWO ARRAYS POSTCACHE TO UNION. FIRS IS FROM DB:");
            $this->logDebug($arr);
            $this->logDebug("SECOND POSTCACHE IS FROM MEM:");
            $this->logDebug($this->post_cache);

            $this->post_cache = $this->array_merge_recursive_distinct($arr, $this->post_cache);


            //return $arr[$this->order_id];

        }
        elseif (count($this->fullhash_array) > 0) { /*die ("FULLHASH ARRAY NOT EMPTY\n");*/
        }
        else
        {
            print_r($this->fullhash_array);
            // die("WRONG ORDER ID");
        }

        $this->logDebug("FULLHASH AFTER UNION");
        $this->logDebug($this->fullhash_array);
        $this->logDebug("POSTCACHE AFTER UNION");
        $this->logDebug($this->post_cache);

        $this->countTime('load_fullhash', 0);
    }


    function findDuplicates($id, $hash)
    {
        $this->countTime('find_duplicates', 1);
        //$this->fullhash_array = array();
        //$this->post_cache = array();
        //$this->hash_array = array();
        $matches = array();
        $idd = '';
        $ids = array();


        $hasharr = $hash->getCrc32array();


        //if ($hash->length < 1 && $hash->docMD5 != '') {return 0;}
        //if (!is_object($hash)) return 0;


        $this->countTime('full_duplicate_chech', 1);


        foreach ($this->fullhash_array[$hash->docMD5] as $key => $val)
        {
            if ($id == $key) continue;
            if ($this->post_cache[$key][1] != 0) continue;

            //$this->post_cache[$key][1] = 'f';
            //echo "FFFF\n";
            //print_r($this->post_cache[$key]); die();
            $ids[] = $key;

            $this->logDebug("FOUND FULL DUPLICATE $key FOR $id");

        }

        $this->logDebug("FOUND DUPLICATE IDS:");
        $this->logDebug($ids);


        //$ids = $this->fullhash_array[$hash->docMD5];
        //print_r($ids);
        //unset($ids[$id]);
        //die("IDS FULLHASH");

        $this->countTime('full_duplicate_chech', 0);

        //print_r($hash->_tokens);
        //в цикле проверяем текст на дубли

        $this->countTime('foreach_to_find_duplicates', 1);
        foreach ($hasharr as $val)
        {
            //загружаем из БД массив для данного слова и данного order_id

            $this->logDebug("LOADING HASH ARRAYS");
            $match = $this->loadHashArray($val);

            //для каждого post_id для этого слова
            foreach ($match as $post_id => $params)
            {
                //если post_id = текущий id - не считаем
                if ($post_id == $id) continue;
                //print_r($this->post_cache); die();
                //пропускаем уже размеченные элементы
                if ($this->post_cache[$post_id][1] != 0) { /*echo "S";*/
                    continue; /*print_r($this->post_cache[$post_id]);die;*/
                }
                //формируем массив всех совпадений для данного поста и всех слов
                @$matches[$post_id] += 1;

                //echo $m;
                //TODO: логика с датой и парентом
                /*
                * если парент уже есть - ставим тот же парент
                */
            }
        }

        $this->logDebug("FOUND MATCHES FOR FUZZY DUPLICATES");
        $this->logDebug($matches);

        //print_r($matches);
        //проверяем там где больше всего совпадений
        foreach ($matches as $post_id => $intersecs)
        {
            //забираем длину сообщения


            $this->countTime('select_lenght', 1);
            /*$result2 = $this->db->query("SELECT length FROM items_hashes_summary WHERE doc_id=$post_id AND order_id = $this->order_id LIMIT 1");
            $length = $this->db->fetch($result2);
            $length = $length['length'];
            */
            $length = $this->post_cache[$post_id][0];
            //$length = 10;
            $this->countTime('select_lenght', 0);

            $length = min($length, $hash->length);

            //echo "LENG="; print_r($length); echo "\n";
            //die();

            $similarity = ($intersecs / $length) * 100; // Similarity between 2 docs in percents
            if ($similarity > 99 && $length > 2) {
                //если совпадает то дочерний в дальнейшем фильтруем
                //$this->corpora[$post_id][2] = 'f';
                //все idшники подходящие по проценту собираем в массив
                $ids[] = $post_id;
                //TODO: убрали в countDuplicates
                //$this->post_cache[$post_id][1] = 'f';
            }
        }
        $this->countTime('foreach_to_find_duplicates', 0);


        //echo "\nFOR $id:";
        //print_r($ids);
        //в массиве $ids мы храним все дочерние id для данного поста!
        $this->countTime('find_duplicates', 0);

        $this->logDebug("FOUND FUZZY DUPLICATES");
        $this->logDebug($ids);

        if (count($ids) > 0) return $ids;
        return 0;


        //return $ids;


    }

    function findParent($id, $hash)
    {
        //return one id for parent
        //return 0 if no parent
        $fullparent = $this->fullhash_array[$hash->docMD5];

        //md5 => id1
        //        id2

        $nofull = 0;


        $this->logDebug("FULLPAERNT ARRAY:");
        $this->logDebug($fullparent);

        if (count($fullparent) == 1 && isset($fullparent[$id])) {

            $this->logDebug("FULLPAERNT ONLY SELF");
            $nofull = 1;
            //TODO: понять, нужно ли это условие
        }
        elseif (count($fullparent) > 1) {
            $this->logDebug("FULLPAERNT FOUND");
            foreach ($fullparent as $parent_id => $val)
            {
                $this->logDebug("PARENT ID: $parent_id, FLAG: {$this->post_cache[$parent_id][1]}, ID:$id");
                if ($this->post_cache[$parent_id][1] === 'p' && $parent_id != $id) {
                    $this->logDebug("ZBS");
                    //echo "Found parents for $id ($parent_id | {$this->post_cache[$parent_id][1]}):" . count($fullparent);
                    //print_r($fullparent);
                    //echo"\n====\n\n";
                    return $parent_id;
                }
                else
                {
                    continue;
                    $this->logDebug("ELSE");
                }

                /*
                if ($parent_id == $id)
                {
                    $this->logDebug("PARENT=ID");
                    continue;
                }
                elseif ($this->post_cache[$parent_id][1] != 'p')
                {
                    $this->logDebug("PARENT!=p");
                    continue;
                }
                elseif ($this->post_cache[$parent_id][1] != 0) {


                }
                else { //TODO: либо континью, либо return 0;

                    continue;
                }*/
            }
        }
        else die('find parent full error');

 

        $hasharr = $hash->getCrc32array();

        foreach ($hasharr as $val)
        {
            //загружаем из БД массив для данного слова и данного order_id

            $match = $this->loadHashArray($val);

            //для каждого post_id для этого слова
            foreach ($match as $post_id => $params)
            {

                if ($this->post_cache[$post_id][1] === 'p' && $post_id != $id) {
                    @$matches[$post_id] += 1;
                    $this->logDebug("NAYDENO FUZZY SOVPADENIE, PARID: $post_id FLAG: {$this->post_cache[$post_id][1]} ID:$id");
                    //echo "Found parents for $id ($parent_id | {$this->post_cache[$parent_id][1]}):" . count($fullparent);
                    //print_r($fullparent);
                    //echo"\n====\n\n";
                    //return $parent_id;
                }
                else
                {
                    continue;
                    $this->logDebug("ELSE CONTINUE IN FUZZY");
                }

                /*
                if ($post_id == $id) continue;
                elseif ($this->post_cache[$post_id][1] != 'p') continue;
                elseif ($this->post_cache[$post_id][1] != 0) {
                    echo "matches parents for $id ($post_id | {$this->post_cache[$post_id][1]}):";
                    echo"\n====\n\n";
                    //return $post_id;
                }
                else { //TODO: либо континью, либо return 0;
                    continue;
                }*/
                //сравниваем только с родителями
                //if ($this->post_cache[$post_id][1] != 'p') continue;
                //если post_id = текущий id - не считаем
                //if ($post_id == $id) continue;
                //print_r($this->post_cache); die();
                //формируем массив всех совпадений для данного поста и всех слов

            }
        }


        //проверяем там где больше всего совпадений
        if (!isset($matches)) return 0;
        $this->logDebug("MATCHES FOR FUZZY PARENT");

        $this->logDebug($matches);
        foreach ($matches as $post_id => $intersecs)
        {
            $length = $this->post_cache[$post_id][0];
            $length = min($length, $hash->length);
            $similarity = ($intersecs / $length) * 100; // Similarity between 2 docs in percents
            if ($similarity > 99 && $length > 2) {
                //первый совпавший возвращаем
                $this->logDebug("SIMILARITY");
                return $post_id;
                //если совпадает то дочерний в дальнейшем фильтруем
                //$this->corpora[$post_id][2] = 'f';
                //все idшники подходящие по проценту собираем в массив
                //$ids[] = $post_id;
                //TODO: убрали в countDuplicates
                //$this->post_cache[$post_id][1] = 'f';
            }
        }


        return 0;

    }

    function countDuplicates()
    {

        $this->logDebug("РАССЧЕТ ДУБЛЕЙ");
        $similar_text = $this->getSetting();
        //$similar_text = 85;


        foreach ($this->corpora as $id => $text)
        {


            $this->logDebug("==========COUNDDUP=================================================");

            //если уже у нас есть parent - то ниче не считаем, он самый ранний!

            //echo "========================count $id ======================================\n";
            if ($this->post_cache[$id][1] === 'f') {
                echo "C";
                $this->logDebug("TEXT IS CHILD:we are watching id=$id | {$text[0]}");

                continue;
            }
            elseif ($this->post_cache[$id][1] === 'p') {
                $this->logDebug("ERROR!! TEXT IS PARENT:we are watching id=$id | {$text[0]}");
                continue;
                //die("ORDER ERROR");
            }
            elseif ($this->post_cache[$id][1] === 0)
            {

                $this->logDebug("PROCESSING:we are watching id=$id | {$text[0]}");


                $hash = new Document_Hash($text[0]);

                if ($hash->length < 1 && $hash->docMD5 != '') {

                    //echo "пустой текст2\n";
                    $this->logDebug("ID:$id пустой текст ");
                    $this->corpora[$id][1] = $id;
                    $this->corpora[$id][2] = 'p';
                    $this->post_cache[$id][1] = 'p';
                    continue;

                }
                //if (!is_object($hash)) return 0;
                $this->countTime('find_parent', 1);
                $parent = 0;
                $parent = $this->findParent($id, $hash);


                $this->logDebug("parent = $parent");


                if ($parent != 0) {
                    $this->corpora[$id][1] = $parent;
                    $this->corpora[$id][2] = 'f';
                    $this->post_cache[$id][1] = 'f';
                    $this->logDebug("PARENT = $parent, ID = $id");
                    $this->logDebug("====================================================");
                    //echo "PARENT = $parent, ID = $id\n\n";
                    continue;
                }

                $this->countTime('find_parent', 0);
                //continue;


                $this->logDebug("\n===========FIND CHILDREN======================================");


                $dup = $this->findDuplicates($id, $hash);
                //$dup = $this->findDuplicates($id, $text[0]);
                //continue;

                if ($dup == 0) {
                    //TODO: переписать SAVE и GROUP на массив post_cache,
                    //TODO: сделать сохранение post_cache отдельно
                    $this->corpora[$id][1] = $id;
                    $this->corpora[$id][2] = 'p';

                    $this->post_cache[$id][1] = 'p';


                    $this->logDebug("DUPLICATES NOT FOUND");

                    //echo "DUP_EQ_0\n";
                    echo ".";
                    continue;
                }
                elseif (count($dup) > 0)
                {
                    //echo "FOUN_DUP\n";
                    //TODO: потом проверить как работает поиск парента и убрать 3 строчки
                    //TODO:(убрал 3 строчки выше)
                    $this->corpora[$id][1] = $id;
                    $this->corpora[$id][2] = 'p';
                    $this->post_cache[$id][1] = 'p';

                    foreach ($dup as $key => $id_child)
                    {
                        if ($this->post_cache[$id_child][1] === 'f') continue;
                        if ($this->post_cache[$id_child][1] === 'p') continue;

                        $this->corpora[$id_child][1] = $id;
                        //$this->corpora[$id][1] = $id;
                        $this->corpora[$id_child][2] = 'f';
                        $this->post_cache[$id_child][1] = 'f';
                        //print_r($dup);
                        //echo "======\n\n";
                        //continue;

                        /*
                        if ($this->corpora[$id_child][1] != '' && $this->corpora[$id_child][1] != $id_child) {
                            //echo "====уже был парент, текущий id={$this->corpora[$id][1]}== новый id={$this->corpora[$id_child][1]} \n\n";
                            $this->corpora[$id][1] = $this->corpora[$id_child][1];
                            $this->corpora[$id][2] = 'f';
                        }
                        else
                        {

                            $this->corpora[$id_child][1] = $id;
                            $this->corpora[$id][1] = $id;
                            $this->corpora[$id_child][2] = 'f';
                        }*/
                    }
                    echo "D";

                }
            }
            /*
            print_r($text);
            echo "\n======================== end ======================================\n";
            */
            $this->logDebug("===========================================================\n");
        }


        $this->countTime('count_duplicates', 0);
        //print_r($this->corpora);


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

            // echo 'SELECT post_nastr,post_spam,post_fav,post_tag FROM blog_post WHERE post_id='.$id.' LIMIT 1'."\n";
            $qparent=$this->db->query('SELECT post_nastr,post_spam,post_fav,post_tag FROM blog_post WHERE post_id='.$id.' LIMIT 1');
            $parent=$this->db->fetch($qparent);

            $sql = 'UPDATE blog_post SET parent = '.$text[1].',post_nastr='.$parent['post_nastr'].',post_spam='.$parent['post_spam'].',post_fav='.$parent['post_fav'].',post_tag=\''.$parent['post_tag'].'\' WHERE post_id = '.$id.' AND order_id = '.$this->order_id;
            //echo $sql;
            $res = $this->db->query($sql);
            if (!$res) die ("db error");
            //die();

        }
    }


    function groupDuplicates()
    {
        foreach ($this->corpora as $id => $text)
        {

            $duplicates[$text[1]][$id] = $text[0];
        }

        print_r($duplicates);
    }


    public function duplicatesControllerNew()
    {
        $this->checkConnection();
        $this->getCorpora();

        $this->checkConnection();
        echo "Индексируем сообщения\n";
        $this->makeOrderIndex();

        //Проверка на полный дубль
        $this->loadFullhash();

        //print_r($this->fullhash_array);
        //print_r($this->post_cache);


        echo "Считаем дубли\n";
        $this->countDuplicates();

        //die();

        //TODO:сделать лок тейблз

        $this->updateHashes();

        //print_r($this->fullhash_array);
        //print_r($this->post_cache);
        //                die();

        //$this->singleDuplicates(6904913);

        //Это для вывода
        //$this->groupDuplicates();

        //print_r($this->debug);
        //die();


        //die();
        echo "Сохраняем";
        $this->checkConnection();

        $this->saveParent();

        // print_r($this->corpora);

        $this->checkConnection();


        //$sql = "UPDATE blog_orders SET similar_text=" . @mktime(0, 0, 0, date("n"), date("j"), date("Y")) . " WHERE order_id={$this->order_id}";
        //TODO: ОШИБКА, т.к. у нас есть LIMIT, а тут мы ставим текущую дату!

        if ($this->lastprocessed==0) $this->lastprocessed = @mktime(0, 0, 0, date("n"), date("j"), date("Y"));

        $sql = "UPDATE blog_orders SET similar_text=".$this->lastprocessed." WHERE order_id={$this->order_id}";
        echo $sql;
        unset($this->timearr['check']);
        print_r($this->timearr);
        $this->db->query($sql);

        //$this->saveParentDuplicates();
    }

    public function duplicatesControllerOld()
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

    //TODO: переписать склейку под конкретную задачу или вообще считать заново

    /*
     * [make_index_inmemory] => 0.089658975601196
    [load_fullhash] => 0.060890197753906
    [load_hash_array] => 36.609844684601
    [load_hash_array_merge] => 32.448340415955
    [find_parent] => 24.499985694885
    [full_duplicate_chech] => 0.0064053535461426
    [select_lenght] => 1.9776525497437
    [foreach_to_find_duplicates] => 25.048705101013
    [find_duplicates] => 25.061225652695
    [count_duplicates] => 1365089046.2629
    [json_encode_arrays] => 0.020127773284912
    [update_hashes] => 0.87288045883179
    [plus_arrays] => 0.0018606185913086
     */
    private function array_merge_recursive_distinct(array &$array1, array &$array2)
    {

        $merged = $array1;

        foreach ($array2 as $key => &$value)
        {
            if (is_array($value) && isset ($merged [$key]) && is_array($merged [$key])) {
                $merged [$key] = $this->array_merge_recursive_distinct($merged [$key], $value);
            }
            else
            {
                $merged [$key] = $value;
            }
        }

        return $merged;
    }

    private function array_merge_hash()
    {

    }


    private function getLastCheckedDate()
    {
        $this->checkConnection();
        $sql = 'SELECT similar_text, order_start, order_end FROM blog_orders WHERE similar_text!=0 AND order_id=' . $this->order_id . ' LIMIT 1';

        //echo "!!!!!!!!\n\n\n\n";
        echo $sql . "\n";
        $res = $this->db->query($sql);
        // (!$res)
        $order = $this->db->fetch($res);
        if (empty($order)) die('DIE!EDDE!!!111');

        print_r($order);
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
        $this->mstart=$order['order_start'];
    }

    function getSetting()
    {
        $order = $this->db->query("SELECT order_id,order_settings FROM blog_orders WHERE order_id = {$this->order_id} ORDER BY order_id DESC");
        //echo 'SELECT order_id,order_settings FROM blog_orders WHERE order_id = {$this->order_id} ORDER BY order_id DESC';
        $orders = $this->db->fetch($order);
        //print_r($orders);
        $settings = json_decode($orders['order_settings'], true);
        //print_r($settings);
        //echo $settings['similar_text'];
        if (@$settings['similar_text'] < 50) @$settings['similar_text'] = 50;
        return $settings['similar_text'];
    }

}

/*
$dup = new Duplicates(712);
$dup->duplicatesControllerNew();
die();
*/


?>