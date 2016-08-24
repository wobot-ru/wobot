<?

require_once('/var/www/api/0/func_graph_data2.php');

require_once('/var/www/new/com/config.php');
require_once('/var/www/new/com/db.php');

$db = new database();
$db->connect();

error_reporting(E_ERROR);

//	Подгружаем $_GET, $_POST
	require_once('/var/www/api/0/func_export.php');

//	Подгружаем $wobot
	require_once('/var/www/new/com/func.php');

//	подрубаем Morphy 
	require_once( '/var/www/new/com/phpmorphy/src/common.php');

    $dir = '/var/www/new/com/phpmorphy/dicts';
    $lang = 'ru_RU';
    $opts = array( 'storage' => PHPMORPHY_STORAGE_FILE );
    $morphy = new phpMorphy($dir, $lang, $opts);

//global $_GET,$_POST,$wobot,$db,$user,$order,$word,$morphy;

if ($_POST['ytype']=='') die(json_encode(get_linear_data($_POST['order_id'],strtotime($_POST['start']),strtotime($_POST['end']),$_POST['xtype'])));
elseif ($_POST['separator']=='') die(json_encode(get_stack_data($_POST['order_id'],strtotime($_POST['start']),strtotime($_POST['end']),$_POST['xtype'],$_POST['ytype'])));
else die(json_encode(get_stack_separator_data($_POST['order_id'],strtotime($_POST['start']),strtotime($_POST['end']),$_POST['xtype'],$_POST['ytype'],$_POST['separator'])));
// get_new_graph_data($_POST['order_id'],$_POST['xtype'],$_POST['ytype'],$_POST['separator']);


?>
