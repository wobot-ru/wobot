<?

require_once('/var/www/com/db.php');
require_once('/var/www/com/config.php');
require_once('/var/www/daemon/com/users.php');

date_default_timezone_set('Europe/Moscow');

$db=new database();
$db->connect();

$redis=new Redis() or die("Can'f load redis module.");
$redis->connect('127.0.0.1');

$user=new users();

$eng_sources['twitter.com']=1;
$eng_sources['livejournal.com']=1;
$eng_sources['facebook.com']=1;
$eng_sources['vk.com']=1;
$eng_sources['vkontakte.ru']=1;

if (isset($_FILES['filename']))
{
	print_r($_FILES);
	$uploaddir = '/var/www/tools/impo/data/';
	if (move_uploaded_file($_FILES['filename']['tmp_name'], $uploaddir . 
		$_FILES['filename']['name'])) {
	    print "File is valid, and was successfully uploaded.";
	} else {
	    print "There some errors!";
	}
}
if ($_GET['file']!='')
{
	$order_id=$_GET['order_id'];

	$cont=file_get_contents('/var/www/tools/impo/data/'.base64_decode($_GET['file']));
	// $cont=iconv('windows-1251','UTF-8',$cont);
	$mcont=explode("\n", $cont);
	// print_r($mcont);
	// die();
	foreach ($mcont as $item)
	{
		$regex='/^(?<time>[^\,]*?)\,(?<content>.*)\,(?<link>[^\,]*?)\,(?<auth>[^\,]*?)$/isu';
		preg_match_all($regex, $item, $out);
		// print_r($out);
		$mitem[0]=$out['time'][0];
		$mitem[1]=$out['content'][0];
		$mitem[2]=$out['link'][0];
		$mitem[3]=$out['auth'][0];
		// $mitem=explode(',', $item);
		// print_r($mitem);
		// continue;

		$qisset=$db->query('SELECT * FROM blog_post WHERE post_link=\''.addslashes(trim($mitem[2])).'\' AND order_id='.$order_id.' LIMIT 1');
		if ($db->num_rows($qisset)==0)
		{
			echo '.';
			$hn=parse_url($mitem[2]);
			$hn=$hn['host'];
			$ahn=explode('.',$hn);
			$hn = $ahn[count($ahn)-2].'.'.$ahn[count($ahn)-1];
			$hh=$ahn[count($ahn)-2];
			$blog_id=0;
			if (!in_array($hn, array('vk.com','facebook.com'))) $blog_id=$user->get_url($mitem[2]);
			else
			{
				if ($hn=='vk.com')
				{
					$qblog=$db->query('SELECT * FROM robot_blogs2 WHERE blog_link=\'vkontakte.ru\' AND blog_login='.$mitem[3].' LIMIT 1');
					if ($db->num_rows($qblog)==0) 
					{
						$qinsert=$db->query('INSERT INTO robot_blogs2 (blog_link,blog_login) VALUES (\'vkontakte.ru\',\''.addslashes($mitem[3]).'\')');
						$blog_id=$db->insert_id($qinsert);
					}
					else
					{
						$blog=$db->fetch($qblog);
						$blog_id=$blog['blog_id'];
					}
				}
				if ($hn=='facebook.com')
				{
					$qblog=$db->query('SELECT * FROM robot_blogs2 WHERE blog_link=\'facebook.com\' AND blog_login=\''.addslashes($mitem[3]).'\' LIMIT 1');
					if ($db->num_rows($qblog)==0) 
					{
						$qinsert=$db->query('INSERT INTO robot_blogs2 (blog_link,blog_login) VALUES (\'facebook.com\',\''.addslashes($mitem[3]).'\')');
						$blog_id=$db->insert_id($qinsert);
					}
					else
					{
						$blog=$db->fetch($qblog);
						$blog_id=$blog['blog_id'];
					}
				}
			}

			$qw='INSERT INTO blog_post (order_id,post_link,post_host,post_content,post_time,blog_id) VALUES ('.$order_id.',\''.addslashes(trim($mitem[2])).'\',\''.$hn.'\',\''.addslashes($mitem[1]).'\','.strtotime($mitem[0]).','.$blog_id.')';
			// echo '<br>'.$qw.'<br>';
			$qinsert=$db->query($qw);
			$post_id=$db->insert_id($qinsert);
			// echo '<br>INSERT INTO blog_full_com (ful_com_post_id,ful_com_order_id,ful_com_post) VALUES ('.$post_id.','.$_POST['orid'].',\''.addslashes($item['post']).'\')<br>';
			$db->query('INSERT INTO blog_full_com (ful_com_post_id,ful_com_order_id,ful_com_post) VALUES ('.$post_id.','.$order_id.',\''.addslashes($mitem[1]).'\')');
		}

	}
}

if ($_GET['order_id']!='')
{
	$files=scandir('data');
	// print_r($files);
	echo '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<form action="/tools/impo/?order_id='.$_GET['order_id'].'" method="post" enctype="multipart/form-data">
	<input type="file" name="filename"><br> 
	<input type="submit" value="Загрузить"><br>
	</form>
	Список файлов<br>';
	foreach ($files as $k => $file)
	{
		if ($k<2) continue;
		echo '<a href="?file='.base64_encode($file).'&order_id='.$_GET['order_id'].'">'.$file.'</a><br>';
	}
}
else
{
	echo '<form action="/tools/impo/" method="GET">order_id: <input type="text" name="order_id" value=""><input type="submit" value="GO"></form>';
}
die();

// print_r($mcont);
// echo $cont;

?>