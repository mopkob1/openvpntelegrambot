# Telegram bot for managing OpenVPN server build in docker container
![telegramopenvpnboten](https://user-images.githubusercontent.com/4906501/209712946-8ea103be-f8da-4b61-a545-4af6f28290c5.png)

## Table of Contents
- [Introduction](#introduction)
- [Quick start](#quick-start)
- [Installation instructions](#installation-instructions)
  - [Create your first bot](#create-your-first-bot)
  - [Configure your nginx to serve container](#configure-your-nginx-to-serve-container)
  - [Configuring containers](#configuring-containers)
- [Bot service](#bot-service)
- [Documentation](#documentation)
- [Demo bot](#demo-bot)
- [Contributing](#contributing)
- [Donate](#donate)
- [License](#license)


## Introduction
This software is designed for rapid deployment and management of the [openVPN](https://openvpn.net/community/)
via a [telegram bot](https://telegram.org/). For ease of deployment, everything is packed into two small [docker](https://www.docker.com/) containers with a minimum
of settings.
All this is designed to combine **small workgroups and/or servers** into a **VPN network**.

```iptables``` settings are provided inside the container, which allow you to control the visibility of hosts within
the network.

Issues of authorization, identification, adding and removing certificates, visibility management,
information and correspondence are combined into a minimalistic set of bot commands.

In addition to certificate and host management functions, containers provide the following functions:  
- separation of users into admins and employers inside the bot  
- hiring and firing users inside the bot  
- adding certificates to a user (one user can have multiple certificates)  
- signing and unsubscribing users from notifications in the bot  
- registration of hiring (connecting) a user in an external api  
- the ability to notify all users (subscribed to alerts) using the **HTTPs REST API**  
- notification of bot administrators about all significant events (hiring, dismissal, ordering certificates)  
- possibility of limited correspondence between admins and employers  

Containers are supplied with iptables settings that do not allow **VPN hosts** to access the Internet via
the server address. 

## Quick start
1. Clone this repository and enter into directory;  
2. Rename ```.env.example``` to ```.env```;
3. Fill in follow variables in ```.env```:
```shell
# Telegram settings:
BOT_API_KEY='<tg-bot-token>'
BOT_USERNAME='<tg-bot-name>'
HOOK_URL=https://<bot-hook>

# Bot Admins Settings:
ADMINS=tguserid1,tguserid2,tguserid3,tguserid4,...

# OpenVPN Server Settings:
VPN_DOMAIN=<vpn_host_name_or_address>
VPN_PORT=<vpn_port_number>
VPN_NET=192.168.0.0/24
API_PORT=<api_port_number>
```  
4. Run docker-composer: ```docker-compose up --build -d```;  
5. Run init script: ```./init.sh``` (answer only one question. Simple: ```<vpn_host_name_or_address>```);  
6. Create virtual host to proxy telegram-web-hook address (```https://<bot-hook>```);  
to ```127.0.0.1:<api_port_number>``` for you web server (see example for __nginx__ follow);
7. Optional: open ```<vpn_port_number>``` in you firewall. 

## Installation instructions

### Create your first bot  

There is a lot of information available on how to create a bot. My instructions are in a separate [file](./code/tgbot/README.md).

### Configure your nginx to serve container  

In this part of the setup, I assume that:   
- you are setting up a system on a server that has an ip address accessible from the internet    
- you have configured the certificate to use encryption (https protocol), 
for example, using the service [LetsEncrypt](https://letsencrypt.org/)

```shell
server {
	server_name <domain_name>;
	root /var/www/html;
	access_log  /var/www/html/access.log;
	error_log   /var/www/html/error.log;

	index  index.api index.html index.htm index.nginx-debian.html;

        location / {
            try_files $uri /index.api?$args;
        }

        location ~ \.api$ {
            root /var/www/public;
    	    fastcgi_split_path_info ^(.+\.api)(/.+)$;

    	    fastcgi_pass 127.0.0.1:<api_port>;
	        fastcgi_index index.api;
            fastcgi_read_timeout 1000;
	        include fastcgi_params;
	        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
            fastcgi_param PATH_INFO $fastcgi_path_info;
            resolver 127.0.0.1;
        }

    location ~ /\.ht {
    	deny all;
    }

    listen 443 ssl; # managed by Certbot
    ssl_certificate /etc/letsencrypt/live/<domain_name>/fullchain.pem; # managed by Certbot
    ssl_certificate_key /etc/letsencrypt/live/<domain_name>/privkey.pem; # managed by Certbot
    ssl_trusted_certificate /etc/letsencrypt/live/<domain_name>/chain.pem;
}
```

### Configuring containers

The service is serviced by two containers:
- container with OpenVPN server
- container providing HTTPs management (via REST API and via telegram bot)

You can change the name of both containers in the [docker-compose.yml](./docker-compose.yml).
The basic rule: the name of the container with the __OpenVPN server__ should not contain an __api__ letter combination,
the name of the container responsible for the __REST API__ should contain an __api__ letter combination.

__The configuration is contained in the file ```.env```__

#### Minimum settings

ID of the current user in the container:  
```shell
USER_ID=1000
GROUP_ID=1000
```

TelegramBot Settings:  
```shell
BOT_API_KEY='<tg-bot-token>'
BOT_USERNAME='<tg-bot-name>'
HOOK_URL=https://<bot-hook>
```

Bot Admin Settings:  
```shell
ADMINS=tguserid1,tguserid2,tguserid3,tguserid4,...
```

OpenVPN Server Settings:  
```shell
VPN_DOMAIN=<vpn_host_name_or_address>
VPN_PORT=<vpn_port_number>
VPN_NET=192.168.xxx.0/23
API_PORT=<api_port_number>
```

#### Advanced settings
Settings of the access token for informing users of the function via the REST API.

To use the function, you need to send a POST request:  
```shell
POST https://<domain-name>/api/resend
Content-Type: application/json
{
  "secret-token-field-name":"secret-token",
  "date":"2022-06-27 00:46:10.239072",
  "body":"Some text second part https://telegra.ph/noFebruary-VPN-for-small-groups-of-developers-12-08",
  "phone":"Some text first part ",
  "type":"sms"
}
```

The names of authorization fields can be both in the request body and in its header.
They are set by the following variables in ```.env```:  
```shell
API_KEY_NAME=<secret-token-field-name>
API_KEY_VAL=<secret-token>
```

If you want each added user to register in
the external api you specified, then define the following variables:

```shell
REG_HEADER=<reg-auth-header-name>
REG_TOKEN=<reg-auth-token>
REG_URL=https://<reg-api-url>
```

Each time a new user is registered, the container will send a request:

```shell
POST https://<reg-api-url>
Content-Type: application/json
<reg-auth-header-name>: <reg-auth-token>

{
  "url":"https://<bot-hook>",
  "name":"<tg_bot_name>",
  "userinfo":"first_name last_name, tg_user_name, tg_user_id"
}
```

In the ```.env``` file, you can specify any other variables that you need when creating
response pages.
In the current version, you can configure a response to an "empty request" (for cases when the user
sent a non-existing command to the bot) - file [empty.tg.md](./www/tgbot/empty.tg.md).
It is also possible to issue a welcome page (the response of the bot that every new
user sees) - file [welcome.tg.md ](./www/tgbot/welcome.tg.md).  
In ```.env``` you specify a variable:  
```shell
EMPTY_MSG="You can use commands from help (*/help*)."
```

To display the value of this variable, it is enough to insert fields of the form: ```#{EMPTY_MSG}```

## Bot service  

The bot allows you to manage:
1. By bot users;
2. OpenVPN certificates for users;
3. Additional hosts for users;
4. Visibility of hosts inside the VPN network.

Some of the commands are available to users, some only to administrators.
Administrators are informed about all significant actions regarding users and their certificates.

### Bot User Management

The bot allows you to hire and fire "employees" (bot users).

Send a hiring request (__from any user__):
```shell
/hire
```
The command sent by a regular user will notify administrators
about the desire to hire and show them the user profile.

Hire an employee (__administrator only__):
```shell
/hire <tg-userid>
```

Dismiss an employee or quit:
```shell
/fire [<tg-userid>]
```

Subscribe to broadcast notifications (__can be sent via REST API__):
```shell
/subs [<tg-userid>]
```

Unsubscribe from the suggestions:
```shell
/unsubs [<tg-userid>]
```

Write something to administrators:
```shell
/admin <message>
```

Write something to the bot user (__available only to administrators__):
```shell
/s <tg-userid|tg-username> <message>
```

Get a list of bot users and their current status (__available only to administrators__):
```shell
/staff
```

### OpenVPN Certificate Management

Send a request for an OpenVPN certificate (__from any user__):
```shell
/newuser
```
A command sent by a regular user will notify administrators
about the desire to get a certificate and will show them the user profile.

Create or receive an already created OpenVPN certificate (__administrator only__):
```shell
/newuser <tg-userid|hostname>
```

Revoke (delete) the OpenVPN certificate:
```shell
/revore <tg-userid|hostname>
```

Prohibit connecting to the VPN network without revoking the certificate (__administrator only__):
```shell
/disable <tg-userid|hostname>
```

Allow to connect to the VPN network by revoking the connection ban (__administrator only__):
```shell
/enable <tg-userid|hostname>
```

Get a list of VPN network hosts and their current status (__administrator only__):
```shell
/users
```

Send previously made certificates for one or more hosts to any
bot user (__administrator only__):
```shell
/impart <fullORpart-name-of-host(s)> <tg-userid|hostname>
```

### Managing additional (virtual) hosts for users

In real life, each user may require additional certificates
(for a second computer, for a mobile phone, for a server, etc.).

To obtain additional certificates, the bot allows you to create virtual hosts (__available only to administrators__):
```shell
/newvirt [<anysymbols>]
```

Get a list of virtual hosts (__available only to administrators__):
```shell
/virts
```

Delete a virtual host (__available only to administrators__):
```shell
/rmvirt <virt-host-name>
```

### Managing the visibility of hosts inside a VPN network

The configuration of ``iptables`` inside the OpenVPN server container is made in such a way that VPN hosts
cannot "see" each other. This does not apply to the VPN server (usually, its address looks like this: ```XXX.XXX.XXX.1```)

In order for the hosts to see each other, you need to give the bot the following command (__available only to administrators__):
```shell
/connect <tg-userid1|hostname1> <tg-userid2|hostname2>
```

If some host should be accessible to everyone inside the network (public), then you need to give the bot the following
command (__available only to administrators__):
```shell
/public <tg-userid|hostname>
```

If some host should not see the public host, then you need to give the bot the following command (__available only to administrators__):
```shell
/obscure <tg-userid-of-public-host|username-of-public-host> <tg-userid|hostname>
```

If any host needs to "reset" the visibility settings, then it is necessary to give the bot the following
command (__available only to administrators__):
```shell
/hide <tg-userid|hostname>
```

The current visibility settings can be obtained by giving the bot a command (__available only to administrators__):
```shell
/net
```

The visibility of hosts is always two-sided.

## Troubleshooting
When you start your containers, you may want to check your bot without webserver.  
1. Install that (debian example):  
```shell
# Install cgi-fcgi:
sudo apt update && sudo apt install libfcgi0ldbl
```
2. Run that and get 200:  
```shell
SCRIPT_NAME=/index.php \
SCRIPT_FILENAME=/var/www/html/index.php \
REQUEST_METHOD=GET \
cgi-fcgi -bind -connect 127.0.0.1:<api_port>
```

## Documentation
No additional documentation. But if you like to read or want to develop something on top of this software, 
you can read follow sources:  
- [Open VPN for docker](https://github.com/kylemanna/docker-openvpn) - take as a basis when developing a container with an OpenVPN server;    
- [PHP Telegram Bot framework](https://github.com/php-telegram-bot/core).  

The above code may contain errors, it may not be good for use in any critical
infrastructure. It was created for personal use, when solving a narrow range of tasks. Any decision
on its use (or modification) is made by you yourself and you are fully responsible for this decision.

## Demo bot
There is a QR code below. Scan it. You will get into telegram-bot.
It is made to demonstrate the capabilities and to support micro-workgroups.
2-3 certificates with each other's visibility are free.

With any business proposals, you can contact the administrators of the demo bot.
![msg514032165-65910](https://user-images.githubusercontent.com/4906501/209350640-a581e6a8-8a68-42da-9d76-215442cb9a4b.jpg)

## Contributing
The easiest way to contribute is to use this software and share it on your social networks.

If you find a bug or want to suggest improvements, write about it using github.

## Donate
[Boosty.to](https://boosty.to/mopkob/single-payment/donation/299388?share=target_link)  
[Ko-Fi.com](https://ko-fi.com/mopkob)  
[destream.net](https://destream.net/live/mopkob/donate)  

## License
Please see the [LICENSE](./LICENSE) included in this repository for a full copy of the MIT license, which this project is licensed under.

