<?php
require_once('com/config.php');
require_once('com/func.php');
require_once('com/db.php');
//require_once('bot/kernel.php');
require_once('functions.php');

$db = new database();
$db->connect();
$ressec=$db->query('SELECT * FROM blog_orders WHERE order_id=538');
while($blog=$db->fetch($ressec))
{
	if ($blog['order_keyword']!='')
	{
		$skey=$blog['order_keyword'];
	}
	else
	{
		$skey=$blog['order_name'];
	}
	$skey=preg_replace('/[^а-яА-Яa-zA-Z]/isu',' ',$skey);
	$skey=preg_replace('/\s+/is',' ',$skey);
	$mkey=explode(' ',$skey);
	$rs=$db->query('SELECT * from blog_post as p LEFT JOIN blog_full_com AS b ON p.post_id=b.ful_com_post_id WHERE p.order_id='.$blog['order_id']);
	while($pst=$db->fetch($rs))
	{
		if ($pst['ful_com_post']=='')
		{
			//$keys=array('блогер','блоггер','мусоры');
			$res=MakeTexts($mkey, $pst['post_link']);
			print_r($mkey);
			print_r($res);
			//echo $res;
			foreach ($res as $item)
			{
				$out.=$item."\n";
			}
			echo $pst['post_id'].' '.$pst['post_link']."\n";
			echo $out."\n\n\n\n";
			sleep(1);
			$upd=$db->query('UPDATE blog_full_com SET ful_com_post=\''.addslashes($out).'\' WHERE ful_com_post_id='.$pst['post_id']);
			$out='';
			//echo 'UPDATE blog_full_com SET ful_com_post=\''.addslashes($out).'\' WHERE ful_com_post_id='.$pst['post_id'];
		}
	}
}

?>

