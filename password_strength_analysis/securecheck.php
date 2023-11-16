<?php
function passwordlen($password)
{
    return (mb_strlen($password) >= 16);
}

function phraselen($phrase)
{
    return substr_count($phrase, '-') + 1;
}
function pwd_entropy($password)
{
    $l = $u = $n = $s = false;
    $LETTERS = 'abcdefghijklmnopqrstuvwxyz';
    $DIGITS = '0123456789';
    $SPECIAL_CHARS = '!#$%&*@^';
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
            $R += 8;
        }
    }
    $pwd_entropy = log(pow($R, $L), 2);
    return $pwd_entropy;
}
function phr_entropy($phraselen)
{
    //The EFF wordlist has 7776 words out of which $phraselen words are selected
    //Thus 7776^($phraselen) combinations are possible
    //A digit(0-9) is inserted at end of one of the words at random
    //Thus 10^($phraselen) more combinations
    //Every word is capitalized thus 2 more combinations(yes or no to capitalization)
    $phr_entropy = $phraselen * log(7776, 2) + $phraselen * log(10, 2) + 1;
    return $phr_entropy;
}
