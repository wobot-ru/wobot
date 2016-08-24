<?

require_once('/var/www/new/com/config.php');
require_once('/var/www/new/com/func.php');
require_once('/var/www/new/com/db.php');
require_once('/var/www/new/bot/kernel.php');
require_once('/var/www/com/checker.php');
require_once('auth.php');

$db = new database();
$db->connect();
$_GET = $_POST;
auth();


function checkQuery($kw, $sw, $auto)
{

    if ($kw != '') {
        $mw = explode(',', $kw);
        foreach ($mw as $w)
        {
            if (preg_match('/\"/is', trim($w))) {
                if ((preg_match('/^\"[а-яА-Яa-zA-ZёЁ\ \'\-\.]+\"$/isu', trim($w))) && (trim($w) != '')) {
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
                if ((preg_match('/^[а-яА-Яa-zA-ZёЁ\ \'\-\.]+$/isu', trim($w))) && (trim($w) != '')) {
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
    elseif ($auto == 0)
    {
        ;
    }
    else
    {
        $mas['status'] = 1;
        echo json_encode($mas);
        die();
    }
    if (trim($sw) != '') {
        $mew = explode(',', $sw);
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
    return $qwry;
    //
}


if (!$loged) {
    $mas['status'] = 'fail';
    echo json_encode($mas);
    die();
}
if ($user['tariff_id'] == 3) {
    $mas['status'] = 'fail';
    echo json_encode($mas);
    die();
}
if ((intval($_GET['order_id']) == 0) || (intval($_GET['tag_id']) == 0)) {
    $mas['status'] = 'fail';
    echo json_encode($mas);
    die();
}
//die();
$res = $db->query('SELECT * FROM blog_tag WHERE order_id=' . intval($_GET['order_id']) . ' AND tag_tag=' . intval($_GET['tag_id']));
while ($row1 = $db->fetch($res))
{
    $row['id'] = $row1['tag_id'];
}


if ($row['id'] != '' && $_GET['change_auto'] == '') {
    if ($_POST['tag_akw']=='')
    {
        $db->query('UPDATE blog_tag SET tag_name=\'' . addslashes($_GET['tag_name']) . '\',
    	tag_kw=\'' . addslashes($_GET['tag_kw']) . '\',
    	tag_sw=\'' . addslashes($_GET['tag_sw']) . '\',
        tag_akw=\'\',
    	tag_auto=\'' . addslashes($_GET['tag_auto']) . '\'
    	WHERE order_id=' . intval($_GET['order_id']) . ' AND tag_tag=' . intval($_GET['tag_id']));
    }
    else
    {
        if (check_query($_POST['tag_akw'])==0)
        {
            $mas['status']=3;
            echo json_encode($mas);
            die();
        }
        $db->query('UPDATE blog_tag SET tag_name=\'' . addslashes($_GET['tag_name']) . '\',
        tag_akw=\'' . addslashes($_GET['tag_akw']) . '\',
        tag_kw=\'\',
        tag_sw=\'\',
        tag_auto=\'' . addslashes($_GET['tag_auto']) . '\'
        WHERE order_id=' . intval($_GET['order_id']) . ' AND tag_tag=' . intval($_GET['tag_id']));
    }
    //echo 'UPDATE blog_tag SET tag_name=\''.addslashes($_GET['tag_name']).'\', tag_kw=\''.addslashes($_GET['tag_kw']).'\' WHERE order_id='.intval($_GET['order_id']).' AND tag_tag='.intval($_GET['tag_id']);

    /*echo 'UPDATE blog_tag SET tag_name=\''.addslashes($_GET['tag_name']).'\',
	tag_kw=\''.addslashes($_GET['tag_kw']).'\',
	tag_sw=\''.addslashes($_GET['tag_sw']).'\',
	tag_auto=\''.addslashes($_GET['tag_auto']).'\'
	WHERE order_id='.intval($_GET['order_id']).' AND tag_tag='.intval($_GET['tag_id']);*/

    $mas['status'] = 'ok';
    echo json_encode($mas);
}
else if ($row['id'] != '' && $_GET['change_auto'] == 1) {


    if (trim($_POST['tag_name']) == '') {
        $mas['status'] = 0;
        echo json_encode($mas);
        die();
    }
    checkQuery($_POST['tag_kw'], $_POST['tag_sw'], $_POST['tag_auto']);
    echo 'UPDATE blog_tag SET
	tag_auto=\'' . addslashes($_GET['tag_auto']) . '\'
	WHERE order_id=' . intval($_GET['order_id']) . ' AND tag_tag=' . intval($_GET['tag_id']);

    $db->query('UPDATE blog_tag SET
	tag_auto=\'' . addslashes($_GET['tag_auto']) . '\'
	WHERE order_id=' . intval($_GET['order_id']) . ' AND tag_tag=' . intval($_GET['tag_id']));

    if (!isset($memcache)) $memcache = memcache_connect('localhost', 11211);
    $qorder=$db->query('SELECT order_id,order_start,order_end FROM blog_orders WHERE order_id='.intval($_POST['order_id']).' LIMIT 1');
    $order=$db->fetch($qorder);
    if ($order['order_end']>time()) $order['order_end']=mktime(0,0,0,date('n'),date('j'),date('Y'));
    $memcache->delete('filters_'.$order['order_id'].'_'.$order['order_start'].'_'.$order['order_end']);

    $mas['status'] = 'ok';
    echo json_encode($mas);

}
else
{
    $mas['status'] = 'fail';
    echo json_encode($mas);
}

?>