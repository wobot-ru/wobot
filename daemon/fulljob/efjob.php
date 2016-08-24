<?

// require_once('/var/www/daemon/com/config.php');
require_once('com/func.php');
// require_once('/var/www/daemon/com/db.php');
//require_once('/var/www/bot/kernel.php');
require_once('functtt/functions.php');
require_once('/var/www/daemon/new/com/porter.php');
require_once('adv_src_func.php');
require_once('com/sources.php');
require_once('/var/www/daemon/Engagement/rEngtoFt/facebook_eg_job.php');
require_once('/var/www/daemon/Engagement/rEngtoFt/livejournal_eg_job.php');
require_once('/var/www/daemon/Engagement/rEngtoFt/twitter_eg_job.php');
require_once('/var/www/daemon/Engagement/rEngtoFt/vkontakte_eg_job.php');

// $db = new database();
// $db->connect();

$redis = new Redis();    
$redis->connect('127.0.0.1');

$memcache = memcache_connect('localhost', 11211);

$fulljob_session_timeout=10;
error_reporting(0);
$order_delta=$_SERVER['argv'][1];

$retro=($_SERVER['argv'][1]%3==0?'_retro':'');

$fp = fopen('/var/www/pids/ef'.$order_delta.'.pid', 'w');
fwrite($fp, getmypid());
fclose($fp);

$mrgx['twitter.com']='/<p class=[\'\"]js-tweet-text tweet-text\s+[\'\"]>(?<text>.*?)<\/p>/isu';
$mxpth['kp.ru']='//content/div[@class="a_content"]|//div[@class="GL_MAR10T  GL_MAR10B MESS"]';
$mxpth['liveinternet.ru']='//content/div[@class="a_content"]|//div[@class="GL_MAR10T  GL_MAR10B MESS"]';
$mxpth['foursquare.com']='//p[@class="shout"]';

$eng_sources['twitter.com']=1;
$eng_sources['livejournal.com']=1;
$eng_sources['facebook.com']=1;
$eng_sources['vk.com']=1;
$eng_sources['vkontakte.ru']=1;

$word=new Lingua_Stem_Ru();
function GetFullPost($link,$part,$kword,$valeng)
{
	//echo $link;
	global $word,$mrgx,$mxpth,$eng_sources;
	$kword=preg_replace('/[^а-яА-Яa-zA-Z]/isu',' ',$kword);
	$kword=preg_replace('/[ ]+/isu',' ',$kword);
	$kword=explode(' ',$kword);
	foreach ($kword as $item)
	{
		$item=mb_strtolower($item,"UTF-8");
		if (!in_array($item,$kww))
		{
			$kww[]=$item;
		}
	}
	//sleep(1);
	//do
	{
		$cont=parseUrl($link);
		if ($cont=='')
		{
			$attmp++;
			echo "\n".'continue...'."\n";
		}
	}
	// echo $link."\n";
	// echo $cont;
	//while (($cont=='') && ($attmp<3));
	preg_match_all('/charset=([-a-z0-9_]+)/is',$cont,$charset);
	//print_r($charset);
	$hn=parse_url($link);
	$hn=$hn['host'];
	$ahn=explode('.',$hn);
	$hn = $ahn[count($ahn)-2].'.'.$ahn[count($ahn)-1];

	if (($hn!='twitter.com')&&($hn!='livejournal.com'))
	{
		if (($charset[1][0]!='') || ($charset[1][0]!='utf-8'))
		{
			if (mb_strtolower($charset[1][0],'UTF-8')!="utf-8")
			{	
				$cont=iconv($charset[1][0], "utf-8", $cont);
			}
		}
	}
	if ($hn=='instagram.com') $ret_ft=instagram($link,$cont);
	$cont=preg_replace('/(<script[^<]*?>.*?<\/script>)/isu',' ',$cont);
	$cont=preg_replace('/<style[^<]*?type=[\"\']text\/css[\"\']>.*?<\/style>/isu',' ',$cont);

	if ($hn=='twitter.com') $ret_ft=twitter($cont);
	if ($hn=='mail.ru') $ret_ft=mailru($link,$cont);
	if (($hn=='vk.com')||($hn=='vkontakte.ru')) $ret_ft=vk($link,$cont);
	if ($hn=='blogspot.com') $ret_ft=blogspot($link);
	if ($hn=='ya.ru') $ret_ft=ya($cont);
	if ($hn=='liveinternet.ru') $ret_ft=liveinternet($link,$cont);
	if ($hn=='baby.ru') $ret_ft=baby($link,$cont);
	if ($hn=='diary.ru') $ret_ft=diary($link,$cont);
	if ($hn=='e1.ru') $ret_ft=e1($link,$cont);
	if ($hn=='blog.ru') $ret_ft=blog($link,$cont);
	if ($hn=='babyblog.ru') $ret_ft=babyblog($link,$cont);
	if ($hn=='molotok.ru') $ret_ft=molotok($link,$cont);
	if ($hn=='rutwit.ru') $ret_ft=rutwit($cont);
	if ($hn=='mamba.ru') $ret_ft=mamba($cont);
	if ($hn=='foursquare.com') $ret_ft=foursquare($cont);
	if ($hn=='materinstvo.ru') $ret_ft=materinstvo($link,$cont);
	if ($hn=='livejournal.com') $ret_ft=livejournal($link,$cont);
	if ($hn=='banki.ru') $ret_ft=get_banki($link,$cont);
	
	if (($eng_sources[$hn]==1)&&($valeng==1))
	{
		if ($hn=='twitter.com') $ret_eg=get_retweets($link,$cont);
		if ($hn=='livejournal.com') $ret_eg=get_comments($link,$cont);
		if (($hn=='vk.com')||($hn=='vkontakte.ru')) $ret_eg=get_vk($link,$cont);
		if ($hn=='facebook.com') $ret_eg=get_likes($link);
	}
	$ret_outmas['engage']=$ret_eg;

	if ($ret_ft!='')
	{
		$ret_ft=preg_replace('/[\s\t]+/isu', ' ', $ret_ft);
		$ret_outmas['fulltext']=stripslashes(preg_replace('/<[^<]*?>/isu',' ',$ret_ft));
		return $ret_outmas;
	}

	$regex='/<a[^<]*?rel=[\'\"](?<rel>[^<]*?)[\'\"][^<]*?>/is';
	preg_match_all($regex,$cont,$out);
	foreach ($out['rel'] as $key => $item)
	{
		$mrel[$item]++;
	}
	ksort($mrel);
	foreach ($mrel as $key => $item)
	{
		if ($item>5) $cont=preg_replace('/<a[^<]*?rel=[\'\"]'.$key.'[\'\"][^<]*?>.*?<\/a>/is','',$cont);
	}
	$regex='/<a[^<]*?class=[\'\"](?<class>[^<]*?)[\'\"][^<]*?>/is';
	preg_match_all($regex,$cont,$out);
	foreach ($out['class'] as $key => $item)
	{
		$mclass[$item]++;
	}
	//print_r($mclass);
	ksort($mclass);
	foreach ($mclass as $key => $item)
	{
		if ($item>15) $cont=preg_replace('/<a[^<]*?class=[\'\"]'.$key.'[\'\"][^<]*?>.*?<\/a>/is','',$cont);
	}
	$regex='/<a[^<]*?id=[\'\"](?<id>[^<]*?)[\'\"][^<]*?>/is';
	preg_match_all($regex,$cont,$out);
	foreach ($out['id'] as $key => $item)
	{
		$mid[$item]++;
	}
	foreach ($mid as $key => $item)
	{
		if ($item>5) $cont=preg_replace('/<a[^<]*?class=[\'\"]'.$key.'[\'\"][^<]*?>.*?<\/a>/is','',$cont);
	}
	ksort($mid);
	//echo $cont;
	$cont=strip_tags($cont,'<body><div><li>');
	//echo $cont;
	$cont=preg_replace('/&nbsp;/is',' ',$cont);
	//$cont=preg_replace('/[^a-zA-ZА-Яа-я<>;:\\/,\.0-9\-\!\+\?\&\=\"\_ё\(\)]/isu',' ',$cont);
	$cont=preg_replace('/\s+/is',' ',$cont);
	$cont=preg_replace('/[ ]+/is',' ',$cont);
	//$cont=mb_strtolower($cont,"UTF-8");
	unset($outmas);
	$cont=preg_replace('/<[^\<]*?>/isu', '|||', $cont);
	$mcont=explode('|||', $cont);
	//echo $cont;
	//print_r($kww);
	//echo similar_text('привет маша','привет оля',$proc);
	//echo $proc.'GgGg';
	if ($out['ou'][0]=='')
	{
		foreach ($kww as $item)
		{
			if (($item!='') && ($item!=' ') && (mb_strlen($item,'UTF-8')>2))
			{
				$item2=$word->stem_word($item);
				if ($item2!='')
				{
					$item=$item2;
				}
				foreach ($mcont as $item_mcont)
				{
					if (trim($item_mcont)=='') continue;
					preg_match_all('/(?<ou>.*'.$item.'.*)/isu',$item_mcont,$out);
					foreach ($out['ou'] as $key => $it)
					{
						//echo $it."\n";
						if (!in_array($it,$outmas))
						{
							$outmas[]=$it;
						}
					}
				}
			}
		}
	}
	$i=0;
	//print_r($outmas);
	asort($outmas);
	if (count($outmas)>3)
	{
		for ($i=0;$i<count($outmas);$i++)
		{
			for ($j=$i+1;$j<=count($outmas);$j++)
			{
				if ((mb_strlen($outmas[$i],'UTF-8')<200) && (mb_strlen($outmas[$j],'UTF-8')<200))
				{
					similar_text($outmas[$i],$outmas[$j],$proc);
					if ($proc>80)
					{
						$del[$i]=1;
						$del[$j]=1;
					}
				}
			}
		}
	}
	$i=0;
	foreach ($outmas as $key => $frase)
	{
		if (!isset($del[$key]))
		{
			$i++;
			// $ft.=$i.')<br>'.$frase."<br>";
			$ft.=' '.$frase."<br>";
		}
		else
		{
			//$ft.='УДАЛЕНО!!!!:'.$frase.'!!!!';
		}
	}

	$ret_outmas['fulltext']=stripslashes(preg_replace('/<[^<]*?>/isu',' ',$ft));
	return $ret_outmas;
}
//echo GetFullPost('https://twitter.com/V_1_n_T/statuses/245258290634846209',$row['post_content'],$rr['order_keyword']);
//die();
//echo parseUrl('http://kp.ru/daily/25656/819683/');
while (1)
{
	if (intval($ccf) % 200==0) $mproxy=json_decode($redis->get('proxy_list'));
	echo 1;

	$crow=$redis->sPop('prev_queue'.$retro);
	// echo '|'.$crow.'|';
	if ((trim($crow)=='') || (trim($crow)=='{"post_ful_com":"null"}')) 
	{
		echo '->';
		sleep(20);
		continue;
	}
	$row=json_decode($crow,true);
	print_r($row);

	$ful_p=GetFullPost($row['post_link'],$row['post_content'],$row['order_keyword'],($eng_sources[$row['post_host']]==1?1:0));
	print_r($ful_p);
	$qft='';
	$qet='';
	$zap='';
	if (trim($row['post_ful_com'])=='')
	{
		if (trim($ful_p['fulltext'])=='') $row['post_ful_com']='null';
		else $row['post_ful_com']=$ful_p['fulltext'];
	}
	if (($eng_sources[$row['post_host']]==1)&&($row['post_engage']==-1))
	{
		$row['post_engage']=intval($ful_p['engage']['count']);
		$row['post_advengage']=json_encode($ful_p['engage']['data']);
	}
	if (intval($ful_p['engage']['count'])!=0 && $row['post_engage']==0)
	{
		$row['post_engage']=intval($ful_p['engage']['count']);
		$row['post_advengage']=json_encode($ful_p['engage']['data']);
	}
	if (is_array($row['post_advengage'])) $row['post_advengage']=json_encode($row['post_advengage']);

	$ccf++;
	$ful_p=iconv("UTF-8", "UTF-8//IGNORE", $ful_p);
	print_r($row);
	echo "\n".'--------'."\n";
	$redis->sAdd('transf_queue'.$retro,json_encode($row));
	// sleep($fulljob_session_timeout);
}

?>
