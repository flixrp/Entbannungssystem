<?php

/**
 * Class Session
 * Useful stuff to handle with the session
 * @author Phil B.
 */
final class Session {
    /**
     * Server domain
     */
    public const DOMAIN = "forgerp.net";

    /**
     * Sets the session cookie param <b>httponly</b> to true. Then starts a session with {@link session_start()}
     */
    public static function create() {
        if (session_status() != PHP_SESSION_ACTIVE) {
            session_start([
                "cookie_httponly" => true,
                "use_strict_mode" => true,
                "cookie_secure" => true,
                "sid_length" => 50,
                "sid_bits_per_character" => 6,
                "cookie_samesite" => "lax",
            ]);
        }
    }
}