<?

require_once('/var/www/api/0/auth.php');
require_once('/var/www/new/bot/kernel.php');
require_once('/var/www/daemon/userjob/com/tmhOAuth.php');
//require_once('/var/www/userjob/com/db.php');
//require_once('/var/www/userjob/com/config.php');

$db=new database();
$db->connect();

echo '<meta http-equiv="content-type" content="text/html; charset=UTF-8" />';
auth();
//print_r($user);
//echo 'SELECT * FROM tp_keys as a LEFT JOIN users AS b on a.user_id=b.user_id WHERE a.user_id='.$user['user_id'];
if (intval($_GET['del_id'])!=0)
{
	//echo 'DELETE FROM tp_keys WHERE id='.intval($_GET['del_id']);
	$db->query('DELETE FROM tp_keys WHERE id='.intval($_GET['del_id']).' AND user_id='.$user['user_id']);
}
echo '<a href="http://production.wobot.ru/facebook/facebook.php">ADD facebook</a><br>';
echo '<a href="http://production.wobot.ru/vkontakte/vkontakte.php">ADD vk</a><br>';
echo '<a href="http://production.wobot.ru/twitter/index.php?start=1">ADD twitter</a><br>';
$qkeys=$db->query('SELECT * FROM tp_keys as a LEFT JOIN users AS b on a.user_id=b.user_id WHERE a.user_id='.$user['user_id']);
while ($key=$db->fetch($qkeys))
{
	echo '<br>';
	if ($key['type']=='fb')
	{
		//echo 123;
		//echo 'https://graph.facebook.com/me?access_token='.$key['key'];
		$cont=parseUrl('https://graph.facebook.com/me?access_token='.$key['key']);
		$mas=json_decode($cont,true);
		if (count($mas['error'])==0)
		{
			echo '<a href="'.$mas['link'].'"><img height="64" src="'.preg_replace('/http\:\/\/www\.facebook/isu','https://graph.facebook',$mas['link']).'/picture">'.$mas['name'].'</a>';
		}
		else
		{
			echo '<a href="http://production.wobot.ru/facebook/facebook.php">ADD facebook</a>';
		}
		echo ' <a href="?del_id='.$key['id'].'">Delete token</a>';
	}
	if ($key['type']=='vk')
	{
		$cont=parseUrl('https://api.vkontakte.ru/method/getUserInfoEx?access_token='.$key['key']);
		//echo $cont;
		$mas=json_decode($cont,true);
		if (count($mas['error'])==0)
		{
			echo '<a href="http://vk.com/id'.$mas['response']['user_id'].'"><img height="64" src="'.preg_replace('/g00000/isu', 'u'.$mas['response']['user_id'], $mas['response']['user_photo']).'">'.$mas['response']['user_name'].'</a>';
		}
		else
		{
			echo '<a href="http://production.wobot.ru/vkontakte/vkontakte.php">ADD vk</a><br>';
		}
		echo ' <a href="?del_id='.$key['id'].'">Delete token</a>';
	}
	if ($key['type']=='tw')
	{
		$m=json_decode($key['key'],true);
		//print_r($m);
		$tmhOAuth = new tmhOAuth($m[0]);
		$tmhOAuth->request('GET', $tmhOAuth->url('1/account/verify_credentials', 'json'));
		//echo $tmhOAuth->response['code']."\n";
	    if ($tmhOAuth->response['code'] == 200) 
	    {
			$user=json_decode($tmhOAuth->response['response'],true);
			//print_r($user);
			echo '<a href="http://twitter.com/'.$user['screen_name'].'"><img height="64" src="'.$user['profile_image_url'].'">'.$user['name'].'</a>';
		}
		else
		{
			echo '<a href="http://production.wobot.ru/twitter/index.php?start=1">ADD twitter</a><br>';
		}
		echo ' <a href="?del_id='.$key['id'].'">Delete token</a>';
	}
	//print_r($key);
}



?>