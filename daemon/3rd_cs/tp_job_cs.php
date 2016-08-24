<?
echo 1;
// Добавить поле в таблицу blog_orders ... third_party


require_once('/var/www/daemon/com/config.php');
require_once('/var/www/daemon/com/func.php');
require_once('/var/www/daemon/com/db.php');
require_once('/var/www/daemon/bot/kernel.php');
/*require_once('/var/www/userjob/get_vkontakte.php');
require_once('/var/www/userjob/get_twitter.php');
require_once('/var/www/userjob/get_livejournal.php');*/

require_once('banki_responses.php');
require_once('facebook-gr.php');
require_once('vk-gr.php');
require_once('vk-ac.php');
require_once('vk-board.php');
require_once('vk-video.php');
require_once('torgmail.php');
require_once('market2.php');
require_once('banki_forum.php');
require_once('banki_friends.php');
require_once('banki_question.php');
echo 123;
echo 123;
require_once('google_plus.php');
require_once('twitter.php');
require_once('ok.php');
require_once('mail.php');
require_once('instagram.php');
require_once('tag_instagram.php');
require_once('instagram_locations.php');

require_once('/var/www/daemon/com/infix.php');
require_once('/var/www/daemon/fsearch3/ch.php');
date_default_timezone_set('Europe/Moscow');
ini_set('memory_limit', '2048M');
// error_reporting(E_ALL);
ignore_user_abort(true);
set_time_limit(0);
ini_set('max_execution_time',0);
ini_set('default_charset','utf-8');
ob_implicit_flush();
$db = new database();
$db->connect();

$redis = new Redis();    
$redis->connect('127.0.0.1');

$mproxy=json_decode($redis->get('proxy_list'),true);

//echo 123;
$ressec=$db->query('SELECT a.tp_id,a.order_id,a.tp_last,a.gr_id,a.tp_type,b.order_start,b.order_end,b.order_keyword,a.tp_filter FROM blog_tp as a LEFT JOIN blog_orders as b ON a.order_id=b.order_id WHERE (a.tp_last<=b.order_end) '.(intval($_SERVER['argv'][1])==0?' AND a.tp_type=\''.$_SERVER['argv'][1].'\'':' AND a.tp_id='.$_SERVER['argv'][1]).' '.($_SERVER['argv'][1]=='vk'?'AND MOD(tp_id,'.$_SERVER['argv'][3].')='.$_SERVER['argv'][2]:'').' AND b.user_id !=145 AND b.user_id !=0 AND b.ut_id !=0 ORDER BY tp_id DESC');
//echo 'SELECT * FROM blog_orders WHERE order_id=704';
//$ressec=$db->query('SELECT * FROM blog_orders WHERE order_id=627');
while($blog=$db->fetch($ressec))
{
	$blog['order_keyword']=construct_object_query($blog['order_keyword']);
	print_r($blog);
	if ($blog['tp_last']>=$blog['order_start'])
	{
		if ($blog['tp_last']!=0)
		{
			$mstart=$blog['tp_last'];
		}
		else
		{
			$mstart = $blog['order_start'];
		}
	}
	else
	{
		$mstart=$blog['order_start'];
	}
	if ($blog['order_end']>=time())
	{
		$mend=time();
	}
	else
	{
		if ($blog['order_end']!=0)
		{
			$mend=mktime(0,0,0,date('n',$blog['order_end']),date('j',$blog['order_end'])+1,date('Y',$blog['order_end']));//$blog['order_end']+86400;
		}
		else
		{
			$mend=time();
		}
	}
	$mstart=mktime(0,0,0,date('n',$mstart),date('j',$mstart)-1,date('Y',$mstart));
	if ($mstart<$blog['order_start'])
	{
		$mstart=$blog['order_start'];
	}
	unset($m1);
	//$mstart=mktime(0,0,0,date('n'),date('j')-1,date('Y'));
	//$mend=mktime(0,0,0,date('n'),date('j'),date('Y'));
	//echo $mstart.' '.$mend.' '.$blog['order_id']."\n";
	//continue;
	if ($blog['order_end']<time())
	{
		//continue;
	}
	echo $mstart.' '.$mend.' '.$blog['order_id']."\n";
	if ($blog['tp_type']=='fb')
	{
		echo $blog['gr_id']."\n";
		$mas=get_facebook_group($blog['gr_id'],$mstart,$mend);
		//print_r($mas);
		//die();
		foreach ($mas['link'] as $key => $it)
		{
			$m1['link'][]=$it;
			$m1['content'][]=$mas['content'][$key];
			$m1['time'][]=$mas['time'][$key];
			$m1['author_id'][]=$mas['author_id'][$key];
			$m1['author_name'][]=$mas['author_name'][$key];
			$m1['engage'][]=$mas['engage'][$key];
			$m1['adv_engage'][]=$mas['adv_engage'][$key];
		}
		unset($mas);
	}
	if ($blog['tp_type']=='vk')
	{
		echo $blog['gr_id']."\n";
		$mas=get_vk_group($blog['gr_id'],$mstart,$mend);
		foreach ($mas['link'] as $key => $it)
		{
			$m1['link'][]=$it;
			$m1['content'][]=$mas['content'][$key];
			$m1['time'][]=$mas['time'][$key];
			$m1['author_id'][]=$mas['author_id'][$key];
			$m1['author_name'][]=$mas['author_name'][$key];
			$m1['engage'][]=$mas['engage'][$key];
			$m1['adv_engage'][]=$mas['adv_engage'][$key];
		}
		unset($mas);
	}
	if ($blog['tp_type']=='vk_acc')
	{
		echo $blog['gr_id']."\n";
		$mas=get_vk_account($blog['gr_id'],$mstart,$mend);
		foreach ($mas['link'] as $key => $it)
		{
			$m1['link'][]=$it;
			$m1['content'][]=$mas['content'][$key];
			$m1['time'][]=$mas['time'][$key];
			$m1['author_id'][]=$mas['author_id'][$key];
			$m1['author_name'][]=$mas['author_name'][$key];
			$m1['engage'][]=$mas['engage'][$key];
			$m1['adv_engage'][]=$mas['adv_engage'][$key];
		}
		unset($mas);
	}
	if ($blog['tp_type']=='ya_market')
	{
		echo $blog['gr_id']."\n";
		$mas=get_market(json_decode($blog['gr_id'],true),$mstart,$mend);
		foreach ($mas['link'] as $key => $it)
		{
			$m1['link'][]=$it;
			$m1['content'][]=$mas['content'][$key];
			$m1['time'][]=$mas['time'][$key];
			$m1['author_id'][]=$mas['author_id'][$key];
			$m1['author_name'][]=$mas['author_name'][$key];
			$m1['engage'][]=$mas['eng'][$key];
		}
		unset($mas);
	}
	if ($blog['tp_type']=='torg_mail')
	{
		echo $blog['gr_id']."\n";
		$mas=get_tmail('http://torg.mail.ru/review/shops/'.$blog['gr_id'].'/',$mstart,$mend);
		foreach ($mas['link'] as $key => $it)
		{
			$m1['link'][]=$it;
			$m1['content'][]=$mas['content'][$key];
			$m1['time'][]=$mas['time'][$key];
			$m1['author_id'][]=$mas['author_id'][$key];
			$m1['author_name'][]=$mas['author_name'][$key];
			$m1['engage'][]=$mas['eng'][$key];
		}
		unset($mas);
	}
	if ($blog['tp_type']=='vk_board')
	{
		echo $blog['gr_id']."\n";
		$mas=get_vk_board($blog['gr_id'],$mstart,$mend);
		foreach ($mas['link'] as $key => $it)
		{
			$m1['link'][]=$it;
			$m1['content'][]=$mas['content'][$key];
			$m1['time'][]=$mas['time'][$key];
			$m1['author_id'][]=$mas['author_id'][$key];
			$m1['author_name'][]='';
			$m1['engage'][]=intval($mas['eng'][$key]);
		}
		unset($mas);
	}
	if ($blog['tp_type']=='vk_video')
	{
		echo $blog['gr_id']."\n";
		$mas=get_vk_video_album($blog['gr_id'],$mstart,$mend);
		foreach ($mas['link'] as $key => $it)
		{
			$m1['link'][]=$it;
			$m1['content'][]=$mas['content'][$key];
			$m1['time'][]=$mas['time'][$key];
			$m1['author_id'][]=$mas['author_id'][$key];
			$m1['author_name'][]='';
			$m1['engage'][]=intval($mas['eng'][$key]);
		}
		unset($mas);
	}
	if ($blog['tp_type']=='banki_forum')
	{
		echo $blog['gr_id']."\n";
		$m_tpid=explode('_',$blog['gr_id']);
		$mas=get_forum_banki($m_tpid[0],$m_tpid[1],$mstart,$mend);
		foreach ($mas['link'] as $key => $it)
		{
			$m1['link'][]=$it;
			$m1['content'][]=$mas['content'][$key];
			$m1['time'][]=$mas['time'][$key];
			$m1['author_id'][]='';
			$m1['author_name'][]='';
			$m1['engage'][]=0;
		}
		unset($mas);
	}
	if ($blog['tp_type']=='banki_friends')
	{
		echo $blog['gr_id']."\n";
		$m_tpid=explode('/',$blog['gr_id']);
		$mas=get_friends_banki($m_tpid[0],$m_tpid[1],$mstart,$mend);
		foreach ($mas['link'] as $key => $it)
		{
			$m1['link'][]=$it;
			$m1['content'][]=$mas['content'][$key];
			$m1['time'][]=$mas['time'][$key];
			$m1['author_id'][]='';
			$m1['author_name'][]='';
			$m1['engage'][]=0;
		}
		unset($mas);
	}
	if ($blog['tp_type']=='banki_question')
	{
		echo $blog['gr_id']."\n";
		$mas=get_banki_question_page($blog['gr_id'],$mstart,$mend);
		foreach ($mas['link'] as $key => $it)
		{
			$m1['link'][]=$it;
			$m1['content'][]=$mas['content'][$key];
			$m1['time'][]=$mas['time'][$key];
			$m1['author_id'][]='';
			$m1['author_name'][]='';
			$m1['engage'][]=0;
		}
		unset($mas);
	}
	if ($blog['tp_type']=='banki_responses')
	{
		echo $blog['gr_id']."\n";
		echo "!!!!!!!!!";
		$mas=get_banki_responses_page($blog['gr_id'],$mstart,$mend);
		print_r($mas);
		foreach ($mas['link'] as $key => $it)
		{
			$m1['link'][]=$it;
			$m1['content'][]=$mas['content'][$key];
			$m1['time'][]=$mas['time'][$key];
			$m1['author_id'][]='';
			$m1['author_name'][]='';
			$m1['engage'][]=0;
		}
		unset($mas);
	}
	if ($blog['tp_type']=='gp')
	{
		echo $blog['gr_id']."\n";
		$mas=get_gp($blog['gr_id'],$mstart,$mend);
		foreach ($mas['link'] as $key => $it)
		{
			$m1['link'][]=$it;
			$m1['content'][]=$mas['content'][$key];
			$m1['time'][]=$mas['time'][$key];
			$m1['author_id'][]='';
			$m1['author_name'][]='';
			$m1['engage'][]=0;
		}
		unset($mas);
	}
	if ($blog['tp_type']=='twitter')
	{
		echo $blog['gr_id']."\n";
		$mas=get_twitter_3rd($blog['gr_id'],$mstart,$mend);
		foreach ($mas['link'] as $key => $it)
		{
			$m1['link'][]=$it;
			$m1['content'][]=$mas['content'][$key];
			$m1['time'][]=$mas['time'][$key];
			$m1['author_id'][]=$mas['author_id'][$key];
			$m1['author_name'][]=$mas['author_id'][$key];
			$m1['engage'][]=0;
		}
		unset($mas);
	}
	if ($blog['tp_type']=='ok')
	{
		echo $blog['gr_id']."\n";
		$mas=get_ok($blog['gr_id'],$mstart,$mend);
		foreach ($mas['link'] as $key => $it)
		{
			$m1['link'][]=$it;
			$m1['content'][]=$mas['content'][$key];
			$m1['time'][]=$mas['time'][$key];
			$m1['author_id'][]=$mas['author_id'][$key];
			$m1['author_name'][]=$mas['author_id'][$key];
			$m1['engage'][]=0;
		}
		unset($mas);
	}
	if ($blog['tp_type']=='mymail')
	{
		echo $blog['gr_id']."\n";
		$mas=get_mail_ru($blog['gr_id'],$mstart,$mend);
		foreach ($mas['link'] as $key => $it)
		{
			$m1['link'][]=$it;
			$m1['content'][]=$mas['content'][$key];
			$m1['time'][]=$mas['time'][$key];
			$m1['author_id'][]=$mas['author_id'][$key];
			$m1['author_name'][]=$mas['author_id'][$key];
			$m1['engage'][]=0;
		}
		unset($mas);
	}
	if ($blog['tp_type']=='instagram')
	{
		echo $blog['gr_id']."\n";
		$mas=get_instagram($blog['gr_id'],$mstart,$mend);
		foreach ($mas['link'] as $key => $it)
		{
			$m1['link'][]=$it;
			$m1['content'][]=$mas['content'][$key];
			$m1['time'][]=$mas['time'][$key];
			$m1['author_id'][]=$mas['author_id'][$key];
			$m1['author_name'][]=$mas['author_id'][$key];
			$m1['engage'][]=0;
		}
		unset($mas);
	}
	if ($blog['tp_type']=='tag_instagram')
	{
		echo $blog['gr_id']."\n";
		$mas=get_tag_instagram($blog['gr_id'],$mstart,$mend);
		foreach ($mas['link'] as $key => $it)
		{
			$m1['link'][]=$it;
			$m1['content'][]=$mas['content'][$key];
			$m1['time'][]=$mas['time'][$key];
			$m1['author_id'][]=$mas['author_id'][$key];
			$m1['author_name'][]=$mas['author_id'][$key];
			$m1['engage'][]=0;
		}
		unset($mas);
	}
	if ($blog['tp_type']=='instagram_locations')
	{
		echo $blog['gr_id']."\n";
		$mas=get_instagram_locations($blog['gr_id'],$mstart,$mend);
		foreach ($mas['link'] as $key => $it)
		{
			$m1['link'][]=$it;
			$m1['content'][]=$mas['content'][$key];
			$m1['time'][]=$mas['time'][$key];
			$m1['author_id'][]=$mas['author_id'][$key];
			$m1['author_name'][]=$mas['author_id'][$key];
			$m1['engage'][]=0;
		}
		unset($mas);
	}
	//echo '1 YANDEX'."\n";
	print_r($m1);
	//die();
	//continue;
	foreach ($m1['link'] as $key => $item)
	{
		$hn='';
		$hn=parse_url($item);
	    $hn=$hn['host'];
	    $ahn=explode('.',$hn);
	    $hn = $ahn[count($ahn)-2].'.'.$ahn[count($ahn)-1];
		$hh = $ahn[count($ahn)-2];
		echo '.';
		if (($blog['tp_filter']==1)&&((check_post($m1['content'][$key],$blog['order_keyword'])==0)||(check_local($m1['content'][$key],'ru')==0))) continue;
		// $qw=$db->query('SELECT post_id FROM blog_post WHERE post_link=\''.addslashes($item).'\' AND order_id='.$blog['order_id']);
		$qw=$db->query('SELECT post_id FROM blog_post WHERE post_content=\''.addslashes($m1['content'][$key]).'\' AND post_time='.$m1['time'][$key].' AND order_id='.$blog['order_id'].' LIMIT 1');
		if ($db->num_rows($qw)!=0) continue;
		if ($blog['tp_type']!='tag_instagram' && $blog['tp_type']!='fb') $qw=$db->query('SELECT post_id FROM blog_post WHERE post_content=\''.addslashes(mb_substr($m1['content'][$key],0,150,'UTF-8').' ...').'\' AND post_time='.$m1['time'][$key].' AND order_id='.$blog['order_id'].' LIMIT 1');
		else $qw=$db->query('SELECT post_id FROM blog_post WHERE post_link=\''.addslashes($item).'\' AND order_id='.$blog['order_id'].' LIMIT 1');
		//echo 'SELECT post_id FROM blog_post WHERE post_link=\''.addslashes($item).'\' AND order_id='.$blog['order_id']."\n";
		// if ((($hn!='yandex.ru') && (mysql_num_rows($qw)==0)) || ($hn=='yandex.ru'))	
		if ($db->num_rows($qw)==0)
		{
			echo '/';
			//echo $item['content']."\n";
			//$rep[]=$item;
			$bb1['blog_id']=0;
			if ($hn=='facebook.com')
			{
				$chbb=$db->query('SELECT blog_id from robot_blogs2 WHERE blog_login=\''.$m1['author_id'][$key].'\' AND blog_link=\'facebook.com\' LIMIT 1');
				if (mysql_num_rows($chbb)==0)
				{
					$ins_bb=$db->query('INSERT INTO robot_blogs2 (blog_link,blog_login,blog_nick) VALUES (\'facebook\.com\',\''.addslashes($m1['author_id'][$key]).'\',\''.addslashes($m1['author_name'][$key]).'\')');
					//echo 'INSERT INTO robot_blogs2 (blog_link,blog_login,blog_nick) VALUES (\'facebook\.com\',\''.addslashes($m1['author_id'][$key]).'\',\''.addslashes($m1['author_name'][$key]).'\')'."\n";
					$bb1['blog_id']=$db->insert_id();
				}
				else
				{
					$bb1=$db->fetch($chbb);
				}
			}
			if ($hn=='vk.com')
			{
				$chbb=$db->query('SELECT blog_id from robot_blogs2 WHERE blog_login=\''.$m1['author_id'][$key].'\' AND blog_link=\'vkontakte.ru\' LIMIT 1');
				if (mysql_num_rows($chbb)==0)
				{
					$ins_bb=$db->query('INSERT INTO robot_blogs2 (blog_link,blog_login,blog_nick) VALUES (\'vkontakte\.ru\',\''.addslashes($m1['author_id'][$key]).'\',\''.addslashes($m1['author_name'][$key]).'\')');
					//echo 'INSERT INTO robot_blogs2 (blog_link,blog_login,blog_nick) VALUES (\'facebook\.com\',\''.addslashes($m1['author_id'][$key]).'\',\''.addslashes($m1['author_name'][$key]).'\')'."\n";
					$bb1['blog_id']=$db->insert_id();
				}
				else
				{
					$bb1=$db->fetch($chbb);
				}
			}
			if ($hn=='twitter.com')
			{
				$chbb=$db->query('SELECT blog_id from robot_blogs2 WHERE blog_login=\''.$m1['author_id'][$key].'\' AND blog_link=\'twitter.com\' LIMIT 1');
				if (mysql_num_rows($chbb)==0)
				{
					$ins_bb=$db->query('INSERT INTO robot_blogs2 (blog_link,blog_login,blog_nick) VALUES (\'twitter\.com\',\''.addslashes($m1['author_id'][$key]).'\',\''.addslashes($m1['author_name'][$key]).'\')');
					//echo 'INSERT INTO robot_blogs2 (blog_link,blog_login,blog_nick) VALUES (\'facebook\.com\',\''.addslashes($m1['author_id'][$key]).'\',\''.addslashes($m1['author_name'][$key]).'\')'."\n";
					$bb1['blog_id']=$db->insert_id();
				}
				else
				{
					$bb1=$db->fetch($chbb);
				}
			}
			if ($hn=='yandex.ru')
			{
				// $chbb=$db->query('SELECT blog_id from robot_blogs2 WHERE blog_login=\''.$m1['author_id'][$key].'\' AND blog_link=\'ya.ru\' LIMIT 1');
				// if (mysql_num_rows($chbb)==0)
				// {
				// 	$ins_bb=$db->query('INSERT INTO robot_blogs2 (blog_link,blog_login,blog_nick) VALUES (\'ya\.ru\',\''.addslashes($m1['author_id'][$key]).'\',\''.addslashes($m1['author_name'][$key]).'\')');
				// 	//echo 'INSERT INTO robot_blogs2 (blog_link,blog_login,blog_nick) VALUES (\'facebook\.com\',\''.addslashes($m1['author_id'][$key]).'\',\''.addslashes($m1['author_name'][$key]).'\')'."\n";
				// 	$bb1['blog_id']=$db->insert_id();
				// }
				// else
				// {
				// 	$bb1=$db->fetch($chbb);
				// }
				$bb1['blog_id']=0;
			}
			//echo 'INSERT INTO blog_post (order_id,post_link,post_host,post_time,post_content,blog_id,post_engage) VALUES ('.$blog['order_id'].',\''.addslashes($item).'\',\''.addslashes($hn).'\',\''.$m1['time'][$key].'\',\''.addslashes(mb_substr($m1['content'][$key],0,150,'UTF-8').' ...').'\','.intval($bb1['blog_id']).','.intval($m1['engage'][$key]).')'."\n";
			$upd_orders[$blog['order_id']]=1;
			$count_pst++;
			$count_pst_hn[$hn]++;
			echo 'INSERT INTO blog_post (order_id,post_link,post_host,post_time,post_content,blog_id,post_engage,post_advengage) VALUES ('.$blog['order_id'].',\''.addslashes($item).'\',\''.addslashes($hn).'\',\''.$m1['time'][$key].'\',\''.addslashes(mb_substr($m1['content'][$key],0,150,'UTF-8').' ...').'\','.intval($bb1['blog_id']).','.intval($m1['engage'][$key]).',\''.addslashes(json_encode($m1['adv_engage'][$key])).'\')'."\n\n\n\n";
			$db->query('INSERT INTO blog_post (order_id,post_link,post_host,post_time,post_content,blog_id,post_engage,post_advengage) VALUES ('.$blog['order_id'].',\''.addslashes($item).'\',\''.addslashes($hn).'\',\''.$m1['time'][$key].'\',\''.addslashes(mb_substr($m1['content'][$key],0,150,'UTF-8').' ...').'\','.intval($bb1['blog_id']).','.intval($m1['engage'][$key]).',\''.addslashes(json_encode($m1['adv_engage'][$key])).'\')');
			$db->query('INSERT INTO blog_full_com (ful_com_post_id,ful_com_order_id,ful_com_post) VALUES ('.$db->insert_id().','.$blog['order_id'].',\''.addslashes($m1['content'][$key]).'\')');
			//$db->query('INSERT INTO blog_post_prev (order_id,post_link,post_host,post_time,post_content,blog_id,post_ful_com) VALUES ('.$blog['order_id'].',\''.addslashes($item).'\',\''.addslashes($hn).'\',\''.$m1['time'][$key].'\',\''.addslashes(mb_substr($m1['content'][$key],0,150,'UTF-8').' ...').'\','.intval($bb1['blog_id']).','.addslashes($m1['content'][$key]).')');
			//$db->query('INSERT INTO blog_post (order_id,post_link,post_host,post_time,post_content,blog_id,post_engage) VALUES ('.$blog['order_id'].',\''.addslashes($item).'\',\''.addslashes($hn).'\',\''.$m1['time'][$key].'\',\''.addslashes($m1['content'][$key]).'\','.intval($bb1['blog_id']).','.intval($m1['eng'][$key]).')');
		}
	}
	unset($rep);
	$qw=$db->query('UPDATE blog_tp SET tp_last='.time().' WHERE tp_id='.$blog['tp_id']);
	//echo 'UPDATE blog_tp SET tp_last='.time().' WHERE order_id='.$blog['order_id'];
	//print_r($outmas);
	//echo "\n".$mstart.' '.$mend.' '.$blog['order_id'];
}

foreach ($upd_orders as $key => $item)
{
	//parseUrl('http://localhost/tools/cashjob.php?order_id='.$key);
	/*$fp = fopen('/var/www/bot/cashjob-spec.log', 'a');
	fwrite($fp, 'start: '.date('r')."\n");
	fclose($fp);

	$descriptorspec=array(
		0 => array("file","/dev/null","a"),
		1 => array("file","/dev/null","a"),
		2 => array("file","/dev/null","a")
		);

	$cwd='/var/www/bot/';
	$end=array();
	
	$process=proc_open('php /var/www/bot/cashjob-spec.php '.$key.' &',$descriptorspec,$pipes,$cwd,$end)/*; or {
		echo json_encode(array('status'=>'fail'), true);
		die();
	}*//*;*/
	
	/*if (is_resource($process))
	{
		//echo 'return: '.$return_value=proc_close($process);
		if (intval($return_value=proc_close($process))==0) echo json_encode(array('status'=>'ok'), true);
	}*/
}

$headers  = "From: noreply2@wobot.ru\r\n"; 
$headers .= "Bcc: noreply2@wobot.ru\r\n";
$headers .= 'MIME-Version: 1.0' . "\r\n";
$headers .= 'Content-type: text/html; charset=utf-8'."\r\n";
// mail('zmei123@yandex.ru','Сборщик 3rd_cs',$count_pst.' '.json_encode($count_pst_hn),$headers);

?>
