<?
require_once('/var/www/com/config.php');
require_once('/var/www/com/func.php');
require_once('/var/www/com/db.php');
require_once('/var/www/bot/kernel.php');
//print_r($_GET);
if ($_GET['rss']!='')
{
	print_r($_GET);
	$db = new database();
	$db->connect();
	$db->query('INSERT INTO azure_rss (rss_id,rss_link,attempts,rss_type) VALUES (NULL,\''.addslashes($_GET['rss']).'\',0,\''.$_GET['ln'].'\')');
}
echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
	<html>
	  <head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<title>Добавление RSS-лент Wobot.RSS</title>
	  </head>
	  <body background="img.jpg">
	  <center>
	  <table align="center" width="100%" height="100%">
	  <td>
	  <form action="http://bmstu.wobot.ru/tools/addrss.php" method="GET" style="margin: 50px;" onsubmit="return confirm(\'Вы точно провели варидацию ссылки W3C RSS?\');">
	  <input type="text" name="rss" style="width: 300px; padding: 10px; margin: 10px;"/>
	<select name="ln">
	  <option value="ru" checked>ru</option>
	  <option value="en">en</option>
	  <option value="az">az</option>
	</select>
	  <input type="submit" value="Добавить ленту"/>
	  </form>
	  <div>
	  Необходимо довавлять валидную ссылку, проверить которую можно по ссылке: <a href="http://validator.w3.org/feed/">W3C RSS Validation</a><br>
	  Добавленная ссылка добавляется в базу сбора RSS-лент в Azure (в следующую ночь), для real-time сбора информации и пополнения выдачи.<br>
	  На одном форуме может быть несколько RSS-лент.
	  </div>
	  </td>
	  </table>
	  </center>
	  </body>
    </html>
';

?>