<?

require_once('/var/www/com/db.php');
require_once('/var/www/com/config.php');

$db=new database();
$db->connect();

function send_query($data)
{
	$curl = curl_init();

	//уcтанавливаем урл, к которому обратимся
	curl_setopt($curl, CURLOPT_URL, 'http://wobotparser.cloudapp.net/engine/api/get_freash_blog.php');

	//включаем вывод заголовков
	curl_setopt($curl, CURLOPT_HEADER, 0);

	//передаем данные по методу post
	curl_setopt($curl, CURLOPT_POST, 1);

	//теперь curl вернет нам ответ, а не выведет
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

	//переменные, которые будут переданные по методу post
	curl_setopt($curl, CURLOPT_POSTFIELDS, 'data='.json_encode($data));
	//я не скрипт, я браузер опера
	curl_setopt($curl, CURLOPT_USERAGENT, 'Opera 10.00');

	$res = curl_exec($curl);
	$content = curl_exec( $curl );
	$err     = curl_errno( $curl );
	$errmsg  = curl_error( $curl );
	$header  = curl_getinfo( $curl ); 
	return json_decode($content,true);
}

do
{
	$qblog=$db->query('SELECT blog_login,blog_link FROM robot_blogs2 WHERE blog_last_update<'.(time()-86400*7).' AND blog_last_update!=0 LIMIT '.($offset*1000).',1000');
	echo "\n\n\n\n\n\n".'SELECT blog_login,blog_link FROM robot_blogs2 WHERE blog_last_update<'.(time()-86400*7).' AND blog_last_update!=0 LIMIT '.($offset*1000).',1000'."\n\n\n\n\n\n";
	$offset++;
	while ($blog=$db->fetch($qblog))
	{
		$data[$blog['blog_login']]=$blog['blog_link'];
		if (count($data)>100)
		{
			// print_r($data);
			$mquery=send_query($data);
			foreach ($mquery as $query)
			{
				echo $query."\n";
				$db->query($query);
			}
			unset($data);
		}
	}
}
while ($db->num_rows($qblog)==1000);

?>