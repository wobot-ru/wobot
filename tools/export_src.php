<?

require_once('/var/www/com/db.php');
require_once('/var/www/com/config.php');

$db=new database();
$db->connect();

$export='Ресурс;Моложе недели(0/1)'."\n";
$qsrc=$db->query('SELECT * FROM blog_src');
while ($src=$db->fetch($qsrc))
{
	$export.=$src['src_host'].';'.($src['src_time']<time()-86400*7?0:1)."\n";
}

header("Content-type: text/csv");
header("Content-Disposition: attachment; filename=wobotsrc_".time().".csv");
header("Pragma: no-cache");
header("Expires: 0");
echo iconv('UTF-8','windows-1251',$export);

?>