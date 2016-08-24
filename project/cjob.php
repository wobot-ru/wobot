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

//$db = new database();
//$db->connect();

function parseday($query,$ts,$order_id)
{
global $nickloc,$times,$timet,$timeb,$locales,$xmlout,$loc,$db;
$link='http://blogs.yandex.ru/search.xml?text='.urlencode($query).'&ft=all&server=twitter.com&from_day='.date('d',$ts).'&from_month='.date('m',$ts).'&from_year='.date('Y',$ts).'&to_day='.date('d',$ts).'&to_month='.date('m',$ts).'&to_year='.date('Y',$ts).'&holdres=mark&numdoc=100&p=';
$numdoc=0;
$count=0;

do {
$isnext=0;
    $content=parseURL($link.intval($numdoc));

        $dom = new DomDocument;
        $res = @$dom->loadHTML($content);

$divs = $dom->getElementsByTagName("a");
foreach($divs as $div) {    
    if ($div->getAttribute("class")=="b-pager__next")
    {
	    $link=$div->getAttribute("href");
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
		//$blog_id=getnicklink($element->getAttribute("href"));
		//$respost=$db->query('INSERT INTO blog_post (post_link,post_time,post_content,order_id,blog_id) values (\''.$element->getAttribute("href").'\',\''.mktime(0,0,0,$mon,$day,$year).'\',\''.addslashes($element->textContent).'\','.$order_id.','.$blog_id.')');
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
usleep(500000);
			echo $count."\n";
} while($isnext && $numdoc<10);







$link='http://blogs.yandex.ru/search.xml?text='.urlencode($query).'&ft=all&server=livejournal.com&from_day='.date('d',$ts).'&from_month='.date('m',$ts).'&from_year='.date('Y',$ts).'&to_day='.date('d',$ts).'&to_month='.date('m',$ts).'&to_year='.date('Y',$ts).'&holdres=mark&numdoc=100&p=';
$numdoc=0;

do {
$isnext=0;
    $content=parseURL($link.intval($numdoc));

        $dom = new DomDocument;
        $res = @$dom->loadHTML($content);

$divs = $dom->getElementsByTagName("a");
foreach($divs as $div) {    
    if ($div->getAttribute("class")=="b-pager__next")
    {
	    $link=$div->getAttribute("href");
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
		//$blog_id=getnicklink($element->getAttribute("href"));
		//$respost=$db->query('INSERT INTO blog_post (post_link,post_time,post_content,order_id,blog_id) values (\''.$element->getAttribute("href").'\',\''.mktime(0,0,0,$mon,$day,$year).'\',\''.addslashes($element->textContent).'\','.$order_id.','.$blog_id.')');
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
			echo $count."\n";
usleep(500000);
} while($isnext && $numdoc<10);









$link='http://blogs.yandex.ru/search.xml?text='.urlencode($query).'&ft=all&server=liveinternet.ru&from_day='.date('d',$ts).'&from_month='.date('m',$ts).'&from_year='.date('Y',$ts).'&to_day='.date('d',$ts).'&to_month='.date('m',$ts).'&to_year='.date('Y',$ts).'&holdres=mark&numdoc=100&p=';
$numdoc=0;

do {
$isnext=0;
    $content=parseURL($link.intval($numdoc));

        $dom = new DomDocument;
        $res = @$dom->loadHTML($content);

$divs = $dom->getElementsByTagName("a");
foreach($divs as $div) {    
    if ($div->getAttribute("class")=="b-pager__next")
    {
	    $link=$div->getAttribute("href");
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
		//$blog_id=getnicklink($element->getAttribute("href"));
		//$respost=$db->query('INSERT INTO blog_post (post_link,post_time,post_content,order_id,blog_id) values (\''.$element->getAttribute("href").'\',\''.mktime(0,0,0,$mon,$day,$year).'\',\''.addslashes($element->textContent).'\','.$order_id.','.$blog_id.')');
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
			echo $count."\n";
usleep(500000);
} while($isnext && $numdoc<10);










$link='http://blogs.yandex.ru/search.xml?text='.urlencode($query).'&ft=all&server=diary.ru&from_day='.date('d',$ts).'&from_month='.date('m',$ts).'&from_year='.date('Y',$ts).'&to_day='.date('d',$ts).'&to_month='.date('m',$ts).'&to_year='.date('Y',$ts).'&holdres=mark&numdoc=100&p=';
$numdoc=0;

do {
$isnext=0;
    $content=parseURL($link.intval($numdoc));

        $dom = new DomDocument;
        $res = @$dom->loadHTML($content);

$divs = $dom->getElementsByTagName("a");
foreach($divs as $div) {    
    if ($div->getAttribute("class")=="b-pager__next")
    {
	    $link=$div->getAttribute("href");
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
		//$blog_id=getnicklink($element->getAttribute("href"));
		//$respost=$db->query('INSERT INTO blog_post (post_link,post_time,post_content,order_id,blog_id) values (\''.$element->getAttribute("href").'\',\''.mktime(0,0,0,$mon,$day,$year).'\',\''.addslashes($element->textContent).'\','.$order_id.','.$blog_id.')');
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
			echo $count."\n";
usleep(500000);
} while($isnext && $numdoc<10);











$link='http://blogs.yandex.ru/search.xml?text='.urlencode($query).'&ft=all&server=twitter.com%2C+liveinternet.ru%2C+diary.ru%2C+livejournal.com&x_server=on&from_day='.date('d',$ts).'&from_month='.date('m',$ts).'&from_year='.date('Y',$ts).'&to_day='.date('d',$ts).'&to_month='.date('m',$ts).'&to_year='.date('Y',$ts).'&holdres=mark&numdoc=100&p=';
$numdoc=0;

do {
$isnext=0;
    $content=parseURL($link.intval($numdoc));

        $dom = new DomDocument;
        $res = @$dom->loadHTML($content);

$divs = $dom->getElementsByTagName("a");
foreach($divs as $div) {    
    if ($div->getAttribute("class")=="b-pager__next")
    {
	    $link=$div->getAttribute("href");
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
		//$blog_id=getnicklink($element->getAttribute("href"));
		//$respost=$db->query('INSERT INTO blog_post (post_link,post_time,post_content,order_id,blog_id) values (\''.$element->getAttribute("href").'\',\''.mktime(0,0,0,$mon,$day,$year).'\',\''.addslashes($element->textContent).'\','.$order_id.','.$blog_id.')');
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
			echo $count."\n";
usleep(500000);
} while($isnext && $numdoc<10);







echo $count;
}

parseday('htc',mktime(0,0,0,3,10,2011),0);

?>