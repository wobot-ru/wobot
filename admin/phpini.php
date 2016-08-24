<?php
//phpinfo();

$date=date('d-m-Y H:i:s'); 
echo $date;
echo('<br>');

echo date("d.m.Y H:i:s", time());
echo('<br>');

echo date("d.m.Y H:i:s e O");
echo('<br>');

echo date_default_timezone_get();
?>