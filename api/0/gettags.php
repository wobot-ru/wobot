<?

require_once('/var/www/new/com/config.php');
require_once('/var/www/new/com/func.php');
require_once('/var/www/new/com/db.php');
require_once('/var/www/new/bot/kernel.php');
require_once('auth.php'); 

$db = new database();
$db->connect(); 

auth();
if (!$loged) die();

$redis=new Redis() or die("Can'f load redis module.");
$redis->connect('127.0.0.1');
set_log('gettag',$_POST);

if ($user['tariff_id'] == 3) {
    $mas['status'] = 'fail';
    echo json_encode($mas);
    die();
}
else
{
    //echo "123";
    $tags_info = $db->query('SELECT * FROM blog_tag WHERE order_id=' . intval($_POST['order_id'].' ORDER BY tag_id'));
    while ($tag = $db->fetch($tags_info))
    {
        //echo str_replace('.',' ',$tag['tag_name']);

        if ($tag['tag_akw']=='') $out['tags'][$tag['tag_tag']] = array('name'=>str_replace('.', '', mb_substr($tag['tag_name'],0,23,'UTF-8')), 'auto' => $tag['tag_auto'],
                                              'keywords'=> $tag['tag_kw'], 'stopwords'=>$tag['tag_sw']);
        else $out['tags'][$tag['tag_tag']] = array('name'=>str_replace('.', '', mb_substr($tag['tag_name'],0,23,'UTF-8')), 'auto' => $tag['tag_auto'],
                                              'advanced_keywords'=> $tag['tag_akw']);
    }
    echo json_encode($out);
}

?>