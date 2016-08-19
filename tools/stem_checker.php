<?

require_once('/var/www/new/com/porter.php');

echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" /><form method="POST">
<input type="text" name="word">
<input type="submit" value="Отстемить">
</form>';
if ($_POST['word']!='')
{
	$word=new Lingua_Stem_Ru();
	$msg=$word->stem_word($_POST['word']);
	echo $_POST['word'].' -> '.$msg;
}
?>