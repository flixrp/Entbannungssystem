<?php

/**
 * Login management system.
 * The instance of this class will be the signed-in User.
 * @author Phil B.
 */
final class Login {
    /**
     * @var array All users from the user-storage-file temporary saved.
     */
    private static $loginData;

    /**
     * Add a User to the login-system.
     * @param string $username The Username.
     * @param string $password The Password.
     * @param int $discord The Discord-ID of the User.
     * @param int $permission The Permission-Level.
     * @return bool Return true on success. Otherwise false.
     */
    public static function addUser(string $username, string $password, int $discord, int $permission): bool {
        if (!in_array($discord, array_column(self::$loginData, "discord"))) {
            if (in_array($username, array_column(self::$loginData, "username"))) {
                return false;
            }
            $user["username"] = $username;
            $user["password"] = password_hash($password, PASSWORD_DEFAULT);
            if ($user["password"] === false) {
                return false;
            }
            $user["discord"] = $discord;
            $user["permission"] = $permission;
            $maxId = max(array_column(self::$loginData, "id"));
            do {
                $maxId++;
            } while (in_array($maxId, array_column(self::$loginData, "id")));
            $user["id"] = $maxId;
            self::$loginData[] = $user;
            if (self::saveLoginData()) {
                return true;
            }
        }
        return false;
    }

    /**
     * Deletes a User from the login-system.
     * @param int $userid The user-id of the User.
     * @return bool Return true on success. Otherwise false.
     */
    public static function removeUser(int $userid): bool {
        for ($i = count(self::$loginData) - 1; $i >= 0; $i--) {
            if (self::$loginData[$i]["id"] === $userid) {
                array_splice(self::$loginData, $i, 1);
                if (self::saveLoginData()) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Gives all users (filtered).
     * @return array An 2-dimensional array with the users.<br>
     * Each user array is constructed like this:<br>
     * [<br>
     *   "id" = 1,<br>
     *   "username" = "NAME",<br>
     *   "discord" = 197234287364237443,<br>
     *   "permission" = 0,<br>
     *   "logged-in" = true<br>
     * ]
     */
    public static function getUsers(): array {
        $users = [];
        foreach (self::$loginData as $userData) {
            $user["id"] = $userData["id"];
            $user["username"] = $userData["username"];
            $user["discord"] = $userData["discord"];
            $user["permission"] = $userData["permission"];
            $user["logged-in"] = array_key_exists("login-token", $userData);
            $users[] = $user;
        }
        return $users;
    }

    /**
     * Sign a User out. (Removes the login token from the user)
     * @param int $userid
     * @return bool Return true on success. Otherwise false.
     */
    public static function logoutUser(int $userid): bool {
        for ($i = count(self::$loginData) - 1; $i >= 0; $i--) {
            if (self::$loginData[$i]["id"] === $userid) {
                if (array_key_exists("login-token", self::$loginData[$i])) {
                    unset(self::$loginData[$i]["login-token"]);
                    if (self::saveLoginData()) {
                        return true;
                    }
                }
            }
        }
        return false;
    }

    /**
     * Get a human-readable Permission-Name by the Permission-Level of a User.
     * @param int $permission_level
     * @return string
     */
    public static function parsePermission(int $permission_level): string {
        if ($permission_level == self::PERMISSION_ADMIN) {
            return "Admin";
        } elseif ($permission_level == self::PERMISSION_MANAGER) {
            return "Manager";
        } elseif ($permission_level == self::PERMISSION_USER) {
            return "User";
        } elseif ($permission_level == self::PERMISSION_VISITOR) {
            return "Besucher";
        } else {
            return "Unknown";
        }
    }

    /**
     * Checks if the coming request was send with HTTPS (SSL).
     * @return bool Returns true when the request was with HTTPS. Otherwise false.
     */
    public static function isRequestSSL(): bool {
        return !empty($_SERVER["HTTPS"]) and $_SERVER["HTTPS"] != "off";
    }

    /**
     * Get the Path of the storage file in which the account for the backend users are stored.
     * @return string The Filepath
     */
    private static function getStorageFilePath(): string {
        return $_SERVER["DOCUMENT_ROOT"] . "/protected/.login.json";
    }

    /**
     * Save {@link $loginData} in the storage file.
     * @return bool Returns true on success. Otherwise false.
     */
    private static function saveLoginData(): bool {
        $file = fopen(self::getStorageFilePath(), "w");
        if ($file === false) {
            return false;
        }
        if (fwrite($file, json_encode(self::$loginData, JSON_INVALID_UTF8_SUBSTITUTE | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)) === false) {
            fclose($file);
            return false;
        }
        fclose($file);
        return true;
    }

    /**
     * Get the users that have an account registered for the backend.
     * @return array An 2-Dimensional Array with the users.
     */
    private static function getLoginData(): array {
        if (is_readable(self::getStorageFilePath())) {
            $loginData = file_get_contents(self::getStorageFilePath());
            if ($loginData !== false) {
                return json_decode($loginData, true, 4, JSON_INVALID_UTF8_SUBSTITUTE | JSON_UNESCAPED_UNICODE);
            }
        }
        die("Server error. Code FF-630RL. Versuche die Seite neu zu laden");
    }

    /**
     * Generates a unique random login token that will be stored in a session or a cookie.
     * With it you can automatically login in all other pages
     * @return string The login Token
     */
    private static function generateLoginToken(): string {
        $chars = "0123456789123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
        do {
            $uniqueLoginToken = "v";
            for ($i = 0; $i < 37; $i++) {
                $uniqueLoginToken .= $chars[rand(0, strlen($chars) - 1)];
            }
        } while (in_array($uniqueLoginToken, array_column(self::$loginData, "login-token")));
        return $uniqueLoginToken;
    }

    /**
     * Permission level for admins. Inherits all permissions from lower permission levels!
     */
    public const PERMISSION_ADMIN = 4;
    /**
     * Permission level for admins. Inherits all permissions from lower permission levels!
     */
    public const PERMISSION_MANAGER = 2;
    /**
     * Permission level for normal Users. Inherits all permissions from lower permission levels!
     */
    public const PERMISSION_USER = 1;
    /**
     * Permission level for visitors. Visitors can only read. Inherits all permissions from lower permission levels!
     */
    public const PERMISSION_VISITOR = 0;

    /**
     * The name of the Cookie and Session storage for the login-token.
     */
    private const LOGIN_COOKIE_NAME = "__sniv_n";

    /**
     * @var string The username of the User.
     */
    protected $username;
    /**
     * @var int The discord-id of the User.
     */
    protected $discord_id;
    /**
     * @var int The unique User-ID.
     */
    protected $id;
    /**
     * @var int Permission level of the User.
     */
    protected $permission;
    /**
     * @var string The remote IP of the User.
     */
    //private $last_ip;

    public function __construct() {
        self::$loginData = self::getLoginData();
    }

    public function __toString(): string {
        return $this->get_username();
    }

    /**
     * Get the username of the signed-in User.
     * @return string Returns the username of the User.
     */
    public function get_username(): string {
        return $this->username;
    }

    /**
     * Get the Discord-ID of the signed-in User.
     * @return int Returns the Discord-ID of the User.
     */
    public function get_discord_id(): int {
        return $this->discord_id;
    }

    /**
     * Get the User-ID of the signed-in User.
     * @return int Returns the User-ID of the User.
     */
    public function get_id(): int {
        return $this->id;
    }

    /**
     * Get the Permission of the signed-in User.
     * @return int Returns the Permission-Level of the User.
     */
    public function get_permission_level(): int {
        return $this->permission;
    }

    /**
     * Get the current IP of the remote.
     * @return string The current remote address (IP)
     */
    /*private function get_current_ip(): string {
        return (string)$_SERVER["REMOTE_ADDR"];
    }*/

    /**
     * Tells either the user has admin permission
     * @param int $permission The permission level. Use the Class variables!
     * @return bool Returns true when the user has this permission. Otherwise false.
     */
    public function hasPermission(int $permission): bool {
        return $this->permission >= $permission;
    }

    /**
     * Check if the remote IP address has changed on the login.
     * @return bool Returns true when the IP has changed with the login
     */
    /*public function ipHasChanged(): bool {
        return $this->last_ip !== $this->get_current_ip();
    }*/

    /**
     * Tries to login with the login-token from the Cookie and the Session.
     * When this failed, the login-screen will be included and the user have to login with a username and password.<br>
     * <strong>IMPORTANT: This function will break the execution until the user has signed-in successfully.</strong><br>
     * <strong>Make sure to include the Session class first and create a session before execute this!</strong>
     */
    public function login() {
        if (self::isRequestSSL()) {
            if ((!empty($_COOKIE[self::LOGIN_COOKIE_NAME]) and $this->loginWithToken($_COOKIE[self::LOGIN_COOKIE_NAME])) or
                (!empty($_SESSION[self::LOGIN_COOKIE_NAME]) and $this->loginWithToken($_SESSION[self::LOGIN_COOKIE_NAME]))) {
                return;
            } elseif ($_SERVER["REQUEST_METHOD"] == "POST" and isset($_POST["login"]) and isset($_POST["password"]) and isset($_POST["name"])) {
                $remember = false;
                if (isset($_POST["remember-me"])) {
                    $remember = true;
                }
                if ($this->loginWithPassword($_POST["name"], $_POST["password"], $remember)) {
                    header("Location: " . htmlspecialchars($_SERVER["PHP_SELF"]));
                    die;
                } else {
                    //self::unsetLoginTokens();
                    require "templates/login-screen.tpl.php";
                    die;
                }
            } else {
                //self::unsetLoginTokens();
                require "templates/login-screen.tpl.php";
                die;
            }
        } else {
            die("SSL required");
        }
    }

    /**
     * Logs the current user out.
     */
    public function logout() {
        self::logoutUser($this->get_id());
        $this->unset_userdata();
    }

    /**
     * Try to login with a login-token.<br>
     * Setts the instance-properties to the userdata on success (see {@link set_userdata()}).
     * @param string $token The Login-Token of the user who wants to login.
     * @return bool Returns true when successfully logged in. Otherwise false.
     */
    private function loginWithToken(string $token): bool {
        if (!empty($token)) {
            foreach (self::$loginData as $user) {
                if (isset($user["login-token"]) and strcmp($user["login-token"], $token) === 0) {
                    $this->set_userdata($user);
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Try to login with a username and password.<br>
     * Sets the session or cookie login token when the login data is correct.<br>
     * <strong>A session needs to be active!</strong><br>
     * <strong>Needs the {@link Session} class to work</strong>
     * @param string $username
     * @param string $password
     * @param bool $stayLogin If this is set to true, The login-token will be stored in a cookie instead in a session.
     * @return bool Returns true when the username and password is correct.
     */
    private function loginWithPassword(string $username, string $password, bool $stayLogin = false): bool {
        $userIndex = null;

        // check password
        if (!empty($username) and !empty($password)) {
            for ($i = count(self::$loginData) - 1; $i >= 0; $i--) {
                if (strcmp(self::$loginData[$i]["username"], $username) === 0 and password_verify($password, self::$loginData[$i]["password"])) {
                    $userIndex = $i;
                }
            }
            if ($userIndex === null) {
                /*if (!empty($_SERVER["HTTP_CLIENT_IP"])) {
                    DiscordWebhook::logWarn("`SECURITY:` Login try from ip ||" . $_SERVER["HTTP_CLIENT_IP"] . "|| with credentials username: ||" . $username . "|| password: ||" . $password . "||");
                } elseif (!empty($_SERVER["HTTP_X_FORWARDED_FOR"])) {
                    DiscordWebhook::logWarn("`SECURITY:` Login try from ip ||" . $_SERVER["HTTP_X_FORWARDED_FOR"] . "|| with credentials username: ||" . $username . "|| password: ||" . $password . "||");
                } else {
                    DiscordWebhook::logWarn("`SECURITY:` Login try from ip ||" . $_SERVER["REMOTE_ADDR"] . "|| with credentials username: ||" . $username . "|| password: ||" . $password . "||");
                }*/
                return false;
            }
        } else {
            return false;
        }

        $uniqueLoginToken = self::generateLoginToken();

        // save token in login file
        self::$loginData[$userIndex]["login-token"] = $uniqueLoginToken;
        //self::$loginData[$userIndex]["ip"] = $this->get_current_ip();
        if (!self::saveLoginData()) {
            return false;
        }

        // set token
        if ($stayLogin) {
            cookie(self::LOGIN_COOKIE_NAME, $uniqueLoginToken, time() + 60 * 60 * 24 * 90);
        } else {
            $_SESSION[self::LOGIN_COOKIE_NAME] = $uniqueLoginToken;
        }
        $this->set_userdata(self::$loginData[$userIndex]);
        return true;
    }

    /**
     * Declare the instance variables with the userdata of the signed-in User.
     * @param array $userData an array with the userdata.
     */
    private function set_userdata(array $userData) {
        $this->username = $userData["username"];
        $this->discord_id = $userData["discord"];
        $this->id = $userData["id"];
        $this->permission = $userData["permission"];
        /*if (isset($userData["ip"])) {
            $this->last_ip = $userData["ip"];
        } else {
            $this->last_ip = $this->get_current_ip();
        }*/
    }

    /**
     * Clear the instance variables with the userdata of the signed-in User.
     */
    private function unset_userdata() {
        unset($this->username);
        unset($this->discord_id);
        unset($this->id);
        unset($this->permission);
        //unset($this->last_ip);
    }

    /**
     * Unset the login token from the cookies and session
     */
    private static function unsetLoginTokens() {
        if (isset($_SESSION[self::LOGIN_COOKIE_NAME])) {
            unset($_SESSION[self::LOGIN_COOKIE_NAME]);
        }
        if (isset($_COOKIE[self::LOGIN_COOKIE_NAME])) {
            unsetCookie(self::LOGIN_COOKIE_NAME);
            unset($_COOKIE[self::LOGIN_COOKIE_NAME]);
        }
    }
}