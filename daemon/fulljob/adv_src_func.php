<?

/*require_once('/var/www/daemon/com/db.php');
require_once('/var/www/daemon/com/config.php');

$db=new database();
$db->connect();

error_reporting(0);*/

function get_links($xpath,$cont)
{
	$html = new DOMDocument();
	$cont = mb_convert_encoding($cont, 'HTML-ENTITIES', "UTF-8");//неведомая ебаная перекодировка....
	$html->loadHTML( $cont );
	$xp = new DOMXPath( $html );
	$lks = $xp->query( $xpath );
	foreach ( $lks as $link )
	{
		if (($link->getAttribute('href')!='#') && (!preg_match('/^javascript\:.*/isu',$link->getAttribute('href'))))
		$links[]=$link->getAttribute('href');
	}
	return $links;
}

function get_pst($xpath,$cont)
{
	$html = new DOMDocument();
	$cont = mb_convert_encoding($cont, 'HTML-ENTITIES', "UTF-8");//неведомая ебаная перекодировка....
	$html->loadHTML( $cont );
	$xp = new DOMXPath( $html );
	$conts = $xp->query( $xpath );
	foreach ( $conts as $cont )
	{
		//echo $cont->nodeValue."\n\n\n\n\n";
		$posts[]=$cont->nodeValue;
	}
	return $posts;
}

function get_pst_al($xpath,$cont)
{
	$html = new DOMDocument();
	$cont = mb_convert_encoding($cont, 'HTML-ENTITIES', "UTF-8");//неведомая ебаная перекодировка....
	$html->loadHTML( $cont );
	$xp = new DOMXPath( $html );
	$conts = $xp->query( $xpath );
	foreach ( $conts as $cont )
	{
		//echo $cont->nodeValue."\n\n\n\n\n";
		$posts=trim(preg_replace('/\s+/isu',' ',$cont->nodeValue));
	}
	return $posts;
}

function get_banki($link,$cont)
{
	$xpth_link='//a[@title="Ccылка на это сообщение"]';
	$xpth_post='//div[@class="forum-post-text"]';
	$links=get_links($xpth_link,$cont);
	$posts=get_pst($xpth_post,$cont);
	if (count($links)!=0)
	{
		foreach ($links as $key => $item)
		{
			if ($item==preg_replace('/read/isu','message',$link))
			{
				return trim(preg_replace('/\s+/isu', ' ', $posts[$key]));
			}
		}
	}
	else
	{
		$pst_xpth='//div[@class="b-article__post"]|//div[@class="b-el-article__text"]';
		$psts=get_pst($pst_xpth,$cont);
		foreach ($psts as $pst_item)
		{
			if ($pst_item!='')
			{
				return trim(preg_replace('/\s+/isu', ' ', $pst_item));
			}
		}
	}
}

/*echo get_pst_al('//p[@class="shout"]',file_get_contents('https://foursquare.com/kyle_soul/checkin/507e822ce4b0054d921b14a5?ref=tw&s=wuSZLfmxmDB8gip3hgmFiF51gXI'));
die();

$qp=$db->query('SELECT * FROM blog_post WHERE order_id=712 AND post_host=\'banki.ru\'');
while ($post=$db->fetch($qp))
{
	if (preg_match('/www\.banki\.ru\/forum\//isu',$post['post_link']))
	{
		sleep(1);
		echo $post['post_link']."\n";
		echo get_banki($post['post_link'],iconv('windows-1251','UTF-8',file_get_contents($post['post_link'])))."\n";
		echo "\n--------\n";
	}
}*/
//$post['post_link']='http://www.banki.ru/forum/index.php?PAGE_NAME=read&FID=12&TID=78991&MID=1776967#message1776967';
//echo get_banki($post['post_link'],iconv('windows-1251','UTF-8',file_get_contents($post['post_link'])));
?>