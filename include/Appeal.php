<?php

/**
 * A class for Appeal-Objects
 * @author Phil B.
 */
class Appeal {
    /**
     * The Steam name key for the array
     */
    public const KEY_STEAM_NAME = "steamName";

    /**
     * The TxAdmin ID key for the array
     */
    public const KEY_TXADMIN_ID = "txAdminId";
    /**
     * The BanTime key for the array
     */
    public const KEY_BAN_TIME = "banTime";
    /**
     * The Ban Expire key for the array
     */
    public const KEY_BAN_EXPIRE = "banExpire";
    /**
     * The Banner key for the array
     */
    public const KEY_BANNER = "banner";
    /**
     * The Reason key for the array
     */
    public const KEY_REASON = "reason";
    /**
     * The Reason description key for the array
     */
    public const KEY_REASON_DESCRIPTION = "reasonDescription";
    /**
     * The why unban key for the array
     */
    public const KEY_WHY_UNBAN = "whyUnban";
    /**
     * The Discord key for the array
     */
    public const KEY_DISCORD = "discord";
    /**
     * The Timestamp key for the array
     */
    public const KEY_TIMESTAMP = "timestamp";
    /**
     * The ID key for the array
     */
    public const KEY_ID = "id";
    /**
     * The Answer key for the array
     */
    public const KEY_ANSWER = "answer";
    /**
     * The Appeal-Author key for the array
     */
    public const KEY_ANSWER_BY = "answerBy";
    /**
     * The timestamp when the answer was sent
     */
    public const KEY_ANSWERED_AT = "answerAt";

    protected const SECURE_MODE_WHITELIST = [
        self::KEY_STEAM_NAME,
        self::KEY_TXADMIN_ID,
        self::KEY_BAN_TIME,
        self::KEY_BAN_EXPIRE,
        self::KEY_BANNER,
        self::KEY_REASON,
        self::KEY_REASON_DESCRIPTION,
        self::KEY_WHY_UNBAN
    ];

    /**
     * @var string|null The User's Steam name.
     */
    protected $steamName;
    /**
     * @var string|null The txAdmin Ban ID of the banned User.
     */
    protected $txAdminId;
    /**
     * @var int The unix timestamp when the User got banned.
     */
    protected $banTime;
    /**
     * @var int The unix timestamp when the ban expires
     */
    protected $banExpire;
    /**
     * @var string The Name of the person who banned the User.
     */
    protected $banner;
    /**
     * @var string The reason from the User, why he got banned.
     */
    protected $reason;
    /**
     * @var string The User can describe the Ban-Reason.
     */
    protected $reasonDescription;
    /**
     * @var string The description from the User why he should be unbanned.
     */
    protected $whyUnban;
    /**
     * @var int The Discord-User-ID of the author of this appeal.
     */
    protected $discord;
    /**
     * @var int The unix timestamp when the Appeal has ben created.
     */
    protected $timestamp;
    /**
     * @var int The Appeal-ID.
     */
    protected $id;
    /**
     * @var string|false The answer of the appeal, written by an moderator (max. 2000 character)! Default is false.
     */
    protected $answer = false;
    /**
     * @var int|null The Discord-User-ID of the person who answered the appeal.
     */
    protected $answerBy;
    /**
     * @var int|null The timestamp when the answer has sent
     */
    protected $answerAt = null;

    /**
     * Appeal constructor.
     * @param array $appealData
     * @param bool $secureMode Tells either the array will be filtered
     * before it automatically execute the setters with it. If set to true,
     * the array have access to all setters of the object!
     */
    public function __construct(array $appealData = [], bool $secureMode = false) {
        $this->setTimestamp(time());

        if (!empty($appealData)) {
            foreach ($appealData as $k => $v) {
                $setterName = "set" . ucfirst($k);
                if ($secureMode and !in_array($k, self::SECURE_MODE_WHITELIST, true)) {
                    // TODO log the abuse in discord when someone try to execute not whitelisted setters in secure mode.
                    continue;
                }
                if (method_exists($this, $setterName)) {
                    $this->$setterName($v);
                }
            }
        }
    }

    /**
     * @return string|null The User's Steam name.
     */
    public function getSteamName(): ?string {
        return self::formatInput($this->steamName);
    }

    /**
     * @return string|null The txAdmin Ban ID of the banned User.
     */
    public function getTxAdminId(): ?string {
        return self::formatInput($this->txAdminId);
    }

    /**
     * @return int The timestamp when the User got banned.
     */
    public function getBanTime(): int {
        return $this->banTime;
    }

    /**
     * @return int The timestamp when the Ban expires.
     */
    public function getBanExpire(): int {
        return $this->banExpire;
    }

    /**
     * @return string The Name of the person who banned the User.
     */
    public function getBanner(): string {
        return self::formatInput($this->banner);
    }

    /**
     * @return string The reason from the User, why he got banned.
     */
    public function getReason(): string {
        return self::formatInput($this->reason);
    }

    /**
     * @return string The User can describe the Ban-Reason.
     */
    public function getReasonDescription(): string {
        return self::formatInput($this->reasonDescription);
    }

    /**
     * @return string The description from the User why he should be unbanned.
     */
    public function getWhyUnban(): string {
        return self::formatInput($this->whyUnban);
    }

    /**
     * @return int The Discord-User-ID of the author of this appeal.
     */
    public function getDiscord(): int {
        return $this->discord;
    }

    /**
     * @return int The creation timestamp of the Appeal.
     */
    public function getTimestamp(): int {
        return $this->timestamp;
    }

    /**
     * @return int The unique Appeal-ID.
     */
    public function getId(): int {
        return $this->id;
    }

    /**
     * @return string|false The answer of the appeal. False if the appeal has no answer yet.
     */
    public function getAnswer() {
        return self::formatInput($this->answer);
    }

    /**
     * @return int|null The Discord-User-ID of the person who answered the appeal.
     */
    public function getAnswerBy(): ?int {
        return $this->answerBy;
    }

    /**
     * Checks if the Appeal is valid with these methods:<br>
     * {@link validWhyUnban()}<br>
     * {@link validReasonDescription()}<br>
     * {@link validReason()}<br>
     * {@link validBanExpire()}<br>
     * {@link validBanTime()}<br>
     * {@link validBanner()}<br>
     * {@link validSteamName()}<br>
     * {@link validTxAdminId()}<br>
     * {@link validAnswer()}
     * @return bool
     */
    public function isValid(): bool {
        if ($this->steamName !== null) {
            return false;
        }
        if (
            $this->validWhyUnban() and
            $this->validReasonDescription() and
            $this->validReason() and
            $this->validBanExpire() and
            $this->validBanTime() and
            $this->validBanner() and
            $this->validSteamName() and
            $this->validTxAdminId() and
            $this->validAnswer()
        ) {
            if ($this->steamName !== null xor $this->txAdminId !== null) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * @param string $steamName The User's Steam name.
     */
    public function setSteamName(string $steamName) {
        $this->steamName = $steamName;
    }
    /**
     * @param string $txAdminBanId The txAdmin Ban ID of the banned User.
     */
    public function setTxAdminId(string $txAdminBanId) {
        $this->txAdminId = strtoupper($txAdminBanId);
    }
    /**
     * @param int|string $time The time when the User got banned as a timestamp or a textual datetime.
     */
    public function setBanTime($time) {
        if (is_int($time) and $time <= PHP_INT_MAX) {
            $this->banTime = $time;
        } else if (($timestamp = strtotime($time)) !== false) {
            $this->banTime = $timestamp;
        }
    }
    /**
     * @param int|string $time The time when the Ban expires as a timestamp or a textual datetime.
     */
    public function setBanExpire($time) {
        if (is_int($time) and $time <= PHP_INT_MAX) {
            $this->banExpire = $time;
        } else if (($timestamp = strtotime($time)) !== false) {
            $this->banExpire = $timestamp;
        }
    }
    /**
     * @param string $banner The Name of the person who banned the User.
     */
    public function setBanner(string $banner) {
        $this->banner = $banner;
    }
    /**
     * @param string $reason The reason from the User, why he got banned.
     */
    public function setReason(string $reason) {
        $this->reason = $reason;
    }
    /**
     * @param string $reasonDescription The User can describe the Ban-Reason.
     */
    public function setReasonDescription(string $reasonDescription) {
        $this->reasonDescription = $reasonDescription;
    }
    /**
     * @param string $whyUnban The description from the User why he should be unbanned.
     */
    public function setWhyUnban(string $whyUnban) {
        $this->whyUnban = $whyUnban;
    }
    /**
     * @param int $discordUserId The Discord-User-ID of the author of this appeal.
     */
    public function setDiscord(int $discordUserId) {
        $this->discord = $discordUserId;
    }
    /**
     * @param int $timestamp The creation timestamp of the Appeal.
     */
    public function setTimestamp(int $timestamp) {
        $this->timestamp = $timestamp;
    }
    /**
     * @param int $id The Appeal-ID. It must be unique!
     * Make sure to check that the ID is unique before you execute this!
     */
    public function setId(int $id) {
        $this->id = $id;
    }
    /**
     * @param string|false $answer The answer of the appeal. (max. 2000 character)!
     */
    public function setAnswer($answer) {
        if (is_string($answer) or $answer === false) {
            $this->answer = $answer;
        }
    }
    /**
     * @param int $discordUserId The Discord-User-ID of the person who answered the appeal.
     */
    public function setAnswerBy(int $discordUserId) {
        $this->answerBy = $discordUserId;
    }
    /**
     * @param int|null $answerAt The timestamp when the answer has sent
     */
    public function setAnswerAt($answerAt) {
        $this->answerAt = $answerAt;
    }

    /**
     * Make sure to check if the appeal is valid before storing it!
     * Note to format the array elements when storing it to prevent XSS.
     * @return array
     */
    public function toArray(): array {
        $array = [
            self::KEY_BAN_TIME => $this->getBanTime(),
            self::KEY_BAN_EXPIRE => $this->getBanExpire(),
            self::KEY_BANNER => $this->getBanner(),
            self::KEY_REASON => $this->getReason(),
            self::KEY_REASON_DESCRIPTION => $this->getReasonDescription(),
            self::KEY_WHY_UNBAN => $this->getWhyUnban(),
            self::KEY_DISCORD => $this->getDiscord(),
            self::KEY_TIMESTAMP => $this->getTimestamp(),
            self::KEY_ID => $this->getId(),
            self::KEY_ANSWER => $this->getAnswer()
        ];
        //if ($this->steamName) $array[self::KEY_STEAM_NAME] = $this->getSteamName();
        if ($this->txAdminId) $array[self::KEY_TXADMIN_ID] = $this->getTxAdminId();
        if ($this->answerBy) $array[self::KEY_ANSWER_BY] = $this->getAnswerBy();
        if ($this->answerAt) $array[self::KEY_ANSWERED_AT] = $this->answerAt;
        return $array;
    }

    public function validAnswer(): bool {
        if ((is_string($this->answer) and $this->answer <= 2000) or $this->answer === false) {
            return true;
        } else {
            return false;
        }
    }

    public function validBanExpire(): bool {
        return ($this->banExpire and is_int($this->banExpire));
    }

    public function validBanner(): bool {
        if (empty($this->banner) or strlen($this->banner) > 60) {
            return false;
        } else {
            return true;
        }
    }

    public function validBanTime(): bool {
        return ($this->banTime and is_int($this->banTime));
    }

    public function validReason(): bool {
        if (empty($this->reason) or strlen($this->reason) > 2000) {
            return false;
        } else {
            return true;
        }
    }

    public function validReasonDescription(): bool {
        if (empty($this->reasonDescription) or strlen($this->reasonDescription) > 2000) {
            return false;
        } else {
            return true;
        }
    }

    public function validSteamName(): bool {
        if ($this->steamName === null or (is_string($this->steamName) and strlen($this->steamName) <= 200)) {
            return true;
        } else {
            return false;
        }
    }

    public function validTxAdminId(): bool {
        if ($this->txAdminId === null) {
            return true;
        } else if (is_string($this->txAdminId)) {
            if (preg_match("/[A-Z0-9]{4}-?[A-Z0-9]{4}/", $this->txAdminId)) {
                return true;
            }
        }
        return false;
    }

    public function validWhyUnban(): bool {
        if (empty($this->whyUnban) or strlen($this->whyUnban) > 2000) {
            return false;
        } else {
            return true;
        }
    }

    // TODO replace this function with formatUrlParam()
    /**
     * Uses the {@link stripcslashes()}, {@link trim()} and {@link htmlentities()} to replace critical user-input.<br>
     * Use {@link formatInput()} instead!
     * @param mixed $param
     * @return mixed The formatted data
     * @removed
     */
    private static function formatInput($param)
    {
        if (is_string($param)) {
            $param = trim($param);
            $param = stripslashes($param);
        }
        return $param;
    }
}