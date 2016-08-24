<?
/*
$link = mysql_connect('localhost', 'starindex', 'JFHsvosd');
mysql_query("SET character_set_results=utf8", $link);
mysql_query("SET character_set_client=utf8", $link);
mysql_query("SET character_set_connection=utf8", $link);
mb_language('uni');
mb_internal_encoding('UTF-8');
$db = mysql_select_db('starindex', $link);
mysql_query("set names 'utf8'",$link);
$cif=1;
*/

function mb_convert($cont,$chset)
{
	preg_match_all('/charset=([-a-z0-9_]+)/is',$cont,$charset);
	if (($charset[1][0]!='') || ($charset[1][0]!=$chset))
	{
		if ($charset[1][0]!=$chset)
		{
			$cont=iconv($charset[1][0], $chset, $cont);
		}
	}
	return $cont;
}


function parseURL( $url )
{
	global $cif;
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
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 120); // таймаут соединения
  curl_setopt($ch, CURLOPT_TIMEOUT, 120);        // таймаут ответа
  curl_setopt($ch, CURLOPT_MAXREDIRS, 10);       // останавливаться после 10-ого редиректа

  //curl_setopt($ch,CURLOPT_INTERFACE, "eth1:".intval($cif));
  //$cif++;
  //if ($cif==30) $cif=1;

  //curl_setopt($ch, CURLOPT_COOKIEJAR, "z://coo.txt");
  //curl_setopt($ch, CURLOPT_COOKIEFILE,"z://coo.txt");

  $content = curl_exec( $ch );
  $err     = curl_errno( $ch );
  $errmsg  = curl_error( $ch );
  $header  = curl_getinfo( $ch );
  curl_close( $ch );

  /*$header['errno']   = $err;
  $header['errmsg']  = $errmsg;
  $header['content'] = $content;
  return $header;*/
  return $content;
}
?>
