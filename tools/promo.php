<?
require_once('/var/www/com/db.php');
require_once('/var/www/com/config.php');


echo '    <head><link rel="stylesheet" href="http://code.jquery.com/ui/1.9.1/themes/base/jquery-ui.css" />
    <script src="http://code.jquery.com/jquery-1.8.2.js"></script>
    <script src="http://code.jquery.com/ui/1.9.1/jquery-ui.js"></script>
        <script>
    $(function() {
        $( "#datepicker" ).datepicker();
    });
    $(function() {
        $( "#datepicker1" ).datepicker();
    });
    </script>
    </head>
    ';

$db=new database();
$db->connect();

$code_type[0]='анулированный';
$code_type[1]='многоразовый';
$code_type[2]='одноразовый';

if (intval($_GET['del'])!=0)
{
	$db->query('DELETE FROM user_promo WHERE code_id='.intval($_GET['del']));
}

if (intval($_GET['null'])!=0)
{
	$db->query('UPDATE user_promo SET code_type=0 WHERE code_id='.intval($_GET['null']));
}

if (intval($_GET['single'])!=0)
{
	$db->query('UPDATE user_promo SET code_type=2 WHERE code_id='.intval($_GET['single']));
}

if (intval($_GET['multi'])!=0)
{
	$db->query('UPDATE user_promo SET code_type=1 WHERE code_id='.intval($_GET['multi']));
}

if ($_POST['act']=='add')
{
	$db->query('INSERT INTO user_promo (code_value,code_exp,code_billing,code_count,code_type) VALUES (\''.addslashes($_POST['name']).'\','.strtotime($_POST['exp']).','.$_POST['type'].','.$_POST['count'].','.$_POST['multiple'].')');
}

if ($_POST['act']=='edit')
{
	$db->query('UPDATE user_promo SET code_value=\''.addslashes($_POST['name']).'\',code_exp='.strtotime($_POST['exp']).',code_billing='.$_POST['type'].',code_count='.$_POST['count'].',code_type='.$_POST['multiple'].' WHERE code_id='.$_GET['edit']);
}

echo '<meta content="text/html; charset=utf-8" http-equiv="Content-Type">';
echo '<table border="1"><tr><td>Редактирование</td><td>Код</td><td>Время истечения</td><td>Количество</td><td>Тип</td><td>Многоразовость</td><td>0</td><td>X</td></tr>';
$qpromo=$db->query('SELECT * FROM user_promo');
while ($promo=$db->fetch($qpromo))
{
	if ($_GET['edit']==$promo['code_id']) $editprom=$promo;
	echo '<tr><td><a href="?edit='.$promo['code_id'].'">Редактировать</a></td><td>'.$promo['code_value'].'</td><td>'.date('n.j.Y',$promo['code_exp']).'</td><td>'.$promo['code_count'].'</td><td>'.($promo['code_billing']==1?'время':'сообщения').'</td><td>'.$code_type[$promo['code_type']].'</td><td><a href="?null='.$promo['code_id'].'">Анулировать</a> <a href="?single='.$promo['code_id'].'">Одноразовый</a> <a href="?multi='.$promo['code_id'].'">Многоразовый</a></td><td><a href="?del='.$promo['code_id'].'" onclick="return confirm(\'Удалить промокод?\')&&confirm(\'Точно удалить промокод?\')&&confirm(\'Ну смари я тебя предупреждал!\')">Удалить</a></td></tr>';
}
echo '</table>';

if (intval($_GET['edit'])!=0)
echo '<br><hr>Редактировать промокод:<br>
	<form method="POST">
		<input type="hidden" name="act" value="edit">
		Название: <input type="text" name="name" value="'.$editprom['code_value'].'"><br>
		Время истечения: <input type="text" name="exp" id="datepicker" value="'.date('n.j.Y',$editprom['code_exp']).'" /><br>
		Количество: <input type="text" name="count" value="'.$editprom['code_count'].'" /><br>
		Тип: 
		<select name="type">
  			<option value="1" '.($editprom['code_billing']==1?'selected':'').'>Время</option>
  			<option value="2" '.($editprom['code_billing']==2?'selected':'').'>Сообщения</option>
		</select><br>
		Многоразовость: 
		<select name="multiple">
  			<option value="0" '.($editprom['code_type']==0?'selected':'').'>Анулированный</option>
  			<option value="1" '.($editprom['code_type']==1?'selected':'').'>Многоразовый</option>
  			<option value="2" '.($editprom['code_type']==2?'selected':'').'>Одноразовый</option>
		</select><br>
		<input type="submit" value="Редактировать">
	</form>
	';

echo '<br><hr>Добавить промокод:<br>
	<form method="POST">
		<input type="hidden" name="act" value="add">
		Название: <input type="text" name="name" value=""><br>
		Время истечения: <input type="text" name="exp" id="datepicker1" value="'.date('n').'/'.date('j').'/'.date('Y').'" /><br>
		Количество: <input type="text" name="count" value="0" /><br>
		Тип: 
		<select name="type">
  			<option value="1">Время</option>
  			<option value="2">Сообщения</option>
		</select><br>
		Многоразовость: 
		<select name="multiple">
  			<option value="0">Анулированный</option>
  			<option value="1">Многоразовый</option>
  			<option value="2">Одноразовый</option>
		</select><br>
		<input type="submit" value="Добавить">
	</form>
	';

?>