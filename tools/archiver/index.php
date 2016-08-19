<?

require_once('/var/www/new/com/db.php');
require_once('/var/www/new/com/config.php');

$db=new database();
$db->connect();
if ($_GET['dump']!='')
{
	$descriptorspec=array(
		0 => array("file","/dev/null","a"),
		1 => array("file","/dev/null","a"),
		2 => array("file","/dev/null","a")
	);

	$cwd='/var/www/bot/';
	$end=array();
	$process=proc_open('php /var/www/tools/archiver/dumper.php '.$_GET['dump'].' &',$descriptorspec,$pipes,$cwd,$end);/* or {
		echo json_encode(array('status'=>'fail'), true);
		die();
	};*/
	
	if (is_resource($process))
	{
		//echo 'return: '.$return_value=proc_close($process);
		if (intval($return_value=proc_close($process))==0) $c=1;
	}
	sleep(1);
}
elseif ($_GET['recover']!='')
{
	$descriptorspec=array(
		0 => array("file","/dev/null","a"),
		1 => array("file","/dev/null","a"),
		2 => array("file","/dev/null","a")
	);

	$cwd='/var/www/bot/';
	$end=array();
	
	$process=proc_open('php /var/www/tools/archiver/recover.php '.$_GET['recover'].' &',$descriptorspec,$pipes,$cwd,$end);/* or {
		echo json_encode(array('status'=>'fail'), true);
		die();
	};*/
	
	if (is_resource($process))
	{
		//echo 'return: '.$return_value=proc_close($process);
		if (intval($return_value=proc_close($process))==0) $c=1;
	}
	sleep(1);
}

echo '
<head>
<script src="http://code.jquery.com/jquery-1.10.1.min.js"></script>
<script src="http://code.jquery.com/jquery-migrate-1.2.1.min.js"></script>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<script>
	$(document).ready(function() {
		setInterval(function() {
			$.ajax({
				url: "stat.php"
			}).done(function(cont) {
				$(\'#stat\').html(cont);
			});
		}, 5000);		
	});
</script>
</head>
<a href="?uid='.$_GET['uid'].'">Обвновить</a> <a href="?show=all">Архив</a><br><br><br><br>
<form method="GET" id="user_select"><select name="uid" onchange="document.getElementById(\'user_select\').submit();">';
$quser=$db->query('SELECT * FROM users ORDER BY user_id ASC');
while ($user=$db->fetch($quser))
{
	echo '<option value="'.$user['user_id'].'">'.$user['user_id'].' '.$user['user_email'].'</option>';
}
echo '</select></form>
<div style="border: 1px solid black; width: 300px;">Состояние процессов:<div id="stat"></div></div>';
if ($_GET['show']=='all')
{
	$qorder=$db->query('SELECT * FROM blog_orders as a LEFT JOIN users as b ON a.user_id=b.user_id WHERE a.ut_id=0 ORDER BY order_id DESC');
	while ($order=$db->fetch($qorder))
	{
		if ($order['ut_id']==0)	$dumped.='<tr><td>'.$order['order_id'].'</td><td>'.$order['order_name'].'</td><td><a href="?recover='.$order['order_id'].'&uid='.$order['user_id'].'&show=all" onclick="return confirm(\'Восстановить тему?\')&&confirm(\'Точно восстановить тему?\')&&confirm(\'Точно-точно восстановить тему?\');">Восстановление</a></td></tr>';
	}
}
elseif ($_GET['uid']!='')
{
	$qorder=$db->query('SELECT * FROM blog_orders as a LEFT JOIN users as b ON a.user_id=b.user_id WHERE a.user_id='.$_GET['uid'].' ORDER BY order_id DESC');
	while ($order=$db->fetch($qorder))
	{
		// echo $order['ut_id'].' ';
		if (($order['order_id']==$_GET['recover'])||($order['order_id']==$_GET['dump']))
		{
			echo '<br>'.$order['order_id'].' <a href="?uid='.$_GET['uid'].'">Обновить</a><br><br>';
		}
		if ($order['ut_id']==0)	$dumped.='<tr><td>'.$order['order_id'].'</td><td>'.$order['order_name'].'</td><td><a href="?recover='.$order['order_id'].'&uid='.$_GET['uid'].'" onclick="return confirm(\'Восстановить тему?\')&&confirm(\'Точно восстановить тему?\')&&confirm(\'Точно-точно восстановить тему?\');">Восстановление</a></td></tr>';
		else $notdumped.='<tr><td>'.$order['order_id'].'</td><td>'.$order['order_name'].'</td><td><a href="?dump='.$order['order_id'].'&uid='.$_GET['uid'].'" onclick="return confirm(\'Заархивировать тему?\')&&confirm(\'Точно заархивировать тему?\')&&confirm(\'Точно-точно заархивировать тему?\');">Архивация</a></td></tr>';
	}
}

$dumped='<table border="1">'.$dumped.'</table>';
$notdumped='<table border="1">'.$notdumped.'</table>';

echo '<div style="float: left">'.$notdumped.'</div><div style="float: left;">'.$dumped.'</div>';

?>