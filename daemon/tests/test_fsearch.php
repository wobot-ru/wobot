<?

error_reporting(0);

$redis=new Redis() or die("Can'f load redis module.");
$redis->connect('127.0.0.1');

$msrc[]='twitter';
$msrc[]='yandex_blogs';
$msrc[]='facebook';
$msrc[]='vkontakte';
$msrc[]='vkontakte_video';
$msrc[]='topsy';
$msrc[]='slideshare';
$msrc[]='google';
$msrc[]='youtube';
$msrc[]='google_plus';
$msrc[]='bing';

$interval=4;

foreach ($msrc as $item)
{
	for ($i=0;$i<24;$i++)
	{
		if ($i%$interval!=0) continue;
		$msrc_val[$item][mktime($i,0,0,date('n'),date('j'),date('Y'))]=$redis->get('source_'.$item.'_'.mktime($i,0,0,date('n'),date('j'),date('Y')));

	}
}

$out='<table border="1"><tr><td>source:</td>';

for ($i=0;$i<24;$i++)
{
	if ($i%$interval!=0) continue;
	$out.='<td>'.$i.'-'.($i+4).'</td>';
}

foreach ($msrc_val as $key => $item)
{
	$out.='<tr><td>'.$key.'</td>';
	foreach ($item as $k => $i)
	{
		$out.='<td>'.(intval($i)==0?'<p style="color: red;">'.intval($i).'</p>':intval($i)).'</td>';
	}
	$out.='</tr>';
}
$out.='</table>';

// print_r($msrc_val);
$headers  = "From: noreply2@wobot.ru\r\n"; 
$headers .= "Bcc: noreply2@wobot.ru\r\n";
$headers .= 'MIME-Version: 1.0' . "\r\n";
$headers .= 'Content-type: text/html; charset=utf-8'."\r\n";
mail('zmei123@yandex.ru','Тест сбора',$out,$headers);
mail('r@wobot.co','Тест сбора',$out,$headers);

// echo $out;

?>