<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<body>
<table width="100%" height="100%">
<tr><td>
<?
echo '
<form method="post">
Введите имя: <input type="text" name="query" value="'.$_POST['query'].'">
</form>
';
?>
</td></tr>
<tr><td valign="top">
<?

if (strlen($_POST['query'])>0)
{
$string = file_get_contents('https://graph.facebook.com/search?q='.urlencode($_POST['query']).'&type=user&access_token=AAACQNvcNtEgBABIlzfBUuhmNQHFZArVxW90QJa5vMfe2VmtkWZCPNqEojO9NL70adSpZA7rtGoHa6S6N7RCLqWchPqTmuIXEFGHPVWVNQZDZD');

$data=json_decode($string,true);
	foreach ($data['data'] as $item)
	{
		echo '<a href="http://www.facebook.com/'.$item['id'].'">'.$item['name'].'</a><br>';
	}
}

?>
</td></tr>
</table>
</body>
</head>
</html>