<?

$av['fs']=1;
$av['fulljob']=1;
$av['et']=1;
$av['uj']=1;
$av['transf']=1;
$av['sj']=1;
$av['tj']=1;

$av_action['start']=1;
$av_action['stop']=1;

if (!isset($av[$_SERVER['argv'][1]])) die('Sorry can\'t find this daemon...');
if (!isset($av_action[$_SERVER['argv'][2]])) die('Chose action please!');

$lauch_array['fs']='cd /var/www/daemon/fsearch3/ && php tp_job3.php';
$lauch_array['fulljob']='cd /var/www/daemon/fulljob/ && php multi_ft.php';
$lauch_array['et']='cd /var/www/daemon/Engagement/ && php eng_job.php';
$lauch_array['uj']='cd /var/www/daemon/userjob/ && php multi_uj.php';
$lauch_array['transf']='cd /var/www/daemon/ && php re_transf.php';
$lauch_array['sj']='cd /var/www/daemon/sentimentjob/ && php wobot.prodSentiment.php';
$lauch_array['tj']='cd /var/www/daemon/tagjob/ && php tagjob.php';

$descriptorspec=array(
	0 => array("file","/dev/null","a"),
	1 => array("file","/dev/null","a"),
	2 => array("file","/dev/null","a")
	);

$cwd='/var/www/bot/';
$end=array();

foreach ($_SERVER['argv'] as $key => $item)
{
	if ($key==0) continue;
	if ($key==1) continue;
	$params[]=$item;
}

function launch_job($name,$params)
{
	global $lauch_array,$descriptorspec,$cwd,$end;
	if ($name=='fs')
	{
		kill_job('fs');
		$count=$params[1];
	}
	if ($name=='fulljob')
	{
		kill_job('fulljob');
		$count=$params[1];
	}
	if ($name=='et')
	{
		kill_job('et');
		$count=$params[1];
	}
	if ($name=='uj')
	{
		kill_job('uj');
		$count=$params[1];
	}
	if ($name=='transf')
	{
		kill_job('transf');
		$count=$params[1];
	}
    if ($name=='sj')
	{
		kill_job('sj');
		$count=$params[1];
	}
    if ($name=='tj')
	{
		kill_job('tj');
		$count=$params[1];
	}
		//$count=1;
	//$count=1;
	for ($i=0;$i<$count;$i++)
	{
		//echo $lauch_array[$name].' '.$i.' '.$count.'&'."\n";
		$process=proc_open($lauch_array[$name].' '.$i.' '.$count.' > /var/www/daemon/logs/'.$name.$i.'.log &',$descriptorspec,$pipes,$cwd,$end);
		if (is_resource($process))
		{
			if (intval($return_value=proc_close($process))==0)
			{
				echo 'launch '.$name.' '.$i."\n";
			}
			else
			{
				echo '!!!fail to launch '.$name.' '.$i."\n";
			}
		}
	}
}

function kill_job($name)
{
	global $descriptorspec,$cwd,$end;
	$mjob=scandir('/var/www/daemon/pids/');
	foreach ($mjob as $key => $item)
	{
		if (preg_match('/'.$name.'.+/isu',$item))
		{
			$filename=$item;
			$handle = fopen('pids/'.$filename, "r");
			$contents = fread($handle, filesize('pids/'.$filename));
			fclose($handle);
			$process=proc_open('kill '.$contents,$descriptorspec,$pipes,$cwd,$end);
			unlink('pids/'.$filename);
			echo 'kill '.$contents.' '.$filename."\n";
		}
	}
}

if ($_SERVER['argv'][2]=='start')
{
	launch_job($_SERVER['argv'][1],$params);
}
else
{
	kill_job($_SERVER['argv'][1],$params);	
}

//kill_job('fs');
/*$fp = fopen('/var/www/bot/cashjob-spec.log', 'a');
fwrite($fp, 'start: '.date('r')."\n");
fclose($fp);

$descriptorspec=array(
	0 => array("file","/dev/null","a"),
	1 => array("file","/dev/null","a"),
	2 => array("file","/dev/null","a")
	);

$cwd='/var/www/bot/';
$end=array();

$process=proc_open('php /var/www/cashjob/cashjob.php '.intval($_GET['order_id']).' '.(($_GET['start']!='')&&($_GET['end']!='')?$_GET['start'].' '.$_GET['end']:'').' &',$descriptorspec,$pipes,$cwd,$end);/* or {
	echo json_encode(array('status'=>'fail'), true);
	die();
};*/

//if (is_resource($process))
{
	//echo 'return: '.$return_value=proc_close($process);
//	if (intval($return_value=proc_close($process))==0) echo json_encode(array('status'=>'ok'), true);
}

?>