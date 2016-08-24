<?
require_once('/var/www/daemon/com/config.php');
require_once('/var/www/daemon/com/func.php');
require_once('/var/www/daemon/com/db.php');
require_once('/var/www/daemon/bot/kernel.php');
require_once('ch.php');

date_default_timezone_set('Europe/Moscow');
// error_reporting(E_ERROR);
ignore_user_abort(true);
set_time_limit(0);
ini_set('max_execution_time',0);
ini_set('default_charset','utf-8');
ob_implicit_flush();

function check_facebook_content($cont)
{
	return intval(json_decode($cont,true));
}

$fb_access_token='CAAHBqPjctnEBADsE8Q2eMZCakinp3MyUAiyrQqh2gfxgigSRQXQbdbZC25fqouSrAddoGaoyrOwxaveevVwqErifULMoUNOmBKKiKdm6yKNli96kUoMTmPiIERtFkp8nO1jUVgRZCMJ9thmQ1fAR0PgtLOTcE0ZD';

function get_facebook($keyword,$ts,$te,$lan,$proxys)
{	
	global $val,$fb_access_token,$redis;
	$fb_access_token=json_decode($redis->get('at_fb'),true);
	shuffle($fb_access_token);
	$fb_access_token=$fb_access_token[0];
	$mas_lan['ru']='ru';
	$mas_lan['en']='en_US';
	$mas_lan['az']='az';
	$k2=$keyword;
	//$mas_lan[$lan]='';
	//хуевое дело этот preg_replace
	//нужно пилить, чтобы обрабатывало запросы по человечески
	//если есть запрос водка|пиво, то оно должно прогонять два запроса к API:
	//http://graph.facebook.com/search?q=водка&type=post&limit=100&since='.$ts.'&until='.$te.'&locale='.$mas_lan[$lan]
	//http://graph.facebook.com/search?q=пиво&type=post&limit=100&since='.$ts.'&until='.$te.'&locale='.$mas_lan[$lan]
	$keyword=preg_replace('/\~+[а-яА-Яa-zA-Z\-\ \,\.]+/isu','',$keyword);
	$keyword=preg_replace('/[^а-яА-Яa-zA-Z0-9\ \'\-\’\.]/isu','  ',$keyword);
	//$keyword=preg_replace('/\s/isu',' ',$keyword);	
	///echo $keyword;
	$mkeyword=explode('  ',$keyword);
	//print_r($mkeyword);
	$i_proxy=0;
	foreach ($mkeyword as $word)
	{
		//sleep(1);
		//echo $word;
		echo '/';
		if (trim($word)=='') continue;
		if (($word!='') && ($word!=' ') && (mb_strlen($word,'UTF-8')>3))
		{
			do
			{
				$cont=parseUrlproxy('https://graph.facebook.com/search?q='.urlencode($word).'&type=post&limit=100&since='.$ts.'&until='.$te.'&locale='.$mas_lan[$lan].'&access_token='.$fb_access_token,$proxys[$i_proxy]);
				echo '.';
				if (check_facebook_content($cont)==0)
				{
					echo '*';
					$i_proxy++;
				}
			}
			while ((check_facebook_content($cont)==0) && ($i_proxy<count($proxys)));
			//echo 'http://graph.facebook.com/search?q='.urlencode($word).'&type=post&limit=100&since='.$ts.'&until='.$te.'&locale='.$mas_lan[$lan]."\n";
			$mas=json_decode($cont,true);
			//print_r($mas);
			foreach ($mas['data'] as $item)
			{
				$count_fb++;
				if ((strtotime($item['created_time'])>=$ts) && (strtotime($item['created_time'])<$te))
				{
					//print_r($item);
					//if ($item['link']!='')
					$post_content='';
					if (strlen($item['message'])>0) $post_content=addslashes($item['message']);
					elseif (strlen($item['description'])>0) $post_content=addslashes($item['description']);
					elseif (strlen($item['name'])>0) $post_content=addslashes($item['name']);
					elseif (strlen($item['caption'])>0) $post_content=addslashes($item['caption']);
					//check_post(strip_tags($post_content),$k2);
					//echo $post_content.'|||'.$val."\n\n\n";
					if (check_post(strip_tags($post_content),$k2)==1)
					{
						$m_link=explode('_',$item['id']);
						$outmas['content'][]=preg_replace('/\s+/isu',' ',$post_content);
						$outmas['fulltext'][]=preg_replace('/\s+/isu',' ',$item['message'].' '.$item['description'].' '.$item['name'].' '.$item['caption']);
						//$outmas['content'][]=$item['caption'].' '.$item['message'].' '.$item['description'].' '.$item['']//mb_substr((($item['message']=='')?$item['caption']:$item['message']),0,100,'UTF-8');
						$outmas['link'][]='http://www.facebook.com/permalink.php?story_fbid='.$m_link[1].'&id='.$m_link[0];//$item['link'];
						$outmas['time'][]=strtotime($item['created_time']);
						$outmas['nick'][]=$item['from']['name'];
						$outmas['nick_id'][]=$item['from']['id'];
						unset($m_link);
					}
					if (count($outmas['time'])>100) $outmas=post_slice($outmas);
				}
			}
		}
	}
	echo "\n";
	add_source_log('facebook',intval($count_fb));
	// print_r($outmas);
	return $outmas;
}

// get_facebook('путин',mktime(0,0,0,2,25,2013),mktime(0,0,0,2,27,2013),'ru',array('77.93.32.252:3128','195.3.254.159:8080','46.50.175.146:3128','85.192.166.187:3128','46.50.220.13:3128'));
//echo chech_facebook_content('');
?>
