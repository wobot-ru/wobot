<?

function get_worker()
{
	$worker[]='ec2-54-228-217-24.eu-west-1.compute.amazonaws.com';
	shuffle($worker);
	return $worker;
}

// print_r(get_worker());

?>