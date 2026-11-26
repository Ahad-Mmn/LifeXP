<?php
declare(strict_types=1);

namespace LifeXP\Services;

final class Captcha
{
    private const LENGTH = 5;
    // No 0/O, 1/I/L or other look-alike characters
    private const CHARS = 'ABCDEFGHJKMNPQRSTUVWXYZ23456789';

    /** Creates a new code and remembers it in the session. The page shows it with the captcha image. */
    public static function generate(): string
    {
        $code = '';
        for ($i = 0; $i < self::LENGTH; $i++) {
            $code .= self::CHARS[random_int(0, strlen(self::CHARS) - 1)];
        }
        $_SESSION['captcha_answer'] = $code;
        return '';
    }

    /** One-time check: the stored code is deleted whether or not it was correct. */
    public static function verify(string $input): bool
    {
        $expected = $_SESSION['captcha_answer'] ?? null;
        unset($_SESSION['captcha_answer']);

        $input = strtoupper(preg_replace('/\s+/', '', $input) ?? '');
        if (!is_string($expected) || $expected === '' || !preg_match('/^[A-Z0-9]{1,8}$/', $input)) {
            return false;
        }
        return hash_equals($expected, $input);
    }

    /** Draws the current code as a PNG. With $fresh = true a new code is created first. */
    public static function image(bool $fresh = false): never
    {
        if ($fresh || empty($_SESSION['captcha_answer'])) {
            self::generate();
        }
        $code = (string) $_SESSION['captcha_answer'];

        $w = 190;
        $h = 64;
        $img = imagecreatetruecolor($w, $h);
        imagealphablending($img, true);

        $bg = imagecolorallocate($img, 24, 28, 44);
        imagefilledrectangle($img, 0, 0, $w, $h, $bg);

        // Background noise
        for ($i = 0; $i < 5; $i++) {
            $c = imagecolorallocate($img, random_int(40, 80), random_int(40, 80), random_int(60, 100));
            imageline($img, random_int(0, $w), random_int(0, $h), random_int(0, $w), random_int(0, $h), $c);
        }
        for ($i = 0; $i < 180; $i++) {
            $c = imagecolorallocate($img, random_int(50, 100), random_int(50, 100), random_int(70, 120));
            imagesetpixel($img, random_int(0, $w - 1), random_int(0, $h - 1), $c);
        }

        // Each character is drawn small, rotated, enlarged and placed with a random offset
        $slot = (int) floor(($w - 20) / self::LENGTH);
        for ($i = 0; $i < self::LENGTH; $i++) {
            $tile = imagecreatetruecolor(16, 20);
            imagealphablending($tile, false);
            imagesavealpha($tile, true);
            $clear = imagecolorallocatealpha($tile, 0, 0, 0, 127);
            imagefilledrectangle($tile, 0, 0, 16, 20, $clear);
            imagealphablending($tile, true);
            $ink = imagecolorallocate($tile, random_int(200, 255), random_int(200, 255), random_int(150, 255));
            imagestring($tile, 5, 4, 2, $code[$i], $ink);
            imagestring($tile, 5, 5, 2, $code[$i], $ink);

            $rot = imagerotate($tile, random_int(-25, 25), $clear);
            if ($rot === false) {
                $rot = $tile;
            }
            imagealphablending($rot, false);
            imagesavealpha($rot, true);

            $size = random_int(36, 44);
            $bw = (int) ($size * 0.8);
            $big = imagecreatetruecolor($bw, $size);
            imagealphablending($big, false);
            imagesavealpha($big, true);
            imagefilledrectangle($big, 0, 0, $bw, $size, imagecolorallocatealpha($big, 0, 0, 0, 127));
            imagecopyresampled($big, $rot, 0, 0, 0, 0, $bw, $size, imagesx($rot), imagesy($rot));

            $x = 10 + $i * $slot + random_int(-2, 4);
            $y = random_int(6, $h - $size - 4);
            imagealphablending($img, true);
            imagecopy($img, $big, $x, $y, 0, 0, $bw, $size);
        }

        // Lines over the text
        for ($i = 0; $i < 2; $i++) {
            $c = imagecolorallocate($img, random_int(90, 140), random_int(90, 140), random_int(110, 170));
            imageline($img, 0, random_int(8, $h - 8), $w, random_int(8, $h - 8), $c);
        }

        header('Content-Type: image/png');
        header('Cache-Control: no-store, no-cache, must-revalidate');
        header('Pragma: no-cache');
        imagepng($img);
        exit;
    }
}