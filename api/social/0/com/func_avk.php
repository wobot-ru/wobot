<?

function get_accout_vk($name,$start,$end,$nk,$access_token)
{
	$outmas[0]=get_main_info($name,$access_token);
	$act=get_activity($outmas[0]['uid'],$start,$end,$access_token);
	$outmas[4]=$act[1];
	//$outmas[4]['admin']=$act[3];
	$outmas[5]=$act[2];
	$k=get_k($act[0],$nk);
	$outmas[2]['k'.$nk]=$k;
	//print_r($k);
	$k=get_k($act[0],1);
	$outmas[2]['k1']=$k;
	//print_r($k);
	$k=get_k($act[0],3);
	$outmas[2]['k3']=$k;
	//print_r($k);
	$k=get_k($act[0],5);
	$outmas[2]['k5']=$k;
	$outmas[1]=get_users($outmas[0]['uid'],$access_token);
	$outmas[2]['passive']=get_passive($outmas[1]['users']['uid'],$outmas[2]['k1']['users']);
	//print_r($outmas);
	//die();
	//print_r($k);
	//print_r($outmas);
	return $outmas;
}

function get_passive($users,$k)
{
	//print_r($users);
	//print_r($k);
	$count=count($users);
	foreach ($k as $item)
	{
		if (in_array($item,$users)) $count-=1;
	}
	return $count;
}

function get_main_info($name,$access_token)
{
	$cont=parseUrl('https://api.vkontakte.ru/method/getProfiles?uids='.urlencode($name).'&fields=uid,counters,first_name,last_name,nickname,sex,bdate,city,country,timezone,photo,photo_medium,photo_big,domain,has_mobile,rate,contacts,education&access_token='.$access_token);
	$minf=json_decode($cont,true);
	return $minf['response'][0];
}

function get_activity($groupid,$start_time,$end_time,$access_token)
{
	global $assoc_type_p,$yet,$p_graph,$to_admin;
	do
	{
		sleep(1);
		$cont=parseUrl('https://api.vkontakte.ru/method/wall.get?owner_id='.$groupid.'&count=100&start_time='.strtotime($start_time).'&end_time='.strtotime($end_time).'&extended=1&access_token='.$access_token.'&offset='.($offset*100));
		echo 'https://api.vkontakte.ru/method/wall.get?owner_id='.$groupid.'&count=100&start_time='.strtotime($start_time).'&end_time='.strtotime($end_time).'&extended=1&access_token='.$access_token.'&offset='.($offset*100)."\n";
		$mposts=json_decode($cont,true);
		foreach ($mposts['response']['wall'] as $key => $post)
		{
			echo '____'.$key.'____'."\n";
			if ($key==0) continue;
			if (($post['date']<$start_time) || ($post['date']>=mktime(0,0,0,date('n',$end_time),date('j',$end_time)+1,date('Y',$end_time)))) continue;
			$type_posts='';
			foreach ($post['attachments'] as $k => $itt)
			{
				//echo '!'.$itt['type'].'|'."\n";
				$p_graph[mktime(0,0,0,date('n',$post['date']),date('j',$post['date']),date('Y',$post['date']))][$assoc_type_p[$itt['type']]==''?$itt['type']:$assoc_type_p[$itt['type']]]++;
				$yet[$assoc_type_p[$itt['type']]==''?$itt['type']:$assoc_type_p[$itt['type']]]++;
			}
			$yet['reposts']+=intval($post['reposts']['count']);
			$mactions[$post['from_id']]++;
			if ($post['comments']['count']>0)
			{
				$mactions=get_comment($mactions,$groupid,$post['id'],$start_time,$end_time,$access_token);
			}
			if ($post['likes']['count']>0)
			{
				$ttm=mktime(0,0,0,date('n',$post['date']),date('j',$post['date']),date('Y',$post['date']));
				$mactions=get_likes($mactions,$ttm,$groupid,$post['id'],$start_time,$end_time,'post',$access_token);
			}
			if ('-'.$groupid==$post['from_id'])
			{
				$to_admin['count_repost']+=intval($post['reposts']['count']);
				$to_admin['count_comment']+=intval($post['comments']['count']);
				$to_admin['count_likes']+=intval($post['likes']['count']);
				$to_admin['count_post']++;
			}
		}
		$offset++;
	}
	while ($offset<($mposts['response']['wall'][0]/100) && ($mposts['response']['wall'][count($mposts['response']['wall'])-1]['date']>$start_time));
	//print_r($mactions);
	//$cont=parseUrl('https://api.vkontakte.ru/method/wall.get?owner_id=-'.$groupid.'&count=100&start_time='.strtotime($start_time).'&end_time='.strtotime($end_time).'&extended=1&access_token='.$access_token.'&offset='.($offset*100));
	print_r($yet);
	$outmas[0]=$mactions;
	$outmas[1]=$yet;
	$outmas[2]=$p_graph;
	$outmas[3]=$to_admin;
	return $outmas;
}

function get_comment($mactions,$groupid,$post_id,$start_time,$end_time,$access_token)
{
	global $assoc_type_p,$yet,$p_graph;
	do
	{
		sleep(1);
		$cont=parseUrl('https://api.vkontakte.ru/method/wall.getComments?owner_id='.$groupid.'&post_id='.$post_id.'&access_token='.$access_token.'&need_likes=1&count=100&offset='.($offset*100));
		echo 'https://api.vkontakte.ru/method/wall.getComments?owner_id='.$groupid.'&post_id='.$post_id.'&access_token='.$access_token.'&need_likes=1&count=100&offset='.($offset*100)."\n";
		$mcomment=json_decode($cont,true);
		foreach ($mcomment['response'] as $key => $comment)
		{
			if ($key==0) continue;
			if (($comment['date']<$start_time) || ($comment['date']>=mktime(0,0,0,date('n',$end_time),date('j',$end_time)+1,date('Y',$end_time)))) 
			{
				continue;
			}
			$yet[$assoc_type_p['comment']]++;
			$p_graph[mktime(0,0,0,date('n',$comment['date']),date('j',$comment['date']),date('Y',$comment['date']))][$assoc_type_p['comment']]++;
			$mactions[$comment['from_id']]++;
			if ($comment['likes']['count']==0) continue;
			$ttm=mktime(0,0,0,date('n',$comment['date']),date('j',$comment['date']),date('Y',$comment['date']));
			$mactions=get_likes($mactions,$ttm,$groupid,$comment['cid'],$start_time,$end_time,'comment',$access_token);
		}
		$offset++;
	}
	while ($offset<($mcomment['response'][0]/100));
	return $mactions;
}

function get_likes($mactions,$time,$groupid,$post_id,$start_time,$end_time,$type,$access_token)
{
	global $assoc_type_p,$yet,$p_graph;
	do
	{
		sleep(1);
		$cont=parseUrl('https://api.vkontakte.ru/method/likes.getList?type='.$type.'&count=1000&owner_id='.$groupid.'&item_id='.$post_id.'&access_token='.$access_token.'&need_likes=1&offset='.($offset*100));
		echo 'https://api.vkontakte.ru/method/likes.getList?type=post&count=1000&owner_id='.$groupid.'&item_id='.$post_id.'&access_token='.$access_token.'&need_likes=1&offset='.($offset*100)."\n";
		$mlikes=json_decode($cont,true);
		echo 'http://vk.com/wall'.$groupid.'_'.$post_id."\n";
		//print_R($mlikes);
		foreach ($mlikes['response']['users'] as $key => $like)
		{
			$mactions[$like]++;
			$yet[$assoc_type_p['like']]++;
			$p_graph[$time][$assoc_type_p['like']]++;
		}
		$offset++;
	}
	while ($offset<($mlikes['response']['count']/1000));
	return $mactions;
}

function get_k($act,$nk)
{
	arsort($act);
	foreach ($act as $idu => $count)
	{
		if ($count>=$nk)
		{
			$outmas['count']++;
			$outmas['users'][]=$idu;
		}
	}
	return $outmas;
}

function get_users($groupid,$access_token)
{
	global $db;
	
	$filename = "/var/www/api/social/0/com/get_vk_countries.txt";
	$handle = fopen($filename, "r");
	$contents = fread($handle, filesize($filename));
	fclose($handle);
	$countries=unserialize($contents);
	$filename = "/var/www/api/social/0/com/get_vk_cities.txt";
	$handle = fopen($filename, "r");
	$contents = fread($handle, filesize($filename));
	fclose($handle);
	$cities=unserialize($contents);
	//print_r($cities);
	unset($contents);
	
	do
	{
		$id_users=parseUrl('https://api.vkontakte.ru/method/friends.get?uid='.$groupid.'&count=1000&offset='.($i*1000).'&access_token='.$access_token);
		sleep(1);
		echo 'https://api.vkontakte.ru/method/friends.get?uid='.$groupid.'&count=1000&offset='.($i*1000).'&access_token='.$access_token."\n";
		$ids=json_decode($id_users,true);
		$zap='';
		$qw='';
		foreach ($ids['response'] as $id)
		{
			$qw.=$zap.$id;
			$zap=',';
			$arr_not_exist[$id]=1;
		}
		$q_us=$db->query('SELECT * FROM robot_blogs4 WHERE blog_login IN ('.$qw.')');
		echo '/';
		//echo 'SELECT * FROM robot_blogs4 WHERE blog_login IN (\''.$qw.'\')';
		while ($user=$db->fetch($q_us))
		{
			if ($user['blog_gen']==1)	$count_woman++;
			if ($user['blog_gen']==2)	$count_man++;
			if ($user['blog_photo']=='http://vk.com/images/deactivated_c.gif')	$count_block++;
			if ($user['blog_nick']=='DELETED ') $count_deleted++;
			if ($user['blog_hasmobile']==1) $count_activate++;
			if (($user['blog_photo']=='http://vk.com/images/deactivated_c.gif') || ($user['first_name']=='DELETED ') || (($user['blog_hasmobile']==0) && (isset($yet_ava[$user['blog_photo']])))) $count_bot++;
			$yet_ava[$user['blog_photo']]++;	
			if ($user['blog_age']!=0) $mage[date('Y')-$user['blog_age']]++;
			if ($countries[intval($user['blog_country'])]!='')
			{
				$loc_cou_mas[$countries[intval($user['blog_country'])]]++;
			}
			if ($cities[intval($user['blog_city'])]!='')
			{
				$loc_mas[$cities[intval($user['blog_city'])]]++;
			}
			unset($arr_not_exist[$user['blog_login']]);
			$users['uid'][]=$user['blog_login'];
			$users['name'][]=$user['blog_nick'];
			$users['sex'][]=$user['blog_gen'];
			$users['bdate'][]=$user['blog_age'];
			$users['city'][]=$cities[intval($user['blog_city'])];
			$users['country'][]=$countries[intval($user['blog_country'])];
			$users['timezone'][]=$user['blog_timezone'];
			$users['photo'][]=$user['blog_photo'];
			$users['has_mobile'][]=$user['blog_hasmobile'];
			$users['rate'][]=$user['blog_rate'];
			$users['mobile_phone'][]=$user['blog_mphone'];
			$users['home_phone'][]=$user['blog_hphone'];
			$users['university'][]=$user['blog_univ'];
			$users['university_name'][]=$user['blog_univ'];
			$users['faculty'][]=$user['blog_fac'];
			$users['faculty_name'][]=$user['blog_fac'];
			$users['graduation'][]=$user['blog_grad'];
		}
		$i++;
	}
	while ($i<($ids['response']['count']/1000));
	print_r($arr_not_exist);
	foreach ($arr_not_exist as $id => $item)
	{
		$pos_ex++;
		$text_ids.=$zap.$id;
		$len_tid++;
		if (($len_tid==250) || ($pos_ex==count($arr_not_exist)))
		{
			$cont=parseUrl('https://api.vkontakte.ru/method/getProfiles?uids='.urlencode($text_ids).'&fields=uid,counters,first_name,last_name,nickname,sex,bdate,city,country,timezone,photo,photo_medium,photo_big,domain,has_mobile,rate,contacts,education&access_token='.$access_token);
			sleep(1);
			//echo 'https://api.vkontakte.ru/method/getProfiles?uids='.urlencode($text_ids).'&fields=uid,counters,first_name,last_name,nickname,sex,bdate,city,country,timezone,photo,photo_medium,photo_big,domain,has_mobile,rate,contacts,education&access_token='.$access_token."\n";
			echo '.';
			$m_user_info=json_decode($cont,true);
			foreach ($m_user_info['response'] as $k_us => $user)
			{
				if ($user['sex']==1) $count_woman++;
				if ($user['sex']==2) $count_man++;
				if ($user['photo']=='http://vk.com/images/deactivated_c.gif') $count_block++;
				if ($user['first_name']=='DELETED') $count_deleted++;
				if ($user['has_mobile']==1) $count_activate++;
				if (($user['photo']=='http://vk.com/images/deactivated_c.gif') || ($user['first_name']=='DELETED') || (($user['has_mobile']==0) && (isset($yet_ava[$user['photo']])))) $count_bot++;
				$yet_ava[$user['photo']]++;			
				$regex='/(?<d>\d\d?)\.(?<m>\d\d?)\.(?<y>\d\d\d\d)/is';
				//echo $inf['bdate'];
				preg_match_all($regex,$user['bdate'],$outt);
				if (intval($outt['y'][0])!=0) $mage[date('Y')-$outt['y'][0]]++;				
				if ($countries[intval($user['country'])]!='')
				{
					$loc_cou_mas[$countries[intval($user['country'])]]++;
				}
				if ($cities[intval($user['city'])]!='')
				{
					$loc_mas[$cities[intval($user['city'])]]++;
				}
				$users['uid'][]=$user['uid'];
				$users['name'][]=$user['first_name'].' '.$user['last_name'];
				$users['sex'][]=$user['sex'];
				$users['bdate'][]=$user['bdate'];
				$users['city'][]=$cities[intval($user['city'])];
				$users['country'][]=$countries[intval($user['country'])];
				$users['timezone'][]=$user['timezone'];
				$users['photo'][]=$user['photo'];
				$users['has_mobile'][]=$user['has_mobile'];
				$users['rate'][]=$user['rate'];
				$users['mobile_phone'][]=$user['mobile_phone'];
				$users['home_phone'][]=$user['home_phone'];
				$users['university'][]=$user['university'];
				$users['university_name'][]=$user['university_name'];
				$users['faculty'][]=$user['faculty'];
				$users['faculty_name'][]=$user['faculty_name'];
				$users['graduation'][]=$user['graduation'];
				$db->query('INSERT INTO robot_blogs4 (blog_link,blog_login,blog_nick,blog_gen,blog_age,blog_city,blog_country,blog_timezone,blog_photo,blog_hasmobile,blog_rate,blog_mphone,blog_hphone,blog_univ,blog_fac,blog_grad,blog_album,blog_video,blog_audio,blog_notes,blog_friends,blog_uphoto,blog_uvideo,blog_fol,blog_subscr) VALUES (\'vk.com\',\''.$user['uid'].'\',\''.addslashes($user['first_name'].' '.$user['last_name']).'\',\''.$user['sex'].'\',\''.$outt['y'][0].'\',\''.$user['city'].'\',\''.$user['country'].'\',\''.$user['timezone'].'\',\''.$user['photo'].'\',\''.$user['has_mobile'].'\',\''.$user['rate'].'\',\''.addslashes($user['mobile_phone']).'\',\''.addslashes($user['home_phone']).'\',\''.addslashes($user['university_name']).'\',\''.addslashes($user['faculty_name']).'\',\''.addslashes($user['graduation']).'\',\''.addslashes($user['counters']['albums']).'\',\''.$user['counters']['videos'].'\',\''.$user['counters']['audios'].'\',\''.$user['counters']['notes'].'\',\''.$user['counters']['friends'].'\',\''.$user['counters']['user_photos'].'\',\''.$user['counters']['user_videos'].'\',\''.$user['counters']['followers'].'\',\''.$user['counters']['subscriptions'].'\')');
			}
			$text_ids='';
			$len_tid=0;
		}
	}
	arsort($yet_ava);
	//print_r($yet_ava);
	$k_yet_i=0;
	foreach ($yet_ava as $k_yet => $i_yet)
	{
		if ($i_yet==1) break;
		$k_yet_i++;
	}
	//print_r($loc_mas);
	//print_r($loc_cou_mas);
	//echo 'WOMAN = '.$count_woman."\n".'MAN = '.$count_man."\n";
	//echo 'BLOCK = '.$count_block."\n";
	//echo 'DELETED = '.$count_deleted."\n";
	//echo 'ACTIVATED = '.$count_activate."\n";
	$outmas['users']=$users;
	$outmas['count_bot']=$count_bot;
	$outmas['count_uniq']=count($yet_ava)-$k_yet_i;
	$outmas['count_woman']=intval($count_woman);
	$outmas['count_man']=intval($count_man);
	$outmas['count_block']=intval($count_block);
	$outmas['count_deleted']=intval($count_deleted);
	$outmas['count_activate']=intval($count_activate);
	$outmas['loc']=$loc_mas;
	$outmas['loc_cou_mas']=$loc_cou_mas;
	$outmas['age']=$mage;
	//print_r($users);
	//print_r($mage);
	return $outmas;
}

?>