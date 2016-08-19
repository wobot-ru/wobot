<?

require_once('/var/www/com/config.php');
require_once('/var/www/com/func.php');
require_once('/var/www/com/db.php');
require_once('/var/www/bot/kernel.php');

//TODO:
// query
// order select
// &, nbsp, mdash -replace
// > < -wrong tags replace

$db = new database();
$db->connect();

if (!isset($_POST['order_id']))
{
  echo '
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  </head>
  <body>
  <form method="post">
    <h1>Экспорт в GEPHI</h1>
    <p># темы: <input type="text"/></p>
    <p>Даты: <input type="text" value="'.date('d.m.Y').'"/> - <input type="text" value="'.date('d.m.Y').'"/></p>
    <input type="submit" value="Сгенерировать"/>
  </form>
  </body>
</html>';
}
else
{
  header('Content-type: text/csv');
  header('Content-disposition: attachment;filename=retweets'.time().'.csv');

  /*echo '<?xml version="1.0" encoding="UTF-8"?>
  <searchresult>
    <query>Сурков</query>
  ';*/
  $i=0;
  $lol=array();
  $lor=array();

  $qw=$db->query('SELECT p.post_content, b.blog_login FROM blog_post AS p LEFT JOIN robot_blogs2 AS b on p.blog_id=b.blog_id WHERE p.order_id=3313 and p.post_host="twitter.com"');
  while ($row = $db->fetch($qw)) {
  /*		echo'
    <document id="'.$i.'">
      <title></title>
      <url></url>
      <snippet>
        '.strip_tags($row['post_content']).'
      </snippet>
    </document>
    ';*/
    $row['blog_login']=preg_replace("/[^A-Za-z0-9]/", '', $row['blog_login']);
    preg_match_all("/RT @(?<nick>.*?)\:/isu",
                  $row['post_content'], $nick);
    if (isset($nick['nick'][0])) {
      $nick['nick'][0]=preg_replace("/[^A-Za-z0-9]/", '', $nick['nick'][0]);
      if (!isset($lol[$row['blog_login']])) $lol[$row['blog_login']]=1;
      if (!isset($lol[$nick['nick'][0]])) $lol[$nick['nick'][0]]=1;
      $lor[$nick['nick'][0]][$row['blog_login']]++;
      $row['retwited']=$nick['nick'][0];
    }
    //print_r($row);
    $i++;
  }
  //print_r($lol);
  //print_r($lor);

  foreach ($lol as $key => $value)
  {
    echo ';'.$key;
  }
  echo "\n";
  foreach ($lol as $key => $value)
  {
    echo $key;
    foreach ($lol as $key1 => $value1)
    {
      echo ';'.intval($lor[$key][$key1]);
    }
    echo "\n";
  }
  //echo '</searchresult>';
  /*
  ;A;B;C;D;E
  A;0;1;0;1;0
  B;1;0;0;0;0
  C;0;0;1;0;0
  D;0;1;0;1;0
  E;0;0;0;0;0
  */
}
?>