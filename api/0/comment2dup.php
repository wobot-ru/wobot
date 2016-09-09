<?
require_once('/var/www/new/com/config.php');
require_once('/var/www/new/com/func.php');
require_once('/var/www/new/com/db.php');
require_once('/var/www/new/bot/kernel.php');
require_once('auth.php');
require_once('/var/www/com/loc.php');
require_once('/var/www/new/com/porter.php');
require_once( '/var/www/new/com/phpmorphy/src/common.php');

$dir = '/var/www/new/com/phpmorphy/dicts';
$lang = 'ru_RU';
$opts = array( 'storage' => PHPMORPHY_STORAGE_FILE );
$morphy = new phpMorphy($dir, $lang, $opts);

//error_reporting(0);

date_default_timezone_set('Europe/Moscow');
$db = new database();
$db->connect();

$redis = new Redis() or die("Can'f load redis module.");
$redis->connect('127.0.0.1');
//print_r($_SESSION);
ini_set("memory_limit", "2048M");
$word_stem = new Lingua_Stem_Ru();
//$msg=$word_stem->stem_word('бдбд');
//echo $msg;
//die();
//print_r($_POST);

function dubsort($a, $b)
{
    if ($a['countshortdup'] == $b['countshortdup']) {
        return 0;
    }
    return ($a['countshortdup'] < $b['countshortdup']) ? 1 : -1;
}

/*
 * order_id:1614
page:0
stime:18.09.2012
etime:18.10.2012
sort:null
positive:true
negative:true
neutral:true
post_type:null
md5:
perpage:100
Promotions:selected
words:selected
tags:selected
*/

// $_POST = $_GET;
//print_r($_POST);
auth();

// $user['tariff_id']=12;
// $user['user_id']=1187;
// $_POST['order_id']=2074;
// $_POST['stime']='01.10.2012';
// $_POST['etime']='14.11.2012';
// $_POST['positive']='true';
// $_POST['negative']='true';
// $_POST['neutral']='true';
// $_POST['perpage']=100;
// $_POST['sort']='dub';
//echo $loged;
//if (!$loged) die();
if ((!$loged) && ($user['tariff_id'] == 3)) die();
set_log('comment', $_POST);
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

//session_destroy();
//print_r($_SESSION);
if ($user['tariff_id'] == 3) {
    $user['user_id'] = 61;
}
if ($_POST['perpage'] == 'null') {
    $_POST['perpage'] = 10;
}
if (isset($_SESSION[$_POST['md5']])) {
    foreach ($_POST as $key => $item)
    {
        //echo substr($key, 0, 4).' ';
        if ((substr($key, 0, 4) == 'res_')) {
            $resorrr[] = str_replace("_", ".", substr($key, 4));
        }
        if (substr($key, 0, 4)=='cou_')
        {
            foreach ($wobot['destn3'] as $kdest => $idest)
            {
                if (str_replace('_',' ',substr($key,4))==$idest)
                $loc[]=$kdest;
            }
        }
        if (($key=='location') && ($item!='')) $loc=explode(',', $item);
        if (($key=='cou') && ($item!=''))
        {
            $mcou=explode(',', $item);
            foreach ($mcou as $kmcou => $imcou)
            {
                foreach ($wobot['destn3'] as $kdest => $idest)
                {
                    if ($imcou==$idest) $loc[]=$kdest;
                }
            }
        }
        if (($key=='res') && ($item!='')) $resorrr=explode(',', $item);
        if (($key=='shres') && ($item!='')) $short_resorrr=explode(',', $item);
        if ((substr($key, 0, 4) == 'loc_')) {
            if (isset($wobot['destn2'][str_replace('_', ' ', substr($key, 4))])) {
                $loc[] = str_replace('_', ' ', substr($key, 4));
            }
            if (substr($key, 4) == 'не_определено') {
                $loc[] = 'na';
            }
        }
        if ((substr($key, 0, 5) == 'tags_')) {
            $tags[] = str_replace("_", ".", substr($key, 5));
        }
        if ((substr($key, 0, 5) == 'word_')) {
            $word[] = str_replace("_", ".", substr($key, 5));
        }
        if ((substr($key, 0, 6) == 'speak_') && (substr($key, 7, 11) != 'link')) {
            $speak[str_replace("_", ".", substr($key, 6))] = $_POST['speak_link_' . str_replace("_", ".", substr($key, 6))];
            $speakid[str_replace("_", ".", substr($key, 6))] = 1; //$_POST['speak_link_'.str_replace("_",".",substr($key,6))];
        }
        if ((substr($key, 0, 5) == 'prom_') && (substr($key, 6, 10) != 'link')) {
            $prom[str_replace("_", ".", substr($key, 5))] = $_POST['prom_link_' . str_replace("_", ".", substr($key, 5))];
            $speakid[str_replace("_", ".", substr($key, 5))] = 1; //$_POST['speak_link_'.str_replace("_",".",substr($key,6))];
        }
    }
    //session_destroy();
    //print_r($_SESSION);
    $order_info = $db->query('SELECT order_id,order_name,order_keyword,order_start,order_end,order_last,third_sources,similar_text FROM blog_orders WHERE order_id=' . intval($_POST['order_id']) . ' LIMIT 1');
    $order = $db->fetch($order_info);
    // print_r($order);
    // echo 123;
    $orderkw = preg_replace('/[^а-яА-Яa-zA-Z0-9]/isu', ' ', $order['order_keyword']);
    $mkw = explode(' ', $orderkw);
    foreach ($mkw as $item)
    {
        if (mb_strlen($word_stem->stem_word($item), 'UTF-8') >= 3) {
            $yet[$word_stem->stem_word($item)] = 1;
        }
    }
    //$db->query('INSERT INTO azure_rss (rss_link) VALUES (\''.addslashes(json_encode($yet)).'\')');
    //print_r($yet);
    $metrics = json_decode($order['order_metrics'], true);
    $tags_info = $db->query('SELECT * FROM blog_tag WHERE order_id=' . intval($_POST['order_id']));
    while ($tg = $db->fetch($tags_info))
    {
        $d_tags[$tg['tag_tag']] = $tg['tag_name'];
        $d_astags[$tg['tag_name']] = $tg['tag_tag'];
    }

    if (intval($order['similar_text']) > 0) {
        if ($_POST['byparent'] > 0 && isset($_POST['byparent'])) {
            // $condition = 'AND p.parent='.$_POST['byparent'].' AND p.post_id!='.$_POST['byparent'].' ';
        }
        else
        {
            // $condition = 'AND p.parent=p.post_id';
        }
        $countdupquery = "SELECT post_id, parent, COUNT(*) as countd FROM blog_post WHERE order_id={$_POST['order_id']} GROUP BY parent ORDER BY parent, post_time DESC";
        $countdup = $db->query($countdupquery);

        while ($row = $db->fetch($countdup))
        {
            $dup[$row['parent']] = $row['countd'] - 1;
        }
        $countdupquery=preg_replace('/ORDER BY/isu',$condition.' ORDER BY',$_SESSION[$_POST['md5']]);;
        $countdupquery=preg_replace('/SELECT \*/isu', 'SELECT p.post_id, parent, COUNT( * ) AS countd', $countdupquery);
        $countdup = $db->query($countdupquery);
        // echo $countdupquery;
        // die();

        while ($row = $db->fetch($countdup))
        {
            if ($row['parent']!=0) $dupreal[$row['post_id']] = $row['countd'] - 1;
        }
    }
    else $condition = '';
    // print_r($dup);
    //print_R($_SESSION);
    if ($_POST['sort']!='dup') $posts = $db->query($_SESSION[$_POST['md5']] . $condition . ' LIMIT ' . ((intval($_POST['page'])) * ($_POST['perpage'])) . ',' . $_POST['perpage']);
    else $posts = $db->query(preg_replace('/ORDER BY/isu',$condition.' ORDER BY',$_SESSION[$_POST['md5']]));
    if ($db->num_rows($posts) == 0) {
        $cpp = $db->query(preg_replace('/SELECT \*/isu', 'SELECT p.post_id', $_SESSION[$_POST['md5']]));
        if (($db->num_rows($cpp) % $_POST['perpage']) == 0) {
            $_POST['page'] = intval($db->num_rows($cpp) / $_POST['perpage']) - 1;
        }
        else
        {
            $_POST['page'] = intval($db->num_rows($cpp) / $_POST['perpage']);
        }
        $posts = $db->query($_SESSION[$_POST['md5']] . ' LIMIT ' . ((intval($_POST['page'])) * ($_POST['perpage'])) . ',' . $_POST['perpage']);
    }
    $i = 1;
    $iw = 0;
    while ($post = $db->fetch($posts))
    {
        $mas[$i]['id'] = $post['post_id'];
        $mas[$i]['parent'] = $post['parent'];

        $mas[$i]['countshortdup'] = (isset($dup[$post['parent']])) ? $dupreal[$post['post_id']] : 0;
        $mas[$i]['countdup'] = (isset($dup[$post['parent']])) ? $dupreal[$post['post_id']].'/'.$dup[$post['parent']] : 0;
        if ($mas[$i]['countdup']=='0/0') $mas[$i]['countdup']=0;
        $parts = explode("\n", html_entity_decode(strip_tags($post['post_content']), ENT_QUOTES, 'UTF-8'));
        $mas[$i]['post'] = stripslashes(strip_tags(html_entity_decode(preg_replace('/\s+/is', ' ', $parts[0] != ''
                                                                                                    ? $parts[0]
                                                                                                    : ($parts[1] != ''
                                                                                                            ? $parts[1]
                                                                                                            : strip_tags($post['post_content']))), ENT_QUOTES, 'UTF-8'))); //preg_replace('/\s+/is',' ',strip_tags($post['post_content']));
        $mas[$i]['title'] = mb_substr(preg_replace('/\s+/is', ' ', strip_tags($post['post_content'])), 0, 140, 'UTF-8') . '...';
        foreach ($yet as $key => $item)
        {
            //echo '/[\s\t\"\'\?\:]('.$key.'.*?)[\s\t\"\'\?\:]/isu ';
            if (trim($key) != '') {
                $mas[$i]['post'] = preg_replace('/([\s\t\"\'\?\:\_\“\”\.\,\!\-\=\+\/\[\]\{\}\«\»\(\)\#\$\%\^\&\@])(' . $key . '[^\s\t\"\'\?\:\“\”\.\,\_\!\-\=\+\/\[\]\{\}\«\»\(\)\#\$\%\^\&\@]{0,3})([\s\t\"\'\?\:\“\”\.\,\!\-\=\+\/\[\]\{\}\«\»\(\)\#\$\%\^\&\@\_])/isu', '$1<span class="kwrd">$2</span>$3', ' ' . $mas[$i]['post'] . ' ');
                $mas[$i]['title'] = preg_replace('/([\s\t\"\'\?\:\_\“\”\.\,\!\-\=\+\/\[\]\{\}\«\»\(\)\#\$\%\^\&\@])(' . $key . '[^\s\t\"\'\?\:\“\”\.\,\_\!\-\=\+\/\[\]\{\}\«\»\(\)\#\$\%\^\&\@]{0,3})([\s\t\"\'\?\:\“\”\.\,\!\-\=\+\/\[\]\{\}\«\»\(\)\#\$\%\^\&\@\_])/isu', '$1<span class="kwrd">$2</span>$3', ' ' . $mas[$i]['title'] . ' ');
            }
        }
        if (trim($mas[$i]['post']) == '') {
            $mas[$i]['post'] = $post['post_link'];
            $mas[$i]['title'] = $post['post_link'];
        }
        $mas[$i]['title'] = trim($mas[$i]['title']);
        $mas[$i]['post'] = trim($mas[$i]['post']);
        if ((intval(date('H', $post['post_time'])) > 0) || (intval(date('i', $post['post_time'])) > 0)) $stime = date("H:i:s d.m.Y", $post['post_time']);
        else $stime = date("d.m.Y", $post['post_time']);
        $mas[$i]['time'] = $stime;
        $mas[$i]['url'] = $post['post_link'];
        if ($post['blog_link'] == 'vkontakte.ru') {
            if ($post['blog_login'][0] == '-') {
                $mas[$i]['auth_url'] = 'http://vk.com/club' . substr($post['blog_login'], 1);
            }
            else
            {
                $mas[$i]['auth_url'] = 'http://vk.com/id' . $post['blog_login'];
            }
        }
        elseif ($post['blog_link'] == 'facebook.com')
        {
            $mas[$i]['auth_url'] = 'http://facebook.com/' . $post['blog_login'];
        }
        elseif ($post['blog_link'] == 'twitter.com')
        {
            $mas[$i]['auth_url'] = 'http://twitter.com/' . $post['blog_login'];
        }
        elseif ($post['blog_link'] == 'livejournal.com')
        {
            $mas[$i]['auth_url'] = 'http://' . $post['blog_login'] . '.livejournal.com';
        }
        elseif (preg_match('/mail\.ru/isu', $post['blog_link']))
        {
            $mas[$i]['auth_url'] = 'http://blogs.' . $post['blog_link'] . '/' . $post['blog_login'];
        }
        elseif ($post['blog_link'] == 'liveinternet.ru')
        {
            $mas[$i]['auth_url'] = 'http://www.liveinternet.ru/users/' . $post['blog_login'];
        }
        elseif ($post['blog_link'] == 'ya.ru')
        {
            $mas[$i]['auth_url'] = 'http://' . $post['blog_login'] . '.ya.ru';
        }
        elseif ($post['blog_link'] == 'yandex.ru')
        {
            $mas[$i]['auth_url'] = 'http://' . $post['blog_login'] . '.ya.ru';
        }
        elseif ($post['blog_link'] == 'rutwit.ru')
        {
            $mas[$i]['auth_url'] = 'http://rutwit.ru/' . $post['blog_login'];
        }
        elseif ($post['blog_link'] == 'rutvit.ru')
        {
            $mas[$i]['auth_url'] = 'http://rutwit.ru/' . $post['blog_login'];
        }
        elseif ($post['blog_link'] == 'babyblog.ru')
        {
            $mas[$i]['auth_url'] = 'http://www.babyblog.ru/user/info/' . $post['blog_login'];
        }
        elseif ($post['blog_link'] == 'blog.ru')
        {
            $mas[$i]['auth_url'] = 'http://' . $post['blog_login'] . '.blog.ru/profile';
        }
        elseif ($post['blog_link'] == 'foursquare.com')
        {
            $mas[$i]['auth_url'] = 'https://ru.foursquare.com/' . $post['blog_login'];
        }
        elseif ($post['blog_link'] == 'kp.ru')
        {
            $mas[$i]['auth_url'] = 'http://blog.kp.ru/users/' . $post['blog_login'] . '/profile/';
        }
        elseif ($post['blog_link'] == 'aif.ru')
        {
            $mas[$i]['auth_url'] = 'http://blog.aif.ru/users/' . $post['blog_login'] . '/profile';
        }
        elseif ($post['blog_link'] == 'friendfeed.com')
        {
            $mas[$i]['auth_url'] = 'http://friendfeed.com/' . $post['blog_login'];
        }
        elseif ($post['blog_link'] == 'plus.google.com')
        {
            $mas[$i]['auth_url'] = 'https://plus.google.com/' . $post['blog_login'] . '/about';
        }
        $hn = parse_url($post['post_link']);
        $hn = $hn['host'];
        $ahn = explode('.', $hn);
        $hn = $ahn[count($ahn) - 2] . '.' . $ahn[count($ahn) - 1];
        $hh = $ahn[count($ahn) - 2];
        $mas[$i]['host'] = $hh;
        $mas[$i]['img_url'] = (!file_exists('/var/www/production/img/social/' . $hh . '.png'))
                ? './img/social/wobot_logo.gif' : './img/social/' . $hh . '.png';
        $mas[$i]['host_name'] = $hn;
        $mas[$i]['nick'] = html_entity_decode($post['blog_nick']);
        $mas[$i]['count_user'] = $metrics['speakers'][$post['blog_nick']];
        $mas[$i]['notes'] = $post['post_note_count'];
        $mas[$i]['is_read'] = $post['post_read'];
        $mas[$i]['nastr'] = $post['post_nastr'];
        $mas[$i]['spam'] = $post['post_spam'];
        $mas[$i]['fav'] = $post['post_fav'];
        $mas[$i]['eng'] = $post['post_engage'];
        $mas[$i]['adv_eng'] = json_decode($post['post_advengage'], true);
        $mas[$i]['foll'] = $post['blog_readers'];
        $mas[$i]['ico'] = $post['blog_ico'];
        $mas[$i]['geo'] = $wobot['destn1'][$post['blog_location']];
        $mas[$i]['geo_c'] = $wobot['destn3'][$wobot['destn1'][$post['blog_location']]];
        $mas[$i]['reaction']['reaction_content']=$post['reaction_content'];
        $mas[$i]['reaction']['reaction_time']=date("H:i:s d.m.Y",$post['reaction_time']);
        $mas[$i]['reaction']['reaction_blog_login']=$post['reaction_blog_login'];
        $mas[$i]['reaction']['reaction_blog_info']=json_decode($post['reaction_blog_info'],true);
        $t_post = explode(',', $post['post_tag']);
        $mas[$i]['tags'] = array();
        foreach ($t_post as $item)
        {
            if ($item != '') {
                $arr_t_post[$item] = $d_tags[$item];
                $mas[$i]['tags'] = $arr_t_post; //array(2=>'Главное',4=>'Офис',6=>'Отчет',7=>'Посмотреть');
            }
        }
        if (count($arr_t_post) != 0) {
            $mas[$i]['tags'] = $arr_t_post; //array(2=>'Главное',4=>'Офис',6=>'Отчет',7=>'Посмотреть');
        }
        else
        {
            $mas[$i]['tags'] = array();
        }
        $mas[$i]['gender'] = $post['blog_gender'];
        $mas[$i]['age'] = $post['blog_age'];
        $i++;
        unset($arr_t_post);
    }
    $i2=1;
    if (($_POST['sort']=='dup') && (intval($_POST['byparent'])==0))
    {
        usort($mas, 'dubsort');
        for ($i=$_POST['page']*$_POST['perpage'];$i<($_POST['page']+1)*$_POST['perpage'];$i++)
        {
            foreach ($mas[$i] as $key => $item)
            {
                $mas2[$i2][$key]=$item;
            }
            $i2++;
        }
        unset($mas);
        $mas=$mas2;
    }
    $mas['page'] = $_POST['page'];
    $mas['md5'] = $_POST['md5'];
    // $mas['md5_count_post']=$db->num_rows($cpp);
    $mas['md5_count_post']=$_SESSION['count_post_'.$_POST['md5']];
    $mas['md5_count_src']=$_SESSION['count_src_'.$_POST['md5']];
   // $_SESSION['count_post_'.$_POST['md5']]=$_SESSION['count_post_'.$_POST['md5']];
    // $_SESSION['count_src_'.$_POST['md5']]=$cnt_host;
}
else
{
    foreach ($_POST as $key => $item)
    {
        //echo substr($key, 0, 4).' ';
        if ((substr($key, 0, 4) == 'res_')) {
            $resorrr[] = str_replace("_", ".", substr($key, 4));
        }
        if (substr($key, 0, 4)=='cou_')
        {
            foreach ($wobot['destn3'] as $kdest => $idest)
            {
                if (str_replace('_',' ',substr($key,4))==$idest)
                $loc[]=$kdest;
            }
        }
        if (($key=='location') && ($item!='')) $loc=explode(',', $item);
        if (($key=='cou') && ($item!=''))
        {
            $mcou=explode(',', $item);
            foreach ($mcou as $kmcou => $imcou)
            {
                foreach ($wobot['destn3'] as $kdest => $idest)
                {
                    if ($imcou==$idest) $loc[]=$kdest;
                }
            }
        }
        if (($key=='res') && ($item!='')) $resorrr=explode(',', $item);
        if (($key=='shres') && ($item!='')) $short_resorrr=explode(',', $item);
        if ((substr($key, 0, 4) == 'loc_')) {
            if (isset($wobot['destn2'][str_replace('_', ' ', substr($key, 4))])) {
                $loc[] = str_replace('_', ' ', substr($key, 4));
            }
            if (substr($key, 4) == 'не_определено') {
                $loc[] = 'na';
            }
        }
        if ((substr($key, 0, 4) == 'tag_')) {
            //$tags[]=str_replace("_",".",substr($key,5));
            $tags[] = intval(substr($key, 4));
            //echo $key;
        }
        if ((substr($key, 0, 5) == 'word_')) {
            // $addjoin = 'LEFT JOIN blog_full_com as f ON f.ful_com_post_id=p.post_id';
            // $word[] = str_replace("_", ".", substr($key, 5));
            $addjoin='LEFT JOIN blog_full_com as f ON f.ful_com_post_id=p.post_id';
            $lem_words=$morphy->getAllFormsWithGramInfo(mb_strtoupper(mb_substr($key,5,mb_strlen($key,'UTF-8')-5,'UTF-8'),'UTF-8'), true);
            // $fp = fopen('logquery.txt', 'a');
            // fwrite($fp, mb_strtoupper(mb_substr($key,5,mb_strlen($key,'UTF-8')-5,'UTF-8'),'UTF-8').' '.json_encode($lem_words)."\n");
            // fclose($fp);
            foreach ($lem_words[0]['forms'] as $item_lem_words)
            {
                $word[]=mb_strtolower($item_lem_words,'UTF-8');
            }
            $word[]=mb_substr($key,5,mb_strlen($key,'UTF-8')-5,'UTF-8');
        }
        if ((substr($key, 0, 3) == 'mw_')) {
            $addjoin = 'LEFT JOIN blog_full_com as f ON f.ful_com_post_id=p.post_id';
            $word[] = str_replace("_", ".", substr($key, 3));
        }
        if ((substr($key, 0, 4) == 'mew_')) {
            $addjoin = 'LEFT JOIN blog_full_com as f ON f.ful_com_post_id=p.post_id';
            $eword[] = str_replace("_", ".", substr($key, 4));
        }
        if ((substr($key, 0, 6) == 'speak_') && (substr($key, 7, 11) != 'link')) {
            $speak[str_replace("_", ".", substr($key, 6))] = $_POST['speak_link_' . str_replace("_", ".", substr($key, 6))];
            $speakid[str_replace("_", ".", substr($key, 6))] = 1; //$_POST['speak_link_'.str_replace("_",".",substr($key,6))];
        }
        if ((substr($key, 0, 5) == 'prom_') && (substr($key, 6, 10) != 'link')) {
            $prom[str_replace("_", ".", substr($key, 5))] = $_POST['prom_link_' . str_replace("_", ".", substr($key, 5))];
            $speakid[str_replace("_", ".", substr($key, 5))] = 1; //$_POST['speak_link_'.str_replace("_",".",substr($key,6))];
        }
    }
    //print_r($word);
    //print_r($loc);
    $order_info = $db->query('SELECT * FROM blog_orders WHERE order_id=' . intval($_POST['order_id']) . ' LIMIT 1');
    $order = $db->fetch($order_info);
    $orderkw = preg_replace('/[^а-яА-Яa-zA-Z0-9]/isu', ' ', $order['order_keyword']);
    $mkw = explode(' ', $orderkw);
    foreach ($mkw as $item)
    {
        if (mb_strlen($word_stem->stem_word($item), 'UTF-8') >= 3) {
            $yet[$word_stem->stem_word($item)] = 1;
        }
    }
    $metrics = json_decode($order['order_metrics']);
    $tags_info = $db->query('SELECT * FROM blog_tag WHERE order_id=' . intval($_POST['order_id']));
    while ($tg = $db->fetch($tags_info))
    {
        $d_tags[$tg['tag_tag']] = $tg['tag_name'];
        $d_astags[$tg['tag_name']] = $tg['tag_tag'];
    }


    /*
    * TODO:
    * если в настройке дублей стоит 1 то группируем по дублям, если так и запрос get parent - то не группируем.
    * Все тоже самое только паренты
    * + поле COUNT - отдельный запрос
    * должны ли диапазоны дат влиять на количество дублей? - нет
    * добавить условие post_id = parent_id, сюдаже if (get parent - тогда условие where parent_id = and pid != parid
    * склеить или отдать отдельный массив ($mas[$i]['dupl'])
    *
    *
    */

    /* условие для запроса что группируем*/
    if (intval($order['similar_text']) > 0) {
        if ($_POST['byparent'] > 0 && isset($_POST['byparent'])) {
            $prev_post_id=$_POST['byparent'];
            $qparent=$db->query('SELECT * FROM blog_post WHERE order_id='.$_POST['order_id'].' AND post_id='.$_POST['byparent'].' LIMIT 1');
            $parent=$db->fetch($qparent);
            $_POST['byparent']=$parent['parent'];
            $condition = 'AND p.parent='.$_POST['byparent'].' AND p.post_id!='.$prev_post_id.' ';
        }
        else
        {
            // $condition = 'AND p.parent=p.post_id';
            // $condition=' AND p.parent!=0';
        }
        $countdupquery = "SELECT post_id, parent, COUNT(*) as countd FROM blog_post WHERE order_id={$_POST['order_id']} GROUP BY parent ORDER BY parent, post_time DESC";
        $countdup = $db->query($countdupquery);

        while ($row = $db->fetch($countdup))
        {
            if ($row['parent']!=0) $dup[$row['parent']] = $row['countd'] - 1;
        }
    }
    else $condition = '';


    //print_r($dup);
    //die();

    if (intval($_POST['byparent'])==0) $qw = 'SELECT *,p.post_id as post_id FROM blog_post as p LEFT JOIN robot_blogs2 as b ON p.blog_id=b.blog_id ' . $addjoin . ' WHERE p.order_id=' . $_POST['order_id'] . ' '.($addjoin==''?'':'AND f.ful_com_order_id='.$_POST['order_id']).' AND post_time>=' . strtotime($_POST['stime']) . ' AND post_time<' . (mktime(0, 0, 0, date('n', strtotime($_POST['etime'])), date('j', strtotime($_POST['etime'])) + 1, date('Y', strtotime($_POST['etime'])))) . ' '.$condition.' ';
    else $qw = 'SELECT *,p.post_id as post_id FROM blog_post as p LEFT JOIN robot_blogs2 as b ON p.blog_id=b.blog_id ' . $addjoin . ' WHERE p.order_id=' . $_POST['order_id'] . ' '.($addjoin==''?'':'AND f.ful_com_order_id='.$_POST['order_id']).' AND post_time>=' . strtotime($_POST['stime']) . ' AND post_time<' . (mktime(0, 0, 0, date('n', strtotime($_POST['etime'])), date('j', strtotime($_POST['etime'])) + 1, date('Y', strtotime($_POST['etime'])))) . ' '.$condition.' '; 
    if ($_POST['positive'] == 'true') {
        $mton[] = 1;
        //$qw.='AND (p.post_nastr=1';
    }
    if ($_POST['negative'] == 'true') {
        $mton[] = -1;
        //$qw.=' OR p.post_nastr=-1';
    }
    if ($_POST['neutral'] == 'true') {
        $mton[] = 0;
        //$qw.=' OR p.post_nastr=0)';
    }
    if ($_POST['undefined'] == 'true') {
        $mton[] = 2;
        //$qw.=' OR p.post_nastr=0)';
    }
    //print_r($mton);
    $or = '';
    if (count($mton) != 0) {
        $qw .= 'AND (';
        foreach ($mton as $item)
        {
            $qw .= $or . 'p.post_nastr=' . $item;
            $or = ' or ';
        }
        $qw .= ')';
    }
    if (strlen($wh1)) $wh .= '(' . $wh1 . ')';
    $or = '';
    if (!isset($_POST['hosts'])) $_POST['hosts']='selected';
    if (!isset($_POST['locations'])) $_POST['locations']='selected';
    if (((count($resorrr)!=0) || (count($short_resorrr)!=0)) && ($_POST['hosts']=='selected'))
    {
        $or='';
        $wh1=' AND (';
        foreach ($resorrr as $item)
        {
            $wh1.=$or.' p.post_host=\''.$item.'\'';
            $or=' OR ';
        } 
        foreach ($short_resorrr as $item)
        {
            if (trim($item)=='') continue;
            $wh1.=$or.' p.post_host LIKE \''.$item.'%\'';
            $or=' OR ';
        } 
        $wh1.=')';
        $qw.=$wh1;
    }
    elseif (((count($resorrr)!=0) || (count($short_resorrr)!=0)) && ($_POST['hosts']=='except'))
    {
        $or='';
        $wh1=' AND (';
        foreach ($resorrr as $item)
        {
            $wh1.=$or.' p.post_host!=\''.$item.'\'';
            $or=' AND ';
        } 
        foreach ($short_resorrr as $item)
        {
            if (trim($item)=='') continue;
            $wh1.=$or.' p.post_host NOT LIKE \''.$item.'%\'';
            $or=' AND ';
        } 
        $wh1.=')';
        $qw.=$wh1;
    }
    if ((count($loc)!=0) && ($_POST['locations']=='selected'))
    {
        $or='';
        $wh1=' AND (';
        foreach ($loc as $item)
        {
            if ($item=='na')
            {
                $wh1.=$or.' b.blog_location=\'\'';
                $or=' OR ';
            }
            else
            {
                if (isset($wobot['destn2'][$item]))
                {
                    $wh1.=$or.' b.blog_location=\''.$wobot['destn2'][$item].'\'';
                    $or=' OR ';
                }
            }
        } 
        $wh1.=')';
        $qw.=$wh1;
    }
    elseif ((count($loc)!=0) && ($_POST['locations']=='except'))
    {
        $or='';
        $wh1=' AND (';
        foreach ($loc as $item)
        {
            if ($item=='na')
            {
                $wh1.=$or.' b.blog_location!=\'\'';
                $or=' AND ';
            }
            else
            {
                if (isset($wobot['destn2'][$item]))
                {
                    $wh1.=$or.' b.blog_location!=\''.$wobot['destn2'][$item].'\'';
                    $or=' AND ';
                }
            }
        } 
        $wh1.=')';
        $qw.=$wh1;
    }
    $or = '';
    if (count($tags) != 0) {
        if ($_POST['tags'] != 'all') {
            $wh1 .= ' AND (';
            foreach ($tags as $item)
            {
                if ($_POST['tags'] == 'selected') {
                    $wh1 .= $or . '(FIND_IN_SET(\'' . $item . '\',post_tag)>0)';
                    $or = ' OR ';
                }
                else
                    if ($_POST['tags'] == 'except') {
                        $wh1 .= $or . '(FIND_IN_SET(\'' . $item . '\',post_tag)=0)';
                        $or = ' AND ';
                    }
                //if ($item!='без_тегов')
                //{
                //$wh.=$or.'(FIND_IN_SET(\''.$d_astags[$item].'\',post_tag)>0)';
                //}
                //else
                /*{
                        $wh.=$or.'(post_tag = \'\')';
                        $or=' OR ';
                    }*/
            }
            $wh1 .= ')';
            $qw .= $wh1;
        }
    }
    switch ($_POST['post_type']) {
        case 'fav':
            $qw .= ' AND (p.post_fav=1)';
            break;
        case 'nospam':
            $qw .= ' AND (p.post_spam!=1)';
            break;
        case 'spam':
            $qw .= ' AND (p.post_spam=1)';
            break;
    }
    if (intval($_POST['post_read']) == 1 && $_POST['post_read']!='') $qw .= ' AND p.post_read=1 ';
    if (intval($_POST['post_read']) == 0 && $_POST['post_read']!='') $qw .= ' AND p.post_read=0 ';
    if (intval($_POST['post_imp']) == 1 && $_POST['post_imp']!='') $qw .= ' AND p.post_fav2=1 ';
    if (intval($_POST['post_imp']) == 0 && $_POST['post_imp']!='') $qw .= ' AND p.post_fav2=0 ';
    /*$or='';
     if (count($speak)!=0)
     {
         $qw.=' AND (';
         foreach ($speak as $key => $item)
         {
             if ($_POST['Speakers']=='selected')
             {
                 $qw.=$or.'(b.blog_nick=\''.$key.'\' AND b.blog_link=\''.$item.'\')';
                 $or=' OR ';
             }
             else
             if ($_POST['Speakers']=='except')
             {
                 $qw.=$or.'(b.blog_nick!=\''.$key.'\' AND b.blog_link!=\''.$item.'\')';
                 $or=' AND ';
             }
         }
         $qw.=')';
     }*/
    $or = '';
    if ((count($speakid) != 0) && ($_POST['Promotions'] != 'all')) {
        $qw .= ' AND (';
        foreach ($speakid as $key => $item)
        {
            if ($key != 0) {
                if ($_POST['Promotions'] == 'selected') {
                    //$qw.=$or.'(b.blog_nick=\''.$key.'\' AND b.blog_link=\''.$item.'\')';
                    $qw .= $or . '(b.blog_id=\'' . $key . '\')';
                    $or = ' OR ';
                }
                else
                    if ($_POST['Promotions'] == 'except') {
                        $qw .= $or . '(IFNULL(b.blog_id,0)!=\'' . $key . '\')';
                        $or = ' AND ';
                    }
            }
        }
        $qw .= ')';
    }
    /*$or = '';
    if (count($word) != 0) {
        $qw .= ' AND (';
        foreach ($word as $key => $item)
        {
            $qw .= $or . '(LOWER(p.post_content) REGEXP "[ ,\.\!\?\{\}\'\"\\\|\>\<\(\)\*\&\^\%\$\#\@\=\+\-\_\n\«\»\№\±\§\/\;\:\←\€\£\¥\©\®\™\±\≠\≤\≥\÷\×\ј\џ\ќ\®\†\њ\ѓ\ѕ\ў\‘\“\ƒ\ћ\÷\©\}\°\љ\∆\…\э\ё\ђ\≈\≠\µ\и\™\~\≤\≤\≤\≥\“\”\”\’0-9]' . $item . '[ ,\.\!\?\{\}\'\"\\\|\>\<\(\)\*\&\^\%\$\#\@\=\+\-\_\n\«\»\№\±\§\/\;\:\←\€\£\¥\©\®\™\±\≠\≤\≥\÷\×\ј\џ\ќ\®\†\њ\ѓ\ѕ\ў\‘\“\ƒ\ћ\÷\©\}\°\љ\∆\…\э\ё\ђ\≈\≠\µ\и\™\~\≤\≤\≤\≥\“\”\”\’0-9]" OR p.post_content LIKE "' . $item . '%")';
            $qw .= ' OR (LOWER(f.ful_com_post) REGEXP "[ ,\.\!\?\{\}\'\"\\\|\>\<\(\)\*\&\^\%\$\#\@\=\+\-\_\n\«\»\№\±\§\/\;\:\←\€\£\¥\©\®\™\±\≠\≤\≥\÷\×\ј\џ\ќ\®\†\њ\ѓ\ѕ\ў\‘\“\ƒ\ћ\÷\©\}\°\љ\∆\…\э\ё\ђ\≈\≠\µ\и\™\~\≤\≤\≤\≥\“\”\”\’0-9]' . $item . '[ ,\.\!\?\{\}\'\"\\\|\>\<\(\)\*\&\^\%\$\#\@\=\+\-\_\n\«\»\№\±\§\/\;\:\←\€\£\¥\©\®\™\±\≠\≤\≥\÷\×\ј\џ\ќ\®\†\њ\ѓ\ѕ\ў\‘\“\ƒ\ћ\÷\©\}\°\љ\∆\…\э\ё\ђ\≈\≠\µ\и\™\~\≤\≤\≤\≥\“\”\”\’0-9]" OR f.ful_com_post LIKE "' . $item . '%")';
            $or = ' OR ';
        }
        $qw .= ')';
    }
    $or = '';
    if (count($eword) != 0) {
        $qw .= ' AND (';
        foreach ($eword as $key => $item)
        {
            $qw .= $or . '(LOWER(p.post_content) NOT REGEXP "[ ,\.\!\?\{\}\'\"\\\|\>\<\(\)\*\&\^\%\$\#\@\=\+\-\_\n\«\»\№\±\§\/\;\:\←\€\£\¥\©\®\™\±\≠\≤\≥\÷\×\ј\џ\ќ\®\†\њ\ѓ\ѕ\ў\‘\“\ƒ\ћ\÷\©\}\°\љ\∆\…\э\ё\ђ\≈\≠\µ\и\™\~\≤\≤\≤\≥\“\”\”\’0-9]' . $item . '[ ,\.\!\?\{\}\'\"\\\|\>\<\(\)\*\&\^\%\$\#\@\=\+\-\_\n\«\»\№\±\§\/\;\:\←\€\£\¥\©\®\™\±\≠\≤\≥\÷\×\ј\џ\ќ\®\†\њ\ѓ\ѕ\ў\‘\“\ƒ\ћ\÷\©\}\°\љ\∆\…\э\ё\ђ\≈\≠\µ\и\™\~\≤\≤\≤\≥\“\”\”\’0-9]" AND p.post_content NOT LIKE "' . $item . '%")';
            $qw .= ' AND (LOWER(f.ful_com_post) NOT REGEXP "[ ,\.\!\?\{\}\'\"\\\|\>\<\(\)\*\&\^\%\$\#\@\=\+\-\_\n\«\»\№\±\§\/\;\:\←\€\£\¥\©\®\™\±\≠\≤\≥\÷\×\ј\џ\ќ\®\†\њ\ѓ\ѕ\ў\‘\“\ƒ\ћ\÷\©\}\°\љ\∆\…\э\ё\ђ\≈\≠\µ\и\™\~\≤\≤\≤\≥\“\”\”\’0-9]' . $item . '[ ,\.\!\?\{\}\'\"\\\|\>\<\(\)\*\&\^\%\$\#\@\=\+\-\_\n\«\»\№\±\§\/\;\:\←\€\£\¥\©\®\™\±\≠\≤\≥\÷\×\ј\џ\ќ\®\†\њ\ѓ\ѕ\ў\‘\“\ƒ\ћ\÷\©\}\°\љ\∆\…\э\ё\ђ\≈\≠\µ\и\™\~\≤\≤\≤\≥\“\”\”\’0-9]" AND f.ful_com_post NOT LIKE "' . $item . '%")';
            $or = ' AND ';
        }
        $qw .= ')';
    }*/
    $or='';
    if ((count($word)!=0) && ($_POST['words']!='all'))
    {
        $qw.=' AND (';
        foreach ($word as $key => $item)
        {
            if ($_POST['words']=='selected')
            {
                //$qw.=$or.'(b.blog_nick=\''.$key.'\' AND b.blog_link=\''.$item.'\')';
                $qw.=$or.'(LOWER(p.post_content) REGEXP "[ ,\.\!\?\{\}\'\"\\\|\>\<\(\)\*\&\^\%\$\#\@\=\+\-\_\n\«\»\№\±\§\/\;\:\←\€\£\¥\©\®\™\±\≠\≤\≥\÷\×\ј\џ\ќ\®\†\њ\ѓ\ѕ\ў\‘\“\ƒ\ћ\÷\©\}\°\љ\∆\…\э\ё\ђ\≈\≠\µ\и\™\~\≤\≤\≤\≥\“\”\”\’0-9]'.$item.'[ ,\.\!\?\{\}\'\"\\\|\>\<\(\)\*\&\^\%\$\#\@\=\+\-\_\n\«\»\№\±\§\/\;\:\←\€\£\¥\©\®\™\±\≠\≤\≥\÷\×\ј\џ\ќ\®\†\њ\ѓ\ѕ\ў\‘\“\ƒ\ћ\÷\©\}\°\љ\∆\…\э\ё\ђ\≈\≠\µ\и\™\~\≤\≤\≤\≥\“\”\”\’0-9]" OR p.post_content LIKE "'.$item.'%" OR p.post_content LIKE "%'.$item.'")';
                $qw.=' OR (LOWER(f.ful_com_post) REGEXP "[ ,\.\!\?\{\}\'\"\\\|\>\<\(\)\*\&\^\%\$\#\@\=\+\-\_\n\«\»\№\±\§\/\;\:\←\€\£\¥\©\®\™\±\≠\≤\≥\÷\×\ј\џ\ќ\®\†\њ\ѓ\ѕ\ў\‘\“\ƒ\ћ\÷\©\}\°\љ\∆\…\э\ё\ђ\≈\≠\µ\и\™\~\≤\≤\≤\≥\“\”\”\’0-9]'.$item.'[ ,\.\!\?\{\}\'\"\\\|\>\<\(\)\*\&\^\%\$\#\@\=\+\-\_\n\«\»\№\±\§\/\;\:\←\€\£\¥\©\®\™\±\≠\≤\≥\÷\×\ј\џ\ќ\®\†\њ\ѓ\ѕ\ў\‘\“\ƒ\ћ\÷\©\}\°\љ\∆\…\э\ё\ђ\≈\≠\µ\и\™\~\≤\≤\≤\≥\“\”\”\’0-9]" OR f.ful_com_post LIKE "'.$item.'%" OR f.ful_com_post LIKE "%'.$item.'")';
                $or=' OR ';
            }
            else
            if ($_POST['words']=='except')
            {
                $qw.=$or.'(LOWER(p.post_content) NOT REGEXP "[ ,\.\!\?\{\}\'\"\\\|\>\<\(\)\*\&\^\%\$\#\@\=\+\-\_\n\«\»\№\±\§\/\;\:\←\€\£\¥\©\®\™\±\≠\≤\≥\÷\×\ј\џ\ќ\®\†\њ\ѓ\ѕ\ў\‘\“\ƒ\ћ\÷\©\}\°\љ\∆\…\э\ё\ђ\≈\≠\µ\и\™\~\≤\≤\≤\≥\“\”\”\’0-9]'.$item.'[ ,\.\!\?\{\}\'\"\\\|\>\<\(\)\*\&\^\%\$\#\@\=\+\-\_\n\«\»\№\±\§\/\;\:\←\€\£\¥\©\®\™\±\≠\≤\≥\÷\×\ј\џ\ќ\®\†\њ\ѓ\ѕ\ў\‘\“\ƒ\ћ\÷\©\}\°\љ\∆\…\э\ё\ђ\≈\≠\µ\и\™\~\≤\≤\≤\≥\“\”\”\’0-9]" AND p.post_content NOT LIKE "'.$item.'%" AND p.post_content NOT LIKE "%'.$item.'")';
                $qw.=' AND (LOWER(f.ful_com_post) NOT REGEXP "[ ,\.\!\?\{\}\'\"\\\|\>\<\(\)\*\&\^\%\$\#\@\=\+\-\_\n\«\»\№\±\§\/\;\:\←\€\£\¥\©\®\™\±\≠\≤\≥\÷\×\ј\џ\ќ\®\†\њ\ѓ\ѕ\ў\‘\“\ƒ\ћ\÷\©\}\°\љ\∆\…\э\ё\ђ\≈\≠\µ\и\™\~\≤\≤\≤\≥\“\”\”\’0-9]'.$item.'[ ,\.\!\?\{\}\'\"\\\|\>\<\(\)\*\&\^\%\$\#\@\=\+\-\_\n\«\»\№\±\§\/\;\:\←\€\£\¥\©\®\™\±\≠\≤\≥\÷\×\ј\џ\ќ\®\†\њ\ѓ\ѕ\ў\‘\“\ƒ\ћ\÷\©\}\°\љ\∆\…\э\ё\ђ\≈\≠\µ\и\™\~\≤\≤\≤\≥\“\”\”\’0-9]" AND f.ful_com_post NOT LIKE "'.$item.'%" AND f.ful_com_post NOT LIKE "%'.$item.'")';
                $or=' AND ';
            }
        }
        $qw.=')';
    }
    if ($_POST['gender'] == '2') {
        $qw .= ' AND b.blog_gender=2';
    }
    else
        if ($_POST['gender'] == '1') {
            $qw .= ' AND b.blog_gender=1';
        }
    if ($_POST['age_min'] != null) {
        $qw .= ' b.blog_age>' . intval($_POST['age_min']);
    }
    if ($_POST['age_max'] != null) {
        $qw .= ' b.blog_age<' . intval($_POST['age_max']);
    }
    if ((count($word) != 0) && ($_POST['words'] != 'all')) {
        /*switch ($_POST['sort']) {
              case 'date':
                  $qw.=' ORDER BY p.post_time DESC';
                  break;
              case 'audience':
                  $qw.=' ORDER BY b.blog_readers DESC';
                  break;
              case 'eng':
                  $qw.=' ORDER BY p.post_engage DESC';
                  break;
              default:
                 $qw.=' ORDER BY p.post_time DESC';
          }*/
        switch ($_POST['sort']) {
            case 'date':
                $qw .= (intval($_POST['byparent'])==0?'GROUP BY parent':'').' ORDER BY p.post_time DESC LIMIT ' . $_POST['perpage'];
                break;
            case 'audience':
                $qw .= (intval($_POST['byparent'])==0?'GROUP BY parent':'').' ORDER BY b.blog_readers DESC LIMIT ' . $_POST['perpage'];
                break;
            case 'eng':
                $qw .= (intval($_POST['byparent'])==0?'GROUP BY parent':'').' ORDER BY p.post_engage DESC LIMIT ' . $_POST['perpage'];
                break;
            case 'dup':
                $qw .= (intval($_POST['byparent'])==0?'GROUP BY parent':'').' ORDER BY p.post_time DESC';
                break;
            default:
                $qw .= (intval($_POST['byparent'])==0?'GROUP BY parent':'').' ORDER BY p.post_time DESC LIMIT ' . $_POST['perpage'];
        }
    }
    else
    {
        switch ($_POST['sort']) {
            case 'date':
                $qw .= (intval($_POST['byparent'])==0?'GROUP BY parent':'').' ORDER BY p.post_time DESC LIMIT ' . $_POST['perpage'];
                break;
            case 'audience':
                $qw .= (intval($_POST['byparent'])==0?'GROUP BY parent':'').' ORDER BY b.blog_readers DESC LIMIT ' . $_POST['perpage'];
                break;
            case 'eng':
                $qw .= (intval($_POST['byparent'])==0?'GROUP BY parent':'').' ORDER BY p.post_engage DESC LIMIT ' . $_POST['perpage'];
                break;
            case 'dup':
                $qw .= (intval($_POST['byparent'])==0?'GROUP BY parent':'').' ORDER BY p.post_time DESC';
                break;
            default:
                $qw .= (intval($_POST['byparent'])==0?'GROUP BY parent':'').' ORDER BY p.post_time DESC LIMIT ' . $_POST['perpage'];
        }
    }
    if ($_POST['sort']=='dup') 
    {

        arsort($dup[$row['post_id']]);
    }
    //$qw.=' ORDER BY p.post_time DESC LIMIT 10';
    //print_r($tags);
    // echo $qw;
    // die();
    $fp = fopen('log_exp.txt', 'a');
    fwrite($fp, $qw."\n");
    fclose($fp);

    $posts = $db->query($qw);
    $i = 1;
    if ($_POST['byparent']==0)
    {
        $countdupquery=preg_replace('/SELECT \*/isu', 'SELECT p.post_id, parent, COUNT( * ) AS countd', $qw);
        $countdupquery=preg_replace('/LIMIT.*/isu', '', $countdupquery);
        $countdup = $db->query($countdupquery);
        // echo $countdupquery;
        // die();

        while ($row = $db->fetch($countdup))
        {
            if ($row['parent']!=0) $dupreal[$row['post_id']] = $row['countd'] - 1;
        }
    }
    // print_r($dupreal);
    // die();
    // echo $qw;
    // die();
    //print_r($word);
    //die();
    while ($post = $db->fetch($posts))
    {
        //echo $post['post_id'].'|';
        //echo $c.' ';
        $mas[$i]['id'] = $post['post_id'];
        $mas[$i]['parent'] = $post['parent'];

        $mas[$i]['countshortdup'] = (isset($dup[$post['parent']])) ? $dupreal[$post['post_id']] : 0;
        $mas[$i]['countdup'] = (isset($dup[$post['parent']])) ? $dupreal[$post['post_id']].'/'.$dup[$post['parent']] : 0;
        if ($mas[$i]['countdup']=='0/0') $mas[$i]['countdup']=0;
        //$mas[$i]['countdup']=$dup[$post['post_id']];

        $parts = explode("\n", html_entity_decode(strip_tags($post['post_content']), ENT_QUOTES, 'UTF-8'));
        $mas[$i]['post'] = stripslashes(strip_tags(html_entity_decode(preg_replace('/\s+/is', ' ', $parts[0] != ''
                                                                                                    ? $parts[0]
                                                                                                    : ($parts[1] != ''
                                                                                                            ? $parts[1]
                                                                                                            : strip_tags($post['post_content']))), ENT_QUOTES, 'UTF-8'))); //preg_replace('/\s+/is',' ',strip_tags($post['post_content']));
        $mas[$i]['title'] = mb_substr(preg_replace('/\s+/is', ' ', strip_tags($post['post_content'])), 0, 140, 'UTF-8') . '...';
        foreach ($yet as $key => $item)
        {
            //echo '/[\s\t\"\'\?\:]('.$key.'.*?)[\s\t\"\'\?\:]/isu ';
            if (trim($key) != '') {
                $mas[$i]['title'] = preg_replace('/([\s\t\"\'\?\:\“\”\.\,\!\-\=\+\/\[\]\{\}\«\»\(\)\#\$\%\^\&\@\_])(' . $key . '[^\s\t\"\'\?\:\“\”\.\,\!\-\=\+\/\[\]\{\}\«\»\(\)\#\$\%\^\&\@\_]{0,3})([\s\t\"\'\?\:\“\”\.\,\!\-\=\+\/\[\]\{\}\«\»\(\)\#\$\%\^\&\@\_])/isu', '$1<span class="kwrd">$2</span>$3', ' ' . $mas[$i]['title'] . ' ');
                $mas[$i]['post'] = preg_replace('/([\s\t\"\'\?\:\“\”\.\,\!\-\=\+\/\[\]\{\}\«\»\(\)\#\$\%\^\&\@\_])(' . $key . '[^\s\t\"\'\?\:\“\”\.\,\!\-\=\+\/\[\]\{\}\«\»\(\)\#\$\%\^\&\@\_]{0,3})([\s\t\"\'\?\:\“\”\.\,\!\-\=\+\/\[\]\{\}\«\»\(\)\#\$\%\^\&\@\_])/isu', '$1<span class="kwrd">$2</span>$3', ' ' . $mas[$i]['post'] . ' ');
            }
        }
        if (trim($mas[$i]['title']) == '') {
            $mas[$i]['title'] = $post['post_link'];
            $mas[$i]['post'] = $post['post_link'];
        }
        $mas[$i]['title'] = trim($mas[$i]['title']);
        $mas[$i]['post'] = trim($mas[$i]['post']);
        //$mas[$i]['title']=preg_replace('/\s+/is',' ',strip_tags($post['post_content']));
        if ((intval(date('H', $post['post_time'])) > 0) || (intval(date('i', $post['post_time'])) > 0)) $stime = date("H:i:s d.m.Y", $post['post_time']);
        else $stime = date("d.m.Y", $post['post_time']);
        $mas[$i]['time'] = $stime;
        $mas[$i]['url'] = $post['post_link'];
        if ($post['blog_link'] == 'vkontakte.ru') {
            if ($post['blog_login'][0] == '-') {
                $mas[$i]['auth_url'] = 'http://vk.com/club' . substr($post['blog_login'], 1);
            }
            else
            {
                $mas[$i]['auth_url'] = 'http://vk.com/id' . $post['blog_login'];
            }
        }
        elseif ($post['blog_link'] == 'facebook.com')
        {
            $mas[$i]['auth_url'] = 'http://facebook.com/' . $post['blog_login'];
        }
        elseif ($post['blog_link'] == 'twitter.com')
        {
            $mas[$i]['auth_url'] = 'http://twitter.com/' . $post['blog_login'];
        }
        elseif ($post['blog_link'] == 'livejournal.com')
        {
            $mas[$i]['auth_url'] = 'http://' . $post['blog_login'] . '.livejournal.com';
        }
        elseif (preg_match('/mail\.ru/isu', $post['blog_link']))
        {
            $mas[$i]['auth_url'] = 'http://blogs.' . $post['blog_link'] . '/' . $post['blog_login'];
        }
        elseif ($post['blog_link'] == 'liveinternet.ru')
        {
            $mas[$i]['auth_url'] = 'http://www.liveinternet.ru/users/' . $post['blog_login'];
        }
        elseif ($post['blog_link'] == 'ya.ru')
        {
            $mas[$i]['auth_url'] = 'http://' . $post['blog_login'] . '.ya.ru';
        }
        elseif ($post['blog_link'] == 'yandex.ru')
        {
            $mas[$i]['auth_url'] = 'http://' . $post['blog_login'] . '.ya.ru';
        }
        elseif ($post['blog_link'] == 'rutwit.ru')
        {
            $mas[$i]['auth_url'] = 'http://rutwit.ru/' . $post['blog_login'];
        }
        elseif ($post['blog_link'] == 'rutvit.ru')
        {
            $mas[$i]['auth_url'] = 'http://rutwit.ru/' . $post['blog_login'];
        }
        elseif ($post['blog_link'] == 'babyblog.ru')
        {
            $mas[$i]['auth_url'] = 'http://www.babyblog.ru/user/info/' . $post['blog_login'];
        }
        elseif ($post['blog_link'] == 'blog.ru')
        {
            $mas[$i]['auth_url'] = 'http://' . $post['blog_login'] . '.blog.ru/profile';
        }
        elseif ($post['blog_link'] == 'foursquare.com')
        {
            $mas[$i]['auth_url'] = 'https://ru.foursquare.com/' . $post['blog_login'];
        }
        elseif ($post['blog_link'] == 'kp.ru')
        {
            $mas[$i]['auth_url'] = 'http://blog.kp.ru/users/' . $post['blog_login'] . '/profile/';
        }
        elseif ($post['blog_link'] == 'aif.ru')
        {
            $mas[$i]['auth_url'] = 'http://blog.aif.ru/users/' . $post['blog_login'] . '/profile';
        }
        elseif ($post['blog_link'] == 'friendfeed.com')
        {
            $mas[$i]['auth_url'] = 'http://friendfeed.com/' . $post['blog_login'];
        }
        elseif ($post['blog_link'] == 'plus.google.com')
        {
            $mas[$i]['auth_url'] = 'https://plus.google.com/' . $post['blog_login'] . '/about';
        }
        $hn = parse_url($post['post_link']);
        $hn = $hn['host'];
        $ahn = explode('.', $hn);
        $hn = $ahn[count($ahn) - 2] . '.' . $ahn[count($ahn) - 1];
        $hh = $ahn[count($ahn) - 2];
        $mas[$i]['host'] = $hh;
        $mas[$i]['img_url'] = (!file_exists('/var/www/production/img/social/' . $hh . '.png'))
                ? './img/social/wobot_logo.gif' : './img/social/' . $hh . '.png';
        $mas[$i]['host_name'] = $hn;
        $mas[$i]['nick'] = html_entity_decode(($post['blog_nick'] == null) ? '' : $post['blog_nick']);
        $mas[$i]['count_user'] = $metrics['speakers'][$post['blog_nick']];
        $mas[$i]['notes'] = $post['post_note_count'];
        $mas[$i]['is_read'] = $post['post_read'];
        $mas[$i]['nastr'] = $post['post_nastr'];
        $mas[$i]['spam'] = $post['post_spam'];
        $mas[$i]['eng'] = $post['post_engage'];
        $mas[$i]['fav'] = $post['post_fav'];
        $mas[$i]['adv_eng'] = json_decode($post['post_advengage'], true);
        $mas[$i]['foll'] = $post['blog_readers'];
        $mas[$i]['ico'] = $post['blog_ico'];
        $mas[$i]['geo'] = $wobot['destn1'][$post['blog_location']];
        $mas[$i]['geo_c'] = $wobot['destn3'][$wobot['destn1'][$post['blog_location']]];
        $mas[$i]['reaction']['reaction_content']=$post['reaction_content'];
        $mas[$i]['reaction']['reaction_time']=date("H:i:s d.m.Y",$post['reaction_time']);
        $mas[$i]['reaction']['reaction_blog_login']=$post['reaction_blog_login'];
        $mas[$i]['reaction']['reaction_blog_info']=json_decode($post['reaction_blog_info'],true);
        $t_post = explode(',', $post['post_tag']);
        $mas[$i]['tags'] = array();
        foreach ($t_post as $item)
        {
            if ($item != '') {
                $arr_t_post[$item] = $d_tags[$item];
                $mas[$i]['tags'] = $arr_t_post; //array(2=>'Главное',4=>'Офис',6=>'Отчет',7=>'Посмотреть');
            }
        }
        if (count($arr_t_post) != 0) {
            $mas[$i]['tags'] = $arr_t_post; //array(2=>'Главное',4=>'Офис',6=>'Отчет',7=>'Посмотреть');
        }
        else
        {
            $mas[$i]['tags'] = array();
        }
        $mas[$i]['gender'] = $post['blog_gender'];
        $mas[$i]['age'] = $post['blog_age'];
        $i++;
        unset($arr_t_post);
    }
    if (($_POST['sort']=='dup') && (intval($_POST['byparent'])==0))
    {
        usort($mas, 'dubsort');
        for ($i=0;$i<$_POST['perpage'];$i++)
        {
            foreach ($mas[$i] as $key => $item)
            {
                $mas2[$i+1][$key]=$item;
            }
        }
        $mas=$mas2;
    }
    //$qw=preg_replace('/SELECT */is','SELECT (*)',$qw);
    $qw = preg_replace('/ LIMIT ' . $_POST['perpage'] . '/is', '', $qw);
    $_SESSION[md5($qw)] = $qw;
    $qw1 = $qw;
    $mas['md5'] = md5($qw);
    $qw=preg_replace('/(SELECT \*)/is','SELECT count(*) as cnt',$qw);
    // $qw=preg_replace('/(ORDER BY)/is','GROUP BY post_host ORDER BY',$qw);
    $countqposts=$db->query($qw);
    while ($count=$db->fetch($countqposts))
    {
    	$cnt++;//=$count['cnt'];
    	$cnt_host++;
    }
    $mas['page'] = 0;
    $mas['md5_count_post']=$cnt;
    $mas['md5_count_src']=$cnt_host;
    $_SESSION['count_post_' . md5($qw1)] = $cnt;
    $_SESSION['count_src_' . md5($qw1)] = $cnt_host;
}


echo json_encode($mas);

?>