<?

header("Cache-Control: no-store, no-cache, must-revalidate");

require_once('/var/www/com/config.php');
require_once('/var/www/com/func.php');
require_once('/var/www/com/db.php');
require_once('/var/www/bot/kernel.php');

error_reporting(E_ERROR | E_PARSE);
ignore_user_abort(true);
set_time_limit(0);
ini_set('max_execution_time',0);
ini_set('default_charset','utf-8');
ob_implicit_flush();

$db = new database();
$db->connect();


//sleep(3);

$datatw1 = array( 	 	 	 	 	
		'post_source'=>'twitter.com',
		'post_avatar'=>'http://wobot.ru/images/wobot-logo.gif',
		'post_nick'=>'test140',
		'post_msg'=>'0123456789012345678901234567890',
		'post_date'=>time()-rand(1,10)
);

$datatw2 = array( 	 	 	 	 	
		'post_source'=>'twitter.com',
		'post_avatar'=>'http://wobot.ru/images/wobot-logo.gif',
		'post_nick'=>'test140',
		'post_msg'=>'01234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789',
		'post_date'=>time()-rand(1,5)
);

$datafb1 = array( 	 	 	 	 	
		'post_source'=>'facebook.com',
		'post_avatar'=>'http://wobot.ru/images/wobot-logo.gif',
		'post_nick'=>'testfb',
		'post_msg'=>'test facebook msg, test facebook msg, test facebook msg, test facebook msg'."\n".'test facebook msg, test facebook msg, test facebook msg',
		'post_date'=>time()-rand(1,10)
);

$datafb2 = array( 	 	 	 	 	
		'post_source'=>'facebook.com',
		'post_avatar'=>'http://wobot.ru/images/wobot-logo.gif',
		'post_nick'=>'testfb',
		'post_msg'=>'test facebook msg, test facebook msg, test facebook msg, test facebook msg, test facebook msg, test facebook msg, test facebook msg, test facebook msg'."\n".'test facebook msg, test facebook msg, test facebook msg',
		'post_date'=>time()-rand(1,5)
);

unset($out);
/*$out[]=$datatw1;
$out[]=$datafb1;
$out[]=$datatw2;
$out[]=$datafb2;
*/

$res=$db->query('SELECT * FROM riw_setup WHERE setup_id=1');
$setup = $db->fetch($res);

$out[]= array( 	 	 	 	 	
		'post_source'=>'interval',
		'post_avatar'=>$setup['setup_interval'],
		'post_nick'=>'',
		'post_msg'=>'',
		'post_date'=>0
);

$i=1;

$res=$db->query('SELECT * FROM riw_post ORDER BY post_date DESC');
while ($post = $db->fetch($res)) {
	if (strlen($post['post_url'])>0) $post['post_avatar']=$post['post_url'];
	else $post['post_avatar']='http://wobot.ru/images/wobot-logo.gif';
	if (mb_strlen($post['post_nick'], 'UTF-8')>40)
	{
	$post['post_nick']=str_replace("\n", " ", mb_substr($post['post_nick'],0,40,'UTF-8'));
	$post['post_nick']=$post['post_nick'].'...';
	}
	if (mb_strlen($post['post_msg'], 'UTF-8')>80)
	{
	$post['post_msg']=str_replace("\n", " ", mb_substr($post['post_msg'],0,80,'UTF-8'));
	$post['post_msg']=$post['post_msg'].'...';
	}
	
	$post['post_source']='twitter'.$i;
	if ($i=='1') $i='2';
	else $i=1;
	$out[]=$post;
}
/*
$res=$db->query('SELECT * FROM riw_post WHERE post_source="facebook.com" ORDER BY post_date ASC');
while ($post = $db->fetch($res)) {
	if (strlen($post['post_url'])>0) $post['post_avatar']=$post['post_url'];
	else $post['post_avatar']='http://wobot.ru/images/wobot-logo.gif';
	if (mb_strlen($post['post_nick'], 'UTF-8')>40)
	{
	$post['post_nick']=str_replace("\n", " ", mb_substr($post['post_nick'],0,40,'UTF-8'));
	$post['post_nick']=$post['post_nick'].'...';
	}
	if (mb_strlen($post['post_msg'], 'UTF-8')>80)
	{
	$post['post_msg']=str_replace("\n", " ", mb_substr($post['post_msg'],0,80,'UTF-8'));
	$post['post_msg']=$post['post_msg'].'...';
	}
	$out[]=$post;
}
*/
$res=$db->query('DELETE FROM riw_post');
echo json_encode($out, true);

?>