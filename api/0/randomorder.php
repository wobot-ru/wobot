<?php

require_once('/var/www/new/com/config.php');
require_once('/var/www/new/com/func.php');
require_once('/var/www/new/com/db.php');
require_once('/var/www/new/bot/kernel.php');
require_once('auth.php');
require_once('/var/www/com/loc.php');

$redis=new Redis() or die("Can'f load redis module.");
$redis->connect('127.0.0.1');
set_log('randomorder',$_POST);

ignore_user_abort(true);
set_time_limit(0);
ini_set('max_execution_time',0);
ob_implicit_flush();

$db = new database();
$db->connect();

auth();
if (!$loged) die();

// if ($_POST['action']=='selection')
{
	$qw=$db->query('SELECT * FROM blog_orders WHERE order_id='.intval($_POST['order_id']).' LIMIT 1');
	$row=$db->fetch($qw);
	$zap1='';
	$zap2='';
	//Создание темы по образу и подобию
	foreach ($row as $key=>$value)
	{
		if ($key=='order_end')
		{
			if ($value>=time()) $value=mktime(0,0,0,date('n'),date('j'),date('Y'));
		}
		if ($key!='order_id')
		{
			if ($key=='order_name') $value.=' Random';
			$params.=$zap1."`".$key."`";
			$values.=$zap2.'"'.str_replace('"', '\"', $value).'"';
			$zap1=',';
			$zap2=',';
		}
	}
	$res2=$db->query('INSERT INTO `blog_orders` ('.$params.') values ('.$values.')');
	//echo 'INSERT INTO `blog_orders` ('.$params.') values ('.$values.')<br>';
	$new_order_id=$db->insert_id();
	
	//Перенос тегов
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

	//Генерация случайных номеров сообщений
	$random_items=array();
	$posts=array();
	$res3=$db->query('SELECT * FROM `blog_post` WHERE order_id='.intval($_POST['order_id']));
	$fullsize=$db->num_rows($res3)-1;

	if (isset($_POST['selection_size_proc']))
	{
		$_POST['selection_size']=($fullsize/100)*intval($_POST['selection_size_proc']);	
	}
	if ($_POST['selection_size']>$fullsize) $_POST['selection_size']=$fullsize;
	if (intval($_POST['selection_size'])<=0) $_POST['selection_size']=intval($fullsize/10);

	do
	{
		$rand_item=rand(0, $fullsize);
		if (!in_array($rand_item, $random_items)) $random_items[]=$rand_item;
	}
	while (count($random_items)<intval($_POST['selection_size']));

	//Перенос сообщений в новую тему
	$i=0;
	while($post=$db->fetch($res3))
	{
		if (in_array($i, $random_items))
		{
			$params='';
			$values='';
			$zap1='';
			$zap2='';
			$blog_post_id_old=0;
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
				else
				{
					$blog_post_id_old=$value;
				}
			}
			$res4=$db->query('INSERT INTO `blog_post` ('.$params.') values ('.$values.')');
			$ins_new_post_id=$db->insert_id($res4);
			if (intval($blog_post_id_old)!=0) 
			{
				$r5=$db->query('SELECT * FROM blog_full_com WHERE ful_com_post_id='.intval($blog_post_id_old).' LIMIT 1');
				$bfc=$db->fetch($r5);
				if (intval($bfc['ful_com_id'])!=0) $db->query('INSERT INTO blog_full_com (ful_com_post_id,ful_com_order_id,ful_com_post) VALUES ('.$ins_new_post_id.','.$new_order_id.',\''.$bfc['ful_com_post'].'\')');
			}
			//echo 'INSERT INTO `blog_post` ('.$params.') values ('.$values.')<br>';
		}
		$i++;
	}

	$result = file_get_contents('http://localhost/tools/cashjob.php?order_id='.intval($new_order_id));
	echo json_encode(array('status'=>'ok'));
	//echo '<script>alert("Случайная выборка создана");</script>';
}
?>