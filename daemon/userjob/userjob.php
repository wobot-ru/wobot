#!/usr/bin/php
<?
require_once('/var/www/userjob/com/config.php');
require_once('/var/www/userjob/com/func.php');
require_once('/var/www/userjob/com/db.php');
require_once('/var/www/userjob/bot/kernel.php');
require_once('/var/www/userjob/com/tmhOAuth.php');
require_once('/var/www/userjob/com/vkapi.class.php');

//twitter app ticket
$tmhOAuth = new tmhOAuth(array(
  'consumer_key' => 'M8RnwsZemYh62YS1dRW1Q',
  'consumer_secret' => 'ovIH2vVz82aMwaNpuBLm3i3nOUKvwgY8lR9M1z20E',
  'user_token' => '88034785-kHuhQFmhVpRpAF3HbOyVlA9fugKeKI4cNU6vgBaQ',
  'user_secret' => 'gYLmncR3SScov8QkSuw0FHMVmxXDbv19bY2j0rZZSM',
));

$api_id = 2124816; // Insert here id of your application
$secret_key = 'f98VkwX1Cc64xSj76vP4'; // Insert here secret key of your application

//sleep(5000);

//error_reporting(E_ERROR | E_WARNING | E_PARSE);
error_reporting(E_ERROR);
ignore_user_abort(true);
set_time_limit(0);
ini_set('max_execution_time',0);
ini_set('default_charset','utf-8');
ob_implicit_flush();
$deltatime=7;
$db = new database();
$db->connect();
$gg1=0;
while(1)
{
$i=0;
$gg1++;
if (($gg1 % 2)==1)
{
$ressec=$db->query('SELECT * FROM robot_blogs2 WHERE blog_last_update=0');
}
else
{
$ressec=$db->query('SELECT * FROM robot_blogs2 WHERE blog_last_update<'.(time()-$deltatime*86400).' LIMIT 10');
}
//$ressec=$db->query('SELECT * FROM robot_blogs2 WHERE blog_link=\'livejournal.com\'');
//echo 'SELECT * FROM robot_blogs2 WHERE blog_last_update=0 OR blog_last_update<'.(time()-$deltatime*86400);
//$blog['blog_link']='vkontakte.ru';
//$blog['blog_login']='91553410';
//echo $blog['blog_login'];
//echo 'scanning all orders: '.mysql_num_rows($ressec)." for new accounts\n";
$mode='wb';
while($blog=$db->fetch($ressec))
{
	//opening post file to generate cash files ////////////////////////////////////////////////////////////
	echo 'order: '.$blog['order_id']."\n";
	$count=0;
	$hn=$blog['blog_link'];
	$nick=$blog['blog_login'];
	//$hn="livejournal.com";
	//$nick="_zombakhan_";
	echo $nick." ".$blog['blog_link']."\n";
	{
		if ($hn=='twitter.com') 
        {
            //$tw=$db->query('SELECT * FROM robot_blogs2 WHERE blog_link="twitter.com" and blog_login="'.$nick.'"');
            //if (mysql_num_rows($tw)==0)
            {
	            $tmhOAuth->request('GET', $tmhOAuth->url('/1/users/show/'.$nick, 'json'));
                if ($tmhOAuth->response['code'] == 200) 
                {
                	echo 'good';
			    	$user=json_decode($tmhOAuth->response['response'],true);
			    	$rru=$db->query("SELECT * FROM robot_location WHERE loc='".$user['location']."'");
			    	if (mysql_num_rows($rru)==0)
			    	{
			        	$content_tw_loc=parseUrl('http://geocode-maps.yandex.ru/1.x/?geocode='.urlencode($user['location']).'&key=ANpUFEkBAAAAf7jmJwMAHGZHrcKNDsbEqEVjEUtCmufxQMwAAAAAAAAAAAAvVrubVT4btztbduoIgTLAeFILaQ==&results=1');
			        	//echo urlencode($user['location']);
			        	$regextw='/<pos>(?<loc_tw>.*?)<\/pos>/is';
			        	preg_match_all($regextw,$content_tw_loc,$out_tw);
				    	$rru=$db->query('INSERT INTO robot_location (loc,loc_coord) VALUES (\''.$user['location'].'\',\''.$out_tw['loc_tw'][0].'\')');
			        }
			        else
			        {
			        	$rru1=$db->fetch($rru);
			        	$out_tw['loc_tw'][0]=$rru1['loc_coord'];
			        }
					echo $user['location'].' '.$out_tw['loc_tw'][0]."\n";
			        //$db->query('INSERT INTO robot_blogs2 (blog_link, blog_login,blog_location,blog_readers) values ("twitter.com","'.$nick.'","'.$out_tw['loc_tw'][0].'",'.intval($user["followers_count"]).')');
	            	$sel="SELECT * FROM robot_blogs2 WHERE blog_login='".$nick."' AND blog_link='twitter.com'";
	            	$ord=$db->query($sel);
					if ($ord['blog_location']=='')
					{
						if ($out_tw['loc_tw'][0]!='')
						{
							$regex='/(?<ch>\-?\d*?)\.(?<d>\d)/is';
							echo $key;
							preg_match_all($regex,$out_tw['loc_tw'][0],$out);
							$twl=$out['ch'][0].'.'.$out['d'][0].' '.$out['ch'][1].'.'.$out['d'][1];
						}
						else
						{
							$twl='';
						}
	            		$upquery="UPDATE robot_blogs2 SET blog_link='".$hn."', blog_location='".$twl."',blog_readers=".intval($user["followers_count"]).",blog_last_update=".time().",blog_nick='".$nick."' WHERE blog_login='".$nick."' AND blog_link='twitter.com'";
					}
					else
					{
	            		$upquery="UPDATE robot_blogs2 SET blog_link='".$hn."',blog_readers=".intval($user["followers_count"]).",blog_last_update=".time().",blog_nick='".$nick."' WHERE blog_login='".$nick."' AND blog_link='twitter.com'";
					}
	            	//echo $upquery;
	            	$db->query($upquery);
	            	//echo "UPDATE robot_blogs2 SET blog_link='".$hn."', blog_location='".$out_tw['loc_tw'][0]."',blog_readers=".intval($user["followers_count"]).",blog_last_update=".time().",blog_nick='".$nick."' WHERE blog_login='".$nick."' AND blog_link='twitter.com'";
			        echo 'add: '.$nick.' '.$out_tw['loc_tw'][0].' '.intval($user["followers_count"])." ".time();
			    } 
			    else 
			    {
	            	$upquery="UPDATE robot_blogs2 SET blog_link='".$hn."' ,blog_last_update=".(time()+9000000000)." ,blog_nick='".$nick."' WHERE blog_login='".$nick."' AND blog_link='twitter.com'";
	            	//echo $upquery;
	            	$db->query($upquery);
			    	$tmhOAuth->pr(htmlentities($tmhOAuth->response['response']));
			    }
			    sleep(11);
				echo $upquery;
            }
       }
       elseif ($hn=='livejournal.com') 
       {
		echo 'gg';
	       //http://knowledgeaction.livejournal.com/21962.html
           //http://community.livejournal.com/chgk_aic/28944.html
		   $cont=parseUrl('http://'.$blog['blog_login'].'.livejournal.com/profile');
		//echo $cont;
		$regex='/<a href=\'.*?\' class=\'region\'>(?<data>.*?)<\/a>/is';
		preg_match_all($regex,$cont,$out);
		if ($out['data'][0]=='')
		{
			$regex='/<a href=\'.*?\' class=\'locality\'>(?<data>.*?)<\/a>/is';
			preg_match_all($regex,$cont,$out);
			//print_r($out);
		}
		$loc=$out['data'][0];
		$regex='/<span class=\'expandcollapse on\' id=\'fofs_header\'><img id=\'fofs_arrow\' src=\'.*?\' align=\'absmiddle\' alt=\'\' \/>.*?\((?<fol>.*?)\).<\/span>/is';
		preg_match_all($regex,$cont,$out);
		$out['fol'][0]=preg_replace('/[^0-9]/is','',$out['fol'][0]);
		//print_r($out);
		//echo $loc.' '.$res['blog_login'].' '.$out['fol'][0];
		//echo $cont;
		//echo $loc;
		$content_tw_loc=parseUrl('http://geocode-maps.yandex.ru/1.x/?geocode='.urlencode($loc).'&key=ANpUFEkBAAAAf7jmJwMAHGZHrcKNDsbEqEVjEUtCmufxQMwAAAAAAAAAAAAvVrubVT4btztbduoIgTLAeFILaQ==&results=1');
		//echo urlencode($user['location']);
		$regextw='/<pos>(?<loc_tw>.*?)<\/pos>/is';
		preg_match_all($regextw,$content_tw_loc,$out_tw);
		if ($out_tw['loc_tw'][0]!='')
		{
			if ($out_tw['loc_tw'][0]!='')
			{
				$regex='/(?<ch>\-?\d*?)\.(?<d>\d)/is';
				echo $key;
				preg_match_all($regex,$out_tw['loc_tw'][0],$out);
				$twl=$out['ch'][0].'.'.$out['d'][0].' '.$out['ch'][1].'.'.$out['d'][1];
			}
			echo "\n".'UPDATE robot_blogs2 SET blog_location=\''.$twl.'\', blog_readers='.intval($out['fol'][0]).', blog_nick=\''.$nick.'\', blog_login=\''.$nick.'\',blog_last_update=\''.time().'\' WHERE blog_login=\''.$nick.'\' AND blog_link=\'livejournal.com\''."\n";
			$rru=$db->query('UPDATE robot_blogs2 SET blog_location=\''.$twl.'\', blog_readers='.intval($out['fol'][0]).', blog_nick=\''.$nick.'\', blog_login=\''.$nick.'\',blog_last_update=\''.time().'\' WHERE blog_login=\''.$nick.'\' AND blog_link=\'livejournal.com\'');	
			echo $loc."\n";
		}
		else
		{
			echo 'UPDATE robot_blogs2 SET blog_readers='.intval($out['fol'][0]).', blog_nick=\''.$nick.'\', blog_login=\''.$nick.'\',blog_last_update=\''.time().'\' WHERE blog_login=\''.$nick.'\' AND blog_link=\'livejournal.com\'';
			$rru=$db->query('UPDATE robot_blogs2 SET blog_readers='.intval($out['fol'][0]).', blog_nick=\''.$nick.'\', blog_login=\''.$nick.'\',blog_last_update=\''.time().'\' WHERE blog_login=\''.$nick.'\' AND blog_link=\'livejournal.com\'');	
			echo $loc."readers\n";
		}
           $regexy="/\/\/(?<gg_id>.*?)\./is";
           preg_match_all($regexy,$link,$out);
           $gg_id=$out[gg_id][0];
			$gg_id=$blog['blog_login'];	
			echo '|+|'.$gg_id.'|+|'."\n";
//$gg_id='dolboeb';			
           //print_r($out);
            //echo $gg_id;
            //$live_text=parseUrl('http://'.$gg_id.'.livejournal.com/data/foaf');
            sleep(1);
            $regexy="/<foaf\:nick>(?<lnick>.*?)<\/foaf:nick>.*?<ya\:city\sdc\:title=\"(?<lcity>.*?)\"/is";
            preg_match_all($regexy,$live_text,$out);
            /*$regexy="/<foaf:nick>(?<lnick>.*?)<\/foaf:nick>/is";
            preg_match_all($regexy,$live_text,$out1);*/
            //$live_text=parseUrl('http://www.livejournal.com/misc/fdata.bml?user='.$gg_id);
            //echo $live_text;
			//echo $live_text;
            sleep(1);
            //echo $live_text;
            if ($out['gg_id'][0]!="community")
            {
	            $live_text=str_replace("# Note: Polite data miners cache on their end.  Impolite ones get banned.","",$live_text);
                $regexy="/.\s(?<llfoll>.*?)\s/is";
                preg_match_all($regexy,$live_text,$outtt);
                //print_r($outtt);
            }
			//echo '/'.count($outtt['llfoll']).'/';
			unset($mfoll);
			foreach ($outtt['llfoll'] as $item)
			{
				if (!in_array($item,$mfoll))
				{
					$mfoll[]=$item;
				}
			}
			echo '|'.count($mfoll).'|';
            //$tw=$db->query('SELECT * FROM robot_blogs2 WHERE blog_link="livejournal.com" and blog_login="'.$gg_id.'"');
            //if (mysql_num_rows($tw)==0)
            {
	            /*$content_tw_loc=parseUrl('http://geocode-maps.yandex.ru/1.x/?geocode='.$out[lcity][0].'&key=ANpUFEkBAAAAf7jmJwMAHGZHrcKNDsbEqEVjEUtCmufxQMwAAAAAAAAAAAAvVrubVT4btztbduoIgTLAeFILaQ==&results=1');
                $regextw='/<pos>(?<loc_tw>.*?)<\/pos>/is';
                preg_match_all($regextw,$content_tw_loc,$out_tw);*/
		    	$rru=$db->query("SELECT * FROM robot_location WHERE loc='".$out['lcity'][0]."'");
		    	if (mysql_num_rows($rru)==0)
		    	{
		        	$content_tw_loc=parseUrl('http://geocode-maps.yandex.ru/1.x/?geocode='.$out['lcity'][0].'&key=ANpUFEkBAAAAf7jmJwMAHGZHrcKNDsbEqEVjEUtCmufxQMwAAAAAAAAAAAAvVrubVT4btztbduoIgTLAeFILaQ==&results=1');
		        	//echo urlencode($user['location']);
		        	$regextw='/<pos>(?<loc_tw>.*?)<\/pos>/is';
		        	preg_match_all($regextw,$content_tw_loc,$out_tw);
			    	$rru=$db->query('INSERT INTO robot_location (loc,loc_coord) VALUES (\''.$out['lcity'][0].'\',\''.$out_tw['loc_tw'][0].'\')');
		        }
		        else
		        {
		        	$rru1=$db->fetch($rru);
		        	$out_tw['loc_tw'][0]=$rru1['loc_coord'];
		        }
				echo $out['lcity'][0].' '.$out_tw['loc_tw'][0]."\n";
                //$db->query('INSERT INTO robot_blogs2 (blog_link, blog_login,blog_location,blog_readers) values ("livejournal.com","'.$gg_id.'","'.$out_tw['loc_tw'][0].'",'.count($outtt[llfoll]).')');
            	//$sel="SELECT * FROM robot_blogs2 WHERE blog_login='".$nick."' AND blog_link='livejournal.com'";  comm------
            	//$ord=$db->query($sel);
				if ($ord['blog_location']=='')
				{
					if ($out_tw['loc_tw'][0]!='')
					{
						$regex='/(?<ch>\-?\d*?)\.(?<d>\d)/is';
						echo $key;
						preg_match_all($regex,$out_tw['loc_tw'][0],$out);
						$twl=$out['ch'][0].'.'.$out['d'][0].' '.$out['ch'][1].'.'.$out['d'][1];
					}
					else
					{
						$twl='';
					}
	            	//$upquery="UPDATE robot_blogs2 SET blog_link='".$hn."',blog_last_update=".time().", blog_nick='".$nick."' WHERE blog_login='".$nick."' AND blog_link='livejournal.com'";
				}
				else
				{
	            	//$upquery="UPDATE robot_blogs2 SET blog_link='".$hn."',blog_last_update=".time().", blog_nick='".$nick."' WHERE blog_login='".$nick."' AND blog_link='livejournal.com'";
				}
            	//$db->query($upquery);
            	//echo "UPDATE robot_blogs2 SET blog_link='".$hn."', blog_location='".$out_tw['loc_tw'][0]."',blog_readers=".count($outtt[llfoll]).",blog_last_update=".time().", blog_nick='".$nick."' WHERE blog_login='".$nick."' AND blog_link='livejournal.com'";
                echo 'add: '.$gg_id.' '.count($outtt[llfoll]).' '.($out[lcity][0])." [livejournal.com]\n";
				echo $upquery;
            }
       }
       elseif ($hn=='facebook.com') 
       {
	       //$link="http://facebook.com/100000344775791/posts/173713625973304";
           /*$mas=explode('/',$link);
           $json=parseUrl("https://graph.facebook.com/".$mas[5]."?access_token=158565747504200|YvEEJ72Q6m3tohIylBb62tQ5EVE");
           $mas1=json_decode($json, true);*/
           //print_r($mas1);
           //echo $mas1[from][id];
           sleep(1);
           $mas1[from][id]=$blog['blog_login'];
           $json=parseUrl("https://graph.facebook.com/".$mas1[from][id]."?access_token=158565747504200|YvEEJ72Q6m3tohIylBb62tQ5EVE");
           $mas2=json_decode($json, true);
           //print_r($mas2);
           //echo $mas2[location][name]."<br>";
           //echo $mas2[name];
           $tw=$db->query('SELECT * FROM robot_blogs2 WHERE blog_link="facebook.com" and blog_login="'.$mas1[from][id].'"');
           //if (mysql_num_rows($tw)==0)
           {
	           /*$content_tw_loc=parseUrl('http://geocode-maps.yandex.ru/1.x/?geocode='.urlencode($mas2[location][name]).'&key=ANpUFEkBAAAAf7jmJwMAHGZHrcKNDsbEqEVjEUtCmufxQMwAAAAAAAAAAAAvVrubVT4btztbduoIgTLAeFILaQ==&results=1');
               $regextw='/<pos>(?<loc_tw>.*?)<\/pos>/is';
               preg_match_all($regextw,$content_tw_loc,$out_tw);*/
		    	$rru=$db->query("SELECT * FROM robot_location WHERE loc='".$mas2['location']['city']."'");
		    	if (mysql_num_rows($rru)==0)
		    	{
		        	$content_tw_loc=parseUrl('http://geocode-maps.yandex.ru/1.x/?geocode='.urlencode($mas2['location']['city']).'&key=ANpUFEkBAAAAf7jmJwMAHGZHrcKNDsbEqEVjEUtCmufxQMwAAAAAAAAAAAAvVrubVT4btztbduoIgTLAeFILaQ==&results=1');
		        	//echo urlencode($user['location']);
		        	$regextw='/<pos>(?<loc_tw>.*?)<\/pos>/is';
		        	preg_match_all($regextw,$content_tw_loc,$out_tw);
			    	$rru=$db->query('INSERT INTO robot_location (loc,loc_coord) VALUES (\''.$mas2['location']['name'].'\',\''.$out_tw['loc_tw'][0].'\')');
		        }
		        else
		        {
		        	$rru1=$db->fetch($rru);
		        	$out_tw['loc_tw'][0]=$rru1['loc_coord'];
		        }
				echo '|'.$mas2['location']['name'].' '.$out_tw['loc_tw'][0]."|\n";
               //$db->query('INSERT INTO robot_blogs2 (blog_link, blog_login,blog_location,blog_readers) values ("facebook.com","'.$mas1[from][id].'","'.$out_tw['loc_tw'][0].'",0)');
            	$sel="SELECT * FROM robot_blogs2 WHERE blog_login='".$nick."' AND blog_link='facebook.com'";
            	$ord=$db->query($sel);
				if ($ord['blog_location']=='')
				{
					if ($out_tw['loc_tw'][0]!='')
					{
						$regex='/(?<ch>\-?\d*?)\.(?<d>\d)/is';
						echo $key;
						preg_match_all($regex,$out_tw['loc_tw'][0],$out);
						$twl=$out['ch'][0].'.'.$out['d'][0].' '.$out['ch'][1].'.'.$out['d'][1];
					}
					else
					{
						$twl='';
					}
       				$upquery="UPDATE robot_blogs2 SET blog_link='".$hn."', blog_location='".$twl."',blog_readers=0 ,blog_last_update=".time().",blog_nick='".$mas2['name']."' WHERE blog_login='".$nick."' AND blog_link='facebook.com'";
				}
				else
				{
	            	$upquery="UPDATE robot_blogs2 SET blog_link='".$hn."',blog_readers=0 ,blog_last_update=".time().",blog_nick='".$mas2['name']."' WHERE blog_login='".$nick."' AND blog_link='facebook.com'";
				}
            	$db->query($upquery);
            	//echo $upquery;
               echo 'add: '.$mas1[from][id].' '.($mas2[location][name])." [facebook.com]\n";
            }
  			echo $upquery;
     }
       elseif ($hn=='vkontakte.ru') 
       {
	   	   //$link="http://vkontakte.ru/note341_10136133";
           //$link="http://vkontakte.ru/id29237?status=179";
           /*$regexy="/note(?<vk_name>.*?)\_/is";
           preg_match_all($regexy,$link,$out);
           if ($out[vk_name][0]=='')
           {
	           $regexy="/id(?<vk_name>.*?)\?/is";
               preg_match_all($regexy,$link,$out);
           }*/
           $out['vk_name'][0]=$blog['blog_login'];
           $VK = new vkapi($api_id, $secret_key);
           $resp = $VK->api('getProfiles', array('uids'=>$out['vk_name'][0],'fields'=>'city,nickname,lists'));
           $nicks=$resp['response'][0]['first_name']." ".$resp['response'][0]['last_name'];
           $id_cit=$resp['response'][0]['city'];
           $resp = $VK->api('getCities',array('cids'=>$id_cit));
           $name_cit=$resp['response'][0]['name'];
           $tw=$db->query('SELECT * FROM robot_blogs2 WHERE blog_link="vkontakte.ru" and blog_login="'.$out['vk_name'][0].'"');
           if (mysql_num_rows($tw)==0)
           {
	       		/*$content_tw_loc=parseUrl('http://geocode-maps.yandex.ru/1.x/?geocode='.urlencode($name_cit).'&key=ANpUFEkBAAAAf7jmJwMAHGZHrcKNDsbEqEVjEUtCmufxQMwAAAAAAAAAAAAvVrubVT4btztbduoIgTLAeFILaQ==&results=1');
                $regextw='/<pos>(?<loc_tw>.*?)<\/pos>/is';
                preg_match_all($regextw,$content_tw_loc,$out_tw);*/
		    	$rru=$db->query("SELECT * FROM robot_location WHERE loc='".$name_cit."'");
		    	if (mysql_num_rows($rru)==0)
		    	{
		        	$content_tw_loc=parseUrl('http://geocode-maps.yandex.ru/1.x/?geocode='.urlencode($name_cit).'&key=ANpUFEkBAAAAf7jmJwMAHGZHrcKNDsbEqEVjEUtCmufxQMwAAAAAAAAAAAAvVrubVT4btztbduoIgTLAeFILaQ==&results=1');
		        	//echo urlencode($user['location']);
		        	$regextw='/<pos>(?<loc_tw>.*?)<\/pos>/is';
		        	preg_match_all($regextw,$content_tw_loc,$out_tw);
			    	$rru=$db->query('INSERT INTO robot_location (loc,loc_coord) VALUES (\''.$name_cit.'\',\''.$out_tw['loc_tw'][0].'\')');
		        }
		        else
		        {
		        	$rru1=$db->fetch($rru);
		        	$out_tw['loc_tw'][0]=$rru['loc_coord'];
		        }
				echo $name_cit.' '.$out_tw['loc_tw'][0]."\n";
                //$db->query('INSERT INTO robot_blogs2 (blog_link, blog_login,blog_location,blog_readers) values ("vkontakte.ru","'.$out[vk_name][0].'","'.$out_tw['loc_tw'][0].'",0)');
            	$sel="SELECT * FROM robot_blogs2 WHERE blog_login='".$nick."' AND blog_link='vkontakte.ru'";
            	$ord=$db->query($sel);
				if ($ord['blog_location']=='')
				{
					/*if ($out_tw['loc_tw'][0]!='')
					{
						$regex='/(?<ch>\-?\d*?)\.(?<d>\d)/is';
						echo $key;
						preg_match_all($regex,$out_tw['loc_tw'][0],$out);
						$twl=$out['ch'][0].'.'.$out['d'][0].' '.$out['ch'][1].'.'.$out['d'][1];
					}
					else
					{
						$twl='';
					}*/
					$cont=parseUrl('http://vkontakte.ru/id'.$nick);//http://vkontakte.ru/id9884728-косяк
					$cont=iconv('windows-1251','UTF-8',$cont);
					//echo $cont;
					$regex='/<div class=\"label fl\_l\">Город\:<\/div>.*?<div class=\"labeled fl\_l\">(?<data>.*?)<\/div>/is';
					preg_match_all($regex,$cont,$out);
					if ($out['data'][0]=='')
					{
						$regex='/<div class=\"label fl\_l\">Родной город\:<\/div>.*?<div class=\"labeled fl\_l\">(?<data>.*?)<\/div>/is';
						preg_match_all($regex,$cont,$out);
					}
					$content_tw_loc=parseUrl('http://geocode-maps.yandex.ru/1.x/?geocode='.urlencode($out['data'][0]).'&key=ANpUFEkBAAAAf7jmJwMAHGZHrcKNDsbEqEVjEUtCmufxQMwAAAAAAAAAAAAvVrubVT4btztbduoIgTLAeFILaQ==&results=1');
					$regextw='/<pos>(?<loc_tw>.*?)<\/pos>/is';
					preg_match_all($regextw,$content_tw_loc,$out_tw);
					//echo $out_tw['loc_tw'][0];
					if ($out_tw['loc_tw'][0]!='')
					{
						$regex='/(?<ch>\-?\d*?)\.(?<d>\d)/is';
						echo ' '.$key.' ';
						preg_match_all($regex,$out_tw['loc_tw'][0],$out);
						$twl=$out['ch'][0].'.'.$out['d'][0].' '.$out['ch'][1].'.'.$out['d'][1];
						echo $twl.' ';
				       	//$upquery='UPDATE robot_blogs2 SET blog_location=\''.$twl.'\' WHERE blog_id='.$blog['blog_id']."\n";
						//echo $upquery;
				    	//$db->query($upquery);
					}
         			$upquery="UPDATE robot_blogs2 SET blog_location='".$twl."' , blog_link='".$hn."',blog_readers=0 ,blog_last_update=".time().",blog_nick='".$nicks."' WHERE blog_login='".$nick."' AND blog_link='vkontakte.ru'";
				}
				else
				{
	            	$upquery="UPDATE robot_blogs2 SET blog_link='".$hn."',blog_readers=0 ,blog_last_update=".time().",blog_nick='".$nicks."' WHERE blog_login='".$nick."' AND blog_link='vkontakte.ru'";
				}
            	$db->query($upquery);
            	//echo $upquery;
                echo 'add: '.$out['vk_name'][0].' '.($name_cit)." [vkontakte.ru]\n";
           }
        	sleep(1);
			echo $upquery;
		}

		$count++;
	}


	unset($blog);
}
}
?>
