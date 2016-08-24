<?

die();

require_once('/var/www/daemon/com/db.php');
require_once('/var/www/daemon/com/config.php');
require_once('/var/www/daemon/bot/kernel.php');
require_once('/var/www/daemon/com/qlib.php');
require_once('/var/www/daemon/fsearch3/ch.php');

// die();

proc_nice(10);

$db=new database();
$db->connect();

error_reporting(0);

$qorder=$db->query('SELECT order_id,order_keyword,order_lang,order_start,order_end FROM blog_orders WHERE order_id='.$_SERVER['argv'][1]);
$order=$db->fetch($qorder);
print_r($order);
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
$keyword=preg_replace('/\~+[а-яА-Яa-zA-Z\-\ \.\'\"]+/isu','',$order['order_keyword']);
$keyword=preg_replace('/[^а-яА-Яa-zA-Z0-9\-\'\’\.]/isu','  ',$keyword);
$keyword=explode('  ',$keyword);
foreach ($keyword as $item)
{
	if (mb_strlen($item,'UTF-8')<3) continue;
	if (trim($item)=='') continue;
	$q.=$or.'LOWER(order_keyword) LIKE \'%'.mb_strtolower(addslashes($item)).'%\'';
	$or=' OR ';
}
//echo 'SELECT order_id,order_keyword FROM blog_orders WHERE ('.$q.') AND order_id!='.$_SERVER['argv'][1];
$q_sim_order=$db->query('SELECT order_id,order_keyword FROM blog_orders WHERE ('.$q.') AND order_id!='.$_SERVER['argv'][1]);
$or='';
while ($sim_order=$db->fetch($q_sim_order))
{
	$qp.=$or.'order_id='.$sim_order['order_id'];
	$or=' OR ';
}
if ($order['order_end']>=time())
{
	$cstart=mktime(0,0,0,date('n'),date('j'),date('Y'));
	$cend=mktime(0,0,0,date('n'),date('j'),date('Y'));
}
else
{
	$cstart=$order['order_end'];
	$cend=$order['order_end'];
}
$ful_query=construct_sql_query($order['order_keyword'],'b.ful_com_post');
$post_query=construct_sql_query($order['order_keyword'],'a.post_content');
$offset=0;
do
{
	echo 'SELECT post_link,post_host,post_content,blog_id,post_time,post_engage,post_advengage,ful_com_post FROM blog_post as a LEFT JOIN blog_full_com as b ON a.post_id=b.ful_com_post_id WHERE (post_time>='.$order['order_start'].' AND post_time<'.($order['order_end']+86400).') AND (('.$ful_query.') OR ('.$post_query.')) LIMIT '.($offset*100).',100';
	$qpost=$db->query('SELECT post_link,post_host,post_content,blog_id,post_time,post_engage,post_advengage,ful_com_post FROM blog_post as a LEFT JOIN blog_full_com as b ON a.post_id=b.ful_com_post_id WHERE (post_time>='.$order['order_start'].' AND post_time<'.($order['order_end']+86400).') AND (('.$ful_query.') OR ('.$post_query.')) LIMIT '.($offset*100).',100');
	$offset++;
	while ($post=$db->fetch($qpost))
	{
		echo '.';
		//echo strip_tags($post['post_content'])."\n";
		if ((check_post(strip_tags($post['post_content']),$order['order_keyword'])==0) && (check_post(strip_tags($post['ful_com_post']),$order['order_keyword'])==0)) continue;
		echo '-';
		$qw=$db->query('SELECT post_id FROM blog_post WHERE post_link=\''.addslashes($post['post_link']).'\' AND order_id='.$_SERVER['argv'][1].' LIMIT 1');
		if (mysql_num_rows($qw)!=0) continue;
		echo '*';
		if (isset($rep[$post['post_link']])) continue;
		echo '/';
		if (check_local($post['post_content'],$text_lang)==0) continue;
		echo '|';
		if (($post['post_time']>($order['order_end']==0?time():$order['order_end'])) || ($post['post_time']<$order['order_start'])) continue;
		$rep[$post['post_link']]=1;
		if ($post['ful_com_post']=='')
		{
			if (checker_links($post['post_link'])) $post['ful_com_post']='';
			else $post['ful_com_post']=$post['post_content'];
		}
		print_r($post);
		//echo '.'.$count."\n";
		$count++;
		if ($count==100)
		{
			//echo "\n".'100!!!'."\n";
			$cj=parseUrl('http://localhost/tools/cashjob.php?order_id='.intval($order['order_id']).'&start='.$cstart.'&end='.$cend);
			$count=0;
			$cstart=$post['post_time'];
			$cend=$post['post_time'];
			$db->query('UPDATE blog_orders SET third_sources=2 WHERE order_id='.$order['order_id']);
		}
		if ($post['post_time']<$cstart) $cstart=mktime(0,0,0,date('n',$post['post_time']),date('j',$post['post_time']),date('Y',$post['post_time']));
		if ($post['post_time']>$cend) $cend=mktime(0,0,0,date('n',$post['post_time']),date('j',$post['post_time']),date('Y',$post['post_time']));
		echo 'INSERT INTO blog_post_prev (order_id,post_link,post_host,post_time,post_content,blog_id,post_engage,post_advengage) VALUES ('.$_SERVER['argv'][1].',\''.$post['post_link'].'\',\''.$post['post_host'].'\','.$post['post_time'].',\''.addslashes($post['post_content']).'\','.$post['blog_id'].','.$post['post_engage'].',\''.$post['post_advengage'].'\')'."\n";
		$db->query('INSERT INTO blog_post_prev (order_id,post_link,post_host,post_time,post_content,blog_id,post_engage,post_advengage,post_ful_com) VALUES ('.$_SERVER['argv'][1].',\''.$post['post_link'].'\',\''.$post['post_host'].'\','.$post['post_time'].',\''.addslashes($post['post_content']).'\','.$post['blog_id'].','.$post['post_engage'].',\''.$post['post_advengage'].'\',\''.addslashes($post['ful_com_post']).'\')');
	}
}
while ($db->num_rows($qpost)!=0);
parseUrl('http://localhost/tools/cashjob.php?order_id='.intval($_SERVER['argv'][1]));
?>