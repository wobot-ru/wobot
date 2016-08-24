#!/usr/bin/php
<?
require_once('com/config.php');
require_once('com/func.php');
require_once('com/db.php');
require_once('bot/kernel.php');


$cont=parseUrl('http://starindex.deggustator.locum.ru/spider/includes/sys/userjob2/uj.php');
$fp = fopen('star_help.txt', 'a');
fwrite($fp, date('r',time()));
close($fp);

?>