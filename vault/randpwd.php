<?php
session_start();
if (!isset($_SESSION['User_ID'])) {
    header("Location: /authentication");
    die();
}
class passwordGenerator
{
    const LETTERS = 'abcdefghijklmnopqrstuvwxyz';
    const DIGITS = '0123456789';
    const SPECIAL_CHARS = '!#$%&*@^';
    // The password minimum length
    private $minLength;
    // The password maximum length
    private $maxLength;
    // The optional list of strings that must be different from the password
    private $diffStrings;
    public function __construct(int $minLength = 16, int $maxLength = 32)
    {
        $this->minLength = $minLength;
        $this->maxLength = $maxLength;
    }
    public function generate(): string
    {
        // List of usable characters
        $chars = self::LETTERS . mb_strtoupper(self::LETTERS) . self::DIGITS . self::SPECIAL_CHARS;

        // Set to true when a valid password is generated
        $passwordReady = false;

        while (!$passwordReady) {
            // The password
            $password = '';

            // Password requirements
            $hasLowercase = true;
            $hasUppercase = true;
            $hasDigit = true;
            $hasSpecialChar = true;

            // A random password length
            $length = random_int($this->minLength, $this->maxLength);

            while ($length > 0) {
                $length--;

                // Add a random character to password from global character set
                $index = random_int(0, mb_strlen($chars) - 1);
                $char = $chars[$index];
                $password .= $char;

                // Checks whether password conditions are fulfilled
                $hasLowercase = $hasLowercase || (mb_strpos(self::LETTERS, $char) !== false);
                $hasUppercase = $hasUppercase || (mb_strpos(mb_strtoupper(self::LETTERS), $char) !== false);
                $hasDigit = $hasDigit || (mb_strpos(self::DIGITS, $char) !== false);
                $hasSpecialChar = $hasSpecialChar || (mb_strpos(self::SPECIAL_CHARS, $char) !== false);
            }

            $passwordReady = ($hasLowercase && $hasUppercase && $hasDigit && $hasSpecialChar && ($password != $_SESSION['Username']));
        }
        return $password;
    }
}
$passwordGenerator = new passwordGenerator($_GET['size'], $_GET['size']);
$pwd = $passwordGenerator->generate();
echo $pwd;
