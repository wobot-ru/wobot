<?

require_once('com/rebuilder.php');

error_reporting(0);

$db = new database();
$db->connect();

$qus=$db->query('SELECT * FROM group_orders WHERE id='.intval($_GET['gr_id']));
//echo 'SELECT * FROM users as a LEFT JOIN group_orders as b ON a.user_id=b.user_id WHERE a.user_id='.intval($_GET['user_id']).' AND b.id='.intval($_GET['gr_id']);
$user=$db->fetch($qus);


$descriptorspec=array(
	0 => array("pipe","r"),
	1 => array("pipe","w"),
	2 => array("file", "/tmp/error-output.txt", "a")
	);

$cwd='/var/www/new/modules';
$end=array('');
//$pipes=json_encode($outmas);
$process=proc_open('perl /var/www/project/excel/excel.pl',$descriptorspec,$pipes,$cwd,$end);
//echo "\n".$row['post_link']."\n";
if (is_resource($process))
{
	fwrite($pipes[0], json_encode(rebuild_json($qus['group_json'],$qus['group_start'],$qus['group_end'])));
	fclose($pipes[0]);
	//echo $return_value;
	//print_r($pipes);
	$fulltext=stream_get_contents($pipes[1]);
	$return_value=proc_close($process);
	header("Content-type: application/vnd.ms-excel");
	//echo preg_replace('/[^а-яА-Яa-zA-Z\-\_0-9]/isu','_',$order['order_name']);
	//echo "Content-Disposition: attachment; filename=wobot_".date('dmy',strtotime($_POST['stime'])).'_'.date('dmy',strtotime($_POST['etime'])).'_'.$file_name.".xls";
	//die();
	header("Content-Disposition: attachment; filename=wobot.xls");
	echo $fulltext;
}

?>