#!/bin/bash
#
# Starts the bot in a screen

name="bot-flixbot-entbannung"

if screen -list | grep $name; then
  echo "${name} is already online"
else
  cd /path_to_project/protected/discord-bot/
  screen -A -ln -dmS $name bash -c 'python3 /path_to_project/protected/discord-bot/pardonbot.py'
  echo "Discord bot: ${name} started!"
fi
