<?
	require_once('/var/www/daemon/bot/kernel.php');
	require_once('/var/www/daemon/com/config.php');
	require_once('/var/www/daemon/com/db.php');
	

	error_reporting(E_ERROR);
	ignore_user_abort(true);
	set_time_limit(0);
	ini_set('max_execution_time',0);
	ini_set('default_charset','utf-8');
	ob_implicit_flush();

	$db = new database();
	$db->connect();


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
			$outpost[]=array('time'=>$responce['items'][$i]['date'],'content'=>$responce['items'][$i]['text'],'author'=>$responce['items'][$i]['from_id'], 'nick_avatar'=>json_encode(array('reaction_blog_nick' => $nick, 'reaction_blog_ico' =>$avatar)));
			unset($nick);
			unset($avatar);
		}
	}
	//$outpost[]=array('time'=>$item,'link'=>$out['link'][$key],'content'=>strip_tags($out['cont'][$key]),'author'=>$id_reaction);
	print_r($outpost);
	return $outpost;
}

		get_posts_vk('http://vk.com/wall-11992392_5342129','178765531','190854060');
?>