<?php
require("_common.php");

$db = Zend_Db::factory('Pdo_Mysql', $dbSettings);
$query = "SELECT * FROM `users`";
Zend_Registry::set("db", $db);
$grid = Gecko_DataGrid::createFromSQL("users", $query);
$grid->buildTable();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
   "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
	<head>
		<title>Grid Test</title>
	</head>
	<body>
		<?php echo $grid; ?>
		<a href="grid.html">Grid Tests</a>
	</body>
</html>