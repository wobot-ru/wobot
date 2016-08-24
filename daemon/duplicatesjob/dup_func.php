<?

function get_hashes($text)
{
	$mtext=preg_split('/[^а-яa-zё0-9]/isu', $text);
	foreach ($mtext as $word)
	{
		if (mb_strlen($word,'UTF-8')>4)
		{
			$mhashes[crc32($word)]=1;
			$mword[]=$word;
			if (count($mhashes)>15) break;
		}
	}
	// print_r($mhashes);
	// print_r($mword);
	return $mhashes;
}

function check_similar($hashes1,$hashes2)
{
	$ch1=count($hashes1);
	$ch2=count($hashes2);
	if ($ch1<$ch2) 
	{
		if ($ch2/$ch1>1.15) return 0;
		$min=$ch1;
	}
	else 
	{
		if ($ch1/$ch2>1.15) return 0;
		$min=$ch2;
	}
	return count(array_intersect_key($hashes1,$hashes2))/$min;
}

// print_r(get_hashes('База медицинских знаний Хеликс www.helix.ru'));

// echo check_similar(get_hashes(' База медицинских знаний Хеликс '),get_hashes('В медцентре Хеликс скидка 10% для льготников
// http://www.кингисепп-сегодня.рф/news/publication-385/'));
?>