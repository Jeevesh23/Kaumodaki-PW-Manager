<?php
function getRandomStringRand($length = 32)
{
    $stringSpace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $stringLength = strlen($stringSpace);
    $randomString = '';
    for ($i = 0; $i < $length; $i ++) {
        $randomString = $randomString . $stringSpace[rand(0, $stringLength - 1)];
    }
    return $randomString;
}
?>