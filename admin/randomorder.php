<?
require_once('com/config.php');
require_once('com/db.php');
require_once('com/auth.php');
require_once 'com/MCAPI.class.php';
require_once 'com/MCAPI.inc.php'; //contains apikey
require_once('bot/kernel.php');
require_once('/var/www/com/checker.php');

ignore_user_abort(true);
set_time_limit(0);
ini_set('max_execution_time',0);
ob_implicit_flush();

$db = new database();
$db->connect();

// auth();
// if (!$loged) die();

$_POST['order_id']=2700;
$_POST['action']=='selection';
$_POST['selection_size']=1000000;
echo 3;
// if ($_POST['action']=='selection')
{
	echo 12;
	echo 'SELECT * FROM blog_orders WHERE order_id='.intval($_POST['order_id']).' LIMIT 1'."\n";
	$qw=$db->query('SELECT * FROM blog_orders WHERE order_id='.intval($_POST['order_id']).' LIMIT 1');
	echo 'SELECT * FROM blog_orders WHERE order_id='.intval($_POST['order_id']).' LIMIT 1'."\n";
	$row=$db->fetch($qw);
	$zap1='';
	$zap2='';
	//Ñîçäàíèå òåìû ïî îáðàçó è ïîäîáèþ
	foreach ($row as $key=>$value)
	{
		if ($key!='order_id')
		{
			if ($key=='order_name') $value.=' Рандомная выборка '.intval($_POST['selection_size']);
			$params.=$zap1."`".$key."`";
			$values.=$zap2.'"'.str_replace('"', '\"', $value).'"';
			$zap1=',';
			$zap2=',';
		}
	}
	$res2=$db->query('INSERT INTO `blog_orders` ('.$params.') values ('.$values.')');
	//echo 'INSERT INTO `blog_orders` ('.$params.') values ('.$values.')<br>';
	$new_order_id=$db->insert_id();
	
	//Ïåðåíîñ òåãîâ
	$res5=$db->query('SELECT * FROM `blog_tag` WHERE order_id='.intval($_POST['order_id']));
	while ($tag=$db->fetch($res5))
	{
		$params='';
		$values='';
		$zap1='';
		$zap2='';
		foreach ($tag as $key=>$value)
		{
			if ($key!='tag_id')
			{
				if ($key=='order_id') $value=intval($new_order_id);
				$params.=$zap1."`".$key."`";
				$values.=$zap2.'"'.str_replace('"', '\"', $value).'"';
				$zap1=',';
				$zap2=',';
			}
		}
		$res6=$db->query('INSERT INTO `blog_tag` ('.$params.') values ('.$values.')');
		//echo 'INSERT INTO `blog_tag` ('.$params.') values ('.$values.')<br>';
	}

	//Ãåíåðàöèÿ ñëó÷àéíûõ íîìåðîâ ñîîáùåíèé
	$random_items=array();
	$posts=array();
	$res3=$db->query('SELECT * FROM `blog_post` WHERE order_id='.intval($_POST['order_id']));
	$fullsize=$db->num_rows($res3)-1;

	if ($_POST['selection_size']>$fullsize) $_POST['selection_size']=$fullsize;
	if (intval($_POST['selection_size'])==0) $_POST['selection_size']=intval($fullsize/10);

	do
	{
		$rand_item=rand(0, $fullsize);
		if (!in_array($rand_item, $random_items)) $random_items[]=$rand_item;
	}
	while (count($random_items)<intval($_POST['selection_size']));

	//Ïåðåíîñ ñîîáùåíèé â íîâóþ òåìó
	$i=0;
	while($post=$db->fetch($res3))
	{
		if (in_array($i, $random_items))
		{
			$params='';
			$values='';
			$zap1='';
			$zap2='';
			foreach ($post as $key=>$value)
			{
				if ($key!='post_id')
				{
					if ($key=='order_id') $value=intval($new_order_id);
					$params.=$zap1."`".$key."`";
					$values.=$zap2.'"'.str_replace('"', '\"', $value).'"';
					$zap1=',';
					$zap2=',';
				}
			}
			$res4=$db->query('INSERT INTO `blog_post` ('.$params.') values ('.$values.')');
			//echo 'INSERT INTO `blog_post` ('.$params.') values ('.$values.')<br>';
		}
		$i++;
	}

	$result = file_get_contents('http://188.120.239.225/tools/cashjob.php?order_id='.intval($new_order_id));
	echo '<script>alert("Ñëó÷àéíàÿ âûáîðêà ñîçäàíà");</script>';
}
?>
