<?

require_once('/var/www/com/config.php');
require_once('/var/www/com/db.php');
require_once('/var/www/com/auth.php');

$db = new database();
$db->connect();

$qw=$db->query('SELECT * FROM blog_orders WHERE order_id='.intval($_POST['order_id']).' LIMIT 1');
$row=$db->fetch($qw);
$zap1='';
$zap2='';
//Создание темы по образу и подобию
foreach ($row as $key=>$value)
{
	if ($key!='order_id')
	{
		if ($key=='order_name') $value.=' СЛУЧАЙНАЯ ВЫБОРКА '.intval($_POST['selection_size']);
		if ($key=='third_sources') $value=time();
		if ($key=='order_start') $value=strtotime($_POST['start_time']);
		if ($key=='order_end') $value=strtotime($_POST['end_time']);
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
$res3=$db->query('SELECT * FROM `blog_post` WHERE order_id='.intval($_POST['order_id']).($_POST['notspam']=='on'?' AND post_spam=0':'').($_POST['start_time']!=''&&$_POST['end_time']!=''?' AND post_time>='.strtotime($_POST['start_time']).' AND post_time<'.(strtotime($_POST['end_time'])+86400):'').' ');
$fullsize=$db->num_rows($res3)-1;

if ($_POST['selection_size']>$fullsize) $_POST['selection_size']=$fullsize;
if (intval($_POST['selection_size'])==0) $_POST['selection_size']=intval($fullsize/10);

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
		$insert_new_id=$db->insert_id();
		$qfull_post=$db->query('SELECT * FROM blog_full_com WHERE ful_com_post_id='.$post['post_id'].' AND ful_com_order_id='.$post['order_id'].' LIMIT 1');
		$full_post=$db->fetch($qfull_post);
		$resful=$db->query('INSERT INTO blog_full_com (ful_com_post_id,ful_com_order_id,ful_com_post) VALUES ('.$insert_new_id.','.intval($new_order_id).',\''.addslashes($full_post['ful_com_post']).'\')');
		//echo 'INSERT INTO `blog_post` ('.$params.') values ('.$values.')<br>';
	}
	$i++;
}

$result = file_get_contents('http://localhost/tools/cashjob.php?order_id='.intval($new_order_id));

?>