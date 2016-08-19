<?
require_once('/var/www/com/checker.php');
echo check_query('((windows|винда|виндовс|виндоуз|виндоус)&(azure|ажур|облако|cloud|облачные)|hdinsight|hadooponazure|(azure && (хостинг | сайт | сервер | (big & data) | бэкенд | ( виртуальная & машина) | ( облачные & сервисы) | marketplace | mobile | sql | connect | (active && directory) | ad | "acs" | blob | nodejs | node.js | node | php | java )))~~антивирус~~breathe');
?>
