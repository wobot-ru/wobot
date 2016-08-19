<?

$redis=new Redis() or die("Can'f load redis module.");
$redis->connect('127.0.0.1');

if ($_POST['id']==1)
{
	$cache=$redis->get('cacher');
	$mcache=json_decode($cache,true);
	for ($t=mktime(date('H')-1,0,0,date('n'),date('j'),date('Y'));$t<=mktime(date('H'),date('i'),0,date('n'),date('j'),date('Y'));$t+=60)
	{
		if ($mcache[$t]>$max) $max=$mcache[$t];
	}
	$outmas['max']=$max;
	$outmas['now']=$mcache[mktime(date('H'),date('i')-1,0,date('n'),date('j'),date('Y'))];
	echo json_encode($outmas);
	die();
}

if ($_POST['id']==2)
{
	$mhorder=explode(',', $_POST['order_id']);
	foreach ($mhorder as $item)
	{
		$cont=shell_exec('tail -n 10 /var/www/daemon/logs/fs'.($item % 100).'.log');
		$mc=explode("\n", $cont);
		unset($mc1);
		foreach ($mc as $key => $it)
		{
			$mc1[]=mb_substr($it,0,50,'UTF-8');
		}
		$mhcont[$item]=implode('<br>',$mc1);//preg_replace('/\n/isu','<br>',shell_exec('tail -n 5 /var/www/daemon/logs/fs'.($item % 100).'.log'));
	}
	echo json_encode($mhcont);

}

?>