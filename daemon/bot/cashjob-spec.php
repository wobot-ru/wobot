<?
require_once('/var/www/com/config.php');
require_once('/var/www/com/func.php');
require_once('/var/www/com/db.php');
require_once('/var/www/bot/kernel.php');

date_default_timezone_set ( 'Europe/Moscow' );
error_reporting(E_ERROR | E_PARSE);
ignore_user_abort(true);
set_time_limit(0);
ini_set('max_execution_time',0);
ini_set('default_charset','utf-8');
ob_implicit_flush();

$db = new database();
$db->connect();
$i=0;
for ($i=1;$i<count($argv);$i++)
{
	if ($i>1) $where.=' or';
	$where.=' order_id='.intval($argv[$i]);
}
//print_r($argv);
//echo $where;
//die();
echo 'SELECT * FROM blog_orders where'.$where;
$ressec=$db->query('SELECT order_start,order_end,order_name,order_keyword,order_id,order_last,third_sources FROM blog_orders where'.$where);
echo 'new orders to cache: '.mysql_num_rows($ressec)."\n";
$mode='wb';
while($blog=$db->fetch($ressec))
{
	$order_start=$blog['order_start']+(intval(($order['order_end']-$order['order_start'])/86400) % 5)*86400;
	$order_end=($blog['order_end']==0)?mktime(0,0,0,date('n'),date('j'),date('Y')):$blog['order_end'];
	if ($order_end>mktime(0,0,0,date('n'),date('j'),date('Y')))
	{
		$order_end=mktime(0,0,0,date('n'),date('j'),date('Y'));
	}
	//opening post file to generate cash files ////////////////////////////////////////////////////////////
	//$fn = "/var/www/data/blog/".$blog['order_id'].".xml";
	//$h = fopen($fn, "r");
	//$data = fread($h, filesize($fn));
	//fclose($h);
	if ($blog['order_end']!=0)
	{
		if ($blog['order_end']<time())
		{
			$enddd=mktime(0,0,0,date('n',$blog['order_end']),date('j',$blog['order_end'])+1,date('Y',$blog['order_end']));
		}
		else
		{
			$enddd=mktime(0,0,0,date('n'),date('j')+1,date('Y'));
		}
	}
	else
	{
		$enddd=mktime(0,0,0,date('n'),date('j')+1,date('Y'));
	}
	
	if ($blog['order_last']==0)
	{
		//echo $blog['order_last'].'ALLALALA';
	//	$enddd=time();
	}
	$query='SELECT * from blog_post as p LEFT JOIN robot_blogs2 AS b ON p.blog_id=b.blog_id WHERE p.order_id='.$blog['order_id'].' AND post_time>'.(intval($blog['order_start'])).' AND post_time<='.intval($enddd).' order by post_time ASC';
	echo $query;
	$postst=$db->query($query);
	$data='<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
	<html>
	<head>
	<meta content="text/html; charset=utf-8" http-equiv="Content-Type">
	'.$data.'
	</head>
	</html>
	';
	        //$dom = new DomDocument;
	        //$res = @$dom->loadHTML($data);
	//$posts = $dom->getElementsByTagName("post");
	unset($posts);
	$count_last_d=0;
	$count_last_m=0;
	$colpos=0;
	$colneg=0;
	if ($blog['order_end']!=0)
	{
		if ($blog['order_last']<$blog['order_end'])
		{
			$count_d=($blog['order_last']-$blog['order_start'])/86400;
			if ($count_d>30)
			{
				$count_d=30;
			}
		}
		else
		{
			$count_d=($blog['order_end']-$blog['order_start'])/86400;
			if ($count_d>30)
			{
				$count_d=30;
			}
		}
	}
	else
	{
		$count_d=($blog['order_last']-$blog['order_start'])/86400;
		if ($count_d>30)
		{
			$count_d=30;
		}
	}
	$k=1;
	echo 'count_d='.$count_d."\n";
	while ($pp=$db->fetch($postst))
	{
		$eng+=$pp['post_engage'];
		$bleng=$blog['order_engage'];
		$posts[]['content']=$pp['post_content'];
		$pcont['words'][]=$pp['post_content'];
		$pcont['time'][]=$pp['post_time'];
		//$pcont[]=html_entity_decode($pp['post_content'],ENT_QUOTES);
		$posts[count($posts)-1]['link']=$pp['post_link'];
		$posts[count($posts)-1]['time']=$pp['post_time'];
		$posts[count($posts)-1]['engg']=$pp['post_engage'];
		$posts[count($posts)-1]['phost']=$pp['post_host'];
		if ($blog['order_end']==0)
		{
			if (($pp['post_time']>=($blog['order_last'])) && ($pp['post_time']<($blog['order_last']+86400)))
			{
				$count_last_d++;
			}
		}
		else
		{
			if (($pp['post_time']>=($blog['order_end'])) && ($pp['post_time']<($blog['order_end']+86400)))
			{
				$count_last_d++;
			}
		}
		if ($blog['order_end']!=0)
		{
			if ($blog['order_last']<$blog['order_end'])
			{
				if (($pp['post_time']>=($blog['order_last']-(86400*$count_d))) && ($pp['post_time']<=$blog['order_last']))
				{
					$count_last_m++;
				}
			}
			else
			{
				if (($pp['post_time']>=($blog['order_end']-(86400*$count_d))) && ($pp['post_time']<=$blog['order_end']))
				{
					$count_last_m++;
				}
			}
		}
		else
		{
			if (($pp['post_time']>=($blog['order_last']-(86400*$count_d))) && ($pp['post_time']<=$blog['order_last']))
			{
				$count_last_m++;
			}
		}
		$posts[count($posts)-1]['login']=$pp['blog_login'];
		$posts[count($posts)-1]['nick']=$pp['blog_nick'];
		$posts[count($posts)-1]['reads']=$pp['blog_readers'];
		$posts[count($posts)-1]['blog_link']=$pp['blog_link'];
		$posts[count($posts)-1]['blog_location']=$pp['blog_location'];
		$posts[count($posts)-1]['blog_id']=$pp['blog_id'];
		$posts[count($posts)-1]['nastr']=$pp['post_nastr'];
		$hn1=parse_url($pp['post_link']);
		//print_r($hn1);
		$hn1=$hn1['path'];
		$ahn1=explode('.',$hn1);
		$hn1 = $ahn1[count($ahn1)-2].'.'.$ahn1[count($ahn1)-1];
		$hh1=$ahn1[count($ahn1)-2];		
		$posts[count($posts)-1]['hn']=$hn1;
		if ($pp['post_nastr']==1)
		{
			$colpos++;
		}
		else
		if ($pp['post_nastr']==-1)
		{
			$colneg++;
		}
		//$posts[]['content']=$pp['post_content'];$pp['post_time']
		//if ($pp['post_time']>$order_start+$k*(intval(($order_end-$order_start)/86400) / 5)*86400)
		if ($pp['post_time']>$order_end-5*86400)
		{
			$val_post++;
			$val_uniq[$pp['blog_id']]++;
			$val_src[$hn1]++;
			$val_au+=$pp['blog_readers'];
			$val_eng+=$pp['post_engage'];
			if ($pp['post_time']>$order_end-(5-$k)*86400)
			{
				echo date('r',$pp['post_time'])."\n";
				$uniqval[]=count($val_uniq);
				$srcval[]=count($val_src);
				unset($val_src);
				unset($val_uniq);
				$mval[]=$val_post;
				$auval[]=$val_au;
				$engval[]=$val_eng;
				$val_eng=0;
				$val_au=0;
				$val_post=0;
				$k++;
			}
		}
	}
	$mval[]=$val_post;
	$uniqval[]=count($val_uniq);
	$srcval[]=count($val_src);
	$auval[]=$val_au;
	$engval[]=$val_eng;
	if (count($engval)<5)
	{
		for ($i=0;$i<5-(count($engval));$i++)
		{
			$engval[]=0;
		}
	}
	if (count($auval)<5)
	{
		for ($i=0;$i<5-(count($auval));$i++)
		{
			$auval[]=0;
		}
	}
	if (count($srcval)<5)
	{
		for ($i=0;$i<5-(count($srcval));$i++)
		{
			$srcval[]=0;
		}
	}
	if (count($mval)<5)
	{
		for ($i=0;$i<5-(count($mval));$i++)
		{
			$mval[]=0;
		}
	}
	if (count($uniqval)<5)
	{
		for ($i=0;$i<5-(count($uniqval));$i++)
		{
			$uniqval[]=0;
		}
	}
	get_main_img($mval,$blog['order_id'],'main');
	get_main_img($mval,$blog['order_id'],'main_2');
	get_main_img($uniqval,$blog['order_id'],'uniq');
	get_main_img($srcval,$blog['order_id'],'src');
	get_main_img($auval,$blog['order_id'],'aud');
	get_main_img($engval,$blog['order_id'],'eng');
	print_r($mval);
	echo $blog['order_start']." ".$blog['order_last']." ".$blog['order_end']."\n";
	$count=0;
	unset($socweb);
	unset($av_host);
	unset($timet);
	unset($allposts);
	$all_host[0]='twitter.com';
	$all_host[1]='livejournal.com';
	$all_host[2]='ya.ru';
	$all_host[3]='blogspot.com';
	//print_r($posts);
	foreach ($posts as $gg => $post)
	{
	        //$link=$post->firstChild->nextSibling->textContent;//->firstChild->nextSibling
	        //$time=$post->firstChild->nextSibling->nextSibling->textContent;
	        //$content=$post->firstChild->nextSibling->nextSibling->nextSibling->textContent;
	        //$nick=$post->firstChild->nextSibling->nextSibling->nextSibling->nextSibling->textContent;
	        //echo 'link ['.$link.'] time ['.$time.'] content ['.$content.'] nick ['.$nick.']<br>';
	        //$timet[date('Y',$time)][date('n',$time)][date('j',$time)]++;
			$blog_id=$post['blog_id'];
			$link=$post['link'];
			//$link=preg_replace("\n","",$link);
			$content=$post['content'];
			$nick=$post['nick'];
			$login=$post['login'];
			$time=$post['time'];
			$loc[$wobot['destn1'][$post['blog_location']]]++;
			$loc_cou[$wobot['destn3'][$wobot['destn1'][$post['blog_location']]]]++;
			//echo $nick."\n";
			$hn=parse_url($link);
			$hn=$hn['host'];
			$ahn=explode('.',$hn);
			$hn = $ahn[count($ahn)-2].'.'.$ahn[count($ahn)-1];
			$hh=$ahn[count($ahn)-2];
			if ($hn=='.')
			{
				$hn=$post['phost'];
			}
			//echo $link." ".$gg." ".$hn."\n";
			//graph block
			//$nastr=getnastr($content,$blog['order_keyword']);
			if (in_array($hn,$all_host)) {
			$timet[$hn][date('Y',$time)][date('n',$time)][date('j',$time)]++;
			$timeemo[$nastr][$hn][date('Y',$time)][date('n',$time)][date('j',$time)]++;
			}
			if (!in_array($hn,$all_host)){
			$timet['other'][date('Y',$time)][date('n',$time)][date('j',$time)]++;
			$timeemo[$nastr]['other'][date('Y',$time)][date('n',$time)][date('j',$time)]++;	
			}
			$beta_data['src'][mktime(0,0,0,date('n',$time),date('j',$time),date('Y',$time))][$hn]++;
			$beta_data['src_nastr'][mktime(0,0,0,date('n',$time),date('j',$time),date('Y',$time))][$post['nastr']]++;
			//$beta_data['sp_pr_ws'][mktime(0,0,0,date('n',$time),date('j',$time),date('Y',$time))][$hn][$nick]['count']++;
			//$beta_data['sp_pr_ws'][mktime(0,0,0,date('n',$time),date('j',$time),date('Y',$time))][$hn][$nick]['readers']=$post['reads'];
			$beta_data['geo'][mktime(0,0,0,date('n',$time),date('j',$time),date('Y',$time))][$wobot['destn1'][$post['blog_location']]]++;

			//$beta_data['val'][mktime(0,0,0,date('n',$time),date('j',$time),date('Y',$time))][$login]=$post['reads'];
			$beta_data['eng'][mktime(0,0,0,date('n',$time),date('j',$time),date('Y',$time))]+=$post['engg'];
			//echo date('r',$time)."\n";
			//endof graph block

			//left block && resources list
			if ($hn=='facebook.com')
			{
				$iop++;
				//echo $iop;
			}
	        $socweb[$hn]++;
			$login1=$login;
			if ($hn=='livejournal.com')
			{
				$rgx='/http\:\/\/(?<nick>.*?)\./is';
				preg_match_all($rgx,$link,$ouut);
				$login1=$ouut['nick'][0];
			}
			//endof left block
			//echo $link.' '.$login1."\n";
			$nastr=$post['nastr'];
			//$beta_data['megamas'][mktime(0,0,0,date('n'),date('j'),date('Y'))][$wobot['destn1'][$post['blog_location']]][$hn][$post['nastr']]['count']++;
			/*$megamas['data'][$wobot['destn1'][$post['blog_location']]][$hn][mktime(0,0,0,date('n'),date('j'),date('Y'))]['count']++;
			$megamas['data'][$wobot['destn1'][$post['blog_location']]][$hn]['count']++;
			$megamas['data'][$wobot['destn1'][$post['blog_location']]]['count']++;*/
			if ($hn=='twitter.com') {
				list($tmp,$tmp,$tmp,$nick,$tmp)=explode("/",$link,5);
				//$bman=$db->query('SELECT * FROM robot_blogs2 WHERE lower(blog_login)="'.mb_strtolower($nick, 'UTF-8').'" AND blog_link="twitter.com"');
				//if (mysql_num_rows($bman)>0)
				{
					if (!isset($yet_users[$login.':'.$hn.':'.mktime(0,0,0,date('n',$time),date('j',$time),date('Y',$time))]) && ($login!=''))
					{
						$vvv+=$post['reads'];
						$value_din[$hn]+=$post['reads'];
						$beta_data['value'][mktime(0,0,0,date('n',$time),date('j',$time),date('Y',$time))][$hn]+=$post['reads'];
						$beta_data['value2'][mktime(0,0,0,date('n',$time),date('j',$time),date('Y',$time))][$login][$hn]+=$post['reads'];
						$beta_data['uniq'][mktime(0,0,0,date('n',$time),date('j',$time),date('Y',$time))][$login][$hn]=$post['reads'];
					}
					$yet_users[$login.':'.$hn.':'.mktime(0,0,0,date('n',$time),date('j',$time),date('Y',$time))]++;
					$beta_data['prom_posts'][mktime(0,0,0,date('n',$time),date('j',$time),date('Y',$time))][$login1.':'.$hn]++;
					$beta_data['sp_pr'][mktime(0,0,0,date('n',$time),date('j',$time),date('Y',$time))][$nick][$hn]['count']++;
					$beta_data['sp_pr'][mktime(0,0,0,date('n',$time),date('j',$time),date('Y',$time))][$nick][$hn]['readers']=$post['reads'];
					$beta_data['sp_pr'][mktime(0,0,0,date('n',$time),date('j',$time),date('Y',$time))][$nick][$hn]['login']=$nick;
					$beta_data['megamas'][mktime(0,0,0,date('n',$time),date('j',$time),date('Y',$time))][$wobot['destn1'][$post['blog_location']]][$hn][$nastr]['count']++;
					$beta_data['megamas_wsp'][mktime(0,0,0,date('n',$time),date('j',$time),date('Y',$time))][$wobot['destn1'][$post['blog_location']]][$hn][$nastr][$nick]['reads']=$post['reads'];
					$beta_data['megamas_wsp'][mktime(0,0,0,date('n',$time),date('j',$time),date('Y',$time))][$wobot['destn1'][$post['blog_location']]][$hn][$nastr][$nick]['count']++;
					//$man=$db->fetch($bman);
					if ($hn=='vkontakte.ru')
					{
						$eng_din['vk.com']+=$post['engg'];
					}
					else
					{
						$eng_din[$hn]+=$post['engg'];
					}
					$beta_data['eng_time'][mktime(0,0,0,date('n',$time),date('j',$time),date('Y',$time))][$hn]+=$post['engg'];
					$allposts[]=array('id'=>$blog_id,'content'=>$content,'nick'=>$login/*$nick*/,'readers'=>$post['reads'],'site'=>'twitter.com');
					$allposts_prom[]=array('id'=>$blog_id,'content'=>$content,'nick'=>$login1/*$nick*/,'readers'=>$post['reads'],'site'=>'twitter.com');
					//echo $hn." ".$nick." ".$man['blog_readers']."\n";
					//array_push($allposts,array('content'=>$content,'nick'=>$man['blog_login'],'readers'=>$man['blog_readers'],'site'=>'twitter'));
				}
			}
			else
			if (($hn=='livejournal.com') || ($hn=='vkontakte.ru') || ($hn=='facebook.com') || ($hn=='vk.com'))
			{
				//$bman=$db->query('SELECT * FROM robot_blogs2 WHERE lower(blog_login)="'.mb_strtolower($nick, 'UTF-8').'" AND blog_link="'.$hn.'"');
				//if (mysql_num_rows($bman)>0)
				{
					//$man=$db->fetch($bman);
					$allposts[]=array('id'=>$blog_id,'content'=>$content,'nick'=>$login/*$nick*/,'readers'=>$post['reads'],'site'=>$hn,'rnick'=>$nick);
					$readers_prom=$post['reads'];
					//if (($hn=='livejournal.com') && ($post['blog_id']==0))
					if ((strpos($link, 'thread')!==false) && ($hn='livejournal.com'))
					{
						$rgx='/\/\/(?<nk>.*?)\./is';
						preg_match_all($rgx,$link,$out);
						$login1=$out['nk'][0];
						if (!isset($prom_info[$login]))
						{
							//echo 'SELECT * FROM robot_blogs2 WHERE blog_login=\''.$login.'\' AND blog_link=\'livejournal\.com\' LIMIT 1'."\n";
							$bl_info=$db->query('SELECT blog_id,blog_readers FROM robot_blogs2 WHERE blog_login=\''.$login.'\' AND blog_link=\'livejournal\.com\' LIMIT 1');
							$innf=$db->fetch($bl_info);
							$readers_prom=$innf['blog_readers'];
							$prom_info[$login]=$readers_prom;
							$blog_id=$innf['blog_id'];
						}
						else
						{
							$readers_prom=$prom_info[$login];
						}
						echo $link.' '.$login1.' '.$login.' '.$readers_prom."\n";
						//echo $login1.' '.$readers_prom."\n";
						//echo $login1.' '.$readers_prom.' '.."\n";
					}
					if (!isset($yet_users[$login.':'.$hn.':'.mktime(0,0,0,date('n',$time),date('j',$time),date('Y',$time))]) && ($login!=''))
					{
						$vvv+=$readers_prom;
						$value_din[$hn]+=$readers_prom;
						$beta_data['value'][mktime(0,0,0,date('n',$time),date('j',$time),date('Y',$time))][$hn]+=$post['reads'];
						$beta_data['value2'][mktime(0,0,0,date('n',$time),date('j',$time),date('Y',$time))][$login][$hn]+=$post['reads'];
						$beta_data['uniq'][mktime(0,0,0,date('n',$time),date('j',$time),date('Y',$time))][$login][$hn]=$post['reads'];//$readers_prom; !!!$nick
					}
					$yet_users[$login.':'.$hn.':'.mktime(0,0,0,date('n',$time),date('j',$time),date('Y',$time))]++;
					//$yet_users1[$nick.':'.$hn]++;
					$yet_users1[$login1.':'.$nick.':'.$hn]++;
					if ($login1!=$nick)
					{
						$beta_data['prom_posts'][mktime(0,0,0,date('n',$time),date('j',$time),date('Y',$time))][$nick.':'.$hn]++;
					}
					$beta_data['prom_posts'][mktime(0,0,0,date('n',$time),date('j',$time),date('Y',$time))][$login1.':'.$hn]++;
					//echo $nick.' '.$login1."\n";
					$beta_data['sp_pr'][mktime(0,0,0,date('n',$time),date('j',$time),date('Y',$time))][$nick][$hn]['count']++;
					$beta_data['sp_pr'][mktime(0,0,0,date('n',$time),date('j',$time),date('Y',$time))][$nick][$hn]['readers']=$readers_prom;//$post['reads'];
					$beta_data['sp_pr'][mktime(0,0,0,date('n',$time),date('j',$time),date('Y',$time))][$nick][$hn]['login']=$login1;
					$beta_data['megamas'][mktime(0,0,0,date('n',$time),date('j',$time),date('Y',$time))][$wobot['destn1'][$post['blog_location']]][$hn][$nastr]['count']++;
					$beta_data['megamas_wsp'][mktime(0,0,0,date('n',$time),date('j',$time),date('Y',$time))][$wobot['destn1'][$post['blog_location']]][$hn][$nastr][$nick]['reads']=$post['reads'];
					$beta_data['megamas_wsp'][mktime(0,0,0,date('n',$time),date('j',$time),date('Y',$time))][$wobot['destn1'][$post['blog_location']]][$hn][$nastr][$nick]['count']++;
					//echo $login1.' '.$readers_prom.' '.$vvv."\n";
					if ($hn=='vkontakte.ru')
					{
						$eng_din['vk.com']+=$post['engg'];
					}
					else
					{
						$eng_din[$hn]+=$post['engg'];
					}
					$beta_data['eng_time'][mktime(0,0,0,date('n',$time),date('j',$time),date('Y',$time))][$hn]+=$post['engg'];
					$allposts_prom[]=array('id'=>$blog_id,'content'=>$content,'nick'=>$login1/*$nick*/,'readers'=>$readers_prom,'site'=>$hn,'rnick'=>$nick);
					//echo $hn." ".$nick." ".$man['blog_readers']."\n";
					//array_push($allposts,array('content'=>$content,'nick'=>$man['blog_login'],'readers'=>$man['blog_readers'],'site'=>'twitter'));
				}
			}
			
		$count++;
	}
	//print_r($allposts_prom);
	//print_r($beta_data['prom_posts']);
	//print_r($yet_users1);
//print_r($loc);
//print_r($pcont);
//print_r($beta_data['src_nastr']);
//print_r($allposts_prom);
$topwords=gettopwords($pcont);
$beta_data['words']=$topwords[1];
print_r($beta_data['uniq'][1339790400]);
//die();
$topwords=$topwords[0];
//print_r($beta_data['words']);
//print_r($topwords);
//print_r($timet);
//print_r($posts);
//print_r($megamas);
//-------------

/*foreach ($loc as $item1 => $key1)
{
	if (!isset($mwobot[$wobot['destn2'][$item1]]['area']))
	{
		//echo $wobot['destn2'][$item1].' '.$item1.'<br>';
	}
	$oumm[$mwobot[$wobot['destn2'][$item1]]['area']]+=$key1;
}
echo '<br>AREAS:<br>';
foreach ($oumm as $kk => $ii)
{
	if ($kk!='')
	{
		//echo $kk.' '.$ii.'<br>';
	}
}
print_r($oumm);*/

//-------------

	//generating left column file /////////////////////////////////////////////////////////////////////////

	arsort($socweb);
	unset($leftout);
	unset($leftout2);
	//unset($sources);
	$leftout.= '
	<h1 class="sh">Найдено</h1>
	<table>
	<tr><td><span class="ss"><u><a href="#" onclick="loaditem(\'user/comment?order_id='.intval($blog['order_id']).'\',\'#commentbox\',function () { showcomment(); } ); return false;"><span class="rln">'.$count.'</span></a></u></span></td><td><span class="ss">мнений по запросу</span></td></tr>
	';
	$leftout2=$leftout;
	$i=0;
	$other=0;
	foreach($socweb as $sw => $cnt)
	{
		$i++;
		if ($i<11)
		$leftout.= '<tr class="socline"><td><span class="ss"><u><a href="#" onclick="loaditem(\'user/comment?order_id='.intval($blog['order_id']).'&social='.$sw.'\',\'#commentbox\',function () { showcomment(); } ); return false;"><span class="rln">'.$cnt.'</span></a></u></span></td><td><a href="http://'.$sw.'" target="_blank"  style="text-decoration: none;"><span class="ss" style="text-decoration: none;">'.(strlen($sw)>20?substr($sw,0,20).'...':$sw).'</span></a></td></tr>';
		else $other+=$cnt;
		$leftout2.= '<tr class="socline"><td><span class="ss"><u><a href="#" onclick="loaditem(\'user/comment?order_id='.intval($blog['order_id']).'&social='.$sw.'\',\'#commentbox\',function () { showcomment(); } ); return false;"><span class="rln">'.$cnt.'</span></a></u></span></td><td><a href="http://'.$sw.'" target="_blank"  style="text-decoration: none;"><span class="ss" style="text-decoration: none;">'.(strlen($sw)>20?substr($sw,0,20).'...':$sw).'</span></a></td></tr>';
	}
	if ($other>0)
	$leftout.= '<tr><td colspan="2"><a href="#" onclick="loaditem(\'user/left?order_id='.intval($blog['order_id']).'&other\',\'#leftbox\'); return false;"><span class="ss" style="text-decoration: none;" onclick="">другие ('.$other.')</span></a></td></tr>';
	$leftout.= '</table>
	';
	$leftout2.= '</table>
	';
	//$fp = fopen('/var/www/data/blog/'.$blog['order_id'].'.left', 'wb');
	//fwrite($fp, $leftout);
	//fclose($fp);
	//$fp = fopen('/var/www/data/blog/'.$blog['order_id'].'.left2', 'wb');
	//fwrite($fp, $leftout2);
	//fclose($fp);
	//$fp = fopen('/var/www/data/blog/'.$blog['order_id'].'.src', 'wb');
	//fwrite($fp, json_encode($socweb));
	//fclose($fp);
	$q1='UPDATE blog_orders SET order_left="'.addslashes($leftout).'", order_left2="'.addslashes($leftout2).'", order_src="'.addslashes(json_encode($socweb)).'" WHERE order_id='.$blog['order_id'];
	$p1=$db->query($q1);
	unset($leftout);
	unset($leftout2);
	//endof left column file ///////////////////////////////////////////////////////////////





	//generating graph block file /////////////////////////////////////////////////////////////////////////
//	$fp = fopen('/var/www/data/blog/'.$blog['order_id'].'.graph', 'wb');
//	fwrite($fp, json_encode(array('all'=>$timet,'emo'=>$timeemo)));
//	fclose($fp);
	//print_r($timet);
	$q1='UPDATE blog_orders SET order_graph="'.addslashes(json_encode(array('all'=>$timet,'emo'=>$timeemo))).'" WHERE order_id='.$blog['order_id'];
	$p1=$db->query($q1);
	//endof graph block file /////////////////////////////////////////////////////////////////////////

	//generating metrics block file /////////////////////////////////////////////////////////////////////////
	unset($metrics);
	//$allposts[]=array('content'=>$content,'nick'=>$man['blog_login'],'readers'=>$man['blog_readers']);
	//$metrics['posts']=$allposts;
	$metrics['speakers']=speakers($allposts);
	echo 'SPEAKERs'."------\n";
	//print_r($metrics['speakers']);
	//unset($allposts);
	//print_r($metrics['speakers']);
	//print_r($posts['nick']);
	foreach ($metrics['speakers']['nick'] as $pnick => $mas)
	{
		if ($metrics['speakers']['site'][$pnick]=='vkontakte.ru')
		{
			$metrics['speakers']['link'][$pnick]='vkontakte.ru';//$ppp['blog_link'];
		}
		else
		{
			foreach ($posts as $ppp)
			{
				if ($ppp['nick']==$mas)
				{
					//echo $ppp['nick']." ".$ppp['blog_link'];
					$metrics['speakers']['link'][$pnick]=$metrics['speakers']['site'][$pnick];//$ppp['blog_link'];
					if ($ppp['site']!='vkontakte.ru')
					{
						$metrics['speakers']['login'][$pnick]=$ppp['nick'];
					}
					else
					{
						$metrics['speakers']['login'][$pnick]=$ppp['rnick'];
					}
					//echo $ppp['blog_link']." ".$pnick."\n";
				}
			}
		}		
	}
	echo 'SPEAKERs'."------\n";
	print_r($metrics['speakers']);
	if ($bleng=='1')
	{
		$metrics['engagement']=intval($eng);
	}
	$metrics['value']=$vvv;//value($allposts_prom);
	$metrics['promotion']=promotion($allposts_prom);
	//echo 'PROMOUTERY'."\n";
	print_r($metrics['promotion']);
	foreach ($metrics['promotion']['nick'] as $pnick => $mas)
	{
		foreach ($posts as $ppp)
		{
			if ($ppp['nick']==$mas)
			{
				//echo $ppp['nick']." ".$ppp['blog_link'];
				$metrics['promotion']['link'][$pnick]=$metrics['promotion']['site'][$pnick];//$ppp['blog_link'];
				$metrics['promotion']['login'][$pnick]=$ppp['nick'];
				//echo $ppp['blog_link']." ".$pnick."\n";
			}
		}		
	}
	//print_r($metrics['promotion']);
	/*$nacht=$blog['order_start'];
	$kont=$blog['order_last'];
	if ($kont==0)
	{
		$kont=time();
	}
	if ((($kont-$nacht)/86400)<30)
	{
		$din=$db->query('SELECT COUNT(*) FROM blog_post WHERE order_id='.$blog['order_id']);
		while ($dint=$db->fetch($din))
		{
			$metrics['din_posts']=intval($count_last_d/(($dint['COUNT(*)']/($kont-$nacht))*86400));
			//echo $count_last_d." ".($dint['COUNT(*)']/($kont-$nacht))*86400;
			//echo $metrics['din_posts'];
		}
	}
	else
	{
		$din=$db->query('SELECT COUNT(*) FROM blog_post WHERE order_id='.$blog['order_id'].' AND post_time<'.$kont.' AND post_time>'.($kont-86400*30));
		while ($dint=$db->fetch($din))
		{
			$metrics['din_posts']=intval($count_last_d/($dint['COUNT(*)']/30));
			//echo $count_last_d." ".($dint['COUNT(*)']/30);
		}
	}*/
	echo $count_last_d." ".$count_last_m/$count_d." ".(($count_last_d-($count_last_m/$count_d))/($count_last_m/$count_d)*100)."%\n";
	if ($count_last_m!=0)
	{
		$metrics['din_posts']=intval(($count_last_d-($count_last_m/$count_d))/($count_last_m/$count_d)*100);
	}
	else
	{
		$metrics['din_posts']='NA';
	}
	$metrics['pos_posts']=$colpos;
	$metrics['neg_posts']=$colneg;
	$metrics['topwords']=$topwords;
	$metrics['d_post']=$mval[4]-$mval[3];
	$metrics['d_src']=$srcval[4]-$srcval[3];
	$metrics['d_aud']=$auval[4]-$auval[3];
	$metrics['d_eng']=$engval[4]-$engval[3];
	$metrics['d_uniq']=$uniqval[4]-$uniqval[3];
	$metrics['value_din']=$value_din;
	$metrics['prom_posts']=$yet_users;
	//print_r($value_din);
	//print_r($beta_data['value']);
	$metrics['eng_din']=$eng_din;
	unset($topwords);
	//print_r($topwords);
	$metrics['location']=$loc;
	$metrics['location_cou']=$loc_cou;
	//print_r($loc_cou);
	unset($loc);
	unset($loc_cou);
	//print_r($metrics);
	//$fp = fopen('/var/www/data/blog/'.$blog['order_id'].'.metrics', 'wb');
	//fwrite($fp, json_encode($metrics));
	//fclose($fp);
	$q1='UPDATE blog_orders SET order_metrics="'.addslashes(json_encode($metrics)).'" WHERE order_id='.$blog['order_id'];
	$p1=$db->query($q1);
	if (count($posts)>40000)
	{
		$m = new Memcached();
		$m->addServer('localhost', 11211);
		$m->setOption(Memcached::OPT_COMPRESSION, false);
		$m->setMulti(array('order_'.$blog['order_id']=>json_encode($beta_data)),time()+86400);
		//print_r(array($blog['order_id']=>'123'));
		//var_dump($m->get('order_'.$blog['order_id']));
		$q1='UPDATE blog_orders SET order_beta=\'\' WHERE order_id='.$blog['order_id'];
		$p1=$db->query($q1);
	}
	else
	{
		$q1='UPDATE blog_orders SET order_beta="'.addslashes(json_encode($beta_data)).'" WHERE order_id='.$blog['order_id'];
		$p1=$db->query($q1);
	}
	//endof metrics block file /////////////////////////////////////////////////////////////////////////

	echo $blog['order_id']." done\n";
	unset($blog);
}
?>
