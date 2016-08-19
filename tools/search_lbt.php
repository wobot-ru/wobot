<?
require_once('/var/www/com/config.php');
require_once('/var/www/com/func.php');
require_once('/var/www/com/db.php');
require_once('/var/www/bot/kernel.php');
$db = new database();
$db->connect();
echo '<meta http-equiv="content-type" content="text/html; charset=UTF-8" /> <form type="GET">INPUT post_id: <input type="text" name="id"><input type="submit"></form>';
$_GET['id']=1762419;
if ($_GET['id']!='')
{
	$pp=$db->query('SELECT * FROM blog_post as a LEFT JOIN blog_orders as b ON a.order_id=b.order_id WHERE post_id='.intval($_GET['id']).' LIMIT 1');
	$post=$db->fetch($pp);
	$regex='/^(((http|https|ftp):\/\/)?([[a-zA-Z0-9]\-\.])+(\.)([[a-zA-Z0-9]]){2,4}([[a-zA-Z0-9]\/+=%&_\.~?\-]*))*$/';
	$post['post_content']=preg_replace('/([a-zA-Z\/\?])([а-яА-Я])/isu','$1 $2',$post['post_content']);
	$post['post_content']=preg_replace('/([а-яА-Я])([a-zA-Z\/\?])/isu','$1 $2',$post['post_content']);
	//  '/^(((http|https|ftp):\/\/)?([[a-zA-Z0-9]\-\.])+(\.)([[a-zA-Z0-9]]){2,4}([[a-zA-Z0-9]\/+=%&_\.~?\-]*))*$/' 
	//  /((http|https|ftp):\/\/(\S*?\.\S*?))(\s|\;|\)|\]|\[|\{|\}|,|\"|\'|:|\<|$|\.\s)/ie
	//preg_match_all($regex,$post['post_content'],$out);
	preg_match_all('#\bhttps?://[^\s()<>]+(?:\([\w\d]+\)|([^[:punct:]\s]|/))#',$post['post_content'],$out);
	//echo $post['order_keyword'];
	$maskw=preg_replace('/[^a-zA-Zа-яА-Я]/isu',' ',$post['order_keyword']);
	$maswk=explode(' ',$maskw);
	//print_r($maswk);
	echo '<b>Текст поста:</b> '.$post['post_content'].'<br><br><b>Ключевые слова: </b>'.$post['order_keyword'].'<br><br><b>Ссылки с ключевыми словами:</b><br>';
	if ($out[0][0]!='')
	{
		foreach ($out[0] as $key => $item)
		{
			$cc=parseUrl($item);
			foreach ($maswk as $it)
			{
				if (($it!=' ') && ($it!=''))
				{
					if (preg_match('/'.$it.'/isu',$cc))
					{
						$c=1;
					}
				}
			}
			if ($c==1)
			{
				echo $item."<br>";
			}
			$c=0;
		}
	}
}

?>