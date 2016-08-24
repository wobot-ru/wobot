<?

$mas_s=explode('.',$_GET['s']);
if (file_exists($mas_s[0].'.php')) require_once($mas_s[0].'.php');
else require_once('error.php');
?>
