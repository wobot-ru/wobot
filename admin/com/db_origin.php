<?
/*
=====================================================================================================================================================

    WOBOT 2010 (с) http://www.wobot.ru
    
    DB COOPERATION
    Developer:  Yudin Roman
    Description:
    Cooperation with databases.
    
    ВЗАИМОДЕЙСТВИЕ С БД
    Разработка: Юдин Роман
    Описание:
    Взаимодействие с базами данных.
    
=====================================================================================================================================================
*/

class database_origin
{
    function connect()
    {
        global $config_origin;
        $this->db_origin = mysql_connect($config_origin['db']['host'], $config_origin['db']['user'], $config_origin['db']['pass'])
        or die("база данных не доступна: " . mysql_error());
        mysql_query("SET character_set_results=utf8", $this->db_origin);
        mysql_query("SET character_set_client=utf8", $this->db_origin);
        mysql_query("SET character_set_connection=utf8", $this->db_origin);
        mb_language('uni');
        mb_internal_encoding('UTF-8');
        mysql_select_db($config_origin['db']['database'], $this->db_origin);
        mysql_query("set names 'utf8'",$this->db_origin);
    }

    function query($sql)
    {
        //$result = mysql_query($sql);
        //$this->result=$result;
        return mysql_query($sql,$this->db_origin);
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
        return @mysql_insert_id($this->db_origin);
    }

    function ping()
    {
        return mysql_ping($this->db_origin);
    }
}


if (!isset($_SESSION)) session_start();

?>
