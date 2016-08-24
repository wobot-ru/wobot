<?

require_once('/var/www/daemon/com/db.php');
require_once('/var/www/daemon/com/config.php');
require_once('/var/www/daemon/bot/kernel.php');

$db=new database();
$db->connect();

function get_posts_fb($link,$id,$id_reaction)
{
	$regex='/id\=(?<id>\d+)/isu';
	preg_match_all($regex, $link, $out);
	if ($out['id'][0]=='')
	{
		$regex='/\/posts?\/(?<id>\d+)/isu';
		preg_match_all($regex, $link, $out);
	}
	$cont=parseUrl('http://graph.facebook.com/'.$out['id'][0].'/comments');
	$mcont=json_decode($cont,true);
	print_r($mcont);
	foreach ($mcont['data'] as $item)
	{
		if (($item['from']['id']!=$id) && ($item['from']['id']!=$id_reaction)) continue;
		elseif ($item['from']['id']==$id) $outpost[]=array('time'=>strtotime($item['created_time']),'content'=>strip_tags($item['message']),'author'=>$id);
		elseif ($item['from']['id']==$id_reaction) $outpost[]=array('time'=>strtotime($item['created_time']),'content'=>strip_tags($item['message']),'author'=>$id_reaction);
	}
	// echo $cont;
	// print_r($outpost);
	return $outpost;
}

$link='https://www.facebook.com/ironman70.3tri/posts/702357466506656';
get_posts_fb($link,'507383637','100003015595087');

?>