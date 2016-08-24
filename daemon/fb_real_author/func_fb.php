<?

// require_once('/var/www/bot/kernel.php');

// $redis = new Redis();    
// $redis->connect('127.0.0.1');

function get_author($link)
{
	global $mat;
	if (!preg_match('/id=\d+\&story_fbid=\d+/isu', $link))
	{
		$regex='/fbid=(?<id>\d+)/isu';
		preg_match_all($regex, $link, $out);
		$cont=parseUrl('https://graph.facebook.com/'.$out['id'][0].'?access_token='.$mat[rand(0,count($mat)-1)]);
		$mcont=json_decode($cont,true);
		$outmas['id']=$mcont['from']['id'];
		print_r($outmas);
	}
	else
	{
		$regex='/id=(?<id>\d+)\&story_fbid=(?<fbid>\d+)/isu';
		preg_match_all($regex, $link, $out);
		// print_r($out);
		// echo 'https://graph.facebook.com/'.$out['id'][0].'_'.$out['fbid'][0].'?access_token='.$mat[rand(0,count($mat)-1)]."\n";
		$cont=parseUrl('https://graph.facebook.com/'.$out['id'][0].'_'.$out['fbid'][0].'?access_token='.$mat[rand(0,count($mat)-1)]);
		$mcont=json_decode($cont,true);
		$outmas['id']=$mcont['from']['id'];
		print_r($outmas);
	}
	return $outmas;
}

// $mat=$redis->get('at_fb');
// get_author('https://www.facebook.com/permalink.php?id=1531396130425203&story_fbid=1550323785199104');

?>