<?
require_once('kernel.php');
require_once('get_rss.php');	

$cont=parseUrl('http://www.ridus.ru/news/24281/');
//$cont=iconv('windows-1251','UTF-8',$cont);
preg_match_all('/charset=([-a-z0-9_]+)/is',$cont,$charset);
//print_r($charset);
if (($charset[1][0]!='') || ($charset[1][0]!='utf-8'))
{
	if (mb_strtolower($charset[1][0],'UTF-8')!="utf-8")
	{
		echo $charset[1][0].'LALALALALAL';
		$cont=iconv($charset[1][0], "UTF-8", $cont);
	}
}
echo $cont;

/*$regex='/<[^<]*?>(?<content>.*?ядро.*?)<.*?>/isu';
preg_match_all($regex,$cont,$ou,PREG_OFFSET_CAPTURE);
print_r($ou);*/
//$cont=strip_tags($cont,'<body><div><span><li>');
$cont=preg_replace('/(<script.*?>.*?<\/script>)/is','',$cont);
$cont=preg_replace('/(<style.*?>.*?<\/style>)/is','',$cont);
$cont=preg_replace('/(<link[^<]*?>)/is','',$cont);
$cont=preg_replace('/(<img[^<]*?>)/is','',$cont);
$cont=preg_replace('/&nbsp;/is','',$cont);
//echo $cont;
$cont=preg_replace('/<u>(.*?)<\/u>/is','$1',$cont);
$cont=preg_replace('/<b>(.*?)<\/b>/is','$1',$cont);
$cont=preg_replace('/<a[^<]*?>(\d+)<\/a>/is','$1',$cont);
$cont=preg_replace('/<sup>(.*?)<\/sup>/is','$1',$cont);
$cont=preg_replace('/<font[^<]*?>(.*?)<\/font>/is','$1',$cont);
$cont=preg_replace('/<[\/]?br( \/)?>/is',' ',$cont);
//echo $cont."\n\n\n\n\n";
$cont=preg_replace('/<[^<]*?>/is','|',$cont);
$cont=preg_replace('/\|+/is','|',$cont);
$cont=preg_replace('/\s+/is',' ',$cont);
$cont=preg_replace('/(\| )+/is','|',$cont);
$regex_time='/(?<time>\|\d\d?\:\d\d[\.\,\s]\d\d?[\.\,\s\-]\d\d?[\.\,\s\-]\d\d\d\d)|(?<time1>[\|\s](\d\d?\d?\d?|[a-zA-Z]*?)?[\.\,\s\-](?:\d\d?|[а-яА-ЯёЁa-zA-Z]*?)[\,]?[\.\,\s\-]\d\d\d?\d?[\,\s]?(?:\s|&nbsp;|\-|[a-zA-Z][a-zA-Z])?[\s]?(<[^<]*?>)?\d\d?:\d\d\s?)/isu';
echo $cont;
$mas_part=explode('|',$cont);
//print_r($mas_part);
echo preg_match($regex_time,'|'.$mas_part[51]).'|'.$mas_part[51].'//';
$c=0;
$undo=0;
foreach ($mas_part as $key => $item)
{
	if (preg_match($regex_time,'|'.$item))
	{
		echo $key.'FFFGGGG';
		echo $item."\n";
		$c=1;
	}
	if (mb_strlen($item,'UTF-8')>100)
	{
		echo $key.'HHHH';
		if ($c==1)
		{
			$undo=1;
		}
		break;
	}
}
echo 'UNDO='.$c;
echo mb_strpos($mas_part[225],'интерфейс').'|';
$regex='/\|(?<content>[^|]*?бандит.*?)\|/isu';
preg_match_all($regex,$cont,$ou,PREG_OFFSET_CAPTURE);
//print_r($ou);
preg_match_all($regex_time,$cont,$out,PREG_OFFSET_CAPTURE);
print_r($out);
foreach ($out['time'] as $key => $item)
{
	if ($item[1]!='-1')
	{
		$mtime['post'][]=$item[1];
		$mtime['time'][]=$item[0];
	}
}
foreach ($out['time1'] as $key => $item)
{
	if ($item[1]!='-1')
	{
		$mtime['post'][]=$item[1];
		$mtime['time'][]=$item[0];
	}
}
print_r($mtime);
print_r($ou['content']);
foreach ($ou['content'] as $item)
{
	if ($undo==0)
	{
		foreach ($mtime['post'] as $key => $it_m)
		{
			//echo $item[1].'|'.mb_strlen($item[0],'UTF-8').'|'.$it_m."\n";
			if (($item[1]+mb_strlen($item[0],'UTF-8'))<$it_m)
			{
				$posts['cont'][]=$item[0];
				$posts['time'][]=$mtime['time'][$key];
				break;
			}
		}
	}
	else
	{
		//echo '22222';
		foreach ($mtime['post'] as $key => $it_m)
		{
			echo $item[1].'|'.mb_strlen($item[0],'UTF-8').'|'.$it_m."\n";
			if (($item[1]+mb_strlen($item[0],'UTF-8'))<$it_m)
			{
				//echo $key.' gg';
				$posts['cont'][]=$item[0];
				$posts['time'][]=$mtime['time'][$key-1];
				break;
			}
		}
	}
	//echo strip_tags($item[0]).' '.mb_strlen($item[0]).' '.$item[1]."\n\n\n\n";
}
echo $ou['content'][count($ou['content'])-1][1].' '.$mtime['post'][count($mtime['time'])-1].' '.$undo;
if (($ou['content'][count($ou['content'])-1][1]>$mtime['post'][count($mtime['time'])-1]) && ($undo==1))
{
	echo '/'.$ou['content'][count($ou['content'])-1][1].'/'.$mtime['post'][count($mtime['time'])-1].'/';
	$posts['cont'][]=$ou['content'][count($ou['content'])-1][0];
	$posts['time'][]=$mtime['time'][count($mtime['time'])-1];
}
print_r($posts);
?>