<?

require_once('/var/www/new/com/config.php');
require_once('/var/www/new/com/func.php');
require_once('/var/www/new/com/db.php');
require_once('/var/www/new/bot/kernel.php');
require_once('auth.php');

$db = new database();
$db->connect();
//$_GET=$_POST;
auth();
if (!$loged) die();
//$redis=new Redis() or die("Can'f load redis module.");
//$redis->connect('127.0.0.1');
//set_log('spam',$_POST);


/*{
	$mas['status']='fail';
	echo json_encode($mas);	
	die();
}
if ((intval($_GET['order_id'])==0) || (intval($_GET['id'])==0))
{
	$mas['status']='fail';
	echo json_encode($mas);	
	die();
}*/

if ((intval($_POST['order_id'])!=0) && (intval($_POST['post_id'])!=0))
{
	$qpost=$db->query('SELECT * FROM blog_post as a LEFT JOIN robot_blogs2 as b ON a.blog_id=b.blog_id WHERE a.post_id='.intval($_POST['post_id']).' AND a.order_id='.intval($_POST['order_id']).' LIMIT 1');
	$post=$db->fetch($qpost);
	$tags_info=$db->query('SELECT * FROM blog_tag WHERE order_id='.intval($_POST['order_id']).' AND user_id='.$user['user_id']);
	while ($tg=$db->fetch($tags_info))
	{
		$d_tags[$tg['tag_tag']]=$tg['tag_name'];
		$d_astags[$tg['tag_name']]=$tg['tag_tag'];
	}
	$mas[$i]['id']=$post['post_id'];
	$parts=explode("\n",html_entity_decode(strip_tags($post['post_content']),ENT_QUOTES,'UTF-8'));
	$mas[$i]['post']=stripslashes(strip_tags(html_entity_decode(preg_replace('/\s+/is',' ',$parts[0]!=''?$parts[0]:($parts[1]!=''?$parts[1]:strip_tags($post['post_content']))),ENT_QUOTES,'UTF-8')));//preg_replace('/\s+/is',' ',strip_tags($post['post_content']));
	$mas[$i]['title']=mb_substr(preg_replace('/\s+/is',' ',strip_tags($post['post_content'])),0,140,'UTF-8').'...';
	foreach($yet as $key => $item)
	{
		//echo '/[\s\t\"\'\?\:]('.$key.'.*?)[\s\t\"\'\?\:]/isu ';
		if (trim($key)!='')
		{
			$mas[$i]['title']=preg_replace('/([\s\t\"\'\?\:\“\”\.\,\!\-\=\+\/\[\]\{\}\«\»\(\)\#\$\%\^\&\@\_])('.$key.'[^\s\t\"\'\?\:\“\”\.\,\!\-\=\+\/\[\]\{\}\«\»\(\)\#\$\%\^\&\@\_]{0,3})([\s\t\"\'\?\:\“\”\.\,\!\-\=\+\/\[\]\{\}\«\»\(\)\#\$\%\^\&\@\_])/isu','$1<span class="kwrd">$2</span>$3',' '.$mas[$i]['title'].' ');	
			$mas[$i]['post']=preg_replace('/([\s\t\"\'\?\:\“\”\.\,\!\-\=\+\/\[\]\{\}\«\»\(\)\#\$\%\^\&\@\_])('.$key.'[^\s\t\"\'\?\:\“\”\.\,\!\-\=\+\/\[\]\{\}\«\»\(\)\#\$\%\^\&\@\_]{0,3})([\s\t\"\'\?\:\“\”\.\,\!\-\=\+\/\[\]\{\}\«\»\(\)\#\$\%\^\&\@\_])/isu','$1<span class="kwrd">$2</span>$3',' '.$mas[$i]['post'].' ');	
		}
	}
	if (trim($mas[$i]['title'])=='')
	{
		$mas[$i]['title']=$post['post_link'];
		$mas[$i]['post']=$post['post_link'];
	}
	//$mas[$i]['title']=preg_replace('/\s+/is',' ',strip_tags($post['post_content']));
	if ((intval(date('H',$post['post_time']))>0)||(intval(date('i',$post['post_time']))>0)) $stime=date("H:i:s d.m.Y",$post['post_time']);
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
	echo json_encode($mas);
	die();
}
else
{
	$out['status']=1;
	echo json_encode($out);
	die();
}

?>