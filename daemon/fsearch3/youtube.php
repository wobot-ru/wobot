<?
//require_once('/var/www/bot/kernel.php');
date_default_timezone_set ( 'Europe/Moscow' );

function parseURL123( $url,$proxy )
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
  curl_setopt ($ch, CURLOPT_HEADER, 'accept-language: ru-RU');
  curl_setopt ($ch, CURLOPT_HEADER, 'accept-language: ru');
  if ($proxy!='')
  {
	//echo 'proxy='.$proxy;
	  // curl_setopt($ch, CURLOPT_PROXY, $proxy);
  }
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

function check_youtube_content($cont)
{
	return intval(json_decode($cont,true));
}

function get_post_yt($kword,$ts,$te,$lan,$proxys)
{
	if ($lan=='')
	{
		$lan='ru';
	}
	$tmp_keyword=$kword;
	$i_proxy=0;
	do
	{
		do
		{
			echo '.';
			$cont=parseUrl123('http://gdata.youtube.com/feeds/api/videos?vq='.urlencode(preg_replace('/\~/is',' -',$kword)).'&orderby=published&start-index='.($c*50+1).'&lr='.$lan.'&max-results=50&alt=json',$proxys[$i_proxy]);
			if (check_youtube_content($cont)==0)
			{
				echo '*';
				$i_proxy++;
			}
		}
		while ((check_youtube_content($cont)==0) && ($i_proxy<count($proxys)));
		//echo 'http://gdata.youtube.com/feeds/api/videos?vq='.urlencode(preg_replace('/\~/is',' -',$kword)).'&orderby=published&start-index='.($c*50+1).'&lr='.$lan.'&max-results=50&alt=json';
		$mas=json_decode($cont,true);
		foreach ($mas['feed']['entry'] as $key => $item)
		{
			$count_youtube++;
			//echo $ts.' '.$item['updated']['$t'].' '.$te."\n";
			//if (((strtotime($item['updated']['$t'])>$ts) && (strtotime($item['updated']['$t'])<$te)) || ((strtotime($item['published']['$t'])>$ts) && (strtotime($item['published']['$t'])<$te)))
			if (check_post($item['title']['$t'].' '.$item['content']['$t'],$tmp_keyword)==0) continue;
			if ((strtotime($item['published']['$t'])>$ts) && (strtotime($item['published']['$t'])<$te))
			{
				//$outmas['id'][]=$item['gd$comments']['gd$feedLink']['href'].'?alt=json';
				if (!in_array($item['link'][0]['href'],$outmas['link']))
				{
					$outmas['link'][]=$item['link'][0]['href'];
					$outmas['content'][]=$item['title']['$t'];
					$outmas['fulltext'][]=$item['title']['$t'].' '.$item['content']['$t'];
					$outmas['time'][]=strtotime($item['published']['$t']);
					$outmas['author'][]=$item['author'][0]['name']['$t'];
					$outmas['viewcount'][]=intval($item['yt$statistics']['viewCount']);
					$outmas['favcount'][]=intval($item['yt$statistics']['favoriteCount']);
				}
			}
			/*if ((strtotime($item['updated']['$t'])>$ts) && (strtotime($item['updated']['$t'])<$te))
			{
				//$outmas['id'][]=$item['gd$comments']['gd$feedLink']['href'].'?alt=json';
				if (!in_array($item['link'][0]['href'],$outmas['link']))
				{
					$outmas['link'][]=$item['link'][0]['href'];
					$outmas['content'][]=$item['title']['$t'];
					$outmas['fulltext'][]=$item['title']['$t'].' '.$item['content']['$t'];
					$outmas['time'][]=strtotime($item['updated']['$t']);
					$outmas['author'][]=$item['author'][0]['name']['$t'];
					$outmas['viewcount'][]=intval($item['yt$statistics']['viewCount']);
					$outmas['favcount'][]=intval($item['yt$statistics']['favoriteCount']);
				}
			}*/
			if (count($outmas['time'])>100) $outmas=post_slice($outmas);
		}
		$c++;
		//sleep(1);
		$count=intval($mas['feed']['openSearch$totalResults']['$t']/50)+1;
		if ($count>5) $count=5;
	}
	while ($c<$count);
	echo "\n";
	add_source_log('youtube',intval($count_youtube));
	//print_r($outmas);
	return $outmas;
}

// get_post_yt('путин',mktime(0,0,0,2,25,2013),mktime(0,0,0,2,27,2013),'ru',array('85.192.166.187:3128','46.50.220.13:3128'));

?>