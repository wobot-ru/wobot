<?

// require_once('/var/www/daemon/com/config.php');
// require_once('/var/www/daemon/com/func.php');
// require_once('/var/www/daemon/com/db.php');
// require_once('/var/www/daemon/bot/kernel.php');

$db = new database();
$db->connect();

// error_reporting(0);
//ignore_user_abort(true);
set_time_limit(0);
ini_set('max_execution_time',0);
ob_implicit_flush();
ini_set("memory_limit", "2048M");
date_default_timezone_set ( 'Europe/Moscow' );


function get_instagram($grid,$ts,$te)
{
	global $assok_ok;
	$cont = parseUrl('http://instagram.com/'.$grid.'/media');
	
	//print_r(json_decode($cont, true));

	$tmp = json_decode($cont, true);

	$out = $tmp['items'];
	/*sleep(1);
	$regex='/<span class="feed_date">(?<time>.*?)<\/span>.*?<div id="hook_ActivateLinks_\d+" itemprop="text" link-class="rev_cnt_a-in-txt" class="media-text_cnt_tx">(?<cont>.*?)<\/div><a class="rev_cnt_a" href="(?<link>.*?)"><\/a>/isu';
	preg_match_all($regex, $cont, $out);
*/	
	// print_r($out);
	foreach ($out as $key => $item)
	{
		//echo " ".($outmas['link'][]=$out[$key]['link'])."\n";
		//die();
		$time = $out[$key]['caption']['created_time'];

		//echo $time." < ".

		if ($time<$ts || $time>($te+86400)) continue;
		$outmas['link'][]=$out[$key]['link'];
		$out[$key]['caption']['text']=preg_replace('/<[^\<]*?>/isu', ' ', $out[$key]['caption']['text']);
		$out[$key]['caption']['text']=preg_replace('/\s+/isu', ' ', $out[$key]['caption']['text']);
		$outmas['content'][]=$out[$key]['caption']['text'];
		$outmas['time'][]=$time;
		$outmas['engage'][]=0;
		$outmas['adv_engage'][]='';
		$outmas['author_id'][]='';

		//comments
		$comments = $out[$key]['comments']['data'];
		//print_r($comments);
		foreach ($comments as $key => $value) {
			$time = $comments[$key]['created_time'];

			if ($time<$ts || $time>($te+86400)) continue;

			$outmas['link'][]=$out[$key]['link']."#".(rand(0,500)+$key);
			$comments[$key]['text']=preg_replace('/<[^\<]*?>/isu', ' ', $comments[$key]['text']);
			$comments[$key]['text']=preg_replace('/\s+/isu', ' ', $comments[$key]['text']);
			$outmas['content'][]=$comments[$key]['text'];
			$outmas['time'][]=$time;
			$outmas['engage'][]=0;
			$outmas['adv_engage'][]='';
			$outmas['author_id'][]='';
		}
	}
	//print_r($outmas);
	return $outmas;
}

//get_instagram('kadyrov_95',mktime(0,0,0,10,13,2014),mktime(0,0,0,10,14,2014));

?>
