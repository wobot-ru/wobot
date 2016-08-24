#!/usr/bin/php
<?
require_once('/var/www/com/config.php');
require_once('/var/www/com/func.php');
require_once('/var/www/com/db.php');
require_once('/var/www/bot/kernel.php');

error_reporting(E_ERROR | E_WARNING | E_PARSE);
ignore_user_abort(true);
set_time_limit(0);
ini_set('max_execution_time',0);
ini_set('default_charset','utf-8');
ob_implicit_flush();

$db = new database();
$db->connect();

function getnicklink($link)
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
	                                       $gg_id=$out['gg_id'][0];
	                                       //print_r($out);
	                                       if ($out['gg_id'][0]=="community")
	                                       {
	                                               $regexy="/com\/(?<ggg_id>.*?)\//is";
	                                               preg_match_all($regexy,$link,$outt);
	                                               //print_r($outt);
	                                               $gg_id=$outt['ggg_id'][0];
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
	                                       if ($out['vk_name'][0]=='')
	                                       {
	                                               $regexy="/id(?<vk_name>.*?)\?/is";
	                                               preg_match_all($regexy,$link,$out);
	                                       }
											$nick=$out['vk_name'][0];
                               }
							if ($nick!='')
							{
							$blg=$db->query('SELECT * FROM robot_blogs2 WHERE blog_link="'.$hn.'" and blog_login="'.$nick.'" LIMIT 1');
							if (mysql_num_rows($blg)==0)
							{
							        $db->query('INSERT INTO robot_blogs2 (blog_link, blog_login) values ("'.$hn.'","'.$nick.'")');
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


function object2array($object) { return @json_decode(@json_encode($object),1); } 

function parsepage($query,$day,$server,$page,$order_id)
{
	global $db,$links,$repeats,$perpage,$requests;
	
	$d=date('j',$day);
	$m=date('n',$day);
	$y=date('Y',$day);
	
	//echo "\n\n".$page."==========================================\n";
	$data=parseurl('http://blogs.yandex.ru/search.rss?text='.urlencode($query).'&numdoc=100&p='.intval($page).'&ft=all&server='.$server.'&from_day='.$d.'&from_month='.$m.'&from_year='.$y.'&to_day='.$d.'&to_month='.$m.'&to_year='.$y);
	$requests++;
	$xml = (array)simplexml_load_string($data);
	$json = object2array($xml);
	$pp=0;
	$rp=0;
	
	foreach($json['channel']['item'] as $item)
	{
			/*
		                            [author] => http://twitter.com/pcweek_ru
		                            [title] => 
		 Легализация торентов при помощи троянского коня Сегодня в Москве состоялась организованная
		                            [pubDate] => Tue, 27 Sep 2011 19:58:58 GMT
		                            [guid] => http://twitter.com/pcweek_ru/statuses/118776667022962688
		                            [link] => http://twitter.com/pcweek_ru/statuses/118776667022962688
		                            [description] => 
			*/		
		
		$item['title']=preg_replace('~\s{2,}~', ' ', $item['title']);
		$item['description']=preg_replace('~\s{2,}~', ' ', $item['description']);
		if(!in_array($item['link'], $links)) //не дубль
		{
			$links[]=$item['link'];//добавляем ссылку для проверки дублей
			$post_link=$item['link'];
			
			$item['title']=preg_replace('~\s{2,}~', ' ', $item['title']);//удаляем двойные пробелы
			$item['title'] = preg_replace('~\n{2,}~', ' ', $item['title']);//удаляем двойные энтеры
			$item['title'] = strip_tags($item['title']);//удаляем теги
			$item['title'] = trim($item['title']); //удаляем вайтспейсы вначале и вконце строки
			$item['description']=preg_replace('~\s{2,}~', ' ', $item['description']);//удаляем двойные пробелы	
			$item['description'] = preg_replace('~\n{2,}~', ' ', $item['description']);//удаляем двойные энтеры
			$item['description'] = strip_tags($item['description']);//удаляем теги
			$item['description'] = trim($item['description']); //удаляем вайтспейсы вначале и вконце строки
			
			$post_content=$item['title']."\n".$item['description'];//склеиваем тайтл и описание
			
			$post_time=strtotime($item['pubDate']);

			$hn=parse_url($post_link);
			$hn=$hn['host'];
			$ahn=explode('.',$hn);
			$post_host = $ahn[count($ahn)-2].'.'.$ahn[count($ahn)-1];

				//$blog_id=getnicklink($post_link);
				//$respost=$db->query('INSERT INTO blog_post (post_link, post_host,post_time,post_content,order_id,blog_id) values (\''.addslashes($item['link']).'\', \''.addslashes($post_host).'\',\''.intval($post_time).'\',\''.addslashes($post_content).'\','.intval($order_id).',\''.intval($blog_id).'\')');
				echo addslashes($item['link']).' | '.addslashes($post_host).' | '.date('r',$post_time).' | ['.addslashes($post_content)."]\n";

			//echo $item['link']."\n".$item['title']."\n".$item['description']."\n\n";			
		}
		else 
		{
			//echo "XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX\n";
			$repeats++;
			$rp++;
		}
		$pp++;
	}
	if($pp>$perpage) $perpage=$pp;
	if (($rp<$pp)&&($page<100)) parsepage($query,$day,$server,$page+1,$order_id);
}

function parseday($query,$day,$order_id)
{
	global $links,$repeats,$perpage,$requests;
	$repeats=0;
	parsepage($query,$day,'twitter.com',0,$order_id);
	echo "twitter:\trequests: ".$requests."\tcount: ".count($links)."\trepeats: ".$repeats."\tperpage: ".$perpage."\n";
	parsepage($query,$day,'livejournal.com',0,$order_id);
	echo "livejournal:\trequests: ".$requests."\tcount\t".count($links)."\trepeats: ".$repeats."\tperpage: ".$perpage."\n";
	parsepage($query,$day,'liveinternet.ru',0,$order_id);
	echo "liveinternet:\trequests: ".$requests."\tcount: ".count($links)."\trepeats: ".$repeats."\tperpage: ".$perpage."\n";
	parsepage($query,$day,'diary.ru',0,$order_id);
	echo "diary:\trequests: ".$requests."\tcount: ".count($links)."\trepeats: ".$repeats."\tperpage: ".$perpage."\n";
	parsepage($query,$day,'twitter.com%2C+liveinternet.ru%2C+diary.ru%2C+livejournal.com&x_server=on',0,$order_id);
	echo "other:\trequests: ".$requests."\tcount: ".count($links)."\trepeats: ".$repeats."\tperpage: ".$perpage."\n";
}



unset($links);
unset($repeats);
unset($perpage);
unset($requests);
$ddd=mktime(0,0,0,9,18,2011);
$query='(Кама&резина)|(Кама&покрышка)|(Кама&шина)|(Кама&колесо)|(Кама&диск)|(Кама&Ирбис)|(Кама&Евро)|(Кама&euro)';
//parseday($query,$ddd,0);
$page=0;

$data=parseurl('http://blogs.yandex.ru/search.rss?text='.urlencode($query).'&numdoc=100&p='.intval($page).'&ft=all&holdres=mark');
$requests++;
$xml = (array)simplexml_load_string($data);
$json = object2array($xml);
$pp=0;
$rp=0;
$links=array();

//print_r($json);
foreach($json['channel']['item'] as $item)
{
		/*
	                            [author] => http://twitter.com/pcweek_ru
	                            [title] => 
	 Легализация торентов при помощи троянского коня Сегодня в Москве состоялась организованная
	                            [pubDate] => Tue, 27 Sep 2011 19:58:58 GMT
	                            [guid] => http://twitter.com/pcweek_ru/statuses/118776667022962688
	                            [link] => http://twitter.com/pcweek_ru/statuses/118776667022962688
	                            [description] => 
		*/
	if(!in_array($item['link'], $links)) //не дубль
	{
		$links[]=$item['link'];
		$count++;
		$post_time=strtotime($item['pubDate']);
		if (($post_time>1314820800)&&($post_time<1316376000)) $countt++;
	}
	else
	{
		$repeats++;
	}
}

$page=1;

$data=parseurl('http://blogs.yandex.ru/search.rss?text='.urlencode($query).'&numdoc=100&p='.intval($page).'&ft=all&holdres=mark');
$requests++;
$xml = (array)simplexml_load_string($data);
$json = object2array($xml);
$pp=0;
$rp=0;

//print_r($json);
foreach($json['channel']['item'] as $item)
{
		/*
	                            [author] => http://twitter.com/pcweek_ru
	                            [title] => 
	 Легализация торентов при помощи троянского коня Сегодня в Москве состоялась организованная
	                            [pubDate] => Tue, 27 Sep 2011 19:58:58 GMT
	                            [guid] => http://twitter.com/pcweek_ru/statuses/118776667022962688
	                            [link] => http://twitter.com/pcweek_ru/statuses/118776667022962688
	                            [description] => 
		*/
		if(!in_array($item['link'], $links)) //не дубль
		{
			$links[]=$item['link'];
		$count++;
			$post_time=strtotime($item['pubDate']);
			if (($post_time>1314820800)&&($post_time<1316376000)) $countt++;
		}
		else
		{
			$repeats++;
		}
}

$page=2;

$data=parseurl('http://blogs.yandex.ru/search.rss?text='.urlencode($query).'&numdoc=100&p='.intval($page).'&ft=all&holdres=mark');
$requests++;
$xml = (array)simplexml_load_string($data);
$json = object2array($xml);
$pp=0;
$rp=0;

//print_r($json);
foreach($json['channel']['item'] as $item)
{
		/*
	                            [author] => http://twitter.com/pcweek_ru
	                            [title] => 
	 Легализация торентов при помощи троянского коня Сегодня в Москве состоялась организованная
	                            [pubDate] => Tue, 27 Sep 2011 19:58:58 GMT
	                            [guid] => http://twitter.com/pcweek_ru/statuses/118776667022962688
	                            [link] => http://twitter.com/pcweek_ru/statuses/118776667022962688
	                            [description] => 
		*/
		if(!in_array($item['link'], $links)) //не дубль
		{
			$links[]=$item['link'];
		$count++;
			$post_time=strtotime($item['pubDate']);
			if (($post_time>1314820800)&&($post_time<1316376000)) $countt++;
		}
		else
		{
			$repeats++;
		}
}


$page=3;

$data=parseurl('http://blogs.yandex.ru/search.rss?text='.urlencode($query).'&numdoc=100&p='.intval($page).'&ft=all&holdres=mark');
$requests++;
$xml = (array)simplexml_load_string($data);
$json = object2array($xml);
$pp=0;
$rp=0;

//print_r($json);
foreach($json['channel']['item'] as $item)
{
		/*
	                            [author] => http://twitter.com/pcweek_ru
	                            [title] => 
	 Легализация торентов при помощи троянского коня Сегодня в Москве состоялась организованная
	                            [pubDate] => Tue, 27 Sep 2011 19:58:58 GMT
	                            [guid] => http://twitter.com/pcweek_ru/statuses/118776667022962688
	                            [link] => http://twitter.com/pcweek_ru/statuses/118776667022962688
	                            [description] => 
		*/
		if(!in_array($item['link'], $links)) //не дубль
		{
			$links[]=$item['link'];
		$count++;
			$post_time=strtotime($item['pubDate']);
			if (($post_time>1314820800)&&($post_time<1316376000)) $countt++;
		}
		else
		{
			$repeats++;
		}
}


$page=4;

$data=parseurl('http://blogs.yandex.ru/search.rss?text='.urlencode($query).'&numdoc=100&p='.intval($page).'&ft=all&holdres=mark');
$requests++;
$xml = (array)simplexml_load_string($data);
$json = object2array($xml);
$pp=0;
$rp=0;

//print_r($json);
foreach($json['channel']['item'] as $item)
{
		/*
	                            [author] => http://twitter.com/pcweek_ru
	                            [title] => 
	 Легализация торентов при помощи троянского коня Сегодня в Москве состоялась организованная
	                            [pubDate] => Tue, 27 Sep 2011 19:58:58 GMT
	                            [guid] => http://twitter.com/pcweek_ru/statuses/118776667022962688
	                            [link] => http://twitter.com/pcweek_ru/statuses/118776667022962688
	                            [description] => 
		*/
		if(!in_array($item['link'], $links)) //не дубль
		{
			$links[]=$item['link'];
		$count++;
			$post_time=strtotime($item['pubDate']);
			if (($post_time>1314820800)&&($post_time<1316376000)) $countt++;
		}
		else
		{
			$repeats++;
		}
}

$page=5;

$data=parseurl('http://blogs.yandex.ru/search.rss?text='.urlencode($query).'&numdoc=100&p='.intval($page).'&ft=all&holdres=mark');
$requests++;
$xml = (array)simplexml_load_string($data);
$json = object2array($xml);
$pp=0;
$rp=0;
//http://blogs.yandex.ru/search.rss?text=%D1%83|%D0%B5|%D1%8B|%D0%B0|%D0%BE|%D1%8D|%D1%8F|%D0%B8&ft=all&from_day=03&from_month=10&from_year=2011&to_day=03&to_month=10&to_year=2011&p=1&numdoc=100
//print_r($json);
foreach($json['channel']['item'] as $item)
{
		/*
	                            [author] => http://twitter.com/pcweek_ru
	                            [title] => 
	 Легализация торентов при помощи троянского коня Сегодня в Москве состоялась организованная
	                            [pubDate] => Tue, 27 Sep 2011 19:58:58 GMT
	                            [guid] => http://twitter.com/pcweek_ru/statuses/118776667022962688
	                            [link] => http://twitter.com/pcweek_ru/statuses/118776667022962688
	                            [description] => 
		*/
		if(!in_array($item['link'], $links)) //не дубль
		{
			$links[]=$item['link'];
		$count++;
			$post_time=strtotime($item['pubDate']);
			if (($post_time>1314820800)&&($post_time<1316376000)) $countt++;
		}
		else
		{
			$repeats++;
		}
}

$page=6;

$data=parseurl('http://blogs.yandex.ru/search.rss?text='.urlencode($query).'&numdoc=100&p='.intval($page).'&ft=all&holdres=mark');
$requests++;
$xml = (array)simplexml_load_string($data);
$json = object2array($xml);
$pp=0;
$rp=0;

//print_r($json);
foreach($json['channel']['item'] as $item)
{
		/*
	                            [author] => http://twitter.com/pcweek_ru
	                            [title] => 
	 Легализация торентов при помощи троянского коня Сегодня в Москве состоялась организованная
	                            [pubDate] => Tue, 27 Sep 2011 19:58:58 GMT
	                            [guid] => http://twitter.com/pcweek_ru/statuses/118776667022962688
	                            [link] => http://twitter.com/pcweek_ru/statuses/118776667022962688
	                            [description] => 
		*/
		if(!in_array($item['link'], $links)) //не дубль
		{
			$links[]=$item['link'];
		$count++;
			$post_time=strtotime($item['pubDate']);
			if (($post_time>1314820800)&&($post_time<1316376000)) $countt++;
		}
		else
		{
			$repeats++;
		}
}

$page=7;

$data=parseurl('http://blogs.yandex.ru/search.rss?text='.urlencode($query).'&numdoc=100&p='.intval($page).'&ft=all&holdres=mark');
$requests++;
$xml = (array)simplexml_load_string($data);
$json = object2array($xml);
$pp=0;
$rp=0;

//print_r($json);
foreach($json['channel']['item'] as $item)
{
		/*
	                            [author] => http://twitter.com/pcweek_ru
	                            [title] => 
	 Легализация торентов при помощи троянского коня Сегодня в Москве состоялась организованная
	                            [pubDate] => Tue, 27 Sep 2011 19:58:58 GMT
	                            [guid] => http://twitter.com/pcweek_ru/statuses/118776667022962688
	                            [link] => http://twitter.com/pcweek_ru/statuses/118776667022962688
	                            [description] => 
		*/
		if(!in_array($item['link'], $links)) //не дубль
		{
			$links[]=$item['link'];
		$count++;
			$post_time=strtotime($item['pubDate']);
			if (($post_time>1314820800)&&($post_time<1316376000)) $countt++;
		}
		else
		{
			$repeats++;
		}
}

echo "all:\trequests: ".$requests."\tcount: ".$countt."/".$count."\trepeats: ".$repeats."\tperpage: ".$perpage."\n\n";



/*
$ressec=$db->query('SELECT * FROM blog_orders WHERE (order_last<='.mktime(0,0,0,date("n"),date("j"),date("Y")).' or (order_last=0 and order_start<='.mktime(0,0,0,date("n"),date("j"),date("Y")).')) and (order_last<=order_end or order_end=0) ORDER BY order_id DESC');

echo 'new orders to parse: '.mysql_num_rows($ressec)."\n";
$mode='wb';
while($blog=$db->fetch($ressec))
{
	$query = $blog['order_keyword'];
	echo $blog['order_keyword'].' - '.$blog['order_id']."\n";
	
	if ($blog['order_last']>=$blog['order_start'])
	{
		if ($blog['order_last']!=0) $mstart=$blog['order_last'];
		else $mstart = $blog['order_start'];
	}
	else
	{
		$mstart=$blog['order_start'];
	}
	if ($blog['order_end']>=mktime(0,0,0,date("n"),date("j"),date("Y")))
	{
		$mend=mktime(0,0,0,date("n"),date("j")-1,date("Y"));
	}
	else
	{
		if ($blog['order_end']!=0) $mend=$blog['order_end'];
		else $mend=mktime(0,0,0,date("n"),date("j")-1,date("Y"));
	}

	for ($ddd=$mstart; $ddd<=$mend; $ddd=mktime(0,0,0,date("n",$ddd),date("j",$ddd)+1,date("Y",$ddd))) 
	{
		echo date("H:i:s d.m.Y",$ddd).' '.$blog['order_id']."\n";
		
		unset($links);
		unset($repeats);
		unset($perpage);
		unset($requests);
		parseday($query,$ddd,$blog['order_id']);
		echo "all:\trequests: ".$requests."\tcount: ".count($links)."\trepeats: ".$repeats."\tperpage: ".$perpage."\n\n";
	}

	//отчет обновлен
	$db->query('UPDATE blog_orders SET order_last='.mktime(0,0,0,date("m"),date("d"),date("Y")).' WHERE order_id='.$blog['order_id']);




	//new cashjob update
	$descriptorspec = array(
	   0 => array("file", "/var/www/bot/cashjob".intval($blog['order_id']).".log", "a"),  // stdin is a pipe that the child will read from
	   1 => array("file", "/var/www/bot/cashjob".intval($blog['order_id']).".log", "a"),  // stdout is a pipe that the child will write to
	   2 => array("file", "/var/www/bot/cashjob".intval($blog['order_id']).".log", "a") // stderr is a file to write to
	);


	$cwd = '/var/www/bot';
	$env = array();

	$process = proc_open('php /var/www/bot/cashjob-spec.php '.intval($blog['order_id']).' &', $descriptorspec, $pipes, $cwd, $env);

	if (is_resource($process)) {
	    $return_value = proc_close($process);
	    echo "cashjob return $return_value\n";
	}
	//endof new cashjob update
	sleep(1);



	unset($blog);
}
*/

?>