<?

/*
echo '<h1>POST vars:</h1>
<textarea>';
print_r($_POST);
echo '
</textarea>';

echo '<h1>GET vars:</h1>
<textarea>';
print_r($_GET);
echo '
</textarea>';

echo '<h1>COOKIE vars:</h1>
<textarea>';
print_r($_COOKIE);
echo '
</textarea>';

echo '<h1>SESSION vars:</h1>
<textarea>';
print_r($_SESSION);
echo '
</textarea>';
*/

$method=preg_replace('/[^A-Za-z0-9_-]+/', '', $_GET['method']);
$callback=preg_replace('/[^A-Za-z0-9_-]+/', '', $_GET['callback']);

if (file_exists('/var/www/api/0/'.$method.'.php'))
{
	header('Content-Type: text/javascript');
	echo $callback.'(';
	require_once('/var/www/api/0/'.$method.'.php');
	echo ');';
}
//echo $method;
?>
