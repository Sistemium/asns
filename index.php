<?php

require 'vendor/autoload.php';
include 'keys.php';
use Aws\Sns\SnsClient;

$snsClient = SnsClient::factory(array(
	'key' => $key,
	'secret' => $secret,
	'region' => 'eu-west-1'
	));

$appArn = $snsClient->listPlatformApplications()['PlatformApplications'][0]['PlatformApplicationArn'];

// echo $appArn;


// Get the application's endpoints

$endpoints = $snsClient->listEndpointsByPlatformApplication(array('PlatformApplicationArn' => $appArn));


// Display all of the endpoints for the iOS application

foreach ($endpoints['Endpoints'] as $endpoint)
{
    $endpointArn = $endpoint['EndpointArn'];
    // echo $endpointArn;
}


// iOS: Send a message to each endpoint

$push_message = 'test message';

foreach ($endpoints['Endpoints'] as $endpoint)
{
    $endpointArn = $endpoint['EndpointArn'];

    try
    {
        $snsClient->publish(array('Message' => $push_message,
            'TargetArn' => $endpointArn));

        echo "<strong>Success:</strong> ".$endpointArn."<br/>";
    }
    catch (Exception $e)
    {
        echo "<strong>Failed:</strong> ".$endpointArn."<br/><strong>Error:</strong> ".$e->getMessage()."<br/>";
    }
}

?>
