#!/bin/bash

PATH=$PATH:/sbin:/bin:/usr/sbin:/usr/bin
installpipe(){
  OUT="$2"
  [ "$OUT" ] || OUT=/dev/stdin
  echo "Looking for: $1" >> $OUT
  while [[ ! -p "$1" ]]; do
      sleep 10s
  done
  (nohup /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf &)
  echo "Installed! $1" >> $OUT
}

isconfigured(){
  OUT="$2"
  [ "$OUT" ] || OUT=/dev/stdin
  FREEZ="$3"
  [ "$FREEZ" ] || FREEZ="30"
  while [[ ! -e "$1" ]]; do
    echo "$1 - not found!" >> "$OUT"
    sleep "$FREEZ"
  done
  echo "$1 - found!" >> $OUT
}

#/usr/bin/mkfifo /opt/pipes/iptable
#/usr/bin/mkfifo /opt/pipes/scripts

PIPE1=/opt/pipes/vpnpipe
PIPE2=/opt/pipes/scripts
FILE=/tmp/results
FILE=/dev/stdin
ARGS="$@"
[ "$ARGS" ] || ARGS="$PIPE1"

for PIPE in $ARGS
do
    installpipe "$PIPE" "$FILE"
done
echo "Pipes, done!" >> "$FILE"

isconfigured "$OPENVPN/openvpn.conf" "$FILE"
isconfigured "$OPENVPN/done" "$FILE"

#rm "$OPENVPN/done"
echo "Starting openvpn ..." >> "$FILE"
ovpn_run
