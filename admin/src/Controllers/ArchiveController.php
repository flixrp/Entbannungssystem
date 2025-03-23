<?php

class archiveController extends AbstractBase {
    use Alerts;

    protected function indexAction() {
        global $user;
        $this->addContext("appeals", Appeals::getAppealsByAnswerAuthor($user->get_discord_id()));
    }

    protected function searchAction() {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            if (!empty($_POST["discord"])) {
                $this->addContext("searching", htmlspecialchars($_POST["discord"]));
                $this->addContext("appeals", Appeals::getHistoryAppealsByDiscordId($_POST["discord"]));
            } else if (!empty($_POST["txAdminId"])) {
                $this->addContext("searching", htmlspecialchars($_POST["txAdminId"]));
                $this->addContext("appeals", Appeals::getHistoryAppealsByTxAdminId($_POST["txAdminId"]));
            } else if (!empty($_POST["steamName"])) {
                $this->addContext("searching", htmlspecialchars($_POST["steamName"]));
                $this->addContext("appeals", Appeals::getHistoryAppealsBySteamName($_POST["steamName"]));
            } else if (!empty($_POST["id"])) {
                $this->addContext("searching", htmlspecialchars($_POST["id"]));
                $this->addContext("appeals", Appeals::getHistoryAppealsById($_POST["id"]));
            } else {
                $this->addContext("searching", "");
                $this->addContext("appeals", []);
            }
        }
    }
}