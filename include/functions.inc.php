<?php
/**
 * Uses the header function to redirect to an URL.<br>
 * Breaks the execution of the script when its called.
 * @param mixed $url The url to redirect to.
 */
function redirect($url) {
    header("Location: " . $url);
    die;
}

/**
 * Creates a description for a historical timestamp.
 * @param int $historyTimestamp The timestamp.
 * @return string An string that says how many days, hours... the time is ago.
 */
function historyTimeString(int $historyTimestamp): string {
    $diff = (int)(time() - $historyTimestamp);
    $hours = $days = 0;
    if ($diff >= 60) {
        $diff = floor($diff / 60);
        $minutes = $diff % 60;
        if ($diff >= 60) {
            $diff = floor($diff / 60);
            $hours = $diff % 24;
            if ($diff >= 24) {
                $diff = floor($diff / 24);
                $days = $diff;
            }
        }
    } else {
        return "vor wenigen Sekunden";
    }
    if ($days > 1) {
        return "vor $days Tage, $hours Stunden";
    } elseif ($days >= 1) {
        return "vor 1 Tag, $hours Stunden";
    } elseif ($hours > 1) {
        return "vor $hours Stunden, $minutes Minuten";
    } elseif ($hours >= 1) {
        return "vor 1 Stunde, $minutes Minuten";
    } else {
        return "vor $minutes Minuten";
    }
}

/**
 * Creates a description for a historical timestamp.
 * @param int $historyTimestamp The timestamp.
 * @return string A highlighted string with html code that says how many days, hours... the time is ago.
 */
function coloredHistoryTimeString(int $historyTimestamp): string {
    $suffix = "</span>";
    $prefix = "";
    $time = historyTimeString($historyTimestamp);
    if (time() - $historyTimestamp < 60 * 60 * 24) {
        $prefix = "<span style='color: red' title='" . $time . "'>";
    } else if (time() - $historyTimestamp < 60 * 60 * 24 * 3) {
        $prefix = "<span style='color: orange' title='" . $time . "'>";
    }
    return $prefix . historyTimeString($historyTimestamp) . $suffix;
}

/**
 * Uses the {@link stripcslashes()}, {@link trim()} and {@link htmlentities()} to replace critical user-input
 * @param int|string $param
 * @return string
 */
function formatInput($param): string {
    $param = trim((string)$param);
    $param = stripslashes($param);
    $param = htmlentities($param, ENT_SUBSTITUTE | ENT_HTML5);
    return $param;
}

/**
 * Set a secured Cookie with flags like <i>httponly</i> and <i>samesite</i>
 * @param string $name The name of the cookie.
 * @param string $value The value of the cookie. This value is stored on the clients' computer; do not store sensitive information.
 * @param int $expires The time the cookie expires. This is a Unix timestamp so is in number of seconds since the epoch.
 * In other words, you'll most likely set this with the {@link time()} function plus the number of seconds before you want it to expire.
 * Or you might use {@link mktime()}. time()+60*60*24*30 will set the cookie to expire in 30 days.
 * If set to 0, or omitted, the cookie will expire at the end of the session (when the browser closes).
 */
function cookie(string $name, string $value, int $expires = 0) {
    setcookie($name, $value, [
        'expires' => $expires,
        'path' => '/',
        'domain' => Session::DOMAIN,
        'secure' => true, // true || false
        'httponly' => true, // true || false
        'samesite' => 'Lax', // None || Lax || Strict
    ]);
}

/**
 * Unset a cookie.
 *
 * Sets a cookie with an empty value and the expiry date to a date in the past
 * @param string $name The name of the cookie.
 */
function unsetCookie(string $name) {
    setcookie($name, '', [
        'expires' => time() - 3600,
        'path' => '/',
        'domain' => Session::DOMAIN,
        'secure' => true, // true || false
        'httponly' => true, // true || false
        'samesite' => 'Lax', // None || Lax || Strict
    ]);
}