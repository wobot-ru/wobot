<?php
require_once('../lib/Phirehose.php');
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

/**
 * Example of using Phirehose to display the 'sample' twitter stream. 
 */
$w=$db->query('SELECT * FROM riw_setup WHERE setup_id=1');
$wrd=$db->fetch($w);
class SampleConsumer extends Phirehose
{
  /**
   * Enqueue each status
   *
   * @param string $status
   */
  public function enqueueStatus($status)
  {
	global $db;
    /*
     * In this simple example, we will just display to STDOUT rather than enqueue.
     * NOTE: You should NOT be processing tweets at this point in a real application, instead they should be being
     *       enqueued and processed asyncronously from the collection process. 
     */
    $data = json_decode($status, true);
    if (is_array($data) && isset($data['user']['screen_name'])) {
      print $data['user']['screen_name'] . ': ' . urldecode($data['text']) . "\n";
	  $db->query('INSERT INTO riw_post (post_source, post_nick, post_msg, post_date, post_url) values ("twitter.com","'.addslashes($data['user']['screen_name']).'","'.addslashes(urldecode($data['text'])).'", '.strtotime($data['created_at']).',"'.addslashes($data['user']['profile_image_url']).'")');
    }
  }
}

// Start streaming
//$sc = new SampleConsumer('UPetrov', 'barann1989', Phirehose::METHOD_SAMPLE);
//$sc->consume();
//$words=array('путин');
$words=explode(',',$wrd['setup_keyword1']);
$sc = new SampleConsumer('UPetrov', 'barann1989', Phirehose::METHOD_FILTER);
$sc->setTrack($words);
$sc->consume();