<?

// Добавить поле в таблицу blog_orders ... third_party
require_once('/var/www/com/config.php');
// require_once('/var/www/com/func.php');
require_once('/var/www/com/db.php');
require_once('/var/www/bot/kernel.php');
require_once('ch.php');
/*require_once('/var/www/userjob/get_vkontakte.php');
require_once('/var/www/userjob/get_twitter.php');
require_once('/var/www/userjob/get_livejournal.php');*/

// require_once('yblogs.php');
// require_once('facebook.php');
// require_once('google.php');
// require_once('topsy.php');
// require_once('twitter.php');
// require_once('youtube.php');
// require_once('vkontakte2.php');
// require_once('google_plus/get_gp.php');
// require_once('slideshare.php');
// require_once('bing.php');
require_once('elastic.php');

date_default_timezone_set('Europe/Moscow');
// error_reporting(E_ERROR);
ignore_user_abort(true);
set_time_limit(0);
ini_set('max_execution_time',0);
ini_set('default_charset','utf-8');
ob_implicit_flush();
$db = new database();
$db->connect();
$order_id=$_SERVER['argv'][1];
echo $order_id.' '.$node_id;
//$ressec=$db->query('UPDATE');
$ressec=$db->query('SELECT * FROM blog_orders WHERE order_id='.$order_id.' ORDER BY order_id DESC');
//$ressec=$db->query('SELECT * FROM blog_orders WHERE order_id=723');

//echo 'SELECT * FROM blog_orders WHERE (third_sources<='.mktime(date("H"),0,0,date("n"),date("j"),date("Y")).' or (third_sources=0 and order_start<='.mktime(date("H"),0,0,date("n"),date("j"),date("Y")).')) and (third_sources<=order_end or order_end=0) AND (third_sources!=0) ORDER BY order_id DESC';
//$ressec=$db->query('SELECT * FROM blog_orders WHERE order_id=627');
while($blog=$db->fetch($ressec))
{
	print_r($blog);
	if ($blog['third_sources']>=$blog['order_start'])
	{
		if ($blog['third_sources']!=0)
		{
			$mstart=$blog['third_sources'];
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
			$mend=$blog['order_end']+86400;
		}
		else
		{
			$mend=time();
		}
	}
	switch ($blog['order_lang']) {
	    case 0:
	        $text_lang='';
	        break;
	    case 1:
	        $text_lang='en';
	        break;
	    case 2:
	        $text_lang='ru';
	        break;
		case 4:
			$text_lang='az';
			break;
	}
	$mstart=$blog['order_start'];
	$mend=$blog['order_end'];
	echo $mstart.' '.$mend.' '.$blog['order_id']."\n";
	/*$m1=getpost_yandex((($blog['order_keyword']=='')?$blog['order_name']:$blog['order_keyword']),$mstart,$mend,$text_lang);
	echo '1 YANDEX'."\n";
	//$m8=get_google_plus((($blog['order_keyword']=='')?$blog['order_name']:$blog['order_keyword']),$mstart,$mend,$text_lang);
	echo '8 GOOGLE_PLUS'."\n";
	foreach ($m8['link'] as $key => $item)
	{
		$m1['link'][]=$item;
		$m1['content'][]=$m8['content'][$key];
		$m1['time'][]=$m8['time'][$key];
	}
	$m2=get_facebook((($blog['order_keyword']=='')?$blog['order_name']:$blog['order_keyword']),$mstart,$mend,$text_lang);
	echo '2 FACEBOOK'."\n";
	foreach ($m2['link'] as $key => $item)
	{
		$m1['link'][]=$item;
		$m1['content'][]=$m2['content'][$key];
		$m1['time'][]=$m2['time'][$key];
	}
	$m3=getpost_google((($blog['order_keyword']=='')?$blog['order_name']:$blog['order_keyword']),$mstart,$mend,$text_lang);
	echo '3 GOOGLE'."\n";
	foreach ($m3['link'] as $key => $item)
	{
		$m1['link'][]=$item;
		$m1['content'][]=$m3['content'][$key];
		$m1['time'][]=$m3['time'][$key];
	}
	$m4=getpost_topsy((($blog['order_keyword']=='')?$blog['order_name']:$blog['order_keyword']),$mstart,$mend,$text_lang);
	echo '4 TOPSY'."\n";
	foreach ($m4['link'] as $key => $item)
	{
		$m1['link'][]=$item;
		$m1['content'][]=$m4['content'][$key];
		$m1['time'][]=$m4['time'][$key];
	}
	$m5=get_twitter((($blog['order_keyword']=='')?$blog['order_name']:$blog['order_keyword']),$mstart,$mend,$text_lang);
	echo '5 TWITTER'."\n";
	foreach ($m5['link'] as $key => $item)
	{
		$m1['link'][]=$item;
		$m1['content'][]=$m5['content'][$key];
		$m1['time'][]=$m5['time'][$key];
	}
	$m6=get_post_yt((($blog['order_keyword']=='')?$blog['order_name']:$blog['order_keyword']),$mstart,$mend,$text_lang);
	echo '6 YOUTUBE'."\n";
	foreach ($m6['link'] as $key => $item)
	{
		$m1['link'][]=$item;
		$m1['content'][]=$m6['content'][$key];
		$m1['time'][]=$m6['time'][$key];
	}
	$m7=get_vkontakte((($blog['order_keyword']=='')?$blog['order_name']:$blog['order_keyword']),$mstart,$mend,$text_lang);
	echo '7 VKONTAKTE'."\n";
	foreach ($m7['link'] as $key => $item)
	{
		$m1['link'][]=$item;
		$m1['content'][]=$m7['content'][$key];
		$m1['time'][]=$m7['time'][$key];
	}
	$m9=get_slideshare((($blog['order_keyword']=='')?$blog['order_name']:$blog['order_keyword']),$mstart,$mend,$text_lang);
	echo '9 SLIDESHARE'."\n";
	foreach ($m9['link'] as $key => $item)
	{
		$m1['link'][]=$item;
		$m1['content'][]=$m9['content'][$key];
		$m1['time'][]=$m9['time'][$key];
	}
	$m10=getpost_bing((($blog['order_keyword']=='')?$blog['order_name']:$blog['order_keyword']),$mstart,$mend,$text_lang);
	echo '10 BING'."\n";
	foreach ($m10['link'] as $key => $item)
	{
		$m1['link'][]=$item;
		$m1['content'][]=$m10['content'][$key];
		$m1['time'][]=$m10['time'][$key];
	}*/
	$m1=get_elastic((($blog['order_keyword']=='')?$blog['order_name']:$blog['order_keyword']),$mstart,$mend,$text_lang);
	//print_r($m1);
	//die();
	foreach ($m1['link'] as $key => $item)
	{
		if (($blog['order_analytics']==1) && (($m1['time'][$key]<$mstart) || ($m1['time'][$key]>=$mend)))
		{
			continue;
		}
		if (($blog['order_analytics']==0) && (($m1['time'][$key]<$blog['order_start']) || ($m1['time'][$key]>=(($blog['order_end'])==0?time():$blog['order_end']))))
		{
			continue;
		}
		$qw=$db->query('SELECT * FROM blog_post WHERE  order_id='.$blog['order_id'].' AND post_time='.$m1['time'][$key].' AND post_content=\''.addslashes($m1['content'][$key]).'\'');
		// echo 'SELECT * FROM blog_post WHERE  order_id='.$blog['order_id'].' AND post_time='.$m1['time'][$key].' AND post_content=\''.addslashes($m1['content'][$key]).'\''."\n";
		if ((mysql_num_rows($qw)==0) && (!in_array($item,$rep)) && ((check_post($m1['content'][$key],$blog['order_keyword'])==1)||(check_post($m1['fulltext'][$key],$blog['order_keyword'])==1)) && (check_local($m1['content'][$key],$text_lang)==1))
		{
			echo $key.' ';
			//echo $item['content']."\n";
			$rep[]=$item;
			$hn='';
			$hn=parse_url($item);
		    $hn=$hn['host'];
		    $ahn=explode('.',$hn);
		    $hn = $ahn[count($ahn)-2].'.'.$ahn[count($ahn)-1];
			$hh = $ahn[count($ahn)-2];
			$bb1['blog_id']=0;
			if ($hn=='vk.com')
			{
				$rgx='/wall(?<id_acc>\d+)\_/is';
				preg_match_all($rgx,$item,$acc_id);
				if (intval($acc_id['id_acc'][0])==0)
				{
					$rgx1='/id(?<id_acc>\d+)\?/is';
					preg_match_all($rgx1,$item,$acc_id);
				}
				if (intval($acc_id['id_acc'][0])==0)
				{
					$rgx='/wall(?<id_acc>\-\d+)\_/is';
					preg_match_all($rgx,$item,$acc_id);
				}
				//$mas_inf_vk=get_vk($acc_id['id_acc'][0]);
				$bb1['blog_id']=0;
				if (intval($acc_id['id_acc'][0])!=0)
				{
					$chbb=$db->query('SELECT * from robot_blogs2 WHERE blog_login=\''.$acc_id['id_acc'][0].'\' AND blog_link=\'vkontakte.ru\'');
					if (mysql_num_rows($chbb)==0)
					{
						$ins_bb=$db->query('INSERT INTO robot_blogs2 (blog_link,blog_login) VALUES (\'vkontakte\.ru\',\''.$acc_id['id_acc'][0].'\')');
						$bb1['blog_id']=$db->insert_id();
					}
					else
					{
						$bb1=$db->fetch($chbb);
					}
				}
			}
			else
			if ($hn=='twitter.com')
			{
				$rgx='/twitter\.com\/(?<id_acc>.*?)\//is';
				preg_match_all($rgx,$item,$acc_id);
				$bb1['blog_id']=0;
				$chbb=$db->query('SELECT * from robot_blogs2 WHERE blog_login=\''.$acc_id['id_acc'][0].'\' AND blog_link=\'twitter.com\'');
				if (mysql_num_rows($chbb)==0)
				{
					$ins_bb=$db->query('INSERT INTO robot_blogs2 (blog_link,blog_login) VALUES (\'twitter\.com\',\''.$acc_id['id_acc'][0].'\')');
					$bb1['blog_id']=$db->insert_id();
				}
				else
				{
					$bb1=$db->fetch($chbb);
				}
			}
			else
			if ($hn=='livejournal.com')
			{
				$rgx='/\/\/(?<id_acc>.*?)\.livejournal/is';
				preg_match_all($rgx,$item,$acc_id);
				//$mas_inf_lj=get_lj($acc_id['id_acc'][0]);
				$bb1['blog_id']=0;
				$chbb=$db->query('SELECT * from robot_blogs2 WHERE blog_login=\''.$acc_id['id_acc'][0].'\' AND blog_link=\'livejournal.com\'');
				if (mysql_num_rows($chbb)==0)
				{
					$ins_bb=$db->query('INSERT INTO robot_blogs2 (blog_link,blog_login) VALUES (\'livejournal\.com\',\''.$acc_id['id_acc'][0].'\')');
					$bb1['blog_id']=$db->insert_id();
				}
				else
				{
					$bb1=$db->fetch($chbb);
				}
			}
			else
			if ($hn=='facebook.com')
			{
				$rgx='/\&id\=(?<id_acc>\d+)$/is';
				preg_match_all($rgx,$item,$acc_id);
				//$mas_inf_lj=get_lj($acc_id['id_acc'][0]);
				$bb1['blog_id']=0;
				$chbb=$db->query('SELECT * from robot_blogs2 WHERE blog_login=\''.$acc_id['id_acc'][0].'\' AND blog_link=\'facebook.com\'');
				if (mysql_num_rows($chbb)==0)
				{
					$ins_bb=$db->query('INSERT INTO robot_blogs2 (blog_link,blog_login) VALUES (\'facebook\.com\',\''.$acc_id['id_acc'][0].'\')');
					$bb1['blog_id']=$db->insert_id();
				}
				else
				{
					$bb1=$db->fetch($chbb);
				}
			}
			//$db->query('INSERT INTO blog_post_azure (order_id,post_link,post_content,post_time) VALUES ('.$blog['order_id'].',\''.addslashes($item['link']).'\',\''.addslashes($item['content']).'\',\''.$item['time'].'\')');
			//$istrue=check_post($m1['content'][$key],$blog['order_keyword']);
			//if ((check_local($m1['content'][$key],$text_lang)==1))// && ($istrue!=''))
			{
				//if ($istrue=='YES')
				{
					$m2['content'][$key]=$m1['content'][$key];
					/*if (mb_strlen($m1['content'][$key],'UTF-8')>500)
					{
						echo addslashes($item)."\n";
						$blog['order_keyword']=preg_replace('/[^а-яА-Яa-zA-ZёЁ\ \-\=\']/isu','  ',$blog['order_keyword']);
						$mkw=explode('  ',$blog['order_keyword']);
						foreach ($mkw as $kw)
						{
							if (mb_strlen($kw,'UTF-8')>3)
							{
								$regex='/\.(?<frase>[^\.]*?\.[^\.]*?\.[^\.]*?'.addslashes($kw).'\.[^\.]*?\.[^\.]*?\.)/isu';
								preg_match_all($regex,$m1['content'][$key],$out);
								foreach ($out['frase'] as $item)
								{
									if (($item!='') && ($item!=' '))
									{
										$outmas[]=$item;
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
					}*/
					$db->query('INSERT INTO blog_post (order_id,post_link,post_host,post_time,post_content,blog_id) VALUES ('.$blog['order_id'].',\''.addslashes($item).'\',\''.addslashes($hn).'\',\''.$m1['time'][$key].'\',\''.addslashes($m2['content'][$key]).'\','.intval($bb1['blog_id']).')');
					if (mb_strlen($m1['fulltext'][$key],'UTF-8')>500)
					{
						$db->query('INSERT INTO blog_full_com (ful_com_post_id,ful_com_order_id,ful_com_post) VALUES ('.$db->insert_id().','.$blog['order_id'].',\''.addslashes($m1['fulltext'][$key]).'\')');
						echo 'INSERT INTO blog_full_com (ful_com_post_id,ful_com_order_id,ful_com_post) VALUES ('.$db->insert_id().','.$blog['order_id'].',\''.addslashes($m1['fulltext'][$key]).'\')';
					}
					
				}
				//else
				{
					//$db->query('INSERT INTO blog_post (order_id,post_link,post_host,post_time,post_content,blog_id,post_spam) VALUES ('.$blog['order_id'].',\''.addslashes($item).'\',\''.addslashes($hn).'\',\''.$m1['time'][$key].'\',\''.addslashes($m1['content'][$key]).'\','.intval($bb1['blog_id']).',1)');
				}
			}
			//else
			{
				//$db->query('INSERT INTO blog_post (order_id,post_link,post_host,post_time,post_content,blog_id,post_spam) VALUES ('.$blog['order_id'].',\''.addslashes($item).'\',\''.addslashes($hn).'\',\''.$m1['time'][$key].'\',\''.addslashes($m1['content'][$key]).'\','.intval($bb1['blog_id']).',1)');
			}
		}
	}
	unset($rep);
	// $qw=$db->query('UPDATE blog_orders SET third_sources='.time().' WHERE order_id='.$blog['order_id']);
	/*//print_r($outmas);
	//echo "\n".$mstart.' '.$mend.' '.$blog['order_id'];
	$descriptorspec=array(
		0 => array("file","/var/www/azurejob/gg.log","a"),
		1 => array("file","/var/www/azurejob/gg.log","a"),
		2 => array("file","/var/www/azurejob/gg.log","a")
		);

	$cwd='/var/www/azurejob';
	$end=array();
	if (count($outmas)!=0)
	{
		$process=proc_open('php /var/www/bot/cashjob-spec.php '.$blog['order_id'].' &',$descriptorspec,$pipes,$cwd,$end);
	}
	unset($outmas);
	
	if (is_resource($process))
	{
		$return_value=proc_close($process);
		//echo $return_value;
	}*/
	//$cj=parseUrl('http://bmstu.wobot.ru/tools/cashjob.php?order_id='.intval($blog['order_id']));
}
//$ressec=$db->query('UPDATE tp_nodes SET charge=charge-1 WHERE id='.$node_id);
?>
