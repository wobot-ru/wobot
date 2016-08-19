<?

require_once('/var/www/com/db.php');
require_once('/var/www/com/config.php');

$db=new database();
$db->connect();



print_r($_POST);
if (isset($_POST['orid']))
{
  die();
  header('Content-type: text/csv');
  header('Content-disposition: attachment;filename=retweets'.time().'.csv');

  /*echo '<?xml version="1.0" encoding="UTF-8"?>
  <searchresult>
    <query>Сурков</query>
  ';*/
  $i=0;
  $lol=array();
  $lor=array();
  $start=intval($_POST['start']);
  $end=intval($_POST['end']);
  $qw=$db->query('SELECT p.post_content, b.blog_login FROM blog_post AS p LEFT JOIN robot_blogs2 AS b on p.blog_id=b.blog_id WHERE p.order_id='.intval($_POST['orid']).' '.((isset($_POST['host']))?' and p.post_host="'.$_POST['host'].'"':'').' '.((($start>0)&&($end>0))?' and post_time>'.$start.' and post_time<'.$end:''));
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
  die();
}
echo '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">';

echo '<form method="GET" id="user_select"><select name="uid" onchange="document.getElementById(\'user_select\').submit();">';
$quser=$db->query('SELECT user_id,user_email FROM users ORDER BY user_id DESC');
if ($_GET['uid']=='') echo '<option value=""></option>';
while ($user=$db->fetch($quser))
{
	echo '<option '.($user['user_id']==$_GET['uid']?'selected':'').' value="'.$user['user_id'].'">'.$user['user_id'].' '.$user['user_email'].'</option>';
}
echo '</select></form>';
echo '<form method="POST">';
if ($_GET['uid'])
{
	echo '<select name="orid">';
	$qorder=$db->query('SELECT * FROM blog_orders WHERE user_id='.intval($_GET['uid']));
	while ($order=$db->fetch($qorder))
	{
		echo '<option '.($_POST['orid']==$order['order_id']?'selected':'').' value="'.$order['order_id'].'">'.$order['order_id'].' '.$order['order_name'].'</option>';
	}
	echo '</select>';
}

	echo '<br>Ресурс: <input type="link" style="width: 300px;" value="twitter.com" name="host"><br>';
	echo 'Интервал дат: <input type="date" name="start"> <input type="date" name="end">';

echo '<br><input type="submit" value="Генерировать GEPHI" name="gephi"/> <input type="submit" name="carrot2" value="Генерировать CARROT2"/>';
echo '</form>';

?>