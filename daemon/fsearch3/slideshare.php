<?
require_once('/var/www/daemon/com/config.php');
require_once('/var/www/daemon/com/func.php');
require_once('/var/www/daemon/com/db.php');
require_once('/var/www/daemon/bot/kernel.php');

function check_slideshare_content($cont)
{
	return intval(preg_match('/\<\?xml version=\"1\.0\" encoding\=\"UTF\-8\"\?\>
	\<Slideshows\>/isu',$cont));
}

function get_slideshare($keyword,$ts,$te,$lan,$proxys)
{
	$mlan['ru']='ru';
	$mlan['en']='en';
	$mlan['']='ru';
	$tmp_keyword=$keyword;
	$keyword=preg_replace('/[^а-яА-Яa-zA-Z0-9\ \'\-\’]/isu','  ',$keyword);
	$mkeyword=explode('  ',$keyword);
	//print_r($mkeyword);
	$i_proxy=0;
	foreach ($mkeyword as $item)
	{
		echo '/';
		//sleep(1);
		$count=100;
		do 
		{
			do
			{
				$t=time();
				echo '.';
				$cont=parseUrlproxy('http://www.slideshare.net/api/2/search_slideshows?api_key=fPTAKJFS&ts='.$t.'&hash='.sha1('tr4lAvNb'.$t).'&q='.$item.'&items_per_page=50&sort=latest&lang='.$mlan[$lan],$proxys[$i_proxy]);
				//echo 'http://www.slideshare.net/api/2/search_slideshows?api_key=fPTAKJFS&ts='.$t.'&hash='.sha1('tr4lAvNb'.$t).'&q='.$item.'&items_per_page=50&sort=latest&lang='.$mlan[$lan];
				if (check_slideshare_content($cont)==0)
				{
					$i_proxy++;
				}
			}
			while ((check_slideshare_content($cont)==0) && ($i_proxy<count($proxys)));
			//echo 'http://www.slideshare.net/api/2/search_slideshows?api_key=fPTAKJFS&ts='.$t.'&hash='.sha1('tr4lAvNb'.$t).'&q='.$item.'&items_per_page=50&sort=latest&lang='.$mlan[$lan];
			$mas=simplexml_load_string($cont);
			$json = json_encode($mas);
			$mas= json_decode($json,true);
			//print_r($mas);
			foreach ($mas['Slideshow'] as $k => $i)
			{
				add_source_log('slideshare');
				if (check_post($i['Title'],$tmp_keyword)==0) continue;
				if ((strtotime($i['Updated'])>$ts) && (strtotime($i['Updated'])<$te))
				{
					$outmas['content'][]=$i['Title'];
					$outmas['link'][]=$i['URL'];
					$outmas['time'][]=strtotime($i['Updated']);
				}
				if (count($outmas['time'])>100) $outmas=post_slice($outmas);
			}
			$ii++;
			$count=intval($mas['Meta']['TotalResults']);
		}
		while (intval($ii*50)<intval($count));
	}
	echo "\n";
	//echo $cont;
	//print_r($outmas);
	return $outmas;
}

//get_slideshare('путин',mktime(0,0,0,6,27,2012),mktime(0,0,0,6,29,2012),'ru',array('85.192.166.187:3128','46.50.220.13:3128'));

?>