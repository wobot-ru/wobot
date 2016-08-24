<?
/*
search.php?mode=results

search_keywords=keyword&search_terms=any&search_author=&search_forum=-1&search_time=0&search_fields=all&search_cat=-1&sort_by=0&sort_dir=DESC&show_results=posts&return_chars=-1
*/
$keyword='малыш';
$keyword=urlencode(iconv('utf-8','windows-1251',$keyword));
$url='http://2mm.ru/forum/search.php?mode=results';
$postvars='search_keywords='.$keyword.'&search_terms=any&search_author=&search_forum=-1&search_time=0&search_fields=all&search_cat=-1&sort_by=0&sort_dir=DESC&show_results=posts&return_chars=-1';
$ch = curl_init($url);
 curl_setopt($ch, CURLOPT_POST      ,1);
 curl_setopt($ch, CURLOPT_POSTFIELDS    ,$postvars);
 curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1); 
 curl_setopt($ch, CURLOPT_HEADER      ,0);  // DO NOT RETURN HTTP HEADERS 
 curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
 $html = curl_exec($ch);
echo $html;
?>
