<?

// require_once('bot/kernel.php');

function get_comments($url,$cont)
{
	// echo $cont;
	$regex='/\?thread.*/isu';
	$url=preg_replace($regex,'',$url);
	if ($outmas=='')
	{
		$regex='/<p\sclass\=\"comments\">.*?<a[^<]*?>(?<data>\d+).*?<\/a>/isu';
		preg_match_all($regex, $cont, $out);
		$outmas=$out['data'][0];
	}
	echo $outmas.'1';
	$regex='/<p.*?class=\"lesstop\".*?>(?<data>.*?)<\/p>/isu';
	preg_match_all($regex,$cont,$out);
	if ($out['data'][0]!='')
	{
		$out['data'][0]=strip_tags($out['data'][0]);
		$out['data'][0]=preg_replace('/[^0-9]/isu','',$out['data'][0]);
		$outmas=$out['data'][0];
	}
	echo $outmas.'2';
	if ($outmas=='')
	{
		$regex='/Comments {(?<data>.*?)}/isu';
		preg_match_all($regex,$cont,$out);
		$outmas=$out['data'][0];
	}
	echo $outmas.'3';
	if ($outmas=='')
	{
		$regex='/<p.*?class=\'lesstop\'.*?>(?<data>.*?)<\/p>/isu';
		preg_match_all($regex,$cont,$out);
		$out['data'][0]=strip_tags($out['data'][0]);
		$out['data'][0]=preg_replace('/[^0-9]/isu','',$out['data'][0]);
		$outmas=$out['data'][0];
	}
	echo $outmas.'4';
	if ($outmas=='')
	{
		$regex='/<span[^<]*?class=[\'\"]comments-count[\'\"]>(<a[^<]*?>)?[\sа-яёa-z]*?(?<data>\d+)[\sа-яёa-z]*?(<\/a>)?<\/span>/isu';
		preg_match_all($regex, $cont, $out);
		$outmas=$out['data'][0];
	}
	echo $outmas.'4?';
	if ($outmas=='')
	{
		$regex='/<div class="comments-nav">[\s\(]+<a[^<]*?>[a-zа-яё\s]*?(?<data>\d+)[a-zа-яё\s]+<\/a>/isu';
		preg_match_all($regex, $cont, $out);
		$outmas=$out['data'][0];
	}
	echo $outmas.'4!';

	if ($outmas=='')
	{
		$regex='/<div.*?id=\"(?<data>ljcmt.*?)\"/isu';
		preg_match_all($regex,$cont,$out);
		//echo $cont;
		//print_r($out);
		if (count($out['data'])==0)
		{
			$regex='/<div.*?id=\'(?<data>ljcmt.*?)\'/isu';
			preg_match_all($regex,$cont,$out);
			//print_r($out);
		}
		$outmas=count($out['data']);
	}
	echo $outmas.'5';
	if ($outmas=='')
	{
		$regex='/\'comment\'/isu';
		preg_match_all($regex,$cont,$out);
		$outmas=count($out[0]);
	}
	echo $outmas.'6';
	if ($outmas=='')
	{
		$regex='/<span class="">(?<data>.*?)<\/span>/isu';
		preg_match_all($regex,$cont,$out);
		$outmas=intval($out['data'][0]);
	}
	$outm['count']=intval($outmas);
	$outm['data']['comment']=intval($outmas);
	return $outm;
}

// $cont=parseUrl('http://188.120.239.225/getlist.php');
// $mproxy=json_decode($cont,true);
// print_r(get_comments('http://kostya-moskowit.livejournal.com/2259495.html'));

?>
