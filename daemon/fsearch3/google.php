<?
require_once('/var/www/daemon/bot/kernel.php');

ignore_user_abort(true);
set_time_limit(0);
ini_set('max_execution_time',0);
ini_set('default_charset','utf-8');
ob_implicit_flush();//проработал добавил кучу камментов
//каждую свою правку прокомментировал
//работает заебись осталось оформить как демона, добавить выдачу как для логов и засунуть на сервер

//необходимо для безопасности работы функции mktime
date_default_timezone_set ( 'Europe/Moscow' ); // локальное время на сервере

/*function parseURL($url) // функция загрузки страницы из инета по ссылке
{
        // create curl resource
        $ch = curl_init();

        // set url
        curl_setopt($ch, CURLOPT_URL, $url);

        //return the transfer as a string
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        // $output contains the output string
        $output = curl_exec($ch);

        // close curl resource to free up system resources
        curl_close($ch);

        return $output;
}*/

//изменял под свои
$google_key='ABQIAAAAAO2IQ2wEk8qzbhBVassz3RQWZvqf6w_6a-zsF36NDtijShSiFRQOmhalY_9HhuT-wxKN6B3fjfmnTA';
//$yandex_key='03.14875897:a3af80c5e63a11257d729ee0287476f6';
$ip='195.19.62.155';

/*

Лимит запросов в сутки	1000 для яндекса
привязаны к аккаунту на http://xml.yandex.ru/ с подтверждением по смс
Документация: http://help.yandex.ru/xml/

Лимит запросов в сутки для гугла неопределен
Документация: http://code.google.com/apis/blogsearch/v1/jsondevguide.html

пример работы с коментами для YouTube
http://gdata.youtube.com/feeds/api/videos/VIDEO_ID/comments
*/

function check_google_content($cont)
{
	return intval(json_decode($cont,true));
}

function getpost_google($text,$ts,$te,$lan,$proxys)
{
	sleep(1);
	$colquery=array();
	global $google_key,$ip;
	//print_r($proxys);
	// $handle = fopen($filename."_google.xml", "a");	
	$mm=array("Jan"=>1,"Feb"=>2,"Mar"=>3,"Apr"=>4,"May"=>5,"Jun"=>6,"Jul"=>7,"Aug"=>8,"Sep"=>9,"Oct"=>10,"Nov"=>11,"Dec"=>12);
	$timemax=0;
	$timemin=0;
	$mas=array( "link" => array() , "time" => array() , "content" => array() , "nick" => array() , "loc" => array());
	$timemosk=$te;//mktime(0,0,0,date('m'),date('j'),date('y')); // текущее время
	$timemosk1=$ts;//$timemosk-86400;
	$tmp_keyword=$text;
	//$text=preg_replace('/[^а-яА-Яa-zA-Z\|]/is',' ',$text);
	//$text=preg_replace('/\|/is',' OR ',$text);
	//$text=preg_replace('/\s+/is',' ',$text);
	//$textgg=urlencode($text);
	//echo $textgg;
	$keyword=preg_replace('/\~+[а-яА-Яa-zA-Z\-\ \,\.]+/isu','',$text);
	$keyword=preg_replace('/[^а-яА-Яa-zA-Z0-9\ \'\-\’\.]/isu','  ',$keyword);
	//$keyword=preg_replace('/\s/isu',' ',$keyword);	
	///echo $keyword;
	$mkeyword=explode('  ',$keyword);
	//print_r($mkeyword);
	$i_proxy=0;
	foreach ($mkeyword as $mkitem)
	{
		echo '/';
		if (trim($mkitem)=='') continue;
		if (mb_strlen(trim($mkitem),'UTF-8')<=2)
		{
			continue;
		}
		$text=$mkitem;
		for ($i=0;$i<8;$i++)
		{//&lr=lang_ru&hl=ru
			//sleep(1);
			do
			{
				echo '.';
				$url = "https://ajax.googleapis.com/ajax/services/search/blogs?v=1.0&q=".urlencode($text)."&key=".($google_key)."&userip=".$proxys[$i_proxy]."&scoring=d&hl=".$lan."&as_qdr=h&rsz=8&start=".$i*8;
				//echo $url;
				// sendRequest
				// note how referer is set manually
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $url);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($ch, CURLOPT_REFERER, 'http://www.wobot.co');
			    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5); // таймаут соединения
			    curl_setopt($ch, CURLOPT_TIMEOUT, 5);        // таймаут ответа
				if ($lan=='ru')
				{
					curl_setopt ($ch, CURLOPT_HEADER, 'accept-language: ru-RU');
					curl_setopt ($ch, CURLOPT_HEADER, 'accept-language: ru');
				}
				elseif ($lan=='az')
				{
					curl_setopt ($ch, CURLOPT_HEADER, 'Accept-Language: az');
					curl_setopt ($ch, CURLOPT_HEADER, 'Accept-Language: az-AZ');
				}
				//echo 'proxy='.$proxys[$i_proxy % count($proxys)].' i='.$i_proxy.' '.$text."\n";
				if (trim($proxys[$i_proxy % count($proxys)])=='')
				{
					sleep(1);
				}
				// curl_setopt($ch, CURLOPT_PROXY, $proxys[$i_proxy % count($proxys)]);
				$body = curl_exec($ch);
				//echo $body;
				//$header  = curl_getinfo( $ch );
				//echo 'GG'.print_r($header).'GG';
				curl_close($ch);
				//$i_proxy++;
				if (check_google_content($body)==0)
				{
					echo '*';
					$i_proxy++;
				}
			}
			while ((check_google_content($body)==0) && ($i_proxy<(count($proxys)/2)));
			//echo $body;
			$json = json_decode($body,true);
			//print_r($json);
			//print_r($head);
			$countg++;
			$count_google+=count($json['responseData']['results']);
			for ($j=0;$j<8;$j++)
			{
				if (check_post($json['responseData']['results'][$j]['content'],$tmp_keyword)==0) continue;
				$timepost=strtotime($json['responseData']['results'][$j]['publishedDate']);
				//echo $timepost.' '.$ts.' '.$te."\n";
				if (($timepost>=$ts) && ($timepost<$te))
				{
					$mas['link'][]=$json['responseData']['results'][$j]['postUrl'];
					$mas['time'][]=$timepost;
					$mas['content'][]=$json['responseData']['results'][$j]['content'];
					$mas['nick'][]=$json['responseData']['results'][$j]['author'];
					$mas['loc'][]="";
				}
				if (count($mas['time'])>100) $mas=post_slice($mas);
			}
			if (count($json['responseData']['results'])<8) break;
			if ($timepost<$ts) break;
		}
	}
	add_source_log('google',intval($count_google));
	//print_r($mas);
	echo "\n";
	return $mas;
}
//(("windows mobile"|"виндоуз мобайл"|"виндовс мобайл"|"win mobile 7"|"win 7 mobile"|"mobile windows 7"|"win7 mobile")|((манго|mango)&(телефон|смартфон|коммуникатор)))~продать~продажа~green
//$m=getpost_google('(windows phone|winphone|wp 7|"wp 7.5"|"phone 7"|"win phone 7"|виндоуз фон|"фон 7"|винфон|виндофон|"телефон windows"|"смартфон на винде"|"телефон на винде"|"коммуникатор на винде"|"смартфон windows"|"телефон microsoft"|вендофон|"телефон на венде"|"виндовс фон")~~скачать~~установка~~игры~~программы~~разработка~~приложений~~WhatsApp',mktime(0,0,0,4,2,2011),mktime(0,0,0,4,3,2012),'ru');
//$m=getpost_google('(Ашманов | Ashmanov)',1358020800,1358163661,'ru',array('81.24.116.46:3128','188.143.232.239:80','82.199.105.217:3128','188.93.20.179:8080','95.31.23.122:3128','79.134.15.145:80','84.17.229.248:3128','84.204.79.228:3128'));


//print_r($m);
?>
