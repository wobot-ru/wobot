<?
require_once('/var/www/com/config.php');
//require_once('/var/www/com/func.php');
require_once('/var/www/com/db.php');
//require_once('/var/www/bot/kernel.php');
//require_once('/var/www/new/com/porter.php');
require_once('/var/www/daemon/fsearch3/ch.php');
require_once('/var/www/tools/epikur/infix.php');
//$_GET['id']=702;
//$word=new Lingua_Stem_Ru();
//$msg=$word->stem_word('белоцкий');
//  путин | медведев && (( прохоров & жириновский | зюганов) | миронов)
//  путин | медведев && (//1 | миронов)
//  путин | медведев && //2
$db = new database();
$db->connect();

$mex=explode('|','скачать|песня|продать|купить|отдать|орейро|sapato|oreiro|пиво|одежда|"esso_besso"|"играем командами"| ассортимент|автомагазин|кроссовки|босоножки|обувь| туфли|сапоги|сандалии|сабо|"Sergio esso"|"de_esso"|игры|смс|Las Ketchup|продажа|порно|секс|казино|игровые автоматы|лучшие рингтоны|мобильная версия');
echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">';
//print_r($mex);
if ($_GET['id']!='')
{
	echo '<a href="check_post_new.php">назад</a><form method="GET"><input type="text" name="id"><input type="submit"></form>';
	$q=$db->query('SELECT post_id,post_content,order_keyword,ful_com_post FROM blog_post as a LEFT JOIN blog_orders as b ON a.order_id=b.order_id LEFT JOIN blog_full_com as c ON a.post_id=c.ful_com_post_id WHERE b.order_id='.intval($_GET['id']).' ORDER BY post_id DESC LIMIT 500');
	while ($p=$db->fetch($q))
	{
		//print_r($p);
		//if (check_post($p['post_content'],$p['order_keyword'])=='YES')
		$c=0;
		/*foreach ($mex as $item)
		{
			if (preg_match('/'.$item.'/isu',$p['post_content']))
			{
				$c=2;
				break;
			}
			else
			{
				$c=1;
			}
		}*/
		//echo check_post($p['order_keyword'],$p['post_content']);
		if ((check_post(strip_tags($p['post_content']),$p['order_keyword'])==1) || (check_post(strip_tags($p['ful_com_post']),$p['order_keyword'])==1))
		//if (check_post(strip_tags($p['post_content']),$p['order_keyword'])==1)
		//if ($c==1)
		{
			echo '<div style="background: #0f0;">'.$p['post_id'].' '.$p['order_keyword'].'<br>'.strip_tags($p['post_content']).'|||'.strip_tags($p['ful_com_post']).'</div><br>';
		}
		else
		{
			//echo 'DELETE FROM blog_post WHERE post_id='.$p['post_id'].'<br>';
			//$db->query('DELETE FROM blog_post WHERE post_id='.$p['post_id']);
			echo '<div style="background: #f00;">'.$p['post_id'].' '.$p['order_keyword'].'<br>'.strip_tags($p['post_content']).'|||'.strip_tags($p['ful_com_post']).'</div><br>';
		}
		if ((ckeck_content($p['post_content'],$p['order_keyword'])==1) || (ckeck_content($p['ful_com_post'],$p['order_keyword'])==1))
		{
			// echo ckeck_content($p['post_content'],$p['order_keyword']).' '.ckeck_content($p['ful_com_post'],$p['order_keyword']);
			echo '<div style="background: #0f0;">'.$p['post_id'].' '.$p['order_keyword'].'<br>'.strip_tags($p['post_content']).'</div><br>';
		}
		// elseif ((ckeck_content($p['ful_com_post'],$p['order_keyword'])==1))
		// {
		// 	echo '][<div style="background: #0f0;">'.$p['order_keyword'].'<br>'.strip_tags($p['post_content']).'</div><br>';
		// }
		else
		{
			echo '<div style="background: #f00;">'.$p['post_id'].' '.$p['order_keyword'].'<br>'.strip_tags($p['post_content']).'</div><br>';
		}
		echo '<br>------------------<br>';
	}
}
else
{
	$qorder=$db->query('SELECT order_id,order_name FROM blog_orders ORDER BY order_id DESC');
	while ($order=$db->fetch($qorder))
	{
		echo '<a href=?id='.$order['order_id'].'>'.$order['order_name'].'</a><br>';
	}
	echo '<form method="GET"><input type="text" name="id"><input type="submit"></form>';
}

//echo check_post('путина краб','путин ~краб');
//for ($i=0;$i<3000;$i++)
/*$cont=file_get_contents('http://blogs.mail.ru/cgi-bin/journal/last_public_posts?rss=1');
$cont=iconv('windows-1251','UTF-8',$cont);
//echo $cont;
$regex='/<item>.*?<description>(?<cont>.*?)<\/description>.*?<pubDate>(?<time>.*?)<\/pubDate>.*?<link>(?<link>.*?)<\/link>.*?<\/item>/is';
preg_match_all($regex,$cont,$out);
//print_r($out);
foreach ($out['cont'] as $key => $item)
{
	echo $key;
	if (check_post($item,'путин')=='YES')
	{
		echo $item."\n\n\n\n";
	}
}

preg_match_all('/\((?<kw>[^()]*?)\)/is','путин | медведев && (( прохоров && жириновский | зюганов) | миронов) | (кудрин | дядя ваня)',$out,PREG_OFFSET_CAPTURE);*/
//echo mb_strlen(substr('путин | медведев && (( прохоров && жириновский | зюганов) | миронов) | (кудрин | дядя ваня)', 0, $out['kw'][0][1]), "UTF-8");
//print_r($out);
?>