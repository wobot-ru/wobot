<?

$fp = fopen('/var/www/tools/input/data.txt', 'a');
fwrite($fp, date('r').' '.json_encode($_REQUEST).' '.json_encode($HTTP_RAW_POST_DATA)."\n");
fclose($fp);

?>
