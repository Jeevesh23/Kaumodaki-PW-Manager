<?php
session_start();
if (!isset($_SESSION['User_ID'])) {
    header("Location: /authentication");
    die();
}
if (!isset($_SESSION['Premium']) || $_SESSION['Premium'] != 1) {
    echo '<script>alert("Only available to premium users!");</script>';
    header("Refresh:0.5,url=/strength-analysis");
    exit();
}
$newIncludePath = '/app/vendor/';
set_include_path($newIncludePath);
include_once('autoload.php');

use ZxcvbnPhp\Zxcvbn;

//$pass is the password or passphrase.
//$type indicates whether password(0) or passphrase(1)
$zxcvbn = new Zxcvbn();
$pass = $_POST['password'];
$type = $_POST['type'];
$weak = $zxcvbn->passwordStrength($pass);
function display_array($arr)
{
    foreach ($arr as $key => $val) {
        if (gettype($val) != 'array')
            if ($key != 'guesses_log10')
                echo $key . ' : ' . $val . '<br>';
            else
                echo 'guesses_log2 : ' . $val / log(2, 10) . '<br>';
    }
}
function display_array_rec($arr)
{
    foreach ($arr as $key => $val) {
        if (gettype($val) != 'array')
            echo $key . ' : ' . $val . '<br>';
        else {
            echo $key . ':<br>';
            display_array_rec($val);
        }
    }
}
function display_sequence_arr($arr)
{
    foreach ($arr as $key => $val) {
        if (gettype($val) == 'object' || gettype($val) == 'array') {
            if (preg_match("/^[0-9]+$/", $key))
                echo '<br>';
            echo $key . " : ";
            display_sequence_arr($val);
        } else {
            if ($key == 'password')
                echo '<br>';
            echo $key . ' : ' . $val . '<br>';
        }
    }
}
echo 'guesses is the estimated guesses needed to crack password.<br>';
echo 'guesses_log2 is complex entropy in bits.<br>';
echo 'score is an integer from 0-4, 0 indicating the very most guessable passwords.<br>';
echo 'calc_time is how long it took zxcvbn to calculate an answer, in milliseconds.<br><br>';
display_array($weak);
echo '<br>';
echo 'The following parameters show crack time estimations, based on a few scenarios:-<br>';
echo 'online_throttling_100_per_hour is online attack on a service that ratelimits password auth attempts.<br>';
echo 'online_no_throttling_10_per_second is online attack on a service that doesn\'t ratelimit, or where an attacker has outsmarted ratelimiting.<br>';
echo 'offline_slow_hashing_1e4_per_second is an offline attack. Assumes multiple attackers, proper user-unique salting, and a slow hash function w/ moderate work factor, such as bcrypt, scrypt, PBKDF2.<br>';
echo 'offline_fast_hashing_1e10_per_second is an offline attack with user-unique salting but a fast hash function like SHA-1, SHA-256 or MD5. A wide range of reasonable numbers anywhere from one billion - one trillion guesses per second, depending on number of cores and machines, ballparking at 10B/sec.<br><br>';
display_array_rec($weak['crack_times_display']);
echo '<br>';
echo 'feedback shows warning and suggestions about password when score <=2. <br><br>';
display_array_rec($weak['feedback']);
echo '<br>';
if (!$type) {
    echo 'sequence denotes whether the password could be easily bruteforced or is a part of a common dictionary.<br><br>';
    display_sequence_arr($weak['sequence']);
    echo '<br>';
}
