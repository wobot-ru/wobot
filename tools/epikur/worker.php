<?

function get_worker()
{
	$worker[]='localhost';
	$worker[]='localhost';
	$worker[]='localhost';
	$worker[]='localhost';
	$worker[]='ec2-79-125-49-85.eu-west-1.compute.amazonaws.com';
	$worker[]='ec2-54-228-55-43.eu-west-1.compute.amazonaws.com';
	$worker[]='ec2-54-228-217-24.eu-west-1.compute.amazonaws.com';
	$worker[]='bmstu.wobot.ru';
	shuffle($worker);
	return $worker;
}

// print_r(get_worker());

?>