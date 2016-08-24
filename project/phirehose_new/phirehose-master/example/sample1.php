<?

require_once('../lib/Phirehose.php');
class MyStream extends Phirehose
{
  public function enqueueStatus($status)
  {
    print $status;
  }
}

$stream = new MyStream('wobottest', 'wobotresearch');
$stream->consume();

?>