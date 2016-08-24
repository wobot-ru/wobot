<?php
require_once('../lib/Phirehose.php');
require_once('../lib/OauthPhirehose.php');
/**
 * Example of using Phirehose to display the 'sample' twitter stream. 
 */
class SampleOauthConsumer extends OauthPhirehose
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
      // print $data['lang'] . ': ' . $data['user']['screen_name'] . ': ' . urldecode($data['text']) . "\n";
    }
  }
}

// The OAuth credentials you received when registering your app at Twitter
define("TWITTER_CONSUMER_KEY", "RPTwR6QmhHoGvCgvFJqKw");
define("TWITTER_CONSUMER_SECRET", "hhPFwWrqFqRU5YRfNZisEGlc5HDvocVil8ksP3CUGfA");


// The OAuth data for the twitter account
define("OAUTH_TOKEN", "1559989441-9hb8YcmYIFkEhumHbTfybG9DlfBqx4arROQjay6");
define("OAUTH_SECRET", "A1hY04SmcHv2rkMdmJRbkihxCT6W2knZWTJN78cU");

// Start streaming
$sc = new SampleOauthConsumer(OAUTH_TOKEN, OAUTH_SECRET, Phirehose::METHOD_SAMPLE);
//$sc = new SampleOauthConsumer('username', 'password', Phirehose::METHOD_SAMPLE, Phirehose::FORMAT_JSON, 'en');
$sc->setLang('ru');
$sc->consume();