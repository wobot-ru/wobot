<?

require_once('/var/www/com/config.php');
require_once('/var/www/com/db.php');


$db=new database();
$db->connect();

$qinf=$db->query('SELECT post_id FROM blog_post_prev');
$all=$db->num_rows($qinf);
$qinf2=$db->query('SELECT post_id FROM blog_post_prev WHERE post_engage!=-1');
$engage=$db->num_rows($qinf2);
$qinf3=$db->query('SELECT post_id FROM blog_post_prev WHERE post_ful_com!=\'\'');
$ful=$db->num_rows($qinf3);
$qinf4=$db->query('SELECT post_id FROM blog_post_prev WHERE post_ful_com!=\'\' AND post_engage!=-1');
$process=$db->num_rows($qinf4);

$out['all']=$all;
$out['engage']=$engage;
$out['ful']=$ful;
$out['process']=$process;

print_r($out);

$filename = "stat.txt";
$handle = fopen($filename, "a");
$contents = fwrite($handle, mktime(date('H'),date('i'),0,date('n'),date('j'),date('Y')).' '.json_encode($out)."\n");
fclose($handle);

// $filename = "/var/www/daemon/logs/la.log";
// $handle = fopen($filename, "a");
// $contents = fwrite($handle, json_encode($out)."\n");
// fclose($handle);

?>