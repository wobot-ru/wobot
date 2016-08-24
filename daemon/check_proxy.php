<?

require_once('bot/kernel.php');
require_once('com/db.php');
require_once('com/config.php');

$db=new database();
$db->connect();

// $qproxy=$db->query('SELECT * FROM tp_proxys ORDER BY id ASC');
// while ($proxy=$db->fetch($qproxy))
// {
// 	$q='';
// 	print_r($proxy);
// 	$time=microtime(true);
// 	$cont=parseURLproxy( 'http://blogs.yandex.ru/search.rss?text=%D0%BF%D1%83%D1%82%D0%B8%D0%BD',$proxy['proxy'] );
// 	$ytime=(microtime(true)-$time);	
// 	$mas=simplexml_load_string($cont);
// 	$json = json_encode($mas);
// 	$mas= json_decode($json,true);
// 	if (count($mas['channel']['item'])!=0) $q.='yandex_delay='.intval($ytime*1000);
// 	else $q.='yandex_delay=0';
// 	echo '.';

// 	$time=microtime(true);
// 	$cont=parseURLproxy( 'http://search.twitter.com/search.json?q=windows',$proxy['proxy'] );
// 	$ttime=(microtime(true)-$time);	
// 	$mas= json_decode($cont,true);
// 	if (is_array($mas)) $q.=',twitter_delay='.intval($ttime*1000);
// 	else $q.=',twitter_delay=0';
// 	echo '*';

// 	$time=microtime(true);
// 	$cont=parseURLproxy( 'api.vkontakte.ru/method/newsfeed.search?q=путин',$proxy['proxy'] );
// 	$vtime=(microtime(true)-$time);	
// 	$mas= json_decode($cont,true);
// 	if (count($mas['response'])>2) $q.=',vk_delay='.intval($vtime*1000);
// 	else $q.=',vk_delay=0';
// 	echo '/'."\n";

// 	echo 'UPDATE tp_proxys SET '.$q.' WHERE id='.$proxy['id']."\n";
// 	$db->query('UPDATE tp_proxys SET '.$q.' WHERE id='.$proxy['id']);
// }

while (1)
{
	$qproxy=$db->query('SELECT * FROM tp_proxys ORDER BY id ASC');
	while ($proxy=$db->fetch($qproxy))
	{
		$q='';
		print_r($proxy);
		$time=microtime(true);
		$cont=parseURLproxy( 'http://91.218.246.79/tools/get_ok.php',$proxy['proxy'] );
		$ytime=(microtime(true)-$time);	
		echo '|'.$cont.'|'."\n";
		$mas=json_decode($cont,true);
		// print_r($mas);
		if ($mas['status']=='ok') $q.='valid=1,response_time='.intval($ytime*1000).',attemp=0';
		else $q.='valid=0,attemp=attemp+1';
		echo 'UPDATE tp_proxys SET '.$q.' WHERE id='.$proxy['id']."\n";

		$db->query('UPDATE tp_proxys SET '.$q.' WHERE id='.$proxy['id']);
	}
}

?>