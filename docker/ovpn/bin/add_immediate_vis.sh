#!/bin/bash

# Добавляет видимость для одной пары
# $1 - первый пользователь
# $2 - второй пользователь
# Если $3 не пустой, то видеть будет только $1

if [ -z "$OPENVPN" ]; then
    export OPENVPN="$PWD"
fi

if ! source "/opt/code/.lib"; then
    echo "Could not source /opt/code/.lib."
    exit 1
fi

if ! source "$OPENVPN/ovpn_env.sh"; then
    echo "Could not source $OPENVPN/ovpn_env.sh."
    exit 1
fi


OPENVPN="${OPENVPN:-/etc/openvpn}"
TMP=/tmp/$(echo "$1$2" | md5sum | awk '{print $1}')
LOOKFOR="INPUT -i eth0 -p udp -m udp --dport 1194 -j ACCEPT"
IPT="$OPENVPN/iptables"

U1=${1:-^%$nbae876^&#*#}
U2=${2:-^%$nbae876^&#*#}
IP1=$(getusers | grep "$U1" | awk -F: '{print $2}' | awk '{print $1}')
IP2=$(getusers | grep "$U2" | awk -F: '{print $2}' | awk '{print $1}')

stop "$IP1" "User1 - not found."
stop "$IP2" "User2 - not found."

TEST=$(iptables-save | grep "$IP1/32 -d $IP2/32")
[ "$3" ] && TEST=$(iptables-save | grep "$IP2/32 -d $IP1/32")

exist "$TEST" "Hosts $IP1($1) and $IP2($2) are visible to each other"

iptables $(iptptp "$IP1" "$IP2")

[ "$3" ] || iptables $(iptptp "$IP2" "$IP1")

# iptables-save > "$IPT"
# START=$(lineinrange "$IPT" "\*filter" "$LOOKFOR")
# insertafter "$START" "$TMP" "$IPT" > "$IPT.1"
# iptables-restore < "$IPT.1"
#rm "$TMP"
#rm "$IPT.1"
iptables-save > "$IPT"

echo "Hosts $IP1($1) and $IP2($2) are visible to each other"
