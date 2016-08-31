<?php
require_once 'src/apiClient.php';
require_once 'src/contrib/apiPlusService.php';

function get_google_plus($text,$ts,$te,$lan)
{
	//echo $text.' '.$ts.' '.$te.' '.$lan;
	$text=preg_replace('/\|/is',' OR ',$text);
	$text=preg_replace('/\&\&?/is',' AND ',$text);
	//echo $text;
	$asm['en']='en-US';
	$asm['ru']='ru';
	$asm['']='ru';
    session_start();

    $client = new apiClient();
    $plus = new apiPlusService($client);

	$client->setApplicationName('google+');
	$client->setClientId('844035275813.apps.googleusercontent.com');
	$client->setClientSecret('jKMM3_QC2EWsPzbaxRkKgVJy');
	$client->setRedirectUri('urn:ietf:wg:oauth:2.0:oob');
	$client->setDeveloperKey('AIzaSyB8uR3Pp44Cfj-JvpUipsx7F89zt4tSORM');

//$client->setAccessToken('{"access_token":"ya29.AHES6ZQE6L3SBc2pB8RqnrMMUQmogWWhdpDK9pPO5J8iO31C08Hx","token_type":"Bearer","expires_in":3600,"id_token":"eyJhbGciOiJSUzI1NiJ9.eyJpc3MiOiJhY2NvdW50cy5nb29nbGUuY29tIiwiYXVkIjoiODQ0MDM1Mjc1ODEzLTh1YXM3cDd1aTdnc3Z0OTRjNmpzYzIzazlkc2hiNWVzLmFwcHMuZ29vZ2xldXNlcmNvbnRlbnQuY29tIiwiY2lkIjoiODQ0MDM1Mjc1ODEzLTh1YXM3cDd1aTdnc3Z0OTRjNmpzYzIzazlkc2hiNWVzLmFwcHMuZ29vZ2xldXNlcmNvbnRlbnQuY29tIiwiaWQiOiIxMTE0MzQ0MTY0MDk1MTM1MTgzMzgiLCJ0b2tlbl9oYXNoIjoidjNIblBzUW1mRFVoR1JHb2tnNS1tQSIsImlhdCI6MTMzMjY3NTY5NiwiZXhwIjoxMzMyNjc5NTk2fQ.2C1c4NwtLKoAzWieYwJKWahPkVdfArsif98rQeFc91ldxF-udEgYBBDGIiFTzZxPSJV4K6ur2pnNalQWIr6VPYelLmWzd7asJmIP5PTgdHOapvc4g4JWcSrcAKcma9foD2TN0vDvTwz35YJqLaU4YvITpT2NmpHePFEMuW24T5c","refresh_token":"1\/EGGE4yDzuEkzb8XHBA79Wt-XclDF8qj5qQ3XNmuVHWM","created":1332676177}');
	$k=0;
	do
	{
		sleep(1);
		$params = array(
 			'orderBy' => 'recent',
 			'maxResults' => '20',
 			'query' => $text,
			'language' => $asm[$lan],
			'pageToken' => $results['nextPageToken']
			);
		$results = $plus->activities->search($params);
        foreach($results['items'] as $activity)
        {
			if ((strtotime($activity['published'])>=$ts) && (strtotime($activity['published'])<=$te))
			{
				//echo strip_tags($activity['object']['content']).' '.$activity['url']."\n\n\n\n\n";
				//echo strip_tags($activity['title']).' '.$activity['url'].' '.$activity['published']."\n\n\n";
				if (strip_tags($activity['title'])!='')
				{
					$content=preg_replace('/[\s\t]/is',' ',strip_tags($activity['title']));
				}
				elseif (strip_tags($activity['object']['content'])!='')
				{
					$content=preg_replace('/[\s\t]/is',' ',strip_tags($activity['object']['content']));
				}
				elseif (strip_tags($activity['object']['attachments'][0]['displayName'])!='')
				{
					$content=preg_replace('/[\s\t]/is',' ',strip_tags($activity['object']['attachments'][0]['displayName']));
				}
				else
				{
					$content=preg_replace('/[\s\t]/is',' ',strip_tags($activity['object']['attachments'][0]['content']));
				}
				$outmas['content'][$k]=$content;
				$outmas['time'][$k]=strtotime($activity['published']);
				$outmas['link'][$k]=$activity['url'];
				$outmas['nick'][$k]=$activity['actor']['displayName'];
				$k++;
			}
        }
    }
	while (strtotime($results['items'][count($results['items'])-1]['published'])>$ts);
	//echo '//'.strtotime($result['items'][3]['published']).'//';
	return $outmas;
}

//print_r(get_google_plus('Тиньков|Тинькоф|Тинькофф|Тиньковв|Тинков|Тинкоф|Тинкофф|ТКС|Тинькоффф|Tinkov|Tinkof|Tinkoff|Tin’kov|Tin’koff|TCS Bank|TCS-Bank|tcsbank',1332446400,1333051200,'ru'));
?>