<?php
 
$order_id = 1614;
$root = '/var/www/daemon/';
require_once($root . 'com/config.php');
require_once($root . 'com/db.php');


$db = new database();
$db->connect();

$sql = "SELECT post_id, post_content, parent
                 FROM blog_post as p
                 WHERE p.order_id={$order_id}
                 ORDER BY post_time ASC
                 LIMIT 15000";

//AND post_time>" . $this->mstart . " AND post_time<" . ($this->mend + 86400) . "

$res = $db->query($sql);
if (!$res) throw new Exception("Failed to get corpora from DB\n" . $sql . "\n\n");

while ($text = $db->fetch($res))
{
    $corpora[$text['post_id']] = array($text['post_content'],
                                       $text['parent']);
    //$text['post_id']);
}

foreach ($corpora as $id => $text)
{

    $duplicates[$text[1]][$id] = $text[0];
}


foreach ($duplicates as $parent_id => $children)
{
    unset($children[$parent_id]);
    echo "$parent_id\t$corpora[$parent_id]\n";
    foreach ($children as $child)
    {
            echo "\t\t$child\t";

            }
}
//print_r($duplicates);


//736 - 13-10-2012 ; 04-11-2012  /
//737 - 01-03-2012 ; 04-11-2012  /
//738 - 01-03-2012 ; 04-11-2012  /
//1355 - 17-09-2012 ; 30-09-2012 /
//1356 - 17-09-2012 ; 30-09-2012 /
//1179 - 01-10-2012 ; 18-11-2012 /
//961 - 01-10-2012 ; 18-11-2012  /

/*
echo "\n";
echo "\n";
echo "\n";
echo strtotime('01-12-2012')."!\n";
echo strtotime('30-09-2012')."!!\n";

*/


//            $this->mstart = strtotime($mstart);
//            $this->mend = strtotime($mend);


/*
 * SELECT p.post_id, p.post_content, p.post_nastr, s.tone_auto FTOM blog_post AS p LEFT JOIN post_sentiment AS s ON p.post_id=s.pid WHERE p.order_id = 736 AND p.post_time > 1350072000 AND p.post_time<1351972800
 * SELECT p.post_id, p.post_content, p.post_nastr, s.tone_auto FROM blog_post AS p LEFT JOIN post_sentiment AS s ON p.post_id=s.pid WHERE p.order_id = 737 AND p.post_time > 1330545600 AND p.post_time<1351972800
 * SELECT p.post_id, p.post_content, p.post_nastr, s.tone_auto FROM blog_post AS p LEFT JOIN post_sentiment AS s ON p.post_id=s.pid WHERE p.order_id = 1355 AND p.post_time > 1347825600 AND p.post_time<1348948800
 * SELECT p.post_id, p.post_content, p.post_nastr, s.tone_auto FROM blog_post AS p LEFT JOIN post_sentiment AS s ON p.post_id=s.pid WHERE p.order_id = 1355 AND p.post_time > 1347825600 AND p.post_time<1348948800
 * SELECT p.post_id, p.post_content, p.post_nastr, s.tone_auto FROM blog_post AS p LEFT JOIN post_sentiment AS s ON p.post_id=s.pid WHERE p.order_id = 1356 AND p.post_time > 1347825600 AND p.post_time<1348948800
 *
 *
 */


/*
function array_merge_recursive_distinct ( array &$array1, array &$array2 )
{
  $merged = $array1;

  foreach ( $array2 as $key => &$value )
  {
    if ( is_array ( $value ) && isset ( $merged [$key] ) && is_array ( $merged [$key] ) )
    {
      $merged [$key] = array_merge_recursive_distinct ( $merged [$key], $value );
    }
    else
    {
      $merged [$key] = $value;
    }
  }

  return $merged;
}

$arr1 = Array ( '1b' => Array ( 11=> 0), '2a' => Array (22 => 0, 24=>0) );
$arr2 = Array ( '1b' => Array ( 11=> 0, 12 => 0), '2a' => Array (22 => 0, 23 => 0) );


$arrr = $arr2 + $arr1;
print_r($arrr);
//print_r(array_merge_recursive_distinct($arr1,$arr2));
*/


?>