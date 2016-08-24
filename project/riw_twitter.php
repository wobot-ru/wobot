<?php
require_once('/var/www/project/lib/Phirehose.php');

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

function dopost($name,$content,$date)
{
	global $db;
	$db->query('INSERT INTO riw_post (post_source, post_nick, post_msg, post_date) values ("twitter.com","'.addslashes($name).'","'.addslashes($content).'", '.strtotime($date).')');
}

/**
 * Example of using Phirehose to display a live filtered stream using track words 
 */
class FilterTrackConsumer extends Phirehose
{
  /**
   * Enqueue each status
   *
   * @param string $status
   */
  public function enqueueStatus($status)
  {
    /*
     * In this simple example, we will just display to STDOUT rather than enqueue.
     * NOTE: You should NOT be processing tweets at this point in a real application, instead they should be being
     *       enqueued and processed asyncronously from the collection process. 
     */
/*
Array
(
    [in_reply_to_user_id_str] => 109530724
    [id_str] => 122330568607281152
    [in_reply_to_user_id] => 109530724
    [text] => @webunese kkkkkk  com um belo patrocínio da Microsoft com certeza!
    [created_at] => Fri Oct 07 15:20:55 +0000 2011
    [contributors] => 
    [geo] => 
    [favorited] => 
    [source] => web
    [retweet_count] => 0
    [in_reply_to_screen_name] => webunese
    [coordinates] => 
    [entities] => Array
        (
            [hashtags] => Array
                (
                )

            [urls] => Array
                (
                )

            [user_mentions] => Array
                (
                    [0] => Array
                        (
                            [id_str] => 109530724
                            [indices] => Array
                                (
                                    [0] => 0
                                    [1] => 9
                                )

                            [name] => Wagner Bunese
                            [id] => 109530724
                            [screen_name] => webunese
                        )

                )

        )

    [retweeted] => 
    [in_reply_to_status_id] => 1.2232860279151E+17
    [in_reply_to_status_id_str] => 122328602791514112
    [place] => 
    [user] => Array
        (
            [id_str] => 150628881
            [show_all_inline_media] => 
            [contributors_enabled] => 
            [following] => 
            [profile_background_image_url_https] => https://si0.twimg.com/profile_background_images/107751550/1024.jpg
            [created_at] => Tue Jun 01 12:13:15 +0000 2010
            [profile_background_color] => C0DEED
            [profile_image_url] => http://a1.twimg.com/profile_images/1534840427/perfil_normal.jpg
            [profile_background_tile] => 1
            [favourites_count] => 2
            [follow_request_sent] => 
            [time_zone] => Brasilia
            [profile_sidebar_fill_color] => DDEEF6
            [url] => 
            [description] => 
            [geo_enabled] => 1
            [profile_sidebar_border_color] => C0DEED
            [followers_count] => 75
            [is_translator] => 
            [profile_image_url_https] => https://si0.twimg.com/profile_images/1534840427/perfil_normal.jpg
            [listed_count] => 2
            [profile_use_background_image] => 1
            [friends_count] => 83
            [location] => Curitiba
            [profile_text_color] => 333333
            [protected] => 
            [lang] => pt
            [verified] => 
            [profile_background_image_url] => http://a2.twimg.com/profile_background_images/107751550/1024.jpg
            [name] => Cassio Kamitani
            [notifications] => 
            [profile_link_color] => 0084B4
            [id] => 150628881
            [default_profile_image] => 
            [default_profile] => 
            [statuses_count] => 1357
            [utc_offset] => -10800
            [screen_name] => kamicassio
        )

    [id] => 1.2233056860728E+17
    [truncated] => 
)

*/
	global $db,$ntwitter;
    $data = json_decode($status, true);
    if (is_array($data) && isset($data['user']['screen_name'])) {
	//print_r($data);
	$content=urldecode($data['text']);
		//if (preg_match("/[а-я]/isu", $content))
		//{
			//print date('h:i:s',strtotime($data['created_at'])).' '.$data['user']['screen_name'] .' '.$data['user']['lang'].' '.$data['user']['profile_image_url']. ': ' . $content . "\n";
			echo '123';
			//$db->query('INSERT INTO riw_post (post_source, post_nick, post_msg, post_date) values ("twitter.com","'.addslashes($data['user']['screen_name']).'","'.addslashes($content).'", '.strtotime($data['created_at']).')');
			//dopost($data['user']['screen_name'],$content,time());
			if (intval($ntwitter)==1) $host='twitter1';
			else $host='twitter2';
				$db->query('INSERT INTO riw_post (post_source, post_nick, post_msg, post_date, post_url) values ("'.$host.'","'.addslashes($data['user']['screen_name']).'","'.addslashes($content).'", '.strtotime($data['created_at']).',"'.addslashes($data['user']['profile_image_url']).'")');
		//}
		//else echo '*';
    }
  }
}
$cert=array(
	'1'=>array('login'=>'sento20121','pass'=>'barann1989'),
	'2'=>array('login'=>'UPetrov','pass'=>'barann1989')
	);

// Start streaming
$res=$db->query('SELECT * FROM riw_setup WHERE setup_id=1');
$setup = $db->fetch($res);
$ntwitter=$argv[1];

$words=explode(',',$setup['setup_keyword1'.$ntwitter]);

echo $cert[$ntwitter]['login'].' '.$cert[$ntwitter]['pass']."\n";
print_r($words);
//die();
//echo 'setup_keyword'.$ntwitter;

//die();

$sc = new FilterTrackConsumer($cert[$ntwitter]['login'], $cert[$ntwitter]['pass'], Phirehose::METHOD_FILTER);
$sc->setTrack('microsoft');
$sc->consume();
