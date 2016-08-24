<?
/*
=====================================================================================================================================================

	WOBOT 2010 (с) http://www.wobot.ru
	
	MAIN ROUTING FILE
	Developer:	Yudin Roman
	Description:
	Web frontend's main routing file. Adapted for ajax-based interface (using jquery).
	
	ОСНОВНОЙ ФАЙЛ РОУТИНГА
	Разработка:	Юдин Роман
	Описание:
	Файл взаимодействия модулей веб-фронтенда. Адаптирован с учетом использования ajax (с использованием jquery).
	
=====================================================================================================================================================
 */
//die('Achtung!!! Left-wing terrorist added to FBI\'s most wanted terrorist list!');
//die('Извините, сервис временно не доступен.');

// Turn off all error reporting
error_reporting(0);

require_once('com/config.php');
require_once('com/func.php');
require_once('com/db.php');
require_once('bot/kernel.php');
require_once('com/auth.php');
require_once('tpl/main.php');

$db = new database();
$db->connect();

//print_r($_GET);
if (!isset($_POST['frm'])) {

if ($config['debug']) echo $_GET['s'].'<br>';

$path = explode('/' , $_GET['s']);
if ($path[0]!='rss') auth();
$module = array_shift($path);

if (file_exists('modules/'.$_GET['s'].'.php')) require_once 'modules/'.$_GET['s'].'.php';
else require_once 'modules/admin2.php';



//if (file_exists('modules/'.$module.'.php')) require_once 'modules/'.$module.'.php';
//elseif (file_exists('modules/'.$module.'/admin2.php')) require_once 'modules/'.$module.'/main.php';
//else require_once 'modules/'.$config['default_module'].'.php';

}
else @require_once('modules/'.$_POST['frm'].'/post.php');
?>
