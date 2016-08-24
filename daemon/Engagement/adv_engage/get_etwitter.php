<?

// require_once('../com/db.php');
// require_once('../com/config.php');
// require_once('../bot/kernel.php');

// $db=new database();
// $db->connect();

function get_retweets($link)
{
	$header=getHeader($link);
	//echo '!'.$link.'!';
	$regex='/statuse?s?\/(?<id>\d+)$/isu';
	preg_match_all($regex, $header['url'], $out);
	$id=$out['id'][0];
	$cont=parseUrl('https://twitter.com/i/activity/retweeted_popup?id='.$id);
	$regex='/data-screen-name=\\\"(?<nick>[^\"]*?)\\\" data\-name\=/isu';
	preg_match_all($regex, $cont, $out);
	foreach ($out['nick'] as $key => $item)
	{
		$outmas[]='http://twitter.com/'.$item.'/statuses/'.$id;
	}
	//print_r($outmas);
	return $outmas;
}

//print_r(get_retweets('http://twitter.com/kicez/statuses/269769239148236801'));
//print_r(getHeader('http://twitter.com/vipmoskva/status/269708544327180288'));
?>