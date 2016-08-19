<?

require_once('/var/www/daemon/Engagement/rEng2/Engagement/bot/kernel.php');
require_once('/var/www/daemon/Engagement/rEng2/Engagement/com/config.php');
require_once('/var/www/daemon/Engagement/rEng2/Engagement/com/db.php');
require_once('/var/www/daemon/Engagement/rEng2/Engagement/new_e_tw.php');
echo '
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<a href="http://188.120.239.225/tools/check_key.php">Проверка ключей</a><br>

Добавление новых ключей API<br>
<form method="POST">
consumer_key: <input type="text" name="consumer_key"><br>
consumer_secret: <input type="text" name="consumer_secret"><br>
user_token: <input type="text" name="user_token"><br>
user_secret: <input type="text" name="user_secret"><br>
<input type="submit" value="Добавить">
</form>
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
		echo '<font color="green">!!!OK!!!</font>';

		$db->query('INSERT INTO `wobot`.`tp_keys` (`id`, `key`, `type`, `in_use`, `n_gr`, `progress`) VALUES (NULL, \''.addslashes(json_encode($mkey)).'\', \'tw\', \'\', \'\', \'\');');
		//echo 'INSERT INTO tp_keys (key,type) VALUES (\''.addslashes(json_encode($mkey)).'\',\'tw\')';
	}
	else
	{
		echo '<font color="red">Не верный ключ</font>';
	}
}

?>