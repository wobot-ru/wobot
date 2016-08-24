<?

// require_once('/var/www/bot/kernel.php');

// error_reporting(0);

function get_novoteka($query,$start,$end)
{
	$i=0;
	do
	{
		$link='http://www.novoteka.ru/search?page='.$i.'&sort1=dt&query='.urlencode(iconv('UTF-8','windows-1251',$query));
		$i++;
		echo $link."\n";
		$cont=file_get_contents($link);
		$cont=iconv('windows-1251','UTF-8',$cont);
		// echo $cont;
		$regex='/<h2 class=p01>(?<title>.*?)<\/h2>/isu';
		preg_match_all($regex, $cont, $outtitle);
		$regex='/<span class=news\_anons>(?<cont>.*?)<\/span>/isu';
		preg_match_all($regex, $cont, $outcont);
		$regex='/<span class=asm><span class=data>(?<time>.*?)<\/span><\/span>/isu';
		preg_match_all($regex, $cont, $outtime);
		// print_r($out);
		$regex='/<a class=source target=_blank href=(?<link>.*?)>/isu';
		preg_match_all($regex, $cont, $outlink);
		foreach ($outtitle['title'] as $key => $item)
		{
			if (preg_match('/<span class=curtime>/isu', $outtime['time'][$key]))
			{
				$regex='/(?<hour>\d\d?)\:(?<min>\d\d?)/isu';
				preg_match_all($regex, $outtime['time'][$key], $out);
				$outmas['time'][]=mktime($out['hour'][0],$out['min'][0],0,date('n'),date('j'),date('Y'));
			}
			else
			{
				$regex='/(?<day>\d+)\.(?<mon>\d+)\.(?<year>\d+).*(?<hour>\d\d?)\:(?<min>\d\d?)/isu';
				preg_match_all($regex, $outtime['time'][$key], $out);
				$outmas['time'][]=mktime($out['hour'][0],$out['min'][0],0,$out['mon'][0],$out['day'][0],$out['year'][0]);
			}
			$outmas['content'][]=preg_replace('/\s+/isu',' ',preg_replace('/(<[^\<]*?>)/isu',' ',$item.' '.$outcont['cont'][$key]));
			$regex='/\/\?\_URL\=(?<link>.*)/isu';
			preg_match_all($regex, urldecode($outlink['link'][$key]), $out);
			$outmas['link'][]=$out['link'][0];
		}	
		//echo $outmas['time'][count($outmas['time'])-1].' '.$start;
	}
	while ((count($outtime['time'])!=0)&&($outmas['time'][count($outmas['time'])-1]>$start));
	// print_r($outmas);
	return $outmas;
}

//get_novoteka('(открытие & брокер)|(брокерский & дом & открытие)',mktime(0,0,0,1,20,2015),mktime(0,0,0,1,27,2015));

?>