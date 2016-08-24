<?
//require_once('/var/www/com/config.php');
require_once('com/func.php');
//require_once('/var/www/com/db.php');
require_once('bot/kernel.php');
require_once('com/tmhOAuth.php');
require_once('com/vkapi.class.php');

$masaccess[]=array(
  'consumer_key' => '4x0pjsuuMeRkmQT4dB76JA',//'M8RnwsZemYh62YS1dRW1Q',
  'consumer_secret' => 'Cikaorq0rTM40gWDo12UkhzwxpWJPdET3wPaKgao',//'ovIH2vVz82aMwaNpuBLm3i3nOUKvwgY8lR9M1z20E',
  'user_token' => '518631626-A4rwQl4ufivAXC3PstKlQVzKhwcvh7PXxpYGCKJK',//'368306310-8WG98x1bwWGAsPPmRZo0sQn8rrMtqEQGNEwGCDP0',//ot
  'user_secret' => 'iVQdBihG6c0KDCCQb6ZSQHo1nKYJKJYJMo5FKfNcQs',//'oOubUvx5UgtgyNMqCUjWSpuZjZZP0tpIbjGyg374',//oauth_token_secret ots
);
$masaccess[]=array(
  'consumer_key' => '8oO2NYwztI5zqlMtBSccg',//'M8RnwsZemYh62YS1dRW1Q',
  'consumer_secret' => 's0pMlX6dVBzRJYSypX5GkKhvMiXWr1p1Zofsh0OxnQ',//'ovIH2vVz82aMwaNpuBLm3i3nOUKvwgY8lR9M1z20E',
  'user_token' => '550068618-ce9P0slfhsFS9gK14JS3pzuWE2NgCUJd7f3ujMIA',//'368306310-8WG98x1bwWGAsPPmRZo0sQn8rrMtqEQGNEwGCDP0',//ot
  'user_secret' => '2tgtbd05kXWD4JZUqqRScpwu3vTLDpM5FXJJkvjGKo',//'oOubUvx5UgtgyNMqCUjWSpuZjZZP0tpIbjGyg374',//oauth_token_secret ots
);
$masaccess[]=array(
  'consumer_key' => 'H8XtHxwZQKeor6ht246hDg',//'M8RnwsZemYh62YS1dRW1Q',
  'consumer_secret' => 'V2TXnyNfRFXDLPOdf5BQOvdy7QCUiuzWPAJOP6lO74',//'ovIH2vVz82aMwaNpuBLm3i3nOUKvwgY8lR9M1z20E',
  'user_token' => '549988388-KL0ldFG6d1TTMirUCw9YhVJmYSKNK43QTbr53JAN',//'368306310-8WG98x1bwWGAsPPmRZo0sQn8rrMtqEQGNEwGCDP0',//ot
  'user_secret' => 'sjB9wPoPlXBsy8MdX0tlLmnIsIJfIiRNINlnCW9QEo',//'oOubUvx5UgtgyNMqCUjWSpuZjZZP0tpIbjGyg374',//oauth_token_secret ots
);
$masaccess[]=array(
  'consumer_key' => 'XsIZMQoJLIjDrOzBUKaDQ',//'M8RnwsZemYh62YS1dRW1Q',
  'consumer_secret' => 'rTHiN0cBkNUFKeGnhv571ljfhfO8VJIMzHVDOWNzo',//'ovIH2vVz82aMwaNpuBLm3i3nOUKvwgY8lR9M1z20E',
  'user_token' => '550126766-YzUsCMPF9lB1tz3lQdi0OYGcFq7OTSA8vubJMiVr',//'368306310-8WG98x1bwWGAsPPmRZo0sQn8rrMtqEQGNEwGCDP0',//ot
  'user_secret' => 'txrufC3RxuZeJpJ1kpwbabDOw9k7HizzG5jzReplbEY',//'oOubUvx5UgtgyNMqCUjWSpuZjZZP0tpIbjGyg374',//oauth_token_secret ots
);
$masaccess[]=array(
  'consumer_key' => 'dWHZ9lu5mMPOzOodSHaNfA',//'M8RnwsZemYh62YS1dRW1Q',
  'consumer_secret' => 'f0wuwVlSZ5PwlHaHf6bLx84DwwfT1vb0FY4Ul2Qhko',//'ovIH2vVz82aMwaNpuBLm3i3nOUKvwgY8lR9M1z20E',
  'user_token' => '550132144-1eBGHPA01Dy3dxpzeVEoMyxrs3OZLCyvP7N1maTU',//'368306310-8WG98x1bwWGAsPPmRZo0sQn8rrMtqEQGNEwGCDP0',//ot
  'user_secret' => 'LdMoRGRr3sN86WhSx5dk2QZ3jn5QQc3RZAjQJrQ2I',//'oOubUvx5UgtgyNMqCUjWSpuZjZZP0tpIbjGyg374',//oauth_token_secret ots
);
$masaccess[]=array(
  'consumer_key' => 'ry538m1mb5SNte1AFsGYQ',//'M8RnwsZemYh62YS1dRW1Q',
  'consumer_secret' => 'r2hbXwX2oKIvUbjVOqbSzqJX4OzNtI8qHqhpx4MPLY',//'ovIH2vVz82aMwaNpuBLm3i3nOUKvwgY8lR9M1z20E',
  'user_token' => '550235088-33f26hIfl1dG94JKwXOoRavsHJD6uvzQO8O72Jo1',//'368306310-8WG98x1bwWGAsPPmRZo0sQn8rrMtqEQGNEwGCDP0',//ot
  'user_secret' => 'Z4ejZPE3hGrOe3guKTsV8n6SuVMvZ7RLmpdbpk0',//'oOubUvx5UgtgyNMqCUjWSpuZjZZP0tpIbjGyg374',//oauth_token_secret ots
);

date_default_timezone_set ( 'Europe/Moscow' );

function get_twitter($nick,$idacs)
{
	global $db,$masaccess;
	$tmhOAuth = new tmhOAuth($masaccess[$idacs]);
	//echo $nick;
	$ppp=0;
	do
	{
		$tmhOAuth->request('GET', $tmhOAuth->url('1/users/show/'.$nick, 'json'));
		//echo $tmhOAuth->response['code']."\n";
	    if ($tmhOAuth->response['code'] == 200) 
	    {
			$user=json_decode($tmhOAuth->response['response'],true);
			if ($user['location']!='')
			{
				$rru=$db->query("SELECT * FROM robot_location WHERE loc='".$user['location']."'");
				if (mysql_num_rows($rru)==0)
				{
				$content_tw_loc=parseUrl('http://geocode-maps.yandex.ru/1.x/?geocode='.urlencode($user['location']).'&key=ANpUFEkBAAAAf7jmJwMAHGZHrcKNDsbEqEVjEUtCmufxQMwAAAAAAAAAAAAvVrubVT4btztbduoIgTLAeFILaQ==&results=1');
					$regextw='/<pos>(?<loc_tw>.*?)<\/pos>/is';
					preg_match_all($regextw,$content_tw_loc,$out_tw);
					$rru=$db->query('INSERT INTO robot_location (loc,loc_coord) VALUES (\''.$user['location'].'\',\''.$out_tw['loc_tw'][0].'\')');
				}
				else
				{
					$rru1=$db->fetch($rru);
					$out_tw['loc_tw'][0]=$rru1['loc_coord'];
				}
			}
			$regex='/(?<ch>\-?\d*?)\.(?<d>\d)/is';
			preg_match_all($regex,$out_tw['loc_tw'][0],$out);
			if (($out['ch'][0]!='') && ($out['d'][0]!='') && ($out['ch'][1]!='') && ($out['d'][1]!=''))
			{
				$twl=$out['ch'][0].'.'.$out['d'][0].' '.$out['ch'][1].'.'.$out['d'][1];
			}
			$outmas['loc']=$twl;
			$outmas['fol']=intval($user["followers_count"]);
			$outmas['nick']=$nick;
			$outmas['name']=$nick;
			$outmas['gender']=0;
			$outmas['age']=0;
		} 
		//echo $ppp."\n";
		$ppp++;
		if (($tmhOAuth->response['code']==403) || ($tmhOAuth->response['code']==404))
		{
			$user['gg']='good';
		}
		sleep(2);
	}
	while (json_encode($user)=='null');
	if (intval($outmas['fol'])==0)
	{
		$fp = fopen('data.txt', 'a');
		fwrite($fp, date('r')."\n");
		fwrite($fp, json_encode($user));
		fclose($fp);
	}
	//print_r($outmas);
	return $outmas;
}
//get_twitter('healthemen',5);

?>
