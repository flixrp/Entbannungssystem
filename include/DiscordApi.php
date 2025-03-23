<?php

// discord user avatar https://cdn.discordapp.com/avatars/<user id>/<avatar hash>.png

/**
 * Some useful functions to communicate with the discord api.
 * @author Phil B.
 */
final class DiscordApi
{
    private static $clientToken = "token";

    /**
     * Get some information of an Discord-User using the discord api
     * @param int|string $discord_user_id The ID of the discord user
     * @return array|null Return the userinfo as an array. {@link https://discord.com/developers/docs/resources/user#user-object}.
     * Returns null when no user was found.
     */
    public static function getUserInfoByID($discord_user_id): ?array
    {
        $opts = [
            "http" => [
                "method" => "GET",
                "header" => "Authorization: Bot " . self::$clientToken
            ]
        ];
        $context = stream_context_create($opts);

        // Open the file using the HTTP headers set above
        if (false !== ($data = file_get_contents("https://discordapp.com/api/users/" . $discord_user_id, false, $context))) {
            return json_decode($data, true, 5, JSON_INVALID_UTF8_SUBSTITUTE);
        }
        return null;
    }
}