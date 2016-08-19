<?

require_once('/var/www/daemon/com/users.php');

require_once('/var/www/com/db.php');
require_once('/var/www/com/config.php');

$db=new database();
$db->connect();

$user=new users();
// print_r($user->get_nick('http://friendfeed.com/llqjgdk'));

echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';

// $order_id=5049;

function get_blocks($xpath,$cont)
{
	$html = new DOMDocument();
	$cont = mb_convert_encoding($cont, 'HTML-ENTITIES', "UTF-8");//неведомая ебаная перекодировка....
	$html->loadHTML( $cont );
	$xp = new DOMXPath( $html );
	$blocks = $xp->query( $xpath );
	foreach ($blocks as $block)
	{
		//echo $html->saveXML($block);
		$blks[]=$html->saveXML($block);
	}
	return $blks;
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
		//$posts=$cont->nodeValue;
		if (preg_replace('/<[^<]*?>/isu',' ',trim($html->saveXML($cont)))!='      показать полный текст            оригинал   сохранённая копия        показать полный текст       ') $posts=$html->saveXML($cont);
	}
	$posts=preg_replace('/<[^<]*?>/isu',' ',$posts);
	return $posts;
}

function get_date($xpath,$cont)
{
	//echo $xpath;
	$html = new DOMDocument();
	$cont = mb_convert_encoding($cont, 'HTML-ENTITIES', "UTF-8");//неведомая ебаная перекодировка....
	//echo $cont;
	$html->loadHTML( $cont );
	$xp = new DOMXPath( $html );
	$dts = $xp->query( $xpath );
	foreach ( $dts as $date )
	{
		//echo $date->nodeValue."\n";
		$dates=$date->nodeValue;
	}
	return $dates;
}

function get_links($xpath,$cont)
{
	$html = new DOMDocument();
	$cont = mb_convert_encoding($cont, 'HTML-ENTITIES', "UTF-8");//неведомая ебаная перекодировка....
	$html->loadHTML( $cont );
	$xp = new DOMXPath( $html );
	$lks = $xp->query( $xpath );
	print_r($lks);
	foreach ( $lks as $link )
	{
		if (($link->getAttribute('href')!='#') && (!preg_match('/^javascript\:.*/isu',$link->getAttribute('href'))))
		$links=$link->getAttribute('href');
	}
	return $links;
}

// $filename = "/var/www/stroik/load_yandex.txt";
// $handle = fopen($filename, "r");
// $contents = fread($handle, filesize($filename));
// fclose($handle);

if ($_POST['content']!='')
{
	$contents=$_POST['content'];
	$order_id=$_POST['order_id'];
	$blocks=get_blocks('//div[@class="b-item i-bem Ppb-c-ItemMore SearchStatistics-item"]|//div[@class="b-item i-bem Ppb-c-ItemMore SearchStatistics-item b-item_type_twitter"]',$contents);
	// print_r($blocks);
	foreach ($blocks as $block)
	{
		$regex='/<a href="(?<link>.*?)".*?class="b-twitter-link.*?\".*?\>/isu';
		preg_match_all($regex, $block, $out);
		if (trim($out['link'][0])=='')
		{
			$regex='/<a href=\"(?<link>.*?)\".*?class=" SearchStatistics-link".*?\>/isu';
			preg_match_all($regex, $block, $out);
		}
		$outmas['link'][]=$out['link'][0];
		$outmas['cont'][]=get_pst('//div[@class="message"]|//h3/a',$block);
		$outmas['time'][]=get_date('//ul[@class="info b-hlist b-hlist-middot"]',$block);
		// echo strip_tags($block)."\n\n";
	}

	foreach ($outmas['time'] as $key => $item)
	{
		if (preg_match('/\d+\s[а-я]+\s\d+\,\s\d+\:\d+/isu',$item))
		{
			$ascarray['января']=1;
			$ascarray['февраля']=2;
			$ascarray['марта']=3;
			$ascarray['апреля']=4;
			$ascarray['мая']=5;
			$ascarray['июня']=6;
			$ascarray['июля']=7;
			$ascarray['августа']=8;
			$ascarray['сентября']=9;
			$ascarray['октября']=10;
			$ascarray['ноября']=11;
			$ascarray['декабря']=12;
			$regex='/(?<day>\d+)\s(?<mon>[а-я]+)\s(?<year>\d+)\,\s(?<hour>\d+)\:(?<min>\d+)/isu';
			preg_match_all($regex, $item, $out);
			// print_r($out);
			$otm['time'][]=mktime($out['hour'][0],$out['min'][0],0,$ascarray[$out['mon'][0]],$out['day'][0],$out['year'][0]);
			// echo "1\n";
		}
		elseif (preg_match('/вчера\,\s\d+\:\d+/isu', $item))
		{
			$regex='/вчера\,\s(?<hour>\d+)\:(?<min>\d+)/isu';
			preg_match_all($regex, $item, $out);
			$otm['time'][]=mktime($out['hour'][0],$out['min'][0],0,date('n'),date('j')-1,date('Y'));
			// echo "2\n";
		}
		elseif (preg_match('/\d+\s\ч\.\s\d+\sмин\.\sназад/isu', $item))
		{	
			$regex='/(?<hour>\d+)\s\ч\.\s(?<min>\d+)\sмин\.\sназад/isu';
			preg_match_all($regex, $item, $out);
			$otm['time'][]=time()-$out['hour'][0]*3600-$out['min'][0]*60;
		}
		else
		{
			// echo "4\n";
		}
		$otm['cont'][]=preg_replace('/\s+/isu', ' ', $outmas['cont'][$key]);
		$otm['link'][]=$outmas['link'][$key];
	}
	// print_r($otm);
	// print_r($outmas);

	$engage_src['twitter.com']=1;
	$engage_src['vk.com']=1;
	$engage_src['facebook.com']=1;
	$engage_src['livejournal.com']=1;

	foreach ($otm['link'] as $key => $item)
	{
		$hn=parse_url($item);
		$hn=$hn['host'];
		$ahn=explode('.',$hn);
		$hn = $ahn[count($ahn)-2].'.'.$ahn[count($ahn)-1];
		$hh = $ahn[count($ahn)-2];
		$blog=$user->get_nick($item);
		// print_r($blog);
		// echo 'INSERT INTO blog_post_prev (order_id,post_link,post_host,post_time,post_content,'.($engage_src[$hn]!=1?'post_engage,':'').'blog_id,post_ful_com) VALUES ('.$order_id.',\''.addslashes($item).'\',\''.addslashes($hn).'\',\''.$otm['time'][$key].'\',\''.addslashes($otm['cont'][$key]).'\','.($engage_src[$hn]!=1?'0,':'').intval($blog).',\''.addslashes($otm['fulltext'][$key]).'\')'."\n";
		$db->query('INSERT INTO blog_post_prev (order_id,post_link,post_host,post_time,post_content,'.($engage_src[$hn]!=1?'post_engage,':'').'blog_id,post_ful_com) VALUES ('.$order_id.',\''.addslashes($item).'\',\''.addslashes($hn).'\',\''.$otm['time'][$key].'\',\''.addslashes($otm['cont'][$key]).'\','.($engage_src[$hn]!=1?'0,':'').intval($blog).',\''.addslashes($otm['fulltext'][$key]).'\')');
	}
	echo 'OK';
}

echo '<form method="POST">ORDER_ID<input type="text" name="order_id" value="'.$_POST['order_id'].'"><br><textarea name="content" cols="80" rows="20"></textarea><br><input type="submit" value="Загрузить"></form>';

?>