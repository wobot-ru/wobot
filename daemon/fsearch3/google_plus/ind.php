<?php
    /*
     * Include the Google clientAPI and the Service for Google Plus
     */
    require_once 'src/apiClient.php';
    require_once 'src/contrib/apiPlusService.php';

    /*
     * Start a PHP Based Session, used to help track the state of the transaction
     */
    session_start();

    /*
     * Instantiate the client and the service object.
     * Notice you pass client into the service object
     */
    $client = new apiClient();
    $plus = new apiPlusService($client);

    /*
     * Set your credentials from Part 1 
    */
$client->setApplicationName('google+');
// Visit https://code.google.com/apis/console?api=plus to generate your
// client id, client secret, and to register your redirect uri.
$client->setClientId('844035275813.apps.googleusercontent.com');
$client->setClientSecret('jKMM3_QC2EWsPzbaxRkKgVJy');
$client->setRedirectUri('urn:ietf:wg:oauth:2.0:oob');
$client->setDeveloperKey('AIzaSyB8uR3Pp44Cfj-JvpUipsx7F89zt4tSORM');
   /* $client->setClientId('Client ID');
    $client->setClientSecret('Client Secret');
    $client->setRedirectUri('Redirect URI');
    $client->setDeveloperKey('Developer key');*/

    /*
     * We need to tell Google's Auth servers what we want to access
     * Scopes are pointers to data, you have different scopes for areas of Google
     * If you wanted to access Google+ and Gmail data for this client, you would request both scopes
     */
    /*$client->setScopes(array(
        'https://www.googleapis.com/auth/plus.me'
    ));

    /*
     * First we check to see if the user has requested us to clear there login information
     * We do this by checking our local url for the logout param, and if exists, we clear the access
     * token from our session
    */
   /* if(isset($_REQUEST['logout'])) //GET or POST or COOKIE
    {
		echo 'hh1';
        unset($_SESSION['access_token']);
    }

    /*
     * When the user authenticates your request, Google redirects the client back to this page
     * passing in params in the URL, if the code param is there, it means that they clicked allow
     * and Google is sending back the code telling us to authenticate it.
     * if this is true, we redirect the user to this page, but without the code, storing it in the session
     */
    /*if(isset($_GET['code']))
    {
		echo 'hh2';
        $client->authenticate();
        $_SESSION['access_token'] = $client->getAccessToken();
        header('Location: http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF']);
    }

    /*
     * if all has gone well, the access token should be in the session
     * so we can now tell the client what the valid access token is
     */
   /* if(isset($_SESSION['access_token']))
    {
		echo 'hh3';
        $client->setAccessToken($_SESSION['access_token']);
//echo $_SESSION['access_token'];
    }

    /*
     * the getAccessToken() method will return the token if were authorized to make requests for data
     * This would normally indicate that the user granted us access and there still logged in
     */
//echo '|'.$client->getAccessToken().'|';
$client->authenticate();
$at=$client->getAccessToken();
echo $at.'GGGG';
   // if ($at=$client->getAccessToken())
	if (true)
    {
	//echo 'gg';
        /*
         * Now were logged in, we can perform requests for data, so let's get started with a simple /me
         * Request (https://www.googleapis.com/plus/v1/people/me) which will return a Person Object
         * Person Object {https://developers.google.com/+/api/latest/people#resource}
         */
//$at=json_decode($at,true);
//echo $at['access_token'];
//$client->setAccessToken('{"access_token":"ya29.AHES6ZQE6L3SBc2pB8RqnrMMUQmogWWhdpDK9pPO5J8iO31C08Hx","token_type":"Bearer","expires_in":3600,"id_token":"eyJhbGciOiJSUzI1NiJ9.eyJpc3MiOiJhY2NvdW50cy5nb29nbGUuY29tIiwiYXVkIjoiODQ0MDM1Mjc1ODEzLTh1YXM3cDd1aTdnc3Z0OTRjNmpzYzIzazlkc2hiNWVzLmFwcHMuZ29vZ2xldXNlcmNvbnRlbnQuY29tIiwiY2lkIjoiODQ0MDM1Mjc1ODEzLTh1YXM3cDd1aTdnc3Z0OTRjNmpzYzIzazlkc2hiNWVzLmFwcHMuZ29vZ2xldXNlcmNvbnRlbnQuY29tIiwiaWQiOiIxMTE0MzQ0MTY0MDk1MTM1MTgzMzgiLCJ0b2tlbl9oYXNoIjoidjNIblBzUW1mRFVoR1JHb2tnNS1tQSIsImlhdCI6MTMzMjY3NTY5NiwiZXhwIjoxMzMyNjc5NTk2fQ.2C1c4NwtLKoAzWieYwJKWahPkVdfArsif98rQeFc91ldxF-udEgYBBDGIiFTzZxPSJV4K6ur2pnNalQWIr6VPYelLmWzd7asJmIP5PTgdHOapvc4g4JWcSrcAKcma9foD2TN0vDvTwz35YJqLaU4YvITpT2NmpHePFEMuW24T5c","refresh_token":"1\/EGGE4yDzuEkzb8XHBA79Wt-XclDF8qj5qQ3XNmuVHWM","created":1332676177}');
//$client->setAccessToken($);
         //$me = $plus->people->get('me');

         /*
          * We can also perform other requests such as Activities
          * https://developers.google.com/+/api/latest/activities/list
          */
         /*$activities = $plus->activities->listActivities('me', 'public', array(
             'maxResults' => 25
         ));*/

         /*
          * The activities array holds an inner array called [items], this is each actual Activity
          * https://developers.google.com/+/api/latest/activities#resource
          */
		$params = array(
  			'orderBy' => 'recent',
  			'maxResults' => '20',
  			'query' => 'Тиньков OR Тинькоф OR Тинькофф OR Тиньковв OR Тинков OR Тинкоф OR Тинкофф OR ТКС OR Тинькоффф OR Tinkov OR Tinkof OR Tinkoff OR Tin’kov OR Tin’koff OR TCS Bank OR TCS-Bank OR tcsbank',
			'language' => 'ru',
			'nextPageToken' => ''
			);
		$results = $plus->activities->search($params);
		print_r($results);
//print_r($activities['items']);
          foreach($results['items'] as $activity)
          {
	echo strip_tags($activity['object']['content']).' '.$activity['url']."\n\n\n\n\n";
            //  $activity['object']['content']; //This is the content of the post
          }

				$params = array(
		  			'orderBy' => 'recent',
		  			'maxResults' => '20',
		  			'query' => 'Тиньков OR Тинькоф OR Тинькофф OR Тиньковв OR Тинков OR Тинкоф OR Тинкофф OR ТКС OR Тинькоффф OR Tinkov OR Tinkof OR Tinkoff OR Tin’kov OR Tin’koff OR TCS Bank OR TCS-Bank OR tcsbank',
					'language' => 'ru',
					'pageToken' => $results['nextPageToken']
					);
				$results = $plus->activities->search($params);
		//		print_r($results);
		//print_r($activities['items']);
		          foreach($results['items'] as $activity)
		          {
			echo strip_tags($activity['object']['content']).' '.$activity['url']."\n\n\n\n\n";
		            //  $activity['object']['content']; //This is the content of the post
		          }


    }
    else
    {
         /*
          * Seems the user is not logged in, we need to display a link for the user to authenticate
          * Getting the redirect link to google is very simple, you just need to display it for them.
          */
          $redirectLink = $client->createAuthUrl();

          /*
           * And we simply create a link for the user to click.
           */
          echo sprintf('<a href="%s">%s</a>', $redirectLink, 'Login to Google+');
    }
	echo 'gg';
?>