<?php

class DeniedUserController extends AbstractBase {
    use Alerts;

    protected function indexAction() {
        global $user;
        if (!$user->hasPermission(Login::PERMISSION_ADMIN)) {
            DiscordWebhook::logWarn("Unauthorised user tried to view the denied users from ID(" . $user->get_id() . ")");
            redirect($_SERVER["SCRIPT_NAME"]);
        }
    }

    protected function banUserAction() {
        global $user;
        if ($_SERVER["REQUEST_METHOD"] === "POST" and !empty($_POST["userid"]) and !empty($_POST["ban_reason"])) {
            if ($user->hasPermission(Login::PERMISSION_ADMIN)) {
                if (Register::banUser((int)$_POST["userid"], $_POST["ban_reason"], $user->get_discord_id())) {
                    DiscordWebhook::logInfo("User " . $_POST["userid"] . " was banned from the system from " . $user->get_discord_id() . " for reason:\n" . $_POST["ban_reason"]);
                    //DiscordWebhook::logInfo("User " . $_POST["userid"] . " was banned from the system from ID(" . $user->get_id() . ")");
                    $this->alertSuccess("User (" . $_POST["userid"] . ") erfolgreich gebannt");
                } else {
                    $this->alertDanger("Fehler beim entbannen des Users. Wahrscheinlich ist der User schon gebannt");
                }
            }
        }
    }

    protected function unbanUserAction() {
        global $user;
        if ($_SERVER["REQUEST_METHOD"] === "POST" and !empty($_POST["userid"])) {
            if ($user->hasPermission(Login::PERMISSION_ADMIN)) {
                if (Register::unbanUser((int)$_POST["userid"])) {
                    DiscordWebhook::logInfo("User " . $_POST["userid"] . " was unbanned from the system from " . $user->get_discord_id());
                    //DiscordWebhook::logInfo("User was unbanned from the system from ID(" . $user->get_id() . ")");
                    $this->alertSuccess("User (" . $_POST["userid"] . ") entbannt");
                } else {
                    $this->alertDanger("Fehler beim entbannen des Users. Wahrscheinlich ist der User nicht gebannt");
                }
            }
        }
    }
}