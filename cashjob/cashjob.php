<?

if ($_SERVER['argv'][1]==2877) die();

require_once('com/config.php');
require_once('com/db.php');
require_once('com/func_spec.php');
require_once('com/geo.php');
require_once('bot/kernel.php');
require_once('/var/www/api/0/rfunc.php');
require_once('/var/www/new/com/func.php');

date_default_timezone_set ( 'Europe/Moscow' );
error_reporting(E_ERROR | E_PARSE);
ignore_user_abort(true);
set_time_limit(0);
ini_set('max_execution_time',0);
ini_set('default_charset','utf-8');
ob_implicit_flush();

$db = new database();
$db->connect();

$qsuborders=$db->query('SELECT subtheme_id FROM blog_subthemes WHERE order_id='.$_SERVER['argv'][1]);
while ($suborder=$db->fetch($qsuborders))
{
	$ids[]=$suborder['subtheme_id'];
}

foreach ($ids as $item)
{
	$fp = fopen('/var/www/bot/cashjob-spec.log', 'a');
	fwrite($fp, 'start: '.date('r')."\n");
	fclose($fp);

	$descriptorspec=array(
		0 => array("file","/dev/null","a"),
		1 => array("file","/dev/null","a"),
		2 => array("file","/dev/null","a")
		);

	$cwd='/var/www/bot/';
	$end=array();
	
	//echo 'php /var/www/cashjob/cashjob_spec.php s'.intval($item).' '.(($_SERVER['argv'][2]!='')&&($_SERVER['argv'][3]!='')?$_SERVER['argv'][2].' '.$_SERVER['argv'][3]:'').' &'."\n";
	$process=proc_open('php /var/www/cashjob/cashjob_spec.php s'.intval($item).' '.(($_SERVER['argv'][2]!='')&&($_SERVER['argv'][3]!='')?$_SERVER['argv'][2].' '.$_SERVER['argv'][3]:'').' &',$descriptorspec,$pipes,$cwd,$end);/* or {
		echo json_encode(array('status'=>'fail'), true);
		die();
	};*/
	
	if (is_resource($process))
	{
		//echo 'return: '.$return_value=proc_close($process);
		if (intval($return_value=proc_close($process))==0) echo json_encode(array('status'=>'ok'), true);
	}
}
$fp = fopen('/var/www/bot/cashjob-spec.log', 'a');
fwrite($fp, 'start: '.date('r')."\n");
fclose($fp);

$descriptorspec=array(
	0 => array("file","/dev/null","a"),
	1 => array("file","/dev/null","a"),
	2 => array("file","/dev/null","a")
	);

$cwd='/var/www/bot/';
$end=array();

$process=proc_open('php /var/www/cashjob/cashjob_spec.php '.intval($_SERVER['argv'][1]).' '.(($_SERVER['argv'][2]!='')&&($_SERVER['argv'][3]!='')?$_SERVER['argv'][2].' '.$_SERVER['argv'][3]:'').' &',$descriptorspec,$pipes,$cwd,$end);/* or {
	echo json_encode(array('status'=>'fail'), true);
	die();
};*/

if (is_resource($process))
{
	//echo 'return: '.$return_value=proc_close($process);
	if (intval($return_value=proc_close($process))==0) echo json_encode(array('status'=>'ok'), true);
}

?>