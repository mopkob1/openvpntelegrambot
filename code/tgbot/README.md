# Create your first bot

1. Message [`@BotFather`](https://telegram.me/BotFather) with the following text: `/newbot`

   If you don't know how to message by username, click the search field on your Telegram app and type `@BotFather`, where you should be able to initiate a conversation. Be careful not to send it to the wrong contact, because some users have similar usernames to `BotFather`.

   ![BotFather initial conversation](https://user-images.githubusercontent.com/9423417/60736229-bc2aeb80-9f45-11e9-8d35-5b53145347bc.png)

2. `@BotFather` replies with:

    ```
    Alright, a new bot. How are we going to call it? Please choose a name for your bot.
    ```

3. Type whatever name you want for your bot.

4. `@BotFather` replies with:

    ```
    Good. Now let's choose a username for your bot. It must end in `bot`. Like this, for example: TetrisBot or tetris_bot.
    ```

5. Type whatever username you want for your bot, minimum 5 characters, and must end with `bot`. For example: `telesample_bot`

6. `@BotFather` replies with:

    ```
    Done! Congratulations on your new bot. You will find it at
    telegram.me/telesample_bot. You can now add a description, about
    section and profile picture for your bot, see /help for a list of
    commands.

    Use this token to access the HTTP API:
    123456789:AAG90e14-0f8-40183D-18491dDE

    For a description of the Bot API, see this page:
    https://core.telegram.org/bots/api
    ```

7. Note down the 'token' mentioned above.

*Optionally set the bot privacy:*

1. Send `/setprivacy` to `@BotFather`.

   ![BotFather later conversation](https://user-images.githubusercontent.com/9423417/60736340-26439080-9f46-11e9-970f-8f33bbe39c5f.png)

2. `@BotFather` replies with:

    ```
    Choose a bot to change group messages settings.
    ```

3. Type (or select) `@telesample_bot` (change to the username you set at step 5
   above, but start it with `@`)

4. `@BotFather` replies with:

    ```
    'Enable' - your bot will only receive messages that either start with the '/' symbol or mention the bot by username.
    'Disable' - your bot will receive all messages that people send to groups.
    Current status is: ENABLED
    ```

5. Type (or select) `Disable` to let your bot receive all messages sent to a group.

6. `@BotFather` replies with:

    ```
    Success! The new status is: DISABLED. /help
    ```