<?php
function passwordlen($password)
{
    return (mb_strlen($password) >= 16);
}

function entropy($password)
{
    $l = $u = $n = $s = false;
    $LETTERS = 'abcdefghijklmnopqrstuvwxyz';
    $DIGITS = '0123456789';
    $SPECIAL_CHARS = '!"#$%&\'()*+,-./:;<=>?@[\]^_`{|}~';
    $L = mb_strlen($password); //Length of password
    $R = 0; //Character pool of password
    for ($i = 0; $i < $L; $i++) {
        if (mb_strpos(($LETTERS), $password[$i]) !== false && !$l) {
            $l = true;
            $R += 26;
        }
        if (mb_strpos(mb_strtoupper($LETTERS), $password[$i]) !== false && !$u) {
            $u = true;
            $R += 26;
        }
        if (mb_strpos(($DIGITS), $password[$i]) !== false && !$n) {
            $n = true;
            $R += 10;
        }
        if (mb_strpos(($SPECIAL_CHARS), $password[$i]) !== false && !$s) {
            $s = true;
            $R += 32;
        }
    }
    $entropy = log(pow($R, $L), 2);
    return ($entropy > 60);
}