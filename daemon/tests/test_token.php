<?

require_once('/var/www/bot/kernel.php');
require_once('/var/www/com/db.php');
require_once('/var/www/com/config.php');
require_once('/var/www/daemon/fsearch3/twitter/codebird/src/codebird.php');

$db=new database();
$db->connect();

$qtoken=$db->query('SELECT * FROM tp_keys WHERE in_use=3');
while ($token=$db->fetch($qtoken))
{
	if ($token['type']=='vk') $vk_tokens[$token['id']]=$token['key'];
	if ($token['type']=='fb') $fb_tokens[$token['id']]=$token['key'];
	if ($token['type']=='gp') $google_tokens[$token['id']]=$token['key'];
	if ($token['type']=='tw') $tw_tokens[$token['id']]=$token['key'];
}

// $vk_tokens[0]='6ae0f09a21fd86388df6e844ae5268f396878b2dfd8e7d37ea18a641252eb943fbac79d9510c7fa211e70';
// $vk_tokens[1]='2af49b2681d87de5f2bae8949d4979d3957c281fb44098ae7ee2c085d815c30925d851be02b0270d4e2a7';
// $vk_tokens[2]='5f04c371670c397e2c5f9115da38e3a2cd3c69e69c66e96af357b4dde670544ea32143a0302413688d82a';
// $vk_tokens[3]='31485842ebb32f95488f1d838cfcfa7072fb31c5aee1875e1eed2a77ca4f70db6daee6ee4b199a57134ad';

// $fb_tokens[0]='AAACQNvcNtEgBABIlzfBUuhmNQHFZArVxW90QJa5vMfe2VmtkWZCPNqEojO9NL70adSpZA7rtGoHa6S6N7RCLqWchPqTmuIXEFGHPVWVNQZDZD';

// $google_tokens[0]='AIzaSyAW5811BL1JjoO1-4wy9CCR3c-oe7A5BnM';
// $google_tokens[1]='AIzaSyAYJ0-HHNSPTZ6km29PlEn2sGuSOXR-zRU';
// $google_tokens[2]='AIzaSyATWJrPep2aprG8dPbqAh4VIx8rFao2Sbk';
// $google_tokens[3]='AIzaSyCfdRWKvjrYONugfSw2pMx_i1qsRWWM-A4';
// $google_tokens[4]='AIzaSyDxv7FwS-ixbHF12yYDDb1O4dKv-HUTKP4';
// $google_tokens[5]='AIzaSyAc7X8b27x3YmzgLF2UQOTx-9mG6e_C9kc';
// $google_tokens[6]='AIzaSyAYClQD_sbE5ubwpuIYqjNhQWrpXsC1LHg';
// $google_tokens[7]='AIzaSyBAn0cWxq4ThF5R759Vh6LTfxFslWYhpwc';
// $google_tokens[8]='AIzaSyDwqIJwBACC_if5TAeGBZzzEsViRkWUMcU';
// $google_tokens[9]='AIzaSyAnweaAhuk3j0hUOVXr5b4YwRpo3vFUxTU';

function test_vk($tokens)
{
	global $db;
	foreach ($tokens as $key => $item)
	{
		echo $item."\n";
		do
		{
			$cont=file_get_contents('https://api.vkontakte.ru/method/getProfiles?uids=1003145,1555432&fields=uid,counters,first_name,last_name,nickname,sex,bdate,city,country,timezone,photo,photo_medium,photo_big,domain,has_mobile,rate,contacts,education&access_token='.$item);
			$mcont=json_decode($cont,true);
			sleep(3);
			$attemp++;
		}
		while (($mcont['error']['error_code']!='')&&($attemp<3));
		$t.=$item.' '.($mcont['error']['error_code']==''?'ok':'<b>fail '.$mcont['error']['error_code'].' '.$mcont['error']['error_msg'].'</b>').'<br>';
		// if ($mcont['error']['error_code']!='') $db->query('UPDATE tp_keys SET in_use=4 WHERE id='.$key);
	}
	return $t;
}

function test_fb($tokens)
{
	global $db;
	foreach ($tokens as $key => $item)
	{
		$cont=parseUrl('https://graph.facebook.com/wobot.ru/feed?access_token='.$item);
		$mcont=json_decode($cont,true);
		print_r($mcont);
		$t.=$item.' '.($mcont['error']['code']==''?'ok':'<b>fail</b>').'<br>';
		if ($mcont['error']['code']!='') $db->query('UPDATE tp_keys SET in_use=4 WHERE id='.$key);
	}
	return $t;
}

function test_gp($tokens)
{
	global $db;
	foreach ($tokens as $key => $item)
	{
		$cont=parseUrl('https://www.googleapis.com/plus/v1/activities?query=wobot&key='.$item);
		$mcont=json_decode($cont,true);
		// print_r($mcont);
		$t.=$item.' '.($mcont['error']['code']==''?'ok':'<b>fail</b>').'<br>';
		if ($mcont['error']['code']!='') $db->query('UPDATE tp_keys SET in_use=4 WHERE id='.$key);
	}
	return $t;
}

function test_tw($tokens)
{
	global $db;
	foreach ($tokens as $key => $item)
	{
		$item=json_decode($item,true);
		print_r($item);
		\Codebird\Codebird::setConsumerKey($item['consumer_key'], $item['consumer_secret']); // static, see 'Using multiple Codebird instances'
		$cb = \Codebird\Codebird::getInstance();
		$cb->setToken($item['access_token'], $item['access_secret']);
		$params = array(
		    'q' => 'путин',
		    'count' => 100,
		    'rpp' => 100,
		    'include_entities' => true,
		    'result_type' => 'recent',
		    'locale' => 'ru',
		    'lang' => 'ru',
		    'until' => date('Y-m-d',($te+86400))
		);
		$cont = $cb->search_tweets($params,'');
		$mcont=json_decode($cont,true);
		print_r($mcont);
		$t.=json_encode($item).' '.($mcont['errors'][0]['code']==''||$mcont['errors'][0]['code']==88?'ok':'<b>fail</b>').'<br>';
		if ($mcont['errors'][0]['code']!=''&&$mcont['errors'][0]['code']!=88) $db->query('UPDATE tp_keys SET in_use=4 WHERE id='.$key);
	}
	return $t;
}

$headers  = "From: noreply2@wobot.ru\r\n"; 
$headers .= "Bcc: noreply2@wobot.ru\r\n";
$headers .= 'MIME-Version: 1.0' . "\r\n";
$headers .= 'Content-type: text/html; charset=utf-8'."\r\n";
mail('zmei123@yandex.ru','Токены','VK:<br>'.test_vk($vk_tokens).'FB:<br>'.test_fb($fb_tokens).'GP:<br>'.test_gp($google_tokens).'TW:<br>'.test_tw($tw_tokens),$headers);
mail('r@wobot.co','Токены','VK:<br>'.test_vk($vk_tokens).'FB:<br>'.test_fb($fb_tokens).'GP:<br>'.test_gp($google_tokens).'TW:<br>'.test_tw($tw_tokens),$headers);

?>