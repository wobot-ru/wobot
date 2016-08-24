#!/usr/bin/php
<?
require_once('/var/www/com/config.php');
require_once('/var/www/com/func.php');
require_once('/var/www/com/db.php');
require_once('/var/www/bot/kernel.php');

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
//echo $where;
//die();
$ressec=$db->query('SELECT * FROM blog_orders');//' where'.$where);
echo 'new orders to cache: '.mysql_num_rows($ressec)."\n";
$mode='wb';
while($blog=$db->fetch($ressec))
{
	//opening post file to generate cash files ////////////////////////////////////////////////////////////
	//$fn = "/var/www/data/blog/".$blog['order_id'].".xml";
	//$h = fopen($fn, "r");
	//$data = fread($h, filesize($fn));
	//fclose($h);
	$query='SELECT * from blog_post as p LEFT JOIN robot_blogs2 AS b ON p.blog_id=b.blog_id WHERE p.order_id='.$blog['order_id'];
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
	echo $count_d."\n";
	while ($pp=$db->fetch($postst))
	{
		$eng+=$pp['post_engage'];
		$bleng=$blog['order_engage'];
		$posts[]['content']=$pp['post_content'];
		$pcont[]=$pp['post_content'];
		$posts[count($posts)-1]['link']=$pp['post_link'];
		$posts[count($posts)-1]['time']=$pp['post_time'];
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
		$hn1=parse_url($pp['blog_link']);
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
	}
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
			$link=$post['link'];
			//$link=preg_replace("\n","",$link);
			$content=$post['content'];
			$nick=$post['nick'];
			$login=$post['login'];
			$time=$post['time'];
			$loc[$wobot['destn1'][$post['blog_location']]]++;
			//echo $nick."\n";
			$hn=parse_url($link);
			$hn=$hn['host'];
			$ahn=explode('.',$hn);
			$hn = $ahn[count($ahn)-2].'.'.$ahn[count($ahn)-1];
			$hh=$ahn[count($ahn)-2];
			//echo $link." ".$gg." ".$hn."\n";
			//graph block
			$nastr=getnastr($content,$blog['order_keyword']);
			if (in_array($hn,$all_host)) {
			$timet[$hn][date('Y',$time)][date('n',$time)][date('j',$time)]++;
			$timeemo[$nastr][$hn][date('Y',$time)][date('n',$time)][date('j',$time)]++;
			}
			if (!in_array($hn,$all_host)){
			$timet['other'][date('Y',$time)][date('n',$time)][date('j',$time)]++;
			$timeemo[$nastr]['other'][date('Y',$time)][date('n',$time)][date('j',$time)]++;	
			}
			//endof graph block

			//left block && resources list
	        $socweb[$hn]++;
			//endof left block
			
			if ($hn=='twitter.com') {
				list($tmp,$tmp,$tmp,$nick,$tmp)=explode("/",$link,5);
				//$bman=$db->query('SELECT * FROM robot_blogs2 WHERE lower(blog_login)="'.mb_strtolower($nick, 'UTF-8').'" AND blog_link="twitter.com"');
				//if (mysql_num_rows($bman)>0)
				{
					//$man=$db->fetch($bman);
					$allposts[]=array('content'=>$content,'nick'=>$login/*$nick*/,'readers'=>$post['reads'],'site'=>'twitter.com');
					//echo $hn." ".$nick." ".$man['blog_readers']."\n";
					//array_push($allposts,array('content'=>$content,'nick'=>$man['blog_login'],'readers'=>$man['blog_readers'],'site'=>'twitter'));
				}
			}
			else
			if (($hn=='livejournal.com') || ($hn=='vkontakte.ru') || ($hn=='facebook.com'))
			{
				//$bman=$db->query('SELECT * FROM robot_blogs2 WHERE lower(blog_login)="'.mb_strtolower($nick, 'UTF-8').'" AND blog_link="'.$hn.'"');
				//if (mysql_num_rows($bman)>0)
				{
					//$man=$db->fetch($bman);
					$allposts[]=array('content'=>$content,'nick'=>$login/*$nick*/,'readers'=>$post['reads'],'site'=>$hn);
					//echo $hn." ".$nick." ".$man['blog_readers']."\n";
					//array_push($allposts,array('content'=>$content,'nick'=>$man['blog_login'],'readers'=>$man['blog_readers'],'site'=>'twitter'));
				}
			}
			
		$count++;
	}
print_r($loc);
$topwords=gettopwords($pcont);
//print_r($topwords);

//print_r($posts);



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
	$q1='UPDATE blog_orders SET order_graph="'.addslashes(json_encode(array('all'=>$timet,'emo'=>$timeemo))).'" WHERE order_id='.$blog['order_id'];
	$p1=$db->query($q1);
	//endof graph block file /////////////////////////////////////////////////////////////////////////

	//generating metrics block file /////////////////////////////////////////////////////////////////////////
	unset($metrics);
	//$allposts[]=array('content'=>$content,'nick'=>$man['blog_login'],'readers'=>$man['blog_readers']);
	//$metrics['posts']=$allposts;
	$metrics['speakers']=speakers($allposts);
	//echo 'SPEAKERs'."\n";
	//print_r($metrics['speakers']);
	//unset($allposts);
	//print_r($metrics['speakers']);
	//print_r($posts['nick']);
	foreach ($metrics['speakers']['nick'] as $pnick => $mas)
	{
		foreach ($posts as $ppp)
		{
			if ($ppp['nick']==$mas)
			{
				//echo $ppp['nick']." ".$ppp['blog_link'];
				$metrics['speakers']['link'][$pnick]=$metrics['speakers']['site'][$pnick];//$ppp['blog_link'];
				$metrics['speakers']['login'][$pnick]=$ppp['nick'];
				//echo $ppp['blog_link']." ".$pnick."\n";
			}
		}		
	}
	if ($bleng=='1')
	{
		$metrics['engagement']=intval($eng);
	}
	$metrics['value']=value($allposts);
	$metrics['promotion']=promotion($allposts);
	//echo 'PROMOUTERY'."\n";
	//print_r($metrics['promotion']);
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
	unset($topwords);
	//print_r($topwords);
	$metrics['location']=$loc;
	unset($loc);
	//$fp = fopen('/var/www/data/blog/'.$blog['order_id'].'.metrics', 'wb');
	//fwrite($fp, json_encode($metrics));
	//fclose($fp);
	$q1='UPDATE blog_orders SET order_metrics="'.addslashes(json_encode($metrics)).'" WHERE order_id='.$blog['order_id'];
	$p1=$db->query($q1);
	//endof metrics block file /////////////////////////////////////////////////////////////////////////

	echo $blog['order_id']." done\n";
	unset($blog);
}
?>
