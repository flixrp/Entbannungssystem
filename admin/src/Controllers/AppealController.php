<?php

class AppealController extends AbstractBase {
    use Alerts;

    protected function indexAction() {
        $this->addContext("modalId", 0);
    }

    protected function answerAppealAction() {
        global $user;
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            if (isset($_POST["appeal-user"]) and isset($_POST["message"]) and $user->hasPermission(Login::PERMISSION_USER)) {
                if (Appeals::answer((int)$_POST["appeal-user"], formatInput($_POST["message"]), $user->get_discord_id(), $appealId)) {
                    DiscordBotSync::answerAppeal($_POST["appeal-user"], trim($_POST["message"]), $user->get_discord_id(), $appealId);
                    DiscordWebhook::logInfo("ID (" . $user->get_id() . ") answered to appeal from " . $_POST["appeal-user"] . " with: " . trim($_POST["message"]));
                }
            }
        }
    }

    protected function ignoreAppealAction() {
        global $user;
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            if (isset($_GET["appeal-user"]) and $user->hasPermission(Login::PERMISSION_MANAGER)) {
                Appeals::answer((int)$_GET["appeal-user"], "ignored at (" . time() . ")", $user->get_discord_id());
                DiscordWebhook::logInfo("appeal from " . $_GET["appeal-user"] ." got ignored by ID (" . $user->get_id() . ")");
                $this->alertSuccess("Entbannungsantrag von " . $_GET["appeal-user"] . " ignoriert");
            }
        }
    }

    protected function ignoreAppealAndBanAction() {
        global $user;
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            if (isset($_GET["appeal-user"]) and $user->hasPermission(Login::PERMISSION_MANAGER)) {
                if (Appeals::answer((int)$_GET["appeal-user"], "ignored at (" . time() . ")", $user->get_discord_id())) {
                    Register::banUser((int)$_GET["appeal-user"], "appeal ignored and banned", $user->get_discord_id());
                    DiscordWebhook::logInfo("appeal from " . $_GET["appeal-user"] ." got ignored and the user got banned by " . $user->get_discord_id());
                    $this->alertSuccess("Entbannungsantrag von " . $_GET["appeal-user"] . " ignoriert und er wurde vom system gebannt");
                }
            }
        }
    }
}