<?

require_once('com/db.php');
require_once('com/config.php');
require_once('/var/www/daemon/fsearch3/ch.php');
require_once('com/users.php');
require_once('/var/www/new/com/func.php');

date_default_timezone_set('Europe/Moscow');

error_reporting(0);

$order_delta=$_SERVER['argv'][1];
sleep($order_delta);
$fp = fopen('/var/www/pids/transf'.$order_delta.'.pid', 'w');
fwrite($fp, getmypid());
fclose($fp);
$refresh_cash=intval($order_delta)*2;

$redis = new Redis();    
$redis->connect('127.0.0.1');

$db=new database();
$db->connect();

$memcache = memcache_connect('localhost', 11211);

sleep($_SERVER['argv'][1]*3);

while (1)
{
	$retro=($_SERVER['argv'][1]%3==0?'_retro':'');
	$cpost=$redis->sPop('transf_queue'.$retro);
	// echo '|'.$cpost.'|';
	if ((trim($cpost)=='null') || (trim($cpost)=='') || (trim($cpost)=='{"post_ful_com":"null"}')) sleep(2);
	$post=json_decode($cpost,true);
	// echo $cpost;
	// print_r($post);
	/*if ($post['order_id'] % $_SERVER['argv'][2]!=$_SERVER['argv'][1] && ) 
	{
		$redis->sAdd('transf_queue'.$retro,json_encode($post));
		echo 'continue...'."\n";
		continue;
	}*/
	
	if (trim($post['post_content'])=='') 
	{
		echo 10;
		echo $post['post_id'].' '.$post['order_id'].' '.$post['post_link'].' del'."\n";
		continue;
	}

	if (($post['tariff_id']==10) && ($post['ut_date']<=0)) //тарификация по сообщениям
	{
		echo $post['post_id'].' '.$post['order_id'].' '.$post['post_link'].' del'."\n";
		continue;
	}
	echo 1;
	/*$isset_post=intval($memcache->add('isset_'.md5($post['order_id'].'_'.html_entity_decode($post['post_link'])), '1', MEMCACHE_COMPRESSED, 86400));
	if ($isset_post==0) 
	{
		echo $post['post_id'].' '.$post['order_id'].' '.$post['post_link'].' del'."\n";
		continue;
	}
	if ($isset_post==1)*/
	{
		echo 3;
		$qisset=$db->query('SELECT post_id FROM blog_post WHERE order_id='.$post['order_id'].' AND post_link=\''.addslashes(html_entity_decode($post['post_link'])).'\' LIMIT 1');
		if ($db->num_rows($qisset)!=0) 
		{
			echo $post['post_id'].' '.$post['order_id'].' '.$post['post_link'].' del'."\n";
			continue;
		}
	}
	echo 4;
	$user['blog_id']=$post['blog_id'];
	$user['loc']=$post['blog_location'];
	$user['gender']=$post['blog_gender'];
	$user['blog_nick']=$post['blog_nick'];
	$user['blog_login']=$post['blog_login'];
	$user['age']=$post['blog_age'];
	$user['last_update']=$post['blog_last_update'];
	$check=check_filters($post['post_link'],json_decode($post['order_settings'],true));

	if (check_local($post['post_content'].' '.$post['post_ful_com'],'ru')==0) continue;

	if ($check['value']==1)
	{
		echo 5;
		// echo "\n2\n";
		echo $post['post_content'].' '.$post['post_ful_com']."\n\n";
		echo $post['order_keyword']."\n\n";
		if (check_post($post['post_content'].' '.$post['post_ful_com'],$post['order_keyword'])==0) 
		{
			echo 6;
			echo $post['post_id'].' '.$post['order_id'].' '.$post['post_link'].' del'."\n";
			continue;
		}
		echo 7;
		if (!isset($to_cash[$post['order_id']]))
		{
			$to_cash[$post['order_id']]['cash_start']=mktime(0,0,0,date('n',$post['post_time']),date('j',$post['post_time']),date('Y',$post['post_time']));
			$to_cash[$post['order_id']]['cash_end']=mktime(0,0,0,date('n',$post['post_time']),date('j',$post['post_time']),date('Y',$post['post_time']));
		}
		else
		{
			if ($post['post_time']<$to_cash[$post['order_id']]['cash_start']) $to_cash[$post['order_id']]['cash_start']=mktime(0,0,0,date('n',$post['post_time']),date('j',$post['post_time']),date('Y',$post['post_time']));
			if ($post['post_time']>$to_cash[$post['order_id']]['cash_end']) $to_cash[$post['order_id']]['cash_end']=mktime(0,0,0,date('n',$post['post_time']),date('j',$post['post_time']),date('Y',$post['post_time']));
		}
		$db->query('INSERT INTO blog_post (order_id,post_link,post_host,post_content,post_time,blog_id,post_engage,post_advengage,post_tag,post_spam) VALUES ('.$post['order_id'].',\''.addslashes($post['post_link']).'\',\''.addslashes($post['post_host']).'\',\''.addslashes($post['post_content']).'\','.$post['post_time'].','.$post['blog_id'].','.$post['post_engage'].',\''.addslashes($post['post_advengage']).'\',\''.addslashes($post['post_tag']).'\','.intval($post['post_spam']).')');
		echo 'INSERT INTO blog_post (order_id,post_link,post_host,post_content,post_time,blog_id,post_engage,post_advengage,post_tag,post_spam) VALUES ('.$post['order_id'].',\''.addslashes($post['post_link']).'\',\''.addslashes($post['post_host']).'\',\''.addslashes($post['post_content']).'\','.$post['post_time'].','.$post['blog_id'].','.$post['post_engage'].',\''.addslashes($post['post_advengage']).'\',\''.addslashes($post['post_tag']).'\','.intval($post['post_spam']).')'."\n";
		$db->query('INSERT INTO blog_full_com (ful_com_post_id,ful_com_order_id,ful_com_post) VALUES ('.$db->insert_id().','.$post['order_id'].',\''.addslashes($post['post_ful_com']=='null'?'':$post['post_ful_com']).'\')');
		// echo 'INSERT INTO blog_full_com (ful_com_post_id,ful_com_order_id,ful_com_post) VALUES ('.$db->insert_id().','.$post['order_id'].',\''.addslashes($post['post_ful_com']=='null'?'':$post['post_ful_com']).'\')'."\n";
		// $db->query('UPDATE blog_post_prev SET order_id=0 WHERE post_id='.$post['post_id']);
		echo 8;
		echo $post['post_id'].' '.$post['order_id'].' '.$post['post_link'].' add'."\n";
		if ($post['tariff_id']==10)// тарификация по сообщениям
		{
			$post['ut_date']--;
			if ($post['ut_date']<=0) $post['ut_date']=0;
			$db->query('UPDATE user_tariff SET ut_date='.$post['ut_date'].' WHERE ut_id='.$post['ut_id']);
		}
		//echo 'UPDATE user_tariff SET ut_date='.$users_info[$orders[$post['order_id']]['ut_id']].' WHERE ut_id='.$orders[$post['order_id']]['ut_id']."\n";
	}
	elseif ($check['value']==0)
	{
		echo 9;
		echo $post['post_id'].' '.$post['order_id'].' '.$post['post_link'].' del'."\n";
	}
	elseif ($check['value']==2)
	{
		usleep(50000);
		echo '*';
		print_r($post);
		$redis->sAdd('transf_queue'.$retro,json_encode($post));
	}

	$refresh_cash++;

	if (($refresh_cash % 10)==0)
	{
		// $db->query('INSERT INTO robot_cash (order_id,cash_start,cash_end,cash_complete,cash_time) VALUES ('.$key.','.$item['cash_start'].','.$item['cash_end'].',0,'.time().')');
		echo 'REFRESH CASH ONE THEME!!!!!!!!!'."\n";
		echo 'REFRESH CASH ONE THEME!!!!!!!!!'."\n";
		echo 'REFRESH CASH ONE THEME!!!!!!!!!'."\n";
		//print_r($to_cash);
		foreach ($to_cash as $key => $item)
		{
			if(intval($key)!=0 && ($item['cash_start']!=$item['cash_end'])){
				sleep(1);
				//$cj=file_get_contents('http://localhost/tools/cashjob.php?order_id='.intval($key).'&start='.$item['cash_start'].'&end='.$item['cash_end']);
				echo 'http://localhost/tools/cashjob.php?order_id='.intval($key).'&start='.$item['cash_start'].'&end='.$item['cash_end'];
				array_shift($to_cash);
				break;
			} else {
				break;
			}
		}
	}

	if (($refresh_cash % 500)==0)
	{
		// $db->query('INSERT INTO robot_cash (order_id,cash_start,cash_end,cash_complete,cash_time) VALUES ('.$key.','.$item['cash_start'].','.$item['cash_end'].',0,'.time().')');
		echo 'REFRESH CASH!!!!!!!!!'."\n";
		echo 'REFRESH CASH!!!!!!!!!'."\n";
		echo 'REFRESH CASH!!!!!!!!!'."\n";
		//print_r($to_cash);
		foreach ($to_cash as $key => $item)
		{
			if(intval($key)!=0 && ($item['cash_start']!=$item['cash_end'])){
				sleep(1);
				//$cj=file_get_contents('http://localhost/tools/cashjob.php?order_id='.intval($key).'&start='.$item['cash_start'].'&end='.$item['cash_end']);
				echo 'http://localhost/tools/cashjob.php?order_id='.intval($key).'&start='.$item['cash_start'].'&end='.$item['cash_end'];
			}
		}
		unset($to_cash);
	}
	//print_r($orders);
}

?>