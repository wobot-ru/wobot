<?

$redis=new Redis() or die("Can'f load redis module.");
$redis->connect('127.0.0.1');

$var=$redis->get('log_activity');
$var=json_decode($var,true);
//print_r($var);
echo '<html><head>
    <meta charset="utf-8" />
    <title>jQuery UI Datepicker - Default functionality</title>
    <link rel="stylesheet" href="http://code.jquery.com/ui/1.9.1/themes/base/jquery-ui.css" />
    <script src="http://code.jquery.com/jquery-1.8.2.js"></script>
    <script src="http://code.jquery.com/ui/1.9.1/jquery-ui.js"></script>
    <link rel="stylesheet" href="/resources/demos/style.css" />
    <script>
    $(function() {
        $( "#datepicker" ).datepicker();
    });
    $(function() {
        $( "#datepicker1" ).datepicker();
    });
    </script>
</head>
<body>';
$_GET['start']=strtotime(urldecode($_GET['start']));
$_GET['end']=strtotime(urldecode($_GET['end']));
echo '<h3>OVERALL:</h3>
<table border="1">
';
foreach ($var as $key => $item)
{
	echo '<tr><td><b>'.$key.'</b></td><td width="50" align="center">'.intval($item['count']).'</td></tr>';
}
echo '</table>';
echo '<hr><h3>For period:</h3><form><select name="hero">
    <option '.($_GET['hero']=='all'?'selected':'').' value="all">all</option>';
foreach ($var as $key => $item) 
{
	echo '<option '.($_GET['hero']==$key?'selected':'').' value="'.$key.'">'.$key.'</option>';
}
echo '</select>From: <input type="text" id="datepicker" name="start" value="'.($_GET['start']==''?date('m/d/Y'):date('m/d/Y',$_GET['start'])).'" /> To: <input type="text" name="end" id="datepicker1" value="'.($_GET['end']==''?date('m/d/Y'):date('m/d/Y',$_GET['end'])).'" /><input type="submit" value="view"></form>';
if ($_GET['hero']!='all')
{
	$var1=$var[$_GET['hero']];
	unset($var);
	$var[$_GET['hero']]=$var1;
}
//print_r($var);
echo '<table border="1">';
foreach ($var as $key => $item)
{
    //echo $key;
	$count=0;
	for($t=$_GET['start'];$t<=$_GET['end'];$t=mktime(0,0,0,date("n",$t),date("j",$t)+1,date("Y",$t)))
	{
		//echo $t.' '.$item['times'][$t]['count'];
		$count+=$item['times'][$t]['count'];
	}
	echo '<tr><td><b>'.$key.'</b></td><td width="50" align="center">'.intval($count).'</td></tr>';
    foreach ($item['hashs'] as $k => $i)
    {
        $queries[md5($i['query'])]['count']+=$i['count'];
        $queries[md5($i['query'])]['type']=$key;
        $queries[md5($i['query'])]['query']=$i['query'];
    }
}
echo '</table><br>';
//print_r($queries);
//arsort($queries);
function startsort($a, $b)
{
    if ($a['count'] == $b['count']) {
        return 0;
    }
    return ($a['count'] < $b['count']) ? 1 : -1;
}
usort($queries, 'startsort');
echo '<textarea cols="160" rows="20">';
foreach ($queries as $key => $value) 
{
    //print_r($value);
    if ($key=='[]') continue;
    echo 'count: '.$value['count']."\n".'type: '.$value['type']."\n".'query:'.$value['query']."\n\n";    
}
echo '</textarea>';
//print_r($queries);
echo '
</body>
</html>';
//echo '<form><input type="text" name="order_id"></form>';

?>