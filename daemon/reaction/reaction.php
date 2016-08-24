<?

require_once('/var/www/daemon/com/db.php');
require_once('/var/www/daemon/com/config.php');
require_once('/var/www/daemon/bot/kernel.php');

$db=new database();
$db->connect();

ini_set('memory_limit', '2048M');

$order_delta = $_SERVER['argv'][1];
$debug_mode = $_SERVER['argv'][2];
$fp = fopen('/var/www/pids/reaction' . $order_delta . '.pid', 'w');
fwrite($fp, getmypid());
fclose($fp);

function get_posts_tw($link,$id,$id_reaction)
{
	$cont=parseUrl($link);
	// echo $cont;
	$regex='/<small class="time">.*?<a href="(?<link>.*?)" class="tweet-timestamp js-permalink js-nav js-tooltip" title=".*?" ><span class="_timestamp js-short-timestamp " data-aria-label-part="last" data-time="(?<time>\d+)".*?>.*?<p class="js-tweet-text tweet-text" lang="ru" data-aria-label-part="0">(?<cont>.*?)<\/p>/isu';
	preg_match_all($regex, $cont, $out);
	print_r($out);
	foreach ($out['time'] as $key => $item)
	{
		if ($key==0) continue;
		if (!preg_match('/\/'.$id.'\//isu', $out['link'][$key]) && !preg_match('/\/'.$id_reaction.'\//isu', $out['link'][$key])) continue;
		elseif (preg_match('/\/'.$id.'\//isu', $out['link'][$key])) $outpost[]=array('time'=>$item,'link'=>$out['link'][$key],'content'=>strip_tags($out['cont'][$key]),'author'=>$id,'nick_avatar'=>array('reaction_blog_nick'=>$id,'reaction_blog_ico'=>''));
		elseif (preg_match('/\/'.$id_reaction.'\//isu', $out['link'][$key])) $outpost[]=array('time'=>$item,'link'=>$out['link'][$key],'content'=>strip_tags($out['cont'][$key]),'author'=>$id_reaction,'nick_avatar'=>array('reaction_blog_nick'=>$id_reaction,'reaction_blog_ico'=>''));
	}
	print_r($outpost);
	return $outpost;
}

function get_posts_fb($link,$id,$id_reaction)
{
	echo $link.' '.$id.' '.$id_reaction."\n";
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
		elseif ($item['from']['id']==$id) $outpost[]=array('time'=>strtotime($item['created_time']),'content'=>strip_tags($item['message']),'author'=>$id,'nick_avatar'=>array('reaction_blog_nick'=>$item['from']['name'],'reaction_blog_ico'=>''));
		elseif ($item['from']['id']==$id_reaction) $outpost[]=array('time'=>strtotime($item['created_time']),'content'=>strip_tags($item['message']),'author'=>$id_reaction,'nick_avatar'=>array('reaction_blog_nick'=>$item['from']['name'],'reaction_blog_ico'=>''));
	}
	// echo $cont;
	print_r($outpost);
	return $outpost;
}

function get_posts_vk($link,$id,$id_reaction)
{
	$outpost = array();
	preg_match('/http:\/\/vk.com\/wall(?<owner_id>[-]*[0-9]+?)_(?<post_id>[0-9]+)/isu', $link, $out);
	$cont=parseUrl('https://api.vkontakte.ru/method/wall.getComments?owner_id='.$out['owner_id'].'&post_id='.$out['post_id'].'&count=100&preview_length=0&v=5.25&extended=1');
	$arr = json_decode($cont, true);
	//echo count($arr);
	$responce = $arr['response'];
	print_r($responce);
	for($i=0; $i<count($responce['items']); $i++){
		if($responce['items'][$i]['from_id']==$id || $responce['items'][$i]['from_id']==$id_reaction){
			for($j=0; $j<count($responce['profiles']);$j++){
				if($responce['profiles'][$j]['id']==$responce['items'][$i]['from_id']){
					$nick = $responce['profiles'][$j]['first_name'].' '.$responce['profiles'][$j]['last_name'];
					$avatar = $responce['profiles'][$j]['photo_50'];
				}
			}
			$outpost[]=array('time'=>$responce['items'][$i]['date'],'content'=>$responce['items'][$i]['text'],'author'=>$responce['items'][$i]['from_id'], 'nick_avatar'=>array('reaction_blog_nick' => $nick, 'reaction_blog_ico' =>$avatar));
			unset($nick);
			unset($avatar);
		}
	}
	//$outpost[]=array('time'=>$item,'link'=>$out['link'][$key],'content'=>strip_tags($out['cont'][$key]),'author'=>$id_reaction);
	print_r($outpost);
	return $outpost;
}

while (1)
{
	echo 'SELECT * FROM blog_reaction as a LEFT JOIN blog_post as b ON a.post_id=b.post_id LEFT JOIN robot_blogs2 as c ON b.blog_id=c.blog_id WHERE a.reaction_time>'.mktime(0,0,0,date('n'),date('j')-2,date('Y'));
	$qpost=$db->query('SELECT * FROM blog_reaction as a LEFT JOIN blog_post as b ON a.post_id=b.post_id LEFT JOIN robot_blogs2 as c ON b.blog_id=c.blog_id WHERE a.reaction_time>'.mktime(0,0,0,date('n'),date('j')-2,date('Y')-1));
	while ($post=$db->fetch($qpost))
	{
		// if ($post['post_host']!='twitter.com') continue;
		// print_r($post);
		if ($post['post_host']=='twitter.com') $posts=get_posts_tw($post['post_link'],$post['blog_login'],$post['reaction_blog_login']);
		if ($post['post_host']=='facebook.com') $posts=get_posts_fb($post['post_link'],$post['blog_login'],$post['reaction_blog_login']);
		if ($post['post_host']=='vk.com') $posts=get_posts_vk($post['post_link'],$post['blog_login'],$post['reaction_blog_login']);
		else continue;
		// print_r($posts);
		foreach ($posts as $ipost)
		{
			$qisset=$db->query('SELECT * FROM blog_reaction WHERE reaction_content=\''.addslashes($ipost['content']).'\' AND order_id='.$post['order_id'].' LIMIT 1');
			if ($db->num_rows($qisset)==0) 
			{
				echo 'INSERT INTO blog_reaction (post_id,order_id,reaction_content,reaction_time,reaction_blog_login,reaction_blog_info) VALUES ('.$post['post_id'].','.$post['order_id'].',\''.addslashes($ipost['content']).'\',\''.$ipost['time'].'\',\''.addslashes($ipost['author']).'\',\''.addslashes(json_encode(array('reaction_blog_nick'=>$ipost['nick_avatar']['reaction_blog_nick'],'reaction_blog_ico'=>$ipost['nick_avatar']['reaction_blog_ico']))).'\')';
				$db->query('INSERT INTO blog_reaction (post_id,order_id,reaction_content,reaction_time,reaction_blog_login,reaction_blog_info) VALUES ('.$post['post_id'].','.$post['order_id'].',\''.addslashes($ipost['content']).'\',\''.$ipost['time'].'\',\''.addslashes($ipost['author']).'\',\''.addslashes(json_encode(array('reaction_blog_nick'=>$ipost['nick_avatar']['reaction_blog_nick'],'reaction_blog_ico'=>$ipost['nick_avatar']['reaction_blog_ico']))).'\')');
			}
		}
	}
	sleep(600);
}

// $link='https://twitter.com/MedvedevRussia/status/512952833906184192';
// get_posts($link,'MedvedevRussia','_____aleksandr');

?>