<?

$av['fs']=1; //realtime
$av['fulljob']=1; 
$av['et']=1; 
$av['uj']=1; //профили
$av['transf']=1; //перебросчик в базу
$av['sj']=1; //тональность
$av['tj']=1; //теги
$av['dj']=1; //дубилкаты
$av['ef']=1; // вовлеченность и фултекст
$av['auth']=1; //правильные ники в контакте
$av['reposts']=1; //репосты вк
$av['utasker']=1; // заносчик заданий для uj
$av['updater']=1; //очередь запросов в бд
$av['typhoon']=1; //первоначальный сбор
$av['all']=1; //запустить все
$av['uservk']=1; //сбор uj по вк
$av['cash']=1; //запуск кеша
$av['checkorders']=1; //автотестирование тем на предмет сбора
$av['freash']=1; //учашенный реал тайм сбор (wobot parser)
$av['egtw']=1; //вовлеченность в твитере (ретвиты)
$av['alert']=1;
$av['cu']=1;
$av['reaction']=1;

$av_action['start']=1;
$av_action['stop']=1;

if (!isset($av[$_SERVER['argv'][1]])) die('Sorry can\'t find this daemon...');
if (!isset($av_action[$_SERVER['argv'][2]])) die('Chose action please!');
if ($_SERVER['argv'][1]=='all') $_SERVER['argv'][4]=$_SERVER['argv'][3];

$lauch_array['fs']='cd /var/www/daemon/fsearch3/ && php tp_job3.php';
$lauch_array['fulljob']='cd /var/www/daemon/fulljob/ && php multi_ft.php';
$lauch_array['et']='cd /var/www/daemon/Engagement/ && php eng_job.php';
$lauch_array['uj']='cd /var/www/daemon/userjob/ && php multi_uj.php';
$lauch_array['transf']='cd /var/www/daemon/ && php re_transf.php';
$lauch_array['sj']='cd /var/www/daemon/sentimentjob/ && php wobot.prodSentiment.php';
$lauch_array['tj']='cd /var/www/daemon/tagjob/ && php tagjob.php';
$lauch_array['dj']='cd /var/www/daemon/duplicatesjob/ && php dj.php';
$lauch_array['ef']='cd /var/www/daemon/fulljob/ && php efjob.php';// engage+fulljob
$lauch_array['auth']='cd /var/www/daemon/vk_real_author/ && php auther.php';// engage+fulljob
$lauch_array['reposts']='cd /var/www/daemon/vk_reposts/ && php vk_reposts.php';// engage+fulljob
$lauch_array['utasker']='cd /var/www/daemon/userjob/ && php tasker.php';// engage+fulljob
$lauch_array['updater']='cd /var/www/daemon/userjob/ && php updater.php';// engage+fulljob
$lauch_array['typhoon']='cd /var/www/daemon/fsearch3/ && php tp_typhoon.php';// engage+fulljob
$lauch_array['uservk']='cd /var/www/daemon/userjob/ && php uj_vk.php';
$lauch_array['cash']='cd /var/www/daemon/ && php cash_launcher.php';
$lauch_array['checkorders']='cd /var/www/daemon/ && php check_orders.php';
$lauch_array['freash']='cd /var/www/daemon/fsearch3/ && php freash_harv.php';
$lauch_array['egtw']='cd /var/www/daemon/Engagement/adv_engage/ && php adv_engage.php';
$lauch_array['alert']='cd /var/www/daemon/ && php alertjob.php';
$lauch_array['cu']='cd /var/www/daemon/ && php cash_updater.php';
$lauch_array['reaction']='cd /var/www/daemon/reaction/ && php reaction.php';

$content_count_confing=file_get_contents('/var/www/com/launcher.config.inc');
$count_config=json_decode($content_count_confing,true);
// $count_config['fs']=10;
// $count_config['ef']=30;
// $count_config['uj']=20;
// $count_config['transf']=5;
// $count_config['tj']=3;
// $count_config['dj']=3;
// $count_config['auth']=3;
// $count_config['reposts']=3;
// $count_config['utasker']=1;
// $count_config['updater']=1;
// $count_config['typhoon']=1;
// $count_config['uservk']=4;
// $count_config['cash']=1;

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
	global $lauch_array,$descriptorspec,$cwd,$end,$_SERVER;
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
    if ($name=='dj')
	{
		kill_job('dj');
		$count=$params[1];
	}
    if ($name=='ef')
	{
		kill_job('ef');
		$count=$params[1];
	}
    if ($name=='auth')
	{
		kill_job('auth');
		$count=$params[1];
	}
    if ($name=='reposts')
	{
		kill_job('reposts');
		$count=$params[1];
	}
    if ($name=='utasker')
	{
		kill_job('utasker');
		$count=$params[1];
	}
    if ($name=='updater')
	{
		kill_job('updater');
		$count=$params[1];
	}
    if ($name=='typhoon')
	{
		kill_job('typhoon');
		$count=$params[1];
	}
    if ($name=='uservk')
	{
		kill_job('uservk');
		$count=$params[1];
	}
    if ($name=='cash')
	{
		kill_job('cash');
		$count=$params[1];
	}
    if ($name=='checkorders')
	{
		kill_job('checkorders');
		$count=$params[1];
	}
    if ($name=='freash')
	{
		kill_job('freash');
		$count=$params[1];
	}
    if ($name=='egtw')
	{
		kill_job('egtw');
		$count=$params[1];
	}
	if ($name=='alert')
	{
		kill_job('alert');
		$count=$params[1];
	}
	if ($name=='cu')
	{
		kill_job('cu');
		$count=$params[1];
	}
	if ($name=='reaction')
	{
		kill_job('reaction');
		$count=$params[1];
	}
		//$count=1;
	//$count=1;
	for ($i=0;$i<$count;$i++)
	{
		//echo $lauch_array[$name].' '.$i.' '.$count.'&'."\n";
		if (@$_SERVER['argv'][4]=='logs') $process=proc_open($lauch_array[$name].' '.$i.' '.$count.' > /var/www/logs/'.$name.$i.'.log &',$descriptorspec,$pipes,$cwd,$end);
		else $process=proc_open($lauch_array[$name].' '.$i.' '.$count.' > /dev/null &',$descriptorspec,$pipes,$cwd,$end);
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
	global $descriptorspec,$cwd,$end,$lauch_array;
	// $lauch_array['freash']='cd /var/www/daemon/fsearch3/ && php freash_harv.php';
	$mjob=scandir('/var/www/pids/');
	foreach ($mjob as $key => $item)
	{
		if (preg_match('/'.$name.'.+/isu',$item))
		{
			$filename=$item;
			$handle = fopen('/var/www/pids/'.$filename, "r");
			$contents = fread($handle, filesize('/var/www/pids/'.$filename));
			fclose($handle);
			$process=proc_open('kill '.$contents,$descriptorspec,$pipes,$cwd,$end);
			unlink('/var/www/pids/'.$filename);
			echo 'kill '.$contents.' '.$filename."\n";
		}
	}
	$regex='/\&\&\sphp\s(?<name>.*?)\.php/isu';
	preg_match_all($regex, $lauch_array[$name], $out);
	// print_r($out);
	$shell=shell_exec('ps ax | grep '.$out['name'][0]);
	// echo $shell;
	$mshell=explode("\n", $shell);
	foreach ($mshell as $item_shell)
	{
		if (preg_match('/'.$out['name'][0].'\.php/isu',$item_shell))
		{
			$regex='/^(?<pid>\d+)\s/isu';
			preg_match_all($regex, $item_shell, $outpid);
			// print_r($outpid);
			if (@trim($outpid['pid'][0])!='')
			{
				shell_exec('kill '.$outpid['pid'][0]);
				echo 'kill '.$outpid['pid'][0]."\n";
			}
		}
	}
}

if ($_SERVER['argv'][2]=='start')
{
	if ($_SERVER['argv'][1]!='all') launch_job($_SERVER['argv'][1],$params);
	elseif ($_SERVER['argv'][1]=='all')
	{
		foreach ($count_config as $daemon => $count)
		{
			$params[0]='start';
			$params[1]=$count;
			launch_job($daemon,$params);
		}
	}
}
else
{
	if ($_SERVER['argv'][1]!='all') kill_job($_SERVER['argv'][1],$params);	
	elseif ($_SERVER['argv'][1]=='all')
	{
		foreach ($count_config as $daemon => $count)
		{
			$params[0]='stop';
			$params[1]=$count;
			kill_job($daemon,$params);
		}
	}
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