<?
/*
=====================================================================================================================================================

	WOBOT 2010 (с) http://www.wobot.ru
	
	DB COOPERATION
	Developer:	Yudin Roman
	Description:
	Cooperation with databases.
	
	ВЗАИМОДЕЙСТВИЕ С БД
	Разработка:	Юдин Роман
	Описание:
	Взаимодействие с базами данных.
	
=====================================================================================================================================================
*/

class database_bmstu
{
    function connect()
    {
        global $config_bmstu;
        $this->db = mysql_connect($config_bmstu['db']['host'], $config_bmstu['db']['user'], $config_bmstu['db']['pass'])
        or die("база данных не доступна: " . mysql_error());
        mysql_select_db($config_bmstu['db']['database'], $this->db);
    }

    function query($sql)
    {
        //$result = mysql_query($sql);
        //$this->result=$result;
        return mysql_query($sql);
    }

    function num_rows($res)
    {
        return @mysql_num_rows($res);
    }

    function fetch($res)
    {
        return @mysql_fetch_array($res, MYSQL_ASSOC);
    }

    function insert_id()
    {
        return @mysql_insert_id($this->db);
    }
}


if (!isset($_SESSION)) session_start();

?>
