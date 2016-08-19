<?
function parseURL( $url )
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
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 120); // таймаут соединения
  curl_setopt($ch, CURLOPT_TIMEOUT, 120);        // таймаут ответа
  curl_setopt($ch, CURLOPT_MAXREDIRS, 10);       // останавливаться после 10-ого редиректа
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

        if ($_POST['url']!='')
        {
        $cont=parseUrl($_POST['url']);
        //echo iconv('UTF-8','windows-1251',$cont);
        $regex=$_POST['regexp'];
        preg_match_all($regex,$cont,$out);
        print_r($out);
        //return $out['data'][0];
        }

?>
<html>
<body style="padding: 0; margin: 0;">
<center>
<table width="100%" height="100%" cellpadding="0" cellspacing="0">
<tr>
<td>
<form method="post">
<input type="text" value="<?=$_POST['url']?>" name="url" style="width: 950px; border: 1px solid #aaa; padding: 5px; margin: 5px;"><input type="submit" value=">>" style="width: 45px; margin: 0; padding: 0; border: 1px solid #aaa;"><br>
<textarea name="regexp" style="width: 1000px; height: 100px; border: 1px solid #aaa; padding: 5px; margin: 5px;"><?=($_POST['regexp']!='')?$_POST['regexp']:'/.*/is'?></textarea>
</form>
</td>
</tr>
</tr>
<td>
<textarea name="html" style="width: 1000px; height: 100px; border: 1px solid #aaa; margin: 5px; padding: 5px;">
<?=$_POST['eval']?>
</textarea>
</td>
</tr>
<tr>
<td>
<textarea name="html" style="width: 1000px; height: 500px; border: 1px solid #aaa; margin: 5px; padding: 5px;">
<?
	eval($_POST['eval']);
	print_r($out);
?>
</textarea>
</td>
</tr>
</table>
</center>
</html>
