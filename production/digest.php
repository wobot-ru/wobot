<?

$filename = '/var/www/production/export/'.$_GET['token'].'_'.($_GET['order_id']).'.'.($_GET['type']==''?'xls':$_GET['type']);
$handle = fopen($filename, 'r');
$contents = fread($handle, filesize($filename));
fclose($handle);
header('Content-Type: text/xml, charset=windows-1251; encoding=windows-1251');
header('Expires: ' . gmdate('D, d M Y H:i:s') . ' GMT');
header("Accept-Ranges: bytes");
header("Content-Transfer-Encoding: binary");



// IE needs specific headers
$order=json_decode(file_get_contents('http://localhost/api/service/order.php?order_id='.$_GET['order_id']),true);
$file_name=preg_replace('/[^а-яА-Яa-zA-Z\-\_0-9]/isu','_',($order['order_name']!=''?$order['order_name']:$order['order_keyword']));
$file_name=preg_replace('/\_+/isu','_',$file_name);
if (mb_strlen($file_name,'UTF-8')>100)
{
	$file_name=mb_substr($file_name,0,100,'UTF-8');
}
$agent = $_SERVER['HTTP_USER_AGENT'];
if(eregi("msie", $agent) && !eregi("opera", $agent))
{
	if (!isset($_GET['name']))
	{
    	header('Content-Disposition: inline; filename="wobot_digest_'.$file_name.'_' . date('Y-m-d') . '.'.($_GET['type']==''?'xls':$_GET['type']).'"');
	}
	else
	{
    	header('Content-Disposition: inline; filename="'.$_GET['name'].'.'.($_GET['type']==''?'xls':$_GET['type']).'"');
	}
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Pragma: public');
}
else
{
	if (!isset($_GET['name']))
	{
    	header('Content-Disposition: attachment; filename="wobot_digest_'.$file_name.'_' . date('Y-m-d') . '.'.($_GET['type']==''?'xls':$_GET['type']).'"');
	}
	else
	{
    	header('Content-Disposition: attachment; filename="'.$_GET['name'].'.'.($_GET['type']==''?'xls':$_GET['type']).'"');
	}
    header('Pragma: no-cache');
}
echo $contents;

?>