<?

require_once('/var/www/com/db.php');
require_once('/var/www/com/config.php');
require_once('/var/www/daemon/com/users.php');
require_once('/var/www/daemon/fsearch3/ch.php');

$db=new database();
$db->connect();

$redis=new Redis() or die("Can'f load redis module.");
$redis->connect('127.0.0.1');

$user=new users();

$eng_host['facebook.com']=1;
$eng_host['vk.com']=1;
$eng_host['twitter.com']=1;
$eng_host['livejournal.com']=1;
// echo 1;
// echo "\n".'====='."\n";
// echo $_POST;
// print_r($_POST);
// print_r(json_decode($_POST,true));
// echo "\n".'++++++'."\n";
// echo base64_decode($_POST['data']);
$posts=json_decode(base64_decode($_POST['data']),true);
echo "\n".'count_recive: '.count($posts)."\n";

$order_id=$posts[0]['order_id'];
$order=json_decode(file_get_contents('http://localhost/api/service/order.php?order_id='.$order_id),true);
$filters=json_decode($order['order_settings'],true);
$spams=json_decode($order['order_spam'],true);
$qtariff=$db->query('SELECT * FROM user_tariff WHERE user_id='.$order['user_id']);
$tariff=$db->fetch($qtariff);
// echo 2;
switch ($order['order_lang']) {
    case 0:
        $text_lang='ru';
        break;
    case 1:
        $text_lang='en';
        break;
    case 2:
        $text_lang='ru';
        break;
	case 4:
		$text_lang='az';
		break;
}
echo 3;
foreach ($posts as $post)
{
	print_r($post);
	$post['post_content']=base64_decode($post['post_content']);
	$post['post_ful_com']=base64_decode($post['post_ful_com']);
	$post['post_link']=base64_decode($post['post_link']);
	echo 4;
	print_r($post);
	$post['post_content']=preg_replace('/<[^\<]*?>/isu', ' ', $post['post_content']);
	$post['post_content']=preg_replace('/\s+/isu', ' ', $post['post_content']);
	print_r($post);
	// if (check_post($post['post_content'],$order['order_keyword'])==0) continue;
	// if (check_local($post['post_content'],$text_lang)==0) continue;
	$hn=parse_url($post['post_link']);
	$hn=$hn['host'];
	$ahn=explode('.',$hn);
	$hn = $ahn[count($ahn)-2].'.'.$ahn[count($ahn)-1];
	$hh=$ahn[count($ahn)-2];
	$blog_id=$user->get_url($post['post_link']);
	if (isset($spams[$blog_id])) $isspam=1;

	unset($queue_item);
	$queue_item['order_id']=$order['order_id'];
	$queue_item['post_link']=$post['post_link'];
	$queue_item['post_host']=$hn;
	$queue_item['post_time']=$post['post_time'];
	$queue_item['post_content']=addslashes(mb_substr($post['post_content'],0,150,'UTF-8'));
	$queue_item['post_engage']=($eng_host[$hn]==1?'-1':'0');
	$queue_item['post_advengage']='';
	$queue_item['blog_id']=$blog_id;
	$queue_item['post_spam']=intval($isspam);
	$queue_item['post_ful_com']=($post['post_ful_com']==''?'':$post['post_ful_com']);
	$queue_item['order_keyword']=$order['order_keyword'];
	$queue_item['order_name']=$order['order_name'];
	$queue_item['order_start']=$order['order_start'];
	$queue_item['order_end']=$order['order_end'];
	$queue_item['order_settings']=$order['order_settings'];
	$queue_item['user_id']=$order['user_id'];
	$queue_item['tariff_id']=$tariff['tariff_id'];
	$queue_item['ut_date']=$tariff['ut_date'];
	$queue_item['ut_id']=$tariff['ut_id'];
	print_r($queue_item);
	echo '.';
	$redis->sAdd('prev_queue',json_encode($queue_item));	

	// $qw='INSERT INTO blog_post_prev (order_id,post_link,post_host,post_content,post_time,blog_id,post_spam,post_engage,post_ful_com) VALUES ('.$post['order_id'].',\''.addslashes($post['post_link']).'\',\''.$hn.'\',\''.addslashes(mb_substr($post['post_content'],0,150,'UTF-8')).'\','.$post['post_time'].','.$blog_id.','.($isspam==0?0:1).','.($eng_host[$hn]==1?'-1':'0').',\''.addslashes($post['post_content']).'\')';
	// echo '<br>'.$qw."\n".'<br>';
	// $db->query($qw);
}

?>