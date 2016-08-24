<?
/*
Документируем код

Last updates:
$access_token - variable

TODO:
Важно
1) добавить в wall.get цикл и использование offset, ограничить цикл по дате поста start_time
Пока не важно:
2) расширить базу городов
3) проверить определение удаленных пользователей
4) определять ботов
5) строить облака тегов по комментариям

*/
require_once('/var/www/com/config.php');
require_once('/var/www/com/func.php');
require_once('/var/www/com/db.php');
require_once('/var/www/bot/kernel.php');

$db = new database();
$db->connect();

$_GET['ngroup']='zapiski_m';
$_GET['ts']='1.07.2012';
$_GET['te']='31.07.2012';
/*$_GET['ngroup']='club47781';
$_GET['ts']='01.04.2012';
$_GET['te']='22.07.2012';
*/
if (($_GET['ngroup']!='') && ($_GET['ts']!='') && ($_GET['te']!='') && ((strtotime($_GET['te'])-strtotime($_GET['ts']))>(86400*14)))
{
	$outmas['1']['name_report']=$_GET['ngroup'].'.xls';
	$outmas['2']['posts']=array();
	$outmas['3']['list']=array();
	$outmas['3']['more5']=0;
	$outmas['3']['less5']=0;
	$outmas['3']['passive']=0;
	$outmas['3']['all']=0;
	$outmas['2']['count_posts']=0;
	$outmas['2']['types']=array();
	$outmas['2']['posts_graph']=array();
	$outmas['2']['count_comments']=0;
	$outmas['2']['count_likes']=0;
	$outmas['2']['count_reposts']=0;
	$outmas['5']['not_in_group']=array();
	session_start();
	error_reporting(0);
	//ignore_user_abort(true);
	set_time_limit(0);
	ini_set('max_execution_time',0);
	ob_implicit_flush();
	ini_set("memory_limit", "8096M");
	date_default_timezone_set ( 'Europe/Moscow' );
	/*print_r($_GET);
	print_r($_POST);
	print_r($_SESSION);*/

	//$all_users['-']['name']

	//Загрузка словарей
	$m_gen[1]='Ж';
	$m_gen[2]='М';
	$assoc_type_p['photo']='фото';
	$assoc_type_p['text']='текст';
	$assoc_type_p['link']='ссылка';
	$assoc_type_p['comment']='комментарий';
	$assoc_type_p['video']='видео';

	$filename = "/var/www/social/get_vk_countries.txt";
	$handle = fopen($filename, "r");
	$contents = fread($handle, filesize($filename));
	fclose($handle);
	$countries=unserialize($contents);

	$filename = "/var/www/social/get_vk_cities.txt";
	$handle = fopen($filename, "r");
	$contents = fread($handle, filesize($filename));
	fclose($handle);
	$cities=unserialize($contents);
	//print_r($cities);
	unset($contents);

	$start_time=$_GET['ts'];//'14.02.2012';
	$end_time=$_GET['te'];//'14.03.2012';
	$appid='2785229';
	$appsecret='OGycTbxYl9ImDBpf3bCH';
	$access_token='97f962de97f62c5797f62c575b97dc539a997f597f9a4a900cce13f184fcfc3';//'9902a6a19912b0219912b021569938cd1599912991d08dfb6bfe36737e288c9';//'c87eb1fbc8690a13c8690a1372c8437727cc869c866b2eda9d782f77b2ba1d5';

	//Получаем название группы по ссылке
	//$group_link='http://vk.com/club12995729/';
	//$regex='/vk\.com\/(?<group>.*?)\//is';
	//preg_match_all($regex,$group_link,$out);

	//17954070
	//http://vk.com/beeline_kaz
	//http://vk.com/beeline_uz
	//http://vk.com/beelinearmenia

	$out['group'][0]=$_GET['ngroup'];//'club30283330';

	//Получаем id-группы по названию
	$json=file_get_contents('https://api.vkontakte.ru/method/groups.getById?gid='.urlencode($out['group'][0]).'&access_token='.$access_token);
	$data=json_decode($json, true);
	$groupid=$data['response'][0]['gid'];
	//echo 'https://api.vkontakte.ru/method/newsfeed.get?source_ids=g'.$groupid.'&start_time='.strtotime($start_time).'&end_time='.strtotime($end_time).'&access_token='.$access_token;
	$group_link='http://vk.com/club'.$groupid;
	//echo $groupid;
	//print_r($data);
	$outmas['1']['name']=$data['response'][0]['name'];
	$outmas['1']['userpic']=$data['response'][0]['photo'];
	$i=0;
	$indx=1;
	do
	{
		echo '/';
		$cgr=parseUrl('https://api.vkontakte.ru/method/groups.getMembers?gid='.$groupid.'&count=1000&offset='.($i*1000).'&access_token='.$access_token);
		$fp = fopen('data.txt', 'a');
		fwrite($fp, $i.' ');
		fclose($fp);		//echo 'https://api.vkontakte.ru/method/groups.getMembers?gid='.$groupid.'&count=1000&offset='.($i*1000).'&access_token='.$access_token."\n";
		$cgr_info=json_decode($cgr, true);
		//echo $i."\n";
		//print_r($cgr_info);
		//echo 'DEACTIVATED!!!!='.$count_deactivated."\n";
		foreach ($cgr_info['response']['users'] as $item)
		{
			if ($item!='')
			{
				//echo $item.'|'."\n";
				$arr_qw[$item]=1;
				$g++;
			}
			//echo $g."\n";
			if ($g==1000)
			{
				$g=0;
				//echo count($arr_qw);
				usleep(500000);
				//echo '.';
				$or='';
				$qw='(';
				$zap='';
				foreach ($arr_qw as $key => $item1)
				{
					//$qw.=$or.' blog_login=\''.$key.'\' ';
					//$or=' OR ';
					$qw.=$zap.$key;
					$zap=',';
				}
				$qw.=')';
				//echo 'SELECT * FROM robot_blogs4 WHERE '.$qw;
				$qus=$db->query('SELECT * FROM robot_blogs4 WHERE blog_login IN '.$qw);
				while ($uss=$db->fetch($qus))
				{
					//print_r($uss);
					//echo $uss['blog_login']."\n";
					unset($arr_qw[$uss['blog_login']]);
					//echo $uss['blog_login'].' '.$uss['blog_photo']."\n";
					if (mb_strpos($uss['blog_photo'],'deactivated',0,'UTF-8')!==false)
					{
						//if (preg_match('/.*DELETED/is'$uss['blog_nick']=='DELETED')
						if (mb_strpos($uss['blog_nick'],'DELETED',0,'UTF-8')!==false)
						{
							$count_deleted++;
						}
						else
						{
							$count_deactivated++;
						}
					}

					if ($uss['blog_gen']==1)
					{
						$count_woman++;
					}
					elseif ($uss['blog_gen']==2)
					{
						$count_man++;
					}
					if ($uss['blog_photo']=='http://vkontakte.ru/images/camera_c.gif')
					{
						$count_without_photo++;
					}
					else
					{
						$count_with_photo++;
					}
					if ($uss['blog_hasmobile']==0)
					{
						//echo $uss['blog_login'].' ';
					}
					$assoc_photo[$uss['blog_photo']]++;
					$assoc_mobile[$uss['blog_hasmobile']]++;
					//$regex='/(?<d>\d\d?)\.(?<m>\d\d?)\.(?<y>\d\d\d\d)/is';
					//echo $inf['bdate'];
					preg_match_all($regex,$uss['blog_age'],$outt);
					$age_mas[date('Y')-$uss['blog_age']]++;
					if ($countries[intval($uss['blog_country'])]!='')
					{
						$loc_cou_mas[$countries[intval($uss['blog_country'])]]++;
					}
					else
					{
						$count_without_city++;
					}
					if ($cities[intval($uss['blog_city'])]!='')
					{
						$loc_mas1[$cities[intval($uss['blog_city'])]]++;
					}
					$all_us[0][]=$indx;
					$all_us[1][]=preg_replace('/[^а-яА-Яa-zA-Z]/isu',' ',$uss['blog_nick']);
					$all_us[2][]=preg_replace('/[^а-яА-Яa-zA-Z]/isu',' ','');
					$all_us[3][]=preg_replace('/[^а-яА-Яa-zA-Z0-9]/isu',' ',$uss['blog_login']);
					$all_us[4][]=preg_replace('/[^а-яА-Яa-zA-Z0-9]/isu',' ','');
					$all_us[5][]=preg_replace('/[^а-яА-Яa-zA-Z0-9\:\/\.]/isu',' ','http://vk.com/id'.$uss['blog_login']);
					$all_us[6][]=preg_replace('/[^а-яА-Яa-zA-Z]/isu',' ',$m_gen[$uss['blog_gen']]);
					$all_us[7][]=preg_replace('/[^а-яА-Яa-zA-Z0-9\.]/isu',' ',date('Y')-$uss['blog_age']);
					$all_us[8][]=preg_replace('/[^а-яА-Яa-zA-Z0-9]/isu',' ',$cities[$uss['blog_city']]);
					$all_us[9][]=preg_replace('/[^а-яА-Яa-zA-Z0-9]/isu',' ',$countries[$uss['blog_country']]);
					$all_us[10][]=intval($uss['blog_timezone']);
					$all_us[11][]=preg_replace('/[^а-яА-Яa-zA-Z0-9\/\.]/is',' ',$uss['blog_photo']);
					$all_us[12][]=intval($uss['blog_hasmobile']);
					$all_us[13][]=intval($uss['blog_rate']);
					$all_us[14][]=preg_replace('/[^а-яА-Яa-zA-Z0-9]/isu',' ',$uss['blog_mphone']);
					$all_us[15][]=preg_replace('/[^а-яА-Яa-zA-Z0-9]/isu',' ',$uss['blog_hphone']);
					$all_us[16][]=preg_replace('/[^а-яА-Яa-zA-Z0-9]/isu',' ',$uss['blog_univ']);
					$all_us[17][]=preg_replace('/[^а-яА-Яa-zA-Z0-9]/isu',' ',$uss['blog_fac']);
					$all_us[18][]=preg_replace('/[^а-яА-Яa-zA-Z0-9]/isu',' ',$uss['blog_grad']);
					$indx++;
				}
				//echo $text_uids;
				//echo '!!!';
				//echo count($arr_qw);
				//echo '!!!'."\n";
				//print_r($arr_qw);
				//uids -- перечисленные через запятую ID пользователей или их короткие имена (screen_name). Максимум 1000 пользователей.
				//почему $g до 200?
				if (count($arr_qw)<250) continue;
				foreach ($arr_qw as $kk => $ii)
				{
					$text_uids.=$zap.$kk;
					$zap=',';
					$mqv[$kk]++;
					unset($arr_qw[$kk]);
					if (count($mqv)<250)
					{
						continue;
					}
					//print_r($mqv);
					unset($mqv);
					//unset($arr_qw);
					//echo '|'.count($mqv).'|'."\n";
					//echo 'unset!!'."\n";
					echo '.';
					$info_users=parseUrl('https://api.vkontakte.ru/method/getProfiles?uids='.urlencode($text_uids).'&fields=uid,counters,first_name,last_name,nickname,sex,bdate,city,country,timezone,photo,photo_medium,photo_big,domain,has_mobile,rate,contacts,education&access_token='.$access_token);
					//echo 'https://api.vkontakte.ru/method/getProfiles?uids='.urlencode($text_uids).'&fields=uid,counters,first_name,last_name,nickname,sex,bdate,city,country,timezone,photo,photo_medium,photo_big,domain,has_mobile,rate,contacts,education&access_token='.$access_token."\n\n\n\n\n\n\n";
					$text_uids='';
					$data_users=json_decode($info_users, true);
					//print_r($data_users);
					foreach ($data_users['response'] as $key => $item)
					{
						$assoc_photo[$item['photo']]++;
						$assoc_mobile[$item['has_mobile']]++;
						$regex='/(?<d>\d\d?)\.(?<m>\d\d?)\.(?<y>\d\d\d\d)/is';
						//echo $inf['bdate'];
						preg_match_all($regex,$item['bdate'],$outt);
						$db->query('INSERT INTO robot_blogs4 (blog_link,blog_login,blog_nick,blog_gen,blog_age,blog_city,blog_country,blog_timezone,blog_photo,blog_hasmobile,blog_rate,blog_mphone,blog_hphone,blog_univ,blog_fac,blog_grad,blog_album,blog_video,blog_audio,blog_notes,blog_friends,blog_uphoto,blog_uvideo,blog_fol,blog_subscr) VALUES (\'vk.com\',\''.$item['uid'].'\',\''.addslashes($item['first_name'].' '.$item['last_name']).'\',\''.$item['sex'].'\',\''.$outt['y'][0].'\',\''.$item['city'].'\',\''.$item['country'].'\',\''.$item['timezone'].'\',\''.$item['photo'].'\',\''.$item['has_mobile'].'\',\''.$item['rate'].'\',\''.addslashes($item['mobile_phone']).'\',\''.addslashes($item['home_phone']).'\',\''.addslashes($item['university_name']).'\',\''.addslashes($item['faculty_name']).'\',\''.addslashes($item['counters']['graduation']).'\',\''.addslashes($item['counters']['albums']).'\',\''.$item['counters']['videos'].'\',\''.$item['counters']['audios'].'\',\''.$item['counters']['notes'].'\',\''.$item['counters']['friends'].'\',\''.$item['counters']['user_photos'].'\',\''.$item['counters']['user_videos'].'\',\''.$item['counters']['followers'].'\',\''.$item['counters']['subscriptions'].'\')');
						if (mb_strpos($item['photo'],'deactivated',0,'UTF-8')!==false)
						{
							if ($item['first_name']=='DELETED')
							//if (mb_strpos($item['first_name'],'DELETED',0,'UTF-8')!==false)
							{
								$count_deleted++;
							}
							else
							{
								$count_deactivated++;
							}
						}

						if ($item['sex']==1)
						{
							$count_woman++;
						}
						elseif ($item['sex']==2)
						{
							$count_man++;
						}
						if ($item['photo']=='http://vkontakte.ru/images/camera_c.gif')
						{
							$count_without_photo++;
						}
						else
						{
							$count_with_photo++;
						}
						$regex='/(?<d>\d\d?)\.(?<m>\d\d?)\.(?<y>\d\d\d\d)/is';
						//echo $inf['bdate'];
						preg_match_all($regex,$item['bdate'],$outt);
						$age_mas[intval((time()-mktime(0,0,0,$outt['m'][0],$outt['d'][0],$outt['y'][0]))/(86400*365))]++;
						if ($countries[intval($item['country'])]!='')
						{
							$loc_cou_mas[$countries[intval($item['country'])]]++;
						}
						else
						{
							$count_without_city++;
						}
						if ($cities[intval($item['city'])]!='')
						{
							$loc_mas1[$cities[intval($item['city'])]]++;
						}
						$all_us[0][]=$indx;
						$all_us[1][]=preg_replace('/[^а-яА-Яa-zA-Z]/isu',' ',$item['first_name']);
						$all_us[2][]=preg_replace('/[^а-яА-Яa-zA-Z]/isu',' ',$item['last_name']);
						$all_us[3][]=preg_replace('/[^а-яА-Яa-zA-Z0-9]/isu',' ',$item['domain']);
						$all_us[4][]=preg_replace('/[^а-яА-Яa-zA-Z0-9]/isu',' ',$item['nickname']);
						$all_us[5][]=preg_replace('/[^а-яА-Яa-zA-Z0-9\:\/\.]/isu',' ','http://vk.com/id'.$item['uid']);
						$all_us[6][]=preg_replace('/[^а-яА-Яa-zA-Z]/isu',' ',$m_gen[$item['sex']]);
						$all_us[7][]=preg_replace('/[^а-яА-Яa-zA-Z0-9\.]/isu',' ',$item['bdate']);
						$all_us[8][]=preg_replace('/[^а-яА-Яa-zA-Z0-9]/isu',' ',$cities[$item['city']]);
						$all_us[9][]=preg_replace('/[^а-яА-Яa-zA-Z0-9]/isu',' ',$countries[$item['country']]);
						$all_us[10][]=intval($item['timezone']);
						$all_us[11][]=preg_replace('/[^а-яА-Яa-zA-Z0-9\/\.]/is',' ',$item['photo']);
						$all_us[12][]=intval($item['has_mobile']);
						$all_us[13][]=intval($item['rate']);
						$all_us[14][]=preg_replace('/[^а-яА-Яa-zA-Z0-9]/isu',' ',$item['mobile_phone']);
						$all_us[15][]=preg_replace('/[^а-яА-Яa-zA-Z0-9]/isu',' ',$item['home_phone']);
						$all_us[16][]=preg_replace('/[^а-яА-Яa-zA-Z0-9]/isu',' ',$item['university_name']);
						$all_us[17][]=preg_replace('/[^а-яА-Яa-zA-Z0-9]/isu',' ',$item['faculty_name']);
						$all_us[18][]=preg_replace('/[^а-яА-Яa-zA-Z0-9]/isu',' ',$item['graduation']);
						$indx++;
					}
				}

				$g=0;
				//echo 'G====0'."\n";
				$text_uids='';
				$zap='';
			}
		}
		$i++;
	
		//Почему таймаут полсекунды? http://vk.com/developers.php?oid=-1&p=%D0%9E%D0%B3%D1%80%D0%B0%D0%BD%D0%B8%D1%87%D0%B5%D0%BD%D0%B8%D1%8F_%D0%BD%D0%B0_%D0%B2%D1%8B%D0%B7%D0%BE%D0%B2_%D0%BC%D0%B5%D1%82%D0%BE%D0%B4%D0%BE%D0%B2_Ads_API
		usleep(500000);
	}
	while ($i<(intval($cgr_info['response']['count']/1000)+1));
	$outmas['1']['count_people']=$cgr_info['response']['count'];
	$outmas['1']['mobile']=$assoc_mobile[1].'/'.($outmas['1']['count_people']-$assoc_mobile[1]);
	$outmas['1']['photo']=(count($assoc_photo)).'/'.($outmas['1']['count_people']-count($assoc_photo));
	//print_r($assoc_photo);
	//echo count($assoc_photo);
	//$assoc_photo[$item['photo']]++;
	//$assoc_mobile[$item['has_mobile']]++
	
	//if ($g==250)
	{
		//echo $text_uids;
	
		//uids -- перечисленные через запятую ID пользователей или их короткие имена (screen_name). Максимум 1000 пользователей.
		//почему $g до 200?
	
		$info_users=parseUrl('https://api.vkontakte.ru/method/getProfiles?uids='.urlencode($text_uids).'&fields=uid,counters,first_name,last_name,nickname,sex,bdate,city,country,timezone,photo,photo_medium,photo_big,domain,has_mobile,rate,contacts,education&access_token='.$access_token);
		//echo 'https://api.vkontakte.ru/method/getProfiles?uids='.urlencode($text_uids).'&fields=uid,counters,first_name,last_name,nickname,sex,bdate,city,country,timezone,photo,photo_medium,photo_big,domain,has_mobile,rate,contacts,education&access_token='.$access_token."\n\n\n\n\n\n\n";
		$data_users=json_decode($info_users, true);
		//print_r($data_users);
		foreach ($data_users['response'] as $key => $item)
		{
			$assoc_photo[$item['photo']]++;
			$assoc_mobile[$item['has_mobile']]++;
			$db->query('INSERT INTO robot_blogs4 (blog_link,blog_login,blog_nick,blog_gen,blog_age,blog_city,blog_country,blog_timezone,blog_photo,blog_hasmobile,blog_rate,blog_mphone,blog_hphone,blog_univ,blog_fac,blog_grad,blog_album,blog_video,blog_audio,blog_notes,blog_friends,blog_uphoto,blog_uvideo,blog_fol,blog_subscr) VALUES (\'vk.com\',\''.$item['uid'].'\',\''.($item['first_name'].' '.$item['last_name']).'\',\''.$item['sex'].'\',\''.$outt['y'][0].'\',\''.$item['city'].'\',\''.$item['country'].'\',\''.$item['timezone'].'\',\''.$item['photo'].'\',\''.$item['has_mobile'].'\',\''.$item['rate'].'\',\''.$item['mobile_phone'].'\',\''.$item['home_phone'].'\',\''.$item['university_name'].'\',\''.$item['faculty_name'].'\',\''.$item['counters']['graduation'].'\',\''.$item['counters']['albums'].'\',\''.$item['counters']['videos'].'\',\''.$item['counters']['audios'].'\',\''.$item['counters']['notes'].'\',\''.$item['counters']['friends'].'\',\''.$item['counters']['user_photos'].'\',\''.$item['counters']['user_videos'].'\',\''.$item['counters']['followers'].'\',\''.$item['counters']['subscriptions'].'\')');
			if (mb_strpos($item['photo'],'deactivated',0,'UTF-8')!==false)
			{
				if ($item['first_name']=='DELETED')
				{
					$count_deleted++;
				}
				else
				{
					$count_deactivated++;
				}
			}
			if ($item['sex']==1)
			{
				$count_woman++;
			}
			elseif ($item['sex']==2)
			{
				$count_man++;
			}
			if ($item['photo']=='http://vkontakte.ru/images/camera_c.gif')
			{
				$count_without_photo++;
			}
			else
			{
				$count_with_photo++;
			}
			$regex='/(?<d>\d\d?)\.(?<m>\d\d?)\.(?<y>\d\d\d\d)/is';
			//echo $inf['bdate'];
			preg_match_all($regex,$item['bdate'],$outt);
			$age_mas[intval((time()-mktime(0,0,0,$outt['m'][0],$outt['d'][0],$outt['y'][0]))/(86400*365))]++;
			if ($countries[intval($item['country'])]!='')
			{
				$loc_cou_mas[$countries[intval($item['country'])]]++;
			}
			else
			{
				$count_without_city++;
			}
			if ($cities[intval($item['city'])]!='')
			{
				$loc_mas1[$cities[intval($item['city'])]]++;
			}
			$all_us[0][]=$indx;
			$all_us[1][]=preg_replace('/[^а-яА-Яa-zA-Z]/isu',' ',$item['first_name']);
			$all_us[2][]=preg_replace('/[^а-яА-Яa-zA-Z]/isu',' ',$item['last_name']);
			$all_us[3][]=preg_replace('/[^а-яА-Яa-zA-Z0-9]/isu',' ',$item['domain']);
			$all_us[4][]=preg_replace('/[^а-яА-Яa-zA-Z0-9]/isu',' ',$item['nickname']);
			$all_us[5][]=preg_replace('/[^а-яА-Яa-zA-Z0-9\:\/\.]/isu',' ','http://vk.com/id'.$item['uid']);
			$all_us[6][]=preg_replace('/[^а-яА-Яa-zA-Z]/isu',' ',$m_gen[$item['sex']]);
			$all_us[7][]=preg_replace('/[^а-яА-Яa-zA-Z0-9\.]/isu',' ',$item['bdate']);
			$all_us[8][]=preg_replace('/[^а-яА-Яa-zA-Z0-9]/isu',' ',$cities[$item['city']]);
			$all_us[9][]=preg_replace('/[^а-яА-Яa-zA-Z0-9]/isu',' ',$countries[$item['country']]);
			$all_us[10][]=intval($item['timezone']);
			$all_us[11][]=preg_replace('/[^а-яА-Яa-zA-Z0-9\/\.]/is',' ',$item['photo']);
			$all_us[12][]=intval($item['has_mobile']);
			$all_us[13][]=intval($item['rate']);
			$all_us[14][]=preg_replace('/[^а-яА-Яa-zA-Z0-9]/isu',' ',$item['mobile_phone']);
			$all_us[15][]=preg_replace('/[^а-яА-Яa-zA-Z0-9]/isu',' ',$item['home_phone']);
			$all_us[16][]=preg_replace('/[^а-яА-Яa-zA-Z0-9]/isu',' ',$item['university_name']);
			$all_us[17][]=preg_replace('/[^а-яА-Яa-zA-Z0-9]/isu',' ',$item['faculty_name']);
			$all_us[18][]=preg_replace('/[^а-яА-Яa-zA-Z0-9]/isu',' ',$item['graduation']);
			$indx++;
		}
		$g=0;
		$text_uids='';
		$zap='';
	}
	$outmas['4']['count_woman']=intval($count_woman);
	$outmas['4']['count_man']=intval($count_man);
	$outmas['4']['count_without_photo']=intval($count_without_photo);
	$outmas['4']['count_with_photo']=$outmas['1']['count_people']-intval($count_without_photo);//intval($count_with_photo);
	//$outmas['4']['count_without_city']=$count_without_city++;
	//$outmas['4']['age']=$age_mas;
	ksort($age_mas);
	foreach ($age_mas as $key => $item)
	{
		if (($key!=date('Y')) && ($key!=0) && ($key>0))
		{
			$outmas['4']['age'][0][]=$key;
			$outmas['4']['age'][1][]=$item;
			$count_with_age+=$item;
		}
		//else
		//{
		//	$count_without_age+=$item;
		//}
	}
	$outmas['4']['age'][0][]='Неизвестно';
	$outmas['4']['age'][1][]=$outmas['1']['count_people']-$count_with_age;
	arsort($loc_cou_mas);
	arsort($loc_mas1);
	foreach ($loc_mas1 as $key => $item)
	{
		$outmas['4']['loc'][0][]=$key;
		$outmas['4']['loc'][1][]=$item;
		$c_loc+=$item;
	}
	$outmas['4']['loc'][0][]='Неизвестно';
	$outmas['4']['loc'][1][]=$outmas['1']['count_people']-$c_loc;//$count_without_city;
	foreach ($loc_cou_mas as $key => $item)
	{
		$outmas['4']['cou'][0][]=$key;
		$outmas['4']['cou'][1][]=$item;
		$c_cou+=$item;
	}
	$outmas['4']['cou'][0][]='Неизвестно';
	$outmas['4']['cou'][1][]=$outmas['1']['count_people']-$c_loc;//$count_without_city;	
	//$outmas['4']['cou']=$loc_cou_mas;
	//$outmas['4']['loc']=$loc_mas1;
	$outmas['1']['link']=$group_link;
	$outmas['1']['start']=$start_time;
	$outmas['1']['end']=$end_time;
	
	//echo $groupid;                         newsfeed.get  source_ids
	$offset=0;
	$pos_p2=-1;
	do
	{
		usleep(500000);
		$json=parseUrl('https://api.vkontakte.ru/method/wall.get?owner_id=-'.$groupid.'&count=100&start_time='.strtotime($start_time).'&end_time='.strtotime($end_time).'&extended=1&access_token='.$access_token.'&offset='.($offset*100));
		$offset++;
		echo 'https://api.vkontakte.ru/method/wall.get?owner_id=-'.$groupid.'&count=100&start_time='.strtotime($start_time).'&end_time='.strtotime($end_time).'&extended=1&access_token='.$access_token.'&offset='.($offset*100)."\n";
		$data_gr=json_decode($json, true);
		//для neewsfeed.get $outmas['2']['count_posts']=count($data_gr['response']['items']);
		//для wall.get
		//$outmas['2']['count_posts']=array_shift($data_gr['response']['wall']);

		foreach ($data_gr['response']['profiles'] as $key => $item)
		{
			$assoc_user[$item['uid']]=preg_replace('/[^а-яА-Яa-zA-Z0-9]/isu',' ',($item['first_name'].' '.$item['last_name']));
		}
		//print_r($assoc_user);
		foreach ($data_gr['response']['wall'] as $key => $item) //['items']
		{
			if (($item['date']>=strtotime($start_time)) && ($item['date']<=strtotime($end_time)))
			{
				echo 1;
				$outmas['2']['count_posts']++;
				$pos_p2++;
				//print_r($item);
				$actions[$item['from_id']]++;
				foreach ($item['attachments'] as $k => $itt)
				{
					if (!isset($yet[$itt['type']]))
					{
						$type_posts.=$assoc_type_p[$itt['type']].' ';
						$yet[$itt['type']]++;
					}
				}
				echo 2;
				unset($yet);
				if ($item['text']!='')
				{
					$type_posts.='текст';//'text';
				}
				//echo $type_posts."\n";
				$types[$type_posts]++;// суммируем типы постов
				$p_graph[mktime(0,0,0,date('n',$item['date']),date('j',$item['date']),date('Y',$item['date']))][$type_posts]++;
				//$p_graph[$item['date']][$type_posts]++;
				$count_comments+=$item['comments']['count'];
				$count_likes+=$item['likes']['count'];
				$count_reposts+=$item['reposts']['count'];
				/*if ($item['text']!='')
				{
					$outmas['2']['posts'][0][$key]='post';
				}
				else
				{
					$outmas['2']['posts'][0][$key]=$item['attachments'][0]['type'];
				}*/
				$outmas['2']['posts'][0][$pos_p2]=preg_replace('/[^а-яА-Яa-zA-Z0-9]/isu',' ',$type_posts);
				$type_posts='';
				$outmas['2']['posts'][1][$pos_p2]=date('H:i:s d.m.Y',$item['date']);
				//$outmas['2']['posts'][2][$key]=$item['source_id']; 
				//$outmas['2']['posts'][3][$key]=(($item['source_id']==('-'.$groupid))?$data['response'][0]['name']:$all_users[$item['source_id']]['name']);
				if ($item['from_id']==('-'.$groupid))
				{
					$au_link='http://vk.com/club'.preg_replace('/\-/ius','',$item['from_id']);
				}
				else
				{
					$au_link='http://vk.com/id'.$item['from_id'];
				}
				$outmas['2']['posts'][2][$pos_p2]=$au_link; 
				$outmas['2']['posts'][3][$pos_p2]=preg_replace('/[^а-яА-Яa-zA-Z0-9]/isu',' ',(($item['from_id']==('-'.$groupid))?$data['response'][0]['name']:$assoc_user[$item['from_id']]));
				echo 3;
				if ($item['likes']['count']!=0)// берем лайки от каждого поста и суммируем действия пользователей
				{
					usleep(500000);
					$who_likes=parseUrl('https://api.vkontakte.ru/method/likes.getList?type=post&count=1000&owner_id=-'.$groupid.'&item_id='.$item['id'].'&access_token='.$access_token);
					//echo 'https://api.vkontakte.ru/method/likes.getList?type=post&owner_id=-'.$groupid.'&item_id='.$item['id'].'&access_token='.$access_token."\n";
					$mas_who_likes=json_decode($who_likes,true);
					foreach ($mas_who_likes['response']['users'] as $itt2)
					{
						if ($itt2!='')
						$actions[$itt2]++;
						$mb_not_gr[$itt2]++;
					}
				}
				echo 4;
				$outmas['2']['posts'][4][$pos_p2]=$item['likes']['count'];
				$outmas['2']['posts'][5][$pos_p2]=$item['comments']['count'];
				$outmas['2']['posts'][6][$pos_p2]=((substr($item['text'],0,1)=='=')?'\'':'').preg_replace('/[^а-яА-Яa-zA-Z0-9\,\.\-\=\+\/\"\'\^\#\$\@\!\(\)]/isu',' ',str_replace('\"','',str_replace("\n",'',addslashes((rtrim(strip_tags(htmlspecialchars_decode($item['text']))))))));//$item['text'];     preg_replace('/[^а-яА-Яa-zA-Z]/isu',' ',addslashes(rtrim($item['text'])));//
				$actions[$item['from_id']]++;
		
				if ($item['comments']['count']!=0)// берем комментарии к каждому посту и суммируем действия пользователей
				{
					usleep(500000);
					$who_comm=parseUrl('https://api.vkontakte.ru/method/wall.getComments?owner_id=-'.$groupid.'&count=100&post_id='.$item['id'].'&access_token='.$access_token.'&need_likes=1');
					//echo 'https://api.vkontakte.ru/method/wall.getComments?owner_id=-'.$groupid.'&count=100&post_id='.$item['id'].'&access_token='.$access_token."\n";
					$mas_who_comm=json_decode($who_comm,true);
					echo 5;
					foreach ($mas_who_comm['response'] as $itt3)
					{
						if ($itt3['uid']!='')
						$actions[$itt3['uid']]++;
						$mb_not_gr[$itt2]++;
						//time
						if (($itt3['date']>strtotime($start_time)) && ($itt3['date']<=strtotime($end_time)))
						{
							$types['комментарий']++;// суммируем типы постов
							$p_graph[mktime(0,0,0,date('n',$itt3['date']),date('j',$itt3['date']),date('Y',$itt3['date']))]['комментарий']++;
							$pos_p2++;
							$outmas['2']['posts'][0][$pos_p2]=preg_replace('/[^а-яА-Яa-zA-Z0-9]/isu',' ','комментарий');
							$outmas['2']['posts'][1][$pos_p2]=date('H:i:s d.m.Y',$itt3['date']);
							$outmas['2']['posts'][2][$pos_p2]='http://vk.com/id'.$itt3['uid'];
							$outmas['2']['posts'][3][$pos_p2]=preg_replace('/[^а-яА-Яa-zA-Z0-9]/isu',' ',(($itt3['uid']==('-'.$groupid))?$data['response'][0]['name']:$assoc_user[$itt3['uid']]));
							$outmas['2']['posts'][4][$pos_p2]=$itt3['likes']['count'];
							$outmas['2']['posts'][5][$pos_p2]=0;
							$outmas['2']['posts'][6][$pos_p2]=preg_replace('/[^а-яА-Яa-zA-Z0-9]/isu',' ',$itt3['text']);
						}
					}
				}
				echo 6;
			}

	
		}
	}
	while (count($data_gr['response']['wall'])>99);
	/*$outmas['2']['count_with_poll']=$count_with_poll;
	$outmas['2']['count_with_photo']=$count_with_photo;
	$outmas['2']['count_without']=$count_without;*/
	$outmas['2']['posts']=array();
	$posts_graph[0][0]='Дата:';
	foreach ($types as $key => $item)
	{
		$outmas['2']['types'][0][]=$key;
		$outmas['2']['types'][1][]=$item;
		$posts_graph[][0]=$key;
	}
	$g=1;
	//print_r($p_graph);
	ksort($p_graph);
	//print_r($p_graph);
	foreach ($p_graph as $key => $item)
	{
		$posts_graph[0][]=date('d.m.Y',$key);
		foreach ($types as $k => $i)
		{
			$posts_graph[$g][]=intval($item[$k]);
			$g++;
		}
		$g=1;
	}
	//array_multisort($posts_graph[0],SORT_ASC,$posts_graph[1],SORT_ASC,$posts_graph[2],SORT_ASC,$posts_graph[3],SORT_ASC,$posts_graph[4],SORT_ASC,$posts_graph[5],SORT_ASC,$posts_graph[6],SORT_ASC,$posts_graph[7],SORT_ASC,$posts_graph[8],SORT_ASC,$posts_graph[9],SORT_ASC);
	/*for ($i=1;$i<count($post_graph[0])-1;$i++)
	{
		$posts_graph[0][$i]=0;//date('d.m.Y',$posts_graph[0][$i]);
	}*/
	$outmas['2']['posts_graph']=$posts_graph;
	//$outmas['2']['posts_graph']=array();
	//$outmas['2']['types']=$types;
	$outmas['2']['count_comments']=intval($count_comments);
	$outmas['2']['count_likes']=intval($count_likes);
	$outmas['2']['count_reposts']=intval($count_reposts);
	arsort($actions);
	$outmas['3']['acccc']=$actions;
	//print_r($actions);
	$kk=0;
	foreach ($actions as $key => $item)
	{
		//echo '|'.$key.' '.$item.'|';
		$top_actions[$item]++;
		//$i++;
		if ($item<5) 
		{
			//break;
			//$i++;
		}
		else
		{
			if ($key==('-'.$groupid))
			{
				$nkey='http://vk.com/club'.preg_replace('/\-/ius','',$key);
			}
			else
			{
				$nkey='http://vk.com/id'.$key;
			}
			$outmas['3']['list'][0][$kk]=$nkey;
			$outmas['3']['list'][1][$kk]=(isset($assoc_user[$key])?$assoc_user[$key]:$outmas['1']['name']);
			$kk++;
		}
	}
	$outmas['3']['acccc1']=$top_actions;
	foreach ($top_actions as $key => $item)
	{
		if ($key>5)
		{
			$cm5++;
		}
		//$outmas['3']['topactions'][0][]=$key;
		//$outmas['3']['topactions'][1][]=$item;
	}
	$outmas['3']['topactions'][0][]=0;
	$outmas['3']['topactions'][1][]=$outmas['1']['count_people']-intval($top_actions[1])-intval($top_actions[2])-intval($top_actions[3])-intval($top_actions[4])-$cm5;
	$outmas['3']['topactions'][0][]=1;
	$outmas['3']['topactions'][1][]=intval($top_actions[1]);
	$outmas['3']['topactions'][0][]=2;
	$outmas['3']['topactions'][1][]=intval($top_actions[2]);
	$outmas['3']['topactions'][0][]=3;
	$outmas['3']['topactions'][1][]=intval($top_actions[3]);
	$outmas['3']['topactions'][0][]=4;
	$outmas['3']['topactions'][1][]=intval($top_actions[4]);
	$outmas['3']['topactions'][0][]=5;
	$outmas['3']['topactions'][1][]=intval($top_actions[5]);
	$outmas['3']['topactions'][0][]='>5';
	$outmas['3']['topactions'][1][]=intval($cm5);
	//$outmas['3']['more5']=(($i==1)?0:$i);
	$outmas['3']['more5']=$kk;
	//$outmas['3']['less5']=((count($actions)-$i)==-1?0:count($actions)-$i);
	$outmas['3']['less5']=((count($actions)-$kk)==-1?0:count($actions)-$kk);
	//$outmas['3']['passive']=(($cgr_info['response']['count']-$i-$outmas['3']['less5'])<0?0:($cgr_info['response']['count']-$i-$outmas['3']['less5']));
	$outmas['3']['passive']=$outmas['1']['count_people']-intval($top_actions[1])-intval($top_actions[2])-intval($top_actions[3])-intval($top_actions[4])-$cm5;//$cgr_info['response']['count']-$outmas['3']['more5']-$outmas['3']['less5'];

	$outmas['3']['all']=$cgr_info['response']['count'];
	//$outmas['4']=$all_us;
	//$outmas['4']=array();
	$outmas['1']['count_block']=$count_deactivated;
	$outmas['1']['count_delete']=intval($count_deleted);
	foreach ($all_users as $key => $item)
	{
		unset($mb_not_gr[$key]);
	}
	$k=0;
	$indx=0;
	//бля unset($mb_not_gr);
	foreach ($mb_not_gr as $key => $item)
	{
		$text_ids.=$zap.$key;
		$zap=',';
		$k++;
		if ($k==250)
		{
			usleep(500000);
			$info_users=parseUrl('https://api.vkontakte.ru/method/getProfiles?uids='.urlencode($text_ids).'&fields=uid,counters,first_name,last_name,nickname,sex,bdate,city,country,timezone,photo,photo_medium,photo_big,domain,has_mobile,rate,contacts,education&access_token='.$access_token);
			$data_users=json_decode($info_users, true);
			//print_r($data_users);
			foreach ($data_users['response'] as $key => $item)
			{
				$not_in_group[0][]=$indx;
				$not_in_group[1][]=preg_replace('/[^а-яА-Яa-zA-Z]/isu',' ',$item['first_name']);
				$not_in_group[2][]=preg_replace('/[^а-яА-Яa-zA-Z]/isu',' ',$item['last_name']);
				$not_in_group[3][]=$item['domain'];
				$not_in_group[4][]=preg_replace('/[^а-яА-Яa-zA-Z]/isu',' ',$item['nickname']);
				$not_in_group[5][]='http://vk.com/id'.$item['uid'];
				$not_in_group[6][]=$m_gen[$item['sex']];
				$not_in_group[7][]=preg_replace('/[^а-яА-Яa-zA-Z0-9\.]/isu',' ',$item['bdate']);
				$not_in_group[8][]=preg_replace('/[^а-яА-Яa-zA-Z0-9]/isu',' ',$cities[$item['city']]);
				$not_in_group[9][]=preg_replace('/[^а-яА-Яa-zA-Z0-9]/isu',' ',$countries[$item['country']]);
				$not_in_group[10][]=preg_replace('/[^а-яА-Яa-zA-Z0-9]/isu',' ',$item['timezone']);
				$not_in_group[11][]=preg_replace('/[^а-яА-Яa-zA-Z0-9\:\/\.]/isu',' ',$item['photo']);
				$not_in_group[12][]=preg_replace('/[^а-яА-Яa-zA-Z0-9]/isu',' ',$item['has_mobile']);
				$not_in_group[13][]=preg_replace('/[^а-яА-Яa-zA-Z0-9]/isu',' ',$item['rate']);
				$not_in_group[14][]=preg_replace('/[^а-яА-Яa-zA-Z0-9]/isu',' ',$item['mobile_phone']);
				$not_in_group[15][]=preg_replace('/[^а-яА-Яa-zA-Z0-9]/isu',' ',$item['home_phone']);
				$not_in_group[16][]=preg_replace('/[^а-яА-Яa-zA-Z0-9]/isu',' ',$item['university_name']);
				$not_in_group[17][]=preg_replace('/[^а-яА-Яa-zA-Z0-9]/isu',' ',$item['faculty_name']);
				$not_in_group[18][]=preg_replace('/[^а-яА-Яa-zA-Z0-9]/isu',' ',$item['graduation']);
				$indx++;
			}
			$zap='';
			$k=0;
		}
	}
	//if ($k==250)
	{
		usleep(500000);
		$info_users=parseUrl('https://api.vkontakte.ru/method/getProfiles?uids='.urlencode($text_ids).'&fields=uid,counters,first_name,last_name,nickname,sex,bdate,city,country,timezone,photo,photo_medium,photo_big,domain,has_mobile,rate,contacts,education&access_token='.$access_token);
		$data_users=json_decode($info_users, true);
		//print_r($data_users);
		foreach ($data_users['response'] as $key => $item)
		{
			$not_in_group[0][]=$indx;
			$not_in_group[1][]=preg_replace('/[^а-яА-Яa-zA-Z]/isu',' ',$item['first_name']);
			$not_in_group[2][]=preg_replace('/[^а-яА-Яa-zA-Z]/isu',' ',$item['last_name']);
			$not_in_group[3][]=$item['domain'];
			$not_in_group[4][]=preg_replace('/[^а-яА-Яa-zA-Z]/isu',' ',$item['nickname']);
			$not_in_group[5][]='http://vk.com/id'.$item['uid'];
			$not_in_group[6][]=$m_gen[$item['sex']];
			$not_in_group[7][]=preg_replace('/[^а-яА-Яa-zA-Z0-9\.]/isu',' ',$item['bdate']);
			$not_in_group[8][]=preg_replace('/[^а-яА-Яa-zA-Z0-9]/isu',' ',$cities[$item['city']]);
			$not_in_group[9][]=preg_replace('/[^а-яА-Яa-zA-Z0-9]/isu',' ',$countries[$item['country']]);
			$not_in_group[10][]=preg_replace('/[^а-яА-Яa-zA-Z0-9]/isu',' ',$item['timezone']);
			$not_in_group[11][]=preg_replace('/[^а-яА-Яa-zA-Z0-9\:\/\.]/isu',' ',$item['photo']);
			$not_in_group[12][]=preg_replace('/[^а-яА-Яa-zA-Z0-9]/isu',' ',$item['has_mobile']);
			$not_in_group[13][]=preg_replace('/[^а-яА-Яa-zA-Z0-9]/isu',' ',$item['rate']);
			$not_in_group[14][]=preg_replace('/[^а-яА-Яa-zA-Z0-9]/isu',' ',$item['mobile_phone']);
			$not_in_group[15][]=preg_replace('/[^а-яА-Яa-zA-Z0-9]/isu',' ',$item['home_phone']);
			$not_in_group[16][]=preg_replace('/[^а-яА-Яa-zA-Z0-9]/isu',' ',$item['university_name']);
			$not_in_group[17][]=preg_replace('/[^а-яА-Яa-zA-Z0-9]/isu',' ',$item['faculty_name']);
			$not_in_group[18][]=preg_replace('/[^а-яА-Яa-zA-Z0-9]/isu',' ',$item['graduation']);
			$indx++;
		}
		$zap='';
		$k=0;
	}
	//print_r($not_in_group);
	/*if (count($all_us[0])<50000)
	{
		$outmas['5']['not_in_group']=$all_us;
	}
	else
	{*/
		$outmas['5']['not_in_group']=array();
	//}
	//unset($outmas['4']);
	//print_r($mb_not_gr);
	//print_r($all_users);
	//print_r($outmas);
	$fp = fopen('vkgr.txt', 'w');
	fwrite($fp, json_encode($outmas));
	fclose($fp);
	//print_r($outmas);
	die();	
	$descriptorspec=array(
		0 => array("pipe","r"),
		1 => array("pipe","w"),
		2 => array("file", "/tmp/error-output.txt", "a")
		);

	$cwd='/var/www/new/modules';
	$end=array('');
	//$pipes=json_encode($outmas);
	$process=proc_open('perl /var/www/project/excel/vkontakte.pl',$descriptorspec,$pipes,$cwd,$end);
	//echo "\n".$row['post_link']."\n";
	if (is_resource($process))
	{
		fwrite($pipes[0], json_encode($outmas));
		fclose($pipes[0]);
		//echo $return_value;
		//print_r($pipes);
		$fulltext=stream_get_contents($pipes[1]);
		$return_value=proc_close($process);

		header("Content-type: application/vnd.ms-excel");
		header("Content-Disposition: attachment; filename=wobot-vkontakte.xls");
		$fp = fopen('exp.xls', 'w');
		fwrite($fp, $fulltext);
		fclose($fp);
		echo $fulltext;
		//echo (stream_get_contents($pipes[1]);
	}
}
else
{
	echo '<html><head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<script type="text/javascript" src="http://bmstu.wobot.ru/new_js/js/jquery.js"></script>
	<script type="text/javascript" src="http://bmstu.wobot.ru/new_js/js/datepicker.js"></script>
	<link href="http://bmstu.wobot.ru/new_css/css/datepicker.css" rel="stylesheet" type="text/css" />
	</head><body>
	<form method="GET">
	Экспорт групп vkonakte, для экпорта надо ввести название группы из ссылки (пример http://vk.com/<font color="red">dailyevents</font>)<br>
	Начало периода:  <input name="ts" id="sd" value="'.date('j.n.Y',time()).'" type="text" style="font-size: 20px;"><br>
	Конец периодна:  <input name="te" id="sd" value="'.date('j.n.Y',time()).'" type="text" style="font-size: 20px;"><br>
	Название группы: <input name="ngroup" id="sd" value="" type="text" style="font-size: 20px;"><br>
	<input type="submit">
	</form>
	</body></html>';
}
?>
