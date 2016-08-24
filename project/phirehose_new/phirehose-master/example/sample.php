<?php
require_once('../lib/Phirehose.php');
require_once('../lib/OauthPhirehose.php');
/**
 * Example of using Phirehose to display the 'sample' twitter stream. 
 */
class SampleConsumer extends OauthPhirehose
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
    $data = json_decode($status, true);
    if (is_array($data) && isset($data['user']['screen_name'])) {
      print $data['user']['screen_name'] . ': ' . urldecode($data['text']) . "\n";
    }
  }
}

// The OAuth credentials you received when registering your app at Twitter
define("TWITTER_CONSUMER_KEY", "ZelcMVWWGjgedNXBLyl88w");
define("TWITTER_CONSUMER_SECRET", "SPBiOoR3OtVvaUf66uF2WS0E3WK0gdHmrmgGJJt2rbo");


// The OAuth data for the twitter account
define("OAUTH_TOKEN", "867595976-aGElZrPia1uPCLjWiCIp6iSQ8fftanPBoUHiVZxh");
define("OAUTH_SECRET", "zVQiDIcbUG6l00vlQFyDheXqibBtovkUDblBkDb0A");

// Start streaming
$sc = new FilterTrackConsumer(OAUTH_TOKEN, OAUTH_SECRET, Phirehose::METHOD_FILTER);
$sc->consume();