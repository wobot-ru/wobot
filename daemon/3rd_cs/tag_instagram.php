<?

/*require_once('/var/www/daemon/com/config.php');
require_once('/var/www/daemon/com/func.php');
require_once('/var/www/daemon/com/db.php');
require_once('/var/www/daemon/bot/kernel.php');*/
//require_once('/var/www/daemon/fsearch3/ch.php');

$db = new database();
$db->connect();

// error_reporting(0);
//ignore_user_abort(true);
set_time_limit(0);
ini_set('max_execution_time',0);
ob_implicit_flush();
ini_set("memory_limit", "2048M");
date_default_timezone_set ( 'Europe/Moscow' );


function parseUrlHeader($url){
	$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $url);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($ch, CURLOPT_REFERER, 'http://www.wobot.co');
			    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5); // таймаут соединения
			    curl_setopt($ch, CURLOPT_TIMEOUT, 5);        // таймаут ответа
				$request_headers[]='accept-language: ru-RU';
				$request_headers[]='accept-language: ru';
				curl_setopt($ch, CURLOPT_HTTPHEADER, $request_headers);
				//echo 'proxy='.$proxys[$i_proxy % count($proxys)].' i='.$i_proxy.' '.$text."\n";
				// curl_setopt($ch, CURLOPT_PROXY, $proxys[$i_proxy % count($proxys)]);
				$body = curl_exec($ch);
				//echo $body;

				//$header  = curl_getinfo( $ch );
				//echo 'GG'.print_r($header).'GG';
				curl_close($ch);
				return $body;
}

function get_tag_instagram($grid,$ts,$te)
{
	//$token = '1543782233.1fb234f.9fa10e5a4a08443186b264ae3d127214';
	$mtoken[]='1032401910.ec53e3f.a09b7eceff8c4d1aa86be0b6a867ce25';
	$mtoken[]='1032401910.ec53e3f.a09b7eceff8c4d1aa86be0b6a867ce25';
	$mtoken[]='1032401910.ec53e3f.a09b7eceff8c4d1aa86be0b6a867ce25';
	$mtoken[]='1032401910.ec53e3f.a09b7eceff8c4d1aa86be0b6a867ce25';

	$outmas = array();
	$next_link = '';
	$iter = 0;
	$count = "&count=50";
	$rus = 0;
	//$lang = '&ln=ru&language=ru&lang=ru&hl=ru&locale=ru&allow_lang=ru';
	do{
	    $iter++;
	    if($next_link!=''){
	        $url = $next_link;
	    } else {
	        $url = 'https://api.instagram.com/v1/tags/'.urlencode($grid).'/media/recent?access_token='.$mtoken[$iter%(count($mtoken)-1)];
	        //$url = 'https://api.instagram.com/v1/tags/'.$grid.'/media/recent?access_token='.$token;
	        // $url='https://api.instagram.com/v1/tags/%D0%BA%D0%BE%D1%84%D0%B5%D1%85%D0%B0%D1%83%D1%81/media/recent?access_token=1032401910.ec53e3f.2b04849f03d04054be56981b5718fd48';

	    }
	    // echo $url.$count."\n";
	    $cont=parseURL($url.$count);
	    echo $cont;
	    //$cont=parseUrlHeader($url.$count);
	    //$cont=parseUrlHeader($url.$count);
	    $mcont = json_decode($cont, true);
	    //print_r($mcont);

	    $next_link = $mcont['pagination']['next_url'];
	    $data = $mcont['data'];
	    echo count($data);
	    echo "\n";
	    //die();
	    foreach ($data as $key => $value) {
	    	$time = $data[$key]['caption']['created_time'];
	    	//echo $time.' '.$ts.' '.$te;
	    	//echo "\n";
	    	//die();
	    	if ($time<$ts || $time>($te+86400)) continue;
	        $outmas['link'][] = $data[$key]['link'];
	        $outmas['content'][] = $data[$key]['caption']['text'];
	        $outmas['time'][] = $time;
	        $outmas['engage'][]=0;
			$outmas['adv_engage'][]='';
			$outmas['author_id'][]='';
			$comments = $data[$key]['comments'];
			//print_r($comments);
			if($comments['count']>0){
				foreach ($comments['data'] as $key_c => $value_c) {
					$time_c = $comments['data'][$key_c]['created_time'];
					if ($time<$ts || $time>($te+86400)) continue;
			        $outmas['link'][] = $data[$key]['link'].'#'.$comments['data'][$key_c]['id'];
			        $outmas['content'][] = $comments['data'][$key_c]['text'];
			        $outmas['time'][] = $time_c;
			        $outmas['engage'][]=0;
					$outmas['adv_engage'][]='';
					$outmas['author_id'][]='';
				}
			}
	    }

	    if($time<$ts) break;
	    if($iter>100) break; 
	    //sleep(1);
	} while(isset($mcont['pagination']['next_url']));

	$cont=parseUrl('https://post-cache.tagboard.com/search/'.urlencode($grid).'?count=50');
	$mas=json_decode($cont,true);
	// print_r($mas);
	echo $word."\n";
	// print_r($mas);
	foreach ($mas['posts'] as $item)
	{
		if (!preg_match('/instagram\.com/isu', $item['permalink'])) continue;
		echo $item['url']."\n";
		// print_r(array(
		// 			'time' => $item['post_time'],
		// 			'content' => $item['text'],
		// 			'link' => $item['permalink'],
		// 			'author_id' => $item['user_id'],
		// 			'author_name' => $item['user_real_name']
		// 		));
		$time=$item['post_time'];
		if ($time<$ts || $time>($te+86400)) continue;
        $outmas['link'][] = $item['permalink'];
        $outmas['content'][] = $item['text'];
        $outmas['time'][] = $item['post_time'];
        $outmas['engage'][]=0;
		$outmas['adv_engage'][]='';
		$outmas['author_id'][]='';
	}


	print_r($outmas);
	//echo "Calls: ".$iter."\n";
	return $outmas;
}

//1417388021 #vtb
//get_tag_instagram('vtb',1417387000,1417389999);

?>