<?

function remove_hill($order)
{
	global $db;
	$qpost=$db->query('SELECT post_time FROM blog_post WHERE order_id='.$order['order_id']);
	while ($post=$db->fetch($qpost))
	{
		$mtime[mktime(0,0,0,date('n',$post['post_time']),date('j',$post['post_time']),date('Y',$post['post_time']))]++;
	}
	krsort($mtime);
	echo 'count_prev='.$count_prev."\n";
	foreach ($mtime as $time => $count)
	{
		echo $time.' '.$count_prev."\n";
		$count_prev+=$count;
		if ($count_prev>$order['tariff_posts']) break;
	}
	// print_r($mtime);
	echo "\n".$time.' '.date('r',$order['order_start']).' '.date('r',$time).' '.date('r',$order['order_end'])."\n";
	return $time;
}

?>