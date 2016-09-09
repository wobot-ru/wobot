<?

require_once('/var/www/new/com/config.php');
require_once('/var/www/new/com/func.php');
require_once('/var/www/new/com/db.php');
require_once('/var/www/new/bot/kernel.php');
require_once('auth.php');

$db = new database();
$db->connect();

auth();
if (!$loged) die();
//$_GET=$_POST;
/*{
	$mas['status']='fail';
	echo json_encode($mas);	
	die();
}
if ((intval($_GET['order_id'])==0) || (intval($_GET['id'])==0))
{
	$mas['status']='fail';
	echo json_encode($mas);	
	die();
}*/

if ($_POST['kw']!='')
{
	$keyword=preg_replace('/\~+[а-яА-Яa-zA-Z\-\ \,\.]+/isu','',$_POST['kw']);
	$keyword=preg_replace('/[^а-яА-Яa-zA-Z0-9\ \'\-\’\.]/isu','  ',$keyword);
	$mkeyword=explode('  ',$keyword);
	//print_r($mkeyword);
	foreach ($mkeyword as $item)
	{
		$qw.=$or.' order_keyword LIKE \'%'.$item.'%\'';
		$or=' OR ';
	}
	$query='SELECT order_keyword FROM blog_orders WHERE '.$qw;
	//echo $query;
	$qokw=$db->query($query);
	while ($okw=$db->fetch($qokw))
	{
		$kw=preg_replace('/\~+[а-яА-Яa-zA-Z\-\ \,\.]+/isu','',$okw['order_keyword']);
		$kw=preg_replace('/[^а-яА-Яa-zA-Z0-9\ \'\-\’\.]/isu','  ',$kw);
		$mkw=explode('  ',$kw);
		foreach ($mkw as $kkw)
		{
			if ((trim($kkw)=='') || (count($outkw)>20)) continue;
			if (!isset($assockw[trim($kkw)]))
			$assockw[trim($kkw)]=1;
			$outkw[]=trim($kkw);
		}
	}
	echo json_encode($outkw);
	//print_r($assockw);
}
else
{
	$outmas['status']=1;
	echo json_encode($outmas);
}

?>