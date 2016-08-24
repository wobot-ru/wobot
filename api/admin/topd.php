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
    echo $server_output;
    return $server_output;
}

if ($_GET['toPD']!='')
{
  $qustopd=$db->query('SELECT * FROM users WHERE user_id='.intval($_GET['toPD']).' LIMIT 1');
  $ustopd=$db->fetch($qustopd);
  //$token='3e36afca3851ac8a611c9f62a34c7ab35b015fbd';
  //$owner_id='71072';
  $user_company=$ustopd['user_company'];
  $user_fio=$ustopd['user_name'];
  $user_email=$ustopd['user_email'];
  $user_phone=$ustopd['user_contact'];
  //$res=json_decode(query('https://api.pipedrive.com/v1/organizations?api_token='.$token,'name='.urlencode($user_company).'&owner_id='.intval($owner_id)),true);
  //$org_id=$res['data']['id'];
  //echo "org_id: ".$org_id."\n";

  //curl --data "name=username&owner_id=71072&email=test%40test.ru&phone=%2B79035386138&org_id=5" https://api.pipedrive.com/v1/persons?api_token=3e36afca3851ac8a611c9f62a34c7ab35b015fbd
  //{"success":true,"data":{"id":9}}
  //$res=json_decode(query('https://api.pipedrive.com/v1/persons?api_token='.$token,'name='.urlencode($user_fio).'&owner_id='.intval($owner_id).'&email='.urlencode($user_email).'&phone='.urlencode($user_phone).'&org_id='.intval($org_id)),true);
  //$person_id=$res['data']['id'];
  //echo "person_id: ".$person_id."\n";

  //curl --data "title=ТЕСТКОМПАНИdeal&value=7500&currency=RUB&user_id=71072&person_id=9&org_id=5&visible_to=0" https://api.pipedrive.com/v1/deals?api_token=3e36afca3851ac8a611c9f62a34c7ab35b015fbd
  //{"success":true,"data":{"id":5}}
  //$res=json_decode(query('https://api.pipedrive.com/v1/deals?api_token='.$token,'title='.urlencode($user_company.' deal').'&value=7500&currency=RUB&user_id='.intval($owner_id).'&person_id='.intval($person_id).'&org_id='.intval($org_id).'&visible_to=0'),true);
  //$deal_id=$res['data']['id'];
  //echo "deal_id: ".$deal_id."\n";
  $db->query('UPDATE users set ref=1 WHERE user_id='.intval($_GET['toPD']).' LIMIT 1');
        $contact=array(
        'USER_LOGIN'=>'account-one@wobot-research.com',
        'USER_HASH'=>'deef2f5f4d6cfdc1862052817b86b430',
        'ACTION'=>'ADD_PERSON',
        'contact'=>array(
        'person_name'          => $ustopd['user_name'],
        'person_position'      => '',
        'person_company_name'  => $ustopd['user_company'],
        'person_company_id'    => '',
        'contact_data'         => Array (
          'phone_numbers' => Array (
            0 => Array ('number'    => $ustopd['user_contact']),
            1 => Array ('location'  => 'Work'),
            2 => Array ('number'    => ''),
            3 => Array ('location'  => 'Mobile')
          ),
          'email_addresses' => Array (
            0 => Array('address'   => $ustopd['user_email']),
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
        )
      );

      query('https://wobot2013.amocrm.ru/private/api/contact_add.php',http_build_query($contact));
      echo '<script>alert("Выполнен экспорт в AmoCRM");</script>';
      //print_r(query('https://wobot2013.amocrm.ru/private/api/contact_add.php',$contact));
}

?>