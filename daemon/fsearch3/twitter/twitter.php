<?
require_once('/var/www/daemon/com/config.php');
require_once('/var/www/daemon/com/func.php');
require_once('/var/www/daemon/com/db.php');
require_once('/var/www/daemon/bot/kernel.php');
require_once('/var/www/daemon/fsearch3/twitter/codebird/src/codebird.php');
//require_once('ch.php');

date_default_timezone_set('Europe/Moscow');
// error_reporting(E_ERROR);
ignore_user_abort(true);
set_time_limit(0);
ini_set('max_execution_time',0);
ini_set('default_charset','utf-8');
ob_implicit_flush();

function check_twitter_content($cont)
{
	return intval(json_decode($cont,true));
}

function get_twitter($keyword,$ts,$te,$lan,$proxys)
{	
	global $redis;
	$proxys=json_decode($redis->get('proxy_twitter'),true);
	$tokens=json_decode($redis->get('at_tw'),true);
	// echo $ctokens;
	// $tokens=json_decode($ctokens,true);
	// print_r($tokens);
	shuffle($tokens);
	$token=$tokens[0];
	// print_r($token);
	\Codebird\Codebird::setConsumerKey($token['consumer_key'], $token['consumer_secret']); // static, see 'Using multiple Codebird instances'
	$cb = \Codebird\Codebird::getInstance();
	$cb->setToken($token['access_token'], $token['access_secret']);
	$mas_lan['ru']='ru';
	$mas_lan['en']='en_US';
	$mas_lan['az']='az';
	$tmp_keyword=$keyword;
	$mkeyword=get_simple_query($keyword,'twitter');
	// print_r($mkeyword);
	$i_proxy=0;
	$i=1;
	foreach ($mkeyword as $itemkw)
	{
		echo '/';
		$max_id='';
		$last_time='';
		if ((trim($itemkw)!='') && ($itemkw!=' '))
		{
			$i=1;
			do
			{
				//sleep(1);
				do
				{
					echo '.';
					$params = array(
					    'q' => $itemkw,
					    'count' => 100,
					    'rpp' => 100,
					    'include_entities' => true,
					    'result_type' => 'recent',
					    'locale' => 'ru',
					    'lang' => 'ru',
					    'until' => date('Y-m-d',($te+86400))
					);
					if ($max_id!='') $params['max_id']=$max_id;
					$cont = $cb->search_tweets($params,$proxys[$i_proxy]);
					if (check_twitter_content($cont)==0)
					{
						echo '*';
						$i_proxy++;
					}
				}
				while ((check_twitter_content($cont)==0) && ($i_proxy<count($proxys)));
				//echo 'http://search.twitter.com/search.json?q='.urlencode($itemkw).'&rpp=100&include_entities=true&result_type=mixed&locale='.$lan.'&lang='.$lan.'&until='.date('Y-m-d',$te).'&page='.$i."\n";
				$mas=json_decode($cont,true);
				// print_r($mas);
				$count_twitter+=count($mas['statuses']);
				foreach ($mas['statuses'] as $item)
				{
					$last_time=strtotime($item['created_at']);
					if (check_post($item['text'],$tmp_keyword)==0) continue;
					if ((strtotime($item['created_at'])>=$ts) && (strtotime($item['created_at'])<$te))
					{
						$outmas['content'][]=$item['text'];
						$outmas['link'][]='http://twitter.com/'.$item['user']['screen_name'].'/statuses/'.$item['id_str'];
						$outmas['time'][]=strtotime($item['created_at']);
						// $outmas['nick'][]=$item['user']['name'];
						if (checker_links($item['text'])) $outmas['fulltext'][]='';
						else $outmas['fulltext'][]=$item['text'];
						$outmas['nick'][]=$item['user']['name'];
						$eng['retweet']=$item['retweet_count'];
						$outmas['engage'][]=json_encode($eng);
					}
					if (count($outmas['time'])>100) $outmas=post_slice($outmas);
				}
				$next_result_params=preg_split('/[\?\&]/isu', $mas['search_metadata']['next_results']);
				foreach ($next_result_params as $item)
				{
					if (preg_match('/max\_id\=\d+/isu',$item)) 
					{
						$mid=explode('=', $item);
						// print_r($mid);
						$max_id=$mid[1];
						// echo '!'.$max_id;
						break;
					}
				}
				// print_r($next_result_params);
				// echo $max_id."\n";
				// echo $last_time.' '.$ts."\n";
				$mlast_times[$last_time]++;
				if ($mlast_times[$last_time]>3) break;
			}
			while ($last_time>$ts);
		}
	}
	add_source_log('twitter',intval($count_twitter));
	// print_r($outmas);
	// echo "\n";
	return $outmas;
}

//get_twitter('путин',mktime(0,0,0,1,1,2013),mktime(0,0,0,20,1,2013),'ru',array('212.119.105.65:3128','87.248.226.210:3128','46.50.220.13:3128'));
?>
