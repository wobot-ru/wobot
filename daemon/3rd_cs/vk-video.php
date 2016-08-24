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

function get_vk_video($gid,$vid,$ts,$te)
{
	global $access_token,$redis;

	$cont_token_vk=$redis->get('at_vk');
	$mcont_token_vk=json_decode($cont_token_vk,true);
	$access_token=$mcont_token_vk[rand(0,count($mcont_token_vk)-1)];

	do
	{
		sleep(1);
		echo '.';
		$cont=parseUrl('https://api.vkontakte.ru/method/video.getComments?vid='.$vid.'&owner_id='.$gid.'&access_token='.$access_token.'&count=100&offset='.intval($i*100));
		$mas=json_decode($cont,true);
		foreach ($mas['response'] as $key => $item)
		{
			if ($key==0) continue;
			if (($item['date']<$ts) || ($item['date']>($te+86400))) continue;
			$out['link'][]='http://vk.com/video-'.$gid.'_'.$vid.'?reply='.$item['id'];
			$out['time'][]=$item['date'];
			$out['content'][]=$item['message'];
			$out['author_id'][]=$item['from_id'];
		}
		$i++;
	}
	while ((count($mas['response'])==101) && ($mas['response'][100]['date']<($te+86400)));
	//print_r($out);
	return $out;
}

function get_vk_video_album($gid,$ts,$te)
{
	global $access_token;
	do
	{
		sleep(1);
		$cont=parseUrl('https://api.vkontakte.ru/method/video.get?'.($gid[0]=='-'?'g':'u').'id='.$gid.'&access_token='.$access_token.'&count=100&offset='.intval($i*100));
		$mas=json_decode($cont,true);
		//print_r($mas);
		foreach ($mas['response'] as $key => $item)
		{
			if ($key==0) continue;
			echo '/';
			$out=get_vk_video($gid,$item['vid'],$ts,$te);
			//print_r($out);
			foreach ($out['time'] as $kk => $ii)
			{
				$outmas['link'][]=$out['link'][$kk];
				$outmas['time'][]=$out['time'][$kk];
				$outmas['content'][]=$out['content'][$kk];
				$outmas['author_id'][]=$out['author_id'][$kk];
			}
		}
		$i++;
	}
	while (count($mas['response']['topics'])==101);
	//print_r($outmas);
	return $outmas;
}
//get_vk_video_album(1,mktime(0,0,0,11,20,2011),mktime(0,0,0,11,30,2012));
//get_vk_board_topic('20225241','27216172',mktime(0,0,0,10,1,2012),mktime(0,0,0,10,30,2012));

?>
