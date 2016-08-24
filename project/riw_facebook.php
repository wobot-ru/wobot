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

function parseurl2($url,$fields=0)
{
  $uagent = "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13";
//$keyword=$word;
//$keyword=urlencode(iconv('utf-8','windows-1251',$keyword));
//$url='http://2mm.ru/forum/search.php?mode=result&start=15';
//$postvars='search_keywords='.$keyword.'&search_terms=any&search_author=&search_forum=-1&search_time=0&search_fields=all&search_cat=-1&sort_by=0&sort_dir=DESC&show_results=posts&return_chars=-1';
foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
rtrim($fields_string,'&');
//echo $fields_string;
$uagent = "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13";
$ch = curl_init( $url );
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);   // возвращает веб-страницу
curl_setopt($ch, CURLOPT_HEADER, 0);           // не возвращает заголовки
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);   // переходит по редиректам
if ($field_string=='&')
{
curl_setopt($ch,CURLOPT_POST,count($fields));
curl_setopt($ch,CURLOPT_POSTFIELDS,$fields_string);
}
curl_setopt($ch, CURLOPT_ENCODING, "");        // обрабатывает все кодировки
curl_setopt($ch, CURLOPT_USERAGENT, $uagent);  // useragent
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 120); // таймаут соединения
curl_setopt($ch, CURLOPT_TIMEOUT, 120);        // таймаут ответа
curl_setopt($ch, CURLOPT_MAXREDIRS, 10);       // останавливаться после 10-ого редиректа
$content = curl_exec( $ch );
$err     = curl_errno( $ch );
$errmsg  = curl_error( $ch );
$header  = curl_getinfo( $ch );
curl_close( $ch );
  return $content;
}

function getnicklink($link,$blog_nick='')
{
	global $db;
	$hn=parse_url($link);
	$hn=$hn['host'];
	$ahn=explode('.',$hn);
	$hn = $ahn[count($ahn)-2].'.'.$ahn[count($ahn)-1];

	//adding account info to db [$hn, $link]
	if ($hn=='twitter.com') 
	            {
	                               //http://twitter.com/kalamanao/statuses/15237434540
	                               //echo $link."\n";http://twitter.com/Rostown/statuses/20961854384
	                               list($tmp,$tmp,$tmp,$nick,$tmp)=explode("/",$link,5);
	                       }
	                       elseif ($hn=='livejournal.com') {
	                               //http://knowledgeaction.livejournal.com/21962.html
	                               //http://community.livejournal.com/chgk_aic/28944.html
	                                       $regexy="/\/\/(?<gg_id>.*?)\./is";
	                                       preg_match_all($regexy,$link,$out);
	                                       $gg_id=$out[gg_id][0];
	                                       //print_r($out);
	                                       if ($out[gg_id][0]=="community")
	                                       {
	                                               $regexy="/com\/(?<ggg_id>.*?)\//is";
	                                               preg_match_all($regexy,$link,$outt);
	                                               //print_r($outt);
	                                               $gg_id=$outt[ggg_id][0];
	                                       }
												$nick=$gg_id;
	                                       //echo $gg_id;
	                               }
	                               elseif ($hn=='facebook.com') 
	                               {
	                                       //$link="http://facebook.com/100000344775791/posts/173713625973304";
										   //$link="http://facebook.com/permalink.php?story_fbid=148266305238031&id=1712660217"
	                                       $mas=explode('/',$link);
	                                       //$json=parseUrl("https://graph.facebook.com/".$mas[3]."?access_token=".$fb_access_token);
										   $nick=$mas[3];
										   if (intval($nick)==0) {
											$mas=explode('=',$link);
											$nick=$mas[count($mas)-1];
											}
	                               }
	                               elseif ($hn=='vkontakte.ru') 
	                               {
	                                       //$link="http://vkontakte.ru/note341_10136133";
	                                       //$link="http://vkontakte.ru/id29237?status=179";
	                                       $regexy="/note(?<vk_name>.*?)\_/is";
	                                       preg_match_all($regexy,$link,$out);
	                                       if ($out[vk_name][0]=='')
	                                       {
	                                               $regexy="/id(?<vk_name>.*?)\?/is";
	                                               preg_match_all($regexy,$link,$out);
	                                       }
											$nick=$out[vk_name][0];
                               }
							if ($nick!='')
							{
							$blg=$db->query('SELECT * FROM robot_blogs2 WHERE blog_link="'.$hn.'" and blog_login="'.$nick.'" LIMIT 1');
							if (mysql_num_rows($blg)==0)
							{
							        $db->query('INSERT INTO robot_blogs2 (blog_link, blog_login, blog_nick) values ("'.$hn.'","'.$nick.'","'.$blog_nick.'")');
									$blog_id=mysql_insert_id();
							}
							else
							{
								$blgl=$db->fetch($blg);
								$blog_id=$blgl['blog_id'];
							}
						}
						return $blog_id;
}

//POST
//curl -v -F type=client_cred -F client_id=your_app_id -F client_secret=your_app_secret https://graph.facebook.com/oauth/access_token?scope=offline_access
$fields['type']='client_cred';
$fields['client_id']='122208881184989';
$fields['client_secret']='e96f80a9f85b83aa864cc0f167ace3ae';
/*
$i=0;
do {
	$auth=parseurl2('https://graph.facebook.com/oauth/access_token?scope=offline_access',$fields);
	$i++;
	"OAuth query".(($i>1)?" #".intval($i):'')."\n";
} while($auth=='');

	$error=json_decode($auth,true);
	if (isset($error['error']))
	{
		echo "API Error:"."\n".$token['error']['type']."\n".$token['error']['message']."\n";
		die();
	}
	$token=$auth;
	echo $token."\n";
*/
$token='access_token=122208881184989|5SIisolGgImegZWAr6UoEhYS5D4';

$since[0]=1319227200;
$since[1]=1319227200;

unset($link_s);

//echo $where;
//die();
while(1)
{
//$ressec=$db->query('SELECT * FROM blog_orders where order_id=441 OR order_id=447 OR order_id=448 OR order_id=449 OR order_id=450 OR order_id=456 OR order_id=457 OR order_id=458 OR order_id=459 OR order_id=464 OR order_id=465');
//$ressec=$db->query('SELECT * FROM blog_orders where order_fb_rt!=0');
//echo 'new orders to cache: '.mysql_num_rows($ressec)."\n";
//while($blog=$db->fetch($ressec))
//{
	//$mqu=explode('|',$blog['order_keyword']);, 'майкрософт'
	$mqu=array('medvedev','медведев');
	foreach ($mqu as $key=>$mqit)
	{
	$query=$mqit;//$blog['order_name'];
	echo '['.date('r').'] '.$query;
	
	//$ressince=$db->query('SELECT * FROM blog_orders where order_id=';
	
	//$link='https://graph.facebook.com/search?q=microsoft&type=post&access_token=2227470867|2.AQD4D98mVfweC-sP.3600.1313074800.0-1548406154|7IVmvPCCRYOjruET-P1M3JfQTAY';
	//simple  &locale=ru_RU//since//&locale=ru_RU since
	$link='https://graph.facebook.com/search?q='.urlencode($query).'&type=post&limit=75&access_token='.(intval($since[$key])>0?'&until='.intval($since[$key]):'');
	echo $link;
	$out=parseurl2($link);
	//echo "==============================================\n";
	//echo $out;
	//echo "==============================================\n";
	$data=json_decode($out,true);
	//print_r($data);
	//echo "==============================================\n";
	/*
	[data] => Array
    	(
        	[0] => Array
            	(
                	[id] => 100000649942753_192977034099505
                	[from] => Array http://www.facebook.com/100000649942753/posts/192977034099505
                    	(
                        	[name] => Dmitry Bulanov
                        	[id] => 100000649942753
                    		)
                			[message] => Microsoft запускает портал BuildMyPinnedSite: 
                			[picture] => http://external.ak.fbcdn.net/safe_image.php?d=AQDyvAVQlkLObdZz&w=90&h=90&url=http%3A%2F%2Fwww.oszone.net%2Ffigs%2Fu%2F88795%2F110811135911%2Fhome_slide_pinning.jpg
                			[link] => http://www.oszone.net/15936/BuildMyPinnedSite_Portal
                			[name] => Microsoft запускает портал BuildMyPinnedSite
                			[caption] => www.oszone.net
                			[description] => Oszone.net: Microsoft запускает портал BuildMyPinnedSite
                			[icon] => http://b.static.ak.fbcdn.net/rsrc.php/v1/yD/r/aS8ecmYRys0.gif
                			[type] => link
                			[created_time] => 2011-08-11T15:00:23+0000
                			[updated_time] => 2011-08-11T15:00:23+0000
                			[likes] => Array
                    		(
                        [data] => Array
                            (
                                [0] => Array
                                    (
                                        [name] => Sergei Tkachenko
                                        [id] => 100000472252516
                                    )
                            )
                        [count] => 1
                    )
            )
 			$upquery="UPDATE robot_blogs2 SET blog_link='".$hn."', blog_location='".$out_tw['loc_tw'][0]."',blog_readers=0 ,blog_last_update=".time().",blog_nick='".$mas2['name']."' WHERE blog_login='".$nick."' AND blog_link='facebook.com'";
			blog_link facebook.com
			blog_nick Dmitry Bulanov
			blog_login 100000649942753
			*/
		$i=0;
		foreach ($data['data'] as $post)
		{
			//echo '['.$post['created_time']."] id:".$post['id']."\nauthor: ".$post['from']['name']." (".$post['from']['id'].")\ncaption: ".$post['caption']."\nmessage: ".$post['message']."\ndescription: ".$post['description']."\nlikes: ".intval($post['likes']['count'])."\n\n";
			//post_id 	order_id 	post_link 	post_time 	post_content 	blog_id 	post_nastr 	post_spam 	post_fav 	post_type 	post_tag 	post_engage
			list($part1,$part2)=explode('_',$post['id']);
			$blog_id=getnicklink('http://www.facebook.com/'.$part1.'/posts/'.$part2);
			//echo 'user:'.$blog_id.': http://www.facebook.com/'.$part1.'/posts/'.$part2."\n";
			if (strlen($post['message'])>0) $post_content=addslashes($post['message']);
			elseif (strlen($post['description'])>0) $post_content=addslashes($post['description']);
			elseif (strlen($post['name'])>0) $post_content=addslashes($post['name']);
			elseif (strlen($post['caption'])>0) $post_content=addslashes($post['caption']);
			$post_time=strtotime($post['created_time']);
			
			//echo $post_time.' '.$post_content."\n";
			if (($post_time<1319400000)&&(!in_array($part1.' '.$part2, $link_s)))
			{
			//$db->query('INSERT INTO riw_post (post_source, post_nick, post_msg, post_date, post_url) values ("facebook.com","'.addslashes($post['name']).'","'.$post_content.'", '.$post_time.',"http://www.facebook.com/'.$part1.'/posts/'.$part2.'")');
			//541
				$db->query('INSERT INTO blog_post (order_id, post_host, post_link, post_time, post_content, blog_id, post_engage) values ("541","facebook.com","http://www.facebook.com/'.$part1.'/posts/'.$part2.'",'.$post_time.',"'.$post_content.'",'.intval($blog_id).','.intval($post['likes']['count']).')');
			$link_s[]=$part1.' '.$part2;
			}
			$i++;
		}
		
		echo 'posts: '.$i."\n";

		//print_r($data['paging']['previous']);
		echo "\n";
		//https://graph.facebook.com/search?q=microsoft&type=post&limit=75&access_token&locale=ru_RU&since=1313143549
		if (strlen($data['paging']['previous'])>0)
		{//previous
			$vars=explode('&',$data['paging']['previous']);
			foreach ($vars as $var)
			{
				list($name,$value)=explode('=',$var);
				if ($name=='since')//since
				{
					$since[$key]=$value;
					break;
				}
			}
			echo "since: ".$since[$key]."\n";
			if ($since[$key]<(time()-86400)) $since[$key]=0;
			//$db->query('UPDATE blog_orders set order_fb_rt="'.$since.'" where order_id='.$blog['order_id']);
		}
		echo "=========================================================\n";
	}
	//getcash($blog['order_id']);
	sleep(5);
}
echo "idle...\n";
sleep(5);
//}
//echo $link."\n";
//with date
//$link='https://graph.facebook.com/search?q='.urlencode($query).'&type=post&limit=75&'.$token.'&since=Tue+Aug+09+16%3A42%3A37+-0400+2011';
//echo $link."\n";





?>