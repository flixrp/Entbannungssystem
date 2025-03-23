<?php

/**
 * Class Recaptcha
 * @author Phil B.
 */
final class Recaptcha {
    /**
     * @var string The SESSION variable-name for storing the captcha code
     */
    private static $captchaCodeName = "captcha";
    /**
     * @var int The timeout of the recaptcha in minutes
     */
    private const CAPTCHA_TIMEOUT = 120;
    /**
     * @var string The last request on the recaptcha will be saved in a SESSION-variable
     * named like {@link $captchaTimeOutName} as a timestamp.
     * To check if the recaptcha timed out and the user have to load a new recaptcha-image
     */
    private static $captchaTimeOutName = "last-captcha-request-timeout";
    /**
     * @var string The location of the font-file for the captcha
     */
    private static $captchaFont = "protected/recaptcha_font.ttf";

    /**
     * Checks if the Recaptcha code matches with the Captcha-code in the Session.
     * Note that the comparison is in lowercase. The Letter 0 is also the same as O. Returns true when the
     * comparison matches. Otherwise it returns false
     * @param $userCode string|int The code that the user types in to check if its correct
     * @return bool
     */
    public static function validRecaptcha($userCode): bool {
        if (empty($userCode) or empty($_SESSION[self::$captchaCodeName])) {
            return false;
        }
        $userKey = strtolower((string)$userCode);
        $sessionKey = strtolower($_SESSION[self::$captchaCodeName]);
        return str_replace('0', 'o', $userKey) === str_replace('0', 'o', $sessionKey);
    }

    /**
     * Check if the recaptcha is timed out.<br>
     * Compares the current time with the time, stored in the session, to checks
     * if the recaptcha is timed out and the user have to load a new recaptcha-image
     * @return bool Returns true if the recaptcha is timed out. Otherwise false
     */
    public static function isRecaptchaTimedOut(): bool {
        return !(
            !empty($_SESSION[self::$captchaTimeOutName]) and
            time() <= ((int)$_SESSION[self::$captchaTimeOutName] + (60 * self::CAPTCHA_TIMEOUT))
        );
    }

    /**
     * Generate a recaptcha-image. The code will be stored in the SESSION<br>
     * <strong>Make sure you create a session first!</strong><br>
     * This function will exit the execution and send the image with the HEADER to the user
     */
    public static function generateImage() {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            return;
        }
        $imageHeight = 45;
        $imageWidth = 130;

        $image = imagecreate($imageWidth, $imageHeight); // Bild erstellen mit 125 Pixel Breite und 30 Pixel Höhe
        imagecolorallocate($image, 255, 255, 255); // Bild weis färben, RGB
        // punkte hinzufügen

        // <- removed code ->

        //linie hinzufügen

        $left = 0; // Initialwert, von links 5 Pixel
        $signs = 'aAbBcCdDeEfFgGhHiIjJkKlLmMnNoOpPqQrRsStTuUvVwWxXyYzZ0123456789';
        // Alle Buchstaben und Zahlen
        $captchaCode = ""; // Der später per Zufall generierte Code

        for ($i = 1; $i <= 6; $i++) // 6 Zeichen
        {
            $sign = $signs[rand(0, strlen($signs) - 1)];
            /*
                Zufälliges Zeichen aus den oben aufgelisteten
                strlen($signs) = Zählen aller Zeichen
                rand = Zufällig zwischen 0 und der Länge der Zeichen - 1

                Grund für diese Rechnung:
                Mit den Geschweiften Klammern kann man auf ein Zeichen zugreifen
                allerdings fängt man dort genauso wie im Array mit 0 an zu zählen

            */
            $captchaCode .= $sign; // Das Zeichen an den gesamten Code anhängen
            // <- removed code ->
            // Das gerade herausgesuchte Zeichen dem Bild hinzufügen
            imagettftext($image, 27, rand(-15, 15), $left + (($i == 1 ? 12 : 18) * $i), 36, imagecolorallocate($image, 69, 103, 177), self::$captchaFont, $sign);
            // Das Zeichen noch einmal hinzufügen, damit es für einen Bot nicht zu leicht lesbar ist
        }

        $_SESSION[self::$captchaCodeName] = $captchaCode; // Den Code in die Session mit dem Sessionname speichern für die Überprüfung
        $_SESSION[self::$captchaTimeOutName] = time(); // die zeit der abfrage speichern für den recaptcha timeout

        header("Content-type: image/png"); // Header für ein PNG Bild setzen
        imagepng($image); // Ausgaben des Bildes

        exit;
    }
}