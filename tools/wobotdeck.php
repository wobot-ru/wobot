<?

require_once('/var/www/com/config.php');
require_once('/var/www/com/func.php');
require_once('/var/www/com/db.php');
require_once('/var/www/bot/kernel.php');

$db = new database();
$db->connect();


$i=0;
$accounts=array();

$qw=$db->query('SELECT * FROM tp_keys WHERE in_use=1 and type="tw"');
while ($row = $db->fetch($qw)) {
  $account=json_decode($row['key'],true);
  list($id,$tmp)=explode("-", $account[0]['user_token']);
  $acc['username']="twitter".$i;
  $acc['userId']=intval($id);
  $acc['profileImageURL']="http://a0.twimg.com/profile_images/702090982/president_normal.JPG";
  $acc['type']="twitter";
  $acc['isDefault']=false;
  $acc['oauth_token']=$account[0]['user_token'];
  $acc['token_secret']=$account[0]['user_secret'];
  $acc['key']="twitter:".$id;
  $acc['isPrivate']=false;
  $acc['updated']=1368020016680;
  $accounts['twitter:'.$id]=$acc;
  $i++;
}

echo json_encode($accounts);
?>