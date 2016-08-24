<?php

require_once('../lib/Phirehose.php');
require_once('../lib/OauthPhirehose.php');
require_once('/var/www/com/db.php');
require_once('/var/www/com/config.php');
require_once('/var/www/daemon/fsearch3/ch.php');

error_reporting(0);

$db=new database();
$db->connect();

/**
 * Example of using Phirehose to display a live filtered stream using track words
 */
class FilterTrackConsumer extends OauthPhirehose
{
  /**
   * Enqueue each status
   *
   * @param string $status
   */
  public function enqueueStatus($status)
  {
    global $db,$order;
    /*
     * In this simple example, we will just display to STDOUT rather than enqueue.
     * NOTE: You should NOT be processing tweets at this point in a real application, instead they should be being
     *       enqueued and processed asyncronously from the collection process.
     */
    $data = json_decode($status, true);
    if (check_post(strip_tags($data['text']),$order['order_keyword'])!=0)
    {
      print_r($data);
      $qblog=$db->query('SELECT blog_id FROM robot_blogs2 WHERE blog_login=\''.addslashes($data['user']['screen_name']).'\' AND blog_link=\'twitter.com\' LIMIT 1');
      if ($db->num_rows($qblog)==0)
      {
        $qins=$db->query('INSERT INTO robot_blogs2 (blog_login,blog_link) VALUES (\''.addslashes($data['user']['screen_name']).'\',\'twitter.com\')');
        $blog['blog_id']=$db->insert_id($qins);
      }
      else
      {
        $blog=$db->fetch($qblog);
      }
      echo 'INSERT INTO blog_post (order_id,post_content,post_time,post_link,post_host,blog_id,post_advengage) VALUES (6954,\''.addslashes($data['text']).'\','.strtotime($data['created_at']).',\'http://twitter.com/'.$data['user']['screen_name'].'/statuses/'.$data['id_str'].'\',\'twitter.com\','.$blog['blog_id'].',\'{"retweet":0}\')'."\n";
      $qins_bp=$db->query('INSERT INTO blog_post (order_id,post_content,post_time,post_link,post_host,blog_id,post_advengage) VALUES (6954,\''.addslashes($data['text']).'\','.strtotime($data['created_at']).',\'http://twitter.com/'.$data['user']['screen_name'].'/statuses/'.$data['id_str'].'\',\'twitter.com\','.$blog['blog_id'].',\'{"retweet":0}\')');
      $db->query('INSERT INTO blog_full_com (ful_com_post_id,ful_com_order_id,ful_com_post) VALUES ('.$db->insert_id($qins_bp).',6954,\''.addslashes($data['text']).'\')');
      if (is_array($data) && isset($data['user']['screen_name'])) {
        print $data['user']['screen_name'] . ': ' . urldecode($data['text']) . "\n";
      }
    }
  }
}

// The OAuth credentials you received when registering your app at Twitter
define("TWITTER_CONSUMER_KEY", "KkrvpkVa8hNaf8xyGgB46w");
define("TWITTER_CONSUMER_SECRET", "kRYCjPqEoyURIBLvQK0uvG4wZ7MMSxDAWPBYY1lQJU");


// The OAuth data for the twitter account
define("OAUTH_TOKEN", "275446633-fMAANNenUOsCXiLNU8VKhl0Zg0nMn4yInDvDR6bM");
define("OAUTH_SECRET", "qya0xEn28Nz5JEHGqO3cVghv5L0HTS8SNUzrX8e4");
$qorder=$db->query('SELECT * FROM blog_orders WHERE order_id=6954 LIMIT 1');
$order=$db->fetch($qorder);
// Start streaming
$sc = new FilterTrackConsumer(OAUTH_TOKEN, OAUTH_SECRET, Phirehose::METHOD_FILTER);
$sc->setTrack(array('втб','#втб'));
$sc->consume(); 