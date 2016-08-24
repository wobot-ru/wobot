<?
require_once('/var/www/new/com/config.php');
require_once('/var/www/new/com/func.php');
require_once('/var/www/new/com/db.php');
require_once('/var/www/new/bot/kernel.php');
require_once('auth.php');
require_once('/var/www/com/loc.php');
require_once('/var/www/com/sent.php');
require_once('/var/www/new/com/porter.php');
require_once( '/var/www/new/com/phpmorphy/src/common.php');

$dir = '/var/www/new/com/phpmorphy/dicts';
$lang = 'ru_RU';
$opts = array( 'storage' => PHPMORPHY_STORAGE_FILE );
$morphy = new phpMorphy($dir, $lang, $opts);
error_reporting(0);

date_default_timezone_set ( 'Europe/Moscow' );
ini_set("memory_limit", "2048M");
$db = new database();
$db->connect();

$redis=new Redis() or die("Can'f load redis module.");
$redis->connect('127.0.0.1');

$memcache = memcache_connect('localhost', 11211);

//print_r($_SESSION);
$word_stem=new Lingua_Stem_Ru();
//$msg=$word_stem->stem_word('бдбд');
//echo $msg;
//die();
//print_r($_POST);
//$_POST=$_GET;
auth();

//echo $loged;
//if (!$loged) die();
if ((!$loged) && ($user['tariff_id']==3)) die();
set_log('comment',$_POST);

$output_comment=$memcache->get('comment_'.md5(json_encode($_POST)));
if ($output_comment!='')
{
	echo $output_comment;
	$memcache->delete('comment_'.md5(json_encode($_POST)));
	die();
}

header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); 
header("Cache-Control: no-store, no-cache, must-revalidate"); 
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

//session_destroy();
//print_r($_SESSION);
if ($user['tariff_id']==3)
{
	$user['user_id']=61;
}
if ($_POST['perpage']=='null')
{
	$_POST['perpage']=10;
}
if (isset($_SESSION[$_POST['md5']]))
{
	foreach ($_POST as $key => $item)
	{
		//echo substr($key, 0, 4).' ';
		if ((substr($key, 0, 4)=='res_'))
		{
			$resorrr[]=str_replace("_",".",substr($key,4));
		}
		if (substr($key, 0, 4)=='cou_')
		{
			foreach ($wobot['destn3'] as $kdest => $idest)
			{
				if (str_replace('_',' ',substr($key,4))==$idest)
				$loc[]=$kdest;
			}
		}
		if (($key=='location') && ($item!='')) $loc=explode(',', $item);
		if (($key=='cou') && ($item!=''))
		{
			$mcou=explode(',', $item);
			foreach ($mcou as $kmcou => $imcou)
			{
				foreach ($wobot['destn3'] as $kdest => $idest)
				{
					if ($imcou==$idest)	$loc[]=$kdest;
				}
			}
		}
		if (($key=='res') && ($item!='')) $resorrr=explode(',', $item);
		if (($key=='shres') && ($item!='')) $short_resorrr=explode(',', $item);
		if ((substr($key, 0, 4)=='loc_'))
		{
			if (isset($wobot['destn2'][str_replace('_',' ',substr($key, 4))]))
			{
				$loc[]=str_replace('_',' ',substr($key,4));
			}
			if (substr($key, 4)=='не_определено')
			{
				$loc[]='na';
			}
		}
		if ((substr($key, 0, 5)=='tags_'))
		{
			$tags[]=str_replace("_",".",substr($key,5));
		}
		if ((substr($key, 0, 5)=='word_'))
		{
			$word[]=str_replace("_",".",substr($key,5));
		}
		if ((substr($key, 0, 6)=='speak_') && (substr($key,7,11)!='link'))
		{
			$speak[str_replace("_",".",substr($key,6))]=$_POST['speak_link_'.str_replace("_",".",substr($key,6))];
			$speakid[str_replace("_",".",substr($key,6))]=1;//$_POST['speak_link_'.str_replace("_",".",substr($key,6))];
		}
		if ((substr($key, 0, 5)=='prom_') && (substr($key,6,10)!='link'))
		{
			$prom[str_replace("_",".",substr($key,5))]=$_POST['prom_link_'.str_replace("_",".",substr($key,5))];
			$speakid[str_replace("_",".",substr($key,5))]=1;//$_POST['speak_link_'.str_replace("_",".",substr($key,6))];
		}
	}
	//session_destroy();
	//print_r($_SESSION);
	$order_info=$db->query('SELECT order_id,order_name,order_keyword,order_start,order_end,order_last,third_sources FROM blog_orders WHERE order_id='.intval($_POST['order_id']).' LIMIT 1');
	$order=$db->fetch($order_info);
	$orderkw=preg_replace('/[^а-яА-Яa-zA-Z0-9]/isu',' ',$order['order_keyword']);
	$mkw=explode(' ',$orderkw);
	foreach ($mkw as $item)
	{
		if (mb_strlen($word_stem->stem_word($item),'UTF-8')>=3)
		{
			$yet[$word_stem->stem_word($item)]=1;
		}
	}
	//$db->query('INSERT INTO azure_rss (rss_link) VALUES (\''.addslashes(json_encode($yet)).'\')');
	//print_r($yet);
	$metrics=json_decode($order['order_metrics'],true);
	$tags_info=$db->query('SELECT * FROM blog_tag WHERE order_id='.intval($_POST['order_id']));
	while ($tg=$db->fetch($tags_info))
	{
		$d_tags[$tg['tag_tag']]=$tg['tag_name'];
		$d_astags[$tg['tag_name']]=$tg['tag_tag'];
	}
	//print_R($_SESSION);
	$posts=$db->query($_SESSION[$_POST['md5']].' LIMIT '.((intval($_POST['page']))*($_POST['perpage'])).','.$_POST['perpage']);
	$output_cp=$memcache->get('countcp_'.$_POST['md5']);
	if (is_numeric($output_cp))
	{
		$cpp=$output_cp;
		$mas['md5_count_post']=$cpp;
	}
	else
	{
		$cpp=$db->query(preg_replace('/(SELECT \*)/is','SELECT p.post_id ',$_SESSION[$_POST['md5']]));
		$mas['md5_count_post']=$db->num_rows($cpp);	
	}
	if ($db->num_rows($posts)==0)
	{
		if (($db->num_rows($cpp) % $_POST['perpage'])==0)
		{
			$_POST['page']=intval($db->num_rows($cpp)/$_POST['perpage'])-1;
		}
		else
		{
			$_POST['page']=intval($db->num_rows($cpp)/$_POST['perpage']);	
		}
		$posts=$db->query($_SESSION[$_POST['md5']].' LIMIT '.((intval($_POST['page']))*($_POST['perpage'])).','.$_POST['perpage']);
	}
	$mas['page']=$_POST['page'];
	$mas['md5']=$_POST['md5'];
	$mas['md5_count_src']=$_SESSION['count_src_'.$_POST['md5']];
	$i=1;
	$iw=0;
	while ($post=$db->fetch($posts))
	{
		if ($post['post_id']=='') $post['post_id']=$post['ful_com_post_id'];
		$post['ful_com_post']=preg_replace('/<[^<]*?>/isu', ' ', $post['ful_com_post']);
		$post['post_content']=preg_replace('/<[^<]*?>/isu', ' ', $post['post_content']);
		$mas[$i]['id']=$post['post_id'];
		$parts=explode("\n",html_entity_decode(strip_tags($post['post_content']),ENT_QUOTES,'UTF-8'));
		$mas[$i]['post']=stripslashes(strip_tags(html_entity_decode(preg_replace('/\s+/is',' ',$parts[0]!=''?$parts[0]:($parts[1]!=''?$parts[1]:strip_tags($post['post_content']))),ENT_QUOTES,'UTF-8')));//preg_replace('/\s+/is',' ',strip_tags($post['post_content']));
		foreach($yet as $key => $item)
		{
			//echo '/[\s\t\"\'\?\:]('.$key.'.*?)[\s\t\"\'\?\:]/isu ';
			if (trim($key)!='')
			{
				$mas[$i]['post']=preg_replace('/([\s\t\"\'\?\:\_\“\”\.\,\!\-\=\+\/\[\]\{\}\«\»\(\)\#\$\%\^\&\@])('.$key.'[^\s\t\"\'\?\:\“\”\.\,\_\!\-\=\+\/\[\]\{\}\«\»\(\)\#\$\%\^\&\@]{0,3})([\s\t\"\'\?\:\“\”\.\,\!\-\=\+\/\[\]\{\}\«\»\(\)\#\$\%\^\&\@\_])/isu','$1<span class="kwrd">$2</span>$3',' '.$mas[$i]['post'].' ');	
			}
		}
		if (trim($mas[$i]['post'])=='')
		{
			$mas[$i]['post']=$post['post_link'];
		}
		$mas[$i]['title']=preg_replace('/\s+/is',' ',strip_tags($post['post_content']));
		if ((intval(date('H',$post['post_time']))>0)||(intval(date('i',$post['post_time']))>0)) $stime=date("H:i:s d.m.Y",($post['post_time']-3600));
		else $stime=date("d.m.Y",$post['post_time']);
		$mas[$i]['time']=$stime;
		$mas[$i]['url']=$post['post_link'];
		if ($post['blog_link']=='vkontakte.ru')
		{
			if ($post['blog_login'][0]=='-')
			{
				$mas[$i]['auth_url']='http://vk.com/club'.substr($post['blog_login'],1);
			}
			else
			{
				$mas[$i]['auth_url']='http://vk.com/id'.$post['blog_login'];
			}
		}
		elseif ($post['blog_link']=='facebook.com')
		{
			$mas[$i]['auth_url']='http://facebook.com/'.$post['blog_login'];			
		}
		elseif ($post['blog_link']=='twitter.com')
		{
			$mas[$i]['auth_url']='http://twitter.com/'.$post['blog_login'];			
		}
		elseif ($post['blog_link']=='livejournal.com')
		{
			$mas[$i]['auth_url']='http://'.$post['blog_login'].'.livejournal.com';			
		}
		elseif (preg_match('/mail\.ru/isu',$post['blog_link']))
		{
			$mas[$i]['auth_url']='http://blogs.'.$post['blog_link'].'/'.$post['blog_login'];			
		}
		elseif ($post['blog_link']=='liveinternet.ru')
		{
			$mas[$i]['auth_url']='http://www.liveinternet.ru/users/'.$post['blog_login'];			
		}
		elseif ($post['blog_link']=='ya.ru')
		{
			$mas[$i]['auth_url']='http://'.$post['blog_login'].'.ya.ru';			
		}
		elseif ($post['blog_link']=='yandex.ru')
		{
			$mas[$i]['auth_url']='http://'.$post['blog_login'].'.ya.ru';			
		}
		elseif ($post['blog_link']=='rutwit.ru')
		{
			$mas[$i]['auth_url']='http://rutwit.ru/'.$post['blog_login'];			
		}
		elseif ($post['blog_link']=='rutvit.ru')
		{
			$mas[$i]['auth_url']='http://rutwit.ru/'.$post['blog_login'];			
		}
		elseif ($post['blog_link']=='babyblog.ru')
		{
			$mas[$i]['auth_url']='http://www.babyblog.ru/user/info/'.$post['blog_login'];			
		}
		elseif ($post['blog_link']=='blog.ru')
		{
			$mas[$i]['auth_url']='http://'.$post['blog_login'].'.blog.ru/profile';			
		}
		elseif ($post['blog_link']=='foursquare.com')
		{
			$mas[$i]['auth_url']='https://ru.foursquare.com/'.$post['blog_login'];			
		}
		elseif ($post['blog_link']=='kp.ru')
		{
			$mas[$i]['auth_url']='http://blog.kp.ru/users/'.$post['blog_login'].'/profile/';			
		}
		elseif ($post['blog_link']=='aif.ru')
		{
			$mas[$i]['auth_url']='http://blog.aif.ru/users/'.$post['blog_login'].'/profile';			
		}
		elseif ($post['blog_link']=='friendfeed.com')
		{
			$mas[$i]['auth_url']='http://friendfeed.com/'.$post['blog_login'];			
		}
		elseif ($post['blog_link']=='plus.google.com')
		{
			$mas[$i]['auth_url']='https://plus.google.com/'.$post['blog_login'].'/about';			
		}
		$hn=parse_url($post['post_link']);
	    $hn=$hn['host'];
	    $ahn=explode('.',$hn);
	    $hn = $ahn[count($ahn)-2].'.'.$ahn[count($ahn)-1];
		$hh = $ahn[count($ahn)-2];
		$mas[$i]['host']=$hh;
		$mas[$i]['img_url']=(!file_exists('/var/www/production/img/social/'.$hh.'.png'))?'./img/social/wobot_logo.gif':'./img/social/'.$hh.'.png';
		$mas[$i]['host_name']=$hn;
		$mas[$i]['nick']=html_entity_decode($post['blog_nick']);
		$mas[$i]['count_user']=$metrics['speakers'][$post['blog_nick']];
		$mas[$i]['nastr']=$post['post_nastr'];
		$mas[$i]['spam']=$post['post_spam'];
		$mas[$i]['fav']=$post['post_fav'];
		$mas[$i]['eng']=$post['post_engage'];
		$mas[$i]['foll']=$post['blog_readers'];
		$mas[$i]['geo']=$wobot['destn1'][$post['blog_location']];
		$mas[$i]['geo_c']=$wobot['destn3'][$wobot['destn1'][$post['blog_location']]];
		$mas[$i]['reaction']['reaction_content']=$post['reaction_content'];
		$mas[$i]['reaction']['reaction_time']=date("H:i:s d.m.Y",$post['reaction_time']);
		$mas[$i]['reaction']['reaction_blog_login']=$post['reaction_blog_login'];
		$mas[$i]['reaction']['reaction_blog_info']=json_decode($post['reaction_blog_info'],true);
		$t_post=explode(',',$post['post_tag']);
		$mas[$i]['tags']=array();
		foreach ($t_post as $item)
		{
			if ($item!='')
			{
				$arr_t_post[$item]=$d_tags[$item];
				$mas[$i]['tags']=$arr_t_post;//array(2=>'Главное',4=>'Офис',6=>'Отчет',7=>'Посмотреть');
			}
		}
		if (count($arr_t_post)!=0)
		{
			$mas[$i]['tags']=$arr_t_post;//array(2=>'Главное',4=>'Офис',6=>'Отчет',7=>'Посмотреть');
		}
		else
		{
			$mas[$i]['tags']=array();
		}
		$mas[$i]['gender']=$post['blog_gender'];
		$mas[$i]['age']=$post['blog_age'];
		$i++;
		unset($arr_t_post);
	}
	//$_SESSION['count_post_'.$_POST['md5']]=$_SESSION['count_post_'.$_POST['md5']];
	//$_SESSION['count_src_'.$_POST['md5']]=$cnt_host;
}
else
{
	foreach ($_POST as $key => $item)
	{
		//echo substr($key, 0, 4).' ';
		if ((substr($key, 0, 4)=='res_'))
		{
			$resorrr[]=str_replace("_",".",substr($key,4));
		}
		if (substr($key, 0, 4)=='cou_')
		{
			foreach ($wobot['destn3'] as $kdest => $idest)
			{
				if (str_replace('_',' ',substr($key,4))==$idest)
				$loc[]=$kdest;
			}
		}
		if (($key=='location') && ($item!='')) $loc=explode(',', $item);
		if (($key=='cou') && ($item!=''))
		{
			$mcou=explode(',', $item);
			foreach ($mcou as $kmcou => $imcou)
			{
				foreach ($wobot['destn3'] as $kdest => $idest)
				{
					if ($imcou==$idest)	$loc[]=$kdest;
				}
			}
		}
		if (($key=='res') && ($item!='')) $resorrr=explode(',', $item);
		if (($key=='shres') && ($item!='')) $short_resorrr=explode(',', $item);
		if ((substr($key, 0, 4)=='loc_'))
		{
			if (isset($wobot['destn2'][str_replace('_',' ',substr($key, 4))]))
			{
				$loc[]=str_replace('_',' ',substr($key,4));
			}
			if (substr($key, 4)=='не_определено')
			{
				$loc[]='na';
			}
		}
		if ((substr($key, 0, 4)=='tag_'))
		{
			//$tags[]=str_replace("_",".",substr($key,5));
			$tags[]=intval(substr($key,4));
			//echo $key;
		}
		if ((mb_substr($key, 0, 5,'UTF-8')=='word_'))
		{
			$addjoin='LEFT JOIN blog_full_com as f ON f.ful_com_post_id=p.post_id';
			$lem_words=$morphy->getAllFormsWithGramInfo(mb_strtoupper(mb_substr($key,5,mb_strlen($key,'UTF-8')-5,'UTF-8'),'UTF-8'), true);
			// $fp = fopen('logquery.txt', 'a');
			// fwrite($fp, mb_strtoupper(mb_substr($key,5,mb_strlen($key,'UTF-8')-5,'UTF-8'),'UTF-8').' '.json_encode($lem_words)."\n");
			// fclose($fp);
			foreach ($lem_words[0]['forms'] as $item_lem_words)
			{
				$word[]=mb_strtolower($item_lem_words,'UTF-8');
			}
			$word[]=mb_substr($key,5,mb_strlen($key,'UTF-8')-5,'UTF-8');
		}
		if ((substr($key, 0, 6)=='speak_') && (substr($key,7,11)!='link'))
		{
			$speak[str_replace("_",".",substr($key,6))]=$_POST['speak_link_'.str_replace("_",".",substr($key,6))];
			$speakid[str_replace("_",".",substr($key,6))]=1;//$_POST['speak_link_'.str_replace("_",".",substr($key,6))];
		}
		if ((substr($key, 0, 5)=='prom_') && (substr($key,6,10)!='link'))
		{
			$prom[str_replace("_",".",substr($key,5))]=$_POST['prom_link_'.str_replace("_",".",substr($key,5))];
			$speakid[str_replace("_",".",substr($key,5))]=1;//$_POST['speak_link_'.str_replace("_",".",substr($key,6))];
		}
	}
	// $addjoin='LEFT JOIN blog_full_com as f ON f.ful_com_post_id=p.post_id';
	//print_r($word);
	//print_r($loc);
	$order_info=$db->query('SELECT * FROM blog_orders WHERE order_id='.intval($_POST['order_id']).' LIMIT 1');
	$order=$db->fetch($order_info);
	$settings=json_decode($order['order_settings'],true);
	$orderkw=preg_replace('/[^а-яА-Яa-zA-Z0-9]/isu',' ',$order['order_keyword']);
	$mkw=explode(' ',$orderkw);
	foreach ($mkw as $item)
	{
		if (mb_strlen($word_stem->stem_word($item),'UTF-8')>=3)
		{
			$yet[$word_stem->stem_word($item)]=1;
		}
	}
	$metrics=json_decode($order['order_metrics']);
	$tags_info=$db->query('SELECT * FROM blog_tag WHERE order_id='.intval($_POST['order_id']));
	while ($tg=$db->fetch($tags_info))
	{
		$d_tags[$tg['tag_tag']]=$tg['tag_name'];
		$d_astags[$tg['tag_name']]=$tg['tag_tag'];
	}
	$qw='SELECT * FROM blog_post as p LEFT JOIN robot_blogs2 as b ON p.blog_id=b.blog_id '.$addjoin.' WHERE p.order_id='.$_POST['order_id'].' '.($addjoin==''?'':'AND f.ful_com_order_id='.$_POST['order_id']).' AND post_time>='.strtotime($_POST['stime']).' AND post_time<'.(mktime(0,0,0,date('n',strtotime($_POST['etime'])),date('j',strtotime($_POST['etime']))+1,date('Y',strtotime($_POST['etime'])))).' ';
	if ($_POST['positive']=='true')
	{
		$mton[]=1;
		//$qw.='AND (p.post_nastr=1';
	}
	if ($_POST['negative']=='true')
	{
		$mton[]=-1;		
		//$qw.=' OR p.post_nastr=-1';
	}
	if ($_POST['neutral']=='true')
	{
		$mton[]=0;		
		//$qw.=' OR p.post_nastr=0)';
	}
	//print_r($mton);
	$or='';
	if (count($mton)!=0)
	{
		$qw.='AND (';
		foreach ($mton as $item)
		{
			$qw.=$or.'p.post_nastr='.$item;
			$or=' or ';
		}
		$qw.=')';
	}
	if (strlen($wh1)) $wh.='('.$wh1.')';
	$or='';
	if (!isset($_POST['hosts'])) $_POST['hosts']='selected';
	if (!isset($_POST['locations'])) $_POST['locations']='selected';
	if (((count($resorrr)!=0) || (count($short_resorrr)!=0)) && ($_POST['hosts']=='selected'))
	{
		$or='';
		$wh1=' AND (';
		foreach ($resorrr as $item)
		{
			if (trim($item)=='') continue;
			$wh1.=$or.' p.post_host=\''.$item.'\'';
			$or=' OR ';
		} 
		foreach ($short_resorrr as $item)
		{
			if (trim($item)=='') continue;
			$wh1.=$or.' p.post_host LIKE \''.$item.'%\'';
			$or=' OR ';
		} 
		$wh1.=')';
		$qw.=$wh1;
	}
	elseif (((count($resorrr)!=0) || (count($short_resorrr)!=0)) && ($_POST['hosts']=='except'))
	{
		$or='';
		$wh1=' AND (';
		foreach ($resorrr as $item)
		{
			if (trim($item)=='') continue;
			$wh1.=$or.' p.post_host!=\''.$item.'\'';
			$or=' AND ';
		} 
		foreach ($short_resorrr as $item)
		{
			if (trim($item)=='') continue;
			$wh1.=$or.' p.post_host NOT LIKE \''.$item.'%\'';
			$or=' AND ';
		} 
		$wh1.=')';
		$qw.=$wh1;
	}
	if ((count($loc)!=0) && ($_POST['locations']=='selected'))
	{
		$or='';
		$wh1=' AND (';
		foreach ($loc as $item)
		{
			if (trim($item)=='') continue;
			if ($item=='na')
			{
				$wh1.=$or.' b.blog_location=\'\'';
				$or=' OR ';
			}
			else
			{
				if (isset($wobot['destn2'][$item]))
				{
					$wh1.=$or.' b.blog_location=\''.$wobot['destn2'][$item].'\'';
					$or=' OR ';
				}
			}
		} 
		$wh1.=')';
		$qw.=$wh1;
	}
	elseif ((count($loc)!=0) && ($_POST['locations']=='except'))
	{
		$or='';
		$wh1=' AND (';
		foreach ($loc as $item)
		{
			if (trim($item)=='') continue;
			if ($item=='na')
			{
				$wh1.=$or.' b.blog_location!=\'\'';
				$or=' AND ';
			}
			else
			{
				if (isset($wobot['destn2'][$item]))
				{
					$wh1.=$or.' b.blog_location!=\''.$wobot['destn2'][$item].'\'';
					$or=' AND ';
				}
			}
		} 
		$wh1.=')';
		$qw.=$wh1;
	}
	$or='';
	if (count($tags)!=0)
	{
		if ($_POST['tags']!='all')
		{
			$wh1.=' AND (';
			foreach ($tags as $item)
			{	if ($_POST['tags']=='selected')
				{
					$wh1.=$or.'(FIND_IN_SET(\''.$item.'\',post_tag)>0)';
					$or=' OR ';
				}
				else
				if ($_POST['tags']=='except')
				{
					$wh1.=$or.'(FIND_IN_SET(\''.$item.'\',post_tag)=0)';
					$or=' AND ';
				}
				//if ($item!='без_тегов')
				//{
					//$wh.=$or.'(FIND_IN_SET(\''.$d_astags[$item].'\',post_tag)>0)';
				//}
				//else
				/*{
					$wh.=$or.'(post_tag = \'\')';
					$or=' OR ';
				}*/
			}
			$wh1.=')';
			$qw.=$wh1;
		}
	}
	if ($_POST['post_type']=='null')
	{
		if ($settings['remove_spam']==1) 
		{
			$_POST['post_type']='nospam';
			$mas['remove_spam']=1;
		}
		else
		{
			$mas['remove_spam']=0;
		}
	}
	switch ($_POST['post_type']) {
	    case 'fav':
	        $qw.=' AND (p.post_fav=1)';
	        break;
	    case 'nospam':
        	$qw.=' AND (p.post_spam!=1)';
	        break;
	    case 'spam':
        	$qw.=' AND (p.post_spam=1)';
	        break;
	}
	/*$or='';
	if (count($speak)!=0)
	{
		$qw.=' AND (';
		foreach ($speak as $key => $item)
		{
			if ($_POST['Speakers']=='selected')
			{
				$qw.=$or.'(b.blog_nick=\''.$key.'\' AND b.blog_link=\''.$item.'\')';
				$or=' OR ';
			}
			else
			if ($_POST['Speakers']=='except')
			{
				$qw.=$or.'(b.blog_nick!=\''.$key.'\' AND b.blog_link!=\''.$item.'\')';
				$or=' AND ';
			}
		}
		$qw.=')';
	}*/
	$or='';
	if ((count($speakid)!=0) && ($_POST['Promotions']!='all'))
	{
		$qw.=' AND (';
		foreach ($speakid as $key => $item)
		{
			if ($key!=0)
			{
				if ($_POST['Promotions']=='selected')
				{
					//$qw.=$or.'(b.blog_nick=\''.$key.'\' AND b.blog_link=\''.$item.'\')';
					$qw.=$or.'(b.blog_id=\''.$key.'\')';
					$or=' OR ';
				}
				else
				if ($_POST['Promotions']=='except')
				{
					$qw.=$or.'(IFNULL(b.blog_id,0)!=\''.$key.'\')';
					$or=' AND ';
				}
			}
		}
		$qw.=')';
	}
	$or='';
	if ((count($word)!=0) && ($_POST['words']!='all'))
	{
		$qw.=' AND (';
		foreach ($word as $key => $item)
		{
			if ($_POST['words']=='selected')
			{
				//$qw.=$or.'(b.blog_nick=\''.$key.'\' AND b.blog_link=\''.$item.'\')';
				$qw.=$or.'(LOWER(p.post_content) REGEXP "[ ,\.\!\?\{\}\'\"\\\|\>\<\(\)\*\&\^\%\$\#\@\=\+\-\_\n\«\»\№\±\§\/\;\:\←\€\£\¥\©\®\™\±\≠\≤\≥\÷\×\ј\џ\ќ\®\†\њ\ѓ\ѕ\ў\‘\“\ƒ\ћ\÷\©\}\°\љ\∆\…\э\ё\ђ\≈\≠\µ\и\™\~\≤\≤\≤\≥\“\”\”\’0-9]'.$item.'[ ,\.\!\?\{\}\'\"\\\|\>\<\(\)\*\&\^\%\$\#\@\=\+\-\_\n\«\»\№\±\§\/\;\:\←\€\£\¥\©\®\™\±\≠\≤\≥\÷\×\ј\џ\ќ\®\†\њ\ѓ\ѕ\ў\‘\“\ƒ\ћ\÷\©\}\°\љ\∆\…\э\ё\ђ\≈\≠\µ\и\™\~\≤\≤\≤\≥\“\”\”\’0-9]" OR p.post_content LIKE "'.$item.'%" OR p.post_content LIKE "%'.$item.'")';
				$qw.=' OR (LOWER(f.ful_com_post) REGEXP "[ ,\.\!\?\{\}\'\"\\\|\>\<\(\)\*\&\^\%\$\#\@\=\+\-\_\n\«\»\№\±\§\/\;\:\←\€\£\¥\©\®\™\±\≠\≤\≥\÷\×\ј\џ\ќ\®\†\њ\ѓ\ѕ\ў\‘\“\ƒ\ћ\÷\©\}\°\љ\∆\…\э\ё\ђ\≈\≠\µ\и\™\~\≤\≤\≤\≥\“\”\”\’0-9]'.$item.'[ ,\.\!\?\{\}\'\"\\\|\>\<\(\)\*\&\^\%\$\#\@\=\+\-\_\n\«\»\№\±\§\/\;\:\←\€\£\¥\©\®\™\±\≠\≤\≥\÷\×\ј\џ\ќ\®\†\њ\ѓ\ѕ\ў\‘\“\ƒ\ћ\÷\©\}\°\љ\∆\…\э\ё\ђ\≈\≠\µ\и\™\~\≤\≤\≤\≥\“\”\”\’0-9]" OR f.ful_com_post LIKE "'.$item.'%" OR f.ful_com_post LIKE "%'.$item.'")';
				$or=' OR ';
			}
			else
			if ($_POST['words']=='except')
			{
				$qw.=$or.'(LOWER(p.post_content) NOT REGEXP "[ ,\.\!\?\{\}\'\"\\\|\>\<\(\)\*\&\^\%\$\#\@\=\+\-\_\n\«\»\№\±\§\/\;\:\←\€\£\¥\©\®\™\±\≠\≤\≥\÷\×\ј\џ\ќ\®\†\њ\ѓ\ѕ\ў\‘\“\ƒ\ћ\÷\©\}\°\љ\∆\…\э\ё\ђ\≈\≠\µ\и\™\~\≤\≤\≤\≥\“\”\”\’0-9]'.$item.'[ ,\.\!\?\{\}\'\"\\\|\>\<\(\)\*\&\^\%\$\#\@\=\+\-\_\n\«\»\№\±\§\/\;\:\←\€\£\¥\©\®\™\±\≠\≤\≥\÷\×\ј\џ\ќ\®\†\њ\ѓ\ѕ\ў\‘\“\ƒ\ћ\÷\©\}\°\љ\∆\…\э\ё\ђ\≈\≠\µ\и\™\~\≤\≤\≤\≥\“\”\”\’0-9]" AND p.post_content NOT LIKE "'.$item.'%" AND p.post_content NOT LIKE "%'.$item.'")';
				$qw.=' AND (LOWER(f.ful_com_post) NOT REGEXP "[ ,\.\!\?\{\}\'\"\\\|\>\<\(\)\*\&\^\%\$\#\@\=\+\-\_\n\«\»\№\±\§\/\;\:\←\€\£\¥\©\®\™\±\≠\≤\≥\÷\×\ј\џ\ќ\®\†\њ\ѓ\ѕ\ў\‘\“\ƒ\ћ\÷\©\}\°\љ\∆\…\э\ё\ђ\≈\≠\µ\и\™\~\≤\≤\≤\≥\“\”\”\’0-9]'.$item.'[ ,\.\!\?\{\}\'\"\\\|\>\<\(\)\*\&\^\%\$\#\@\=\+\-\_\n\«\»\№\±\§\/\;\:\←\€\£\¥\©\®\™\±\≠\≤\≥\÷\×\ј\џ\ќ\®\†\њ\ѓ\ѕ\ў\‘\“\ƒ\ћ\÷\©\}\°\љ\∆\…\э\ё\ђ\≈\≠\µ\и\™\~\≤\≤\≤\≥\“\”\”\’0-9]" AND f.ful_com_post NOT LIKE "'.$item.'%" AND f.ful_com_post NOT LIKE "%'.$item.'")';
				$or=' AND ';
			}
		}
		$qw.=')';
	}
	if ($_POST['gender']=='м')
	{
		$qw.=' AND b.blog_gender=2';
	}
	else
	if ($_POST['gender']=='ж')
	{
		$qw.=' AND b.blog_gender=1';
	}
	if ($_POST['age_min']!=null)
	{
		$qw.=' b.blog_age>'.intval($_POST['age_min']);
	}
	if ($_POST['age_max']!=null)
	{
		$qw.=' b.blog_age<'.intval($_POST['age_max']);
	}
	if ((count($word)!=0) && ($_POST['words']!='all'))
	{
		/*switch ($_POST['sort']) {
		    case 'date':
		        $qw.=' ORDER BY p.post_time DESC';
		        break;
		    case 'audience':
		        $qw.=' ORDER BY b.blog_readers DESC';
		        break;
		    case 'eng':
		        $qw.=' ORDER BY p.post_engage DESC';
		        break;
		    default:
		       $qw.=' ORDER BY p.post_time DESC';
		}*/
		switch ($_POST['sort']) {
		    case 'date':
		        $qw.=' ORDER BY p.post_time DESC LIMIT '.$_POST['perpage'];
		        break;
		    case 'audience':
		        $qw.=' ORDER BY b.blog_readers DESC LIMIT '.$_POST['perpage'];
		        break;
		    case 'eng':
		        $qw.=' ORDER BY p.post_engage DESC LIMIT '.$_POST['perpage'];
		        break;
		    default:
		       $qw.=' ORDER BY p.post_time DESC LIMIT '.$_POST['perpage'];
		}
	}
	else
	{
		switch ($_POST['sort']) {
		    case 'date':
		        $qw.=' ORDER BY p.post_time DESC LIMIT '.$_POST['perpage'];
		        break;
		    case 'audience':
		        $qw.=' ORDER BY b.blog_readers DESC LIMIT '.$_POST['perpage'];
		        break;
		    case 'eng':
		        $qw.=' ORDER BY p.post_engage DESC LIMIT '.$_POST['perpage'];
		        break;
		    default:
		       $qw.=' ORDER BY p.post_time DESC LIMIT '.$_POST['perpage'];
		}
	}
	//$qw.=' ORDER BY p.post_time DESC LIMIT 10';
	//print_r($tags);
	// $fp = fopen('logquery.txt', 'a');
	// fwrite($fp, $qw."\n");
	// fclose($fp);
	$posts=$db->query($qw);
	$i=1;
	// echo $qw;
	// die();
	//print_r($word);
	//die();
	while ($post=$db->fetch($posts))
	{
		if ($post['post_id']=='') $post['post_id']=$post['ful_com_post_id'];
		$post['ful_com_post']=preg_replace('/<[^<]*?>/isu', ' ', $post['ful_com_post']);
		$post['post_content']=preg_replace('/<[^<]*?>/isu', ' ', $post['post_content']);
		//echo $post['post_id'].'|';
		//echo $c.' ';
		$mas[$i]['id']=$post['post_id'];
		$parts=explode("\n",html_entity_decode(strip_tags($post['post_content']),ENT_QUOTES,'UTF-8'));
		$mas[$i]['post']=stripslashes(strip_tags(html_entity_decode(preg_replace('/\s+/is',' ',$parts[0]!=''?$parts[0]:($parts[1]!=''?$parts[1]:strip_tags($post['post_content']))),ENT_QUOTES,'UTF-8')));//preg_replace('/\s+/is',' ',strip_tags($post['post_content']));
		$msent=get_sentence($post['ful_com_post']==''?' '.$post['post_content'].' ':' '.$post['ful_com_post'].' ');
		$mas[$i]['post']=(get_needed_sentence($msent,$order['order_keyword'])==''?$post['post_content']:get_needed_sentence($msent,$order['order_keyword']));	
		if (mb_strlen($mas[$i]['post'])>300) $mas[$i]['post']=mb_substr($mas[$i]['post'], 0, 300).'...';	
		foreach($yet as $key => $item)
		{
			//echo '/[\s\t\"\'\?\:]('.$key.'.*?)[\s\t\"\'\?\:]/isu ';
			if (trim($key)!='')
			{
				$mas[$i]['post']=preg_replace('/([\s\t\"\'\?\:\“\”\.\,\!\-\=\+\/\[\]\{\}\«\»\(\)\#\$\%\^\&\@\_])('.$key.'[^\s\t\"\'\?\:\“\”\.\,\!\-\=\+\/\[\]\{\}\«\»\(\)\#\$\%\^\&\@\_]{0,3})([\s\t\"\'\?\:\“\”\.\,\!\-\=\+\/\[\]\{\}\«\»\(\)\#\$\%\^\&\@\_])/isu','$1<span class="kwrd">$2</span>$3',' '.$mas[$i]['post'].' ');	
			}
		}
		if (trim($mas[$i]['post'])=='')
		{
			$mas[$i]['post']=$post['post_link'];
		}
		//$mas[$i]['post']=preg_replace('/\s+/is',' ',strip_tags($post['post_content']));
		$mas[$i]['title']=preg_replace('/\s+/is',' ',strip_tags($post['post_content']));
		if ((intval(date('H',$post['post_time']))>0)||(intval(date('i',$post['post_time']))>0)) $stime=date("H:i:s d.m.Y",($post['post_time']-3600));
		else $stime=date("d.m.Y",$post['post_time']);
		$mas[$i]['time']=$stime;
		$mas[$i]['url']=$post['post_link'];
		if ($post['blog_link']=='vkontakte.ru')
		{
			if ($post['blog_login'][0]=='-')
			{
				$mas[$i]['auth_url']='http://vk.com/club'.substr($post['blog_login'],1);
			}
			else
			{
				$mas[$i]['auth_url']='http://vk.com/id'.$post['blog_login'];
			}
		}
		elseif ($post['blog_link']=='facebook.com')
		{
			$mas[$i]['auth_url']='http://facebook.com/'.$post['blog_login'];			
		}
		elseif ($post['blog_link']=='twitter.com')
		{
			$mas[$i]['auth_url']='http://twitter.com/'.$post['blog_login'];			
		}
		elseif ($post['blog_link']=='livejournal.com')
		{
			$mas[$i]['auth_url']='http://'.$post['blog_login'].'.livejournal.com';			
		}
		elseif (preg_match('/mail\.ru/isu',$post['blog_link']))
		{
			$mas[$i]['auth_url']='http://blogs.'.$post['blog_link'].'/'.$post['blog_login'];			
		}
		elseif ($post['blog_link']=='liveinternet.ru')
		{
			$mas[$i]['auth_url']='http://www.liveinternet.ru/users/'.$post['blog_login'];			
		}
		elseif ($post['blog_link']=='ya.ru')
		{
			$mas[$i]['auth_url']='http://'.$post['blog_login'].'.ya.ru';			
		}
		elseif ($post['blog_link']=='yandex.ru')
		{
			$mas[$i]['auth_url']='http://'.$post['blog_login'].'.ya.ru';			
		}
		elseif ($post['blog_link']=='rutwit.ru')
		{
			$mas[$i]['auth_url']='http://rutwit.ru/'.$post['blog_login'];			
		}
		elseif ($post['blog_link']=='rutvit.ru')
		{
			$mas[$i]['auth_url']='http://rutwit.ru/'.$post['blog_login'];			
		}
		elseif ($post['blog_link']=='babyblog.ru')
		{
			$mas[$i]['auth_url']='http://www.babyblog.ru/user/info/'.$post['blog_login'];			
		}
		elseif ($post['blog_link']=='blog.ru')
		{
			$mas[$i]['auth_url']='http://'.$post['blog_login'].'.blog.ru/profile';			
		}
		elseif ($post['blog_link']=='foursquare.com')
		{
			$mas[$i]['auth_url']='https://ru.foursquare.com/'.$post['blog_login'];			
		}
		elseif ($post['blog_link']=='kp.ru')
		{
			$mas[$i]['auth_url']='http://blog.kp.ru/users/'.$post['blog_login'].'/profile/';			
		}
		elseif ($post['blog_link']=='aif.ru')
		{
			$mas[$i]['auth_url']='http://blog.aif.ru/users/'.$post['blog_login'].'/profile';			
		}
		elseif ($post['blog_link']=='friendfeed.com')
		{
			$mas[$i]['auth_url']='http://friendfeed.com/'.$post['blog_login'];			
		}
		elseif ($post['blog_link']=='plus.google.com')
		{
			$mas[$i]['auth_url']='https://plus.google.com/'.$post['blog_login'].'/about';			
		}
		$hn=parse_url($post['post_link']);
	    $hn=$hn['host'];
	    $ahn=explode('.',$hn);
	    $hn = $ahn[count($ahn)-2].'.'.$ahn[count($ahn)-1];
		$hh = $ahn[count($ahn)-2];
		$mas[$i]['host']=$hh;
		$mas[$i]['img_url']=(!file_exists('/var/www/production/img/social/'.$hh.'.png'))?'./img/social/wobot_logo.gif':'./img/social/'.$hh.'.png';
		$mas[$i]['host_name']=$hn;
		$mas[$i]['nick']=html_entity_decode(($post['blog_nick']==null)?'':$post['blog_nick']);
		//$mas[$i]['count_user']=$metrics['speakers'][$post['blog_nick']];
		$mas[$i]['nastr']=$post['post_nastr'];
		$mas[$i]['spam']=$post['post_spam'];
		$mas[$i]['eng']=$post['post_engage'];
		$mas[$i]['fav']=$post['post_fav'];
		$mas[$i]['foll']=$post['blog_readers'];
		$mas[$i]['geo']=$wobot['destn1'][$post['blog_location']];
		$mas[$i]['geo_c']=$wobot['destn3'][$wobot['destn1'][$post['blog_location']]];
		$mas[$i]['reaction']['reaction_content']=$post['reaction_content'];
		$mas[$i]['reaction']['reaction_time']=date("H:i:s d.m.Y",$post['reaction_time']);
		$mas[$i]['reaction']['reaction_blog_login']=$post['reaction_blog_login'];
		$mas[$i]['reaction']['reaction_blog_info']=json_decode($post['reaction_blog_info'],true);
		$t_post=explode(',',$post['post_tag']);
		$mas[$i]['tags']=array();
		foreach ($t_post as $item)
		{
			if ($item!='')
			{
				$arr_t_post[$item]=$d_tags[$item];
				$mas[$i]['tags']=$arr_t_post;//array(2=>'Главное',4=>'Офис',6=>'Отчет',7=>'Посмотреть');
			}
		}
		if (count($arr_t_post)!=0)
		{
			$mas[$i]['tags']=$arr_t_post;//array(2=>'Главное',4=>'Офис',6=>'Отчет',7=>'Посмотреть');
		}
		else
		{
			$mas[$i]['tags']=array();
		}
		$mas[$i]['gender']=$post['blog_gender'];
		$mas[$i]['age']=$post['blog_age'];
		$i++;
		unset($arr_t_post);
	}
	//$qw=preg_replace('/SELECT */is','SELECT (*)',$qw);
	$qw=preg_replace('/ LIMIT '.$_POST['perpage'].'/is','',$qw);
	$_SESSION[md5($qw)]=$qw;
	$qw1=$qw;
	$mas['md5']=md5($qw);
	$qw=preg_replace('/(SELECT \*)/is','SELECT p.post_id ',$qw);
	// $qw=preg_replace('/(ORDER BY)/is','GROUP BY post_host ORDER BY',$qw);
	//echo $qw;
	//die();
	// $output_cp=$memcache->get('countcp_'.$mas['md5']);
	// if (is_numeric($output_cp))
	// {
	// 	$mas['page']=0;
	// 	$mas['md5_count_post']=$output_cp;	
	// 	$mas['md5_count_src']=0;
	// 	$_SESSION['count_post_'.md5($qw1)]=$output_cp;
	// 	$_SESSION['count_src_'.md5($qw1)]=$cnt_host;
	// }
	// else
	{
		$countqposts=$db->query($qw);
		// while ($count=$db->fetch($countqposts))
		// {
		// 	$cnt+=$count['cnt'];
		// 	$cnt_host++;
		// }
		$cnt=$db->num_rows($countqposts);
		$mas['page']=0;
		$mas['md5_count_post']=$cnt;	
		$mas['md5_count_src']=$cnt_host;
		$_SESSION['count_post_'.md5($qw1)]=$cnt;
		$_SESSION['count_src_'.md5($qw1)]=$cnt_host;
		$memcache->set('countcp_'.$mas['md5'], intval($cnt), 60);
	}
}
//echo $jjj;
//die();
//$mas['gg']=1;
/*$mas['1']['id']=1231231;
$mas['1']['post']='RT @SU_HSE: RT @SU_HSE: А в других универах сессия только начинается :) http://t.co/36kgMRWD';
$mas['1']['time']='12:12:12 10.01.2012';
$mas['1']['url']='https://twitter.com/#!/PeiteMors/statuses/156997915011854336';
$mas['1']['auth_url']='https://twitter.com/#!/PeiteMors';
$mas['1']['host']='twitter';
$mas['1']['host_name']='twitter.com';
$mas['1']['nick']='PeiteMors';
$mas['1']['nastr']=1;
$mas['1']['spam']=0;
$mas['1']['fav']=0;
$mas['1']['foll']=120;
$mas['1']['geo']='Ростов на дону';
$mas['1']['geo_c']='Россия';
$mas['1']['tags']=array(2=>'Главное',4=>'Офис',6=>'Отчет',7=>'Посмотреть');
$mas['1']['gender']=1;

$mas['2']['id']=1231232;
$mas['2']['post']='Симпозиум «Уголовная политика и бизнес». ВШЭ России. ';
$mas['2']['time']='12:12:12 11.01.2012';
$mas['2']['url']='http://ukraine-2-0.livejournal.com/15270.html';
$mas['2']['auth_url']='http://ukraine-2-0.livejournal.com/';
$mas['2']['host']='livejournal';
$mas['2']['host_name']='livejournal.com';
$mas['2']['nick']='ukraine-2-0';
$mas['2']['nastr']=-1;
$mas['2']['spam']=1;
$mas['2']['fav']=0;
$mas['2']['foll']=21220;
$mas['2']['geo']='Донецк';
$mas['2']['geo_c']='Украина';
$mas['2']['tags']=array(4=>'Офис',6=>'Отчет',7=>'Посмотреть');
$mas['2']['gender']=1;

$mas['3']['id']=1231233;
$mas['3']['post']='Стипендии будут повышены Стипендии будут повышены (www.hse.ru)';
$mas['3']['time']='15.01.2011';
$mas['3']['url']='http://vkontakte.ru/note101771427_11533203';
$mas['3']['auth_url']='http://vkontakte.ru/id101771427';
$mas['3']['host']='vkontakte';
$mas['3']['host_name']='vkontakte.ru';
$mas['3']['nick']='Дмитрий Анохин';
$mas['3']['nastr']=1;
$mas['3']['spam']=0;
$mas['3']['fav']=1;
$mas['3']['foll']=130;
$mas['3']['geo']='Москва';
$mas['3']['geo_c']='Россия';
$mas['3']['tags']=array(7=>'Посмотреть');
$mas['3']['gender']=2;

$mas['4']['id']=1231234;
$mas['4']['post']='С 1 марта стартует очередной набор программы «Комплексный Интернет-маркетинг ...';
$mas['4']['time']='12:15:31 10.01.2012';
$mas['4']['url']='http://www.facebook.com/621493153/posts/184010461640303';
$mas['4']['auth_url']='http://www.facebook.com/dmitry.satin';
$mas['4']['host']='facebook';
$mas['4']['host_name']='facebook.com';
$mas['4']['nick']='Дмитрий Сатин';
$mas['4']['nastr']=1;
$mas['4']['spam']=0;
$mas['4']['fav']=1;
$mas['4']['foll']=130;
$mas['4']['geo']='Москва';
$mas['4']['geo_c']='Россия';
$mas['4']['tags']=array(7=>'Посмотреть');
$mas['4']['gender']=2;

$mas['5']['id']=1231235;
$mas['5']['post']='885 000 руб. Продаётся Range Rover 2004 года,4.4, АКПП Авто в идеальном состоянии и максимальной комплекцации VOGUE HSE для ценителей роскоши и комфорта.';
$mas['5']['time']='21.10.2011';
$mas['5']['url']='http://www.vblizimetro.ru/obyavlenie/prodayotsya-range-rover-2004-goda-4-4/98913278';
$mas['5']['auth_url']='';
$mas['5']['host']='vblizimetro';
$mas['5']['host_name']='vblizimetro.ru';
$mas['5']['nick']='';
$mas['5']['nastr']=0;
$mas['5']['spam']=1;
$mas['5']['fav']=0;
$mas['5']['foll']=0;
$mas['5']['geo']='';
$mas['5']['geo_c']='';
$mas['5']['tags']=array(6=>'Отчет');
$mas['5']['gender']=0;

$mas['6']['id']=1231236;
$mas['6']['post']='RT @SU_HSE: RT @SU_HSE: А в других универах сессия только начинается :) http://t.co/36kgMRWD';
$mas['6']['time']='27.03.2011';
$mas['6']['url']='https://twitter.com/#!/PeiteMors/statuses/156997915011854336';
$mas['6']['auth_url']='https://twitter.com/#!/PeiteMors';
$mas['6']['host']='twitter';
$mas['6']['host_name']='twitter.com';
$mas['6']['nick']='PeiteMors';
$mas['6']['nastr']=1;
$mas['6']['spam']=0;
$mas['6']['fav']=0;
$mas['6']['foll']=120;
$mas['6']['geo']='Ростов на дону';
$mas['6']['geo_c']='Россия';
$mas['6']['tags']=array(2=>'Главное',4=>'Офис',6=>'Отчет',7=>'Посмотреть');
$mas['6']['gender']=1;

$mas['7']['id']=1231237;
$mas['7']['post']='Симпозиум «Уголовная политика и бизнес». ВШЭ России. ';
$mas['7']['time']='12:12:12 19.03.2011';
$mas['7']['url']='http://ukraine-2-0.livejournal.com/15270.html';
$mas['7']['auth_url']='http://ukraine-2-0.livejournal.com/';
$mas['7']['host']='livejournal';
$mas['7']['host_name']='livejournal.com';
$mas['7']['nick']='ukraine-2-0';
$mas['7']['nastr']=-1;
$mas['7']['spam']=1;
$mas['7']['fav']=0;
$mas['7']['foll']=21220;
$mas['7']['geo']='Донецк';
$mas['7']['geo_c']='Украина';
$mas['7']['tags']=array(4=>'Офис',6=>'Отчет',7=>'Посмотреть');
$mas['7']['gender']=1;

$mas['8']['id']=1231238;
$mas['8']['post']='Стипендии будут повышены Стипендии будут повышены (www.hse.ru)';
$mas['8']['time']='00:24:37 21.11.2011';
$mas['8']['url']='http://vkontakte.ru/note101771427_11533203';
$mas['8']['auth_url']='http://vkontakte.ru/id101771427';
$mas['8']['host']='vkontakte';
$mas['8']['host_name']='vkontakte.ru';
$mas['8']['nick']='Дмитрий Анохин';
$mas['8']['nastr']=1;
$mas['8']['spam']=0;
$mas['8']['fav']=1;
$mas['8']['foll']=130;
$mas['8']['geo']='Москва';
$mas['8']['geo_c']='Россия';
$mas['8']['tags']=array(7=>'Посмотреть');
$mas['8']['gender']=2;

$mas['9']['id']=1231239;
$mas['9']['post']='С 1 марта стартует очередной набор программы «Комплексный Интернет-маркетинг ...';
$mas['9']['time']='11:13:32 18.07.2011';
$mas['9']['url']='http://www.facebook.com/621493153/posts/184010461640303';
$mas['9']['auth_url']='http://www.facebook.com/dmitry.satin';
$mas['9']['host']='facebook';
$mas['9']['host_name']='facebook.com';
$mas['9']['nick']='Дмитрий Сатин';
$mas['9']['nastr']=1;
$mas['9']['spam']=0;
$mas['9']['fav']=1;
$mas['9']['foll']=130;
$mas['9']['geo']='Москва';
$mas['9']['geo_c']='Россия';
$mas['9']['tags']=array(7=>'Посмотреть');
$mas['9']['gender']=2;

$mas['10']['id']=1231240;
$mas['10']['post']='885 000 руб. Продаётся Range Rover 2004 года,4.4, АКПП Авто в идеальном состоянии и максимальной комплекцации VOGUE HSE для ценителей роскоши и комфорта.';
$mas['10']['time']='12:12:12 10.09.2011';
$mas['10']['url']='http://www.vblizimetro.ru/obyavlenie/prodayotsya-range-rover-2004-goda-4-4/98913278';
$mas['10']['auth_url']='';
$mas['10']['host']='vblizimetro';
$mas['10']['host_name']='vblizimetro.ru';
$mas['10']['nick']='';
$mas['10']['nastr']=0;
$mas['10']['spam']=1;
$mas['10']['fav']=0;
$mas['10']['foll']=0;
$mas['10']['geo']='';
$mas['10']['geo_c']='';
$mas['10']['tags']=array(6=>'Отчет');
$mas['10']['gender']=0;*/

echo json_encode($mas);

?>