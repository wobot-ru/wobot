<?

require_once('/var/www/social/twitter-client/tmhOAuth.php');
require_once('/var/www/social/twitter-client/tmhUtilities.php');

$tmhOAuth = new tmhOAuth(array(
  'consumer_key'    => 'M8RnwsZemYh62YS1dRW1Q',
  'consumer_secret' => 'ovIH2vVz82aMwaNpuBLm3i3nOUKvwgY8lR9M1z20E',
));

function request_token($tmhOAuth) {
  $code = $tmhOAuth->request('POST', $tmhOAuth->url('oauth/request_token', ''), array(
    'oauth_callback' => 'oob',
  ));
  if ($code == 200) {
    $oauth_creds = $tmhOAuth->extract_params($tmhOAuth->response['response']);
    $tmhOAuth->config['user_token']  = $oauth_creds['oauth_token'];
    $tmhOAuth->config['user_secret'] = $oauth_creds['oauth_token_secret'];
    $url = $tmhOAuth->url('oauth/authorize', '') . "?oauth_token={$oauth_creds['oauth_token']}";
  } else { die(); }
}

function access_token($tmhOAuth, $pin) {
  $code = $tmhOAuth->request('POST', $tmhOAuth->url('oauth/access_token', ''), array(
    'oauth_verifier' => trim($pin)
  ));
  if ($code == 200) {
    $oauth_creds = $tmhOAuth->extract_params($tmhOAuth->response['response']);
  } else {
  	$out['tokens']['consumer_key']=$tmhOAuth->config['consumer_key'];
  	$out['tokens']['consumer_secret']=$tmhOAuth->config['consumer_secret'];
  	$out['tokens']['user_token']=$tmhOAuth->config['user_token'];
  	$out['tokens']['user_secret']=$tmhOAuth->config['user_secret'];
  	$out['status']='ok';
  	echo json_encode($out);
  	die();
   //echo 'consumer_key: '.$tmhOAuth->config['consumer_key'].'<br>consumer_secret: '.$tmhOAuth->config['consumer_secret'].'<br>user_token: '.$tmhOAuth->config['user_token'].'<br>user_secret: '.$tmhOAuth->config['user_secret'];
  die(); }
}

if ($_GET['method']!='callback')
{
	$code = $tmhOAuth->request('POST', $tmhOAuth->url('oauth/request_token', ''), array(
  	'oauth_callback' => 'oob',
	));

	if ($code == 200) {
	  $oauth_creds = $tmhOAuth->extract_params($tmhOAuth->response['response']);

	  // update with the temporary token and secret
	  $tmhOAuth->config['user_token']  = $oauth_creds['oauth_token'];
	  $tmhOAuth->config['user_secret'] = $oauth_creds['oauth_token_secret'];

	  $url = $tmhOAuth->url('oauth/authorize', '') . "?oauth_token={$oauth_creds['oauth_token']}";
	  $out['url']=$url;
	  $out['status']='ok';
	  echo json_encode($out);
	  die();
	}
	else
	{
		$out['status']=$code;
		echo json_encode($out);
		die();
	}

}
else
{
	//$_POST=$_GET;
  request_token($tmhOAuth);
  access_token($tmhOAuth, trim($_POST['pin']));
}

?>