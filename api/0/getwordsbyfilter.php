<?

require_once('/var/www/new/com/config.php');
require_once('/var/www/new/com/func.php');
require_once('/var/www/new/com/db.php');
require_once('/var/www/new/com/porter.php');
require_once('/var/www/new/bot/kernel.php');
require_once('/var/www/cashjob/com/func_spec.php');
require_once('auth.php');
require_once('rfunc.php');
require_once( '/var/www/new/com/phpmorphy/src/common.php');

$dir = '/var/www/new/com/phpmorphy/dicts';
$lang = 'ru_RU';
$opts = array( 'storage' => PHPMORPHY_STORAGE_FILE );
$morphy = new phpMorphy($dir, $lang, $opts);

$db = new database();
$db->connect();

//$_POST=$_GET;

auth();
if (!$loged) die();
error_reporting(0);

$stem_word=new Lingua_Stem_Ru();

//$user['user_id']=204;
//$_POST['order_id']=737;
//$_POST['stime']='01.04.2012';
//$_POST['etime']='31.10.2012';
$_POST['order_id']=intval($_POST['order_id']);
$qorder=$db->query('SELECT * FROM blog_orders WHERE order_id='.$_POST['order_id'].' AND user_id='.$user['user_id'].' LIMIT 1');
if ($db->num_rows($qorder)==0)
{
	$outmas['status']='fail';
	echo json_encode($outmas);
	die();
}
$_POST['suborder_id']=intval($_POST['suborder_id']);
if ($_POST['suborder_id']!=0)
{
	$qsuborder=$db->query('SELECT * FROM blog_subthemes as a LEFT JOIN blog_orders as b ON a.order_id=b.order_id WHERE a.order_id='.$_POST['order_id'].' AND b.subtheme_id='.$_POST['suborder_id'].' LIMIT 1');
	if ($db->num_rows($qsuborder)==0)
	{
		$outmas['status']='fail';
		echo json_encode($outmas);
		die();
	}
}
//$_POST['response_type']='tag';

echo json_encode(getwordsbyfilter($_POST));
//echo json_encode(get_authors($_POST));
?>