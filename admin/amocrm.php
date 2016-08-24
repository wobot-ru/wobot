<?php
function query($url,$post)
{
	$ch = curl_init();

	curl_setopt($ch, CURLOPT_URL,$url);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS,$post);

	// receive server response ...
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

	$server_output = curl_exec ($ch);

	curl_close ($ch);

	return $server_output;
}
/*$auth=array(
	'USER_LOGIN'=>'r@wobot.co',
	'USER_HASH'=>'19eb507bf7bb30b6075acce3398d2758'
	);
print_r(query('https://wobot2013.amocrm.ru/private/api/auth.php',$auth));*/
 
				$contact=Array(
			  'USER_LOGIN'=>'r@wobot.co',
			  'USER_HASH'=>'19eb507bf7bb30b6075acce3398d2758',
			  'ACTION'=>'ADD_PERSON',
			  'contact'=>array(
			  'person_name'          => 'тест',
			  'person_company_name'  => 'тест',
			  'person_company_id'    => '1',
			  'contact_data'         => Array (
			    'phone_numbers' => Array (
			      0 => Array ('number'    => 'тест'),
			      1 => Array ('location'  => 'Work')
			    ),
			    'email_addresses' => Array (
			      0 => Array('address'   => 'test@test.ru'),
			      1 => Array('location'  => 'Work')
			    )
			  ),
			  'main_user_id'    => '95928',
			  'tags'            => 'демо')
			);
			print_r(query('https://wobot2013.amocrm.ru/private/api/contact_add.php',http_build_query($contact)));
?>