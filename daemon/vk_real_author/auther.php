<?

require_once('/var/www/daemon/bot/kernel.php');
require_once('/var/www/daemon/com/db.php');
require_once('/var/www/daemon/com/config.php');
require_once('/var/www/daemon/vk_real_author/func_author.php');

$db=new database();
$db->connect();

$redis = new Redis();    
$redis->connect('127.0.0.1');

$order_delta=$_SERVER['argv'][1];
$fp = fopen('/var/www/pids/auth'.$order_delta.'.pid', 'w');
fwrite($fp, getmypid());
fclose($fp);

$i=0;
$last_post_id=get_last_id();
while (1)
{
    if ($last_id=='') $qpost=$db->query('SELECT * FROM blog_post WHERE post_host=\'vk.com\' AND post_link NOT LIKE \'%?reply%\' AND MOD(post_id,'.$_SERVER['argv'][2].')='.$_SERVER['argv'][1].' AND post_id>'.($last_post_id-10000).' ORDER BY post_id ASC');
    else $qpost=$db->query('SELECT * FROM blog_post WHERE post_host=\'vk.com\' AND post_link NOT LIKE \'%?reply%\' AND MOD(post_id,'.$_SERVER['argv'][2].')='.$_SERVER['argv'][1].' AND post_id>'.$last_id.' ORDER BY post_id ASC');
    while ($post=$db->fetch($qpost))
    {//'http://vk.com/wall-19572555_9459'
        // $post['post_link']='http://vk.com/wall-19572555_9459';
        $last_id=$post['post_id'];
        $new_blog_id=0;
        if ($i % 100==0) 
        {
            // $cproxy=file_get_contents('http://localhost/api/service/getlist.php?type=vk');
            // $mproxy=json_decode($cproxy,true);proxy_vk
            $mproxy=json_decode($redis->get('proxy_vk'),true);
        }
        echo "\n----\n".$post['post_id'].' '.$post['post_link']."\n";
        $new_auth_id=get_author_id($post['post_link']);  
        if (intval($new_blog_id)==0) continue;
        $qblog=$db->query('SELECT * FROM robot_blogs2 WHERE blog_login=\''.$new_auth_id.'\' AND blog_link=\'vkontakte.ru\' LIMIT 1');
        if ($db->num_rows($qblog)==0)
        {
            $qins=$db->query('INSERT INTO robot_blogs2 (blog_login,blog_link) VALUES (\''.$new_auth_id.'\',\'vkontakte.ru\')');
            $new_blog_id=$db->insert_id($qins);
        }
        else
        {
            $blog=$db->fetch($qblog);
            $new_blog_id=$blog['blog_id'];
        }
        if (intval($new_blog_id)!=0) $db->query('UPDATE blog_post SET blog_id='.$new_blog_id.' WHERE post_id='.$post['post_id'].' AND order_id='.$post['order_id']);
        $mcache[$post['order_id']][mktime(0,0,0,date('n',$post['post_time']),date('j',$post['post_time']),date('Y',$post['post_time']))]++;
        echo '!'.$new_blog_id.'!';
        $i++;
    }
    foreach ($mcache as $key => $item)
    {
        foreach ($item as $k => $i)
        {
            //file_get_contents('http://localhost/tools/cashjob.php?order_id='.intval($key).'&start='.$k.'&end='.$k);
        }
    }
}

?>