<?
function parseURL( $url )
{
  $uagent = "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13";
  //$uagent = "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.1.4322)";

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

function parseUrl1($word)
{
  $uagent = "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13";
$keyword='мама';
$keyword=urlencode(iconv('utf-8','windows-1251',$keyword));
$url='http://aktobe.flybb.ru/search.php?mode=results';
$postvars='search_keywords='.$keyword.'&search_terms=any&search_author=&search_forum=-1&search_time=0&search_fields=all&search_cat=-1&sort_by=0&sort_dir=DESC&show_results=posts&return_chars=-1';
$uagent = "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13";
$ch = curl_init( $url );
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);   // возвращает веб-страницу
curl_setopt($ch, CURLOPT_HEADER, 0);           // не возвращает заголовки
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);   // переходит по редиректам
curl_setopt($ch, CURLOPT_POSTFIELDS    ,$postvars);
curl_setopt($ch, CURLOPT_ENCODING, "");        // обрабатывает все кодировки
curl_setopt($ch, CURLOPT_USERAGENT, $uagent);  // useragent
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 120); // таймаут соединения
curl_setopt($ch, CURLOPT_TIMEOUT, 120);        // таймаут ответа
curl_setopt($ch, CURLOPT_MAXREDIRS, 10);       // останавливаться после 10-ого редиректа
$content = curl_exec( $ch );
$err     = curl_errno( $ch );
$errmsg  = curl_error( $ch );
$header  = curl_getinfo( $ch );
curl_close( $ch );
 /*$header['errno']   = $err;
 $header['errmsg']  = $errmsg;
  $header['content'] = $content;*/
  return $content;
}


function getPost()
{
$html=parseUrl1();
//echo $html;
//$html=iconv('windows-1251','UTF-8',$html);
//echo $html;
$html=preg_replace('/.*<\/head>/is', '', $html);
$html=preg_replace('/<script.*?>.*?<\/script>/is', '', $html);
$html=preg_replace('/\n/is','',$html);
$html=preg_replace('/<br>/is',' ',$html);
$html=preg_replace('/<br \/>/is',' ',$html);
$html=preg_replace('/<a href=\"(?<link>.*?)\".*?>/is','hrefgg=$1|',$html);
$html=preg_replace('/<.*?>/is','|',$html);
$html=preg_replace('/&nbsp;/is','|',$html);
//echo $html;
$mas=explode('|',$html);
//print_r($mas);
foreach ($mas as $key => $item)
{
	$item=preg_replace('/\s\s/is','',$item);
	$item=preg_replace('/\s\s/is','',$item);
	$item=preg_replace('/\s\s/is','',$item);
	$item=preg_replace('/\s\s/is','',$item);
	$regex='/(?<data>\s)/is';
	preg_match_all($regex,$item,$out);
	if (((strlen($item)>10) && ($item!=' ') && ($item!='') && ($item!='  ') && ($out['data'][0]!='')) || (strpos($item,'hrefgg')!==false))
	{
		echo '|'.$item.'|<br>';
		$regex='/(?<d>[0-9])/is';
		preg_match_all($regex,$item,$ou);
		if (strpos($item,'hrefgg')!==false)
		{
			$type[$key]='l';
		}
		else
		if ((count($ou['d'])>4) && (strlen($item)<=50))
		{
			$type[$key]='d';
		}
		else
		if (strlen($item)>50)
		{
			$type[$key]='p';
		}
		else
		{
			$type[$key]='f';
		}
	}
}
print_r($type);
foreach ($type as $key => $item)
{
	if ($item=='d')
	{
		echo $item.' |'.$mas[$key].'|<br>';
	}
	else
	if ($item=='p')
	{
		echo $item.' '.$mas[$key].'<br>';
	}
	else
	if ($item=='l')
	{
		echo $item.' '.$mas[$key].'<br>';
	}
	else
	{
		echo $item.'<br>';
	}
}
//print_r($mas);


/*$cont=parseUrl('http://kemdetki.ru/search/?FORUM=1&keywords=%D0%BF%D0%BE%D0%BC%D0%BE%D1%89%D1%8C&x=0&y=0');
$cont=preg_replace('/.*<\/head>/is', '', $cont);
$cont=preg_replace('/<script.*?>.*?<\/script>/is', '', $cont);
$cont=preg_replace('/\s/is','',$cont);
$cont=preg_replace('<br>','gg',$cont);
$cont=preg_replace('/<.*?>/is','|',$cont);
$mas=explode('|',$cont);
foreach ($mas as $item)
{
	if (strlen($item)!=0)
	{
		$item=preg_replace('/\s\s/is','',$item);
		echo '|'.$item."|\n";
	}
}*/


//echo $cont;
}
getPost();
?>
