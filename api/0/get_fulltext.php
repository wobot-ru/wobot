<?
require_once('/var/www/new/com/config.php');
require_once('/var/www/new/com/func.php');
require_once('/var/www/new/com/db.php');
require_once('/var/www/new/bot/kernel.php');
require_once('auth.php');
require_once('/var/www/com/loc.php');
date_default_timezone_set ( 'Europe/Moscow' );
$db = new database();
$db->connect();

auth();
if (!$loged) die();
$_GET=$_POST;
$exc_src=array('twitter.com','diary.ru','foursquare.com','ya.ru','yandex.ru');
$p=$db->query('SELECT a.post_content,b.ful_com_post,c.order_keyword,a.post_host FROM blog_post as a LEFT JOIN blog_full_com as b ON a.post_id=b.ful_com_post_id LEFT JOIN blog_orders as c ON a.order_id=c.order_id WHERE a.post_id='.intval($_GET['id']).' AND a.order_id='.intval($_GET['order_id']).' LIMIT 1');
//$p=$db->query('SELECT ful_com_post FROM blog_full_com WHERE ful_com_post_id='.intval($_GET['id']).' AND ful_com_order_id='.intval($_GET['order_id']).' LIMIT 1');
//echo 'SELECT a.post_content,b.ful_com_post,c.order_keyword FROM blog_post as a LEFT JOIN blog_full_com as b ON a.post_id=b.ful_com_post_id LEFT JOIN blog_orders as c ON a.order_id=c.order_id WHERE a.post_id='.intval($_GET['id']).' AND a.order_id='.intval($_GET['order_id']);
$pp=$db->fetch($p);
if (($pp['ful_com_post']!='') && (!in_array($pp['post_host'],$exc_src)))
{
	$mas['full_content']=($pp['ful_com_post']);
	$pp['order_keyword']=preg_replace('/[^а-яА-Яa-zA-ZёЁ\ \-\=\']/isu','  ',$pp['order_keyword']);
	$mkw=explode('  ',$pp['order_keyword']);
	foreach ($mkw as $kw)
	{
		if (mb_strlen($kw,'UTF-8')>3)
		{
			$regex='/\.(?<frase>[^\.]*?\.[^\.]*?\.[^\.]*?'.addslashes($kw).'\.[^\.]*?\.[^\.]*?\.)/isu';
			preg_match_all($regex,$mas['full_content'],$out);
			foreach ($out['frase'] as $item)
			{
				if (($item!='') && ($item!=' '))
				{
					$outmas[]=$item;
				}
			}
		}
	}
	if ($outmas[0]!='')
	{
		$mas['full_content']=html_entity_decode($outmas[0],ENT_QUOTES,'UTF-8');
	}
	elseif ($pp['ful_com_post']!='')
	{
		//echo '|'.html_entity_decode($pp['ful_com_post'],ENT_QUOTES,'UTF-8').'|';
		if (html_entity_decode($pp['ful_com_post'],ENT_QUOTES,'UTF-8')=='')
		{
			$mas['full_content']=$pp['ful_com_post'];
		}
		else
		{
			$mas['full_content']=html_entity_decode($pp['ful_com_post'],ENT_QUOTES,'UTF-8');
		}
	}
}
else
{
	$parts=explode("\n",html_entity_decode(strip_tags($pp['post_content']),ENT_QUOTES,'UTF-8'));
	$mas['full_content']=($parts[1]!=''?$parts[1]:($parts[0]!=''?$parts[0]:strip_tags($pp['post_content'])));
}

echo json_encode($mas);


?>