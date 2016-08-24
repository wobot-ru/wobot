<?

function parseURLheader( $url )
{
    global $mproxy;
    do
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
        curl_setopt($ch, CURLOPT_PROXY, $mproxy[rand(0,9)]);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 10);       // останавливаться после 10-ого редиректа
        curl_setopt ($ch, CURLOPT_COOKIE, 'adult_concepts=1' ); 

        //curl_setopt($ch, CURLOPT_COOKIEJAR, "z://coo.txt");
        //curl_setopt($ch, CURLOPT_COOKIEFILE,"z://coo.txt");

        $content = curl_exec( $ch );
        $err     = curl_errno( $ch );
        $errmsg  = curl_error( $ch );
        $header  = curl_getinfo( $ch );
        curl_close( $ch );
        $attemp++;
    }
    while ($attemp<3 && $header['http_code']!=200);
    // echo $content;
    // print_r($err);
    // print_r($errmsg);
    // print_r($header);
    // die();
    return $header['url'];
}

function get_author_id($url)
{
    $real_url=parseURLheader($url);
    if ($real_url==$url) return 0;
    elseif ($real_url=='') return 0;
    else 
    {
        $regex='/wall(?<owner_id>[\d\-]+)\_(?<post_id>\d+)\?reply\=(?<reply>\d+)/isu';
        preg_match_all($regex, $real_url, $out);
        return get_auth_id($out['owner_id'][0],$out['post_id'][0],$out['reply'][0]);
    }
}

function get_auth_id($owner_id,$post_id,$reply)
{
    global $mproxy;
    do
    {
        $attemp=0;
        do
        {
            $cont=parseURLproxy('https://api.vkontakte.ru/method/wall.getComments?owner_id='.$owner_id.'&post_id='.$post_id.'&count=100&offset='.intval($offset*100),$mproxy[rand(0,9)]);
            $mcont=json_decode($cont,true);
            $attemp++;
        }
        while (!isset($mcont['response']) && $attemp<3);
        // print_r($mcont);
        foreach ($mcont['response'] as $item)
        {
            if ($item['cid']==$reply) return $item['from_id'];
        }
        $offset++;
    }
    while (($offset+1)*100<$mcont['response'][0]);
}

function get_last_id()
{
    global $db;
    $qpost=$db->query('SELECT post_id FROM blog_post ORDER BY post_id DESC LIMIT 1');
    $post=$db->fetch($qpost);
    return $post['post_id'];
}

?>