<?
require_once('/var/www/daemon/bot/kernel.php');
require_once('/var/www/daemon/com/infix.php');
// require_once('../ch.php');

ignore_user_abort(true);
set_time_limit(0);
ini_set('max_execution_time',0);
ini_set('default_charset','utf-8');
ob_implicit_flush();//проработал добавил кучу камментов

date_default_timezone_set ( 'Europe/Moscow' ); // локальное время на сервере

$app_id='QEnvZSBS8i6DNdF0gZZMxIyWFvS3vgpMmqRbkoX7ULg=';

function check_bing_content($cont)
{
	return intval(json_decode($cont,true));
}

function getpost_bing($text,$ts,$te,$lan,$proxys)
{
	global $app_id;
	$keyword=preg_replace('/\~+[а-яА-Яa-zA-Z\-\ \.\'\"]+/isu','',$text);
	$keyword=preg_replace('/[^а-яА-Яa-zA-Z0-9\-\'\’\ \.]/isu','  ',$keyword);
	$mkeyword=explode('  ',$keyword);
	foreach ($mkeyword as $it_kw)
	{
		$i=0;
		if (mb_strlen(trim($it_kw),'UTF-8')<=4) continue;
		do
		{
			$query = http_build_query(
				array(
		   			'Sources' => "'news'", 
		   			'Query' => "'".$it_kw."'",
		   			'$format' => "json",
		   			'$skip' => intval($i*15),
		   			'NewsSortBy' => "'Date'"
		   			)
				);
		  
			$keyName='accountKey';
			$keyValue=$app_id;
			$accountKey = base64_encode($keyName.':'.$keyValue);

			$options = array(
					'http' =>array(
						'method' => 'GET',
			    		'header' => array('Authorization: Basic '.$accountKey, 'Content-type: application/json')
			    		)
			   		);
			$context  = stream_context_create($options);
			$results = file_get_contents('https://api.datamarket.azure.com/Data.ashx/Bing/Search/v1/Composite?'.$query, false, $context);
			$mres=json_decode($results,true);
			// print_r($mres);
			foreach ($mres['d']['results']['0']['News'] as $key => $item)
			{
				add_source_log('bing');
				if (check_post($item['Description'],$text)==0) continue;
				$outmas['link'][]=$item['Url'];
				$outmas['content'][]=$item['Description'];
				$outmas['time'][]=strtotime($item['Date']);
			}
			// print_r($outmas);
			$i++;
			// echo strtotime($mres['d']['results'][0]['News'][count($mres['d']['results'][0]['News'])-1]['Date']).' '.$start;
		}
		while (strtotime($mres['d']['results'][0]['News'][count($mres['d']['results'][0]['News'])-1]['Date'])>$start);
	}
	// print_r($outmas);
	return $outmas;
}

//echo 123;
//(("windows mobile"|"виндоуз мобайл"|"виндовс мобайл"|"win mobile 7"|"win 7 mobile"|"mobile windows 7"|"win7 mobile")|((манго|mango)&(телефон|смартфон|коммуникатор)))~продать~продажа~green
// $m=getpost_bing('wobot',mktime(0,0,0,1,1,2013),mktime(0,0,0,8,5,2013),'ru',array('85.192.166.187:3128','46.50.220.13:3128'));
//$m=getpost_google('nokia site:facebook.com',mktime(0,0,0,2,1,2011),mktime(0,0,0,2,3,2012),array('85.192.166.187:3128','46.50.220.13:3128'));

//print_r($m);
?>
