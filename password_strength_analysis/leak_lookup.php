<?php
session_start();
if (!isset($_SESSION['User_ID'])) {
    header("Location: /authentication");
    die();
}
$url = 'https://leak-lookup.com/api/search';

$api_key = getenv('LEAK_LOOKUP_API_KEY');

$email = empty($_POST['email']) ? $_SESSION['email'] : $_POST['email'];
$data = array(
    'key' => $api_key,
    'type' => 'email_address',
    'query' => $email
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
if (curl_errno($ch)) {
    echo 'CURL error: ' . curl_error($ch);
}
// Close CURL session
curl_close($ch);

// Handling the API response
if ($response) {
    echo "Email checked:" . $email . "<br>";
    // Decode JSON response into an array and print
    $result = json_decode($response, true);
    if ($result['error'] === 'false') {
        $message = $result['message'];
        if (!empty($message)) {
            foreach ($message as $siteName => $breachData) {
                echo "Site Name: $siteName<br>";
            }
            echo "You should change your credentials!<br>";
            echo "If the leak corresponds to a particular website, changing the corresponding credentials will help.<br>";
            echo "If not, the leak could be a part of an internet-wide attack. It's best to search about it and change appropriate credentials.<br>";
        } else {
            echo "You are safe for now! Practice good internet safety techniques!";
        }
    } else {
        echo "Error: " . $data['error'];
    }
} else {
    // Handle the case when there is no response
    echo 'No response from the API';
}
