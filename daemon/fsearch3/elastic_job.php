<?
// die();
// Добавить поле в таблицу blog_orders ... third_party
require_once('/var/www/daemon/com/config.php');
// require_once('/var/www/com/func.php');
require_once('/var/www/daemon/com/db.php');
require_once('/var/www/daemon/bot/kernel.php');
require_once('/var/www/daemon/com/users.php');
require_once('/var/www/daemon/com/qlib.php');
require_once('ch.php');
/*require_once('/var/www/userjob/get_vkontakte.php');
require_once('/var/www/userjob/get_twitter.php');
require_once('/var/www/userjob/get_livejournal.php');*/

proc_nice(10);

$memcache = memcache_connect('localhost', 11211);
$redis=new Redis() or die("Can'f load redis module.");
$redis->connect('127.0.0.1');

require_once('elastic.php');
/*
$yet=$redis->get('yet_elastic_'.$_SERVER['argv'][1]);
echo 'yet_elastic_'.$_SERVER['argv'][1];
if ($yet!='') die();

$redis->set('yet_elastic_'.$_SERVER['argv'][1], '1');

$max_count_elastic=3;
//$memcache->set('count_elastic',0);

do
{
	sleep(15);
	$count_elastic=intval($redis->get('count_elastic'));
	echo 'repeat...'.$count_elastic."\n";
}
while ($count_elastic>=$max_count_elastic);
$redis->incr('count_elastic');
*/
date_default_timezone_set('Europe/Moscow');
error_reporting(E_ERROR);
ignore_user_abort(true);
set_time_limit(0);
ini_set('max_execution_time',0);
ini_set('default_charset','utf-8');
ob_implicit_flush();
$db = new database();
$db->connect();
$order_id=$_SERVER['argv'][1];
$elastic_start=$_SERVER['argv'][2];
$elastic_end=$_SERVER['argv'][3];
$elastic_start-=86400*2;

echo $order_id.' '.$node_id;
//$ressec=$db->query('UPDATE');
$ressec=$db->query('SELECT * FROM blog_orders WHERE order_id='.$order_id.' ORDER BY order_id DESC');
//$ressec=$db->query('SELECT * FROM blog_orders WHERE order_id=723');

//echo 'SELECT * FROM blog_orders WHERE (third_sources<='.mktime(date("H"),0,0,date("n"),date("j"),date("Y")).' or (third_sources=0 and order_start<='.mktime(date("H"),0,0,date("n"),date("j"),date("Y")).')) and (third_sources<=order_end or order_end=0) AND (third_sources!=0) ORDER BY order_id DESC';
// $ressec=$db->query('SELECT * FROM blog_orders WHERE user_id!=145 AND order_last_rss<order_end');
while($blog=$db->fetch($ressec))
{
	if ($blog['order_id']==34730) $blog['order_keyword']='(голодные /+1 игры)|"#голодные игры"|"THE HUNGER GAMES"|"#HUNGERGAMES"|"Сойка-пересмешница"';
	if ($blog['order_id']==34731) $blog['order_keyword']='"Insurgent"|"#Insurgent"|"инсургент"|"#инсургент"|"дивергент"|"#дивергент"|"Divergent"|"#Divergent"';
	if ($blog['order_id']==34732) $blog['order_keyword']='"дивергент"|"#дивергент"|"Divergent"|"#Divergent"';
	if ($blog['order_id']==34733) $blog['order_keyword']='(голодные /+1 игры)|"#голодные игры"|"THE HUNGER GAMES"|"#HUNGERGAMES"|"Сойка-пересмешница"';
	$blog['order_keyword']=construct_object_query($blog['order_keyword']);
	$order_settings=json_decode($blog['order_settings'],true);
	if (!preg_match('/\~ТЕСТ\~.*/isu', $blog['order_name']))
	{
		// $redis->decr('count_elastic');
		// $redis->delete('yet_elastic_'.$_SERVER['argv'][1]); 
		//die();		
	}
	print_r($blog);

	if ($elastic_start<$blog['order_start']) $elastic_start=$blog['order_start'];
	if ($elastic_end>$blog['order_end']) $elastic_end=$blog['order_end'];

	switch ($blog['order_lang']) {
	    case 0:
	        $text_lang='';
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
	$mstart=$elastic_start;
	$mend=$elastic_end;
	echo $mstart.' '.$mend.' '.$blog['order_id']."\n";
	if ($mend==0) $mend=time();
	$m1=get_elastic((($blog['order_keyword']=='')?$blog['order_name']:$blog['order_keyword']),$mstart,$mend,$text_lang);
	// if ($blog['order_last']==0) $m2=get_elastic((($blog['order_keyword']=='')?$blog['order_name']:$blog['order_keyword']),$mstart,$mend,$text_lang,1);
	foreach ($m2['link'] as $key => $item)
	{
		$m1['link'][]=$item;
		$m1['content'][]=$m2['content'][$key];
		$m1['time'][]=$m2['time'][$key];
	}
	// print_r($m1);
	//die();
	if ($blog['order_end']>=time())
	{
		$cstart=mktime(0,0,0,date('n'),date('j'),date('Y'));
		$cend=mktime(0,0,0,date('n'),date('j'),date('Y'));
	}
	else
	{
		$cstart=$blog['order_end'];
		$cend=$blog['order_end'];
	}
	foreach ($m1['link'] as $key => $item)
	{
		if (($blog['order_analytics']==1) && (($m1['time'][$key]<$mstart) || ($m1['time'][$key]>=$mend)))
		{
			continue;
		}
		if (($blog['order_analytics']==0) && (($m1['time'][$key]<$blog['order_start']) || ($m1['time'][$key]>=(($blog['order_end'])==0?time():$blog['order_end']))))
		{
			continue;
		}
		$check=check_filters($item,$order_settings);
		if (($check['value']==0) && ($check['value']!='')) continue;
		// $qw=$db->query('SELECT post_id FROM blog_post WHERE  order_id='.$blog['order_id'].' AND post_time='.$m1['time'][$key].' AND post_content=\''.addslashes($m1['content'][$key]).'\' LIMIT 1');
		$qw=$db->query('SELECT post_id FROM blog_post WHERE  order_id='.$blog['order_id'].' AND post_link=\''.addslashes($item).'\' LIMIT 1');
		echo 'SELECT post_id FROM blog_post WHERE  order_id='.$blog['order_id'].' AND post_link=\''.addslashes($item).'\' LIMIT 1'."\n";
		// echo 'SELECT * FROM blog_post WHERE  order_id='.$blog['order_id'].' AND post_time='.$m1['time'][$key].' AND post_content=\''.addslashes($m1['content'][$key]).'\''."\n";
		if ((mysql_num_rows($qw)==0) && (!in_array($item,$rep)) && ((check_post($m1['content'][$key],$blog['order_keyword'])==1)||(check_post($m1['fulltext'][$key],$blog['order_keyword'])==1)) && (check_local($m1['content'][$key],$text_lang)==1))
		{
			echo $key.' ';
			//echo $item['content']."\n";
			$rep[]=$item;
			$hn='';
			$hn=parse_url($item);
		    $hn=$hn['host'];
		    $ahn=explode('.',$hn);
		    $hn = $ahn[count($ahn)-2].'.'.$ahn[count($ahn)-1];
			$hh = $ahn[count($ahn)-2];
			$bb1['blog_id']=0;

			$m2['content'][$key]=$m1['content'][$key];
			echo 'INSERT INTO blog_post_prev (order_id,post_link,post_host,post_time,post_content,blog_id,post_ful_com,post_engage) VALUES ('.$blog['order_id'].',\''.addslashes($item).'\',\''.addslashes($hn).'\',\''.$m1['time'][$key].'\',\''.addslashes($m1['content'][$key]).'\','.intval($bb1['blog_id']).',\''.addslashes($m1['fulltext'][$key]).'\',0)'."\n\n\n";
			// $db->query('INSERT INTO blog_post_prev (order_id,post_link,post_host,post_time,post_content,blog_id,post_ful_com,post_engage) VALUES ('.$blog['order_id'].',\''.addslashes($item).'\',\''.addslashes($hn).'\',\''.$m1['time'][$key].'\',\''.addslashes($m1['content'][$key]).'\','.intval($bb1['blog_id']).',\''.addslashes($m1['fulltext'][$key]).'\',0)');
			if ($m1['time'][$key]<$cstart) $cstart=mktime(0,0,0,date('n',$m1['time'][$key]),date('j',$m1['time'][$key]),date('Y',$m1['time'][$key]));
			$db->query('INSERT INTO blog_post (order_id,post_link,post_host,post_time,post_content,blog_id,post_engage) VALUES ('.$blog['order_id'].',\''.addslashes($item).'\',\''.addslashes($hn).'\',\''.$m1['time'][$key].'\',\''.addslashes($m1['content'][$key]).'\','.intval($bb1['blog_id']).',0)');
			$db->query('INSERT INTO blog_full_com (ful_com_post_id,ful_com_order_id,ful_com_post) VALUES ('.$db->insert_id().','.$blog['order_id'].',\''.addslashes($m1['fulltext'][$key]).'\')');

		}
	}
	unset($rep);
}
/*
//$cj=file_get_contents('http://localhost/tools/cashjob.php?order_id='.intval($order_id).'&start='.$cstart.'&end='.$cend);
$redis->decr('count_elastic');
$redis->delete('yet_elastic_'.$_SERVER['argv'][1]); 
*/
?>
