<?
require_once('/var/www/new/com/config.php');
require_once('/var/www/new/com/func.php');
require_once('/var/www/new/com/db.php');
require_once('/var/www/new/bot/kernel.php');
require_once('auth.php');
require_once('/var/www/com/loc.php');
require_once('/var/www/com/sent.php');
require_once('/var/www/new/com/porter.php');

//error_reporting(0);

date_default_timezone_set ( 'Europe/Moscow' );
$db = new database();
$db->connect();

$redis=new Redis() or die("Can'f load redis module.");
$redis->connect('127.0.0.1');
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
// $_POST['perpage']=80;
if (isset($_SESSION[$_POST['md5']]))
{
	foreach ($_POST as $key => $item)
	{
		//echo substr($key, 0, 4).' ';
		if ((substr($key, 0, 4)=='res_'))
		{
			$resorrr[]=str_replace("_",".",substr($key,4));
		}
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
	$cpp=$db->query(preg_replace('/SELECT \*/isu','SELECT post_id',$_SESSION[$_POST['md5']]));
	$posts=$db->query($_SESSION[$_POST['md5']].' LIMIT '.((intval($_POST['page']))*($_POST['perpage'])).','.$_POST['perpage']);
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
	$mas['md5_count_post']=$db->num_rows($cpp);	
	// $mas['md5_count_src']=$_SESSION['count_src_'.$_POST['md5']];
	$i=1;
	$iw=0;
	while ($post=$db->fetch($posts))
	{
		$mas[$i]['id']=$post['post_id'];
		$parts=explode("\n",html_entity_decode(strip_tags($post['post_content']),ENT_QUOTES,'UTF-8'));
		if ($post['post_host']!='twitter.com')
		{
			$msent=get_sentence($post['ful_com_post']==''?' '.$post['post_content'].' ':' '.$post['ful_com_post'].' ');
			$mas[$i]['post']=get_needed_sentence($msent,$order['order_keyword']);
			$mas[$i]['title']=$msent[0];
		}
		else
		{
			// echo 'gg';
			// $mas[$i]['post']=$post['post_content'];
			$mas[$i]['title']=$post['post_content'];
		}
		// $mas[$i]['post']=stripslashes(strip_tags(html_entity_decode(preg_replace('/\s+/is',' ',$parts[0]!=''?$parts[0]:($parts[1]!=''?$parts[1]:strip_tags($post['post_content']))),ENT_QUOTES,'UTF-8')));//preg_replace('/\s+/is',' ',strip_tags($post['post_content']));
		// $mas[$i]['title']=mb_substr(preg_replace('/\s+/is',' ',strip_tags($post['post_content'])),0,140,'UTF-8').'...';
		foreach($yet as $key => $item)
		{
			//echo '/[\s\t\"\'\?\:]('.$key.'.*?)[\s\t\"\'\?\:]/isu ';
			if (trim($key)!='')
			{
				$mas[$i]['post']=preg_replace('/([\s\t\"\'\?\:\_\“\”\.\,\!\-\=\+\/\[\]\{\}\«\»\(\)\#\$\%\^\&\@])('.$key.'[^\s\t\"\'\?\:\“\”\.\,\_\!\-\=\+\/\[\]\{\}\«\»\(\)\#\$\%\^\&\@]{0,3})([\s\t\"\'\?\:\“\”\.\,\!\-\=\+\/\[\]\{\}\«\»\(\)\#\$\%\^\&\@\_])/isu','$1<span class="kwrd">$2</span>$3',' '.$mas[$i]['post'].' ');	
				$mas[$i]['title']=preg_replace('/([\s\t\"\'\?\:\_\“\”\.\,\!\-\=\+\/\[\]\{\}\«\»\(\)\#\$\%\^\&\@])('.$key.'[^\s\t\"\'\?\:\“\”\.\,\_\!\-\=\+\/\[\]\{\}\«\»\(\)\#\$\%\^\&\@]{0,3})([\s\t\"\'\?\:\“\”\.\,\!\-\=\+\/\[\]\{\}\«\»\(\)\#\$\%\^\&\@\_])/isu','$1<span class="kwrd">$2</span>$3',' '.$mas[$i]['title'].' ');	
			}
		}
		if ((trim($mas[$i]['title'])=='') && ($post['post_host']!='twitter.com'))
		{
			$mas[$i]['title']=$post['post_link'];
			// $mas[$i]['post']=$post['post_link'];
		}
		if (mb_strlen($mas[$i]['title'])>50) $mas[$i]['title']=mb_substr($mas[$i]['title'], 0, 50).'...';
		if (mb_strlen($mas[$i]['post'])>300) $mas[$i]['post']=mb_substr($mas[$i]['post'], 0, 300).'...';
		$mas[$i]['title']=trim($mas[$i]['title']);
		$mas[$i]['post']=trim($mas[$i]['post']);
		if ((intval(date('H',$post['post_time']))>0)||(intval(date('i',$post['post_time']))>0)) $stime=date("H:i:s d.m.Y",($post['post_time']+3600));
		else $stime=date("d.m.Y",($post['post_time']+3600));
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
		$mas[$i]['notes']=$post['post_note_count'];
		$mas[$i]['is_read']=$post['post_read'];
		$minp=explode(',',$post['post_fav2']);
		$mas[$i]['imp']=(in_array($user['user_id'], $minp)?1:0);
		$mas[$i]['nastr']=$post['post_nastr'];
		$mas[$i]['spam']=$post['post_spam'];
		$mas[$i]['fav']=$post['post_fav'];
		$mas[$i]['eng']=$post['post_engage'];
		$mas[$i]['adv_eng']=json_decode($post['post_advengage'],true);
		$mas[$i]['foll']=$post['blog_readers'];
		$mas[$i]['ico']=$post['blog_ico'];
		$mas[$i]['geo']=$wobot['destn1'][$post['blog_location']];
		$mas[$i]['geo_c']=$wobot['destn3'][$wobot['destn1'][$post['blog_location']]];
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
		if ((substr($key, 0, 3)=='mw_'))
		{
			$addjoin='LEFT JOIN blog_full_com as f ON f.ful_com_post_id=p.post_id';
			$word[]=str_replace("_",".",substr($key,3));
		}
		if ((substr($key, 0, 4)=='mew_'))
		{
			$addjoin='LEFT JOIN blog_full_com as f ON f.ful_com_post_id=p.post_id';
			$eword[]=str_replace("_",".",substr($key,4));
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
	$addjoin='LEFT JOIN blog_full_com as f ON f.ful_com_post_id=p.post_id';
	//print_r($word);
	//print_r($loc);
	$order_info=$db->query('SELECT * FROM blog_orders WHERE order_id='.intval($_POST['order_id']).' LIMIT 1');
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
	$metrics=json_decode($order['order_metrics']);
	$tags_info=$db->query('SELECT * FROM blog_tag WHERE order_id='.intval($_POST['order_id']));
	while ($tg=$db->fetch($tags_info))
	{
		$d_tags[$tg['tag_tag']]=$tg['tag_name'];
		$d_astags[$tg['tag_name']]=$tg['tag_tag'];
	}
	$qw='SELECT * FROM blog_post as p LEFT JOIN robot_blogs2 as b ON p.blog_id=b.blog_id '.$addjoin.' WHERE order_id='.$_POST['order_id'].' AND post_time>='.strtotime($_POST['stime']).' AND post_time<'.(mktime(0,0,0,date('n',strtotime($_POST['etime'])),date('j',strtotime($_POST['etime']))+1,date('Y',strtotime($_POST['etime'])))).' ';
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
	if ($_POST['undefined']=='true')
	{
		$mton[]=2;		
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
	if (!isset($_POST['hosts'])) $_POST['hosts']=='selected';
	if (!isset($_POST['locations'])) $_POST['locations']=='selected';
	if ((count($resorrr)!=0) && ($_POST['hosts']=='selected'))
	{
		$or='';
		$wh1=' AND (';
		foreach ($resorrr as $item)
		{
			$wh1.=$or.' p.post_host=\''.$item.'\'';
			$or=' OR ';
		} 
		$wh1.=')';
		$qw.=$wh1;
	}
	elseif ((count($resorrr)!=0) && ($_POST['hosts']=='except'))
	{
		$or='';
		$wh1=' AND (';
		foreach ($resorrr as $item)
		{
			$wh1.=$or.' p.post_host!=\''.$item.'\'';
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
	if ((intval($_POST['post_read'])==1)&&(isset($_POST['post_read']))) $qw.=' AND p.post_read=1 ';
	if ((intval($_POST['post_read'])==0)&&(isset($_POST['post_read']))) $qw.=' AND p.post_read=0 ';
	if ((intval($_POST['post_imp'])==1)&&(isset($_POST['post_imp']))) $qw.=' AND (p.post_fav2=\''.$user['user_id'].'\' OR p.post_fav2 LIKE \''.$user['user_id'].',%\' OR p.post_fav2 LIKE \'%,'.$user['user_id'].'\' OR p.post_fav2 LIKE \'%,'.$user['user_id'].',%\') ';
	if ((intval($_POST['post_imp'])==0)&&(isset($_POST['post_imp']))) $qw.=' AND p.post_fav2=\'\' ';
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
	if (count($word)!=0)
	{
		$qw.=' AND (';
		foreach ($word as $key => $item)
		{
			$qw.=$or.'(LOWER(p.post_content) REGEXP "[ ,\.\!\?\{\}\'\"\\\|\>\<\(\)\*\&\^\%\$\#\@\=\+\-\_\n\«\»\№\±\§\/\;\:\←\€\£\¥\©\®\™\±\≠\≤\≥\÷\×\ј\џ\ќ\®\†\њ\ѓ\ѕ\ў\‘\“\ƒ\ћ\÷\©\}\°\љ\∆\…\э\ё\ђ\≈\≠\µ\и\™\~\≤\≤\≤\≥\“\”\”\’0-9]'.$item.'[ ,\.\!\?\{\}\'\"\\\|\>\<\(\)\*\&\^\%\$\#\@\=\+\-\_\n\«\»\№\±\§\/\;\:\←\€\£\¥\©\®\™\±\≠\≤\≥\÷\×\ј\џ\ќ\®\†\њ\ѓ\ѕ\ў\‘\“\ƒ\ћ\÷\©\}\°\љ\∆\…\э\ё\ђ\≈\≠\µ\и\™\~\≤\≤\≤\≥\“\”\”\’0-9]" OR p.post_content LIKE "'.$item.'%")';
			$qw.=$or.' OR (LOWER(f.ful_com_post) REGEXP "[ ,\.\!\?\{\}\'\"\\\|\>\<\(\)\*\&\^\%\$\#\@\=\+\-\_\n\«\»\№\±\§\/\;\:\←\€\£\¥\©\®\™\±\≠\≤\≥\÷\×\ј\џ\ќ\®\†\њ\ѓ\ѕ\ў\‘\“\ƒ\ћ\÷\©\}\°\љ\∆\…\э\ё\ђ\≈\≠\µ\и\™\~\≤\≤\≤\≥\“\”\”\’0-9]'.$item.'[ ,\.\!\?\{\}\'\"\\\|\>\<\(\)\*\&\^\%\$\#\@\=\+\-\_\n\«\»\№\±\§\/\;\:\←\€\£\¥\©\®\™\±\≠\≤\≥\÷\×\ј\џ\ќ\®\†\њ\ѓ\ѕ\ў\‘\“\ƒ\ћ\÷\©\}\°\љ\∆\…\э\ё\ђ\≈\≠\µ\и\™\~\≤\≤\≤\≥\“\”\”\’0-9]" OR f.ful_com_post LIKE "'.$item.'%")';
			$or=' OR ';
		}
		$qw.=')';
	}
	$or='';
	if (count($eword)!=0)
	{
		$qw.=' AND (';
		foreach ($eword as $key => $item)
		{
			$qw.=$or.'(LOWER(p.post_content) NOT REGEXP "[ ,\.\!\?\{\}\'\"\\\|\>\<\(\)\*\&\^\%\$\#\@\=\+\-\_\n\«\»\№\±\§\/\;\:\←\€\£\¥\©\®\™\±\≠\≤\≥\÷\×\ј\џ\ќ\®\†\њ\ѓ\ѕ\ў\‘\“\ƒ\ћ\÷\©\}\°\љ\∆\…\э\ё\ђ\≈\≠\µ\и\™\~\≤\≤\≤\≥\“\”\”\’0-9]'.$item.'[ ,\.\!\?\{\}\'\"\\\|\>\<\(\)\*\&\^\%\$\#\@\=\+\-\_\n\«\»\№\±\§\/\;\:\←\€\£\¥\©\®\™\±\≠\≤\≥\÷\×\ј\џ\ќ\®\†\њ\ѓ\ѕ\ў\‘\“\ƒ\ћ\÷\©\}\°\љ\∆\…\э\ё\ђ\≈\≠\µ\и\™\~\≤\≤\≤\≥\“\”\”\’0-9]" AND p.post_content NOT LIKE "'.$item.'%")';
			$qw.=$or.' AND (LOWER(f.ful_com_post) NOT REGEXP "[ ,\.\!\?\{\}\'\"\\\|\>\<\(\)\*\&\^\%\$\#\@\=\+\-\_\n\«\»\№\±\§\/\;\:\←\€\£\¥\©\®\™\±\≠\≤\≥\÷\×\ј\џ\ќ\®\†\њ\ѓ\ѕ\ў\‘\“\ƒ\ћ\÷\©\}\°\љ\∆\…\э\ё\ђ\≈\≠\µ\и\™\~\≤\≤\≤\≥\“\”\”\’0-9]'.$item.'[ ,\.\!\?\{\}\'\"\\\|\>\<\(\)\*\&\^\%\$\#\@\=\+\-\_\n\«\»\№\±\§\/\;\:\←\€\£\¥\©\®\™\±\≠\≤\≥\÷\×\ј\џ\ќ\®\†\њ\ѓ\ѕ\ў\‘\“\ƒ\ћ\÷\©\}\°\љ\∆\…\э\ё\ђ\≈\≠\µ\и\™\~\≤\≤\≤\≥\“\”\”\’0-9]" AND f.ful_com_post NOT LIKE "'.$item.'%")';
			$or=' AND ';
		}
		$qw.=')';
	}
	if ($_POST['gender']=='2')
	{
		$qw.=' AND b.blog_gender=2';
	}
	else
	if ($_POST['gender']=='1')
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
	//echo $qw;
	$fp = fopen('log_exp.txt', 'a');
	fwrite($fp, $qw);
	fclose($fp);

	$posts=$db->query($qw);
	$i=1;
	// echo $qw;
	// die();
	//print_r($word);
	//die();
	while ($post=$db->fetch($posts))
	{
		//echo $post['post_id'].'|';
		//echo $c.' ';
		$mas[$i]['id']=$post['post_id'];
		$parts=explode("\n",html_entity_decode(strip_tags($post['post_content']),ENT_QUOTES,'UTF-8'));
		if ($post['post_host']!='twitter.com')
		{
			$msent=get_sentence($post['ful_com_post']==''?' '.$post['post_content'].' ':' '.$post['ful_com_post'].' ');
			$mas[$i]['post']=get_needed_sentence($msent,$order['order_keyword']);
			$mas[$i]['title']=$msent[0];
		}
		else
		{
			//$mas[$i]['post']=$post['ful_com_post']==''?$post['post_content']:$post['ful_com_post'];
			$mas[$i]['title']=$post['ful_com_post']==''?$post['post_content']:$post['ful_com_post'];
		}
		// $mas[$i]['post']=stripslashes(strip_tags(html_entity_decode(preg_replace('/\s+/is',' ',$parts[0]!=''?$parts[0]:($parts[1]!=''?$parts[1]:strip_tags($post['post_content']))),ENT_QUOTES,'UTF-8')));//preg_replace('/\s+/is',' ',strip_tags($post['post_content']));
		// $mas[$i]['title']=mb_substr(preg_replace('/\s+/is',' ',strip_tags($post['post_content'])),0,140,'UTF-8').'...';
		foreach($yet as $key => $item)
		{
			//echo '/[\s\t\"\'\?\:]('.$key.'.*?)[\s\t\"\'\?\:]/isu ';
			if (trim($key)!='')
			{
				$mas[$i]['title']=preg_replace('/([\s\t\"\'\?\:\“\”\.\,\!\-\=\+\/\[\]\{\}\«\»\(\)\#\$\%\^\&\@\_])('.$key.'[^\s\t\"\'\?\:\“\”\.\,\!\-\=\+\/\[\]\{\}\«\»\(\)\#\$\%\^\&\@\_]{0,3})([\s\t\"\'\?\:\“\”\.\,\!\-\=\+\/\[\]\{\}\«\»\(\)\#\$\%\^\&\@\_])/isu','$1<span class="kwrd">$2</span>$3',' '.$mas[$i]['title'].' ');	
				$mas[$i]['post']=preg_replace('/([\s\t\"\'\?\:\“\”\.\,\!\-\=\+\/\[\]\{\}\«\»\(\)\#\$\%\^\&\@\_])('.$key.'[^\s\t\"\'\?\:\“\”\.\,\!\-\=\+\/\[\]\{\}\«\»\(\)\#\$\%\^\&\@\_]{0,3})([\s\t\"\'\?\:\“\”\.\,\!\-\=\+\/\[\]\{\}\«\»\(\)\#\$\%\^\&\@\_])/isu','$1<span class="kwrd">$2</span>$3',' '.$mas[$i]['post'].' ');	
			}
		}
		if ((trim($mas[$i]['title'])=='') && ($post['post_host']!='twitter.com'))
		{
			$mas[$i]['title']=$post['post_link'];
			// $mas[$i]['post']=$post['post_link'];
		}
		if (mb_strlen($mas[$i]['title'])>50) $mas[$i]['title']=mb_substr($mas[$i]['title'], 0, 50).'...';
		if (mb_strlen($mas[$i]['title'])>300) $mas[$i]['post']=mb_substr($mas[$i]['post'], 0, 300).'...';
		$mas[$i]['title']=trim($mas[$i]['title']);
		$mas[$i]['post']=trim($mas[$i]['post']);
		// echo '!!!'.$mas[$i]['title'].' '.$mas[$i]['post'].'!!!';
		//$mas[$i]['title']=preg_replace('/\s+/is',' ',strip_tags($post['post_content']));
		if ((intval(date('H',$post['post_time']))>0)||(intval(date('i',$post['post_time']))>0)) $stime=date("H:i:s d.m.Y",($post['post_time']+3600));
		else $stime=date("d.m.Y",($post['post_time']+3600));
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
		$mas[$i]['count_user']=$metrics['speakers'][$post['blog_nick']];
		$mas[$i]['notes']=$post['post_note_count'];
		$mas[$i]['is_read']=$post['post_read'];
		$minp=explode(',',$post['post_fav2']);
		$mas[$i]['imp']=(in_array($user['user_id'], $minp)?1:0);
		$mas[$i]['nastr']=$post['post_nastr'];
		$mas[$i]['spam']=$post['post_spam'];
		$mas[$i]['eng']=$post['post_engage'];
		$mas[$i]['fav']=$post['post_fav'];
		$mas[$i]['adv_eng']=json_decode($post['post_advengage'],true);
		$mas[$i]['foll']=$post['blog_readers'];
		$mas[$i]['ico']=$post['blog_ico'];
		$mas[$i]['geo']=$wobot['destn1'][$post['blog_location']];
		$mas[$i]['geo_c']=$wobot['destn3'][$wobot['destn1'][$post['blog_location']]];
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
	$qw=preg_replace('/(SELECT \*)/is','SELECT post_id',$qw);
	// $qw=preg_replace('/(ORDER BY)/is','GROUP BY post_host ORDER BY',$qw);
	$countqposts=$db->query($qw);
	// while ($count=$db->fetch($countqposts))
	// {
	// 	$cnt+=$count['cnt'];
	// 	$cnt_host++;
	// }
	$mas['page']=0;
	$mas['md5_count_post']=$db->num_rows($countqposts);	
	// $mas['md5_count_src']=$cnt_host;
	$_SESSION['count_post_'.md5($qw1)]=$cnt;
	$_SESSION['count_src_'.md5($qw1)]=$cnt_host;
}

echo json_encode($mas);

?>