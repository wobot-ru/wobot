<?

//require_once('/var/www/bot/kernel.php');

function get_likes($url)
{
	$re_array = array('/story_fbid=(?<data>\d+)/isu', '/photo.php\?fbid=(?<data>\d+)/isu', '/note.php\?note_id=(?<data>\d+)/isu', '/set=a.(?<data>\d+)/isu');
	$outmas['count']=0;
	$outmas['data']['likes']=0;
	$regex='/\/posts\/(?<data>.*)/isu';
	preg_match_all($regex,$url,$out);
	//print_r($out);
	if ($out['data'][0]=='')
	{
		for($i=0; $i<count($re_array); $i++){
			preg_match_all($re_array[$i],$url,$out);
			//print_r($out);
			if($out['data'][0]!=''){
				break;
			}
		}
		
	}
	if ($out['data'][0]!='')
	{
		//do
		{
			$cont=parseUrl('https://graph.facebook.com/'.$out['data'][0].'/likes?limit=5000&offset=0');
			if ($cont=='')
			{
				$attmp++;
				echo "\n".'continue...'."\n";
			}
		}
		//while (($cont=='') && ($attmp<3));
		//$cont=parseUrl('https://graph.facebook.com/'.$out['data'][0].'/likes?limit=5000&offset=0');
		$mas=json_decode($cont,true);
		$outmas['count']=count($mas['data']);
		$outmas['data']['likes']=count($mas['data']);
	}
	//print_r($outmas);
	return $outmas;
}


//get_likes('https://www.facebook.com/photo.php?fbid=763787413685702');
//https://www.facebook.com/media/set/?set=a.906795349336786&l=c80037126a&type=3
// $cont=parseUrl('http://188.120.239.225/getlist.php');
// $mproxy=json_decode($cont,true);
// print_r(get_likes('http://facebook.com/282681245570/posts/169140753098360'));

//http://www.facebook.com/permalink.php?story_fbid=10152704595299271&id=325794294270

//http://www.facebook.com/photo.php?fbid=684008551690983

//http://www.facebook.com/note.php?note_id=290790517789967


?>
