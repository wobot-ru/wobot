#!/usr/bin/php
<?
require_once('/var/www/com/config.php');
require_once('/var/www/com/func.php');
require_once('/var/www/com/db.php');
require_once('/var/www/bot/kernel.php');

error_reporting(E_ERROR | E_WARNING | E_PARSE);
ignore_user_abort(true);
set_time_limit(0);
ini_set('max_execution_time',0);
ini_set('default_charset','utf-8');
ob_implicit_flush();

$db = new database();
$db->connect();

$ressec=$db->query('SELECT * FROM blog_orders2 WHERE (order_last<='.mktime(0,0,0,date("n"),date("j"),date("Y")).' or (order_last=0 and order_start<='.mktime(0,0,0,date("n"),date("j"),date("Y")).')) and (order_last<=order_end or order_end=0)');

//$ressec=$db->query('SELECT * FROM blog_orders');
echo 'new orders to parse: '.mysql_num_rows($ressec)."\n";
$mode='wb';
while($blog=$db->fetch($ressec))
{
$query = $blog['order_name'];
echo $blog['order_name'].' - '.$blog['order_id']."\n";

if ($blog['order_last']>=$blog['order_start'])
{
	if ($blog['order_last']!=0) $mstart=$blog['order_last'];
	else $mstart = $blog['order_start'];
}
else
{
	$mstart=$blog['order_start'];
}
if ($blog['order_end']>=mktime(0,0,0,date("n"),date("j"),date("Y")))
{
	$mend=mktime(0,0,0,date("n"),date("j")-1,date("Y"));
}
else
{
	if ($blog['order_end']!=0) $mend=$blog['order_end'];
	else $mend=mktime(0,0,0,date("n"),date("j")-1,date("Y"));
}
//for ($ddd=0;$ddd<30;$ddd++) parseday($query,$ddd,$ddd,0,0);
/*$blog['order_start']=mktime(0,0,0,date("m",$blog['order_start']),date("d",$blog['order_start']),date("Y",$blog['order_start']));
if ($blog['order_end']==0) $blog['order_end']=mktime(0,0,0,date("m"),date("d")-1,date("Y"));
$blog['order_end']=mktime(0,0,0,date("m",$blog['order_end']),date("d",$blog['order_end']),date("Y",$blog['order_end']));
if ($blog['order_last']==0) $blog['order_last']=$blog['order_start'];
else $mode='ab';
$blog['order_last']=mktime(0,0,0,date("m",$blog['order_last']),date("d",$blog['order_last']),date("Y",$blog['order_last']));
*/

for ($ddd=$mstart; $ddd<=$mend; $ddd=mktime(0,0,0,date("n",$ddd),date("j",$ddd)+1,date("Y",$ddd))) 
{
echo date("H:i:s d.m.Y",$ddd).' '.$blog['order_id']."\n";
}
}

?>