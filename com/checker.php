<?

function check_item($item)
{
	// echo 'part:'.$item."\n";
	if (trim($item)=='') return 0;
	// echo '0'."\n";	
	if (trim($item)=='0') return 0;
	if (preg_match('/\&{3,}/isu', $item)) return 0;// &&&
	if (preg_match('/\~{3,}/isu', $item)) return 0;// &&&
	if (preg_match('/\|{2,}/isu', $item)) return 0;// ||
	if (preg_match('/[\(\)]/isu',$item)) return 0;// ))) (((
	if (preg_match('/\&\s+\&/isu',$item)) return 0;// & &
	// echo '1'."\n";
	$mquot=explode('"',$item);
	// print_r($mquot);
	// echo (count($mquot)-1);
	if ((count($mquot)-1)%2==1) return 0;// "
	$item=preg_replace('/\s*([\&\|\~])+\s*/isu','$1',$item);
	// echo '2'."\n";
	$mitem=preg_split('/[\|]/isu', $item);
	// echo '3'."\n";
	// print_r($mitem);
	foreach ($mitem as $it)
	{
		// echo $it."!\n";
		$mit_split=preg_split('/[\&\~]+/isu', $it);
		// print_r($mit_split);
		// echo '!';
		// echo '4'."\n";
		if (trim($it)=='0') return 0;
		// echo '5'."\n";
		foreach ($mit_split as $it_split)
		{
			if (trim($it_split)=='') return 0;
			// echo '6'."\n";
			if (trim($it_split)=='0') return 0;
			// echo '7'."\n";
			// echo '***'.$it_split."\n";
			if (substr_count($it_split,'"')>2) return 0;
			if (substr_count($it_split,'"')==0)
			{
				if (preg_match('/[^а-яёa-z0-9\@\#\'\-\_\+\=\!\$\s\/\?\:\.\*]/isu',$it_split)) return 0;
				// echo 'gg';
			} 
			$microit_split=preg_split('/\s+/isu', $it_split);
			// print_r($microit_split);
			foreach ($microit_split as $microit_split_item)
			{
				if ($microit_split_item=='""') return 0;// ""
				if ($microit_split_item=='0') return 0;
				// echo '8'."\n";
			}
		}
	}
	return 1;
}

function check_null_bracket($query)//  ппп)  прпарпара
{
	if (preg_match('/\)\s*[а-яёa-z0-9\"]/isu', $query)) return 0;
	if (preg_match('/[а-яёa-z0-9\"]\s*\(/isu', $query)) return 0;
	return 1;
}


function check_query($query)
{
	// echo $query."\n";
	$query=preg_replace('/(\/\s*\(?[\+\-]\d+\s*[\+\-]\d+\)?|\/[\-\+]?\d+)/isu','&',$query);
	if (mb_strlen($query,'UTF-8')>1000) return 0;
	if (mb_strlen($query,'UTF-8')<3) return 0;
	if (substr_count($query,'"')%2==1) return 0;
	if (preg_match("/\n/isu", $query)) return 0;
	if (preg_match("/[а-яёa-z0-9]\(/isu", $query) || preg_match("/\)[а-яёa-z0-9]/isu", $query)) return 0;
	if (preg_match("/\)\(/isu", $query)) return 0;
	if (preg_match('/\)\s*\(/isu', $query)) return 0;
	if (preg_match('/\)\s*[а-яёa-z]/isu', $query)) return 0;
	if (preg_match('/[а-яёa-z]\s*\(/isu', $query)) return 0;
	// if (check_null_bracket($query)==0) return 0;
	$query=preg_replace('/\"([^\"]+?)\"/isu','"qwerty"',$query);
	do
	{
		$query_before=$query;
		$mcount_open=count(explode('(', $query))-1;
		$mcount_close=count(explode(')', $query))-1;
		$regex='/\(([^\(]*?)\)/isu';
		preg_match_all($regex, $query, $out, PREG_OFFSET_CAPTURE);
		// print_r($out);
		for ($i=count($out[1])-1;$i>=0;$i--)
		{
			$pos=mb_strpos($query,$out[1][$i][0],0,'UTF-8');
			$pos=($pos==0?$out[1][$i][1]-1:$pos);
			// echo '---'.$i.' |'.$out[1][$i][0].'| '.$out[1][$i][1]."\n";
			$c_item=check_item($out[1][$i][0]);
			// echo 'result:'.$c_item."\n";
			// echo '/(.{'.($pos-1).'})\('.preg_replace('/([^а-яёa-z0-9])/isu','\\\\$1',$out[1][$i][0]).'\)/isu'."\n";
			// echo 'query before:'.$query."\n";
			$query=preg_replace('/(.{'.($pos-1).'})\('.preg_replace('/([^а-яёa-z0-9])/isu','\\\\$1',$out[1][$i][0]).'\)/isu', '$1 '.$c_item, $query);
			// echo 'query after:'.$query."\n";
		}
		// echo 'QUERY:'.$query."\n";
		// print_r($out);
		//$query=preg_replace('/\([^\(]*?\)/isu','',$query);
		// echo $query."\n";
		// sleep(1);
	}
	while ((($mcount_open+$mcount_close)>=2)&&($query_before!=$query));
	return check_item($query);
}

?>