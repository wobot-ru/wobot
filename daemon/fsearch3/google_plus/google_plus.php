<?

require_once('/var/www/daemon/com/config.php');
require_once('/var/www/daemon/com/func.php');
require_once('/var/www/daemon/com/db.php');
require_once('/var/www/daemon/bot/kernel.php');

error_reporting(0);

function get_google_plus($keyword,$ts,$te,$lan,$proxys)
{
	global $redis;
	$google_tokens=json_decode($redis->get('at_gp'),true);
	// $google_tokens[0]='AIzaSyAW5811BL1JjoO1-4wy9CCR3c-oe7A5BnM';
	// $google_tokens[1]='AIzaSyAYJ0-HHNSPTZ6km29PlEn2sGuSOXR-zRU';
	// $google_tokens[2]='AIzaSyATWJrPep2aprG8dPbqAh4VIx8rFao2Sbk';
	// $google_tokens[3]='AIzaSyCfdRWKvjrYONugfSw2pMx_i1qsRWWM-A4';
	// $google_tokens[4]='AIzaSyDxv7FwS-ixbHF12yYDDb1O4dKv-HUTKP4';
	// $google_tokens[5]='AIzaSyAc7X8b27x3YmzgLF2UQOTx-9mG6e_C9kc';
	// $google_tokens[6]='AIzaSyAYClQD_sbE5ubwpuIYqjNhQWrpXsC1LHg';
	// $google_tokens[7]='AIzaSyBAn0cWxq4ThF5R759Vh6LTfxFslWYhpwc';
	// $google_tokens[8]='AIzaSyDwqIJwBACC_if5TAeGBZzzEsViRkWUMcU';
	// $google_tokens[9]='AIzaSyAnweaAhuk3j0hUOVXr5b4YwRpo3vFUxTU';
	// print_r($google_tokens);
	$mas_lan['ru']='ru';
	$mas_lan['en']='en_US';
	$mas_lan['az']='az';
	$tmp_keyword=$keyword;
	$keyword=preg_replace('/\~+[а-яА-Яa-zA-Z\-\ \,\.]+/isu','',$keyword);
	$keyword=preg_replace('/[^а-яА-Яa-zA-Z0-9\ \'\-\’\.]/isu','  ',$keyword);
	$mkeyword=explode('  ',$keyword);
	//print_r($mkeyword);
	$i_proxy=0;
	foreach ($mkeyword as $word)
	{
		echo '/';
		if ((trim($word)!='') && (trim($word)!=' ') && (mb_strlen($word,'UTF-8')>3))
		{
			//$i_proxy=0;
			$pageToken='';
			$iterator=0;
			//echo $word."\n";
			do
			{
				do
				{
					//echo $proxys[$i_proxy];
					//sleep(1);
					// echo 'https://www.googleapis.com/plus/v1/activities?query='.(urlencode($word)).'&key='.$google_tokens[date('i') % count($google_tokens)].'&maxResults=20&orderBy=recent&language='.$mas_lan[$lan].'&pageToken='.$pageToken."\n\n\n";
					echo '.';
					$cont=parseUrlproxy('https://www.googleapis.com/plus/v1/activities?query='.(urlencode($word)).'&key='.$google_tokens[date('i') % count($google_tokens)].'&maxResults=20&orderBy=recent&language='.$mas_lan[$lan].'&pageToken='.$pageToken,$proxys[$i_proxy]);
					//echo 'https://www.googleapis.com/plus/v1/activities?query='.(urlencode($word)).'&key='.$google_tokens[date('i') % 10].'&maxResults=20&orderBy=recent&language='.$mas_lan[$lan].'&pageToken='.$pageToken."\n\n\n\n";
					//echo '!'.$cont.'!';
					$mas=json_decode($cont,true);
					$pageToken=$mas['nextPageToken'];
					if ((count($mas['error'])!=0) || ($cont==''))
					{
						echo '*';
						$i_proxy++;
					}
				}
				while ((count($mas['error'])!=0) && ($i_proxy<count($proxys)) || (($cont=='') && ($i_proxy<count($proxys))));
				foreach ($mas['items'] as $key => $item) 
				{
					$count_gp++;
					if (check_post($item['title'].' '.$item['object']['content'].' '.$item['object']['attachments'][0]['displayName'].' '.$item['object']['attachments'][0]['content'],$tmp_keyword)==0) continue;
					$outmas['fulltext'][]=strip_tags(preg_replace('/\s+/',' ',$item['title'].' '.$item['object']['content'].' '.$item['object']['attachments'][0]['displayName'].' '.$item['object']['attachments'][0]['content']));
					//if (trim($outmas['fulltext'][count($outmas['fulltext'])-1])=='')
					{
						$regex='/^(?<st>.{0,150})[^а-яa-zё]/isu';
						preg_match_all($regex, trim($outmas['fulltext'][count($outmas['fulltext'])-1]), $out);
						$short_text=trim($out['st'][0]).'...';
					}
					$outmas['content'][]=$short_text;
					$outmas['link'][]=$item['url'];
					$outmas['time'][]=strtotime($item['published']);
					$outmas['nick'][]=$item['actor']['displayName'];
					$outmas['ico'][]=$item['actor']['image']['url'];
					$outmas['author_id'][]=$item['actor']['id'];
					if (count($outmas['time'])>100) $outmas=post_slice($outmas);
				}
				//echo strtotime($mas['items'][count($mas['items'])-1]['published']).' '.$ts."\n";
				$iterator++;
				if ($iterator>5)
				{
					$mas['items']=array();
				}
			}
			while ((count($mas['items'])!=0) && (strtotime($mas['items'][count($mas['items'])-1]['published'])>$ts));
		}
	}
	add_source_log('google_plus',intval($count_gp));
	echo "\n";
	// print_r($outmas);
	return $outmas;
}

// get_google_plus('путин',mktime(0,0,0,5,16,2013),mktime(0,0,0,5,17,2013),'ru',array('212.119.97.198:3128','85.234.22.126:3128','27.50.22.186:8080','5.192.166.187:3128','46.50.220.13:3128'));
//echo parseUrlproxy('https://www.googleapis.com/plus/v1/activities?query=Ilya&key=AIzaSyCbalRmnHpMyOHn2xiKVFvkf_sJcjI0SMY&maxResults=20&orderBy=recent&language=ru&pageToken=','27.50.22.186:8080');
?>