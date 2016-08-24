<?

require_once('/var/www/daemon/com/db.php');
require_once('/var/www/daemon/com/config.php');
require_once('/var/www/daemon/Engagement/rEngtoFt/facebook_eg_job.php');
require_once('/var/www/daemon/Engagement/rEngtoFt/livejournal_eg_job.php');
require_once('/var/www/daemon/Engagement/rEngtoFt/twitter_eg_job.php');
require_once('/var/www/daemon/Engagement/rEngtoFt/vkontakte_eg_job.php');
require_once('/var/www/daemon/bot/kernel.php');

$db=new database();
$db->connect();

$order_id=$_SERVER['argv'][1];

$qpost=$db->query('SELECT post_id,post_host,post_link FROM blog_post WHERE order_id='.$order_id.' AND post_time>'.mktime(0,0,0,date('n')-1,date('j'),date('Y')).' AND post_host IN (\'twitter.com\',\'livejournal.com\',\'vk.com\',\'facebook.com\')');
while ($post=$db->fetch($qpost))
{
	$hn=$post['post_host'];
	$link=$post['post_link'];

	$cont=parseUrl($link);
	preg_match_all('/charset=([-a-z0-9_]+)/is',$cont,$charset);
	if (($hn!='twitter.com')&&($hn!='livejournal.com'))
	{
		if (($charset[1][0]!='') || ($charset[1][0]!='utf-8'))
		{
			if (mb_strtolower($charset[1][0],'UTF-8')!="utf-8")
			{	
				$cont=iconv($charset[1][0], "utf-8", $cont);
			}
		}
	}
	$cont=preg_replace('/(<script[^<]*?>.*?<\/script>)/isu',' ',$cont);
	$cont=preg_replace('/<style[^<]*?type=[\"\']text\/css[\"\']>.*?<\/style>/isu',' ',$cont);

	if ($hn=='twitter.com') $ret_eg=get_retweets($link,$cont);
	if ($hn=='livejournal.com') $ret_eg=get_comments($link,$cont);
	if (($hn=='vk.com')||($hn=='vkontakte.ru')) $ret_eg=get_vk($link,$cont);
	if ($hn=='facebook.com') $ret_eg=get_likes($link);
	print_r($ret_eg);

	echo 'UPDATE blog_post SET post_engage='.intval($ret_eg['count']).',post_advengage=\''.addslashes(json_encode($ret_eg['data'])).'\' WHERE post_id='.$post['post_id']."\n";
	$db->query('UPDATE blog_post SET post_engage='.intval($ret_eg['count']).',post_advengage=\''.addslashes(json_encode($ret_eg['data'])).'\' WHERE post_id='.$post['post_id']);
}

?>