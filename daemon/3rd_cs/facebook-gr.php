<?
// require_once('/var/www/daemon/com/config.php');
// require_once('/var/www/daemon/com/func.php');
// require_once('/var/www/daemon/com/db.php');
// require_once('/var/www/daemon/bot/kernel.php');
//require_once('lcheck.php'); ///???

$redis=new Redis() or die("Can'f load redis module.");
$redis->connect('127.0.0.1');

date_default_timezone_set('Europe/Moscow');
// error_reporting(E_ERROR);
ignore_user_abort(true);
set_time_limit(0);
ini_set('max_execution_time',0);
ini_set('default_charset','utf-8');
ob_implicit_flush();
$access_token_fb='CAAJ68ASJQwQBAHL2ESIoqojdp4edYotgZCP0L5mYQe38rrVZBZBS1hxZAb1HFwe22xJ2dkQowB5IxAptYMEebqeSaa3ZAHaIwpwyaagHRXqIo2SXyIJPU1DQeVoZCZClRRn5yZBRr7fSeyuOV3CIBuZCxTKrvOA401OxGtqokNUjOnySZCAGdFr55KtjbfjdvGHmIZD';

function get_facebook_group($name,$ts,$te,$lan)
{	
	global $access_token_fb,$redis;
	$cont_token_fb=$redis->get('at_fb');
	$mcont_token_fb=json_decode($cont_token_fb,true);
	$access_token_fb=$mcont_token_fb[0];
	do
	{
		echo '.';
		$attemp=0;
		do
		{
			// echo 'https://graph.facebook.com/'.$name.'/feed?access_token='.$access_token_fb.'&limit=100&offset='.intval($offset*100)."\n";
			$cont=parseUrl('https://graph.facebook.com/'.$name.'/feed?access_token='.$access_token_fb.'&limit=100&offset='.intval($offset*100));
			$mas=json_decode($cont,true);
			//print_r($mas);
			$attemp++;
		}
		while (($mas['error']['code']!='')&&($attemp<3));
		echo '/';
		// print_r($mas);
		// die();
		sleep(1);
		foreach ($mas['data'] as $key => $item)
		{
			if ((strtotime($item['created_time'])>=$ts) && (strtotime($item['created_time'])<=$te))
			{			
				$out['time'][]=strtotime($item['created_time']);
				$ids=explode('_',$item['id']);
				$out['link'][]='http://www.facebook.com/'.$ids[0].'/posts/'.$ids[1];
				$post_content='';
				if (strlen($item['message'])>0) $post_content.=' '.($item['message']);
				elseif (strlen($item['caption'])>0) $post_content.=' '.($item['caption']);
				elseif (strlen($item['description'])>0) $post_content.=' '.($item['description']);
				elseif (strlen($item['name'])>0) $post_content.=' '.($item['name']);
				elseif (strlen($item['caption'])>0) $post_content.=' '.($item['caption']);
				else $post_content='http://www.facebook.com/'.$ids[0].'/posts/'.$ids[1];
				$out['content'][]=$post_content;
				$out['author_id'][]=$item['from']['id'];
				$out['author_name'][]=$item['from']['name'];
				$out['engage'][]=$item['likes']['count']+$item['comments']['count'];
				$adv_engage['likes']=intval($item['likes']['count']);
				$adv_engage['comment']=intval($item['comments']['count']);
				$out['adv_engage'][]=$adv_engage;
				$offset_comment=0;
				// if ($item['comments']['count']!=0)
				{
					do
					{
						echo '*';
						$attemp=0;
						do
						{
							$cont_comment=parseUrl('https://graph.facebook.com/'.$item['id'].'/comments?access_token='.$access_token_fb.'&limit=100&offset='.intval($offset_comment*100));			
							sleep(1);
							echo '|';
							$mas_comment=json_decode($cont_comment,true);
							$attemp++;
						}
						while (($mas_comment['error']['code']!='')&&($attemp<3));
						foreach ($mas_comment['data'] as $keyc => $itemc)
						{
							$out['time'][]=strtotime($itemc['created_time']);
							$ids2=explode('_',$itemc['id']);
							$out['link'][]='http://www.facebook.com/'.$ids[0].'/posts/'.$ids[1].'?comment_id='.$ids2[1];
							$post_content='';
							if (strlen($itemc['message'])>0) $post_content.=' '.($itemc['message']);
							elseif (strlen($itemc['caption'])>0) $post_content.=' '.($itemc['caption']);
							elseif (strlen($itemc['description'])>0) $post_content.=' '.($itemc['description']);
							elseif (strlen($itemc['name'])>0) $post_content.=' '.($itemc['name']);
							elseif (strlen($itemc['caption'])>0) $post_content.=' '.($itemc['caption']);
							else $post_content='http://www.facebook.com/'.$ids[0].'/posts/'.$ids[1].'?comment_id='.$ids2[1];
							$out['content'][]=$post_content;
							$out['author_id'][]=$itemc['from']['id'];
							$out['author_name'][]=$itemc['from']['name'];
							$out['engage'][]=$item['like_count'];
							$out['adv_engage'][]['likes']=intval($item['like_count']);
						}
						$offset_comment++;
					}
					while (count($mas_comment['data'])!=0);
				}
			}
		}
		$offset++;
	}
	while (count($mas['data'])!=0);
	$offset=0;
	do
	{
		echo '.';
		$attemp=0;
		do
		{
			$cont=parseUrl('https://graph.facebook.com/'.$name.'/albums?access_token='.$access_token_fb.'&limit=100&offset='.intval($offset*100));
			$mas=json_decode($cont,true);
			$attemp++;
		}
		while (($mas['error']['code']!='')&&($attemp<3));
		echo '/';
		//print_r($mas);
		sleep(1);
		foreach ($mas['data'] as $key => $item)
		{
			if ((strtotime($item['created_time'])>=$ts) && (strtotime($item['created_time'])<=$te))
			{			
				$out['time'][]=strtotime($item['created_time']);
				$ids=explode('_',$item['id']);
				$out['link'][]=$item['link'];
				$post_content='';
				if (strlen($item['message'])>0) $post_content.=' '.($item['message']);
				elseif (strlen($item['caption'])>0) $post_content.=' '.($item['caption']);
				elseif (strlen($item['description'])>0) $post_content.=' '.($item['description']);
				elseif (strlen($item['name'])>0) $post_content.=' '.($item['name']);
				elseif (strlen($item['caption'])>0) $post_content.=' '.($item['caption']);
				else $post_content='http://www.facebook.com/'.$ids[0].'/posts/'.$ids[1];
				$out['content'][]=$post_content;
				$out['author_id'][]=$item['from']['id'];
				$out['author_name'][]=$item['from']['name'];
				$out['engage'][]=$item['likes']['count']+$item['comments']['count'];
				$adv_engage['likes']=intval($item['likes']['count']);
				$adv_engage['comment']=intval($item['comments']['count']);
				$out['adv_engage'][]=$adv_engage;
				$offset_comment=0;
				// if ($item['comments']['count']!=0)
				{
					do
					{
						echo '*';
						$attemp=0;
						do
						{
							$cont_comment=parseUrl('https://graph.facebook.com/'.$item['id'].'/comments?access_token='.$access_token_fb.'&limit=100&offset='.intval($offset_comment*100));			
							sleep(1);
							echo '|';
							$mas_comment=json_decode($cont_comment,true);
							$attemp++;
						}
						while (($mas_comment['error']['code']!='')&&($attemp<3));
						foreach ($mas_comment['data'] as $keyc => $itemc)
						{
							$out['time'][]=strtotime($itemc['created_time']);
							$ids2=explode('_',$itemc['id']);
							$out['link'][]=$item['link'].'?comment_id='.$ids2[1];
							$post_content='';
							if (strlen($itemc['message'])>0) $post_content.=' '.($itemc['message']);
							elseif (strlen($itemc['caption'])>0) $post_content.=' '.($itemc['caption']);
							elseif (strlen($itemc['description'])>0) $post_content.=' '.($itemc['description']);
							elseif (strlen($itemc['name'])>0) $post_content.=' '.($itemc['name']);
							elseif (strlen($itemc['caption'])>0) $post_content.=' '.($itemc['caption']);
							else $post_content='http://www.facebook.com/'.$ids[0].'/posts/'.$ids[1].'?comment_id='.$ids2[1];
							$out['content'][]=$post_content;
							$out['author_id'][]=$itemc['from']['id'];
							$out['author_name'][]=$itemc['from']['name'];
							$out['engage'][]=$item['like_count'];
							$out['adv_engage'][]['likes']=intval($item['like_count']);
						}
						$offset_comment++;
					}
					while (count($mas_comment['data'])!=0);
				}
			}
		}
		$offset++;
	}
	while (count($mas['data'])!=0);
	// print_r($out);
	return $out;
}

// get_facebook_group('DaikinRussia',mktime(0,0,0,10,20,2013),mktime(0,0,0,10,30,2013));
//echo chech_facebook_content('');
?>