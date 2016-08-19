<?

function replace_link($text)
{
	$regex='@\b(([\w-]+://?|www[.])[^\s()<>]+(?:\([\w\d]+\)|([^[:punct:]\s]|/)))@';
	preg_match_all($regex, $text, $out);
	foreach ($out[0] as $key => $item)
	{
		$item_temp=$item;
		$item=preg_replace('/([\.])/isu','***point***',$item);
		$item=preg_replace('/([\?])/isu','***quest***',$item);
		//echo $item."\n";
		//echo '/'.preg_replace('/([^а-яёa-z0-9])/isu','\\\\$1',$item_temp).'/isu'."\n";
		$text=preg_replace('/'.preg_replace('/([^а-яёa-z0-9])/isu','\\\\$1',$item_temp).'/isu',$item,$text,1);
		// echo $text."\n\n\n";
	}
	return $text;
}

// echo replace_link($text);

function split_sentences($text)
{
	// echo $text."\n";
	$text=replace_link($text);
	$text=preg_replace('/(\s[a-z]+)(\.)(ru|com|net|az|en|gov|org|info|ру)([\s\,])/isu', '$1***point***$3$4', $text);
	$text=preg_replace('/([^а-яёa-z])([а-яёa-z])(\.)([а-яёa-z])(\.)/isu','$1$2***point***$4***point***',$text);
	$text=preg_replace('/([^а-яёa-z])([а-яёa-z])(\.)/isu','$1$2***point***',$text);
	$text=preg_replace('/(«[^«]*?)\!([^«]*?»)/isu','$1***voskl***$2',$text);
	$text=preg_replace('/(«[^«]*?)\.([^«]*?»)/isu','$1***point***$2',$text);
	$text=preg_replace('/(«[^«]*?)\?([^«]*?»)/isu','$1***point***$2',$text);
	$text=preg_replace('/(\!|\?)([^\s])/isu', '$1 $2',$text);
	// echo $text."\n";
	//$text=preg_replace('/(\s[^\@\:\/]+)(\.)([^r][^u]|[^c][^o][^m]|[^n][^e][^t])/isu', '$1$2 $3',$text);
	$text=preg_replace('/([\s\-\:\,])([а-яёa-z]+)(\.)([а-яёa-z]+)([\s\-\:\,])/isu','$1$2$3 $4$5',$text);
	// echo $text;
	$result = preg_split('/(?<=[.?!;])\s+/', $text, -1, PREG_SPLIT_NO_EMPTY);
	foreach ($result as $key => $item)
	{
		$item=trim(preg_replace('/\*\*\*point\*\*\*/isu','.',$item));
		$item=trim(preg_replace('/\*\*\*voskl\*\*\*/isu','!',$item));
		$item=trim(preg_replace('/\*\*\*quest\*\*\*/isu','?',$item));
		$outmas[]=$item;
		// $outmas[]=trim(preg_replace('/\*\*\*quest\*\*\*/isu','?',$item));
	}
	return $outmas;
}

?>