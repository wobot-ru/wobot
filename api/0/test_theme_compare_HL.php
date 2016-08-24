<?
require_once('/var/www/api/0/func_graph_data_testing.php');
require_once('/var/www/new/com/config.php');
require_once('/var/www/new/com/db.php');
require_once('/var/www/new/com/func.php');
$db = new database();
$db->connect();
//error_reporting(E_ERROR);
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
# Присваиваю массиву тестового запроса переменные
//global $_GET,$_POST,$wobot,$db,$user,$order,$word,$morphy;
$_POST['page']= '0';
$_POST['sort']= 'null';
$_POST['positive']= 'true';
$_POST['negative']= 'true';
$_POST['neutral']= 'true';
$_POST['post_type']= 'null';
$_POST['md5']= '';
$_POST['perpage']= 'null';
$_POST['Promotions']= 'selected';
$_POST['words']='selected';
$_POST['tags']= 'selected';
$_POST['location']= '';
$_POST['cou']= '';
$_POST['locations']= 'selected';
$_POST['res']= '';
$_POST['shres']= '';
$_POST['hosts']= 'selected';
$_POST['start']= '25.04.2014';
//$_POST['stime']= '10.03.2014';
$_POST['end']= '10.05.2014';
//$_POST['etime']= '16.05.2014';
$_POST['blog_location']= 'post_count';
$_POST['order_ids']='6906,6914';

get_new_graph_data($_POST['order_id'],$_POST['xtype'],$_POST['ytype'],$_POST['separator']);

?>