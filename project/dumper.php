<?
$link = mysql_connect('localhost', 'wobot', 'JFHsvosd');
/*mysql_query("SET character_set_results=utf8", $link);
mysql_query("SET character_set_client=utf8", $link);
mysql_query("SET character_set_connection=utf8", $link);
mb_language('uni');
mb_internal_encoding('UTF-8');*/
//mysql_select_db($config['db']['database'], $link);
$db_selected = mysql_select_db('wobot', $link);
/*function dataadd($keyword_id,$class,$source,$type,$rtype,$date,$data)
{
$result = mysql_query('INSERT INTO `starindex`.`data` (
`keyword_id`,`data_class`,`data_source`,`data_type`,`data_rtype`,`data_date`,`data_data`
)
VALUES
('.intval($keyword_id).',"'.$class.'","'.$source.'","'.$type.'","'.$rtype.'","'.$date.'","'.addslashes($data).'")');
}*/
/*while ($row = mysql_fetch_assoc($result)) {
	//print_r($row);
$name_qw=$row['type'];
}*/
$table='blog_posts';
//$result = mysql_query('SELECT * FROM blog_full_com WHERE ful_com_order_id=310');
//$result=mysql_query('SELECT * from blog_post as p LEFT JOIN robot_blogs2 AS b ON p.blog_id=b.blog_id LEFT JOIN blog_full_com AS f ON f.ful_com_post_id=p.post_id where order_id=467 OR order_id=479 OR order_id=480 OR order_id=482 OR order_id=483 OR order_id=484 OR order_id=478');
$result=mysql_query('SELECT (post_link) from blog_post ORDER BY post_time DESC LIMIT 2000');
//$last=0;
//$block=0;
while($row=mysql_fetch_assoc($result))
{
	//Full Texts 	about_id 	keyword_id 	wiki_year 	wiki_country 	wiki_genre 	discogs_varnames 	discogs_albums 	funky_albums
	/*$q='';
	foreach ($row as $key=>$value) {
		if (strlen($q)>0) $q.=', ';
		$q.=$key.'="'.addslashes($value).'"';
	}*/
	/*if ($last==0) { $last=$row['post_id']; echo $last."\n"; }
	if (($row['post_id']-$last)>3) { echo $block.' '.$last."\n"; $block=0; }
	$last=$row['post_id'];
	$block++;*/
	echo 'INSERT INTO links (post_link) values ("'.addslashes($row['post_link']).'");
';
}
?>