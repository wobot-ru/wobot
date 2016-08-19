<?

echo '<head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<script type="text/JavaScript">
	<!--
	function timedRefresh(timeoutPeriod) {
		setTimeout("location.reload(true);",timeoutPeriod);
	}
	</script>
</head>
<body onload="JavaScript:timedRefresh(5000);">';

$command='tail -n 5 /var/www/daemon/logs/elastic_query.log';
$mcom=explode("\n",shell_exec($command));
$mcom=array_reverse($mcom);
// print_r($mcom);
echo '<table border="1" style="font-size: 12px;"><tr><td>query:</td><td>offset:</td><td>from:</td><td>to:</td></tr>';
foreach ($mcom as $item)
{
	$regex='/\"query\"\:\"(?<query>.*?)\"\}\}.*?\"from\"\:\"(?<ft>[\d\-\:]+)\T[\d\:]+\"\,\"to\"\:\"(?<et>[\d\-\:]+)\T[\d\:]+\".*?\"from\"\:(?<from>\d+)/isu';
	preg_match_all($regex, $item, $out);
	if ($out['query'][0]=='') continue;
	if ($out['from'][0]=='') continue;
	echo '<tr><td>'.mb_substr($out['query'][0],0,30,'UTF-8').'</td><td>'.$out['from'][0].'</td><td>'.date('d.m.Y',strtotime($out['ft'][0])).'</td><td>'.date('d.m.Y',strtotime($out['et'][0])).'</td></tr>';
	// print_r($out);
}
echo '</table></body>';
// echo preg_replace('/\n/isu','<br>',shell_exec());

?>