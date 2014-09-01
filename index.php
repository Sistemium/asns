<?php

require 'vendor/autoload.php';
include 'keys.php';
use Aws\Sns\SnsClient;

$snsClient = SnsClient::factory(array(
	'key' => $key, //AWS_ACCESS_KEY_ID
	'secret' => $secret, //AWS_SECRET_ACCESS_KEY
	'region' => 'eu-west-1'
	));

// 0 — for ActionManager; 1 — iSistemium

$appArn = $snsClient->listPlatformApplications()['PlatformApplications'][1]['PlatformApplicationArn'];

// echo $appArn;


// Get the application's endpoints

$endpoints = $snsClient->listEndpointsByPlatformApplication(array('PlatformApplicationArn' => $appArn));


// Display all of the endpoints for the iOS application

foreach ($endpoints['Endpoints'] as $endpoint) {

    $endpointArn = $endpoint['EndpointArn'];
    // echo $endpointArn;

}


// iOS: Send a message to each endpoint

foreach ($endpoints['Endpoints'] as $endpoint) {

    $endpointArn = $endpoint['EndpointArn'];

    try {

        // $push_message = 'test message';
        // $snsClient->publish(array('Message' => $push_message, 'TargetArn' => $endpointArn));

        // $aps = json_encode(array('aps'=> array('alert'=> 'testAlert', 'badge'=> 1), 'id'=> '123'));
        $aps = json_encode(array('aps'=> array('content-available'=> 1)));
        $message = json_encode(array('APNS_SANDBOX'=> $aps));
        $payload =  array('TargetArn'=> $endpointArn, 'MessageStructure'=> 'json', 'Message'=> $message);
        $snsClient->publish($payload);

        echo "<strong>Success:</strong> ".$endpointArn."<br/>";

    }

    catch (Exception $e) {

        echo "<strong>Failed:</strong> ".$endpointArn."<br/><strong>Error:</strong> ".$e->getMessage()."<br/>";

    }

}

?>
