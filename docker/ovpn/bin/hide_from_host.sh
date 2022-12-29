#!/bin/bash

# Скрыть хост $1 от хоста $2

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
U2=${2:-^%$nbae876^&#*#}
IP1=$(getusers | grep "$U1" | awk -F: '{print $2}' | awk '{print $1}')
IP2=$(getusers | grep "$U2" | awk -F: '{print $2}' | awk '{print $1}')

stop "$IP1" "User1 - not found."
stop "$IP2" "User2 - not found."

S1=$(hidehost $IP1 $IP2 | awk -F'-s' '{print $2}' | awk '{print $1}')
S2=$(hidehost $IP1 $IP2 | awk -F'-d' '{print $2}' | awk '{print $1}')

TEST=$(iptables-save | grep "$S1" | grep "$S2" | grep "DROP")
exist "$TEST" "Host $IP1($1) now hidden from $IP2($2)."

hidehost "$IP1" "$IP2"  > "$TMP"

LOOKFOR=$(iptall "$IP1" |grep "$OVPN_SERVER" | awk -F"FORWA" '{print $2}')
TO=$(iptall "$IP1" |grep "$OVPN_SERVER")

[ "$LOOKFOR" ] || {
  echo "Host $IP1($1) have't pulished yet."
  exit 0
}
iptables-save > "$IPT"

START=$(lineinrange "$IPT" "\*filter" "$TO" "$LOOKFOR")

stop "$START" "You have to give public host in first place"

let "START=$START - 1"

insertafter "$START" "$TMP" "$IPT" > "$IPT.1"
iptables-restore < "$IPT.1"
rm "$TMP"
rm "$IPT.1"
iptables-save > "$IPT"

echo "Host $IP1($1) now hidden from $IP2($2)."
