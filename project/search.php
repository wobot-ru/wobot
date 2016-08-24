<?


$filename = "xz.log";
$handle = fopen($filename, "r");
$html = fread($handle, filesize($filename));
fclose($handle);
echo mb_strpos($html,'Извините, сервис временно недоступен.',0,'UTF-8');

?>
