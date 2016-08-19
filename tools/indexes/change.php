<?

require_once('/var/www/com/db.php');
require_once('/var/www/com/config.php');

$db=new database();
$db->connect();

echo '<b><a href="http://188.120.239.225/tools/indexes/change.php">add new weight</a></b><br><br>';

if ($_POST['act']=='add')
{
	$qisset=$db->query('SELECT * FROM blog_host_weight WHERE order_id='.$_POST['order_id'].' AND host_name=\''.addslashes($_POST['host']).'\' LIMIT 1');
	if ($db->num_rows($qisset)==0) $db->query('INSERT INTO blog_host_weight (order_id,host_name,host_weight) VALUES ('.$_POST['order_id'].',\''.addslashes($_POST['host']).'\','.intval($_POST['value']).')');
}
elseif ($_POST['act']=='edit')
{
	$db->query('UPDATE blog_host_weight SET order_id='.$_POST['order_id'].',host_name=\''.addslashes($_POST['host']).'\',host_weight='.$_POST['value'].' WHERE host_id='.$_POST['host_id']);
}
elseif ($_GET['del_id']!='')
{
	$db->query('DELETE FROM blog_host_weight WHERE host_id='.intval($_GET['del_id']));
}

if ($_GET['edit_id']!='')
{
	$qhost=$db->query('SELECT * FROM blog_host_weight WHERE host_id='.$_GET['edit_id'].' LIMIT 1');
	$host=$db->fetch($qhost);
	echo '<form method="POST">
	<input type="hidden" name="act" value="edit">
	<input type="hidden" name="host_id" value="'.$host['host_id'].'">
	order_id: <input type="text" name="order_id" value="'.$host['order_id'].'"><br>
	host: <input type="text" name="host" value="'.$host['host_name'].'"><br>
	value: <input type="text" name="value" value="'.$host['host_weight'].'"><br>
	<input type="submit" value="change"><br>
	</form>';
}
else
{
	echo '<form method="POST">
	<input type="hidden" name="act" value="add">
	order_id: <input type="text" name="order_id"><br>
	host: <input type="text" name="host"><br>
	value: <input type="text" name="value"><br>
	<input type="submit" value="add"><br>
	</form>';	
}

echo '<table border="1"><tr><td>order_id</td><td>host_name</td><td>weight</td><td>action</td></tr>';
$qweight=$db->query('SELECT * FROM blog_host_weight');
while ($weight=$db->fetch($qweight))
{
	echo '<tr><td>'.$weight['order_id'].'</td><td>'.$weight['host_name'].'</td><td>'.$weight['host_weight'].'</td><td><a href="?edit_id='.$weight['host_id'].'">edit</a> <a href="?del_id='.$weight['host_id'].'">delete</a></td></tr>';
}
echo '</table>';

?>