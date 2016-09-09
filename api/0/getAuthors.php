<?php
ini_set('memory_limit', '2048M');
date_default_timezone_set ( 'Europe/Moscow' );
// $_POST['order_id']=3361;
// $_POST['start']='1.1.2012';
// $_POST['end']='1.1.2014';
$order_id = $_POST['order_id'];
error_reporting(0);

require_once('/var/www/new/com/config.php');
require_once('/var/www/new/com/db.php');
require_once('auth.php');
require_once('/var/www/new/com/func.php');
require_once('/var/www/new/bot/kernel.php');
require_once('func_export.php');
require_once('/var/www/com/loc.php');

// $user['tariff_id']=15;
// $user['user_id']=1187;
auth();
if (!$loged){
	echo '{"error":"authentication_error"}';
	die(); // if not loged in , die
}
if (!isset($_POST['order_id'])){
	echo '{"error":"missing the order_id parameter"}';
	die(); // if not loged in , die
}
$db = new database();
$db->connect();
// echo "SELECT * from blog_orders WHERE order_id=".intval($_POST['order_id'])." and user_id=".intval($user['user_id'])." LIMIT 1";
// die(json_encode($user));
$res=$db->fetch($db->query("SELECT * from blog_orders WHERE order_id=".intval($order_id)." and user_id=".intval($user['user_id'])." LIMIT 1"));
if(!$res){
	echo '{"error":"this order_id doesn\'t belong to this user"}';
	die();
} // if order_id not belongs to current user

$file_name=preg_replace('/[^а-яА-Яa-zA-Z\-\_0-9]/isu','_',($res['order_name']!=''?$res['order_name']:$res['order_keyword']));
$file_name=preg_replace('/\_+/isu','_',$file_name);
if (mb_strlen($file_name,'UTF-8')>100)
{
	$file_name=mb_substr($file_name,0,100,'UTF-8');
}

header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;");
header("Content-Disposition: attachment;filename=\"wobot_".
date('dmy',strtotime($_POST['start'])).'_'.date('dmy',strtotime($_POST['end'])).'_'.$file_name.".csv\"");
header("Cache-Control: max-age=0");


function sorted_authors($ar){	
	 $head = array("Номер","Автор","Ссылка","Фоловеры","Кол-во упоминаний","Коментариев","Лайков","Ретвитов","Негативных","Позитивных","Нейтральных","Источник","Пол","Возраст","Регион");
	 foreach ($head as $key => $value) {
	 	$head[$key] = iconv("UTF-8//IGNORE","CP1251",$head[$key]);
	 }
	 $result[] = $head;
	foreach ($ar['count'] as $key => $item)
	{	
		$author_counter++;
		$iter_result = array();
		$iter_result[]=$key+1;
		$iter_result[]=iconv("UTF-8//IGNORE","CP1251",$ar['nick'][$key]);
		if ($ar['src'][$key]=='livejournal.com')
		{
			if ($ar['login'][$key]!='')
			{
				$iter_result[]='http://'.$ar['login'][$key].'.livejournal.com/';
			}
			else
			{
				$regex='/http\:\/\/(?<nick>.*?)\./isu';
				preg_match_all($regex,$ar['nick'][$key],$out);
				$iter_result[]='http://'.$out['nick'][0].'.livejournal.com/';
			}
		}
		else
		if (($ar['src'][$key]=='vk.com') || ($ar['src'][$key]=='vkontakte.ru'))
		{
			$iter_result[]='http://vk.com/'.($ar['login'][$key][0]=='-'?'club'.mb_substr($ar['login'][$key],1,mb_strlen($ar['login'][$key],'UTF-8'),'UTF-8'):'id'.$ar['login'][$key]);
		}
		else
		if ($ar['src'][$key]=='twitter.com')
		{
			$iter_result[]='http://twitter.com/'.$ar['login'][$key];
		}
		else
		if ($ar['src'][$key]=='facebook.com')
		{
			$iter_result[]='http://facebook.com/'.$ar['login'][$key];
		}
		else
		if ($ar['src'][$key]=='mail.ru')
		{
			$iter_result[]=$ar['link'][$key];
		}
		else
		if ($ar['src'][$key]=='liveinternet.ru')
		{
			$iter_result[]=$ar['link'][$key];
		}
		else
		if ($ar['src'][$key]=='ya.ru')
		{
			$iter_result[]=$ar['link'][$key];
		}
		else
		if ($ar['src'][$key]=='yandex.ru')
		{
			$iter_result[]=$ar['link'][$key];
		}
		else
		if ($ar['src'][$key]=='rutwit.ru')
		{
			$iter_result[]=$ar['link'][$key];
		}
		else
		if ($ar['src'][$key]=='rutvit.ru')
		{
			$iter_result[]=$ar['link'][$key];
		}
		else
		if ($ar['src'][$key]=='babyblog.ru')
		{
			$iter_result[]=$ar['link'][$key];
		}
		else
		if ($ar['src'][$key]=='blog.ru')
		{
			$iter_result[]=$ar['link'][$key];
		}
		else
		if ($ar['src'][$key]=='foursquare.com')
		{
			$iter_result[]=$ar['link'][$key];
		}
		else
		if ($ar['src'][$key]=='kp.ru')
		{
			$iter_result[]=$ar['link'][$key];
		}
		else
		if ($ar['src'][$key]=='aif.ru')
		{
			$iter_result[]=$ar['link'][$key];
		}
		else
		if ($ar['src'][$key]=='friendfeed.com')
		{
			$iter_result[]=$ar['link'][$key];
		}
		else
	    if ($ar['src'][$key]=='google.com')
	    {
	    	$iter_result[]=$ar['link'][$key];
	    }
		$iter_result[]=intval($ar['foll'][$key]);
		$iter_result[]=$ar['count'][$key];
		$iter_result[]=intval($ar['comments_count'][$key]);
		$iter_result[]=intval($ar['likes_count'][$key]);
		$iter_result[]=intval($ar['retweet_count'][$key]);
		$iter_result[]=intval($ar['neg'][$key]);
		$iter_result[]=intval($ar['pos'][$key]);
		$iter_result[]=intval($ar['neu'][$key]);
		$iter_result[]=$ar['src'][$key];//source
		$iter_result[]=iconv("UTF-8//IGNORE","CP1251",$ar['gnd'][$key]);//gender
		$iter_result[]=$ar['age'][$key];//age
		$iter_result[]=iconv("UTF-8//IGNORE","CP1251",$ar['loc'][$key]);//region
		$result[] = $iter_result;
	}

	if (count($result)==0)
	{
		$result=array('','','','','','','','','','','','','','','');
	}
	return $result;
}

function outputCSV($data) {
  $outstream = fopen("php://output", 'w');
  function __outputCSV(&$vals, $key, $filehandler) {
    fputcsv($filehandler, $vals, ';', '"');
  }
  array_walk($data, '__outputCSV', $outstream);
  fclose($outstream);
}

$qqq=get_query();
$qqq=preg_replace('/SELECT \*/isu', 'SELECT p.post_id,p.post_link,p.post_host,p.blog_id,p.post_spam,p.post_fav,p.post_nastr,p.post_tag,p.post_advengage,b.blog_link,b.blog_location,b.blog_readers,b.blog_nick,b.blog_login,b.blog_gender ', $qqq);

$qpost=$db->query($qqq);

while($pst = $db->fetch($qpost))
{		
	$outcash['link'][$ii]=str_replace("\n","",$pst['post_link']);
	$outcash['time'][$ii]=$pst['post_time']; // time
	$outcash['content'][$ii]=$pst['post_content']; //content
	$outcash['fcontent'][$ii]=$pst['ful_com_post'];
	$outcash['isfav'][$ii]=$pst['post_fav']; // is favorite
	$outcash['nastr'][$ii]=$pst['post_nastr'];
	$outcash['isspam'][$ii]=$pst['post_spam'];
	$outcash['nick'][$ii]=$pst['blog_nick'];
	$outcash['tag'][$ii]=$pst['post_tag'];
	$outcash['comm'][$ii]=$pst['blog_readers'];
	$outcash['eng'][$ii]=$pst['post_engage'];
	//export_fix_1 begin
	$aeng=json_decode($pst['post_advengage'],true);
	$outcash['comment'][$ii]=intval($aeng['comment']);
	$outcash['likes'][$ii]=intval($aeng['likes']);
	$outcash['retweet'][$ii]=intval($aeng['retweet']);
	//export_fix_1 end
	$outcash['loc'][$ii]=$pst['blog_location'];
	$outcash['gender'][$ii]=$pst['blog_gender'];
	$outcash['age'][$ii]=$pst['blog_age'];
	$outcash['login'][$ii]=$pst['blog_login'];
	$outcash['host'][$ii]=$pst['post_host'];
	$outcash['blog_id'][$ii]=$pst['blog_id'];
	$outcash['blog_link'][$ii]=$pst['blog_link'];
	$ii++;
}

foreach ($outcash['link'] as $key => $llink)
{		
	$link=urldecode($llink);
	$comm=intval($outcash['comm'][$key]);
	$loc=$outcash['loc'][$key];
	$gender=$outcash['gender'][$key];
	$blog_id=$outcash['blog_id'][$key];
	if ($gender==0)
	{
		$gender='-';
	}
	else
	{
		if ($gender==1)
		{
			$gender='Ж';
		}
		else
		{
			$gender='М';
		}
	}
	$age=$outcash['age'][$key];
	$age = (is_null($age) || $age==0) ? 0 : intval($age);
	$nastr=$outcash['nastr'][$key];

	$nick=$outcash['nick'][$key];
	$login=$outcash['login'][$key];
	$hn=parse_url($link);
	$hn=$hn['host'];
	$ahn=explode('.',$hn);
	$hn = $ahn[count($ahn)-2].'.'.$ahn[count($ahn)-1];
	if ($hn=='.')
	{
		$hn=$outcash['host'][$key];
	}
	if ($hn=='livejournal.com')
	{
		$rgx='/\/\/(?<nk>.*?)\./is';
		preg_match_all($rgx,$link,$out);
		$nick=$out['nk'][0];
		$login=$nick;
	}
	if ((trim($nick)!='') && ($hn!='.'))
	{		
		$top_speakers[$nick.':'.$blog_id]['comments_count']+=$outcash['comment'][$key];
		$top_speakers[$nick.':'.$blog_id]['likes_count']+=$outcash['likes'][$key];
		$top_speakers[$nick.':'.$blog_id]['retweet_count']+=$outcash['retweet'][$key];				

		$top_speakers[$nick.':'.$blog_id]['location']=(!$wobot['destn1'][$loc]) ? '' : $wobot['destn1'][$loc];;
		$top_speakers[$nick.':'.$blog_id]['gender']=$gender;
		$top_speakers[$nick.':'.$blog_id]['age']=$age;

		$top_speakers[$nick.':'.$blog_id]['login']=$login;
		$top_speakers[$nick.':'.$blog_id]['count']++;
		$top_speakers[$nick.':'.$blog_id]['foll']=$comm;
		if ($nastr==1)
		{
			$top_speakers[$nick.':'.$blog_id]['pos']++;
		}
		elseif ($nastr==-1)
		{
			$top_speakers[$nick.':'.$blog_id]['neg']++;
		}
		else
		{
			$top_speakers[$nick.':'.$blog_id]['neu']++;
		}
		$top_speakers[$nick.':'.$blog_id]['src']=$hn;
		if ($hn=='twitter.com')
		{
			$top_speakers[$nick.':'.$blog_id]['link']='http://twitter.com/'.$login;
		}
		else
		if ($hn=='livejournal.com')
		{
			$top_speakers[$nick.':'.$blog_id]['link']='http://'.$login.'.livejournal.com';
		}
		else
		if (($hn=='vkontakte.ru') || ($hn=='vk.com'))
		{
			//echo $login.'!!!<br>';
			$top_speakers[$nick.':'.$blog_id]['link']='http://vk.com/id'.$login;
		}
		else
		if ($hn=='facebook.com')
		{
			$top_speakers[$nick.':'.$blog_id]['link']='http://facebook.com/'.$login;
		}
		else
		if ($hn=='mail.ru')
		{
			$top_speakers[$nick.':'.$blog_id]['link']='http://blogs.'.$outcash['blog_link'][$key].'/'.$login;
		}
		else
		if ($hn=='liveinternet.ru')
		{
			$top_speakers[$nick.':'.$blog_id]['link']='http://www.liveinternet.ru/users/'.$login;
		}
		else
		if ($hn=='ya.ru')
		{
			$top_speakers[$nick.':'.$blog_id]['link']='http://'.$login.'.ya.ru';
		}
		else
		if ($hn=='yandex.ru')
		{
			$top_speakers[$nick.':'.$blog_id]['link']='http://'.$login.'.ya.ru';
		}
		else
		if ($hn=='rutwit.ru')
		{
			$top_speakers[$nick.':'.$blog_id]['link']='http://rutwit.ru/'.$login;
		}
		else
		if ($hn=='rutvit.ru')
		{
			$top_speakers[$nick.':'.$blog_id]['link']='http://rutwit.ru/'.$login;
		}
		else
		if ($hn=='babyblog.ru')
		{
			$top_speakers[$nick.':'.$blog_id]['link']='http://www.babyblog.ru/user/info/'.$login;
		}
		else
		if ($hn=='blog.ru')
		{
			$top_speakers[$nick.':'.$blog_id]['link']='http://'.$login.'.blog.ru/profile';
		}
		else
		if ($hn=='foursquare.com')
		{
			$top_speakers[$nick.':'.$blog_id]['link']='https://ru.foursquare.com/'.$login;
		}
		else
		if ($hn=='kp.ru')
		{
			$top_speakers[$nick.':'.$blog_id]['link']='http://blog.kp.ru/users/'.$login.'/profile/';
		}
		else
		if ($hn=='aif.ru')
		{
			$top_speakers[$nick.':'.$blog_id]['link']='http://blog.aif.ru/users/'.$login.'/profile';
		}
		else
		if ($hn=='friendfeed.com')
		{
			$top_speakers[$nick.':'.$blog_id]['link']='http://friendfeed.com/'.$login;
		}
		else
	    if ($hn=='google.com')
	    {
	    	$top_speakers[$nick.':'.$blog_id]['link']='http://plus.google.com/'.$login.'/about';
	    }
	}
}
foreach ($top_speakers as $key => $item)
{	
	$keyinf=explode(':',$key);
	$top_speak['comments_count'][] = $item['comments_count'];
	$top_speak['likes_count'][] = $item['likes_count'];
	$top_speak['retweet_count'][] = $item['retweet_count'];
	$top_speak['nick'][]=$keyinf[0];
	$top_speak['neg'][]=$item['neg'];
	$top_speak['pos'][]=$item['pos'];

	$top_speak['loc'][]=$item['location'];
	$top_speak['gnd'][]=$item['gender'];
	$top_speak['age'][]=$item['age'];

	$top_speak['neu'][]=$item['neu'];
	$top_speak['foll'][]=$item['foll'];
	$top_speak['src'][]=$item['src'];
	$top_speak['count'][]=$item['count'];
	$top_speak['link'][]=$item['link'];
	$top_speak['login'][]=$item['login'];			
}

array_multisort($top_speak['count'],SORT_DESC,$top_speak['nick'],SORT_ASC,$top_speak['pos'],SORT_DESC,$top_speak['neg'],SORT_DESC,$top_speak['neu'],SORT_DESC,$top_speak['foll'],SORT_DESC,$top_speak['src'],SORT_DESC,$top_speak['link'],SORT_DESC,$top_speak['login'],SORT_DESC,$top_speak['loc'],SORT_DESC,$top_speak['gnd'],SORT_DESC,$top_speak['age'],SORT_DESC);
$authors_sorted_by_name = sorted_authors($top_speak);

// foreach ($csvout as $rid => $row){
// 	foreach ($row as $cid => $cell){
// 		$csvout[$rid][$cid] = iconv("UTF-8//IGNORE","CP1251",$csvout[$rid][$cid]);
// 	}
// }

outputCSV($authors_sorted_by_name);

// function getCSV($array) {
//     ob_start(); // buffer the output ...
//     outputCSV($array);
//     return ob_get_clean(); // ... then return it as a string!
// }
//$output = getCSV($authors_sorted_by_name);
?>