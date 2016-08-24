<?php
set_include_path("C:\\WebRoot\\htdocs\\library\\" . PATH_SEPARATOR . get_include_path());
require_once( 'Gecko/Router.php' );

$router = new Gecko_Router();
$router->dispatch();
?>