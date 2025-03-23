<?php

final class DiscordWebhook {
    private const LOG_CHANNEL_URL = "webhook url";
    private const AVATAR_URL = "icon.jpg";
    /**
     * Webhook name
     */
    private const NAME = "Entbannungssystem";

    /**
     * Sends in the Discord-Log-Channel a message
     * @param string $message The message to send.
     * @return bool Returns true on success. Otherwise false.
     */
    private static function log(string $message): bool {
        if (($json_data = self::createJsonData($message)) !== false) {
            if (self::send(self::LOG_CHANNEL_URL, $json_data)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Sends a info message in the Discord-Log-Channel.
     * @param string $message The message to send.
     * @return bool Returns true on success. Otherwise false.
     */
    public static function logInfo(string $message): bool {
        return self::log("`" . date("Y-m-d H:i:s") . " INFO:` " . $message);
    }

    /**
     * Sends a warning message in the Discord-Log-Channel.
     * @param string $message The message to send.
     * @return bool Returns true on success. Otherwise false.
     */
    public static function logWarn(string $message): bool {
        return self::log("`" . date("Y-m-d H:i:s") . " WARNING:` " . $message);
    }

    /**
     * Sends a error message in the Discord-Log-Channel.
     * @param string $message The message to send.
     * @return bool Returns true on success. Otherwise false.
     */
    public static function logError(string $message): bool {
        return self::log("`" . date("Y-m-d H:i:s") . " ERROR:` " . $message);
    }

    /**
     * @param $message
     * @return false|string Returns the json data on success. False on failure.
     */
    private static function createJsonData($message): string {
        if (($json_data = json_encode([
            "content" => $message,
            "username" => self::NAME,
            "avatar_url" => self::AVATAR_URL,
            "tts" => false
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)) !== false) {
            return $json_data;
        }
        return false;
    }

    /**
     * @param $webhookUrl string The Webhook url
     * @param $json_data string Use {@link createJsonData()} to create the json-string.
     * @return bool Returns true on success. Otherwise false.
     */
    private static function send(string $webhookUrl, string $json_data): bool {
        $ch = curl_init($webhookUrl);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $response = curl_exec($ch);
        curl_close($ch);

        if ($response === true) {
            return true;
        } else {
            return false;
        }
    }
}