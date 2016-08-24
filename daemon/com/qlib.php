<?

function get_simple_word($query)
{
	$query=preg_replace('/(\~+\([^\(]*?\))/isu', '', $query);
	$query=preg_replace('/(\~+[^\~\|\&]*)/isu', '', $query);
	$query=preg_replace('/(\/)(\()([\s\d\-\+]+)(\))/isu','',$query);
	do
	{
		$count_open=count(explode('(',$query))-1;
		$count_close=count(explode(')',$query))-1;
		// echo "\n".$query."\n";
		$regex='/\((?<kw>[^\(]*?)\)/isu';
		preg_match_all($regex, $query, $out);
		foreach ($out['kw'] as $item)
		{
			$mor=explode('|', $item);
			foreach ($mor as $item_or)
			{
				// echo $item_or.'|';
				$words=trim(preg_replace('/\&+/isu',' ',$item_or));
				$words=preg_replace('/[\s\t]+/isu',' ',$words);
				$words=preg_replace('/\"/isu','',$words);
				if ((($words)=='')||(in_array($words, $outword))||preg_match('/[\~\(\)]/isu', $words)) continue;
				$outword[]=$words;
			}
		}
		$query=preg_replace('/(\([^\(]*?\))/isu','',$query);
		// echo $query."\n";
	}
	while (($count_open!=0)&&($count_close!=0));
	$mor=explode('|', $query);
	foreach ($mor as $item_or)
	{
		$words=trim(preg_replace('/\&+/isu',' ',$item_or));
		$words=preg_replace('/[\s\t]+/isu',' ',$words);
		$words=preg_replace('/\"/isu','',$words);
		if ((($words)=='')||(in_array($words, $outword))||preg_match('/[\~\(\)]/isu', $words)) continue;
		$outword[]=$words;
	}
	// print_r($outword);
	return $outword;
}

function construct_sql_query($query,$field)
{
	$kw['order_keyword']=$query;
	$query=trim($kw['order_keyword']);
	$query=preg_replace('/(\~+\([^\(]*?\))/isu', '', $query);
	$query=preg_replace('/(\~+[^\~\|\&]*)/isu', '', $query);
	$query=preg_replace('/(\/)(\()([\s\d\-\+]+)(\))/isu','',$query);
	$kw['order_keyword']=trim($query);
	$query=preg_replace('/\~+[а-яА-Яa-zA-Z\-\ \.\'\"]+/isu','',$query);
	$query=preg_replace('/[^а-яА-Яa-zA-Z0-9\-\'\’\ \.]/isu','  ',$query);
	$mkw=explode('  ',$query);
	// print_r($mkw);
	$kw['order_keyword']=trim($kw['order_keyword']);
	// echo '|'.$kw['order_keyword']."|\n";
	// print_r($mkw);
	unset($yetmkw);
	foreach ($mkw as $item)
	{
		$item=trim($item);
		if (($item=='')||(isset($yetmkw[$item]))) continue;
		$yetmkw[$item]=1;
		// print_r($yetmkw);
		// echo $kw['order_keyword']."\n";
		// echo '/('.$item.')/isu'."\n";
		$kw['order_keyword']=preg_replace('/([^%а-я])('.$item.')([^%а-я])/isu', '$1 '.$field.' LIKE \'%$2%\' $3', ' '.$kw['order_keyword'].' ');
		// echo $kw['order_keyword']."\n";
	}
	$kw['order_keyword']=preg_replace('/\|/isu', ' OR ', $kw['order_keyword']);
	$kw['order_keyword']=preg_replace('/\&+/isu', ' AND ', $kw['order_keyword']);
	$kw['order_keyword']=preg_replace('/\"/isu', ' ', $kw['order_keyword']);
	$kw['order_keyword']=preg_replace('/[\s\t]+/isu', ' ', $kw['order_keyword']);
	return $kw['order_keyword'];
}

function get_simple_query($query,$src)// построение простых запросов из сложного
{
	$query=preg_replace('/(\~+\s*\([^\(]*?\))/isu', '', $query);
	$query=preg_replace('/(\~+\s*[^\~\|\&]*)/isu', '', $query);
	$query=preg_replace('/(\/\s*\{?[\+\-]\d+\s*[\+\-]\d+\}?|\/\s*[\-\+]?\d+)/isu',' ',$query);
	// echo $query."\n";
	do
	{
		// echo $query."\n";
		$query=preg_replace('/(\([\s\&\|]*\))/isu', '', $query);
		$count_open=count(explode('(',$query))-1;
		$count_close=count(explode(')',$query))-1;
		$regex='/\((?<kw>[^\(]*?)\)/isu';
		preg_match_all($regex, $query, $out);
		// print_r($out);
		foreach ($out['kw'] as $key => $item)
		{
			if ((trim($item)=='')&&(count($out['kw'])==1)) 
			{
				$c=1;
				continue;
			}
			$query=preg_replace('/'.preg_replace('/([^a-zа-яё0-9])/isu','\\\\$1',$out[0][$key]).'/isu', ' ', $query);
			$rep_op=operand_replace($item,$src);
			if (trim($rep_op)!='') $out_simple_query[]=$rep_op;
			// $out_simple_query[]=operand_replace($item,$src);
		}
		if ($c==1) break;
	}	
	while (($count_open!=0)&&($count_close!=0));
	$rep_op=operand_replace($query,$src);
	if ((trim($query)!='')&&(trim($rep_op)!='')) $out_simple_query[]=$rep_op;
	if ($src=='twitter') $simple_queries=check_sq_twitter($out_simple_query);
	elseif ($src=='vk') $simple_queries=check_sq_vk($out_simple_query);
	// print_r($simple_queries);
	return $simple_queries;
}

function operand_replace($query,$src)// замена операторов для каждого ресурса
{
	if ($src=='twitter')
	{
		$mpart=explode('|', $query);
		foreach ($mpart as $item)
		{
			if (trim($item)=='') continue;
			$out_query.=$or.preg_replace('/(\s*\&+\s*)/isu',' ',$item);
			$or=' OR ';
		}
	}
	elseif ($src=='vk')
	{
		$mpart=explode('|', $query);
		foreach ($mpart as $item)
		{
			if (trim($item)=='') continue;
			$out_query.=$or.preg_replace('/(\s*\&+\s*)/isu',' ',$item);
			$or=' | ';
		}
	}
	if (!preg_match('/[а-яa-zё]/isu', $out_query)) return '';
	return preg_replace('/\s+/isu',' ',$out_query);
}

function check_sq_twitter($mq)// проверка и очистка от мусора готовых запросов
{
	// print_r($mq);
	foreach ($mq as $q)
	{
		$oldq=$q;
		if (mb_strlen(urlencode($q),'UTF-8')>900)
		{
			$msq=preg_split('/OR/isu', $q);
			// print_r($msq);
			foreach ($msq as $lsq)
			{
				// echo $lsq."\n";
				if (mb_strlen(urlencode($query_part),'UTF-8')<=900) 
				{
					$query_part.=$or.'('.trim($lsq).')';
					$or=' OR ';
				}
				else
				{
					$outq[]=$query_part.$or.'('.trim($lsq).')';
					$query_part='';
					$or='';
				}
			}
			$outq[]=$query_part;
		}
		else
		{
			$q=preg_replace('/OR/isu','',$q);
			if (preg_match('/[а-яa-zё]/isu', $q)) $outq[]='('.preg_replace('/\s*OR\s*/isu',') OR (',trim($oldq)).')';
		}
	}
	return $outq;
}

function check_sq_vk($mq)// проверка и очистка от мусора готовых запросов
{
	// print_r($mq);
	foreach ($mq as $q)
	{
		$oldq=$q;
		if (trim($q)=='') continue;
		if (mb_strlen(urlencode($q),'UTF-8')>900)
		{
			$msq=preg_split('/\|/isu', $q);
			// print_r($msq);
			foreach ($msq as $lsq)
			{
				// echo $lsq."\n";
				if (mb_strlen(urlencode($query_part),'UTF-8')<=900) 
				{
					$query_part.=$or.trim($lsq);
					$or=' | ';
				}
				else
				{
					$outq[]=$query_part.$or.trim($lsq);
					$query_part='';
					$or='';
				}
			}
			$outq[]=$query_part;
		}
		else
		{
			$q=preg_replace('/OR/isu','',$q);
			if (preg_match('/[а-яa-zё]/isu', $q)) $outq[]=preg_replace('/\s*OR\s*/isu',') OR (',trim($oldq));
		}
	}
	return $outq;
}

?>