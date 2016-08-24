<?php
require_once('/var/www/com/config.php');
require_once('/var/www/com/db.php');

    # Connect to memcache:
    //global $memcache, $db;

    # Gets key / value pair into memcache ... called by mysql_query_cache()
    function getCache($key) {
        global $memcache;
        return ($memcache) ? memcache_get($memcache,$key) : false;
    }

    # Puts key / value pair into memcache ... called by mysql_query_cache()
    function setCache($key,$object,$timeout = 180) {
        global $memcache;
        return ($memcache) ? memcache_set($memcache,$key,$object,MEMCACHE_COMPRESSED,$timeout) : false;
    }

    # Caching version of mysql_query()
    function mysql_query_cache($sql,$timeout = 180) {
		global $db;
        if (($cache = getCache(md5("mysql_query" . $sql))) == false) {
			echo "cachening\n";
            $cache = false;
            //$r = ($linkIdentifier !== false) ? mysql_query($sql,$linkIdentifier) : mysql_query($sql);
			$r = $db->query($sql);
            if (is_resource($r) && (($rows = mysql_num_rows($r)) !== 0)) {
	echo "cache rows: $rows\n";
                /*for ($i=0;$i<$rows;$i++) {
                    $fields = mysql_num_fields($r);
                    $row = mysql_fetch_array($r);
                    for ($j=0;$j<$fields;$j++) {
                        if ($i === 0) {
                            $columns[$j] = mysql_field_name($r,$j);
                        }
                        $cache[$i][$columns[$j]] = $row[$j];
                    }
                }*/
				$i=0;
				while($row=$db->fetch($r))
				{
					$cache[$i]=$row;
					$i++;
				}
                if (!setCache(md5("mysql_query" . $sql),$cache,$timeout)) {
                    # If we get here, there isn't a memcache daemon running or responding
                }
            }
			else echo "non res\n";
        }
        return $cache;
    }
?>

