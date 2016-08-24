<?

require_once('/var/www/new/com/config.php');
require_once('/var/www/new/com/func.php');
require_once('/var/www/new/com/db.php');
require_once('/var/www/new/bot/kernel.php');
require_once('/var/www/com/checker.php');
require_once('auth.php');

$db = new database();
$db->connect();

auth();
if (!$loged) die();
if ($user['tariff_id'] == 3) {
    $mas['status'] = 'fail';
    echo json_encode($mas);
    die();
}
/* status
0 - нет имени
1 - нет ключевых слов
21 - неправильные ключевые слова
22 - неправильные стоп слова
fail - другие ошибки
*/
if (trim($_POST['tag_name']) == '') {
    $mas['status'] = 0;
    echo json_encode($mas);
    die();
}
if ($_POST['tag_kw'] != '') {
    $mw = explode(',', $_POST['tag_kw']);
    foreach ($mw as $w)
    {
        if (preg_match('/\"/is', trim($w))) {
            if ((preg_match('/^\"[0-9а-яА-Яa-zA-ZёЁ\ \'\-\.]+\"$/isu', trim($w))) && (trim($w) != '')) {
                $strw .= $and . trim($w);
                $and = ' | ';
            }
            else
            {
                //echo $w;
                $mas['status'] = 21;
                echo json_encode($mas);
                die();
            }
        }
        else
        {
            if ((preg_match('/^[0-9а-яА-Яa-zA-ZёЁ\ \'\-\.]+$/isu', trim($w))) && (trim($w) != '')) {
                $strw .= $and . trim($w);
                $and = ' | ';
            }
            else
            {
                //echo $w;
                $mas['status'] = 21;
                echo json_encode($mas);
                die();
            }
        }
    }
}
elseif ($_POST['auto']==0)
{
    ;
}
elseif ($_POST['tag_akw']=='')
{
    $mas['status'] = 1;
    echo json_encode($mas);
    die();
}
if ($_POST['tag_sw'] != '') {
    $mew = explode(',', $_POST['tag_sw']);
    foreach ($mew as $ew)
    {
        if (preg_match('/\"/is', trim($ew))) {
            if ((preg_match('/^\"[а-яА-Яa-zA-ZёЁ\ \'\-\.]+\"$/isu', trim($ew))) && (trim($ew) != '')) {
                $ex = ' ~~ ';
                $strew .= $ex . trim($ew);
            }
            else
            {
                $mas['status'] = 22;
                echo json_encode($mas);
                die();
            }
        }
        else
        {
            if ((preg_match('/^[а-яА-Яa-zA-ZёЁ\ \'\-\.]+$/isu', trim($ew))) && (trim($ew) != '')) {
                $ex = ' ~~ ';
                $strew .= $ex . trim($ew);
            }
            else
            {
                $mas['status'] = 22;
                echo json_encode($mas);
                die();
            }
        }
    }
}
$qwry = (($strw != '') ? '(' . $strw . ')' : '') . $strew;
if ($_POST['tag_akw']!='') $qwry=$_POST['tag_akw'];
//echo $qwry; die();
if ((check_query($qwry)==0) && ($_POST['auto']!=0))
{
    $mas['status']=3;
    echo json_encode($mas);
    die();
}

$query = 'SELECT * FROM blog_tag WHERE order_id=' . intval($_POST['order_id']);
//echo $query;
$mtt=array('1','2','3','4','5','6','7','8','9','10','11','12','13','14','15','16','17','18','19','20','21','22','23','24','25','26','27','28','29','30','31','32','33','34','35','36','37','38','39','40','41','42','43','44','45','46','47','48','49','50');
$respost = $db->query($query);
while ($rpp = $db->fetch($respost))
{
    $mtt2[] = $rpp['tag_tag'];
}
//print_r($mtt2);
foreach ($mtt as $item)
{
    if (!in_array($item, $mtt2)) {
        $mtt3[] = $item;
    }
}
if (count($mtt3) != 0) {
    if ($_POST['tag_akw']=='')
    {
        $query = 'INSERT INTO blog_tag (user_id,order_id,tag_name,tag_tag,tag_auto, tag_kw, tag_sw)
    	VALUES (' . intval($_POST['user_id']) . ',' . intval($_POST['order_id']) . ',
    	\'' . addslashes($_POST['tag_name']) . '\',' . $mtt3[0] . ', ' . intval($_POST['auto']) . ', \'' . addslashes($_POST['tag_kw']) . '\', \'' . addslashes($_POST['tag_sw']) . '\' )';
    }
    else
    {
        $query = 'INSERT INTO blog_tag (user_id,order_id,tag_name,tag_tag,tag_auto, tag_akw)
        VALUES (' . intval($_POST['user_id']) . ',' . intval($_POST['order_id']) . ',
        \'' . addslashes($_POST['tag_name']) . '\',' . $mtt3[0] . ', ' . intval($_POST['auto']) . ', \'' . addslashes($_POST['tag_akw']) . '\' )';
    }
    $respost = $db->query($query);
    if (!isset($memcache)) $memcache = memcache_connect('localhost', 11211);
    $qorder=$db->query('SELECT order_id,order_start,order_end FROM blog_orders WHERE order_id='.intval($_POST['order_id']).' LIMIT 1');
    $order=$db->fetch($qorder);
    if ($order['order_end']>time()) $order['order_end']=mktime(0,0,0,date('n'),date('j'),date('Y'));
    $memcache->delete('filters_'.$order['order_id'].'_'.$order['order_start'].'_'.$order['order_end']);
    //echo $query; die();
    $mas['id'] = $mtt3[0];
    $mas['status'] = 'ok';
    echo json_encode($mas);
}
else
{
    $mas['status'] = 'fail';
    echo json_encode($mas);
}

?>