<?
require_once('/var/www/daemon/bot/kernel.php');
require_once('ch.php');

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

/*function getpost_bing($text,$ts,$te,$lan,$proxys)
{
	$kt=$text;
	$keyword=preg_replace('/[^а-яА-Яa-zA-Z0-9\ \'\-\’]/isu','  ',$text);
	$mkeyword=explode('  ',$keyword);
	$maslan['ru']='ru-RU';
	$maslan['en']='en-US';
	$maslan['az']='az';
	$maslan['']='ru-RU';
	print_r($mkeyword);
	global $app_id;
	$i_proxy=0;
	$i=0;
	foreach ($mkeyword as $it2)
	{
		echo '/';
		if (($it2!=' ') && ($it2!=''))
		{
			do
			{
				//sleep(1);
				do
				{
					echo '.';
					$cont=parseUrlproxy('http://api.search.live.net/json.aspx?AppId='.$app_id.'&Query='.urlencode($it2).'&Sources=web&Version=2.0&Market='.$maslan[$lan].'&Web.Count=50&Web.Offset='.intval($i*50).'&UILanguage='.$maslan[$lan].'&Adult=Moderate&NewsRequest.SortBy=NewsSortOption.Date',$proxys[$i_proxy]);
					//echo 'http://api.search.live.net/json.aspx?AppId='.$app_id.'&Query='.urlencode($it2).'&Sources=web&Version=2.0&Market='.$maslan[$lan].'&Web.Count=50&Web.Offset='.intval($i*50).'&UILanguage='.$maslan[$lan].'&Adult=Moderate&NewsRequest.SortBy=NewsSortOption.Date';
					if (check_bing_content($cont)==0)
					{
						$i_proxy++;
					}
				}
				while ((check_bing_content($cont)==0) && ($i_proxy<count($proxys)));
				//echo 'http://api.search.live.net/json.aspx?AppId='.$app_id.'&Query='.urlencode($text).'&Sources=web&Version=2.0&Market='.$maslan[$lan].'&Web.Count=50&Web.Offset='.intval($i*50).'&UILanguage='.$maslan[$lan].'&Adult=Moderate&NewsRequest.SortBy=NewsSortOption.Date';
				$mas=json_decode($cont,true);
				$count=$mas['SearchResponse']['Web']['Total'];
				foreach ($mas['SearchResponse']['Web']['Results'] as $item)
				{
					if (((strtotime($item['DateTime'])>$ts) && (strtotime($item['DateTime'])<$te)) && ((check_local($item['Title'],$lan)==1) || (check_local($item['Description'],$lan)==1)) && (check_post($item['Title'],$kt)==1))
					{
						$outmas['content'][]=$item['Title'];
						$outmas['link'][]=$item['Url'];
						$outmas['time'][]=strtotime($item['DateTime']);
					}
				}
				$i++;
				//echo $i.' '.$count."\n";
			}
			while (($i*50)<$count);
		}
	}
	echo "\n";
	//print_r($outmas);
	return $outmas;
}*/

function getpost_bing($text,$ts,$te,$lan,$proxys)
{
	$keyword=preg_replace('/\~+[а-яА-Яa-zA-Z\-\ \.\'\"]+/isu','',$text);
	$keyword=preg_replace('/[^а-яА-Яa-zA-Z0-9\-\'\’\ \.]/isu','  ',$keyword);
	$mkeyword=explode('  ',$keyword);
	foreach ($mkeyword as $it_kw)
	{
		$i=0;
		if (mb_strlen(trim($it_kw),'UTF-8')<=3) continue;
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
			$keyValue='QEnvZSBS8i6DNdF0gZZMxIyWFvS3vgpMmqRbkoX7ULg=';
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
				$outmas['link'][]=$item['Url'];
				$outmas['content'][]=$item['Description'];
				$outmas['time'][]=strtotime($item['Date']);
			}
			// print_r($outmas);
			$i++;
			echo strtotime($mres['d']['results'][0]['News'][count($mres['d']['results'][0]['News'])-1]['Date']).' '.$start;
		}
		while (strtotime($mres['d']['results'][0]['News'][count($mres['d']['results'][0]['News'])-1]['Date'])>$start);
	}
	//print_r($outmas);
	return $outmas;
}

//echo 123;
//(("windows mobile"|"виндоуз мобайл"|"виндовс мобайл"|"win mobile 7"|"win 7 mobile"|"mobile windows 7"|"win7 mobile")|((манго|mango)&(телефон|смартфон|коммуникатор)))~продать~продажа~green
//$m=getpost_bing('wobot',mktime(0,0,0,8,1,2012),mktime(0,0,0,8,5,2012),'ru',array('85.192.166.187:3128','46.50.220.13:3128'));
//$m=getpost_google('nokia site:facebook.com',mktime(0,0,0,2,1,2011),mktime(0,0,0,2,3,2012),array('85.192.166.187:3128','46.50.220.13:3128'));

//print_r($m);
?>
