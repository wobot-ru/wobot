<?
require_once('/var/www/userjob/com/config.php');
require_once('/var/www/userjob/com/func.php');
require_once('/var/www/userjob/com/db.php');
require_once('/var/www/userjob/bot/kernel.php');

$db = new database();
$db->connect();
$v=$db->query('UPDATE robot_location SET loc=\'Якутск\' , loc_coord=\'129.746398 62.039257\' WHERE id=51');
$v=$db->query('UPDATE robot_location SET loc=\'Ростовская область\' , loc_coord=\'40.806744 47.699256\' WHERE id=135');
$v=$db->query('UPDATE robot_location SET loc=\'Волгоград\' , loc_coord=\'44.515942 48.707793\' WHERE id=160');
$v=$db->query('UPDATE robot_location SET loc=\'Москва\' , loc_coord=\'37.609218 55.753559\' WHERE id=360');
$v=$db->query('UPDATE robot_location SET loc=\'USA\' , loc_coord=\'-95.854104 38.041216\' WHERE id=395');
$v=$db->query('UPDATE robot_location SET loc=\'Москва\' , loc_coord=\'37.609218 55.753559\' WHERE id=409');
$v=$db->query('UPDATE robot_location SET loc=\'Москва\' , loc_coord=\'37.609218 55.753559\' WHERE id=497');
$v=$db->query('UPDATE robot_location SET loc=\'Париж\' , loc_coord=\'2.406641 48.835499\' WHERE id=512');
$v=$db->query('UPDATE robot_location SET loc=\'Москва\' , loc_coord=\'37.609218 55.753559\' WHERE id=657');
$v=$db->query('UPDATE robot_location SET loc=\'Уфа\' , loc_coord=\'55.954481 54.727782\' WHERE id=658');
$v=$db->query('UPDATE robot_location SET loc=\'Красноярск\' , loc_coord=\'92.902725 56.045166\' WHERE id=842');
$v=$db->query('UPDATE robot_location SET loc=\'Москва\' , loc_coord=\'37.609218 55.753559\' WHERE id=847');
$v=$db->query('UPDATE robot_location SET loc=\'Санкт-Петербург\' , loc_coord=\'30.314287 59.853821\' WHERE id=880');
$v=$db->query('UPDATE robot_location SET loc=\'Санкт-Петербург\' , loc_coord=\'30.314287 59.853821\' WHERE id=895');
$v=$db->query('UPDATE robot_location SET loc=\'Москва\' , loc_coord=\'37.609218 55.753559\' WHERE id=1044');
$v=$db->query('UPDATE robot_location SET loc=\'Москва\' , loc_coord=\'37.609218 55.753559\' WHERE id=1170');
$v=$db->query('UPDATE robot_location SET loc=\'Москва\' , loc_coord=\'37.609218 55.753559\' WHERE id=1403');

?>
