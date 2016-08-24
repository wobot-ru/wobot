<?php
// https://github.com/datasift/datasift-php
require 'lib/datasift.php';
$user = new DataSift_User('rcp', '518a86cccf877b998ff5df98683462b6');
$consumer = $user->getConsumer(DataSift_StreamConsumer::TYPE_HTTP, '506519aa36eac4e174affc15178d3707', 'display', 'stopped');
$consumer->consume();

function display($consumer, $interaction) {
	//echo $interaction['interaction']['content']."\n--\n";
	print_r($interaction);
	echo "\n\n";
}
function stopped($consumer, $reason) {
	echo "\nStopped: $reason\n\n";
}
?>