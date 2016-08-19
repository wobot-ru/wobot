<?

require_once('/var/www/daemon/Engagement/rEng2/Engagement/bot/kernel.php');
require_once('/var/www/daemon/Engagement/rEng2/Engagement/com/config.php');
require_once('/var/www/daemon/Engagement/rEng2/Engagement/com/db.php');
require_once('/var/www/daemon/Engagement/rEng2/Engagement/new_e_tw.php');

echo '
<a href="http://188.120.239.225/tools/add_key.php">Добавление ключей</a>
<a href="http://188.120.239.225/tools/check_key.php">Проверка ключей</a>
';

$db = new database();
$db->connect();
if (($_POST['consumer_key']!='') && ($_POST['consumer_secret']!='') && ($_POST['user_token']!='') && ($_POST['user_secret']!=''))
{
	/*$mkey[0]=array(
	'consumer_key' => 'TyjMeQ3u1axtIPqNVTjLOw',
	'consumer_secret' => 'XQdntdWuFyZCAGNlkUlCq8Cd9DaDyYd9DWC3u1PQA8',
	'user_token' => '368306310-REPXp1s7pYVHKV97Cbdr3HGSStCzilXcbGZUuSgp',
	'user_secret' => 'nc0VUnVuIy1DfD9h2QL6wsOx31KVNXAzhglsL7xnEI');*/
	$mkey[0]['consumer_key']=$_POST['consumer_key'];
	$mkey[0]['consumer_secret']=$_POST['consumer_secret'];
	$mkey[0]['user_token']=$_POST['user_token'];
	$mkey[0]['user_secret']=$_POST['user_secret'];
	if (count(get_retweets('https://twitter.com/#!/LaithIliana/statuses/131756278744760320',$mkey[0]))==20)
	{
		$db->query('UPDATE  `wobot`.`tp_keys` SET  `key` = \''.addslashes(json_encode($mkey)).'!!\' WHERE  `tp_keys`.`id` ='.intval($_GET['id']));
	}
}
$k=$db->query('SELECT * FROM tp_keys WHERE id='.intval($_GET['id']));
while ($key=$db->fetch($k))
{
	$kk=json_decode($key['key'],true);
	echo '
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<form method="POST" action="?id='.intval($_GET['id']).'">
	consumer_key: <input type="text" name="consumer_key" value="'.$kk[0]['consumer_key'].'"><br>
	consumer_secret: <input type="text" name="consumer_secret" value="'.$kk[0]['consumer_secret'].'"><br>
	user_token: <input type="text" name="user_token" value="'.$kk[0]['user_token'].'"><br>
	user_secret: <input type="text" name="user_secret" value="'.$kk[0]['user_secret'].'"><br>	
	<input type="submit" value="Изменить">
	</form>
	';
}

?>