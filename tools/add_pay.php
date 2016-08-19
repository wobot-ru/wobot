<?

require_once('/var/www/com/db.php');
require_once('/var/www/com/config.php');

$db=new database();
$db->connect();

echo '<form method="post">
<textarea name="pay"></textarea>
<input type="submit" value="submit"/>
</form>';

if (isset($_POST['pay']))
{
	/*$text='Цена: 4000.000000
inv_id: 1381222209
Метод оплаты: EMoney
Shp_item=0';*/
	list($price,$bill_id,$tmp,$user_id)=explode("\n", $_POST['pay']);
	list($tmp,$price)=explode(" ", $price);
	list($tmp,$bill_id)=explode(" ", $bill_id);
	list($tmp,$user_id)=explode("=", $user_id);
	echo 'UPDATE billing SET status=2, money="'.intval($price).'" WHERE bill_id='.intval($bill_id).' and user_id='.intval($user_id);
	$db->query('UPDATE billing SET status=2, money="'.intval($price).'" WHERE bill_id='.intval($bill_id).' and user_id='.intval($user_id));
}

// print_r($_POST);
//$_POST['orid']='3377';

// include lastRSS 
//include "./lastRSS.php"; 
/*
// List of RSS URLs 
$rsses = array( 
'http://photos.googleapis.com/data/feed/base/all?alt=rss&kind=photo&q='.$keyword,
'http://'.$keyword.'.blogspot.com/rss.xml',
'http://'.$keyword.'.tumblr.com/rss',
'http://'.$keyword.'.wordpress.com/feed/',
'https://feeds.foursquare.com/history/'.$keyword.'.rss',
'http://search.twitter.com/search.rss?q='.urlencode('#').$keyword,
'http://search.twitter.com/search.rss?q='.$keyword
); 

// Create lastRSS object 
$rss = new lastRSS; 

// Set cache dir and cache time limit (5 seconds) 
// (don't forget to chmod cahce dir to 777 to allow writing) 
$rss->cache_dir = './temp'; 
$rss->cache_time = 1200; 


// Show all rss files 
//echo "<table cellpadding=\"10\" border=\"0\"><tr><td width=\"50%\" valign=\"top\">"; 
foreach ($rsses as $url) { 
    if ($rs = $rss->get($url)) { 
        foreach ($rs['items'] as $item) { 
            //echo "$item[link]\n$item[description]\n$item[title]\n$item[dc_date]\n\n"; 
            $item['date']=time()-rand(0,60*60*48);
            //print_r($item);
				if (($item['title']!='')&&($item['link']!='')&&($item['date'].' '.$item['time']!=' '))
				{
				$hn=parse_url($item['link']);
				$hn=$hn['host'];
				$ahn=explode('.',$hn);
				$hn = $ahn[count($ahn)-2].'.'.$ahn[count($ahn)-1];
				$hh=$ahn[count($ahn)-2];
				$qw='INSERT INTO blog_post (order_id,post_link,post_host,post_content,post_time,post_engage) VALUES ('.$_POST['orid'].',\''.addslashes($item['link']).'\',\''.$hn.'\',\''.addslashes($item['title']).'\','.($item['date']).',0)';
				echo $qw."\n";
				$db->query($qw);
				$cc++;
				}
        } 
    } 
    else { 
        echo "RSS dontwork $url\n"; 
        // you will probably hide this message in a live version 
    } 
} 

die();
$mpost=array(
	array('post'=>'','link'=>'','date'=>'','time'=>'')
	);

$cc=0;
foreach ($mpost as $item)
{

}
if ($cc>0) echo '<br/>Добавлено сообщений: '.$cc.'<br/>';


//get url var containing Facebook Group ID
$guid = $_GET['guid'];
//get the number of posts to display in feed
$limit = $_GET['posts'];

//if no guid given, defaults to Lounge Pirata
$guid = (strlen($guid)>0)?$guid:'177906248889471';
$facebookFeed =  'http://graph.facebook.com/'.$guid.'/feed';

//get the feed
$json = file_get_contents($facebookFeed);

//parse Json format into PHP array
$data = json_decode($json);
if ($_GET['dump'] == true) var_dump($data);

//if no feed title and desctiption given, defaults to Lounge Pirata
$feedTitle = (isset($_GET['feedTitle']))? $_GET['feedTitle']:'Lounge Pirata RSS';
$feedDesc = (isset($_GET['feedDesc']))? $_GET['feedDesc']:'Feed RSS do grupo Lounge Pirata no Facebook';

//start constructing the xml rss feed
// xmlns:media="http://search.yahoo.com/mrss/"
$xmlresult= '<?xml version="1.0" encoding="ISO-8859-1"?>
<rss version="2.0">
<channel>
	<title>'.$feedTitle.'</title> 
	<description>'.$feedDesc.'</description> 
	<link>'.htmlentities('http://www.facebook.com/home.php?sk=group_'.$guid, ENT_QUOTES,"ISO-8859-1").'</link>';
//<atom:link href="http://www.debatevisual.com/code/facebook.php" rel="self" type="application/rss+xml" />';

//if no posts limit given, use all
$limit = (strlen($limit)>0)? $limit : count($data->data);

//$limit=9;
//loop posts
for ($i=0; $i<$limit;$i++){
	//get values from json array
	$username = $data->data[$i]->from->name;
	$message = $data->data[$i]->message;
	$pictureUrl = $data->data[$i]->picture;
	$link = $data->data[$i]->link;
	$name = $data->data[$i]->name;
	$description = $data->data[$i]->description;
	$caption = $data->data[$i]->caption;
	$created_time = $data->data[$i]->created_time;
	$id = $data->data[$i]->id;
	$likes = $data->data[$i]->likes->count;
	$commentCount = $data->data[$i]->comments->count;

	//loop to get comments
	$comments = '';
	if ($commentCount>0){
		$comments = '<div style="margin:10px 0 0 20px;display:block;padding:5px;background-color:#f7f7f7;">Last Comments: ('.$commentCount.' total)';
		$j=0;
		while ($j<count($data->data[$i]->comments->data)){
			$commentName = $data->data[$i]->comments->data[$j]->from->name;
			$commentMessage = $data->data[$i]->comments->data[$j]->message;
			$commentCreated_time = $data->data[$i]->comments->data[$j]->created_time;
			$comments .= '<br /><b>'.$commentName.'</b> - '.substr(strftime("%a, %d %b %Y %H:%M:%S %z",tstamptotime($commentCreated_time)),5,17).'<br />- '.$commentMessage.'<br />';
			$j++;
		}
		$comments .= '</div>';
	}
	
	//formats picture url into html
	$picture = '';
	if (strlen($pictureUrl)>0)$picture = '<a href="'.$link.'" target="_blank"><img alt="" border="0" src="'.utf8_decode($pictureUrl).'"  align="left" style="padding-right:5px;"/></a>';
	
	$likes = ($likes>1)? '<i>'.$likes.' people like this</i>' : $likes = ($likes>0)? '<i>'.$likes.' person likes this</i>' : '';;
	
	
	//transforms all info into html formatting
	$message = utf8_decode(($message));
	if (strlen($description)>0 && strlen($message)>0) $message .= '<br />';
	if (strlen($name)>0) {
		$message .= '<div style="display:block;padding-top:5px;">'.$picture;
		if (strlen($link)>0) $message .= '<a href="'.$link.'" target="_blank"><b>'.utf8_decode($name).'</b></a>
		
		<br /><i>'.$caption.'</i>
		
		<br />'.utf8_decode(($description));
		$message .='<div style="clear:both"></div>'.$likes.'</div>';	
	}
	else{
		$message .= '<br />'.$likes;	
	}
	
	//convert facebook create_time format into RFC 822 rss compatible format
	$pubDate = strftime("%a, %d %b %Y %H:%M:%S %z",tstamptotime($created_time));
	
	//if there's a link, encode it into html format
	$link = (strlen($link)>0)? '<link>'.htmlentities($link, ENT_QUOTES,"ISO-8859-1").'</link>': '';
	
	//rss item
	$rssitem = 	 '
	<item>
		<title>'.utf8_decode($username).' - '.substr($pubDate,5,17).'</title> 
		<pubDate>'.$pubDate.'</pubDate>
		'.$link.'
		<description><![CDATA['.$message.utf8_decode(nl2br($comments)).']]></description>
		<guid isPermaLink="false">'.$id.'</guid>
	</item>';
				//'<media:content url="'.$pictureUrl.'" medium="image" />
	$xmlresult .= $rssitem;
}

//end rss xml structure
$xmlresult .= '
	</channel>
</rss>';

//print rss xml
header('Content-Type: application/xml; charset=iso-8859-1');
print $xmlresult;

function tstamptotime($tstamp) {
        // converts ISODATE to unix date
        // 1984-09-01T14:21:31Z
       sscanf($tstamp,"%u-%u-%uT%u:%u:%uZ",$year,$month,$day,
        $hour,$min,$sec);
        $newtstamp=mktime($hour,$min,$sec,$month,$day,$year);
        return $newtstamp;
    }        

?>

<?php
	header("Content-Type: application/xml; charset=UTF-8");
	$screen_name = $_GET['screen_name'];
	$list_name = $_GET['list_name'];
	$statuses_url = 'http://api.twitter.com/1/'.$screen_name.'/lists/'.$list_name.'/statuses.json?per_page=100';
	$fetch_json = file_get_contents($statuses_url);
	$return = json_decode($fetch_json);
	$now = date("D, d M Y H:i:s T");
	$link = htmlspecialchars('http://'.$_SERVER['SERVER_NAME'].$_SERVER['SCRIPT_NAME'].'?'.$_SERVER['QUERY_STRING']);
?>
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
	<channel>
		<title><?php echo $list_name; ?></title>
		<link><?php echo $link; ?></link>
		<atom:link href="<?php echo $link; ?>" rel="self" type="application/rss+xml" />
		<description><?php echo $list_name; ?></description>
		<pubDate><?php echo $now; ?></pubDate>
		<lastBuildDate><?php echo $now; ?></lastBuildDate>
	<?php foreach ($return as $line){ ?>
	<item>
		<title><?php echo htmlspecialchars(htmlspecialchars_decode($line->user->screen_name.": ".strip_tags($line->text))); ?></title>
		<description><?php echo htmlspecialchars(htmlspecialchars_decode(strip_tags($line->text))); ?></description>
		<guid><?php echo htmlspecialchars("https://twitter.com/".$line->user->screen_name."/statuses/".$line->id_str); ?></guid>
		<link><?php echo htmlspecialchars("https://twitter.com/".$line->user->screen_name."/statuses/".$line->id_str); ?></link>
	</item>
	<?php } ?>
</channel>
</rss>

<?

  // Set API key. Get your key at https://code.google.com/apis/console.
  $key = getenv('PLUS_KEY') ? getenv('PLUS_KEY') : 'insert-key-here';

  // Set ID of Plus user. That's the long number in their profile URL.
  $uid = getenv('PLUS_ID') ? getenv('PLUS_ID') : '106413090159067280619';

  // Other parameters you can tweak if you like
  $size = 20; // number of RSS items
  $cachetime = 5 * 60;
  $cachefolder = getenv('PLUS_CACHE') ? getenv('PLUS_CACHE') : '/tmp';
  $cachefile = "$cachefolder/index-cached-".md5($_SERVER["REQUEST_URI"]).".html";
  date_default_timezone_set('GMT');

///////////////////////////////
// SERVE FROM CACHE IF EXISTS
///////////////////////////////

  // http://simonwillison.net/2003/may/5/cachingwithphp/ modded
  if (file_exists($cachefile) && time() - $cachetime < filemtime($cachefile)) {
    print file_get_contents($cachefile);
    exit;
  }
  ob_start();

/////////////
// GO FETCH
/////////////

  $url = "https://www.googleapis.com/plus/v1/people/$uid/activities/public?key=$key&maxResults=$size";
  $activities = json_decode(get_remote($url));
  $items = $activities -> items;

/////////////////////////////////////////
// HELPERS TO PROCESS SOME OF THE DATA
//////////////////////////////////////////

  function get_remote($url) {

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, 0);

    $contents = curl_exec($ch);
    curl_close($ch);
    return $contents;

  }

  function pubDate($item) { return gmdate(DATE_RFC822, strtotime($item -> published)); }

  function content($item) {

    $object = $item -> object;
    $content = '';

    if ($item->verb == 'share') {
      $source = "<a href={$object->actor->url}>{$object->actor->displayName}</a>";
      $content .= "{$item->annotation}<p>&nbsp;<br/><em>$source:</em></p><blockquote>{$object->content}</blockquote>";
    } else {
      $content .= $object -> content;
    }

    if ($object->attachments and sizeof($object->attachments)) {
      $attachment = $object->attachments[0];
      if ($attachment->objectType == 'photo')
        $content.="<p><a href='{$attachment->url}'><img width='{$attachment->image->width}' ".
                  "height='{$attachment->image->height}' src='{$attachment->image->url}' /></a></p>";
      else if ($attachment->objectType='article')
        $content .= "<p><a href='{$attachment->url}'>{$attachment->displayName}</a></p>";
    }

    return utf8_encode(htmlspecialchars($content));

  }

//////////////////////
// PUMP OUT THE FEED
//////////////////////
?>
<? echo '<?xml version="1.0" encoding="UTF-8"?>'."\n" ?>
<rss xmlns:dc="http://purl.org/dc/elements/1.1/" version="2.0">
  <channel>
    <title><?= $activities -> title ?></title>
    <link>http://plus.google.com/<?= $uid ?>/posts</link>
    <pubDate><?= sizeof($items) ? pubDate($items[0]) : ""?></pubDate>
    <dc:date><?= sizeof($items) ? $items[0]->published : ""?></dc:date>
<? foreach ($items as $item) {
   $item_content = content($item);
 ?>
    <item>
      <title><?= $item -> title ?>...</title>
      <link><?= $item -> url ?></link>
      <description><?= $item_content ?></description>
      <pubDate><?= pubDate($item) ?></pubDate>
      <guid><?= $item -> url ?></guid>
      <dc:date><?= $item -> published ?></dc:date>
    </item>
<? } ?>
  </channel>
</rss><?
////////////////////////////
// WRITE ALL THAT TO CACHE
////////////////////////////
  $fp = fopen($cachefile, 'w');
  fwrite($fp, ob_get_contents());
  fclose($fp);
  ob_end_flush(); // Send the output to the browser


header('Content-Type: ' . feed_content_type('rss-http') . '; charset=' . get_option('blog_charset'), true);
$more = 1;

echo '<?xml version="1.0" encoding="'.get_option('blog_charset').'"?'.'>'; ?>

<rss version="2.0"
	xmlns:content="http://purl.org/rss/1.0/modules/content/"
	xmlns:wfw="http://wellformedweb.org/CommentAPI/"
	xmlns:dc="http://purl.org/dc/elements/1.1/"
	xmlns:atom="http://www.w3.org/2005/Atom"
	xmlns:sy="http://purl.org/rss/1.0/modules/syndication/"
	xmlns:slash="http://purl.org/rss/1.0/modules/slash/"
	<?php do_action('rss2_ns'); ?>
>

<channel>
	<title><?php bloginfo_rss('name'); wp_title_rss(); ?></title>
	<atom:link href="<?php self_link(); ?>" rel="self" type="application/rss+xml" />
	<link><?php bloginfo_rss('url') ?></link>
	<description><?php bloginfo_rss("description") ?></description>
	<lastBuildDate><?php echo mysql2date('D, d M Y H:i:s +0000', get_lastpostmodified('GMT'), false); ?></lastBuildDate>
	<language><?php echo get_option('rss_language'); ?></language>
	<sy:updatePeriod><?php echo apply_filters( 'rss_update_period', 'hourly' ); ?></sy:updatePeriod>
	<sy:updateFrequency><?php echo apply_filters( 'rss_update_frequency', '1' ); ?></sy:updateFrequency>
	<?php do_action('rss2_head'); ?>
	<?php global $post; ?>
	<?php while( have_posts()) : the_post(); ?>
		
	<item>
		<title><?php the_title_rss() ?></title>
		<link><?php the_permalink_rss() ?></link>
		<?php
			$arrImages =& get_children('post_type=attachment&post_mime_type=image&post_parent=' . $post->ID );
			 if($arrImages) {
				$arrKeys = array_keys($arrImages);
				$iNum = $arrKeys[0];
      				$sThumbUrl = wp_get_attachment_thumb_url($iNum);
				echo '<image>'.$sThumbUrl .'</image>';
			}
		?>
		
		<comments><?php comments_link_feed(); ?></comments>
		<pubDate><?php echo mysql2date('D, d M Y H:i:s +0000', get_post_time('Y-m-d H:i:s', true), false); ?></pubDate>
		<dc:creator><?php the_author() ?></dc:creator>
		<?php the_category_rss('rss2') ?>

		<guid isPermaLink="false"><?php the_guid(); ?></guid>
<?php if (get_option('rss_use_excerpt')) : ?>
		<description><![CDATA[<?php the_excerpt_rss() ?>]]></description>
<?php else : ?>
		<description><![CDATA[<?php the_excerpt_rss() ?>]]></description>
	<?php if ( strlen( $post->post_content ) > 0 ) : ?>
		<content:encoded><![CDATA[<?php the_content_feed('rss2') ?>]]></content:encoded>
	<?php else : ?>
		<content:encoded><![CDATA[<?php the_excerpt_rss() ?>]]></content:encoded>
	<?php endif; ?>
<?php endif; ?>
		<wfw:commentRss><?php echo esc_url( get_post_comments_feed_link(null, 'rss2') ); ?></wfw:commentRss>
		<slash:comments><?php echo get_comments_number(); ?></slash:comments>
<?php rss_enclosure(); ?>
	<?php do_action('rss2_item'); ?>
	</item>
	<?php endwhile; ?>
</channel>
</rss>
*/
?>
