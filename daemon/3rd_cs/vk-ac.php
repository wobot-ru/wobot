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
// require_once('/var/www/daemon/com/config.php');
// require_once('/var/www/daemon/com/func.php');
// require_once('/var/www/daemon/com/db.php');
// require_once('/var/www/daemon/bot/kernel.php');

$db = new database();
$db->connect();

$redis=new Redis() or die("Can'f load redis module.");
$redis->connect('127.0.0.1');

// error_reporting(0);
//ignore_user_abort(true);
set_time_limit(0);
ini_set('max_execution_time',0);
ob_implicit_flush();
ini_set("memory_limit", "2048M");
date_default_timezone_set ( 'Europe/Moscow' );

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
$access_token='ef20e8639419177666bcd0c6cdf7db0ff28344c023d3540ba9cb460fad05afcf313fdc85f8c70644008b5';//аня жданова//'9902a6a19912b0219912b021569938cd1599912991d08dfb6bfe36737e288c9';//'c87eb1fbc8690a13c8690a1372c8437727cc869c866b2eda9d782f77b2ba1d5';

function get_vk_account($grid,$ts,$te)
{
	global $access_token,$redis;

	$cont_token_vk=$redis->get('at_vk');
	$mcont_token_vk=json_decode($cont_token_vk,true);
	$access_token=$mcont_token_vk[rand(0,count($mcont_token_vk)-1)];

	$json=parseUrl('https://api.vkontakte.ru/method/getProfiles?uids='.urlencode($grid).'&access_token='.$access_token);
	$grinfo=json_decode($json,true);
	$gr_id=$grinfo['response'][0]['uid'];
	// print_r($grinfo);
	do
	{
		sleep(1);
		// echo 'https://api.vkontakte.ru/method/wall.get?owner_id='.$gr_id.'&count=100&start_time='.($ts).'&end_time='.($te).'&extended=1&access_token='.$access_token.'&offset='.intval($offset*100);
		$json=parseUrl('https://api.vkontakte.ru/method/wall.get?owner_id='.$gr_id.'&count=100&start_time='.($ts).'&end_time='.($te).'&extended=1&access_token='.$access_token.'&offset='.intval($offset*100));
		echo '.';
		$cont_wall=json_decode($json,true);
		$offset++;
		foreach ($cont_wall['response']['wall'] as $key => $item)
		{
			if ($key>0)
			{
				if (($item['date']>$ts) && ($item['date']<$te))
				{
					$out['time'][]=$item['date'];
					$out['link'][]='http://vk.com/wall'.$gr_id.'_'.$item['id'];
					$out['content'][]=$item['text'];
					$out['engage'][]=intval($item['comments']['count']+$item['likes']['count']+$item['reposts']['count']);
					unset($adv_eng);
					$adv_eng['likes']=intval($item['likes']['count']);
					$adv_eng['comment']=intval($item['comments']['count']);
					$out['adv_engage'][]=$adv_eng;
					$out['author_id'][]=$item['from_id'];
					$comm_offset=0;
					if ($item['comments']['count']>0)
					{
						do 
						{
							sleep(1);
							$json_comm=parseUrl('https://api.vkontakte.ru/method/wall.getComments?owner_id=-'.$gr_id.'&count=100&post_id='.$item['id'].'&access_token='.$access_token.'&need_likes=1&offset='.intval($comm_offset*100));
							$comm_offset++;
							echo '/';
							$cont_wall_comm=json_decode($json_comm,true);
							foreach ($cont_wall_comm['response'] as $keyc => $itemc)
							{
								if ($keyc>0)
								{
									if (($itemc['date']>$ts) && ($itemc['date']<$te))
									{
										$out['time'][]=$itemc['date'];
										$out['link'][]='http://vk.com/wall'.$gr_id.'_'.$itemc['cid'];
										$out['content'][]=$itemc['text'];
										$out['engage'][]=intval($itemc['likes']['count']);
										$out['adv_engage'][]['likes']=intval($itemc['likes']['count']);
										$out['author_id'][]=$itemc['from_id'];
									}
								}
							}
						}
						while ((count($cont_wall_comm['response'])==101) && ($cont_wall_comm['response'][count($cont_wall_comm['response'])-1]['date']>$ts));
					}
				}
			}
		}
	}
	while ((count($cont_wall['response']['wall'])==101) && ($cont_wall['response']['wall'][count($cont_wall['response']['wall'])-1]['date']>$ts));
	// print_r($out);
	return $out;
}

// get_vk_account('1555432',mktime(0,0,0,12,1,2012),mktime(0,0,0,12,5,2015));

?>
