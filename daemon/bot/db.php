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

class database
{
    function connect()
    {
        global $config;
        $this->db = mysql_connect($config['db']['host'], $config['db']['user'], $config['db']['pass'])
        or die("база данных не доступна: " . mysql_error());
        mysql_query("SET character_set_results=utf8", $this->db);
        mysql_query("SET character_set_client=utf8", $this->db);
        mysql_query("SET character_set_connection=utf8", $this->db);
        mb_language('uni');
        mb_internal_encoding('UTF-8');
        mysql_select_db('sentiment', $this->db);
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
