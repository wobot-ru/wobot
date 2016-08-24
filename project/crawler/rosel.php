<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
	<html>
	  <head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<title>РОСЕЛЬТОРГ &copy;</title>
	  </head>
	  <body>
<?
include('kernel.php');

$cont=parseurl('http://etp.roseltorg.ru/trade/suppliers-registry/?q=&inn=&kpp=&dateaccredit_from='.date('d.m.Y', time()-60*60*24).'&dateaccredit_to='.date('d.m.Y', time()-60*60*24).'&toaccredit_from=&toaccredit_to=&page=1&limit=100');
//(?<basel>.*?)
$regexbase='/<tr class="new_str double_row row_.*? clickable" onclick=".*?">.*?<td>(?<name>.*?)<\/td>.*?<td>.*?<\/td>.*?<td>.*?<\/td>.*?<td>(?<type>.*?)<\/td>.*?<td>(?<date1>.*?)<\/td>.*?<td class="rigt_text">(?<date2>.*?)<\/td>.*?<\/tr>/is';
preg_match_all($regexbase,$cont,$outbase);
foreach ($outbase['name'] as $name)
{
	echo $name.'<br>
	';
}

?>
	</body>
</html>