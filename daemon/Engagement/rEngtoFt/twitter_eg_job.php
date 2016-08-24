<?

function get_retweets($url,$cont)
{
	// echo $url."\n";
	// echo $cont;
	$cont=preg_replace('/<\/?strong>/isu','',$cont);
	$cont=preg_replace('/<[^<]*?>/isu','|||',$cont);
	$mcont=explode('|||', $cont);
	// print_r($mcont);
	foreach ($mcont as $item)
	{
		$item=trim($item);
		if ($item=='') continue;
		$regex='/(?<cret>[\d\s\,]+)\s(ретвит|retweet)/isu';
		preg_match_all($regex, $item, $out);
		if ($out['cret'][0]!='') $outm['count']=intval(preg_replace('/[\s\,]/isu','',$out['cret'][0]));
	}
	if (intval($outm['count'])==0) $outm['count']=0;
	$outm['data']['retweet']=intval($outm['count']);
	//print_r($outm);
	return $outm;
}
//get_retweets('http://twitter.com/clubmirnyi/statuses/296304939817390080');
//retweet!!!!
// $cont=parseUrl('http://188.120.239.225/getlist.php');
// $mproxy=json_decode($cont,true);
// print_r(get_retweets('https://twitter.com/MedvedevRussia/status/67326038769799168'));
?>
