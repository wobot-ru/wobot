<?

require_once('/var/www/daemon/com/config.php');
require_once('/var/www/daemon/com/db.php');

error_reporting(0);

$db=new database();
$db->connect();

function get_token()
{
	$curl = curl_init();

	//уcтанавливаем урл, к которому обратимся
	curl_setopt($curl, CURLOPT_URL, 'https://eu2.salesforce.com/services/oauth2/token');

	//включаем вывод заголовков
	curl_setopt($curl, CURLOPT_HEADER, 0);

	// $headr[] = 'Content-length: 0';
	// $headr[] = 'Content-type: application/json';
	// $headr[] = 'Content-Type: application/json;charset=UTF-8';
	// $headr[] = 'Authorization: Bearer 00D1100000BuoLL!ARMAQO76_S0xioO1HUIpWzXbiN8s1qKcvSq7OO.XpBoIXOnm3kvgMi0Bt_4pTplWVSnNlG2XeXqTmNAdd9swlgGWhAUfsMlD';

	curl_setopt($crl, CURLOPT_HTTPHEADER,$headr);
	// curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: 00D1100000BuoLL!ARMAQOPPgyzX_dr7SlfAMpGKFivsy651NJII95Es6lx3V0hiMQ.7JIvGUpOaAONLTD8t2wpP26FZq26s0y2VkyjiBv9kdzez'));
	//передаем данные по методу post
	// curl_setopt($curl, CURLOPT_POST, 1);

	//теперь curl вернет нам ответ, а не выведет
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

	//переменные, которые будут переданные по методу post
	curl_setopt($curl, CURLOPT_POSTFIELDS, 'grant_type=password&client_id=3MVG99qusVZJwhsnDoLL5yUFaicI55gvQrxaeTH4TJZtKnID_XCG4Sr5UwBjhnJXqu0rW800lQrDrUZaKSTwF&client_secret=7051594992958005457&username=smm@tinkoffinsurance.ru&password=331064Asdft2JMtX1sbyKzgPWGUJBwxsxj');
	//я не скрипт, я браузер опера
	curl_setopt($curl, CURLOPT_USERAGENT, 'Opera 10.00');

	$res = curl_exec($curl);
	$content = curl_exec( $curl );
	$err     = curl_errno( $curl );
	$errmsg  = curl_error( $curl );
	$header  = curl_getinfo( $curl ); 
	// print_r($header);
	// echo $content;
	$mcont=json_decode($content,true);
	// echo $mcont['access_token'];
	return $mcont['access_token'];
}
// die();

function sync_post($posts,$access_token)
{
	print_r($posts);
	// echo json_encode($posts);
	$curl = curl_init();

	//уcтанавливаем урл, к которому обратимся
	curl_setopt($curl, CURLOPT_URL, 'https://eu2.salesforce.com/services/apexrest/Wobot');

	//включаем вывод заголовков
	curl_setopt($curl, CURLOPT_HEADER, 0);

	// $headr[] = 'Content-length: 0';
	// $headr[] = 'Content-type: application/json';
	$headr[] = 'Content-Type: application/json;charset=UTF-8';
	$headr[] = 'Authorization: Bearer '.$access_token;
	// print_r($headr);
	curl_setopt($curl, CURLOPT_HTTPHEADER,$headr);

	//теперь curl вернет нам ответ, а не выведет
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

	//переменные, которые будут переданные по методу post
	// curl_setopt($curl, CURLOPT_POSTFIELDS, '{"posts":[{"id":"47268966","post":"  <span class=\"kwrd\">рожков</span> и <span class=\"kwrd\">черезов</span> красавчики уася  ","title":"рожков и черезов красавчики уася","timepost":"12:03:00 04.02.2014","url":"http://3ojlotou.livejournal.com/176393.html","auth_url":"http://3ojlotou.livejournal.com","host":"livejournal","imgurl":"./img/social/livejournal.png","nick":"3ojlotou","nastr":"0","spam":"0","eng":"0","fav":"0","foll":"4088","geo":null,"geo_c":null,"gender":"0","age":"0"},{"id":"47268962","post":"  <span class=\"kwrd\">рожков</span> и <span class=\"kwrd\">черезов</span> красавчики уася  ","title":"рожков и черезов красавчики уася","timepost":"12:03:00 04.02.2014","url":"http://3ojlotou.livejournal.com/176393.html","auth_url":"http://3ojlotou.livejournal.com","host":"livejournal","imgurl":"./img/social/livejournal.png","nick":"3ojlotou","nastr":"0","spam":"0","eng":"0","fav":"0","foll":"4088","geo":null,"geo_c":null,"gender":"0","age":"0"}]}');
	curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($posts));
	//я не скрипт, я браузер опера
	// curl_setopt($curl, CURLOPT_USERAGENT, 'Opera 10.00');

	$res = curl_exec($curl);
	$content = curl_exec( $curl );
	$err     = curl_errno( $curl );
	$errmsg  = curl_error( $curl );
	$header  = curl_getinfo( $curl ); 
	echo $content."\n\n";
	sleep(5);
	return $content;
}

/*
{
  "posts": [
    {
      "id": "47268966",
      "post": "  <span class=\"kwrd\">рожков</span> и <span class=\"kwrd\">черезов</span> красавчики уася  ",
      "title": "рожков и черезов красавчики уася",
      "timepost": "12:03:00 04.02.2014",
      "url": "http://3ojlotou.livejournal.com/176393.html",
      "auth_url": "http://3ojlotou.livejournal.com",
      "host": "livejournal",
      "imgurl": "./img/social/livejournal.png",
      "nick": "3ojlotou",
      "nastr": "0",
      "spam": "0",
      "eng": "0",
      "fav": "0",
      "foll": "4088",
      "geo": null,
      "geo_c": null,
      "gender": "0",
      "age": "0"
    },
    {
      "id": "47268962",
      "post": "  <span class=\"kwrd\">рожков</span> и <span class=\"kwrd\">черезов</span> красавчики уася  ",
      "title": "рожков и черезов красавчики уася",
      "timepost": "12:03:00 04.02.2014",
      "url": "http://3ojlotou.livejournal.com/176393.html",
      "auth_url": "http://3ojlotou.livejournal.com",
      "host": "livejournal",
      "imgurl": "./img/social/livejournal.png",
      "nick": "3ojlotou",
      "nastr": "0",
      "spam": "0",
      "eng": "0",
      "fav": "0",
      "foll": "4088",
      "geo": null,
      "geo_c": null,
      "gender": "0",
      "age": "0"
    }
  ]
}
*/

do
{
	$qpost=$db->query('SELECT *,a.post_id as post_id,a.order_id as order_id FROM blog_post as a LEFT JOIN robot_blogs2 as b ON a.blog_id=b.blog_id LEFT JOIN blog_reaction as c ON a.post_id=c.post_id WHERE post_time>'.mktime(0,0,0,date('n'),date('j')-1,date('Y')).' AND post_read=0 AND a.order_id='.intval($_SERVER['argv'][1]).' LIMIT 10');
	echo 'SELECT *,a.post_id as post_id,a.order_id as order_id FROM blog_post as a LEFT JOIN robot_blogs2 as b ON a.blog_id=b.blog_id LEFT JOIN blog_reaction as c ON a.post_id=c.post_id WHERE post_time>'.mktime(0,0,0,date('n'),date('j')-1,date('Y')).' AND post_read=0 AND a.order_id='.intval($_SERVER['argv'][1]).' LIMIT 10'."\n";
	while ($post=$db->fetch($qpost))
	{
		print_r($post);
		$post_ids[]=$post['post_id'];
		// $db->query('UPDATE blog_post SET post_read=1 WHERE post_id='.$post['post_id']);
		$mpost['id']=$post['post_id'];
		$mpost['order_id']=$post['order_id'];
		$mpost['post']=mb_substr($post['post_content'],0,150,'UTF-8');
		$mpost['title']=mb_substr($post['post_content'],0,150,'UTF-8');
		$mpost['timepost']=date('H:i:s d.m.Y',$post['post_time']);
		$mpost['url']=$post['post_link'];
		$mpost['auth_url']=$post['blog_link'];
		$mpost['host']=$post['post_host'];
		$mpost['imgurl']=$post['post_host'].'.png';
		$mpost['nick']=$post['blog_nick'];
		$mpost['nastr']=$post['post_nastr'];
		$mpost['spam']=$post['post_spam'];
		$mpost['eng']=$post['post_engage'];
		$mpost['fav']=$post['post_fav'];
		$mpost['foll']=$post['blog_readers'];
		$mpost['geo']=$post['blog_location'];
		$mpost['geo_c']=$post['blog_location'];
		$mpost['gender']=$post['blog_gender'];
		$mpost['age']=$post['blog_age'];
		if(intval($post['reaction_id'])==0){
			$react = $db->query('SELECT * FROM blog_reaction WHERE post_id='.$post['post_id'].' AND order_id='.$post['order_id']);
			while($tmp = $db->fetch($react)){
				$post_reaction[] = array('reaction_content' => $tmp['reaction_content'], 'reaction_time' => ($tmp['reaction_time']==0?null:date('H:i:s d.m.Y',$tmp['reaction_time'])), 'reaction_blog_login' => $tmp['reaction_blog_login']);
			}
		}
		$mpost['reaction']=$post_reaction;
		//$mpost['reaction']['reaction_time']=date('H:i:s d.m.Y',$post['reaction_time']);
		//$mpost['reaction']['reaction_blog_login']=$post['reaction_blog_login'];
		$arrpost[]=$mpost;
		// if (count($arrpost)==10) echo sync_post(array('posts'=>$arrpost),get_token());
	}
	echo '.';
	echo sync_post(array('posts'=>$arrpost),get_token());
	$db->query('UPDATE blog_post SET post_read=1 WHERE post_id IN ('.implode(',', $post_ids).')');
	echo 'UPDATE blog_post SET post_read=1 WHERE post_id IN ('.implode(',', $post_ids).')'."\n";
	unset($post_ids);
	unset($arrpost);
	unset($post_reaction);
}
while ($db->num_rows($qpost)!=0);

// print_r($header);
// echo $content;

?>