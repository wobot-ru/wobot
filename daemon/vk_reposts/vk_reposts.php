<?

require_once('/var/www/daemon/com/db.php');
require_once('/var/www/daemon/com/config.php');
require_once('/var/www/daemon/bot/kernel.php');
require_once('/var/www/daemon/com/users.php');
require_once('/var/www/daemon/vk_reposts/func_reposts.php');

$db=new database();
$db->connect();

$redis = new Redis();    
$redis->connect('127.0.0.1');

error_reporting(0);

$access_token='34161805bbd968a82b584458b1b783761b9e0619e39d34f687a261cf228e9d5b603b27cb9a50ac6638a66';

$filename = 'last_id.txt';
$handle = fopen($filename, "rb");
$last_id = intval(trim(fread($handle, filesize($filename))));
fclose($handle);

function get_reposts($link)
{
	global $access_token,$errors,$mproxy;
	$regex='/vk\.com\/wall(?<owner_id>[\d\-]+)\_(?<post_id>\d+)/isu';
	preg_match_all($regex, $link, $out);
	do
	{
		$cont=parseUrlproxy('https://api.vkontakte.ru/method/wall.getReposts?owner_id='.$out['owner_id'][0].'&post_id='.$out['post_id'][0].'&count=1000',$mproxy[rand(0,9)]);
		$mcont=json_decode($cont,true);
		$errors[$mcont['error']['error_code'].' '.$mcont['error']['error_msg']]++;
		$attemp++;
	}
	while (!isset($mcont['response']['items']) && $attemp<3);
	foreach ($mcont['response']['items'] as $item)
	{
		$outmas['link'][]='http://vk.com/wall'.$item['from_id'].'_'.$item['id'];
		$outmas['cont'][]=$item['text'].' '.$item['copy_text'];
		$outmas['time'][]=$item['date'];
		$outmas['eng'][]=json_encode(array('comment'=>$item['comments']['count'],'likes'=>$item['likes']['count']));
		$outmas['full_eng'][]=intval($item['comments']['count'])+intval($item['likes']['count']);
		$outmas['from_id'][]=$item['from_id'];
	}
	// print_r($outmas);
	return $outmas;
}

// get_reposts('http://vk.com/wall-30666517_578333');

$order_delta=$_SERVER['argv'][1];
$fp = fopen('/var/www/pids/reposts'.$order_delta.'.pid', 'w');
fwrite($fp, getmypid());
fclose($fp);

$last_post_id=get_last_id();
$iteration=0;
while (1)
{
    if ($iteration % 5==0) 
    {
    	echo 'Получение прокси листа...'."\n";
        $mproxy=json_decode($redis->get('proxy_vk'),true);
        sleep(1);
    }
    echo '.';
    if ($last_id=='') $res=$db->query('SELECT post_id,order_id,post_link from blog_post WHERE post_id>'.($last_post_id-10000).' AND MOD(post_id,'.$_SERVER['argv'][2].')='.$_SERVER['argv'][1].' AND post_host=\'vk.com\' AND post_engage!=0 ORDER BY post_id ASC LIMIT 100');
    else $res=$db->query('SELECT post_id,order_id,post_link from blog_post WHERE post_id>'.$last_id.' AND MOD(post_id,'.$_SERVER['argv'][2].')='.$_SERVER['argv'][1].' AND post_host=\'vk.com\' AND post_engage!=0 ORDER BY post_id ASC LIMIT 100');
    $offset++;
    while ($post=$db->fetch($res))
    {
    	// print_r($post);
        $last_id=$post['post_id'];
        // $fp = fopen('last_id.txt', 'w');
        // fwrite($fp, $post['post_id']);
        // fclose($fp);
        $reposts=get_reposts($post['post_link']);
        // print_r($reposts);
        echo 'count='.count($reposts['link'])."\n";
        $hn='vk.com';
        foreach ($reposts['link'] as $key => $item)
        {
        	$qisset=$db->query('SELECT * FROM blog_post WHERE post_link=\''.addslashes($item).'\' AND order_id='.$post['order_id'].' LIMIT 1');
        	echo 'SELECT * FROM blog_post WHERE post_link=\''.addslashes($item).'\' AND order_id='.$post['order_id'].' LIMIT 1'."\n";
        	if ($db->num_rows($qisset)!=0) 
    		{
    			echo 'CONTINUE...'."\n";
    			continue;
    		}
            $slice_time=mktime(0,0,0,date('n',$reposts['time'][$key]),date('j',$reposts['time'][$key]),date('Y',$reposts['time'][$key]));
            $to_cash[$post['order_id']][$slice_time]++;   
            // $user=new users();
			// $blog_id=$user->get_url($item);
			$qblog=$db->query('SELECT * FROM robot_blogs2 WHERE blog_login=\''.$reposts['from_id'][$key].'\' AND blog_link=\'vkontakte.ru\' LIMIT 1');
			if ($db->num_rows($qblog)==0)
			{
				$qins=$db->query('INSERT INTO robot_blogs2 (blog_login,blog_link) VALUES (\''.addslashes($reposts['from_id'][$key]).'\',\'vkontakte.ru\')');
				$blog_id=$db->insert_id($qins);
			}
			else
			{
				$blog=$db->fetch($qblog);
				$blog_id=$blog['blog_id'];
			}
			echo "\n".'!!!!'.$blog_id.'!!!!'."\n";
			// echo 'INSERT INTO blog_post (order_id,post_host,post_link,post_time,post_content,blog_id,post_engage,post_advengage) VALUES ('.$post['order_id'].',\''.$hn.'\',\''.$item.'\','.$reposts['time'][$key].',\''.addslashes(mb_substr($reposts['cont'][$key],0,150,'UTF-8').'...').'\','.$blog_id.','.$reposts['full_eng'][$key].',\''.addslashes($reposts['eng'][$key]).'\')'."\n";
			// echo 'INSERT INTO blog_full_com (ful_com_post_id,ful_com_order_id,ful_com_post) VALUES ('.$insert_id.','.$post['order_id'].',\''.addslashes($reposts['cont'][$key]).'\')';
			echo "\n\n\n";
            $qpost=$db->query('INSERT INTO blog_post (order_id,post_host,post_link,post_time,post_content,blog_id,post_engage,post_advengage) VALUES ('.$post['order_id'].',\''.$hn.'\',\''.$item.'\','.$reposts['time'][$key].',\''.addslashes(mb_substr($reposts['cont'][$key],0,150,'UTF-8').'...').'\','.$blog_id.','.$reposts['full_eng'][$key].',\''.addslashes($reposts['eng'][$key]).'\')');
            $insert_id=$db->insert_id($qpost);
            $db->query('INSERT INTO blog_full_com (ful_com_post_id,ful_com_order_id,ful_com_post) VALUES ('.$insert_id.','.$post['order_id'].',\''.addslashes($reposts['cont'][$key]).'\')');
            $count_retweet++;
        }
        usleep(500000);
    }
    $iteration++;
    if ($iteration % 10 == 0)
    {
	    print_r($to_cash);
	    foreach ($to_cash as $key => $item)
	    {
	    	foreach ($item as $ke => $it)
	    	{
	    		//sleep(1);
	        	//file_get_contents('http://localhost/tools/cashjob.php?order_id='.intval($key).'&start='.$ke.'&end='.$ke);
	        }
	    }
	    unset($to_cash);
	}
	if ($iteration % 10000 == 0)
	{
		$headers  = "From: noreply2@wobot.ru\r\n"; 
		$headers .= "Bcc: noreply2@wobot.ru\r\n";
		$headers .= 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=utf-8'."\r\n";
		mail('zmei123@yandex.ru','Ошибки/сбор vk_retweets',json_encode($errors).' '.'<br>Собранных ретвитов: '.$count_retweet,$headers);
	}
    sleep(5);
}

?>