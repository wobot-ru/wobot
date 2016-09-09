<?
header('Content-Type: application/json');
//print_r($_GET);
//die();
if ($_GET['s']=='deck/oauth/authorize/twitter')
{
	require_once('/var/www/social/twitter-client/index.php');
}
elseif ($_GET['s']=='deck/oauth/callback/twitter')
{
	require_once('/var/www/social/twitter-client/callback.php');
}
else
{
	//die();
	$mas_s=explode('.',$_GET['s']);
	if (file_exists($mas_s[0].'.php')) require_once($mas_s[0].'.php');
	else require_once('error.php');
}
?>
