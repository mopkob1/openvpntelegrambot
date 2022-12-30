#!/bin/bash
source ./.env


NEEDSETBOT=yes
NEEDPIPE=yes
NEEDCONF=yes
NEEDPKI=yes
NEEDRULES=yes
#NEEDDEBUG=yes


export APIID=$(grep api docker-compose.yml | grep container_name |  awk -F': ' '{print $2}')
export OVPNID=$(grep -v api docker-compose.yml | grep container_name |  awk -F': ' '{print $2}')

VPN_MASK=$(docker run --rm troglobit/netcalc:latest netcalc "${VPN_NET}" | grep -i netmask | awk '{print $3}')
VPN_ADDR=$(docker run --rm troglobit/netcalc:latest netcalc "${VPN_NET}" | grep -i address | awk '{print $3}')

cont(){
    CONT=${3:-$DBID}
    docker exec -it "${CONT}" sh -c "$1 1>&2 " && echo "$2" 1>&2
}
cont_cat(){
    CONT=${4:-$DBID}
    docker exec -i "${CONT}" sh -c "$1"  < $3 && echo "$2"
}

VENDOR="cd /var/www/html && composer install"
SETBOT="php unset.php"
STORAGE="php /var/www/html/app/consts.php; chmod -R a+w ./Store"
RULES="cat /opt/code/iptables.tmpl.noforward > /opt/code/iptables.tmpl; control_visability.sh"
#RULES="cat /opt/code/iptables.tmpl.forward > /opt/code/iptables.tmpl; control_visability.sh"
CLEAN="rm /opt/files/*"

PIPE='[ -e "/opt/pipes/vpnpipe" ] || mkfifo /opt/pipes/vpnpipe ; chmod a+w /opt/pipes/vpnpipe'

CONF="ovpn_genconfig -u udp://${VPN_DOMAIN}:${VPN_PORT} -e /opt/code/server_adds.conf -s ${VPN_NET} -p 'route ${VPN_ADDR} ${VPN_MASK} vpn_gateway' -r 0 -d -E /opt/code/client_adds.conf -C AES-256-CBC"
#CONF="ovpn_genconfig -u udp://${VPN_DOMAIN}:${VPN_PORT} -s ${VPN_NET} -p 'route ${VPN_ADDR} ${VPN_MASK} vpn_gateway' -C AES-256-CBC"
PKI="ovpn_initpki nopass"



[ "$NEEDSETBOT" ] && {
    [ -e "./code/tgbot/.env" ] && rm ./code/tgbot/.env
    ln .env ./code/tgbot/.env
    cont "${VENDOR}" " - Packages installed!" "${APIID}"
    curl "https://api.telegram.org/bot${BOT_API_KEY}/setwebhook?url=${HOOK_URL}" \
    && echo -e "\n - Bot set"
    curl "https://api.telegram.org/bot${BOT_API_KEY}/getWebhookInfo"
    echo ""
    cont "${STORAGE}" " - Storage created" "${APIID}"
}

[ "$NEEDPIPE" ] && {
    cont "${PIPE}" " - Pipe created." "${APIID}"
}

[ "$NEEDCONF" ] && {
  cont "${CONF}" " - VPN SERVER Configured!" "${OVPNID}"
}

[ "$NEEDPKI" ] && {
  cont "${PKI}" " - CERTs center are ready!" "${OVPNID}"
}

echo "Waiting server initialization at about 30 sec ..."
sleep 35

STATUS=$(docker-compose logs | grep "Initialization Sequence Completed")
[ "$STATUS" ] && echo "Open VPN server was started!" || echo "Open VPN server not started!"

[ "$NEEDRULES" ] && {
  cont "${RULES}" " - Setting iptables control." "${OVPNID}"
}
[ "$NEEDDEBUG" ] && {
  sudo mkdir -p /tmp/xdebug
  sudo chmod -R 777 /tmp/xdebug
}