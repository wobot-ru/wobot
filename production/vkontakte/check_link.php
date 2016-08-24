<?php
if( isset($_REQUEST['link']) )
{
    $link = $_REQUEST['link'];
    $url = parseURLheader($link);
    echo $url;
}

function parseURLheader( $url )
{
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
    curl_setopt($ch, CURLOPT_COOKIE, 'adult_concepts=1' );

    curl_exec( $ch );
    $header  = curl_getinfo( $ch );
    curl_close( $ch );
    return $header['url'];
}

die;
