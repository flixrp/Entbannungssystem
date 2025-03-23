<?php

/**
 * Class DiscordSync to communicate with the Discord-Appeal-Bot
 * @author Phil B.
 */
final class DiscordBotSync {
    /**
     * @var array The answers from the user-appeal-answer File.
     */
    private static $answers;

    /**
     * Send an Embed answer-message with the Discord bot to a user.
     * @param int|string $discord_user_id The Discord-ID of the user who the Bot sends the message to
     * @param string $message The message you want to send
     * @param int $author_discord_id The Discord-ID of the author of this message
     * @param int $appealId The appeal id
     */
    public static function answerAppeal($discord_user_id, string $message, int $author_discord_id, int $appealId) {
        if (array_key_exists((string)$discord_user_id, self::getSync_Answers())) {
            // TODO timestamp mit reinspeichern
            self::$answers[(string)$discord_user_id][] = ["message" => $message, "author" => $author_discord_id, "appeal_id" => $appealId];
        } else {
            self::$answers[(string)$discord_user_id] = [["message" => $message, "author" => $author_discord_id, "appeal_id" => $appealId]];
        }
        self::saveSync_Answers();
    }

    /**
     * Send an Embed message to a user which says that his appeal was sent successfully.
     * @param int|string $discord_user_id The Discord-ID of the user who the Bot sends the message to
     * @param int $appealId The appeal id
     */
    public static function appealPosted($discord_user_id, int $appealId) {
        if (array_key_exists((string)$discord_user_id, self::getSync_Answers())) {
            self::$answers[(string)$discord_user_id][] = ["posted" => true, "appeal_id" => $appealId];
        } else {
            self::$answers[(string)$discord_user_id] = [["posted" => true, "appeal_id" => $appealId]];
        }
        self::saveSync_Answers();
    }

    /**
     * Get the storage file in which the answers are stored.
     * @return string
     */
    private static function getSync_AnswersFilePath(): string {
        return $_SERVER["DOCUMENT_ROOT"] . "/protected/discord-bot/sync/user-appeal-answer.json";
    }

    /**
     * Gets all answers saved in the sync File.
     * @return array The answers
     */
    private static function &getSync_Answers(): array {
        if (!isset(self::$answers)) {
            self::$answers = json_decode(file_get_contents(self::getSync_AnswersFilePath()), true, 4, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_INVALID_UTF8_IGNORE);
        }
        return self::$answers;
    }

    /**
     * Save the answers.
     * @return bool Returns true on success. Otherwise false.
     */
    private static function saveSync_Answers(): bool {
        $file = fopen(self::getSync_AnswersFilePath(), "w");
        if ($file === false) {
            return false;
        }
        if (fwrite($file, json_encode(self::$answers, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_INVALID_UTF8_IGNORE)) === false) {
            fclose($file);
            return false;
        }
        fclose($file);
        return true;
    }
}