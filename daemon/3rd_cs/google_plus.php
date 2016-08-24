<?

// require_once('/var/www/daemon/com/config.php');
// require_once('/var/www/daemon/com/func.php');
// require_once('/var/www/daemon/com/db.php');
// require_once('/var/www/daemon/bot/kernel.php');
//require_once('lcheck.php');

$redis=new Redis() or die("Can'f load redis module.");
$redis->connect('127.0.0.1');

date_default_timezone_set('Europe/Moscow');
// error_reporting(E_ERROR);
ignore_user_abort(true);
set_time_limit(0);
ini_set('max_execution_time',0);
ini_set('default_charset','utf-8');
ob_implicit_flush();

function get_gp($id,$ts,$te,$lan)
{
	global $redis;
	$cont_token_gp=$redis->get('at_gp');
	$mcont_token_gp=json_decode($cont_token_gp,true);
	$access_token_gp=$mcont_token_gp[0];
	do
	{
		sleep(1);
		if ($pagetoken!='') $addition='&pageToken='.$pagetoken;
		$cont=parseUrl('https://www.googleapis.com/plus/v1/people/'.$id.'/activities/public?key='.$access_token_gp.$addition);
		echo 'https://www.googleapis.com/plus/v1/people/'.$id.'/activities/public?key='.$access_token_gp.$addition."\n";
		$mcont=json_decode($cont,true);
		$pagetoken=$mcont['nextPageToken'];
		foreach ($mcont['items'] as $key => $item)
		{
			$time=strtotime($item['published']);
			if (($time<$ts) || ($time>$te)) continue;
			$outmas['content'][]=$item['title'].' '.$item['object']['content'];
			$outmas['link'][]=$item['url'];
			$outmas['time'][]=$time;
		}
	}
	while ($time>$ts);
	// print_r($outmas);
	return $outmas;
}

// get_gp('+ulmart',mktime(0,0,0,1,1,2013),mktime(0,0,0,9,20,2013),$lan)

?>