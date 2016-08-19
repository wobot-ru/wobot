<?

$redis=new Redis() or die("Can'f load redis module.");
$redis->connect('127.0.0.1');

error_reporting(0);

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
for($t=$_GET['start'];$t<=$_GET['end'];$t=mktime(date('H',$t)+1,0,0,date("n",$t),date("j",$t),date("Y",$t)))
{
    $var=$redis->get('log_activity'.$t);
    $var=json_decode($var,true);
    foreach ($var as $key => $item)
    {
        $actions[$key]+=$item['count'];
        $var_all[$key]['count']+=$item['count'];
        foreach ($var[$key]['times'] as $key1 => $item1)
        {
            //print_r($item1);
            $var_all[$key]['times'][$key1]['count']+=$item1['count'];    
        }
        foreach ($var[$key]['hashs'] as $key1 => $item1)
        {
            $var_all[$key]['hashs'][$key1]['count']+=$item1['count'];
            $var_all[$key]['hashs'][$key1]['query']=$item1['query'];
        }
    }
}
foreach ($actions as $key => $item)
{
	echo '<tr><td><b>'.$key.'</b></td><td width="50" align="center">'.intval($item).'</td></tr>';
}
echo '</table>';
echo '<hr><h3>For period:</h3><form><select name="hero">
    <option '.($_GET['hero']=='all'?'selected':'').' value="all">all</option>';
foreach ($actions as $key => $item) 
{
	echo '<option '.($_GET['hero']==$key?'selected':'').' value="'.$key.'">'.$key.'</option>';
}
echo '</select>From: <input type="text" id="datepicker" name="start" value="'.($_GET['start']==''?date('m/d/Y'):date('m/d/Y',$_GET['start'])).'" /> To: <input type="text" name="end" id="datepicker1" value="'.($_GET['end']==''?date('m/d/Y'):date('m/d/Y',$_GET['end'])).'" /><input type="submit" value="view"></form>';
//print_r($var);
//echo '<br>';
//print_r($var_all);
if ($_GET['hero']!='all')
{
    $var1=$var_all[$_GET['hero']];
	unset($var_all);
	$var_all[$_GET['hero']]=$var1;
}
//echo '<br>';
//print_r($var_all);
echo '<table border="1">';
foreach ($var_all as $key => $item)
{
    //echo $key;
    //print_r($actions[$key]);
    //print_r($item['hashs']);
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
//arsort($queries);
function startsort($a, $b)
{
    if ($a['count'] == $b['count']) {
        return 0;
    }
    return ($a['count'] < $b['count']) ? 1 : -1;
}
usort($queries, 'startsort');
//print_r($queries);
echo '<textarea cols="160" rows="20">';
foreach ($queries as $key => $value) 
{
    //print_r($value);
    //echo $key;
    //if ($key=='[]') continue;
    echo 'count: '.$value['count']."\n".'type: '.$value['type']."\n".'query:'.$value['query']."\n\n";    
}
echo '</textarea>';
//print_r($queries);
echo '
</body>
</html>';
//echo '<form><input type="text" name="order_id"></form>';

?>