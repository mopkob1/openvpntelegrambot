#!/bin/bash

# Добавляет видимость всей сети для $1
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
LOOKFOR="FORWARD -j REJECT --reject-with icmp-host-prohibited"
IPT="$OPENVPN/iptables"

IP=$(getusers | grep "$1" | awk -F: '{print $2}' | awk '{print $1}')


iptall "$IP" > "$TMP"
S1=$(sed -n '2p' "$TMP" | awk -F'-s' '{print $2}' | awk '{print $1}')
S2=$(sed -n '2p' "$TMP" | awk -F'-d' '{print $2}' | awk '{print $1}')

TEST=$(iptables-save | grep "$S1" | grep "$S2" | grep "ACCEPT")
exist "$TEST" "Hosts $IP1($1) are published and visible to every host in the network ($OVPN_SERVER)."

iptables-save > "$IPT"

START=$(lineinrange "$IPT" "\*filter" "$LOOKFOR")
let "START= $START - 1"
insertafter "$START" "$TMP" "$IPT" > "$IPT.1"
iptables-restore < "$IPT.1"
rm "$TMP"
rm "$IPT.1"
iptables-save > "$IPT"

echo "Hosts $IP1($1) are published and visible to every host in the network ($OVPN_SERVER)."