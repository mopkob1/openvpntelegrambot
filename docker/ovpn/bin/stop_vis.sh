#!/bin/bash

# Удаляет из раздела filter все строки с упоминанием пользователя $1
OPENVPN="${OPENVPN:-/etc/openvpn}"

if ! source "/opt/code/.lib"; then
    echo "Could not source /opt/code/.lib."
    exit 1
fi

if ! source "$OPENVPN/ovpn_env.sh"; then
    echo "Could not source $OPENVPN/ovpn_env.sh."
    exit 1
fi


TMP=/tmp/$(echo "$1" | md5sum | awk '{print $1}')
IPT="$OPENVPN/iptables"

U1=${1:-^%$nbae876^&#*#}
IP=$(getusers | grep "$U1" | awk -F: '{print $2}' | awk '{print $1}')
stop "$IP" "User1 - not found."

START=$(lineinrange "$IPT" "\*filter" "\*filter")
END=$(lineinrange "$IPT" "\*filter" "COMMIT")
FROM=''
let "FROM= $START - 1"

sed -n "$START,$END p" "$IPT"| grep -v "$IP"> "$TMP"
sed -i "$START,$END d" "$IPT"

insertafter "$START" "$TMP" "$IPT" > "$IPT.1"
iptables-restore < "$IPT.1"
rm "$TMP"
rm "$IPT.1"
iptables-save > "$IPT"

echo "Host $IP1($1) visible only to public hosts."
