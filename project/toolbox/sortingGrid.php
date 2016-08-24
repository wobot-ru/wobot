<?php
require("_common.php");

$db = Zend_Db::factory('Pdo_Mysql', $dbSettings);
$query = "SELECT * FROM `users`";
$model = new Gecko_DataSource_Table_SQL($query, $db);

$settings = array(
	"sorting" => array(
		"sortColumn" => "id",
		"sortOrder" => "ASC"
	)
);

$grid = new Gecko_DataGrid("users", $model, $settings);
$grid->buildTable();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
   "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
	<head>
		<title>Grid Test Sorting</title>
	</head>
	<body>
		<?php echo $grid; ?>
		<a href="grid.html">Grid Tests</a>
	</body>
</html>