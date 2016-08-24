<?php

require_once('tmhOAuth.php');
require_once('tmhUtilities.php');

if( is_file('/var/www/com/config_server.php') )
{
  require_once('/var/www/com/config_server.php');
  $twitter_keys = file_get_contents('http://188.120.239.225/api/service/get_at.php?server='.$config_server['server_ip'].'&type=tw');
  $twitter_keys = json_decode($twitter_keys, true);
  $tmhOAuth = new tmhOAuth(array(
    'consumer_key' => $twitter_keys['consumer_key'],
    'consumer_secret' => $twitter_keys['consumer_secret'],
  ));
}
else
{
  $tmhOAuth = new tmhOAuth(array(
    'consumer_key' => 'L45DIge1QusRfCb4TP6LxRpY9',
    'consumer_secret' => '7hvKk2eha9vr3RbkMTiTmq1TqyK8Fc8opIxjbHRTqppF26gKIn',
//  'consumer_key' => '1dMbekEbhTMziEU0Shq3zLFoT',
//  'consumer_secret' => 'ieFF7bmiT5YJ464q2pS02STSIAvHPQx58Y6YukGcOHNJKAzMcQ',
  ));
}


session_start();

function outputError()
{
  header('Location: ' . tmhUtilities::php_self() . '?wipe=1');
  die();
}

function logout()
{
  global $_SESSION;
  session_destroy();
  unset($_SESSION['access_token']);
  setcookie('twitter_user', null, 0, '/');
  die();
}

function wipe()
{
  global $_SESSION;
  session_destroy();
  unset($_SESSION['access_token']);
  setcookie('twitter_user', null, 0, '/');
  echo '<script type="text/javascript">window.close();</script>';
  die();
}

// Step 1: Request a temporary token
function request_token($tmhOAuth)
{
  $code = $tmhOAuth->request('POST', $tmhOAuth->url('oauth/request_token', ''), array(
    'oauth_callback' => tmhUtilities::php_self()
  ));

  if ($code == 200)
  {
    $_SESSION['oauth'] = $tmhOAuth->extract_params($tmhOAuth->response['response']);
    authorize($tmhOAuth);
  }
  else
  {
    outputError();
  }
}

// Step 2: Direct the user to the authorize web page
function authorize($tmhOAuth)
{
  $authurl = $tmhOAuth->url("oauth/authorize", '') . "?oauth_token={$_SESSION['oauth']['oauth_token']}";
  header("Location: {$authurl}");
}

// Step 3: This is the code that runs when Twitter redirects the user to the callback. Exchange the temporary token for a permanent access token
function access_token($tmhOAuth)
{
  $tmhOAuth->config['user_token'] = $_SESSION['oauth']['oauth_token'];
  $tmhOAuth->config['user_secret'] = $_SESSION['oauth']['oauth_token_secret'];

  $code = $tmhOAuth->request('POST', $tmhOAuth->url('oauth/access_token', ''), array(
    'oauth_verifier' => $_REQUEST['oauth_verifier']
  ));

  if ($code == 200)
  {
    $_SESSION['access_token'] = $tmhOAuth->extract_params($tmhOAuth->response['response']);
    unset($_SESSION['oauth']);
    header('Location: ' . tmhUtilities::php_self());
  }
  else
  {
    outputError();
  }
}

// Step 4: Now the user has authenticated, do something with the permanent token and secret we received
function verify_credentials($tmhOAuth)
{
  $twitter_user = json_decode($_COOKIE['twitter_user'], true);
  if ($twitter_user == null)
  {
    $twitter_user = array();
  }
  $tmhOAuth->config['user_token'] = $_SESSION['access_token']['oauth_token'];
  $tmhOAuth->config['user_secret'] = $_SESSION['access_token']['oauth_token_secret'];

  $code = $tmhOAuth->request('GET', $tmhOAuth->url('1.1/account/verify_credentials'));
  if ($code == 200)
  {
    $user = json_decode($tmhOAuth->response['response'], true);
    $twitter_user[$user['id']] = array(
      'user_id' => $user['id'],
      'user_token' => $tmhOAuth->config['user_token'],
      'user_secret' => $tmhOAuth->config['user_secret'],
      'consumer_key' => $tmhOAuth->config['consumer_key'],
      'consumer_secret' => $tmhOAuth->config['consumer_secret'],
      'name' => rawurlencode($user['name']),
      'screen_name' => $user['screen_name'],
      'avatar' => $user['profile_image_url']
    );
    setcookie('twitter_user', json_encode($twitter_user), 0, '/');
    echo '<script type="text/javascript">window.close();</script>';
//    if ($_COOKIE['twitter_answer'] != null)
//    {
//      answer($tmhOAuth, rawurldecode($_COOKIE['twitter_answer']));
//    }
//    else
//    {
//      echo '<script type="text/javascript">window.close();</script>';
//    }
  }
  else
  {
    request_token($tmhOAuth);
  }
}


function answer($tmhOAuth, $answer)
{
//  setcookie('twitter_answer', rawurlencode($answer), 0, '/');
  $answer = json_decode($answer, true);
  $user_data = current(json_decode($_COOKIE['twitter_user'], true));

  $tmhOAuth->config['user_token'] = $user_data['user_token'];
  $tmhOAuth->config['user_secret'] = $user_data['user_secret'];

  $link_ar = explode('/', str_replace('http://twitter.com/', '', $answer['link']));
  $user_name = $link_ar[count($link_ar) - 3];
  $message_id = $link_ar[count($link_ar) - 1];
  $message = '@' . $user_name . ' ' . $answer['text'];
  $code = $tmhOAuth->request('POST', $tmhOAuth->url('1.1/statuses/update.json', ''), array(
      'status' => $message,
      'in_reply_to_status_id' => $message_id
    ));

//  setcookie('twitter_answer', null, 0, '/');

  echo json_encode(array('status' => $code));
  die;
}

if (isset($_REQUEST['start'])) :
  request_token($tmhOAuth);
elseif (isset($_REQUEST['oauth_verifier'])) :
  access_token($tmhOAuth);
elseif (isset($_REQUEST['verify'])) :
  verify_credentials($tmhOAuth);
elseif (isset($_REQUEST['wipe'])) :
  wipe();
elseif (isset($_REQUEST['logout'])) :
  logout();
elseif (isset($_REQUEST['answer'])) :
  answer($tmhOAuth, $_REQUEST['answer']);
endif;

if (($_SESSION['access_token']['oauth_token'] != '') && ($_SESSION['access_token']['oauth_token_secret'] != '') && ($_GET['verify'] == ''))
{
  header('Location: ?verify=1');
}