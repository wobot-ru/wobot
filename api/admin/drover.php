<?

require_once('/var/www/com/config.php');
require_once('/var/www/com/db.php');
require_once('/var/www/com/auth.php');

$db = new database();
$db->connect();

$descriptorspec=array(
	0 => array("file","/dev/null","a"),
	1 => array("file","/dev/null","a"),
	2 => array("file","/dev/null","a")
	);

$cwd='/var/www/bot/';
$end=array();

$process=proc_open('php /var/www/admin/drover.php '.intval($_POST['user_id']).' '.$_POST['server'].' &',$descriptorspec,$pipes,$cwd,$end);

if (is_resource($process))
{
	//echo 'return: '.$return_value=proc_close($process);
	if (intval($return_value=proc_close($process))==0) echo json_encode(array('status'=>'ok'), true);
}
?>