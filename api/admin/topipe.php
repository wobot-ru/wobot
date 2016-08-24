<?

require_once('/var/www/com/config.php');
require_once('/var/www/com/db.php');
require_once('/var/www/com/auth.php');

$db = new database();
$db->connect();

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

$user_company=$_POST['user_company'];
$user_fio=$_POST['user_name'];
$user_email=$_POST['user_email'];
$user_phone=$_POST['user_contact'];
$contact=Array(
  'USER_LOGIN'=>'r@wobot.co',
  'USER_HASH'=>'19eb507bf7bb30b6075acce3398d2758',
  'person_name'          => $_POST['user_name'],
  'person_position'      => '',
  'person_company_name'  => $_POST['user_company'],
  'person_company_id'    => '',
  'contact_data'         => Array (
    'phone_numbers' => Array (
      0 => Array ('number'    => $_POST['user_contact']),
      1 => Array ('location'  => 'Work'),
      2 => Array ('number'    => ''),
      3 => Array ('location'  => 'Mobile')
    ),
    'email_addresses' => Array (
      0 => Array('address'   => $_POST['user_email']),
      1 => Array('location'  => 'Work')
    ),
    'web_addresses' => Array(
      0 => Array('url' => '')
    ),
    'addresses' => Array(
      'street' => 'Адрес'
    ),
    'instant_messengers' => Array(
      0 => Array('address'   => ''),
      1 => Array('protocol'  => 'Skype')
    )
  ),
  'main_user_id'    => '95928',
  'tags'            => 'демо'
);
print_r(query('https://wobot2013.amocrm.ru/private/api/contact_add.php',$contact));

?>