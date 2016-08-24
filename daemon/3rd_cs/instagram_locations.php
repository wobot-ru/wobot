<?

// require_once('/var/www/daemon/com/config.php');
// require_once('/var/www/daemon/com/func.php');
// require_once('/var/www/daemon/com/db.php');
// require_once('/var/www/daemon/bot/kernel.php');

// $db = new database();
// $db->connect();

// error_reporting(0);
//ignore_user_abort(true);
set_time_limit(0);
ini_set('max_execution_time',0);
ob_implicit_flush();
ini_set("memory_limit", "2048M");
date_default_timezone_set ( 'Europe/Moscow' );

$access_token='1032401910.ec53e3f.a09b7eceff8c4d1aa86be0b6a867ce25';

function get_instagram_locations($location,$ts,$te)
{
	global $access_token;
	do
	{
		if ($link=='') $link='https://api.instagram.com/v1/locations/'.$location.'/media/recent?access_token='.$access_token;
		else $link=$mcont['pagination']['next_url'];
		$cont = parseUrl($link);
		
		$mcont = json_decode($cont, true);
		// print_r($mcont);
		foreach ($mcont['data'] as $key => $item)
		{
			$time = $item['created_time'];
			$text = $item['caption']['text'];
			$link = $item['link'];
			//echo $time." < ".

			if ($time<$ts || $time>($te+86400)) continue;
			$outmas['link'][]=$link;
			$outmas['content'][]=$text;
			$outmas['time'][]=$time;
			$outmas['engage'][]=0;
			$outmas['adv_engage'][]='';
			$outmas['author_id'][]='';

			//comments
			$comments = $item['comments']['data'];
			//print_r($comments);
			foreach ($comments as $key_comment => $value_comment) {
				$time_comment = $comments[$key_comment]['created_time'];

				if ($time_comment<$ts || $time_comment>($te+86400)) continue;

				$outmas['link'][]=$item['link']."#".(rand(0,500)+$key_comment);
				$comments[$key_comment]['text']=preg_replace('/<[^\<]*?>/isu', ' ', $comments[$key_comment]['text']);
				$comments[$key_comment]['text']=preg_replace('/\s+/isu', ' ', $comments[$key_comment]['text']);
				$outmas['content'][]=$comments[$key_comment]['text'];
				$outmas['time'][]=$time_comment;
				$outmas['engage'][]=0;
				$outmas['adv_engage'][]='';
				$outmas['author_id'][]='';
			}
		}
		echo date('r',$time).' '.date('r',$ts)."\n";
	}
	while (count($mcont['data'])!=0 && $time>$ts);
	print_r($outmas);
	return $outmas;
}

// get_instagram_locations('498079790',mktime(0,0,0,6,1,2015),mktime(0,0,0,8,4,2015));

?>
