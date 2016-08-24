<?
session_start();

//require_once('/var/www/com/config.php');
//require_once('/var/www/com/func.php');
//require_once('/var/www/com/db.php');
require_once('/var/www/daemon/bot/kernel.php');
require_once('ch.php');

//error_reporting(E_ERROR | E_WARNING | E_PARSE);
error_reporting(E_ERROR);
ignore_user_abort(true);
set_time_limit(0);
ini_set('max_execution_time',0);
ini_set('default_charset','utf-8');
ob_implicit_flush();

//$db = new database();
//$db->connect();

/*print_r($_GET);
print_r($_POST);
print_r($_SESSION);*/

$appid='2785229';
$appsecret='OGycTbxYl9ImDBpf3bCH';
$real_access_token='9a90df38a9f778671c09b0aec360ed92753668df8331b3972d11250a97e26e4e8f366a4b74807bd2703d8';
$_POST['start']='17.02.2012';
$_POST['start']='18.02.2012';

function check_vk_content($cont)
{
	return intval(json_decode($cont,true));
}

function get_vkontakte($keyword,$st,$et,$lang,$access_token,$proxys)
{
	global $val,$real_access_token,$redis;
	$ctokens=json_decode($redis->get('at_vk'),true);
	shuffle($ctokens);
	$real_access_token=$ctokens[0];
	$k2=$keyword;
	//global $access_token;
	$tmp_keyword=$keyword;
	$offset=0;
	$keyword=preg_replace('/\~+[а-яА-Яa-zA-Z\-\ \.\'\"]+/isu','',$keyword);
	$keyword=preg_replace('/[^а-яА-Яa-zA-Z0-9\-\'\’\ \.\#]/isu','  ',$keyword);
	$keyword=explode('  ',$keyword);
	// $keyword=get_simple_query($tmp_keyword,'vk');
	//print_r($keyword);
	$i_proxy=0;
	foreach ($keyword as $it2)
	{
		echo '/';
		if (($it2!='') && ($it2!=' '))
		{
			$offset=0;
			do
			{
				//sleep(1);
				do
				{
					echo '.';
					//$cont=parseUrlproxy('https://api.vkontakte.ru/method/newsfeed.search?q='.urlencode($it2).'&count=100&extended=1&offset='.($offset*100),$proxys[$i_proxy]);
					$cont=parseUrl('https://api.vkontakte.ru/method/newsfeed.search?q='.urlencode($it2).'&count=100&extended=1&offset='.($offset*100));
					// echo 'https://api.vkontakte.ru/method/newsfeed.search?q='.$it2.'&count=100&extended=1&offset='.($offset*100);
					if (check_vk_content($cont)==0)
					{
						echo '*';
						$i_proxy++;
					}
				}
				while ((check_vk_content($cont)==0) && ($i_proxy<count($proxys)));
				//echo 'https://api.vkontakte.ru/method/newsfeed.search?q='.$it2.'&count=100&start_time='.$st.'&end_time='.$et.'&extended=1&access_token='.$access_token.'&offset='.($offset*100)."\n";
				$mas=json_decode($cont,true);
				$i=0;
				// print_r($mas);
				foreach ($mas['response'] as $key => $item)
				{
					$count_vk++;
					// echo $st.' '.$item['date'].' '.$et."\n";
					if (($item['date']>=$st) && ($item['date']<$et))
					{
						// echo '!';
						//echo check_post(strip_tags($item['text']),$k2).' '.strip_tags($item['text'])."\n";
						//if (($i>0) && ($item['owner_id'][0]!='-') && (check_post(strip_tags($item['text']),$k2)==1))
						if ($i>0)
						{
							// print_r($item);
							$attach_text='';
							foreach ($item['attachments'] as $kk => $ii)
							{
								if ($mas['response'][$key]['attachments'][$kk]['photo']['text']=='') continue;
								if ($mas['response'][$key]['attachments'][$kk]['audio']['performer']=='') continue;
								if ($mas['response'][$key]['attachments'][$kk]['audio']['title']=='') continue;
								$attach_text.=' '.$mas['response'][$key]['attachments'][$kk]['photo']['text'].' '.$mas['response'][$key]['attachments'][$kk]['audio']['performer'].' - '.$mas['response'][$key]['attachments'][$kk]['audio']['title'];
							}
							if (check_post($item['text'].$attach_text,$tmp_keyword)==0) continue;
							//правильный формат http://vkontakte.ru/id142159369?status=1872
							// fuck spam generator!!!
							//$outmas['link'][]='http://vk.com/wall'.$item['owner_id'].'_'.$item['id'];
							if ($item['owner_id']>0)
							{
								$outmas['link'][]='http://vk.com/wall'.$item['owner_id'].'_'.$item['id'];
							}
							else
							{
								$outmas['link'][]='http://vk.com/wall'.$item['owner_id'].'_'.$item['id'];
							}
							$outmas['content'][]=mb_substr(strip_tags($item['text']),0,150,'UTF-8');
							$outmas['fulltext'][]=strip_tags($item['text']);
							foreach ($item['attachments'] as $kk => $ii)
							{
								if (isset($mas['response'][$key]['attachments'][$kk]['photo'])) $outmas['fulltext'][count($outmas['fulltext'])-1].=' '.$mas['response'][$key]['attachments'][$kk]['photo']['text'];
								if (isset($mas['response'][$key]['attachments'][$kk]['audio'])) $outmas['fulltext'][count($outmas['fulltext'])-1].=' '.$mas['response'][$key]['attachments'][$kk]['audio']['performer'].' - '.$mas['response'][$key]['attachments'][$kk]['audio']['title'];
							}
							$outmas['time'][]=$item['date'];
							$eng['comment']=intval($item['comments']['count']);
							$eng['likes']=intval($item['likes']['count']);
							$eng['repost']=intval($item['reposts']['count']);
							$outmas['engage'][]=json_encode($eng);
						}
					}
					if (count($outmas['time'])>100) $outmas=post_slice($outmas);
					$i++;
				}
				$offset++;
			}
			while ((count($mas['response'])>99) && ($mas['response'][count($mas['response'])-1]['date']>=$st));
			$offset=0;
			$mintime=99999999999;
			do
			{
				do
				{
					echo '.';
					//$cont=parseUrlproxy('https://api.vkontakte.ru/method/video.search?q='.urlencode($it2).'&count=100&sort=0&access_token='.$real_access_token.'&offset='.($offset*100),$proxys[$i_proxy]);
					$cont=parseUrl('https://api.vkontakte.ru/method/video.search?q='.urlencode($it2).'&count=100&sort=0&access_token='.$real_access_token.'&offset='.($offset*100));
					// echo 'https://api.vkontakte.ru/method/video.search?q='.urlencode($it2).'&count=100&sort=0&access_token='.$real_access_token.'&offset='.($offset*100)."\n";
					//echo 'https://api.vkontakte.ru/method/video.search?q='.$it2.'&count=200&sort=0&access_token='.$access_token.'&offset='.($offset*200);
					if (check_vk_content($cont)==0)
					{
						echo '*';
						$i_proxy++;
					}
				}
				while ((check_vk_content($cont)==0) && ($i_proxy<count($proxys)));
				$mas=json_decode($cont,true);
				foreach ($mas['response'] as $key => $item)
				{
					$count_vk_video++;
					if (($item['date']>=$st) && ($item['date']<$et))
					{
						//echo check_post(strip_tags($item['title']),$k2).' '.strip_tags($item['title'])."\n";
					}
					if ((($item['date']>=$st) && ($item['date']<$et)) && ((check_post(strip_tags($item['description']),$k2)==1) || (check_post(strip_tags($item['title']),$k2)==1)))
					{
						$outmas['time'][]=$item['date'];
						$outmas['content'][]=mb_substr(($item['description']!=''?$item['description']:$item['title']),0,150,'UTF-8');
						$outmas['fulltext'][]=($item['description']!=''?$item['description']:$item['title']);
						$outmas['link'][]='http://vk.com/video'.$item['owner_id'].'_'.$item['id'];
						if ($item['data']<$mintime)
						{
							$mintime=$item['date'];
						}
						$outmas['engage'][]='';
					}
					if (count($outmas['time'])>100) $outmas=post_slice($outmas);
				}
			}
			while ($mintime<$st);
		}
	}
	echo "\n";
	add_source_log('vkontakte',intval($count_vk));
	add_source_log('vkontakte_video',intval($count_vk_video));
	// print_r($outmas);
	return $outmas;
}

// get_vkontakte('"Московский картофель"|"Московского картофеля"|"Московскому картофелю"|"Московский картофель"|"Московским картофелем"|"Московским картофеле"|"Московская картошка"|"Московской картошки"|"Московской картошке"|"Московскую картошку"|"Московской картошкой"',1360872000,1363696133,'ru',$access_token,array('212.119.97.198:3128','197.155.65.2:8080','186.101.70.10:3128','141.85.204.56:1920','123.101.232.4:8080','177.69.219.145:3128','200.183.80.148:3128','109.173.47.238:80','80.79.179.10:8181','46.191.192.94:3128','46.50.220.13:3128'));

?>
