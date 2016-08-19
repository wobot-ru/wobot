<?

require_once('/var/www/daemon/Engagement/rEng2/Engagement/bot/kernel.php');
require_once('/var/www/daemon/Engagement/rEng2/Engagement/com/config.php');
require_once('/var/www/daemon/Engagement/rEng2/Engagement/com/db.php');
require_once('/var/www/daemon/Engagement/rEng2/Engagement/new_e_tw.php');

$db = new database();
$db->connect();
echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
$k=$db->query('SELECT * FROM tp_keys WHERE type=\'tw\' LIMIT '.(10*(intval($_GET['offset']))).',10');
while ($key=$db->fetch($k))
{
	$mkey=json_decode($key['key'],true);
	sleep(1);
	if (count(get_retweets('https://twitter.com/#!/LaithIliana/statuses/131756278744760320',$mkey[0]))==20)
	{
		echo $key['id'].' - good<br>';
	}
	else
	{
		echo $key['id'].' - bad <a href="http://188.120.239.225/tools/edit_key.php?id='.$key['id'].'">Редактировать</a><br>';
	}
	
}
echo '<a href="?offset='.intval($_GET['offset']+1).'">далее</a>'
?>