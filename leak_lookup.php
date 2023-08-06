<?php

$url = 'https://leak-lookup.com/api/search';

$api_key = getenv('leak_lookup_api_key');

$data = array(
    'key' => $api_key,
    'type' => 'username',
    'query' => 'david.smith'
);

// Initialize the CURL session
$ch = curl_init();

// Set CURL options
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);

// Error checking
if (curl_errno($ch)) 
{
    echo 'CURL error: ' . curl_error($ch);
}

// Close CURL session
curl_close($ch);

// Handling the API response
if ($response) 
{
    // Decode JSON response into an array and print
    $result = json_decode($response, true);
    print_r($result);
} 
else 
{
    // Handle the case when there is no response
    echo 'No response from the API';
}