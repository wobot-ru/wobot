<?

// require_once('/var/www/bot/kernel.php');
// error_reporting(0);

function get_comment_youtube($id,$ts,$te)
{
	do
	{
		echo '.';
		$cont=parseUrl('http://gdata.youtube.com/feeds/api/videos/'.$id.'/comments?alt=json&orderby=published&max-results=50&start-index='.($c*50+1));
		// echo 'http://gdata.youtube.com/feeds/api/videos/'.$id.'/comments?alt=json&orderby=published&max-results=50&start-index='.($c*50+1);
		// $cont=parseUrl('http://gdata.youtube.com/feeds/api/videos?vq='.urlencode(preg_replace('/\~/is',' -',$kword)).'&orderby=published&start-index='.($c*50+1).'&lr='.$lan.'&max-results=50&alt=json');
		$mas=json_decode($cont,true);
		foreach ($mas['feed']['entry'] as $key => $item)
		{
			$time=strtotime($item['published']['$t']);
			if (($time>$ts) && ($time<$te))
			{
				//$outmas['id'][]=$item['gd$comments']['gd$feedLink']['href'].'?alt=json';
				$mid=explode('/', $item['id']['$t']);
				$link='http://www.youtube.com/comment?lc='.$mid[count($mid)-1];
				if (!in_array($link,$outmas['link']))
				{
					$item['content']['$t']=preg_replace('/\s+/isu', ' ', $item['content']['$t']);
					$outmas['link'][]=$link;
					$outmas['content'][]=$item['content']['$t'];
					$outmas['fulltext'][]=$item['content']['$t'];
					$outmas['time'][]=$time;
					$outmas['author'][]=$item['author'][0]['name']['$t'];
				}
			}
			$last_time=$time;
		}
		$c++;
		sleep(1);
		// echo $last_time.' '.$ts;
	}
	while ((count($mas['feed']['entry'])!=0)&&($last_time>$ts));
	// print_r($outmas);
	return $outmas;
}

// get_comment_youtube('QNsonWAaFk4',mktime(0,0,0,1,1,2012),mktime(0,0,0,1,1,2014));

?>