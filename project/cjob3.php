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
date_default_timezone_set ( 'Europe/Moscow' );

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



function parseday($query,$ts,$order_id)
{
global $nickloc,$times,$timet,$timeb,$locales,$xmlout,$loc,$db;
//echo $query.' ['.date('r',$ts)."]\n";

echo "twitter.com: ";
$link='http://blogs.yandex.ru/search.xml?text='.urlencode($query).'&ft=all&server=twitter.com&from_day='.date('d',$ts).'&from_month='.date('m',$ts).'&from_year='.date('Y',$ts).'&to_day='.date('d',$ts).'&to_month='.date('m',$ts).'&to_year='.date('Y',$ts).'&holdres=mark&numdoc=100&p=';
$numdoc=0;
$count=0;

do {
$isnext=0;

$content=parseURL($link.intval($numdoc));
if ((intval(mb_strpos($content,'Извините, сервис временно недоступен.',0,'UTF-8'))!=0))
{
	echo ' !server don\'t work:';
for($rcount=1;$rcount<=3;$rcount++)
{
    $content=parseURL($link.intval($numdoc));
	if ((intval(mb_strpos($content,'Извините, сервис временно недоступен.',0,'UTF-8'))==0))
	{
		echo ' '.$rcount.'*';
		break;
	}
	else {
		echo ' '.$rcount;
		usleep(5000000);
	}
}
}
echo "!";

echo "[p:".intval($numdoc)."]";
        $dom = new DomDocument;
        $res = @$dom->loadHTML($content);

$divs = $dom->getElementsByTagName("a");
foreach($divs as $div) {    
    if ($div->getAttribute("class")=="b-pager__next")
    {
	    //$link=$div->getAttribute("href");
	    $isnext=1;
	    $numdoc++;
	//echo 'next founded'."\n<div class=\"b-error\">";
	    break;
    }
}



$tables = $dom->getElementsByTagName("a");
$i=0;
foreach($tables as $table) {
    $element = $table;
    count($element);
    if ($element->getAttribute("class")==" SearchStatistics-link")
    {
	//detecting city
	    //$nickloc[$i]=getcity($element->getAttribute("href"));
	    list($nick,$loc)=$nickloc[$i];
       	    $time=$element->parentNode->nextSibling->nextSibling->firstChild->textContent;
	    $mon2int=array(
'января'=>1,
'февраля'=>2,
'марта'=>3,
'апреля'=>4,
'мая'=>5,
'июня'=>6,
'июля'=>7,
'августа'=>8,
'сентября'=>9,
'октября'=>10,
'ноября'=>11,
'декабря'=>12
);
	    list($day,$mon,$year,$tmp)=explode(" ",$time,4);
	    $mon=$mon2int[$mon];
	    $year=str_replace(',','',$year);
	    if ($day=='вчера,')
	    {
	        $day=date("j",mktime(0, 0, 0, date("m"),date("d")-1,date("Y")));
	        $mon=date("n",mktime(0, 0, 0, date("m"),date("d")-1,date("Y")));
	        $year=date("Y",mktime(0, 0, 0, date("m"),date("d")-1,date("Y")));
	    }elseif ($year=='назад' || $mon=='')
	    {
	        $day=date("j");
	        $mon=date("n");
	        $year=date("Y");
	    }
	    $times[$i]=array($day,$mon,$year);
	    $timet[$year][$mon][$day]++;
	    if (strpos($element->getAttribute("href"), 'livejournal.com')) $domain='livejournal.com';
	    elseif (strpos($element->getAttribute("href"), 'twitter.com')) $domain='twitter.com';
	    elseif (strpos($element->getAttribute("href"), 'ya.ru')) $domain='ya.ru';
	    elseif (strpos($element->getAttribute("href"), 'qip.ru')) $domain='qip.ru';
	    elseif (strpos($element->getAttribute("href"), 'rutvit.ru')) $domain='rutvit.ru';
	    else $domain='default';

	    $timeb[$domain][$year][$mon][$day]++;
	    $locales[$loc].='<a href="'.$element->getAttribute("href").'">'.str_replace("<br>","",str_replace("\n","",$nick)).'</a> '.str_replace("<br>","",str_replace("\n","",$element->textContent)).'<br>';
		
		/* Adding to blog_post */
		$blog_id=getnicklink($element->getAttribute("href"));
		$respost=$db->query('INSERT INTO blog_post (post_link,post_time,post_content,order_id,blog_id) values (\''.addslashes($element->getAttribute("href")).'\',\''.mktime(0,0,0,$mon,$day,$year).'\',\''.addslashes($element->textContent).'\','.intval($order_id).',\''.intval($blog_id).'\')');
            $xmlout.= '
<post>
<link>'.$element->getAttribute("href").'</link>
<time>'.mktime(0,0,0,$mon,$day,$year).'</time>
<content>'.($element->textContent).'</content>
<nick>'.$nick.'</nick>
<loc>'.$loc.'</loc>
</post>';

            $i++;
			$count++;
    }
}
usleep(1000000);
			echo "[c:".$count."]\n";
} while($isnext && $numdoc<10);





echo "livejournal.com: ";

$link='http://blogs.yandex.ru/search.xml?text='.urlencode($query).'&ft=all&server=livejournal.com&from_day='.date('d',$ts).'&from_month='.date('m',$ts).'&from_year='.date('Y',$ts).'&to_day='.date('d',$ts).'&to_month='.date('m',$ts).'&to_year='.date('Y',$ts).'&holdres=mark&numdoc=100&p=';
$numdoc=0;

do {
$isnext=0;

$content=parseURL($link.intval($numdoc));
if ((intval(mb_strpos($content,'Извините, сервис временно недоступен.',0,'UTF-8'))!=0))
{
	echo ' !server don\'t work:';
for($rcount=1;$rcount<=3;$rcount++)
{
    $content=parseURL($link.intval($numdoc));
	if ((intval(mb_strpos($content,'Извините, сервис временно недоступен.',0,'UTF-8'))==0))
	{
		echo ' '.$rcount.'*';
		break;
	}
	else {
		echo ' '.$rcount;
		usleep(5000000);
	}
}
}
echo "!";

	echo "[p:".intval($numdoc)."]";

        $dom = new DomDocument;
        $res = @$dom->loadHTML($content);

$divs = $dom->getElementsByTagName("a");
foreach($divs as $div) {    
    if ($div->getAttribute("class")=="b-pager__next")
    {
	    //$link=$div->getAttribute("href");
	    $isnext=1;
	    $numdoc++;
	    break;
    }
}



$tables = $dom->getElementsByTagName("a");
$i=0;
foreach($tables as $table) {
    $element = $table;
    count($element);
    if ($element->getAttribute("class")==" SearchStatistics-link")
    {
	//detecting city
	    //$nickloc[$i]=getcity($element->getAttribute("href"));
	    list($nick,$loc)=$nickloc[$i];
       	    $time=$element->parentNode->nextSibling->nextSibling->firstChild->textContent;
	    $mon2int=array(
'января'=>1,
'февраля'=>2,
'марта'=>3,
'апреля'=>4,
'мая'=>5,
'июня'=>6,
'июля'=>7,
'августа'=>8,
'сентября'=>9,
'октября'=>10,
'ноября'=>11,
'декабря'=>12
);
	    list($day,$mon,$year,$tmp)=explode(" ",$time,4);
	    $mon=$mon2int[$mon];
	    $year=str_replace(',','',$year);
	    if ($day=='вчера,')
	    {
	        $day=date("j",mktime(0, 0, 0, date("m"),date("d")-1,date("Y")));
	        $mon=date("n",mktime(0, 0, 0, date("m"),date("d")-1,date("Y")));
	        $year=date("Y",mktime(0, 0, 0, date("m"),date("d")-1,date("Y")));
	    }elseif ($year=='назад' || $mon=='')
	    {
	        $day=date("j");
	        $mon=date("n");
	        $year=date("Y");
	    }
	    $times[$i]=array($day,$mon,$year);
	    $timet[$year][$mon][$day]++;
	    if (strpos($element->getAttribute("href"), 'livejournal.com')) $domain='livejournal.com';
	    elseif (strpos($element->getAttribute("href"), 'twitter.com')) $domain='twitter.com';
	    elseif (strpos($element->getAttribute("href"), 'ya.ru')) $domain='ya.ru';
	    elseif (strpos($element->getAttribute("href"), 'qip.ru')) $domain='qip.ru';
	    elseif (strpos($element->getAttribute("href"), 'rutvit.ru')) $domain='rutvit.ru';
	    else $domain='default';

	    $timeb[$domain][$year][$mon][$day]++;
	    $locales[$loc].='<a href="'.$element->getAttribute("href").'">'.str_replace("<br>","",str_replace("\n","",$nick)).'</a> '.str_replace("<br>","",str_replace("\n","",$element->textContent)).'<br>';
		
		/* Adding to blog_post */
		$blog_id=getnicklink($element->getAttribute("href"));
		$respost=$db->query('INSERT INTO blog_post (post_link,post_time,post_content,order_id,blog_id) values (\''.addslashes($element->getAttribute("href")).'\',\''.mktime(0,0,0,$mon,$day,$year).'\',\''.addslashes($element->textContent).'\','.intval($order_id).',\''.intval($blog_id).'\')');
            $xmlout.= '
<post>
<link>'.$element->getAttribute("href").'</link>
<time>'.mktime(0,0,0,$mon,$day,$year).'</time>
<content>'.($element->textContent).'</content>
<nick>'.$nick.'</nick>
<loc>'.$loc.'</loc>
</post>';

            $i++;
			$count++;
    }
}
			echo "[c:".$count."]\n";
usleep(500000);
} while($isnext && $numdoc<10);








echo "liveinternet.ru: ";
$link='http://blogs.yandex.ru/search.xml?text='.urlencode($query).'&ft=all&server=liveinternet.ru&from_day='.date('d',$ts).'&from_month='.date('m',$ts).'&from_year='.date('Y',$ts).'&to_day='.date('d',$ts).'&to_month='.date('m',$ts).'&to_year='.date('Y',$ts).'&holdres=mark&numdoc=100&p=';
$numdoc=0;

do {
$isnext=0;

$content=parseURL($link.intval($numdoc));
if ((intval(mb_strpos($content,'Извините, сервис временно недоступен.',0,'UTF-8'))!=0))
{
	echo '!server don\'t work:';
for($rcount=1;$rcount<=3;$rcount++)
{
    $content=parseURL($link.intval($numdoc));
	if ((intval(mb_strpos($content,'Извините, сервис временно недоступен.',0,'UTF-8'))==0))
	{
		echo ' '.$rcount.'*';
		break;
	}
	else {
		echo ' '.$rcount;
		usleep(5000000);
	}
}
}
echo "!";

	echo "[p:".intval($numdoc)."]";

        $dom = new DomDocument;
        $res = @$dom->loadHTML($content);

$divs = $dom->getElementsByTagName("a");
foreach($divs as $div) {    
    if ($div->getAttribute("class")=="b-pager__next")
    {
	    //$link=$div->getAttribute("href");
	    $isnext=1;
	    $numdoc++;
	    break;
    }
}



$tables = $dom->getElementsByTagName("a");
$i=0;
foreach($tables as $table) {
    $element = $table;
    count($element);
    if ($element->getAttribute("class")==" SearchStatistics-link")
    {
	//detecting city
	    //$nickloc[$i]=getcity($element->getAttribute("href"));
	    list($nick,$loc)=$nickloc[$i];
       	    $time=$element->parentNode->nextSibling->nextSibling->firstChild->textContent;
	    $mon2int=array(
'января'=>1,
'февраля'=>2,
'марта'=>3,
'апреля'=>4,
'мая'=>5,
'июня'=>6,
'июля'=>7,
'августа'=>8,
'сентября'=>9,
'октября'=>10,
'ноября'=>11,
'декабря'=>12
);
	    list($day,$mon,$year,$tmp)=explode(" ",$time,4);
	    $mon=$mon2int[$mon];
	    $year=str_replace(',','',$year);
	    if ($day=='вчера,')
	    {
	        $day=date("j",mktime(0, 0, 0, date("m"),date("d")-1,date("Y")));
	        $mon=date("n",mktime(0, 0, 0, date("m"),date("d")-1,date("Y")));
	        $year=date("Y",mktime(0, 0, 0, date("m"),date("d")-1,date("Y")));
	    }elseif ($year=='назад' || $mon=='')
	    {
	        $day=date("j");
	        $mon=date("n");
	        $year=date("Y");
	    }
	    $times[$i]=array($day,$mon,$year);
	    $timet[$year][$mon][$day]++;
	    if (strpos($element->getAttribute("href"), 'livejournal.com')) $domain='livejournal.com';
	    elseif (strpos($element->getAttribute("href"), 'twitter.com')) $domain='twitter.com';
	    elseif (strpos($element->getAttribute("href"), 'ya.ru')) $domain='ya.ru';
	    elseif (strpos($element->getAttribute("href"), 'qip.ru')) $domain='qip.ru';
	    elseif (strpos($element->getAttribute("href"), 'rutvit.ru')) $domain='rutvit.ru';
	    else $domain='default';

	    $timeb[$domain][$year][$mon][$day]++;
	    $locales[$loc].='<a href="'.$element->getAttribute("href").'">'.str_replace("<br>","",str_replace("\n","",$nick)).'</a> '.str_replace("<br>","",str_replace("\n","",$element->textContent)).'<br>';
		
		/* Adding to blog_post */
		$blog_id=getnicklink($element->getAttribute("href"));
		$respost=$db->query('INSERT INTO blog_post (post_link,post_time,post_content,order_id,blog_id) values (\''.addslashes($element->getAttribute("href")).'\',\''.mktime(0,0,0,$mon,$day,$year).'\',\''.addslashes($element->textContent).'\','.intval($order_id).',\''.intval($blog_id).'\')');
            $xmlout.= '
<post>
<link>'.$element->getAttribute("href").'</link>
<time>'.mktime(0,0,0,$mon,$day,$year).'</time>
<content>'.($element->textContent).'</content>
<nick>'.$nick.'</nick>
<loc>'.$loc.'</loc>
</post>';

            $i++;
			$count++;
    }
}
			echo "[c:".$count."]\n";
usleep(500000);
} while($isnext && $numdoc<10);









echo "diary.ru: ";
$link='http://blogs.yandex.ru/search.xml?text='.urlencode($query).'&ft=all&server=diary.ru&from_day='.date('d',$ts).'&from_month='.date('m',$ts).'&from_year='.date('Y',$ts).'&to_day='.date('d',$ts).'&to_month='.date('m',$ts).'&to_year='.date('Y',$ts).'&holdres=mark&numdoc=100&p=';
$numdoc=0;

do {
$isnext=0;

$content=parseURL($link.intval($numdoc));
if ((intval(mb_strpos($content,'Извините, сервис временно недоступен.',0,'UTF-8'))!=0))
{
	echo '!server don\'t work:';
for($rcount=1;$rcount<=3;$rcount++)
{
    $content=parseURL($link.intval($numdoc));
	if ((intval(mb_strpos($content,'Извините, сервис временно недоступен.',0,'UTF-8'))==0))
	{
		echo ' '.$rcount.'*';
		break;
	}
	else {
		echo ' '.$rcount;
		usleep(5000000);
	}
}
}
echo "!";

	echo "[p:".intval($numdoc)."]";

        $dom = new DomDocument;
        $res = @$dom->loadHTML($content);

$divs = $dom->getElementsByTagName("a");
foreach($divs as $div) {    
    if ($div->getAttribute("class")=="b-pager__next")
    {
	    //$link=$div->getAttribute("href");
	    $isnext=1;
	    $numdoc++;
	    break;
    }
}



$tables = $dom->getElementsByTagName("a");
$i=0;
foreach($tables as $table) {
    $element = $table;
    count($element);
    if ($element->getAttribute("class")==" SearchStatistics-link")
    {
	//detecting city
	    //$nickloc[$i]=getcity($element->getAttribute("href"));
	    list($nick,$loc)=$nickloc[$i];
       	    $time=$element->parentNode->nextSibling->nextSibling->firstChild->textContent;
	    $mon2int=array(
'января'=>1,
'февраля'=>2,
'марта'=>3,
'апреля'=>4,
'мая'=>5,
'июня'=>6,
'июля'=>7,
'августа'=>8,
'сентября'=>9,
'октября'=>10,
'ноября'=>11,
'декабря'=>12
);
	    list($day,$mon,$year,$tmp)=explode(" ",$time,4);
	    $mon=$mon2int[$mon];
	    $year=str_replace(',','',$year);
	    if ($day=='вчера,')
	    {
	        $day=date("j",mktime(0, 0, 0, date("m"),date("d")-1,date("Y")));
	        $mon=date("n",mktime(0, 0, 0, date("m"),date("d")-1,date("Y")));
	        $year=date("Y",mktime(0, 0, 0, date("m"),date("d")-1,date("Y")));
	    }elseif ($year=='назад' || $mon=='')
	    {
	        $day=date("j");
	        $mon=date("n");
	        $year=date("Y");
	    }
	    $times[$i]=array($day,$mon,$year);
	    $timet[$year][$mon][$day]++;
	    if (strpos($element->getAttribute("href"), 'livejournal.com')) $domain='livejournal.com';
	    elseif (strpos($element->getAttribute("href"), 'twitter.com')) $domain='twitter.com';
	    elseif (strpos($element->getAttribute("href"), 'ya.ru')) $domain='ya.ru';
	    elseif (strpos($element->getAttribute("href"), 'qip.ru')) $domain='qip.ru';
	    elseif (strpos($element->getAttribute("href"), 'rutvit.ru')) $domain='rutvit.ru';
	    else $domain='default';

	    $timeb[$domain][$year][$mon][$day]++;
	    $locales[$loc].='<a href="'.$element->getAttribute("href").'">'.str_replace("<br>","",str_replace("\n","",$nick)).'</a> '.str_replace("<br>","",str_replace("\n","",$element->textContent)).'<br>';
		
		/* Adding to blog_post */
		$blog_id=getnicklink($element->getAttribute("href"));
		$respost=$db->query('INSERT INTO blog_post (post_link,post_time,post_content,order_id,blog_id) values (\''.addslashes($element->getAttribute("href")).'\',\''.mktime(0,0,0,$mon,$day,$year).'\',\''.addslashes($element->textContent).'\','.intval($order_id).',\''.intval($blog_id).'\')');
            $xmlout.= '
<post>
<link>'.$element->getAttribute("href").'</link>
<time>'.mktime(0,0,0,$mon,$day,$year).'</time>
<content>'.($element->textContent).'</content>
<nick>'.$nick.'</nick>
<loc>'.$loc.'</loc>
</post>';

            $i++;
			$count++;
    }
}
			echo "[c:".$count."]\n";
usleep(500000);
} while($isnext && $numdoc<10);










echo "other: ";
$link='http://blogs.yandex.ru/search.xml?text='.urlencode($query).'&ft=all&server=twitter.com%2C+liveinternet.ru%2C+diary.ru%2C+livejournal.com&x_server=on&from_day='.date('d',$ts).'&from_month='.date('m',$ts).'&from_year='.date('Y',$ts).'&to_day='.date('d',$ts).'&to_month='.date('m',$ts).'&to_year='.date('Y',$ts).'&holdres=mark&numdoc=100&p=';
$numdoc=0;

do {
$isnext=0;

$content=parseURL($link.intval($numdoc));
if ((intval(mb_strpos($content,'Извините, сервис временно недоступен.',0,'UTF-8'))!=0))
{
	echo '!server don\'t work:';
for($rcount=1;$rcount<=3;$rcount++)
{
    $content=parseURL($link.intval($numdoc));
	if ((intval(mb_strpos($content,'Извините, сервис временно недоступен.',0,'UTF-8'))==0))
	{
		echo ' '.$rcount.'*';
		break;
	}
	else {
		echo ' '.$rcount;
		usleep(5000000);
	}
}
}
echo "!";

	echo "[p:".intval($numdoc)."]";

        $dom = new DomDocument;
        $res = @$dom->loadHTML($content);

$divs = $dom->getElementsByTagName("a");
foreach($divs as $div) {    
    if ($div->getAttribute("class")=="b-pager__next")
    {
	    //$link=$div->getAttribute("href");
	    $isnext=1;
	    $numdoc++;
	    break;
    }
}



$tables = $dom->getElementsByTagName("a");
$i=0;
foreach($tables as $table) {
    $element = $table;
    count($element);
    if ($element->getAttribute("class")==" SearchStatistics-link")
    {
	//detecting city
	    //$nickloc[$i]=getcity($element->getAttribute("href"));
	    list($nick,$loc)=$nickloc[$i];
       	    $time=$element->parentNode->nextSibling->nextSibling->firstChild->textContent;
	    $mon2int=array(
'января'=>1,
'февраля'=>2,
'марта'=>3,
'апреля'=>4,
'мая'=>5,
'июня'=>6,
'июля'=>7,
'августа'=>8,
'сентября'=>9,
'октября'=>10,
'ноября'=>11,
'декабря'=>12
);
	    list($day,$mon,$year,$tmp)=explode(" ",$time,4);
	    $mon=$mon2int[$mon];
	    $year=str_replace(',','',$year);
	    if ($day=='вчера,')
	    {
	        $day=date("j",mktime(0, 0, 0, date("m"),date("d")-1,date("Y")));
	        $mon=date("n",mktime(0, 0, 0, date("m"),date("d")-1,date("Y")));
	        $year=date("Y",mktime(0, 0, 0, date("m"),date("d")-1,date("Y")));
	    }elseif ($year=='назад' || $mon=='')
	    {
	        $day=date("j");
	        $mon=date("n");
	        $year=date("Y");
	    }
	    $times[$i]=array($day,$mon,$year);
	    $timet[$year][$mon][$day]++;
	    if (strpos($element->getAttribute("href"), 'livejournal.com')) $domain='livejournal.com';
	    elseif (strpos($element->getAttribute("href"), 'twitter.com')) $domain='twitter.com';
	    elseif (strpos($element->getAttribute("href"), 'ya.ru')) $domain='ya.ru';
	    elseif (strpos($element->getAttribute("href"), 'qip.ru')) $domain='qip.ru';
	    elseif (strpos($element->getAttribute("href"), 'rutvit.ru')) $domain='rutvit.ru';
	    else $domain='default';

	    $timeb[$domain][$year][$mon][$day]++;
	    $locales[$loc].='<a href="'.$element->getAttribute("href").'">'.str_replace("<br>","",str_replace("\n","",$nick)).'</a> '.str_replace("<br>","",str_replace("\n","",$element->textContent)).'<br>';
		
		/* Adding to blog_post */
		$blog_id=getnicklink($element->getAttribute("href"));
		$respost=$db->query('INSERT INTO blog_post (post_link,post_time,post_content,order_id,blog_id) values (\''.addslashes($element->getAttribute("href")).'\',\''.mktime(0,0,0,$mon,$day,$year).'\',\''.addslashes($element->textContent).'\','.intval($order_id).',\''.intval($blog_id).'\')');
            $xmlout.= '
<post>
<link>'.$element->getAttribute("href").'</link>
<time>'.mktime(0,0,0,$mon,$day,$year).'</time>
<content>'.($element->textContent).'</content>
<nick>'.$nick.'</nick>
<loc>'.$loc.'</loc>
</post>';

            $i++;
			$count++;
    }
}
			echo "[c:".$count."]\n";
usleep(500000);
} while($isnext && $numdoc<10);







echo $count;
}

//parseday('Собянин | сергей семенович | (мэр столицы ~~ (мэр столицы /(-3 +3) Лужков ~~ собянин) ~~ (мэр столицы /(-3 +3) батурина ~~ собянин)) | (мэр москвы ~~ (мэр москвы /(-3 +3) Лужков ~~ собянин) ~~ (мэр москвы /(-3 +3) батурина ~~ собянин)) | (столичный градоначальник ~~ (столичный градоначальник /(-3 +3) Лужков ~~ собянин) ~~ (столичный градоначальник /(-3 +3) батурина ~~ собянин))',mktime(0,0,0,3,2,2011),0);









//$ordid='436';
//$db->query('DELETE FROM blog_post WHERE order_id='.$ordid);
//$db->query('UPDATE blog_orders SET order_last=0 WHERE order_id='.$ordid);
//$ressec=$db->query('SELECT * FROM blog_orders WHERE (order_id='.$ordid.')');
//$ressec=$db->query('SELECT * FROM blog_orders WHERE order_last<'.mktime(0,0,0,date("n"),date("j"),date("Y")).' and (order_last<=order_end or order_end=0) and (order_id='.$order_id.')');
//$ressec=$db->query('SELECT * FROM blog_orders WHERE (order_last<='.mktime(0,0,0,date("n"),date("j"),date("Y")).' or (order_last=0 and order_start<='.mktime(0,0,0,date("n"),date("j"),date("Y")).')) and (order_last<=order_end or order_end=0)');
$ressec=$db->query('SELECT * FROM blog_orders WHERE (order_last<='.mktime(0,0,0,date("n"),date("j"),date("Y")).' or (order_last=0 and order_start<='.mktime(0,0,0,date("n"),date("j"),date("Y")).')) and (order_last<=order_end or order_end=0)');

//$ressec=$db->query('SELECT * FROM blog_orders');
echo 'new orders to parse: '.mysql_num_rows($ressec)."\n";
$mode='wb';
while($blog=$db->fetch($ressec))
{
$query = $blog['order_keyword'];
echo $blog['order_keyword'].' - '.$blog['order_id']."\n";

/*if ($blog['order_last']>=$blog['order_start'])
{
	$mstart=$blog['order_last'];
}
else
{
	$mstart=$blog['order_start'];
}
if ($blog['order_end']>=mktime(0,0,0,date("n"),date("j"),date("Y")))
{
	$mend=mktime(0,0,0,date("n"),date("j"),date("Y"));
}
else
{
	$mend=$blog['order_end'];
}*/
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
//for ($ddd=0;$ddd<30;$ddd++) parseday($query,$ddd,$ddd,0,0);
/*$blog['order_start']=mktime(0,0,0,date("m",$blog['order_start']),date("d",$blog['order_start']),date("Y",$blog['order_start']));
if ($blog['order_end']==0) $blog['order_end']=mktime(0,0,0,date("m"),date("d")-1,date("Y"));
$blog['order_end']=mktime(0,0,0,date("m",$blog['order_end']),date("d",$blog['order_end']),date("Y",$blog['order_end']));
if ($blog['order_last']==0) $blog['order_last']=$blog['order_start'];
else $mode='ab';
$blog['order_last']=mktime(0,0,0,date("m",$blog['order_last']),date("d",$blog['order_last']),date("Y",$blog['order_last']));
*/
//for ($ddd=$blog['order_last']; $ddd<=$blog['order_end']; $ddd+=60*60*24) 
for ($ddd=$mstart; $ddd<=$mend; $ddd=mktime(0,0,0,date("n",$ddd),date("j",$ddd)+1,date("Y",$ddd))) 
{
echo date("H:i:s d.m.Y",$ddd).' '.$blog['order_id']."\n";
parseday($query,$ddd,$blog['order_id']);
}

$fp = fopen('/var/www/data/blog/'.$blog['order_id'].'.xml', $mode);
fwrite($fp, $xmlout);
fclose($fp);
$xmlout='';
unset($nickloc);
unset($times);
unset($timet);
unset($timeb);

/*$dstnind=0;
foreach ($destn as $ct => $dstn)
{
	if ($dstnind==1) echo ',';
	else $dstnind=1;
	echo '
        \''.$ct.'\' : new YMaps.GeoPoint('.$dstn[0].','.$dstn[1].')';
}*/



//generating mapblock file /////////////////////////////////////////////////////////////////////////
foreach($locales as $loc => $localeb) //mustbe with all post (not only last day)
{
if (array_key_exists($loc,$wobot['destn'])) $mapout.='            var moscow = new YMaps.Placemark(destinations[\''.$loc.'\'], {style: \'default#whitePoint\'});
            moscow.setBalloonContent(\''.(str_replace("\n","",str_replace("'","\'",nl2br($localeb)))).'\');
            map.addOverlay(moscow);
            moscow.setIconContent(\'\');

';
}
$locales='';
$loc='';
$fp = fopen('/var/www/data/blog/'.$blog['order_id'].'.map', 'wb');
fwrite($fp, $mapout);
fclose($fp);
$mapout='';
$db->query('UPDATE blog_orders SET order_last='.mktime(0,0,0,date("m"),date("d"),date("Y")).' WHERE order_id='.$blog['order_id']);
//endof map block file /////////////////////////////////////////////////////////////////////////



//opening post file to generate cash files ////////////////////////////////////////////////////////////
$fn = "/var/www/data/blog/".$blog['order_id'].".xml";
$h = fopen($fn, "r");
$data = fread($h, filesize($fn));
fclose($h);
$data='<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type">
'.$data.'
</head>
</html>
';
        $dom = new DomDocument;
        $res = @$dom->loadHTML($data);
$posts = $dom->getElementsByTagName("post");
$count=0;
unset($socweb);
unset($av_host);
unset($timet);
unset($allposts);
unset($timeemo);
$all_host[0]='twitter.com';
$all_host[1]='livejournal.com';
$all_host[2]='ya.ru';
$all_host[3]='blogspot.com';
foreach ($posts as $post)
{
        $link=$post->firstChild->nextSibling->textContent;//->firstChild->nextSibling
        $time=$post->firstChild->nextSibling->nextSibling->textContent;
        $content=$post->firstChild->nextSibling->nextSibling->nextSibling->textContent;
        $nick=$post->firstChild->nextSibling->nextSibling->nextSibling->nextSibling->textContent;
        //echo 'link ['.$link.'] time ['.$time.'] content ['.$content.'] nick ['.$nick.']<br>';
        //$timet[date('Y',$time)][date('n',$time)][date('j',$time)]++;


		$hn=parse_url($link);
		$hn=$hn['host'];
		$ahn=explode('.',$hn);
		$hn = $ahn[count($ahn)-2].'.'.$ahn[count($ahn)-1];
		
		//fulljob
		//$db->query('INSERT INTO blog_full_com (ful_com_part, ful_com_link) VALUES (\''.addslashes($content).'\',\''.$link.'\')');
		//endof fulljob
		
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

		//left block
        $socweb[$hn]++;
		//endof left block

		if ($hn=='twitter.com') {
			list($tmp,$tmp,$tmp,$nick,$tmp)=explode("/",$link,5);
			$bman=$db->query('SELECT * FROM robot_blogs WHERE lower(blog_login)="'.mb_strtolower($nick, 'UTF-8').'"');
			if (mysql_num_rows($bman)>0)
			{
				$man=$db->fetch($bman);
				$allposts[]=array('content'=>$content,'nick'=>$man['blog_login'],'readers'=>$man['blog_readers'],'site'=>'twitter');
				//array_push($allposts,array('content'=>$content,'nick'=>$man['blog_login'],'readers'=>$man['blog_readers'],'site'=>'twitter'));
			}
		}

	$count++;
}






//generating left column file /////////////////////////////////////////////////////////////////////////

arsort($socweb);
unset($leftout);
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
$fp = fopen('/var/www/data/blog/'.$blog['order_id'].'.left', 'wb');
fwrite($fp, $leftout);
fclose($fp);
$fp = fopen('/var/www/data/blog/'.$blog['order_id'].'.left2', 'wb');
fwrite($fp, $leftout2);
fclose($fp);
$fp = fopen('/var/www/data/blog/'.$blog['order_id'].'.src', 'wb');
fwrite($fp, json_encode($socweb));
fclose($fp);
unset($leftout);
unset($leftout2);
//endof left column file ///////////////////////////////////////////////////////////////





//generating graph block file /////////////////////////////////////////////////////////////////////////
$fp = fopen('/var/www/data/blog/'.$blog['order_id'].'.graph', 'wb');
fwrite($fp, json_encode(array('all'=>$timet,'emo'=>$timeemo)));
fclose($fp);
//endof graph block file /////////////////////////////////////////////////////////////////////////




//generating metrics block file /////////////////////////////////////////////////////////////////////////
unset($metrics);
//$allposts[]=array('content'=>$content,'nick'=>$man['blog_login'],'readers'=>$man['blog_readers']);
//$metrics['posts']=$allposts;
$metrics['speakers']=speakers($allposts);
$metrics['value']=value($allposts);
$metrics['promotion']=promotion($allposts);
$fp = fopen('/var/www/data/blog/'.$blog['order_id'].'.metrics', 'wb');
fwrite($fp, json_encode($metrics));
fclose($fp);
//endof metrics block file /////////////////////////////////////////////////////////////////////////


unset($blog);
}

$res=$db->query('SELECT * FROM blog_orders');
while($blog=$db->fetch($res))
{

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
    echo "command returned $return_value\n";
}
}
?>
