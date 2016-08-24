<?

require_once('/var/www/daemon/com/config.php');
require_once('/var/www/daemon/com/func.php');
require_once('/var/www/daemon/com/db.php');
require_once('/var/www/daemon/bot/kernel.php');
//require_once('/var/www/tools/fsearch2/lcheck.php');
require_once('/var/www/daemon/fsearch3/ch.php');

date_default_timezone_set('Europe/Moscow');
error_reporting(E_ERROR);
ignore_user_abort(true);
set_time_limit(0);
ini_set('max_execution_time', 0);
ini_set('default_charset', 'utf-8');
ob_implicit_flush();
$db = new database();
$db->connect();


$order_delta=$_SERVER['argv'][1];
$debug_mode=$_SERVER['argv'][2];
$fp = fopen('/var/www/pids/tj'.$order_delta.'.pid', 'w');
fwrite($fp, getmypid());
fclose($fp);
echo $order_delta;


//Из ключевых слов формируем запрос
function makeQuery($keyword, $stopword)
{
    $headers  = "From: noreply@wobot.ru\r\n"; 
    $headers .= "Bcc: noreply@wobot.ru\r\n";
    $headers .= 'MIME-Version: 1.0' . "\r\n";
    $headers .= 'Content-type: text/html; charset=utf-8'."\r\n";
    if ($keyword != '') {
        $mw = explode(',', $keyword);
        print_r($mw);
        foreach ($mw as $w)
        {
            $w=preg_replace('/[\t\s]+/isu',' ',$w);
            if (preg_match('/\"/is', trim($w))) {
                if ((preg_match('/^\"[0-9а-яА-Яa-zA-ZёЁ\ \'\-\.\!]+\"$/isu', trim($w))) && (trim($w) != '')) {
                    $strw .= $and . trim($w);
                    $and = ' | ';
                }
                else
                {
                    //echo $w;
                    $mas['status'] = 21;
                    echo json_encode($mas);
                    mail('zmei123@yandex.ru','tagjob',$keyword,$headers);
                }
            }
            else
            {
                if ((preg_match('/^[0-9а-яА-Яa-zA-ZёЁ\ \'\-\.\!]+$/isu', trim($w))) && (trim($w) != '')) {
                    $strw .= $and . trim($w);
                    $and = ' | ';
                }
                else
                {
                    //echo $w;
                    $mas['status'] = 21;
                    echo json_encode($mas);
                    mail('zmei123@yandex.ru','tagjob',$keyword,$headers);
                }
            }
        }
    }
    if ($stopword != '') {
        $mew = explode(',', $stopword);
        foreach ($mew as $ew)
        {
            if (preg_match('/\"/is', trim($ew))) {
                if ((preg_match('/^\"[0-9а-яА-Яa-zA-ZёЁ\ \'\-\.\!]+\"$/isu', trim($ew))) && (trim($ew) != '')) {
                    $ex = ' ~~ ';
                    $strew .= $ex . trim($ew);
                }
                else
                {
                    $mas['status'] = 23;
                    echo json_encode($mas);
                    mail('zmei123@yandex.ru','tagjob',$keyword,$headers);
                }
            }
            else
            {
                if ((preg_match('/^[0-9а-яА-Яa-zA-ZёЁ\ \'\-\.\!]+$/isu', trim($ew))) && (trim($ew) != '')) {
                    $ex = ' ~~ ';
                    $strew .= $ex . trim($ew);
                }
                else
                {
                    $mas['status'] = 23;
                    echo json_encode($mas);
                    mail('zmei123@yandex.ru','tagjob',$keyword,$headers);
                }
            }
        }
    }

    $qwry = (($strw != '') ? '(' . $strw . ')' : '') . $strew;

    return $qwry;
}

//Заносит тег
function updateTag($order_id, $post_id, $tag_id, $tag_value)
{
    global $db;
    $lock = $db->query('LOCK TABLES blog_post WRITE');
    $res = $db->query('SELECT * FROM blog_post WHERE order_id=' . intval($order_id) . ' AND post_id=' . intval($post_id) . ' ');
    echo "\n" . 'SELECT * FROM blog_post WHERE order_id=' . intval($order_id) . ' AND post_id=' . intval($post_id) . "\n";
    $tt = $db->fetch($res);
    if (intval($tt['post_id']) != 0) {
        $mt = explode(',', $tt['post_tag']);
        //print_r($mt);echo "\n";
        $zap = '';
        $newt = '';
        if ($tag_value == 'true') {
            $mt[] = $tag_id;
            sort($mt);
            //print_r($mt);
            $zap = '';
            $mt = array_unique($mt);
            foreach ($mt as $it)
            {
                $newt .= $zap . $it;
                $zap = ',';
            }
            echo "\n newt =";
            print_r($newt);
            echo "\n";
            echo 'UPDATE blog_post SET post_tag=\'' . $newt . '\' WHERE post_id=' . intval($post_id) . ' AND order_id=' . intval($order_id);
            //TODO: uncomment
            $db->query('UPDATE blog_post SET post_tag=\'' . $newt . '\' WHERE post_id=' . intval($post_id) . ' AND order_id=' . intval($order_id));


            //$db->query('UPDATE blog_post SET post_tag=\''.$newt.'\' WHERE post_id='.intval($_GET['id']).' AND order_id='.intval($_GET['order_id']));
            //echo 'UPDATE blog_post SET post_tag=\''.$newt.'\' WHERE post_id='.intval($_GET['id']).' AND order_id='.intval($_GET['order_id']);
            $mas['status'] = 'ok';
        }
        else
        {
            //print_r($mt);
            foreach ($mt as $item)
            {
                if ($item != $_GET['tag_id']) {
                    $newt .= $zap . $item;
                    $zap = ',';
                }
            }
            //$db->query('UPDATE blog_post SET post_tag=\''.$newt.'\' WHERE post_id='.intval($_GET['id']).' AND order_id='.intval($_GET['order_id']));
            //echo 'UPDATE blog_post SET post_tag=\'' . $newt . '\' WHERE post_id=' . intval($_GET['id']) . ' AND order_id=' . intval($_GET['order_id']);

            //echo 'UPDATE blog_post SET post_tag=\''.$newt.'\' WHERE post_id='.intval($_GET['id']).' AND order_id='.intval($_GET['order_id']);

            $mas['status'] = 'ok';
        }
        //$rs=$db->query('UPDATE blog_post SET post_tag=\''.$text_tag.'\' WHERE post_id='.intval($_GET['id']).' AND order_id='.intval($_GET['order_id']));
        echo json_encode($mas);
    }
    else
    {
        echo "TUT NE QUERY\n";
        // die();

        $mas['status'] = 'fail';
        echo json_encode($mas);
    }
    $lock = $db->query('UNLOCK TABLES');

}

//функционал для получения и простановки тегов
//получаем все order_id у кого есть auto tag
//для каждого ордера получаем теги и сообщения
//для каждого сообщения выставляем теги

//для каждого тега заносить post_id последнего обработанного


while (1)
{

    $orders = array();
    $minauto = array();
    $last_id = array();

    $query = "SELECT tag_akw, tag_kw, tag_sw, tag_tag, order_id, tag_name, tag_auto, tag_id
    FROM blog_tag
    WHERE tag_auto!=0
    AND MOD (order_id, ".$_SERVER['argv'][2].") = ".$_SERVER['argv'][1]."";

    $res = $db->query($query);
    while ($tag = $db->fetch($res))
    {
        if ($minauto[$tag['order_id']] == '' || $minauto[$tag['order_id']] > $tag['tag_auto']) {
            $minauto[$tag['order_id']] = $tag['tag_auto'];
        }
        print_r($tag);
        //TODO: вычисление минимального tag_auto (order_id)
        if ($tag['tag_akw']=='') $tagQuery = makeQuery($tag['tag_kw'], $tag['tag_sw']);
        else $tagQuery=$tag['tag_akw'];
        $orders[$tag['order_id']][] = array($tag['tag_tag'], $tagQuery, $tag['tag_auto'], $tag['tag_id']);
        echo $tag['tag_tag'] . " - " . $tagQuery . "\n";
    }

    //print_r($minauto);
    //die();

    foreach ($orders as $id => $tags)
    {
        //$minauto = ;

        $query = 'SELECT p.post_content, p.post_id, f.ful_com_post, p.post_host FROM blog_post AS p
	    LEFT JOIN blog_full_com AS f ON p.post_id = f.ful_com_post_id
	    WHERE p.order_id=' . $id . ' AND p.post_id >= ' . $minauto[$id] . ' ';

        echo $query . "\n";
        //die();    
        $res = $db->query($query);
        //TODO: if ()
        while ($post = $db->fetch($res))
        {

            // print_r($tags);
            foreach ($tags as $tag) {

                if ($tag[2] > $post['post_id']) continue;

                //TODO: проверка if tag_auto > post_id then continue;
                //echo "ПО ЗАПРОСУ $tag[1]\n";

                //echo $post['post_id']."==\n";
                if ($post['ful_com_post'] == '') {
                    //echo $post['post_content']." \n";
                    //$status = check_post($post['post_content'],$tag[1]);
                    $status = check_post($post['post_content'], $tag[1]);
                }
                else
                {
                    //echo "======ПОЛНЫЙ ТЕКСТ НЕ ПУСТОЙ\n";
                    //echo "Запрос: {$tag[1]}, пост: {$post['ful_com_post']}\n";
                    $status = check_post($post['ful_com_post'], $tag[1]);
                }


                //echo "STATUS = $status \n";
                //if ($status=="YES")
                if ($status == 1) {
                    //TODO: update
                    updateTag($id /*order_id*/, $post['post_id'], $tag[0], true);
                    //echo "!!!!!!!!!!!YES!!!!!!!!!!!!!\n";
                } //else echo "NO\n";
                // print_r($tag);
                $last_id[$tag[3]] = $post['post_id'];


            }
            //print_r($post['ful_com_post']);
            //echo "\n";
            //echo $order[1]."BALSBLALBAL\n";
        }

        //echo "END";
        print_r($last_id);
        //die();
        foreach ($last_id as $id => $last)
        {
            $query = 'UPDATE blog_tag SET tag_auto = ' . $last . ' WHERE tag_id = ' . $id;
            $db->query($query);
            //echo "\n$query";
            //$db->query($query);

        }

    }
    echo "\nidle...\n";
    sleep(600);
}
die("\nTHE END\n");
?>
