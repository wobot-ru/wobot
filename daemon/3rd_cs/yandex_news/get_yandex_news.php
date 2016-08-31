<?

function get_news($md5,$token,$start,$end)
{
	do
	{
		$cont=parseURL( 'https://api-lenta.yandex.ru/posts?md5='.$md5.'&items_per_page=20',$token );
		sleep(5);
		$attemp++;
		echo '.';
		// echo $cont;
		$m = json_decode(json_encode((array) simplexml_load_string($cont)),1);
	}
	while (($m['title']=='400: Bad Request')&&($attemp<10));
	print_r($m);
	foreach ($m['post'] as $key => $item)
	{
		$outmas['content'][]=$item['entry']['title'].'. '.$item['entry']['first_line'];
		$outmas['link'][]=$item['entry']['link']['@attributes']['href'];
		$outmas['time'][]=strtotime($item['entry']['issued']);
	}
	return $outmas;
}

// $token='9ecc28acb11d45029df59025d604d713';
// $md5='da89b2126ca0d849ae7cd1d49a19a36b';
// $m=get_news($md5,$token,$start,$end);
// print_r($m);

?>