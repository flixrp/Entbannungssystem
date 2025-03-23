<?php

/**
 * Static Class to manage the Appeals.
 * @author Phil B.
 */
final class Appeals {

    /*------------------------------------------*/
    /* ---------- backup appeals -------------- */

    /**
     * @var array[] All appeals from the deleted-appeals-storage-file temporary saved.
     */
    private static $historyAppeals;

    /**
     * Get the history Appeals-Data as an array.
     * @return array The Appeals-Data on success. Otherwise an empty array.
     */
    private static function &getHistoryAppeals(): array {
        if (self::$historyAppeals) {
            return self::$historyAppeals;
        }
        $file = dirname(__FILE__) . "/deleted-appeals-backup.json";
        if (is_readable($file)) {
            $appeals = file_get_contents($file);
            if ($appeals !== false) {
                $data = json_decode($appeals, true, 3);
                if ($data !== null) {
                    self::$historyAppeals = $data;
                    return self::$historyAppeals;
                }
            }
        }
        die("Server error. Code FB-430RB. Versuche die Seite neu zu laden");
    }

    /*-------------------------------------------------*/
    /* ---------- end of backup appeals -------------- */


    /**
     * @var array All appeals from the appeals-storage-file temporary saved.
     */
    private static $appealsData;

    /**
     * Get the Path of the storage file in which the appeals are stored.
     * @return string The Filepath
     */
    private static function getStorageFilePath(): string {
        return $_SERVER["DOCUMENT_ROOT"] . "/include/appeals/appeals.json";
    }

    /**
     * Read the Appeals-Data as an array from the storage file.
     * @return array|null The Appeals on success. Otherwise null.
     */
    private static function readAppeals(): ?array {
        if (is_readable(self::getStorageFilePath())) {
            $appeals = file_get_contents(self::getStorageFilePath());
            if ($appeals !== false) {
                $encoded = json_decode($appeals, true, 3);
                if ($encoded === null) {
                    http_response_code(500);
                    die("Datenbankfehler. Bitte versuche es später erneut");
                }
                return $encoded;
            }
        }
        die("Server error. Code FF-420RA. Versuche die Seite neu zu laden");
    }

    /**
     * Save {@link $appealsData} in the storage file.
     * @return bool Returns true on success. Otherwise false.
     */
    private static function saveAppeals(): bool {
        $file = fopen(self::getStorageFilePath(), "w");
        if ($file === false) {
            return false;
        }
        $encoded_content = json_encode(self::$appealsData, JSON_PRETTY_PRINT, 12);
        if ($encoded_content === false) {
            return false;
        }
        for ($i = 0; $i < 4; $i++) { // try to save the appeals 4 times

            if (flock($file, LOCK_EX)) { // lock the file

                // write
                if (fwrite($file, $encoded_content) === false) {
                    fclose($file);
                    return false;
                }
                fclose($file);
                return true;

            } else {
                sleep(rand(0.1, 1)); // if the file couldn't be locked, sleep and try again
            }
        }
        return false;
    }

    /**
     * Get the Appeals-Data as an array.
     * @return array The Appeals-Data on success. Otherwise an empty array.
     */
    public static function getAppeals(): array {
        if (self::$appealsData) {
            return self::$appealsData;
        }
        $appeals = self::readAppeals();
        if ($appeals !== null) {
            self::$appealsData = $appeals;
            return $appeals;
        }
        return [];
    }

    /**
     * Test if the user has an unanswered appeal.<br>
     * A user is not able to send an appeal when he already send an appeal.
     * He has to wait until the crew answers his last appeal.
     * @param int $userDiscordId The Discord-ID of the User.
     * @return bool Returns false when the user has to wait for an answer. Otherwise true.
     */
    public static function userHasUnansweredAppeal(int $userDiscordId): bool {
        $userAppealIndexes = self::searchForPropertyValue(Appeal::KEY_DISCORD, $userDiscordId);
        foreach ($userAppealIndexes as $index) {
            if (self::$appealsData[$index][Appeal::KEY_ANSWER] === false) {
                return true;
            }
        }
        return false;
    }

    /**
     * Set an answer to a specific appeal
     * @param int $userDiscordId The Discord-ID of the User.
     * @param string $message The message you want to answer.
     * @param int $answeredBy The Discord-ID of the Person who has answered.
     * @return bool Returns true on success and false on failure.
     */
    public static function answer(int $userDiscordId, string $message, int $answeredBy, &$holdAppealId = null): bool {
        $userAppealsIndexes = self::searchForPropertyValue(Appeal::KEY_DISCORD, $userDiscordId);
        foreach ($userAppealsIndexes as $index) {
            if (self::$appealsData[$index][Appeal::KEY_ANSWER] === false) {
                self::$appealsData[$index][Appeal::KEY_ANSWER] = $message;
                self::$appealsData[$index][Appeal::KEY_ANSWER_BY] = $answeredBy;
                self::$appealsData[$index][Appeal::KEY_ANSWERED_AT] = time();
                if (self::saveAppeals()) {
                    $holdAppealId = self::$appealsData[$index][Appeal::KEY_ID];
                    return true;
                }
                return false;
            }
        }
        return false;
    }

    /**
     * Call this function to save a appeal-request to the appeals-file.
     * This is only able when the user is registered.<br>
     * <strong>Check the user-input before store this data.</strong>
     * @param Appeal $appeal An Appeal object
     */
    public static function newAppeal(Appeal $appeal) {
        $id = (int)max(array_column(self::getAppeals(), "id"));
        do {
            $id++;
        } while (in_array($id, self::$appealsData));
        $appeal->setId($id);
        self::$appealsData[] = $appeal->toArray();
        self::saveAppeals();
    }

    /**
     * Sort Appeals by the timestamp. It sorts from the latest to oldest.
     * @param array $appeals The appeals that you will sort
     */
    public static function sortByDate(array &$appeals) {
        uasort($appeals, function ($a, $b): int {
            if ($a->getTimestamp() == $b->getTimestamp()) {
                return 0;
            }
            return ($a->getTimestamp() > $b->getTimestamp()) ? -1 : 1;
        });
    }

    /**
     * Search for Key/-Value pairs in the appeals file to get there parent-Appeal.<br>
     * Searches for OldAppeals that have the specified Key/Value pair with {@link searchForPropertyValue()}.
     * @param string $property The Key to search for.
     * @param mixed $value The Value to search for.
     * @param bool $strict [optional]<br>Determines if strict comparison (===) should be used during the search.
     * @return array - An array with the Appeals that matches.<br>
     * - Otherwise an empty array, when nothing was found.
     */
    public static function getAppealsByPropertyValue(string $property, $value, bool $strict = false): array {
        return self::getAppealsByIndexes(self::searchForPropertyValue($property, $value, $strict));
    }

    /* new history search methods */
    /* ------------------------- */

    /**
     * @param string $steamName The steamname to search for
     * @return Appeal[] The array with the founded appeals
     */
    public static function getHistoryAppealsBySteamName(string $steamName): array {
        $appeals = [];
        foreach (self::getAppeals() as $appeal) {
            if (isset($appeal[Appeal::KEY_STEAM_NAME]) and $appeal[Appeal::KEY_STEAM_NAME] === $steamName) {
                $appeals[] = new Appeal($appeal);
            }
        }
        foreach (self::getHistoryAppeals() as $appeal) {
            if (isset($appeal[Appeal::KEY_STEAM_NAME]) and $appeal[Appeal::KEY_STEAM_NAME] === $steamName) {
                $appeals[] = new Appeal($appeal);
            }
        }
        if (!empty($appeals)) {
            usort($appeals, function ($a, $b) {
                return ($a->getTimestamp() <= $b->getTimestamp()) ? -1 : 1;
            });
        }
        return $appeals;
    }

    /**
     * @param string $txAdminId The txadmin ID to search for
     * @return Appeal[] the array with the founded appeals
     */
    public static function getHistoryAppealsByTxAdminId(string $txAdminId): array {
        $appeals = [];
        foreach (self::getAppeals() as $appeal) {
            if (isset($appeal[Appeal::KEY_TXADMIN_ID]) and $appeal[Appeal::KEY_TXADMIN_ID] === $txAdminId) {
                $appeals[] = new Appeal($appeal);
            }
        }
        foreach (self::getHistoryAppeals() as $appeal) {
            if (isset($appeal[Appeal::KEY_TXADMIN_ID]) and $appeal[Appeal::KEY_TXADMIN_ID] === $txAdminId) {
                $appeals[] = new Appeal($appeal);
            }
        }
        if (!empty($appeals)) {
            usort($appeals, function ($a, $b) {
                return ($a->getTimestamp() <= $b->getTimestamp()) ? -1 : 1;
            });
        }
        return $appeals;
    }

    /**
     * @param string|int $id The Appeal-ID to search for
     * @return array the array with the founded appeals
     */
    public static function getHistoryAppealsById($id): array {
        $id = (int)$id;
        $appeals = [];
        foreach (self::getAppeals() as $appeal) {
            if (isset($appeal[Appeal::KEY_ID]) and $appeal[Appeal::KEY_ID] === $id) {
                $appeals[] = new Appeal($appeal);
            }
        }
        foreach (self::getHistoryAppeals() as $appeal) {
            if (isset($appeal[Appeal::KEY_ID]) and $appeal[Appeal::KEY_ID] === $id) {
                $appeals[] = new Appeal($appeal);
            }
        }
        if (!empty($appeals)) {
            usort($appeals, function ($a, $b) {
                return ($a->getTimestamp() <= $b->getTimestamp()) ? -1 : 1;
            });
        }
        return $appeals;
    }

    /**
     * @param string|int $discordId The txadmin ID to search for
     * @return Appeal[] the array with the founded appeals
     */
    public static function getHistoryAppealsByDiscordId($discordId): array {
        $discordId = (int)$discordId;
        $appeals = [];
        foreach (self::getAppeals() as $appeal) {
            if (isset($appeal[Appeal::KEY_DISCORD]) and $appeal[Appeal::KEY_DISCORD] === $discordId) {
                $appeals[] = new Appeal($appeal);
            }
        }
        foreach (self::getHistoryAppeals() as $appeal) {
            if (isset($appeal[Appeal::KEY_DISCORD]) and $appeal[Appeal::KEY_DISCORD] === $discordId) {
                $appeals[] = new Appeal($appeal);
            }
        }
        if (!empty($appeals)) {
            usort($appeals, function ($a, $b) {
                return ($a->getTimestamp() <= $b->getTimestamp()) ? -1 : 1;
            });
        }
        return $appeals;
    }

    /* end of new history search methods */
    /* -------------------------------- */

    /**
     * Specific method to search for appeals that are answered by a specific user!
     * @param int $discordUserId The discord User ID of the user who answered to the appeal
     * @return array - An array with the Appeals that matches.<br>
     * - Otherwise an empty array, when nothing was found.
     */
    public static function getAppealsByAnswerAuthor(int $discordUserId): array {
        $matches = [];
        foreach (self::getAppeals() as $appeal) {
            if (array_key_exists(Appeal::KEY_ANSWER_BY, $appeal) and $appeal[Appeal::KEY_ANSWER_BY] === $discordUserId) {
                $matches[] = new Appeal($appeal);
            }
        }
        if (!empty($matches)) {
            usort($matches, function ($a, $b) {
                return ($a->getTimestamp() <= $b->getTimestamp()) ? -1 : 1;
            });
        }
        return $matches;
    }

    /**
     * Search for Appeals with related Discord users
     * @param Appeal $appeal The Appeal to search for comparisons
     * @return Appeal[] The array of appeals where the author (Discord user) are related to each other
     */
    public static function searchRelatedDiscordIds(Appeal $appeal): array {

        $txAdminIds = [];
        $steamNames = [];
        $appeals = [];

        $append = function (&$appeals, $a) use ($appeal) {
            // append the appeal if not already appended (based on the discord user who send the appeal)
            if (isset($a[Appeal::KEY_DISCORD]) and $a[Appeal::KEY_DISCORD] !== $appeal->getDiscord()) {
                foreach ($appeals as $alreadySavedAppeal) {
                    if ($alreadySavedAppeal->getDiscord() === $a[Appeal::KEY_DISCORD]) {
                        return;
                    }
                }
                $appeals[] = new Appeal($a);
            }
        };

        foreach (self::getAppeals() as $a) {
            if ($appeal->getTxAdminId() and
                isset($a[Appeal::KEY_TXADMIN_ID]) and $a[Appeal::KEY_TXADMIN_ID] === $appeal->getTxAdminId()) {
                $append($appeals, $a);
            }
            if ($appeal->getSteamName() and
                isset($a[Appeal::KEY_STEAM_NAME]) and $a[Appeal::KEY_STEAM_NAME] === $appeal->getSteamName()) {
                $append($appeals, $a);
            }
            /*if (isset($a[Appeal::KEY_DISCORD]) and $a[Appeal::KEY_DISCORD] === $appeal->getDiscord()) {
                // check if the user has written for other ingame accounts
                if (isset($a[Appeal::KEY_STEAM_NAME]) and $a[Appeal::KEY_STEAM_NAME] !== $appeal->getSteamName() and
                    !in_array($a[Appeal::KEY_STEAM_NAME], $steamNames)) {
                    $steamNames[] = $a[Appeal::KEY_STEAM_NAME];

                    foreach (self::getAppeals() as $i) {
                        if (isset($i[Appeal::KEY_STEAM_NAME]) and $i[Appeal::KEY_STEAM_NAME] === $a[Appeal::KEY_STEAM_NAME]) {
                            $append($appeals, $i);
                        }
                    }
                }
                if (isset($a[Appeal::KEY_TXADMIN_ID]) and $a[Appeal::KEY_TXADMIN_ID] !== $appeal->getSteamName() and
                    !in_array($a[Appeal::KEY_TXADMIN_ID], $txAdminIds)) {
                    $txAdminIds[] = $a[Appeal::KEY_TXADMIN_ID];

                    foreach (self::getAppeals() as $i) {
                        if (isset($i[Appeal::KEY_TXADMIN_ID]) and $i[Appeal::KEY_TXADMIN_ID] === $a[Appeal::KEY_TXADMIN_ID]) {
                            $append($appeals, $i);
                        }
                    }
                }
            }*/
        }
        /*foreach (self::getHistoryAppeals() as $a) {
            if ($appeal->getTxAdminId() and
                isset($a[Appeal::KEY_TXADMIN_ID]) and $a[Appeal::KEY_TXADMIN_ID] === $appeal->getTxAdminId()) {
                $append($appeals, $a);
            }
            if ($appeal->getSteamName() and
                isset($a[Appeal::KEY_STEAM_NAME]) and $a[Appeal::KEY_STEAM_NAME] === $appeal->getSteamName()) {
                $append($appeals, $a);
            }
        }*/

        return $appeals;
    }

    /**
     * Slice all appeals with specific indexes.
     * @param array $indexes An array of indexes which you want from the appeals.<br>
     * The indexes can be searched with {@link searchForPropertyValue()}
     * @return array An array with the appeals.
     */
    private static function getAppealsByIndexes(array $indexes): array {
        $foundedAppeals = [];
        foreach ($indexes as $index) {
            $foundedAppeals[] = new Appeal(self::getAppeals()[$index]);
        }
        return $foundedAppeals;
    }

    /**
     * Search for Key/-Value pairs in the appeals file.<br>
     * If you search for Values of a specific Key then it returns an array, containing
     * the indexes of the appeals which matches.<br>
     * Or search only for a Key and get all the Values.<br>
     * @param string $property The Key to search for.
     * @param mixed|null $value [optional]<br>The Value to search for.
     * @param bool $strict [optional]<br>Determines if strict comparison (===) should be used during the search.
     * @return array Returns one of the following:<br>
     * - An array with indexes when you searches for Key/Value pairs.<br>
     * - An array with the values when searching for Keys.<br>
     * - Otherwise an empty array, when nothing was found.
     */
    private static function searchForPropertyValue(string $property, $value = null, bool $strict = false): array {
        if ($value === null) {
            return array_column(self::getAppeals(), $property);
        } else {
            return array_keys(array_column(self::getAppeals(), $property), $value, $strict);
        }
    }

    /**
     * Search for similar OldAppeals. It looks for the "why-unban" and "reason-description"
     * @param Appeal $searchAppeal The Appeal which you looking for similar ones.
     * @return array The appeals that are similar
     *
     * @removed
     */
    public static function filterSimilarAppeals(Appeal $searchAppeal): array {
        $similarAppeals = self::filterCompareOfPropertyValues(Appeal::KEY_REASON_DESCRIPTION, $searchAppeal->getReasonDescription());
        $otherSimilarAppeals = self::filterCompareOfPropertyValues(Appeal::KEY_WHY_UNBAN, $searchAppeal->getWhyUnban());
        foreach ($otherSimilarAppeals as $appeal) {
            if (!in_array($appeal->getId(), array_column($similarAppeals, Appeal::KEY_ID))) {
                $similarAppeals[] = $appeal;
            }
        }
        return $similarAppeals;
    }

    /**
     * Compares the values.
     * Vergleicht die values von KEY mit dem angegeben value. Wenn sich die values mehr als 60% ähneln, gälten Sie als änliche values.
     * @param string $appealProperty The Key/Property which should be compared.
     * @param int|string $comparison The value of the Key/Property to compare to.
     * @param int $minAccordance Accordance in percent.<br>
     * The Accordance says how much percent the values must be the same to mark it as an similar value.
     * @return array An array with {@link Appeal} objects
     * @removed
     */
    private static function filterCompareOfPropertyValues(string $appealProperty, $comparison, int $minAccordance = 82): array {
        $matchesAppeals = [];
        foreach (self::getAppeals() as $appeal) {
            similar_text($appeal[$appealProperty], $comparison, $percent);
            if ($percent >= $minAccordance) {
                $matchesAppeals[] = new Appeal($appeal);
            } else {
                similar_text($comparison, $appeal[$appealProperty], $percent);
                if ($percent >= $minAccordance) {
                    $matchesAppeals[] = new Appeal($appeal);
                }
            }
        }
        return $matchesAppeals;
    }
}