window.addEventListener("load", function () {
    // resolveDiscordUserIds(); disabled due we got banned from the discord's API for huge amounts of requests
});

function resolveDiscordUserIds() {
    /**
     * Replace the content of all html elements of an discord-user-id
     * @param id
     * @param fullName
     */
    function replaceAll(/*number*/id, /*string*/fullName) {
        for (let idElement of idElements) {
            if (idElement.textContent === id) {
                idElement.innerHTML = fullName;
            }
        }
    }

    let idElements = document.querySelectorAll("span.resolveDiscordUserId");
    let ids = [];
    for (let idElement of idElements) {
        let id = idElement.textContent.toString();
        if (!ids.includes(id)) {
            ids[ids.length] = id;
        }
    }
    DiscordUserStorage.fetch();
    for (let discord_user_id of ids) {
        let fullName = DiscordUserStorage.get(discord_user_id);
        if (fullName === null) {
            $.getJSON("/resolve_discord_id.php?discord-user-id=" + discord_user_id, function (data) {
                if (data !== null) {
                    let fullName = data.username + "#" + data.discriminator;
                    DiscordUserStorage.add(discord_user_id, fullName)
                    replaceAll(discord_user_id, fullName);
                }
            });
        } else {
            replaceAll(discord_user_id, fullName);
        }
    }
    DiscordUserStorage.save();
}

/**
 * Active record and storage for the discord users
 * @type {{add: DiscordUserStorage.add, contains: (function(number): boolean), get: (function(number): (string|null)), fetch: DiscordUserStorage.fetch, save: DiscordUserStorage.save, clear: DiscordUserStorage.clear, storage: [], key: string}}
 */
var DiscordUserStorage = {
    /**
     * The active record. two-dimensional array with discord users
     */
    storage: [],
    key: "discordUsers",
    /**
     * Get a discord user (username + hash) from this active record
     * @param id
     * @return string|null
     * The full name when its found. Otherwise null
     */
    get: function (/*number*/id) {
        for (let user of this.storage) {
            if (user[0] === id) {
                return user[1];
            }
        }
        return null;
    },
    /**
     * Checks if this active record has stored a discord user
     * @param id
     * @return {boolean}
     */
    contains: function (/*number*/id) {
        for (let user of this.storage) {
            if (user[0] === id) {
                return true;
            }
        }
        return false;
    },
    /**
     * Add a discord user to the active record
     * @param id
     * @param fullName
     */
    add: function (/*number*/id, /*string*/fullName) {
        if (!this.contains(id)) {
            this.storage.push([id, fullName]);
            this.save();
        }
    },
    /**
     * Parse the discord users from the session storage to this active record
     */
    fetch: function () {
        if (typeof (Storage) !== "undefined") {
            let storage = JSON.parse(sessionStorage.getItem(this.key));
            if (storage !== null) {
                this.storage = storage;
            }
        }
    },
    /**
     * Saves the active record to session storage
     */
    save: function () {
        if (typeof (Storage) !== "undefined") {
            sessionStorage.setItem(this.key, JSON.stringify(this.storage));
        } else {
            console.warn("Cannot save local discordUsers.");
        }
    },
    /**
     * Removes all discord users from session storage
     */
    clear: function () {
        if (typeof (Storage) !== "undefined") {
            sessionStorage.removeItem(this.key);
        } else {
            console.warn("Cannot delete local discordUsers");
        }
    }
};