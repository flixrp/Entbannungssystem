#!/usr/bin/python3
import asyncio
import configparser
import json
import logging
import os
import sys
from time import time
import traceback
import random
from datetime import datetime

import discord

logging.basicConfig(
    filename=os.path.join(os.path.dirname(os.path.realpath(__file__)), "latest.log"),
    filemode="w",
    level=logging.INFO,
    format="%(asctime)s:%(levelname)s:%(message)s"
)
logging.info("Started with python version " + sys.version)
config = configparser.ConfigParser()
config.read(os.path.join(os.path.dirname(os.path.realpath(__file__)), "config.ini"))
registerConfig = configparser.ConfigParser()
registerConfig.read(os.path.join(os.path.dirname(os.path.realpath(__file__)), "register-config.ini"))


def get_timestamp() -> int:
    return int(time())


class Register:
    """
    This Class also exists as a PhpClass!
    If something changes make sure to change it too!
    """

    @staticmethod
    def __get_auth2token_length() -> int:
        return int(registerConfig.get("auth2token", "length"))

    @staticmethod
    def __get_auth2token_letters() -> str:
        return registerConfig.get("auth2token", "letters")

    @staticmethod
    def __generate_unique_auth2token(register: dict) -> str:
        chars = Register.__get_auth2token_letters()
        size = Register.__get_auth2token_length()
        auth_token = ''.join(random.choice(chars) for _ in range(size))
        while auth_token in register.values():
            auth_token = ''.join(random.choice(chars) for _ in range(size))
        return auth_token

    @staticmethod
    def __get():
        try:
            with open(registerConfig.get("Register", "register-path")) as json_obj:
                return json.load(json_obj)
        except IOError or BlockingIOError or json.decoder.JSONDecodeError:
            return None

    @staticmethod
    def __put(register: dict) -> bool:
        try:
            with open(registerConfig.get("Register", "register-path"), "w") as outfile:
                json.dump(register, outfile)
                outfile.close()
        except IOError or BlockingIOError:
            return False
        return True

    @staticmethod
    def get_auth2token_by_discord_user_id(discord_user_id: str):
        register = Register.__get()
        if register is None:
            return None
        if discord_user_id in register:
            return register[discord_user_id]
        else:
            new_auth2token = Register.__generate_unique_auth2token(register)
            register[discord_user_id] = new_auth2token
            if Register.__put(register) is False:
                return None
            return new_auth2token


class DiscordBotSync:
    @staticmethod
    def get_user_appeal_answers() -> dict:
        try:
            with open(config.get("Sync", "appeal-answer-file")) as json_obj:
                answers = json.load(json_obj)
                json_obj.close()
        except IOError or BlockingIOError or json.decoder.JSONDecodeError:
            return {}
        try:
            with open(config.get("Sync", "appeal-answer-file"), "w") as outfile:
                json.dump({}, outfile)
                outfile.close()
        except IOError or BlockingIOError:
            return {}
        return answers


class Client(discord.Client):
    async def on_error(self, *args, **kwargs):
        logging.error(traceback.format_exc())

    async def on_ready(self):
        print("Logged in as " + str(self.user.name) + " (" + str(self.user.id) + ")")
        logging.info("Logged in as " + str(self.user.name) + " (" + str(self.user.id) + ")")
        await self.change_presence(activity=discord.Activity(type=discord.ActivityType.watching,
                                                             name=str(config.get("Settings", "status-message"))))
        while True:
            await self.sync_appeal_answers()
            await asyncio.sleep(10)

    async def on_message(self, message):
        if message.author.bot or message.author.system:
            return
        if isinstance(message.channel, discord.DMChannel):
            await message.channel.send(embed=self.get_embed_message(message.author.id))
        elif str(message.content.lower()).startswith("!entban"):
            try:
                await message.author.send(embed=self.get_embed_message(message.author.id))
                await message.channel.send("Ich habe dir deinen Link geschickt mit dem du einen Entbannungsantrag "
                                           "schreiben kannst")
            except discord.Forbidden:
                await message.channel.send("Sorry, Ich kann dir Privat nicht schreiben. Es sieht so aus als hättest "
                                           "du in deinen Einstellungen \"Direktnachrichten von Servermitgliedern\" "
                                           "deaktiviert. Alternativ kannst du mir auch Privat irgendwas schreiben.")
            except:
                logging.error("failed to send unban-link to " + str(message.author.id))
                await self.discord_channel_log("ERROR", "failed to send unban-link to " + str(message.author.id))

    async def discord_channel_log(self, prefix: str, message: str):
        """Logs a message in a discord-log-channel
        """
        # datetime object containing current date and time
        now = datetime.now()
        # dd/mm/YY H:M:S
        dt_string = now.strftime("%Y-%m-%d %H:%M:%S")
        channel = self.get_channel(int(config.get("Settings", "log-channel-id")))
        if channel is None:
            logging.error("log channel not found")
        else:
            await channel.send("`" + dt_string + " " + prefix + "`: " + message)

    def get_embed_message(self, discord_user_id: int) -> discord.Embed:
        embed = discord.Embed(color=0x1b1b1b)
        token = Register.get_auth2token_by_discord_user_id(str(discord_user_id))
        if token is None:
            embed.description = "Fehler beim erstellen des Links. Bitte versuche es erneut"
            return embed
        tkn_link = config.get("Appeals", "form-link") + token
        embed.description = "Hier ist dein Link um einen Entbannungsantrag zu senden: [Entbannungsantrag]" \
                            "(" + tkn_link + ")\n\nDu kannst diesen Link jederzeit neu anfordern.\n" \
                                             "Gebe diesen Link nicht weiter!\n\nMit freundlichen Grüßen,\n" \
                                             "Die Projektleitungen\n\n" \
                                             "[Website](https://www.forgerp.net) | " \
                                             "[Datenschutzerklärung](https://verwaltung.forgerp.net/datenschutz.php)"
        embed.title = "Forgerp.net Entbannungsanträge"
        return embed

    async def get_user_by_id(self, user_id: int):
        """
        :param user_id: The id of the discord user
        :return: Returns discord.User object when the user was found. Otherwise None
        """
        user = self.get_user(user_id)
        if user is None:
            try:
                return await self.fetch_user(user_id)
            except discord.NotFound:
                return None
            except discord.HTTPException or discord.DiscordException:
                logging.error("fetching of user " + str(user_id) + " failed")
                return None
        else:
            return user

    async def sync_appeal_answers(self):
        answers = DiscordBotSync.get_user_appeal_answers()
        for user_id in answers:
            user = await self.get_user_by_id(int(user_id))
            if user is not None:
                for message in answers[user_id]:
                    author = None
                    text = None
                    posted = False
                    appeal_id = None
                    for key, value in message.items():
                        if key == "message":
                            text = value
                        elif key == "author":
                            author = await self.get_user_by_id(value)
                        elif key == "posted":
                            posted = True
                        elif key == "appeal_id":
                            appeal_id = int(value)
                    if author is not None and text is not None:
                        try:
                            await user.send(embed=self.create_appeal_answer(text, author, appeal_id))
                            logging.info(str(user.id) + " -> " + str(text) + " -> by: " + str(author.id))
                            await self.discord_channel_log("INFO", "confirmed user " + str(
                                user.id) + " with appeal answer: " + str(text) + " - answered by: " + str(author.id))
                        except:
                            logging.error("failed to send answer: " + str(text) + " to " + str(user.id))
                            await self.discord_channel_log("ERROR",
                                                           "cannot send answer: " + str(text) + " to " + str(user.id))
                    elif posted:
                        try:
                            await user.send(embed=self.create_appeal_posted(appeal_id))
                            logging.info(str(user.id) + " -> Antrag eingegangen")
                            await self.discord_channel_log("INFO", "confirmed user " + str(
                                user.id) + " that his appeal has arrived")
                        except:
                            logging.error("failed sending message that his appeal was received - to " + str(user.id))
                            await self.discord_channel_log("ERROR",
                                                           "cannot send message that his appeal was received - to " + str(
                                                               user.id))
                    else:
                        logging.warning("failed to send message to " + str(user.id))
                        await self.discord_channel_log("ERROR", "failed to send message to " + str(user.id))

    def create_appeal_posted(self, appeal_id) -> discord.Embed:
        embed = discord.Embed()
        embed.description = "Wir haben deinen Entbannungs-Antrag erhalten! " \
                            "Du wirst hier von diesem Bot Benachrichtigt sobald er bearbeitet ist." \
                            "\n\nMit freundlichen Grüßen,\nDie Projektleitungen"
        embed.set_footer(text=f"Antrags-ID: {appeal_id}")
        embed.timestamp = datetime.utcnow()
        return embed

    def create_appeal_answer(self, message, author: discord.User, appeal_id) -> discord.Embed:
        embed = discord.Embed()
        embed.description = message
        embed.set_author(name=author.display_name + " hat auf deinen Entbannungsantrag reagiert",
                         icon_url=author.display_avatar)
        embed.set_footer(text=f"Antrags-ID: {appeal_id}")
        embed.timestamp = datetime.utcnow()
        return embed


intents = discord.Intents.default()
intents.message_content = True

client = Client(
    intents=intents
)
client.run(str(config.get("Settings", "token")))
