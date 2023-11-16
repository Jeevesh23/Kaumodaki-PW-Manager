<?php
function createIconAndStoreInDB($name, $uid)
{
    $firstLetter = strtoupper(substr($name, 0, 1));
    $image = imagecreate(40, 40);
    $background = imagecolorallocate($image, 0, 116, 217);
    $textColor = imagecolorallocate($image, 255, 255, 255);
    imagefilledellipse($image, 20, 20, 40, 40, $background);
    imagestring($image, 5, 16, 10, $firstLetter, $textColor);

    $filename = $uid . '_user_icon.png';
    imagepng($image, __DIR__ . '/Icons/' . $filename);
}
