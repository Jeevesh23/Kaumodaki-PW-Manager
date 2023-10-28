<?php
function generatePassphrase($wordlistFile = __DIR__ . '/eff_large_wordlist.txt', $numWords = 5, $delimiter = '-')
{
    //Read file into an array
    $words = file($wordlistFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    //Error code if reading file fails or file is empty
    if (empty($words)) {
        return -1;
    }

    $passphrase = '';
    $wordCount = count($words);
    //Randomly decide for which word number should be affixed
    $numPos = random_int(0, $numWords - 1);
    for ($i = 0; $i < $numWords; $i++) {
        $randomIndex = mt_rand(0, $wordCount - 1);
        $passphrase .= $words[$randomIndex];
        if ($i === $numPos) {
            $passphrase .= random_int(0, 9);
        }
        if ($i < $numWords - 1) {
            $passphrase .= $delimiter;
        }
    }
    //Capitalize the entire string's words
    $passphrase = ucwords($passphrase, '-');
    return $passphrase;
}
