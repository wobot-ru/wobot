<?

// сборщик комментариев с youtube.com

require_once('/var/www/daemon/com/config.php');
require_once('/var/www/daemon/com/func.php');
require_once('/var/www/daemon/com/db.php');
require_once('/var/www/daemon/bot/kernel.php');
/*require_once('/var/www/userjob/get_vkontakte.php');
require_once('/var/www/userjob/get_twitter.php');
require_once('/var/www/userjob/get_livejournal.php');*/

require_once('youtube.php');

require_once('/var/www/daemon/com/infix.php');

date_default_timezone_set('Europe/Moscow');
error_reporting(E_ERROR);
ignore_user_abort(true);
set_time_limit(0);
ini_set('max_execution_time',0);
ini_set('default_charset','utf-8');
ob_implicit_flush();

$db = new database();
$db->connect();

$ressec=$db->query('SELECT order_id,order_end,order_start,order_keyword,ind_id,ind,ind_last FROM youtube_id as a LEFT JOIN blog_orders as b ON a.ord_id=b.order_id WHERE b.order_end>'.time().' ORDER BY ind_id DESC');
echo 'SELECT order_id,order_end,order_start,order_keyword,ind_id,ind,ind_last FROM youtube_id as a LEFT JOIN blog_orders as b ON a.ord_id=b.order_id WHERE b.order_end>'.time().' ORDER BY ind_id DESC';

while($blog=$db->fetch($ressec))
{
	print_r($blog);
	$mstart=$blog['ind_last'];
	if ($mstart<$blog['order_start']) $mstart=$blog['order_start'];
	$mend=time();
	unset($m1);

	echo $mstart.' '.$mend.' '.$blog['order_id']."\n";
	echo $blog['ind'].' '.$blog['ind_id']."\n";
	$mas=get_comment_youtube($blog['ind'],$mstart,$mend);
	foreach ($mas['link'] as $key => $it)
	{
		$m1['link'][]=$it;
		$m1['content'][]=$mas['content'][$key];
		$m1['time'][]=$mas['time'][$key];
	}
	unset($mas);
	//echo '1 YANDEX'."\n";
	print_r($m1);
	// die();
	//continue;
	foreach ($m1['link'] as $key => $item)
	{
		if (check_post($m1['content'][$key],$blog['order_keyword'])==0) continue;
		$hn='';
		$hn=parse_url($item);
	    $hn=$hn['host'];
	    $ahn=explode('.',$hn);
	    $hn = $ahn[count($ahn)-2].'.'.$ahn[count($ahn)-1];
		$hh = $ahn[count($ahn)-2];
		// if (($blog['tp_filter']==1)&&(check_post($m1['content'][$key],$blog['order_keyword'])==0)) continue;
		$qw=$db->query('SELECT post_id FROM blog_post WHERE post_link=\''.addslashes($item).'\' AND order_id='.$blog['order_id'].' LIMIT 1');
		if ($db->num_rows($qw)==0)
		{
			$count_pst++;
			echo '/'.$key.'/';
			$upd_orders[$blog['order_id']][mktime(0,0,0,date('n',$m1['time'][$key]),date('j',$m1['time'][$key]),date('Y',$m1['time'][$key]))]=1;
			// echo 'INSERT INTO blog_post (order_id,post_link,post_host,post_time,post_content) VALUES ('.$blog['order_id'].',\''.addslashes($item).'\',\''.addslashes($hn).'\',\''.$m1['time'][$key].'\',\''.addslashes($m1['content'][$key]).'\')'."\n\n\n\n";
			$db->query('INSERT INTO blog_post (order_id,post_link,post_host,post_time,post_content) VALUES ('.$blog['order_id'].',\''.addslashes($item).'\',\''.addslashes($hn).'\',\''.$m1['time'][$key].'\',\''.addslashes($m1['content'][$key]).'\')');
			// echo 'INSERT INTO blog_full_com (ful_com_post_id,ful_com_order_id,ful_com_post) VALUES (id,'.$blog['order_id'].',\''.addslashes($m1['content'][$key]).'\')';
			$db->query('INSERT INTO blog_full_com (ful_com_post_id,ful_com_order_id,ful_com_post) VALUES ('.$db->insert_id().','.$blog['order_id'].',\''.addslashes($m1['content'][$key]).'\')');
		}
	}
	$qw=$db->query('UPDATE youtube_id SET ind_last='.$mend.' WHERE ind_id='.$blog['ind_id']);
}

foreach ($upd_orders as $key => $item)
{
	foreach ($item as $k => $i)
	{
		//parseUrl('http://localhost/tools/cashjob.php?order_id='.$key.'&start='.$k.'&end='.$k);
	}
}

$headers  = "From: noreply2@wobot.ru\r\n"; 
$headers .= "Bcc: noreply2@wobot.ru\r\n";
$headers .= 'MIME-Version: 1.0' . "\r\n";
$headers .= 'Content-type: text/html; charset=utf-8'."\r\n";
// mail('zmei123@yandex.ru','Сборщик youtube',$count_pst,$headers);

?>
