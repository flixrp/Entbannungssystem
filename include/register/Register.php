<?php

/**
 * Static class for the Management of the register<br>
 * There is also a Class to access the register in Python.
 * Make sure when you change something, you change it there too!
 * @author Phil B.
 */
final class Register
{
    /**
     * @var array the register data temporary saved.
     */
    private static $register;

    /**
     * @var array|false The register config temporary saved.
     */
    private static $config;

    /**
     * @var array The banned users. An array of authTokens.
     */
    private static $deniedUsers;

    /**
     * Get the configuration of the register
     * @return array
     */
    private static function getConfig(): array
    {
        if (!isset(self::$config)) {
            self::$config = parse_ini_file("register-config.ini", true);
        }
        return self::$config;
    }

    /**
     * Returns the auth2token length defined in the configuration of the register
     * @return int
     */
    private static function getAuth2TokenLength(): int
    {
        return (int)self::getConfig()["auth2token"]["length"];
    }

    /**
     * Returns the auth2token letters defined in the configuration of the register
     * @return string
     */
    private static function getAuth2TokenLetters(): string
    {
        return self::getConfig()["auth2token"]["letters"];
    }

    /**
     * Get the Path of the JSON file in which the banned users are stored.<br>
     * (an array containing auth2tokens)
     * @return string
     */
    private static function getDeniedUsersPath(): string
    {
        return $_SERVER["DOCUMENT_ROOT"] . "/include/register/denied-users.json";
    }

    /**
     * Get the ban-data parsed as an Array
     * @return array an array containing auth2tokens of the players who are banned.
     */
    private static function &getDeniedUsers(): array
    {
        if (!isset(self::$deniedUsers)) {
            $data = file_get_contents(self::getDeniedUsersPath());
            if ($data !== false) {
                $p = json_decode($data, true, 3);
                if ($p !== null) {
                    self::$deniedUsers = $p;
                } else {
                    die("Server error. Code FF-512RH. Versuche die Seite neu zu laden");
                }
            } else {
                die("Server error. Code FF-510RB. Versuche die Seite neu zu laden");
            }
        }
        return self::$deniedUsers;
    }

    /**
     * Save {@link $deniedUsers} in the storage file.
     * @return bool Returns true on success. Otherwise false.
     */
    private static function saveDeniedUsers(): bool
    {
        $file = fopen(self::getDeniedUsersPath(), "w");
        if ($file === false) {
            return false;
        }
        if (fwrite($file, json_encode(self::$deniedUsers, JSON_PRETTY_PRINT)) === false) {
            fclose($file);
            return false;
        }
        fclose($file);
        return true;
    }

    /**
     * Get the Path of the JSON file in which the User-Register is stored.<br>
     * (discord-user-id and auth2token)
     * @return string
     */
    private static function getRegisterPath(): string
    {
        return $_SERVER["DOCUMENT_ROOT"] . "/include/register/register.json";
    }

    /**
     * Get the Register data with the discord-user-ids and auth2tokens parsed as an Array
     * @return array
     */
    private static function getRegister(): array
    {
        if (!isset(self::$register)) {
            $data = file_get_contents(self::getRegisterPath());
            if ($data !== false) {
                self::$register = json_decode($data, true, 2);
            } else {
                die("Server error. Code FF-160RR");
            }
        }
        return self::$register;
    }

    /**
     * Save {@link $register} in the register-storage file.
     * @return bool Returns true on success. Otherwise false.
     */
    private static function saveRegister(): bool
    {
        $file = fopen(self::getRegisterPath(), "w");
        if ($file === false) {
            return false;
        }
        if (!fwrite($file, json_encode(self::$register))) {
            fclose($file);
            return false;
        }
        fclose($file);
        return true;
    }

    /**
     * Check if a user is banned from the appeal-panel.
     * @param string|int $discordUserId The Discord-ID of the User.
     * @return bool Returns true when the user is banned. Otherwise false.
     */
    public static function isUserBanned(string $discordUserId): bool
    {
        $discordUserId = (int)$discordUserId;
        foreach (self::getDeniedUsers() as $deniedUser) {
            if ($deniedUser["id"] === $discordUserId) {
                return true;
            }
        }
        return false;
    }

    /**
     * Unban users from the appeals-panel by his Discord-ID.
     * @param int $discordUserId The Discord-ID of the User.
     * @return bool Returns true on success. Otherwise false.
     */
    public static function unbanUser(int $discordUserId): bool
    {
        for ($i = count(self::getDeniedUsers()) - 1; $i >= 0; $i--) {
            if (self::$deniedUsers[$i]["id"] === $discordUserId) {
                array_splice(self::$deniedUsers, $i, $i);
                if (self::saveDeniedUsers()) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Ban a user from the appeals-panel by his Discord-ID.
     * @param int $discordUserId The Discord-ID of the User.
     * @param string $reason The ban reason
     * @param int $by The discord-ID of the author of the ban
     * @return bool Returns true on success. Otherwise false.
     */
    public static function banUser(int $discordUserId, string $reason, int $by): bool
    {
        foreach (self::getDeniedUsers() as $deniedUser) {
            if ($deniedUser["id"] === $discordUserId) {
                return false;
            }
        }
        self::$deniedUsers[] = ["id" => $discordUserId, "reason" => $reason, "timestamp" => time(), "by" => $by];
        if (self::saveDeniedUsers()) {
            return true;
        }
        return false;
    }

    /**
     * Register new Person with discord-user-id in the register-file<br>
     * Checks if the user already exists and returns null if the user was already registered
     * @param int $discordUserId The Discord-ID of the User.
     * @return string|null Returns the auth2token on success. Returns null when the user is already registered
     */
    public static function setUser(int $discordUserId): ?string
    {
        if (!self::isDiscordUserRegistered($discordUserId)) {
            $uniqueAuth2Token = self::generateUniqueAuth2Token();
            self::$register[(string)$discordUserId] = $uniqueAuth2Token;
            if (self::saveRegister()) {
                return $uniqueAuth2Token;
            }
        }
        return null;
    }

    /**
     * Get the discord-user-id by the auth2token
     * @param string $auth2Token The Auth2Token of the User.
     * @return int|null Returns the Discord-ID of the User when it was found. Otherwise null.
     */
    public static function getDiscordUserIdByAuth2Token(string $auth2Token): ?int
    {
        if (self::isAuth2TokenRegistered($auth2Token)) {
            return (int)array_search($auth2Token, self::$register);
        } else {
            return null;
        }
    }

    /**
     * Get the auth2token by the user's discord-user-id
     * @param int $discordUserId The Discord-ID of the User.
     * @return string|null Returns the auth2token of the User when it was found. Otherwise null.
     */
    /*private static function getAuth2TokenByDiscordUser(int $discordUserId): ?string
    {
        if (self::isDiscordUserRegistered($discordUserId)) {
            return self::$register[$discordUserId];
        } else {
            return null;
        }
    }*/

    /**
     * Generates an Unique auth2token.
     * Looks automatically in the register if the auth2token is already taken.
     * @return string an unique auth2token
     */
    private static function generateUniqueAuth2Token(): string
    {
        do {
            $uniqueAuth2Token = "";
            for ($i = 0; $i < self::getAuth2TokenLength(); $i++) {
                $uniqueAuth2Token .= self::getAuth2TokenLetters()[rand(0, strlen(self::getAuth2TokenLetters()) - 1)];
            }
        } while (self::isAuth2TokenRegistered($uniqueAuth2Token) or strpos($uniqueAuth2Token, "u0") !== false);
        return $uniqueAuth2Token;
    }

    /**
     * Check if a User with a specific discord-user-id is already registered
     * @param int $discordUserId
     * @return bool True when the discord-user-id is registered. Otherwise false.
     */
    private static function isDiscordUserRegistered(int $discordUserId): bool
    {
        return array_key_exists($discordUserId, self::getRegister());
    }

    /**
     * Check if a User with a specific auth2token is already registered
     * @param string $auth2Token
     * @return bool True when the auth2token is registered. Otherwise false.
     */
    public static function isAuth2TokenRegistered(string $auth2Token): bool
    {
        return in_array($auth2Token, self::getRegister());
    }
}