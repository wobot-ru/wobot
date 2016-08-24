<?

function get_google_news($query,$start,$end)
{
	$cont=parseUrl('http://news.google.ru/news?js=0&cf=all&ned=ru_ru&hl=ru&q='.urlencode($query).'&output=rss');
	$m = json_decode(json_encode((array) simplexml_load_string($cont)),1);
	print_r($m);
	foreach ($m['channel']['item'] as $key => $item)
	{
		$outmas['content'][]=preg_replace('/\s+/isu',' ',preg_replace('/(\<[^\<]*?\>)/isu',' ',$item['title'].' '.$item['description']));
		$outmas['time'][]=strtotime($item['pubDate']);
		$regex='/\&url\=(?<link>.*)/isu';
		preg_match_all($regex, $item['link'], $out);
		$outmas['link'][]=$out['link'][0];
	}
	return $outmas;
	// print_r($outmas);
}

// get_google_news('( открытие /+1 брокер ) | ( брокерский /+1 дом /+1 открытие ) | ( БД & открытие )',mktime(0,0,0,1,1,2013),mktime(0,0,0,1,1,2014));

?>