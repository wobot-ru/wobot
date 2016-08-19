<?php
$couch_dsn = "http://localhost:5984/";
$couch_db = "orders";

require_once "/var/www/tools/couch/couch.php";
require_once "/var/www/tools/couch/couchClient.php";
require_once "/var/www/tools/couch/couchDocument.php";

$client = new couchClient($couch_dsn,$couch_db);
$song = new stdClass();
/*$song->_id = "in_the_meantime";
$song->title = "In the Meantime";
$song->album = "Resident Alien";
$song->artist = "Space Hog";
$song->genre = "Alternative";
$song->year = 1995;*/
$mas=array('1','2','dsadasd');
$song->_id = '311';
/*$descriptorspec=array(
	0 => array("pipe","r"),
	1 => array("pipe","w"),
	2 => array("file", "/tmp/error-output.txt", "a")
	);

$cwd='/var/www/tools/';
$end=array('');
//$pipes=json_encode($outmas);
$process=proc_open('curl -X DELETE \'http://localhost:5984/orders/310?rev=3-6ff628b7aae59e742ca90c92c3828584\'',$descriptorspec,$pipes,$cwd,$end);
//echo "\n".$row['post_link']."\n";
if (is_resource($process))
{
	fwrite($pipes[0], json_encode($outmas));
	fclose($pipes[0]);
	//echo $return_value;
	//print_r($pipes);
	$fulltext=stream_get_contents($pipes[1]);
	$return_value=proc_close($process);

	echo $fulltext;
}*/
//$client->deleteDocs('3-6ff628b7aae59e742ca90c92c3828584');
//$song->_rev='3-d26aa06b624eacf705feaa99b547f4df';
//$song->_handlers = 'order';
for ($i=0;$i<500000;$i++)
{
	$mas[]=md5($i);
}
//print_r($mas);
//die();
$song->data=json_encode($mas);	
//$client->deleteDocs ($song);

try {
	$response = $client->storeDoc($song);
} catch (Exception $e) {
	echo "Error: ".$e->getMessage()." (errcode=".$e->getCode().")\n";
	exit(1);
}
print_r($response);
die();
try {
	$response = $client->updateDoc('_bulk_docs','qwe',array(),$song->data);
	//$client->db('orders')->update('qwe', array('password' => 'g2'));
	
} catch (Exception $e) {
	echo "Error: ".$e->getMessage()." (errcode=".$e->getCode().")\n";
	exit(1);
}
print_r($response);


//
/*
try {
	$info = $client->getDatabaseInfos();
} catch (Exception $e) {
	echo "Error:".$e->getMessage()." (errcode=".$e->getCode().")\n";
	exit(1);
}
print_r($info);
*/

try {
	$doc = $client->getDoc('310');
} catch (Exception $e) {
	if ( $e->code() == 404 ) {
		echo "Document not found\n";
	} else {
		echo "Error: ".$e->getMessage()." (errcode=".$e->getCode().")\n";
	}
	exit(1);
}
print_r($doc);
echo $doc->_rev;
?>