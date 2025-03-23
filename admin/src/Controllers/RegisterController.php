<?php

class RegisterController extends AbstractBase {
    use Alerts;

    protected function indexAction() {
        global $user;
        if (!$user->hasPermission(Login::PERMISSION_USER)) {
            DiscordWebhook::logWarn("Unauthorised user tried to view the register from ID(" . $user->get_id() . ")");
            redirect($_SERVER["SCRIPT_NAME"]);
        }
    }

    protected function registerAddUserAction() {
        global $user;
        if ($_SERVER["REQUEST_METHOD"] == "POST" and isset($_POST["discord"])) {
            if ($user->hasPermission(Login::PERMISSION_USER)) {
                if (DiscordApi::getUserInfoByID($_POST["discord"]) !== null) {
                    $userAuth2Token = Register::setUser((int)$_POST["discord"]);
                    if ($userAuth2Token === null) {
                        $this->alertDanger("Fehler beim Registrieren des Users. Der User ist schon Registriert");
                    } else {
                        $this->alertSuccess("User erfolgreich registriert. Das ist der Authentifizierungs-Token des Users: " . $userAuth2Token);
                        DiscordWebhook::logInfo("User " . $_POST["discord"] . " was manually registered from " . $user->get_discord_id());
                    }
                } else {
                    $this->alertDanger("Fehler beim Registrieren des Users. Es gibt keinen Discord account mit dieser ID");
                }
            } else {
                $this->alertDanger("Keine Berechtigung um einen User hinzuzufügen");
            }
        }
    }
}