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
  'consumer_key' => 'NKzRUfXmozYY51m6yODdTQ',
  'consumer_secret' => 'zW7XoEi3MgdH1SWUbIaYm2IMJsXH1M4zyN2FbZZ8ypE',
  'user_token' => '275446633-trkOnzx41KoEzwiiTHw1oCM4aexr05i3ZRH5ELtc',
  'user_secret' => 'NvJNJyrY0yaf1anfQsBiF9r7psHfl9tw530wsSYXkko',
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
while(1)
{
$i=0;

///$ressec=$db->query('SELECT * FROM robot_blogs2 WHERE blog_last_update=0 OR blog_last_update<'.(time()-$deltatime*86400));
//echo 'SELECT * FROM robot_blogs2 WHERE blog_last_update=0 OR blog_last_update<'.(time()-$deltatime*86400);
//$blog['blog_link']='vkontakte.ru';
//$blog['blog_login']='91553410';
//echo $blog['blog_login'];
//echo 'scanning all orders: '.mysql_num_rows($ressec)." for new accounts\n";
$mode='wb';
//while($blog=$db->fetch($ressec))
{
	//opening post file to generate cash files ////////////////////////////////////////////////////////////
	echo 'order: '.$blog['order_id']."\n";
	$count=0;
	$hn=$blog['blog_link'];
	$nick=$blog['blog_login'];
	$nick='iErana';
	$hn='twitter.com';
	echo $nick." ".$blog['blog_link']."\n";
	{
		if ($hn=='twitter.com') 
        {
            //$tw=$db->query('SELECT * FROM robot_blogs2 WHERE blog_link="twitter.com" and blog_login="'.$nick.'"');
            //if (mysql_num_rows($tw)==0)
            {
	            $tmhOAuth->request('POST', $tmhOAuth->url(/*'/1/users/show/'.$nick*//*'/1/statuses/retweets.xml?id=52364288387584000'*//*'/1/statuses/retweets/53365011497750529.json'*/'/1/statuses/retweets/55721028554465280.json', 'json'));
	            echo $tmhOAuth->response['code']; 
	            echo 'gg';
                if ($tmhOAuth->response['code'] == 200) 
                {
		            //$tmhOAuth->request('GET', '/1/statuses/retweets/55493678248103936.json');
                	echo 'gg';
			    	$user=json_decode($tmhOAuth->response['response'],true);
			    	print_r($user);
			        $content_tw_loc=parseUrl('http://geocode-maps.yandex.ru/1.x/?geocode='.urlencode($user['location']).'&key=ANpUFEkBAAAAf7jmJwMAHGZHrcKNDsbEqEVjEUtCmufxQMwAAAAAAAAAAAAvVrubVT4btztbduoIgTLAeFILaQ==&results=1');
			        //echo urlencode($user['location']);
			        $regextw='/<pos>(?<loc_tw>.*?)<\/pos>/is';
			        preg_match_all($regextw,$content_tw_loc,$out_tw);
			        //$db->query('INSERT INTO robot_blogs2 (blog_link, blog_login,blog_location,blog_readers) values ("twitter.com","'.$nick.'","'.$out_tw['loc_tw'][0].'",'.intval($user["followers_count"]).')');
	            	//$upquery="UPDATE robot_blogs2 SET blog_link='".$hn."', blog_location='".$out_tw['loc_tw'][0]."',blog_readers=".intval($user["followers_count"]).",blog_last_update=".time().",blog_nick='".$nick."' WHERE blog_login='".$nick."' AND blog_link='twitter.com'";
	            	//echo $upquery;
	            	//$db->query($upquery);
			        echo 'add: '.$nick.' '.$out_tw['loc_tw'][0].' '.intval($user["followers_count"])." ".time();
			    } 
			    else 
			    {
			    	$tmhOAuth->pr(htmlentities($tmhOAuth->response['response']));
			    }
			    sleep(11);
            }
       }
       elseif ($hn=='livejournal.com') 
       {
	       //http://knowledgeaction.livejournal.com/21962.html
           //http://community.livejournal.com/chgk_aic/28944.html
           $regexy="/\/\/(?<gg_id>.*?)\./is";
           preg_match_all($regexy,$link,$out);
           $gg_id=$out[gg_id][0];
			$gg_id=$blog['blog_login'];		
           //print_r($out);
            //echo $gg_id;
            $live_text=parseUrl('http://'.$gg_id.'.livejournal.com/data/foaf');
            sleep(1);
            $regexy="/<foaf\:nick>(?<lnick>.*?)<\/foaf:nick>.*?<ya\:city\sdc\:title=\"(?<lcity>.*?)\"/is";
            preg_match_all($regexy,$live_text,$out);
            /*$regexy="/<foaf:nick>(?<lnick>.*?)<\/foaf:nick>/is";
            preg_match_all($regexy,$live_text,$out1);*/
            $live_text=parseUrl('http://www.livejournal.com/misc/fdata.bml?user='.$gg_id);
            sleep(1);
            //echo $live_text;
            if ($out[gg_id][0]!="community")
            {
	            $live_text=str_replace("# Note: Polite data miners cache on their end.  Impolite ones get banned.","",$live_text);
                $regexy="/.\s(?<llfoll>.*?)\s/is";
                preg_match_all($regexy,$live_text,$outtt);
            }
            //$tw=$db->query('SELECT * FROM robot_blogs2 WHERE blog_link="livejournal.com" and blog_login="'.$gg_id.'"');
            //if (mysql_num_rows($tw)==0)
            {
	            $content_tw_loc=parseUrl('http://geocode-maps.yandex.ru/1.x/?geocode='.$out[lcity][0].'&key=ANpUFEkBAAAAf7jmJwMAHGZHrcKNDsbEqEVjEUtCmufxQMwAAAAAAAAAAAAvVrubVT4btztbduoIgTLAeFILaQ==&results=1');
                $regextw='/<pos>(?<loc_tw>.*?)<\/pos>/is';
                preg_match_all($regextw,$content_tw_loc,$out_tw);
                //$db->query('INSERT INTO robot_blogs2 (blog_link, blog_login,blog_location,blog_readers) values ("livejournal.com","'.$gg_id.'","'.$out_tw['loc_tw'][0].'",'.count($outtt[llfoll]).')');
            	$upquery="UPDATE robot_blogs2 SET blog_link='".$hn."', blog_location='".$out_tw['loc_tw'][0]."',blog_readers=".count($outtt[llfoll]).",blog_last_update=".time().", blog_nick='".$nick."' WHERE blog_login='".$nick."' AND blog_link='livejournal.com'";
            	$db->query($upquery);
                echo 'add: '.$gg_id.' '.count($outtt[llfoll]).' '.($out[lcity][0])." [livejournal.com]\n";
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
           if (mysql_num_rows($tw)==0)
           {
	           $content_tw_loc=parseUrl('http://geocode-maps.yandex.ru/1.x/?geocode='.urlencode($mas2[location][name]).'&key=ANpUFEkBAAAAf7jmJwMAHGZHrcKNDsbEqEVjEUtCmufxQMwAAAAAAAAAAAAvVrubVT4btztbduoIgTLAeFILaQ==&results=1');
               $regextw='/<pos>(?<loc_tw>.*?)<\/pos>/is';
               preg_match_all($regextw,$content_tw_loc,$out_tw);
               //$db->query('INSERT INTO robot_blogs2 (blog_link, blog_login,blog_location,blog_readers) values ("facebook.com","'.$mas1[from][id].'","'.$out_tw['loc_tw'][0].'",0)');
            	$upquery="UPDATE robot_blogs2 SET blog_link=".$hn.", blog_location='".$out_tw['loc_tw'][0]."',blog_readers=0 ,blog_last_update=".time().",blog_nick='".$mas2['name']."' WHERE blog_login='".$nick."' AND blog_link='facebook.com'";
            	$db->query($upquery);
               echo 'add: '.$mas1[from][id].' '.($mas2[location][name])." [facebook.com]\n";
            }
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
           $resp = $VK->api('getUserSettings',array());
           print_r($resp);
           $resp = $VK->api('getProfiles', array('uids'=>'1555432'/*$out['vk_name'][0]*/,'fields'=>'city,nickname,lists'));
           //$resp = $VK->api('wall.getById',array('posts'=>'5123187_1528'));
           print_r($resp);
           $nicks=$resp['response'][0]['first_name']." ".$resp['response'][0]['last_name'];
           $id_cit=$resp['response'][0]['city'];
           $resp = $VK->api('getCities',array('cids'=>$id_cit));
           $name_cit=$resp['response'][0]['name'];
           $tw=$db->query('SELECT * FROM robot_blogs2 WHERE blog_link="livejournal.com" and blog_login="'.$out['vk_name'][0].'"');
           if (mysql_num_rows($tw)==0)
           {
	       		$content_tw_loc=parseUrl('http://geocode-maps.yandex.ru/1.x/?geocode='.urlencode($name_cit).'&key=ANpUFEkBAAAAf7jmJwMAHGZHrcKNDsbEqEVjEUtCmufxQMwAAAAAAAAAAAAvVrubVT4btztbduoIgTLAeFILaQ==&results=1');
                $regextw='/<pos>(?<loc_tw>.*?)<\/pos>/is';
                preg_match_all($regextw,$content_tw_loc,$out_tw);
                //$db->query('INSERT INTO robot_blogs2 (blog_link, blog_login,blog_location,blog_readers) values ("vkontakte.ru","'.$out[vk_name][0].'","'.$out_tw['loc_tw'][0].'",0)');
            	//$upquery="UPDATE robot_blogs2 SET blog_link='".$hn."', blog_location='".$out_tw['loc_tw'][0]."',blog_readers=0 ,blog_last_update=".time().",blog_nick='".$nicks."' WHERE blog_login='".$nick."' AND blog_link='vkontakte.ru'";
            	//$db->query($upquery);
                echo 'add: '.$out['vk_name'][0].' '.($name_cit)." [vkontakte.ru]\n";
           }
        	sleep(1);
		}

		$count++;
	}


	unset($blog);
}
}
?>
