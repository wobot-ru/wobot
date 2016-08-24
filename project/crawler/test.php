<?
include('kernel.php');

/*
test link:
http://www.volchat.ru/forum/

TODO:
- robots.txt
- links rules by regexp
- garbage cleaner: head and foot
- codepage detector
- date detector
- language detector

- s3 adapter & putter by date
*/

//deleting html tag and information inside
function deletetag($data, $tagname)
{
	$data = preg_replace('/<'.$tagname.'[^>]*>(.*?)<\/'.$tagname.'>/is', '', $data);
	return $data;
}

//deleting html tag only
function striptag($data, $tagname)
{
	$data = preg_replace('/<'.$tagname.'[^>]*>(.*?)<\/'.$tagname.'>/is', '$1', $data);
	return $data;
}

//mark html tag
function marktag($data, $tagname)
{
	$data = preg_replace('/<'.$tagname.'[^>]*>(.*?)<\/'.$tagname.'>/is', '|$1|', $data);
	return $data;
}

$data = iconv('windows-1251','utf-8',parseurl('http://www.volchat.ru/forum/viewtopic.php?t=2842'));

// Select only between <body> and </body>
// another text deleting
preg_match('/<body[^>]*>(.*)<\/body>/is', $data, $matches);
$data = $matches[1];
unset($matches);

$data=deletetag($data,'noindex');
$data=deletetag($data,'script');

/*$data=marktag($data,'table');
$data=marktag($data,'th');
$data=marktag($data,'tr');
$data=marktag($data,'td');
$data=marktag($data,'div');
$data=marktag($data,'p');

$data=strip_tags($data);

$data = preg_replace('/[ ]+/is', ' ', $data);
$data = str_replace("\n", '', $data);
*/
echo $data;

?>
