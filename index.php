<?php

require 'vendor/autoload.php';
include 'keys.php';
use Aws\Sns\SnsClient;

$snsClient = SnsClient::factory(array(
	'key' => $key, //AWS_ACCESS_KEY_ID
	'secret' => $secret, //AWS_SECRET_ACCESS_KEY
	'region' => 'eu-west-1'
	));

function getArn($snsClient) {

    $appArns = $snsClient->listPlatformApplications()['PlatformApplications'];

    foreach ($appArns as $arn) {

        $tempArn = $arn['PlatformApplicationArn'];

        if (strpos($tempArn,'APNS_SANDBOX/iSistemium') !== false) {

            echo 'ARN: ' . $tempArn . '<br />';
            return $tempArn;

        }

    }

}

function getEndpoint($appArn, $snsClient, $device) {

    $endpoints = $snsClient->listEndpointsByPlatformApplication(array('PlatformApplicationArn' => $appArn));

    foreach ($endpoints['Endpoints'] as $endpoint) {

        $endpointArn = $endpoint['EndpointArn'];

        if (strpos($endpointArn, $device) !== false) {
            
            echo 'ENDPOINT: ' . $endpointArn . '<br />';
            return $endpointArn;

        }

    }

}

$appArn = getArn($snsClient);
$endpointArn = getEndpoint($appArn, $snsClient, $device);


try {

    // $push_message = 'test message';
    // $snsClient->publish(array('Message' => $push_message, 'TargetArn' => $endpointArn));

     $aps = json_encode(array('aps'=> array('content-available'=> 1)));
    // $aps = json_encode(array('aps'=> array('alert'=> 'Notification', 'badge'=> 1), 'id'=> '123'));

    //$aps = json_encode(array('aps'=> array('alert'=> 'Notification', 'badge'=> 1, 'sound'=>'default')));
    $message = json_encode(array('APNS_SANDBOX'=> $aps));
    $payload =  array('TargetArn'=> $endpointArn, 'MessageStructure'=> 'json', 'Message'=> $message);
    $snsClient->publish($payload);

    echo "<strong>Success:</strong> ".$endpointArn."<br/>";
	echo "PAYLOAD: ";
	var_dump($payload);
	echo "<br />";

	date_default_timezone_set('Europe/Moscow');
	echo date('l jS \of F Y h:i:s A');

}

catch (Exception $e) {

    echo "<strong>Failed:</strong> ".$endpointArn."<br/><strong>Error:</strong> ".$e->getMessage()."<br/>";

}

?>
