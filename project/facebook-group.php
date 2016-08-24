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
date_default_timezone_set('Europe/Moscow');

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
//$token='access_token=122208881184989|5SIisolGgImegZWAr6UoEhYS5D4';

$token='AAACQNvcNtEgBAC54q2gcwWv7evhCNZAFEoy7fXZAmto867ZAEvUhEjQSKtNJN5KwGGczthuUdZAthFu8FZCBGnOL4zGlGJxBLKYGL3j0IwfV3i7ZATjrzp';
$group='AvtoKlub';
$groups=array(
	/*'AvtoKlub',
	'azercell',
	'38459155632',
	'135826959806296',
	'sazz4G',
	'azeronline',
	'240355629318899',
	'AzeriHits',
	'144992852238834',
	'ESC.BAKU',
	'flashmob.azerbaijan.contact',
	'100000259996781',
	'prbankaz',
	'BankStandard.KB',
	'BankRespublika',
	'kapitalbank',
	'Parabank',
	'azerbaijann',
	'AzerbayCanimiz',
	'ShoppingAzerbaijan',
	'214568377036',
	'InfoCity.az',
	'NissanAzerbaijan',
	'fordazerbaijan',
	'Navigator.az',
	'baku.az',*/
	'CarsBaku'//,
	/*'Baku.City',
	'233954213285192',
	'127527590627357',
	'Avtomobili.Baku',
	'bankofbaku',
	'Fashion.Baku',
	'143664725663004',
	'105348592840046',
	'157843220925550',
	'LikedCars',
	'cars.in.azerbaijan',
	'sabacars',
	'177856385592294',
	'APA.Agency',
	'onlinexeber',
	'NarMobile.Official',
	'Bakcell.Company',
	'AzMobil',
	'AzerbaycanlilarBurada'*/
);
$order_id='635';
//echo $where;
//die();
//while(1)
//{
//$ressec=$db->query('SELECT * FROM blog_orders where order_id=441 OR order_id=447 OR order_id=448 OR order_id=449 OR order_id=450 OR order_id=456 OR order_id=457 OR order_id=458 OR order_id=459 OR order_id=464 OR order_id=465');
//$ressec=$db->query('SELECT * FROM blog_orders WHERE order_fb_rt!=0 and order_fb_rt<(order_end+86400)');


foreach ($groups as $group)
{

echo "\n\n".$group."\n\n";

$ressec=$db->query('SELECT * FROM blog_orders WHERE order_id='.$order_id);

echo 'new orders to cache: '.mysql_num_rows($ressec)."\n";
	$i=0;
while($blog=$db->fetch($ressec))
{
	
	
	
$next='';
//https://graph.facebook.com/109600314980/feed&format=json&limit=75&access_token=AAACQNvcNtEgBADaOdNuVtEOALI6iIG87eI8IvPhWhHlLBAHf8amvbdAgfYDf1bVerYDJpkG8eUb5ZCE7r7t3jCAxmROqLkoKYOyDQMQs9wqkW53pN

	do {

		if ($next=='')
		{
			$link='https://graph.facebook.com/'.$group.'/feed&format=json&limit=75&access_token='.$token;
		}
		else
		{
			$link=$next.'&access_token='.$token;
		}
		echo $link."\n";
		$out=parseurl2($link);

		$data=json_decode($out,true);
		//print_r($data['data']);
		//print_r($data['data']);
		//die();
		foreach($data['data'] as $post)
		{
			list($part1,$part2)=explode('_',$post['id']);
			$blog_id=getnicklink('http://www.facebook.com/'.$part1.'/posts/'.$part2);
			echo 'user:'.$blog_id.': http://www.facebook.com/'.$part1.'/posts/'.$part2."\n";
			if (strlen($post['message'])>0) $post_content=addslashes($post['message']);
			elseif (strlen($post['description'])>0) $post_content=addslashes($post['description']);
			elseif (strlen($post['name'])>0) $post_content=addslashes($post['name']);
			elseif (strlen($post['caption'])>0) $post_content=addslashes($post['caption']);
			$post_time=strtotime($post['created_time']);
			
			$qw=$db->query('SELECT * FROM blog_post WHERE order_id='.$blog['order_id'].' and post_link=\''.addslashes('http://www.facebook.com/'.$part1.'/posts/'.$part2).'\''); //проверка дублей

			if (($post_time>$blog['order_start'])&&($post_time<($blog['order_end']+86400))&&(mysql_num_rows($qw)==0))
			{
				$db->query('INSERT INTO blog_post (order_id, post_link, post_host, post_time, post_content, blog_id, post_engage) values ('.$blog['order_id'].',"http://www.facebook.com/'.$part1.'/posts/'.$part2.'", "facebook.com", '.$post_time.',"'.$post_content.'",'.intval($blog_id).','.intval($post['likes']['count']).')');
				/*$db->query('INSERT INTO blog_post (order_id, post_link, post_time, post_content, blog_id, post_engage) values (585,"http://www.facebook.com/'.$part1.'/posts/'.$part2.'",'.$post_time.',"'.$post_content.'",'.intval($blog_id).','.intval($post['likes']['count']).')');
				$db->query('INSERT INTO blog_post (order_id, post_link, post_time, post_content, blog_id, post_engage) values (586,"http://www.facebook.com/'.$part1.'/posts/'.$part2.'",'.$post_time.',"'.$post_content.'",'.intval($blog_id).','.intval($post['likes']['count']).')');
				$db->query('INSERT INTO blog_post (order_id, post_link, post_time, post_content, blog_id, post_engage) values (587,"http://www.facebook.com/'.$part1.'/posts/'.$part2.'",'.$post_time.',"'.$post_content.'",'.intval($blog_id).','.intval($post['likes']['count']).')');
				$db->query('INSERT INTO blog_post (order_id, post_link, post_time, post_content, blog_id, post_engage) values (588,"http://www.facebook.com/'.$part1.'/posts/'.$part2.'",'.$post_time.',"'.$post_content.'",'.intval($blog_id).','.intval($post['likes']['count']).')');
				$db->query('INSERT INTO blog_post (order_id, post_link, post_time, post_content, blog_id, post_engage) values (589,"http://www.facebook.com/'.$part1.'/posts/'.$part2.'",'.$post_time.',"'.$post_content.'",'.intval($blog_id).','.intval($post['likes']['count']).')');
				$db->query('INSERT INTO blog_post (order_id, post_link, post_time, post_content, blog_id, post_engage) values (590,"http://www.facebook.com/'.$part1.'/posts/'.$part2.'",'.$post_time.',"'.$post_content.'",'.intval($blog_id).','.intval($post['likes']['count']).')');
				$db->query('INSERT INTO blog_post (order_id, post_link, post_time, post_content, blog_id, post_engage) values (591,"http://www.facebook.com/'.$part1.'/posts/'.$part2.'",'.$post_time.',"'.$post_content.'",'.intval($blog_id).','.intval($post['likes']['count']).')');
				$db->query('INSERT INTO blog_post (order_id, post_link, post_time, post_content, blog_id, post_engage) values (592,"http://www.facebook.com/'.$part1.'/posts/'.$part2.'",'.$post_time.',"'.$post_content.'",'.intval($blog_id).','.intval($post['likes']['count']).')');
				$db->query('INSERT INTO blog_post (order_id, post_link, post_time, post_content, blog_id, post_engage) values (593,"http://www.facebook.com/'.$part1.'/posts/'.$part2.'",'.$post_time.',"'.$post_content.'",'.intval($blog_id).','.intval($post['likes']['count']).')');
				$db->query('INSERT INTO blog_post (order_id, post_link, post_time, post_content, blog_id, post_engage) values (594,"http://www.facebook.com/'.$part1.'/posts/'.$part2.'",'.$post_time.',"'.$post_content.'",'.intval($blog_id).','.intval($post['likes']['count']).')');
				$db->query('INSERT INTO blog_post (order_id, post_link, post_time, post_content, blog_id, post_engage) values (595,"http://www.facebook.com/'.$part1.'/posts/'.$part2.'",'.$post_time.',"'.$post_content.'",'.intval($blog_id).','.intval($post['likes']['count']).')');
				$db->query('INSERT INTO blog_post (order_id, post_link, post_time, post_content, blog_id, post_engage) values (596,"http://www.facebook.com/'.$part1.'/posts/'.$part2.'",'.$post_time.',"'.$post_content.'",'.intval($blog_id).','.intval($post['likes']['count']).')');
				$db->query('INSERT INTO blog_post (order_id, post_link, post_time, post_content, blog_id, post_engage) values (597,"http://www.facebook.com/'.$part1.'/posts/'.$part2.'",'.$post_time.',"'.$post_content.'",'.intval($blog_id).','.intval($post['likes']['count']).')');
				$db->query('INSERT INTO blog_post (order_id, post_link, post_time, post_content, blog_id, post_engage) values (598,"http://www.facebook.com/'.$part1.'/posts/'.$part2.'",'.$post_time.',"'.$post_content.'",'.intval($blog_id).','.intval($post['likes']['count']).')');
				$db->query('INSERT INTO blog_post (order_id, post_link, post_time, post_content, blog_id, post_engage) values (599,"http://www.facebook.com/'.$part1.'/posts/'.$part2.'",'.$post_time.',"'.$post_content.'",'.intval($blog_id).','.intval($post['likes']['count']).')');
				$db->query('INSERT INTO blog_post (order_id, post_link, post_time, post_content, blog_id, post_engage) values (600,"http://www.facebook.com/'.$part1.'/posts/'.$part2.'",'.$post_time.',"'.$post_content.'",'.intval($blog_id).','.intval($post['likes']['count']).')');
				$db->query('INSERT INTO blog_post (order_id, post_link, post_time, post_content, blog_id, post_engage) values (601,"http://www.facebook.com/'.$part1.'/posts/'.$part2.'",'.$post_time.',"'.$post_content.'",'.intval($blog_id).','.intval($post['likes']['count']).')');
				$db->query('INSERT INTO blog_post (order_id, post_link, post_time, post_content, blog_id, post_engage) values (602,"http://www.facebook.com/'.$part1.'/posts/'.$part2.'",'.$post_time.',"'.$post_content.'",'.intval($blog_id).','.intval($post['likes']['count']).')');
				$db->query('INSERT INTO blog_post (order_id, post_link, post_time, post_content, blog_id, post_engage) values (603,"http://www.facebook.com/'.$part1.'/posts/'.$part2.'",'.$post_time.',"'.$post_content.'",'.intval($blog_id).','.intval($post['likes']['count']).')');
				*/
				echo ' '.$i;
				//echo $i.' '.$blog['order_id'].',"http://www.facebook.com/'.$part1.'/posts/'.$part2.'",'.$post_time.',"'.$post_content.'",'.intval($blog_id).','.intval($post['likes']['count'])."\n\n";
				$i++;
			}
			//echo ($i++).' '.$post_time.' '.$post_content."\n";


			$link2='https://graph.facebook.com/'.$post['id'].'&format=json&limit=75&access_token='.$token;
			echo '=='.$link2."\n";
			$out2=parseurl2($link2);

			$data2=json_decode($out2,true);
			
			foreach($data2['comments']['data'] as $comment)
			{
				/*
			    {
			      "id": "179467052099296_291004420939677_4087854", 
			      "from": {
			        "name": "Artem  Nevsky", 
			        "id": "100002597805091"
			      }, 
			      "message": "А где поподробнее можно узнать об этом не подскажете? У меня жена - сценарист, ее это заинтересовало", 
			      "created_time": "2011-11-24T08:49:38+0000"
			    },
				*/
				list($part1,$part2,$tmp)=explode('_',$comment['id'],3);
				$blog_id=getnicklink('http://www.facebook.com/'.$part1.'/posts/'.$part2);
				echo 'user:'.$blog_id.': http://www.facebook.com/'.$part1.'/posts/'.$part2."\n";
				if (strlen($comment['message'])>0) $post_content=addslashes($comment['message']);
				elseif (strlen($comment['description'])>0) $post_content=addslashes($comment['description']);
				elseif (strlen($comment['name'])>0) $comment_content=addslashes($comment['name']);
				elseif (strlen($comment['caption'])>0) $comment_content=addslashes($comment['caption']);
				$post_time=strtotime($comment['created_time']);
				$qw=$db->query('SELECT * FROM blog_post WHERE order_id='.$blog['order_id'].' and post_link=\''.addslashes('http://www.facebook.com/'.$part1.'/posts/'.$part2.'/'.$tmp).'\''); //проверка дублей

				if (($post_time>$blog['order_start'])&&($post_time<($blog['order_end']+86400))&&(mysql_num_rows($qw)==0))
				{
					$db->query('INSERT INTO blog_post (order_id, post_link, post_host, post_time, post_content, blog_id, post_engage) values ('.$blog['order_id'].',"http://www.facebook.com/'.$part1.'/posts/'.$part2.'/'.$tmp.'", "facebook.com", '.$post_time.',"'.$post_content.'",'.intval($blog_id).','.intval($post['likes']['count']).')');
					echo 'ok '.$i.' '.$blog['order_id'].',"http://www.facebook.com/'.$part1.'/posts/'.$part2.'/'.$tmp.'",'.$post_time.',"'.$post_content.'",'.intval($blog_id).','.intval($post['likes']['count'])."\n\n";
					$i++;
				}
			}
		}

		$next=$data['data']['paging']['next'];
		} while($next!='');

	}


if ($i>0)
{
$descriptorspec=array(
	0 => array("file","fb_rt_cash.log","a"),
	1 => array("file","fb_rt_cash.log","a"),
	2 => array("file","fb_rt_cash.log","a")
	);

$cwd='/var/www/bot';
$end=array();

$process=proc_open('php /var/www/bot/cashjob-spec.php '.$blog['order_id'],$descriptorspec,$pipes,$cwd,$end);

	if (is_resource($process))
	{
		$return_value=proc_close($process);
		//echo $return_value;
	}
}
}
//echo "idle...\n";
//sleep(600);
//}
//echo $link."\n";
//with date
//$link='https://graph.facebook.com/search?q='.urlencode($query).'&type=post&limit=75&'.$token.'&since=Tue+Aug+09+16%3A42%3A37+-0400+2011';
//echo $link."\n";





?>