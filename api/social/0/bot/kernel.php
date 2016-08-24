<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

function mb_convert($cont,$chset)
{
	preg_match_all('/charset=([-a-z0-9_]+)/isu',$cont,$charset);
	if ($charset[1][0]=='')
	{
		preg_match_all('/charset=([-a-z0-9_]+)/is',$cont,$charset);
	}
	//print_r($charset);
	if (($charset[1][0]!='') || ($charset[1][0]!=$chset))
	{
		if ($charset[1][0]!=$chset)
		{
			$cont=iconv($charset[1][0], $chset, $cont);
		}
	}
	return $cont;
}

function run_crawling($gr_id,$type)
{
	//$fp = fopen('/Applications/MAMP/htdocs/stgit/stools/st.log', 'a');
	$fp = fopen('/Applications/MAMP/htdocs/sts/st.log', 'a');
	fwrite($fp, 'start: '.date('r')."\n");
	fclose($fp);

	$descriptorspec=array(
		0 => array("file","/dev/null","a"),
		1 => array("file","/dev/null","a"),
		2 => array("file","/dev/null","a")
		);

	$cwd='/Applications/MAMP/htdocs/sts/';
	$end=array();
echo 'php /Applications/MAMP/htdocs/sts/get_vk'.($type=='acc'?'_account':'').'.php '.intval($gr_id).' &';
	$process=proc_open('php /Applications/MAMP/htdocs/sts/get_vk'.($type=='acc'?'_account':'').'.php '.intval($gr_id).' &',$descriptorspec,$pipes,$cwd,$end);
	/*$process=proc_open('php /var/www/api/social/0/get_vk'.($type=='acc'?'_account':'').'.php '.intval($gr_id).' &',$descriptorspec,$pipes,$cwd,$end);*//* or {
		echo json_encode(array('status'=>'fail'), true);
		die();
	};*/
	
	if (is_resource($process))
	{
		//echo 'return: '.$return_value=proc_close($process);
		if (intval($return_value=proc_close($process))==0) echo '';//json_encode(array('status'=>'ok'), true);
	}
}

/*function parseURL($url)
{
        // create curl resource
        $ch = curl_init();

        // set url
        curl_setopt($ch, CURLOPT_URL, $url);

        //return the transfer as a string
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

		//-----------FUCKING SHIT OPTIONS!!!!!!!!----------
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	    curl_setopt($ch, CURLOPT_ENCODING, "");
		curl_setopt($ch, CURLOPT_TIMEOUT, 120);
	    curl_setopt($ch, CURLOPT_FAILONERROR, 1);
		curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
		curl_setopt($ch, CURLOPT_MAXREDIRS, 10);

		//-------------------------------------------------

        // $output contains the output string
        $output = curl_exec($ch);

        // close curl resource to free up system resources
        curl_close($ch);

        return $output;
}*/

function parseURL( $url )
{
	global $proxys;
	do
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

		curl_setopt($ch, CURLOPT_PROXY, $proxys[$attemp]);

		$content = curl_exec( $ch );
		$err     = curl_errno( $ch );
		$errmsg  = curl_error( $ch );
		$header  = curl_getinfo( $ch );
		curl_close( $ch );
		$attemp++;
		sleep(1);
	}
	while (($header['http_code']!=200) && ($attemp<10));
	/*$header['errno']   = $err;
	$header['errmsg']  = $errmsg;
	$header['content'] = $content;
	return $header;*/
	return $content;
}

mb_detect_order("windows-1251, UTF-8");
function loadNprepare($url,$encod='') {
        $content        = file_get_contents(urlencode($url));
        /*if (!empty($content)) {
                if (empty($encod))
                        $encod  = mb_detect_encoding($content);
                $headpos        = mb_strpos($content,'<head>');
                if (FALSE=== $headpos)
                        $headpos= mb_strpos($content,'<HEAD>');
                if (FALSE!== $headpos) {
                        $headpos+=6;
                        $content = mb_substr($content,0,$headpos) . '<meta http-equiv="Content-Type" content="text/html; charset='.$encod.'">' .mb_substr($content,$headpos);
                }
		$encod  = mb_detect_encoding($content);
                $content=mb_convert_encoding($content, 'UTF-8', $encod);
        }*/
        $dom = new DomDocument;
        $res = @$dom->loadHTML($content);
        if (!$res) return FALSE;
        return $dom;
}

// Определение UTF8
function detectUTF8($string)
{
        return preg_match('%(?:
        [\xC2-\xDF][\x80-\xBF]        # non-overlong 2-byte
        |\xE0[\xA0-\xBF][\x80-\xBF]               # excluding overlongs
        |[\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}      # straight 3-byte
        |\xED[\x80-\x9F][\x80-\xBF]               # excluding surrogates
        |\xF0[\x90-\xBF][\x80-\xBF]{2}    # planes 1-3
        |[\xF1-\xF3][\x80-\xBF]{3}                  # planes 4-15
        |\xF4[\x80-\x8F][\x80-\xBF]{2}    # plane 16
        )+%xs', $string);
}

// перевод cp1251 - utf8
function cp1251_utf8( $sInput )
{
    $sOutput = "";

    for ( $i = 0; $i < strlen( $sInput ); $i++ )
    {
        $iAscii = ord( $sInput[$i] );

        if ( $iAscii >= 192 && $iAscii <= 255 )
            $sOutput .=  "&#".( 1040 + ( $iAscii - 192 ) ).";";
        else if ( $iAscii == 168 )
            $sOutput .= "&#".( 1025 ).";";
        else if ( $iAscii == 184 )
            $sOutput .= "&#".( 1105 ).";";
        else
            $sOutput .= $sInput[$i];
    }

    return $sOutput;
}

// финальная функция перевода в utf8
function encoding($string){
    if (function_exists('iconv')) {
        if (@!iconv('utf-8', 'cp1251', $string)) {
            $string = iconv('cp1251', 'utf-8', $string);
        }
        return $string;
    } else {
        if (detectUTF8($string)) {
            return $string;
        } else {
            return cp1251_utf8($string);
        }
    }
}
//echo encoding($string);

?>
