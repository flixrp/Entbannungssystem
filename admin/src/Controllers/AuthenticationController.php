<?php

class AuthenticationController extends AbstractBase {
    use Alerts;

    protected function logoutAction() {
        global $user;
        $user->logout();

        redirect($_SERVER["SCRIPT_NAME"]);
    }

    protected function indexAction() {
        global $user;
        if (!$user->hasPermission(Login::PERMISSION_ADMIN)) {
            DiscordWebhook::logWarn("Unauthorised user tried to view the users from ID(" . $user->get_id() . ")");
            redirect($_SERVER["SCRIPT_NAME"]);
        }
    }

    protected function logoutUserAction() {
        global $user;
        if ($_SERVER["REQUEST_METHOD"] == "POST" and isset($_POST["user-id"])) {
            if ($user->hasPermission(Login::PERMISSION_ADMIN)) {
                Login::logoutUser((int)$_POST["user-id"]);
                DiscordWebhook::logInfo($_POST["user-id"] . " got logged out from ID (" . $user->get_id() . ")");
            }
        }
    }

    protected function removeUserAction() {
        global $user;
        if ($_SERVER["REQUEST_METHOD"] == "POST" and isset($_POST["user-id"])) {
            if ($user->hasPermission(Login::PERMISSION_ADMIN)) {
                Login::removeUser((int)$_POST["user-id"]);
                DiscordWebhook::logInfo("User (" . $_POST["user-id"] . ") was deleted from ID(" . $user->get_id() . ")");
                $this->alertSuccess("Login-Account wurde erfolgreich gelöscht");
            }
        }
    }

    protected function addUserAction() {
        global $user;
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            if (isset($_POST["username"]) and isset($_POST["discord"]) and isset($_POST["password"]) and isset($_POST["permission"])) {
                if ($user->hasPermission(Login::PERMISSION_ADMIN)) {
                    if (Login::addUser($_POST["username"], $_POST["password"], (int)$_POST["discord"], (int)$_POST["permission"])) {
                        DiscordWebhook::logInfo("A User was added from ID(" . $user->get_id() . ")");
                        $this->alertSuccess("Login-Account wurde erfolgreich hinzugefügt");
                    } else {
                        $this->alertDanger("Fehler beim hinzufügen des Login-Accounts");
                    }
                } else {
                    DiscordWebhook::logWarn("Unauthorised user tried to add a user from ID(" . $user->get_id() . ")");
                }
            }
        }
    }
}