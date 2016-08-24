<?

//пересборщик feedproxy.google.com в базе

require_once('/var/www/daemon/bot/kernel.php');
require_once('/var/www/daemon/com/db.php');
require_once('/var/www/daemon/com/config.php');
$db = new database();
$db->connect();
function parseURLreal( $url )
{
    $uagent = "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13";
    //$uagent = "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.1.4322)";
    $uagent = "Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.2.15) Gecko/20110303 Firefox/3.6.152011";

    $ch = curl_init( $url );
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);   // возвращает веб-страницу
    curl_setopt($ch, CURLOPT_HEADER, 0);           // не возвращает заголовки
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);   // переходит по редиректам
    curl_setopt($ch, CURLOPT_ENCODING, "");        // обрабатывает все кодировки
    curl_setopt($ch, CURLOPT_USERAGENT, $uagent);  // useragent
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10); // таймаут соединения
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);        // таймаут ответа
    curl_setopt($ch, CURLOPT_MAXREDIRS, 10);       // останавливаться после 10-ого редиректа
    curl_setopt ($ch, CURLOPT_COOKIE, 'adult_concepts=1' ); 

    //curl_setopt($ch, CURLOPT_COOKIEJAR, "z://coo.txt");
    //curl_setopt($ch, CURLOPT_COOKIEFILE,"z://coo.txt");

    $content = curl_exec( $ch );
    $err     = curl_errno( $ch );
    $errmsg  = curl_error( $ch );
    $header  = curl_getinfo( $ch );
    curl_close( $ch );

    //$header['errno']   = $err;
    //$header['errmsg']  = $errmsg;
    $header['content'] = $content;
    //print_r($header['url']);
    return $header['url'];
    //return $content;
}

// echo parseURLreal('http://feedproxy.google.com/~r/blogspot/vorovtsev/~3/TiUNxeDAH_s/48921055775');
// die();

$filename = 'last_id.txt';
$handle = fopen($filename, "rb");
$last_id = trim(fread($handle, filesize($filename)));
fclose($handle);

while (1)
{
    $offset=0;
    do
    {
        echo "SELECT * from blog_post WHERE post_id>".$last_id." AND post_link LIKE '%feedproxy.google.com%' ORDER BY post_id ASC LIMIT ".($offset*100).",100";
        $res=$db->query("SELECT * from blog_post WHERE post_id>".$last_id." AND post_link LIKE '%feedproxy.google.com%' ORDER BY post_id ASC LIMIT ".($offset*100).",100");
        $offset++;
        while ($post=$db->fetch($res))
        {
            $last_id=$post['post_id'];
            $fp = fopen('last_id.txt', 'w');
            fwrite($fp, $post['post_id']);
            fclose($fp);
            $real_link=trim(parseURLreal($post['post_link']));
            $hn=parse_url($real_link);
            $hn=$hn['host'];
            $ahn=explode('.',$hn);
            $hn = $ahn[count($ahn)-2].'.'.$ahn[count($ahn)-1];
            $hh = $ahn[count($ahn)-2];
            if (trim($real_link)=='') continue;
            $slice_time=mktime(0,0,0,date('n',$post['post_time']),date('j',$post['post_time']),date('Y',$post['post_time']));
            if (($to_cash[$post['order_id']]['start']=='')&&($to_cash[$post['order_id']]['end']==''))
            {
                $to_cash[$post['order_id']]['start']=$slice_time;   
                $to_cash[$post['order_id']]['end']=$slice_time;   
            }
            if ($slice_time<$to_cash[$post['order_id']]['start']) $to_cash[$post['order_id']]['start']=$slice_time;
            if ($slice_time>$to_cash[$post['order_id']]['end']) $to_cash[$post['order_id']]['end']=$slice_time;
            // print_r($to_cash);
            echo 'UPDATE blog_post SET post_link=\''.addslashes($real_link).'\',post_host=\''.$hn.'\' WHERE post_id='.$post['post_id'].' AND order_id='.$post['order_id']."\n";
            $rr=$db->query('UPDATE blog_post SET post_link=\''.addslashes($real_link).'\',post_host=\''.$hn.'\' WHERE post_id='.$post['post_id'].' AND order_id='.$post['order_id']);
            sleep(1);
        }
        print_r($to_cash);
        foreach ($to_cash as $key => $item)
        {
           // file_get_contents('http://localhost/tools/cashjob.php?order_id='.intval($key).'&start='.$item['start'].'&end='.$item['end']);
        }
        unset($to_cash);
        sleep(60);
    }
    while ($db->num_rows($res)!=0);
    sleep(3600);
}
?>