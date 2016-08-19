<?

//----дублирование темы 3533 в демо кабинетах------

require_once('/var/www/com/db.php');
require_once('/var/www/com/config.php');
require_once('/var/www/bot/kernel.php');

error_reporting(0);

if ($_SERVER['argv'][1]!='') $user_id=intval($_SERVER['argv'][1]);
else die();

$db=new database();
$db->connect();

//----берем инфу о теме 3533----
$qorder=$db->query('SELECT * FROM blog_orders WHERE order_id=3533');
$order=$db->fetch($qorder);

//----берем инфу о демо кабинете в который копировать----
$quser=$db->query('SELECT * FROM user_tariff WHERE user_id='.$user_id);
$user_info=$db->fetch($quser);

//----добавляем тему в кабинет-----
$db->query('INSERT INTO blog_orders (order_name,order_keyword,user_id,ut_id,order_start,order_end,order_last,ful_com,order_engage,order_nastr,third_sources,order_lang) VALUES (\''.addslashes($order['order_name']).'\',\''.addslashes($order['order_keyword']).'\','.$user_id.','.$user_info['ut_id'].','.mktime(0,0,0,date('n')-1,date('j'),date('Y')).','.mktime(0,0,0,date('n'),date('j')+7,date('Y')).','.$order['order_last'].',1,1,1,'.time().',2)');
$new_order_id=$db->insert_id();

$offset=0;

//----добавляем посты в кабинет-----
do
{
	echo '.';
	$qpost=$db->query('SELECT * FROM blog_post as a LEFT JOIN blog_full_com as b ON a.post_id=b.ful_com_post_id WHERE order_id=3533 AND post_time>='.mktime(0,0,0,date('n')-1,date('j'),date('Y')).' LIMIT '.$offset.',100');
	while ($post=$db->fetch($qpost))
	{
		$db->query('INSERT INTO blog_post (order_id,post_link,post_host,post_time,post_content,blog_id,post_nastr,post_spam,post_fav,post_tag,post_engage,post_advengage) VALUES ('.$new_order_id.',\''.addslashes($post['post_link']).'\',\''.addslashes($post['post_host']).'\',\''.$post['post_time'].'\',\''.addslashes($post['post_content']).'\','.$post['blog_id'].','.$post['post_nastr'].','.$post['post_spam'].','.$post['post_fav'].',\''.$post['post_tag'].'\',\''.$post['post_engage'].'\',\''.$post['post_advengage'].'\')');
		$db->query('INSERT INTO blog_full_com (ful_com_post_id,ful_com_order_id,ful_com_post) VALUES (\''.addslashes($db->insert_id()).'\','.$new_order_id.',\''.addslashes($post['ful_com_post']).'\')');
	}
	$offset+=100;
}
while ($db->num_rows($qpost)!=0);

//----добавляем теги в каинет----
$qtag=$db->query('SELECT * FROM blog_tag WHERE order_id=3533');
while ($tag=$db->fetch($qtag))
{
	$db->query('INSERT INTO blog_tag (user_id,order_id,tag_name,tag_tag,tag_auto,tag_kw,tag_sw) VALUES ('.$user_id.','.$new_order_id.',\''.addslashes($tag['tag_name']).'\','.$tag['tag_tag'].','.$tag['tag_auto'].',\''.addslashes($tag['tag_kw']).'\',\''.addslashes($tag['tag_sw']).'\')');
}

// parseUrl('http://188.120.239.225/tools/cashjob.php?order_id='.intval($new_order_id));
$descriptorspec=array(
	0 => array("file","/dev/null","a"),
	1 => array("file","/dev/null","a"),
	2 => array("file","/dev/null","a")
	);

$cwd='/var/www/bot/';
$end=array();

$process=proc_open('php /var/www/cashjob/cashjob.php '.intval($new_order_id).' &',$descriptorspec,$pipes,$cwd,$end);/* or {
	echo json_encode(array('status'=>'fail'), true);
	die();
};*/

if (is_resource($process))
{
	//echo 'return: '.$return_value=proc_close($process);
	if (intval($return_value=proc_close($process))==0) echo json_encode(array('status'=>'ok'), true);
}
?>