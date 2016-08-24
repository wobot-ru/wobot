<?

$order_id=intval($argv[2]);
$type=$argv[1];

if (intval($_GET['order_id'])>0)
{
	//$fp = fopen('/var/www/daemon/fsearch3/typhoon.log', 'a');
	//fwrite($fp, 'start: '.date('r')."\n");
	//fclose($fp);

	$descriptorspec=array(
		0 => array("file","/dev/null","a"),
		1 => array("file","/dev/null","a"),
		2 => array("file","/dev/null","a")
		);

	$cwd='/var/www/bot/';
	$end=array();
	
	//$process=proc_open('php /var/www/daemon/fsearch3/service.php '.$_GET['type'].' '.intval($_GET['order_id']).' & > /var/www/daemon/fsearch3/typhoon.log',$descriptorspec,$pipes,$cwd,$end);
	//echo 'php /var/www/daemon/fsearch3/service.php '.$_GET['type'].' '.intval($_GET['order_id']).' &';
	$process=proc_open('php /var/www/daemon/fsearch3/service.php '.$_GET['type'].' '.intval($_GET['order_id']).' &',$descriptorspec,$pipes,$cwd,$end);

	if (is_resource($process))
	{
		if (intval($return_value=proc_close($process))==0) echo json_encode(array('status'=>'ok'), true);
	}
}
elseif ($order_id>0)
{
	require_once('/var/www/daemon/com/config.php');
	require_once('/var/www/daemon/com/db.php');
	require_once('/var/www/daemon/bot/kernel.php');
	require_once('/var/www/daemon/com/users.php');
	require_once('/var/www/daemon/fsearch3/cfg.php');//конфиг токенов
	require_once('/var/www/daemon/fsearch3/ch.php');//чекер постов
	require_once('/var/www/daemon/com/qlib.php');
	
	//require_once('/var/www/daemon/fsearch3/facebook.php');
	//require_once('/var/www/daemon/fsearch3/google.php');
	//require_once('/var/www/daemon/fsearch3/topsy.php');
	//require_once('/var/www/daemon/fsearch3/twitter.php');
	//require_once('/var/www/daemon/fsearch3/youtube.php');
	//require_once('/var/www/daemon/fsearch3/vkontakte2.php');
	//require_once('/var/www/daemon/fsearch3/google_plus/get_gp.php');
	//require_once('/var/www/daemon/fsearch3/slideshare.php');
	//require_once('/var/www/daemon/fsearch3/bing.php');

	date_default_timezone_set('Europe/Moscow');
	// error_reporting(E_ERROR);
	ignore_user_abort(true);
	set_time_limit(0);
	ini_set('max_execution_time',0);
	ini_set('default_charset','utf-8');
	ob_implicit_flush();
	$db = new database();
	$db->connect();

	$memcache = memcache_connect('localhost', 11211);

	$redis=new Redis() or die("Can'f load redis module.");
	$redis->connect('127.0.0.1');

	$engage_src['twitter.com']=1;
	$engage_src['vk.com']=1;
	$engage_src['facebook.com']=1;
	$engage_src['livejournal.com']=1;
	
	//$m->set('mproxy',json_encode($mproxy),time()+86400);

	//обнуление текущих номеров прокси для потоков
	/*$m->set('yblogs',0,time()+86400);
	$m->set('facebook',0,time()+86400);
	$m->set('google',0,time()+86400);
	$m->set('topsy',0,time()+86400);
	$m->set('twitter',0,time()+86400);
	$m->set('youtube',0,time()+86400);
	$m->set('vkontakte2',0,time()+86400);
	$m->set('google_plus',0,time()+86400);
	$m->set('bing',0,time()+86400);*/

	//$m->set('mproxy',json_encode($mproxy),time()+86400);	
	$mproxy = json_decode(memcache_get($memcache, 'mproxy'),true);
	$task = json_decode(memcache_get($memcache, 'typhoon_'.$order_id),true);
	// print_r($task);
	// echo 123;
	//$fp = fopen('/var/www/daemon/fsearch3/typhoon.log', 'a');
	//fwrite($fp, $type.' '.json_encode($task).' '.date('r')."\n");
	//fclose($fp);

	$blog=$task['blog'];
	$task['text']=construct_object_query($task['text']);
	$text_lang=$task['geo'];

	$qtariff=$db->query('SELECT * FROM user_tariff WHERE user_id='.$blog['user_id'].' AND ut_id='.$blog['ut_id']);
	// echo 'SELECT * FROM user_tariff WHERE user_id='.$blog['user_id'].' AND ut_id='.$blog['ut_id'];
	$tariff=$db->fetch($qtariff);

	$temp_orderkw=$task['text'];
	if ($type!='yblogs')
	{
		$o_kw_wnot=val_not($task['text'],'');
		$task['text']=$o_kw_wnot['kw'];
	}
	$mstart=$task['mstart'];
	$mend=$task['mend'];

	if ($type=='yblogs')
	{
			require_once('/var/www/daemon/fsearch3/typhoon/typhoon_yblogs.php');
			$m1=getpost_yandex($task['text'],$task['mstart'],$task['mend'],$task['geo'],$mproxy);
			echo '1 YANDEX'."\n";
	}
	elseif ($type=='facebook')
	{
			require_once('/var/www/daemon/fsearch3/typhoon/typhoon_facebook.php');
			$m1=get_facebook($task['text'],$task['mstart'],$task['mend'],$task['geo'],$mproxy);
			echo '2 FACEBOOK'."\n";
	}
	elseif ($type=='google')
	{
			require_once('/var/www/daemon/fsearch3/google.php');
			$m1=getpost_google($task['text'],$task['mstart'],$task['mend'],$task['geo'],$mproxy);
			echo '3 GOOGLE'."\n";
	}
	elseif ($type=='topsy')
	{
			require_once('/var/www/daemon/fsearch3/typhoon/typhoon_topsy.php');
			$m1=getpost_topsy($task['text'],$task['mstart'],$task['mend'],$task['geo'],$mproxy);
			echo '4 TOPSY'."\n";
	}
	elseif ($type=='twitter')
	{
			require_once('/var/www/daemon/fsearch3/twitter/twitter.php');
			$m1=get_twitter($task['text'],$task['mstart'],$task['mend'],$task['geo'],$mproxy);
			echo '5 TWITTER'."\n";
	}
	elseif ($type=='typhoon_twitter')
	{
			require_once('/var/www/daemon/fsearch3/typhoon/typhoon_twitter.php');
			$m1=getpost_typhoon_twitter($task['text'],$task['mstart'],$task['mend'],$task['geo'],$mproxy);
			echo '5 TYPHOON TWITTER'."\n";
	}
	elseif ($type=='youtube')
	{
			require_once('/var/www/daemon/fsearch3/youtube.php');
			$m1=get_post_yt($task['text'],$task['mstart'],$task['mend'],$task['geo'],$mproxy);
			echo '6 YOUTUBE'."\n";
	}
	elseif ($type=='vkontakte2')
	{
			require_once('/var/www/daemon/fsearch3/vkontakte2.php');
			$m1=get_vkontakte($task['text'],$task['mstart'],$task['mend'],$task['geo'],0,$mproxy);
			echo '6 vkontakte'."\n";
	}
	elseif ($type=='slideshare')
	{
			require_once('/var/www/daemon/fsearch3/slideshare.php');
			$m1=get_slideshare($task['text'],$task['mstart'],$task['mend'],$task['geo'],$mproxy);
			echo '9 SLIDESHARE'."\n";
	}
	elseif ($type=='bing')
	{
			require_once('/var/www/daemon/fsearch3/typhoon/typhoon_bing.php');
			$m1=getpost_bing($task['text'],$task['mstart'],$task['mend'],$task['geo'],$mproxy);
			echo '10 BING'."\n";
	}
	elseif ($type=='solr')
	{
			require_once('/var/www/daemon/fsearch3/solr/get_solr.php');
			$m1=get_solr($task['text'],$task['mstart'],$task['mend'],$task['geo']);
			echo '11 SOLR'."\n";
	}
	elseif ($type=='google_plus')
	{
			require_once('/var/www/daemon/fsearch3/google_plus/google_plus.php');
			$m1=get_google_plus($task['text'],$task['mstart'],$task['mend'],$task['geo'],$mproxy);
			echo '12 GOOGLE_PLUS'."\n";
	}
	elseif ($type=='elastic')
	{
			parseUrl('http://localhost/tools/elastic.php?order_id='.intval($blog['order_id']).'&start='.$task['mstart'].'&end='.$task['mend']);
			require_once('/var/www/daemon/fsearch3/elastic.php');
			// $m1=get_elastic($task['text'],$task['mstart'],$task['mend'],$task['geo'],$mproxy);
			echo '13 ELASTIC'."\n";
	}
	
	$task['text']=$temp_orderkw;

	$fp = fopen('service.log', 'a');
	fwrite($fp, date('r').' '.$type.' '.count($m1['link'])."\n");
	fclose($fp);

	//echo '[['.$blog['order_id'].']]';
	if ($blog['order_end']>=time())
	{
		$cstart=mktime(0,0,0,date('n'),date('j'),date('Y'));
		$cend=mktime(0,0,0,date('n'),date('j'),date('Y'));
	}
	else
	{
		$cstart=$blog['order_end'];
		$cend=$blog['order_end'];
	}
	$cch=0;
	
	foreach ($m1['link'] as $key => $item)
	{
		if (($blog['order_analytics']==1) && (($m1['time'][$key]<$mstart) || ($m1['time'][$key]>=$mend)))
		{
			//echo '[[continued1]]';
			continue;
		}
		if (($blog['order_analytics']==0) && (($m1['time'][$key]<$blog['order_start']) || ($m1['time'][$key]>=(($blog['order_end'])==0?time():($blog['order_end']+86400)))))
		{
			//echo '[[continued2]]';
			continue;
		}
		$qw=$db->query('SELECT post_id FROM blog_post WHERE post_link=\''.addslashes($item).'\' AND order_id='.$blog['order_id'].' LIMIT 1');
		//echo mysql_num_rows($qw).' '.check_post($m1['content'][$key],$blog['order_keyword']).' '.$blog['order_keyword'].' '.$m1['content'][$key].' '.check_local($m1['content'][$key],$text_lang)."\n";
		// if ((mysql_num_rows($qw)==0) && (check_post(mb_strtolower(strip_tags($m1['content'][$key].' '.$m1['fulltext'][$key]),'UTF-8'),$blog['order_keyword'])==1) && (check_local($m1['content'][$key],$text_lang)==1))			
		if (mysql_num_rows($qw)!=0) continue;
		if (check_post(mb_strtolower(strip_tags($m1['content'][$key].' '.$m1['fulltext'][$key]),'UTF-8'),$blog['order_keyword'])==0) continue;
		if (check_local(strip_tags($m1['content'][$key].' '.$m1['fulltext'][$key]),$text_lang)==0) continue;
		{
			echo $key.$m1['flag'][$key].' ';
			//echo $item['content']."\n";
			//$rep[]=$item;
			/*$hn='';
			$hn=parse_url($item);
		    $hn=$hn['host'];
		    $ahn=explode('.',$hn);
		    $hn = $ahn[count($ahn)-2].'.'.$ahn[count($ahn)-1];
			$hh = $ahn[count($ahn)-2];
			if ($hh=='.')
			{
				continue;
			}*/
			//$bb1['blog_id']=0;
			//$user=new users();
			//$bb1['blog_id']=$user->get_url($item);

			//if ((check_local($m1['content'][$key],$text_lang)==1))// && ($istrue!=''))
			$hn=parse_url($item);
		    $hn=$hn['host'];
		    $ahn=explode('.',$hn);
		    $hn = $ahn[count($ahn)-2].'.'.$ahn[count($ahn)-1];
			$hh = $ahn[count($ahn)-2];
			$check_filt=check_filters($item,json_decode($blog['order_settings'],true));
			if ($engage_src[$hn])
			{
				$mas_eng=json_decode($m1['engage'][$key],true);
				$engage_val=intval($mas_eng['likes'])+intval($mas_eng['comment'])+intval($mas_eng['retweet']);
			}
			// print_r($check_filt);
			if ($check_filt['value']==1)
			{
				//if ($istrue=='YES')
				{
					$bb1=$check_filt['rb_info'];
					$hn=parse_url($item);
				    $hn=$hn['host'];
				    $ahn=explode('.',$hn);
				    $hn = $ahn[count($ahn)-2].'.'.$ahn[count($ahn)-1];
					$hh = $ahn[count($ahn)-2];
					$m2['content'][$key]=$m1['content'][$key];
					if ($hn=='google.com')
					{
						$quser=$db->query('SELECT blog_id FROM robot_blogs2 WHERE blog_login=\''.addslashes($m1['author_id'][$key]).'\' LIMIT 1');
						if ($db->num_rows($quser)==0)
						{
							$db->query('INSERT INTO robot_blogs2 (blog_link,blog_login,blog_nick,blog_ico) VALUES (\'plus.google.com\',\''.addslashes($m1['author_id'][$key]).'\',\''.addslashes($m1['nick'][$key]).'\',\''.addslashes($m1['ico'][$key]).'\')');
							$bb1['blog_id']=$db->insert_id();
						}
						else
						{
							$bb1=$db->fetch($quser);
						}
					}
					unset($outmas);
					if (mb_strlen($m1['content'][$key],'UTF-8')>500)
					{
						echo addslashes($item)."\n";
						$blog1['order_keyword']=preg_replace('/[^а-яА-Яa-zA-ZёЁ\ \-\=\']/isu','  ',$blog['order_keyword']);
						$mkw=explode('  ',$blog1['order_keyword']);
						foreach ($mkw as $kw)
						{
							if (mb_strlen($kw,'UTF-8')>3)
							{
								$regex='/\.(?<frase>[^\.]*?\.[^\.]*?\.[^\.]*?'.addslashes($kw).'\.[^\.]*?\.[^\.]*?\.)/isu';
								preg_match_all($regex,$m1['content'][$key],$out);
								foreach ($out['frase'] as $itemft)
								{
									if (($itemft!='') && ($itemft!=' '))
									{
										$outmas[]=$itemft;
									}
								}
							}
						}
						if ($outmas[0]!='')
						{
							$m2['content'][$key]=$outmas[0];
						}
						else
						{
							//$mas['full_content']=$pp['ful_com_post'];
						}
					}
					if ($hn=='twitter.com')
					{
						$m2['content'][$key]=preg_replace('/<a.*?href\=[\'\"](.*?)[\'\"].*?>.*?<\/a>/isu','$1',$m2['content'][$key]);
					}
					if (isset($spams[$bb1['blog_id']]))
					{
						continue;
					}
					//echo 'added';
					$cch++;
					if ($m1['time'][$key]<$cstart) $cstart=mktime(0,0,0,date('n',$m1['time'][$key]),date('j',$m1['time'][$key]),date('Y',$m1['time'][$key]));
					unset($queue_item);
					$queue_item['order_id']=$blog['order_id'];
					$queue_item['post_link']=$item;
					$queue_item['post_host']=$hn;
					$queue_item['post_time']=$m1['time'][$key];
					$queue_item['post_content']=$m2['content'][$key];
					$queue_item['post_engage']=($engage_src[$hn]!=1?0:($m1['engage'][$key]!=''?$engage_val:-1));
					$queue_item['post_advengage']=($engage_src[$hn]!=1?'':json_decode($m1['engage'][$key],true));
					$queue_item['blog_id']=intval($bb1['blog_id']);
					$queue_item['post_spam']=$isspam;
					$queue_item['post_ful_com']=$m1['fulltext'][$key];
					$queue_item['blog_location']=$check_filt['rb_info']['loc'];
					$queue_item['blog_last_update']=$check_filt['rb_info']['last_update'];
					$queue_item['blog_age']=$check_filt['rb_info']['age'];
					$queue_item['blog_gender']=$check_filt['rb_info']['gender'];
					$queue_item['order_keyword']=$temp_orderkw;
					$queue_item['order_name']=$blog['order_name'];
					$queue_item['order_start']=$blog['order_start'];
					$queue_item['order_end']=$blog['order_end'];
					$queue_item['order_settings']=$blog['order_settings'];
					$queue_item['user_id']=$blog['user_id'];
					$queue_item['tariff_id']=$tariff['tariff_id'];
					$queue_item['ut_date']=$tariff['ut_date'];
					$queue_item['ut_id']=$tariff['ut_id'];
					$redis->sAdd('prev_queue',json_encode($queue_item));
					// $db->query('INSERT INTO blog_post_prev (order_id,post_link,post_host,post_time,post_content,'.($engage_src[$hn]!=1?'post_engage,':($m1['engage'][$key]!=''?'post_engage,post_advengage,':'')).'blog_id,post_ful_com) VALUES ('.$blog['order_id'].',\''.addslashes($item).'\',\''.addslashes($hn).'\',\''.$m1['time'][$key].'\',\''.addslashes($m2['content'][$key]).'\','.($engage_src[$hn]!=1?'0,':($m1['engage'][$key]!=''?$engage_val.',\''.addslashes($m1['engage'][$key]).'\',':'')).''.intval($bb1['blog_id']).',\''.addslashes($m1['fulltext'][$key]).'\')');
					// echo 'INSERT INTO blog_post_prev (order_id,post_link,post_host,post_time,post_content,'.($engage_src[$hn]!=1?'post_engage,':($m1['engage'][$key]!=''?'post_engage,post_advengage,':'')).'blog_id,post_ful_com) VALUES ('.$blog['order_id'].',\''.addslashes($item).'\',\''.addslashes($hn).'\',\''.$m1['time'][$key].'\',\''.addslashes($m2['content'][$key]).'\','.($engage_src[$hn]!=1?'0,':($m1['engage'][$key]!=''?$engage_val.',\''.addslashes($m1['engage'][$key]).'\',':'')).''.intval($bb1['blog_id']).',\''.addslashes($m1['fulltext'][$key]).'\')'."\n";
					//if (mb_strlen($m1['content'][$key],'UTF-8')>500)
					//{
					//	$db->query('INSERT INTO blog_full_com (ful_com_post_id,ful_com_order_id,ful_com_post) VALUES ('.$db->insert_id().','.$blog['order_id'].',\''.addslashes($m1['content'][$key]).'\')');
					//	echo 'INSERT INTO blog_full_com (ful_com_post_id,ful_com_order_id,ful_com_post) VALUES ('.$db->insert_id().','.$blog['order_id'].',\''.addslashes($m1['content'][$key]).'\')';
					//}
			
				}
				//else
				{
					//$db->query('INSERT INTO blog_post (order_id,post_link,post_host,post_time,post_content,blog_id,post_spam) VALUES ('.$blog['order_id'].',\''.addslashes($item).'\',\''.addslashes($hn).'\',\''.$m1['time'][$key].'\',\''.addslashes($m1['content'][$key]).'\','.intval($bb1['blog_id']).',1)');
				}
			}
			elseif ($check_filt['value']==2)
			{
				$bb1=$check_filt['rb_info'];
				$isspam=0;
				if (isset($spams[$bb1['blog_id']]))
				{
					continue;
				}
				$hn=parse_url($item);
			    $hn=$hn['host'];
			    $ahn=explode('.',$hn);
			    $hn = $ahn[count($ahn)-2].'.'.$ahn[count($ahn)-1];
				$hh = $ahn[count($ahn)-2];
				if ($hn=='google.com')
				{
					$quser=$db->query('SELECT blog_id FROM robot_blogs2 WHERE blog_login=\''.addslashes($m1['author_id'][$key]).'\' LIMIT 1');
					if ($db->num_rows($quser)==0)
					{
						$db->query('INSERT INTO robot_blogs2 (blog_link,blog_login,blog_nick,blog_ico) VALUES (\'plus.google.com\',\''.addslashes($m1['author_id'][$key]).'\',\''.addslashes($m1['nick'][$key]).'\',\''.addslashes($m1['ico'][$key]).'\')');
						$bb1['blog_id']=$db->insert_id();
					}
					else
					{
						$bb1=$db->fetch($quser);
					}
				}
				unset($queue_item);
				$queue_item['order_id']=$blog['order_id'];
				$queue_item['post_link']=$item;
				$queue_item['post_host']=$hn;
				$queue_item['post_time']=$m1['time'][$key];
				$queue_item['post_content']=$m2['content'][$key];
				$queue_item['post_engage']=($engage_src[$hn]!=1?0:($m1['engage'][$key]!=''?$engage_val:-1));
				$queue_item['post_advengage']=($engage_src[$hn]!=1?'':json_decode($m1['engage'][$key],true));
				$queue_item['blog_id']=intval($bb1['blog_id']);
				$queue_item['post_spam']=$isspam;
				$queue_item['post_ful_com']=$m1['fulltext'][$key];
				$queue_item['blog_location']=$check_filt['rb_info']['loc'];
				$queue_item['blog_last_update']=$check_filt['rb_info']['last_update'];
				$queue_item['blog_age']=$check_filt['rb_info']['age'];
				$queue_item['blog_gender']=$check_filt['rb_info']['gender'];
				$queue_item['order_keyword']=$temp_orderkw;
				$queue_item['order_name']=$blog['order_name'];
				$queue_item['order_start']=$blog['order_start'];
				$queue_item['order_end']=$blog['order_end'];
				$queue_item['order_settings']=$blog['order_settings'];
				$queue_item['user_id']=$blog['user_id'];
				$queue_item['tariff_id']=$tariff['tariff_id'];
				$queue_item['ut_date']=$tariff['ut_date'];
				$queue_item['ut_id']=$tariff['ut_id'];
				$redis->sAdd('prev_queue',json_encode($queue_item));
				// $db->query('INSERT INTO blog_post_prev (order_id,post_link,post_host,post_time,post_content,'.($engage_src[$hn]!=1?'post_engage,':($m1['engage'][$key]!=''?'post_engage,post_advengage,':'')).'blog_id,post_ful_com) VALUES ('.$blog['order_id'].',\''.addslashes($item).'\',\''.addslashes($hn).'\',\''.$m1['time'][$key].'\',\''.addslashes($m1['content'][$key]).'\','.($engage_src[$hn]!=1?'0,':($m1['engage'][$key]!=''?$engage_val.',\''.addslashes($m1['engage'][$key]).'\',':'')).intval($bb1['blog_id']).',\''.addslashes($m1['fulltext'][$key]).'\')');
				// echo 'INSERT INTO blog_post_prev (order_id,post_link,post_host,post_time,post_content,'.($engage_src[$hn]!=1?'post_engage,':($m1['engage'][$key]!=''?'post_engage,post_advengage,':'')).'blog_id,post_ful_com) VALUES ('.$blog['order_id'].',\''.addslashes($item).'\',\''.addslashes($hn).'\',\''.$m1['time'][$key].'\',\''.addslashes($m1['content'][$key]).'\','.($engage_src[$hn]!=1?'0,':($m1['engage'][$key]!=''?$engage_val.',\''.addslashes($m1['engage'][$key]).'\',':'')).intval($bb1['blog_id']).',\''.addslashes($m1['fulltext'][$key]).'\')'."\n";
			}
		}
	}
	//unset($rep);
	if ($cch>0)
	{
		//$cj=parseUrl('http://localhost/tools/cashjob.php?order_id='.intval($blog['order_id']).'&start='.$cstart.'&end='.$cend);
	}
	$qw=$db->query('UPDATE blog_orders SET third_sources=2 WHERE order_id='.$blog['order_id'].' AND third_sources<2');
}
?>
