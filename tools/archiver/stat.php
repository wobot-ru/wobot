<?
// echo exec('ps ax | grep "recover"');
$mrecover=explode("\n", shell_exec('ps ax | grep "recover.php"'));
// print_r($mrecover);
foreach ($mrecover as $item)
{
	// echo $item."\n";
	if (preg_match('/recover\.php\s\d+/is', $item))
	{
		$mitem=explode(' ', $item);
		echo $mitem[count($mitem)-1]." - восстановление<br>";
	}
}
// die();
// echo shell_exec('ps ax | grep "dumper.php"');
$mdumper=explode("\n", shell_exec('ps ax | grep "dumper.php"'));
foreach ($mdumper as $item)
{
	// echo $item;
	if (preg_match('/dumper\.php\s\d+/is', $item))
	{
		$mitem=explode(' ', $item);
		echo $mitem[count($mitem)-1]." - архивация<br>";
	}
}

?>