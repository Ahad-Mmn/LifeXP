<?php
declare(strict_types=1);

namespace LifeXP\Services;

final class Captcha
{
    /** Creates a new question, remembers the answer in the session, returns the question text. */
    public static function generate(): string
    {
        $a = random_int(2, 12);
        $b = random_int(1, 9);

        if (random_int(0, 1) === 0) {
            $_SESSION['captcha_answer'] = $a + $b;
            return "$a + $b";
        }
        $_SESSION['captcha_answer'] = $a - $b;
        return "$a - $b";
    }

    /** One-time check: the stored answer is deleted whether or not it was correct. */
    public static function verify(string $input): bool
    {
        $expected = $_SESSION['captcha_answer'] ?? null;
        unset($_SESSION['captcha_answer']);

        $input = trim($input);
        if ($expected === null || !preg_match('/^-?\d{1,3}$/', $input)) {
            return false;
        }
        return (int) $input === (int) $expected;
    }
}