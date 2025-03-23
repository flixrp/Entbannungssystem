<?php
function printAppealSidePanel(Appeal $appeal) {
    require "templates/AppealController/appeal-sidepanel.tpl.php";
}

function printAppealPanel(Appeal $appeal, int $appealNumber) {
    global $modalId, $user;
    $sameUserAppeals = Appeals::getAppealsByPropertyValue(Appeal::KEY_DISCORD, $appeal->getDiscord());
    Appeals::sortByDate($sameUserAppeals);
    $relatedAppeals = Appeals::searchRelatedDiscordIds($appeal);
    //$similarAppeals = Appeals::filterSimilarAppeals($appeal);
    //$similarAppealsCount = count($similarAppeals) - 1;
    require "templates/AppealController/appeal-panel.tpl.php";
}