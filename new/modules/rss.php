<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
require_once('com/config.php');
require_once('com/func.php');
require_once('com/db.php');
require_once('bot/kernel.php');
require_once('com/auth.php');
require_once('modules/tcpdf/config/lang/rus.php');
require_once('modules/tcpdf/tcpdf.php');
//$_GET['order_id']=3;
//$_COOKIE['showfav']=1;
//$_GET['order_id']=3;
//print_r($_COOKIE);
//print_r($_GET);
//$_POST['order_id']=145;
ignore_user_abort(true);
set_time_limit(0);
ini_set('max_execution_time',0);
ob_implicit_flush();
//print_r($_POST);

$db = new database();
$db->connect();
//print_r($_POST);
//echo 'not loged';

//auth();
//if (!$loged) die();



header("content-type: application/rss+xml");

//echo '1';

/*$fn = "data/blog/".intval($_GET['order_id']).".xml";
//$fn = "4.xml";
$h = fopen($fn, "r");
$data = fread($h, filesize($fn));
fclose($h);*/
//echo '1';
echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
<rss version=\"2.0\" xmlns:atom=\"http://www.w3.org/2005/Atom\">
<channel>
  <atom:link href=\"http://bmstu.wobot.ru/new/rss?order_id=".intval($_GET['order_id'])."\" rel=\"self\" type=\"application/rss+xml\" />
  <title>Wobot research</title>
  <link>http://wobot.ru/</link>
  <description>Wobot daily digest</description>
  <language>ru-ru</language>
  <pubDate>".date('r',mktime(0,0,0,date('n',time()),date('j',time())-1,date('Y',time())))."</pubDate>

  <lastBuildDate>".date('r',time())."</lastBuildDate>
  <generator>Wobot daily digest</generator>
  <webMaster>wobot@mail.ru (Wobot team)</webMaster>

";

//$regexcash="/<link>(?<link>.*?)<\/link>.*?<time>(?<time>.*?)<\/time>.*?<content>(?<content>.*?)<\/content>.*?<nick>(?<nick>.*?)<\/nick>.*?<loc>(?<loc>.*?)<\/loc>/is";
//preg_match_all($regexcash,$data,$outcash);
//array_multisort($outcash['time'],SORT_DESC,$outcash['link'],SORT_DESC,$outcash['content'],SORT_DESC,$outcash['nick'],SORT_DESC,$outcash['loc'],SORT_DESC);
//$where=get_isshow2();p.post_time>='.mktime(0,0,0,date('n',time()),date('j',time())-1,date('Y',time())).'
$query='SELECT * from blog_post as p LEFT JOIN robot_blogs2 AS b ON p.blog_id=b.blog_id WHERE p.order_id="'.intval($_GET['order_id']).'" ORDER BY p.post_time DESC LIMIT 20';
//echo '<br><br>'.$query.'<br><br>';
//die();
$respost=$db->query($query);
$ii=0;
while($pst = $db->fetch($respost))
{
	$outcash['link'][$ii]=str_replace("\n","",$pst['post_link']);
	$outcash['time'][$ii]=$pst['post_time'];
	$outcash['content'][$ii]=$pst['post_content'];
	$outcash['isfav'][$ii]=$pst['post_fav'];
	$outcash['nastr'][$ii]=$pst['post_nastr'];
	$outcash['isspam'][$ii]=$pst['post_spam'];
	$outcash['nick'][$ii]=$pst['blog_nick'];
	$outcash['type'][$ii]=$pst['post_type'];
	$ii++;
}

foreach ($outcash['link'] as $key => $llink)
{
	$link=urldecode($llink);
	$time=$outcash['time'][$key];
	$content=$outcash['content'][$key];
	$nick=$outcash['nick'][$key];
	$time=mktime(0,0,0,date('n',$time),date('j',$time),date('Y',$time));
		echo "\t<item>\n";
		echo "\t\t<title>".(strlen($nick)>0?$nick:'неизвестный автор')."</title>\n";
		echo "\t\t<link>".preg_replace('/\&/is','&amp;',$link)."</link>\n";
		echo "\t\t<description>".strip_tags($content)."</description>\n";
		echo "\t\t<pubDate>".date('r',$time)."</pubDate>\n";
		echo "\t\t<guid>".preg_replace('/\&/is','&amp;',$link)."</guid>\n";
		echo "\t</item>\n\n";
             //$csv_output.=date("d.m.Y",$time)."\t ".$link."\t ".$isfav."\t ".$isspam."\t ".$nstr."\t ".$nick."\t ".$content_r."\n";

}

echo "</channel>\n</rss>";
?>
