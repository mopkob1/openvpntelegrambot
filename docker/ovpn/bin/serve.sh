#!/bin/bash

date >> /tmp/serve
if [ -z "$OPENVPN" ]; then
    export OPENVPN="$PWD"
fi
if ! source "$OPENVPN/ovpn_env.sh"; then
    echo "Could not source $OPENVPN/ovpn_env.sh."
    exit 1
fi
if [ -z "$EASYRSA_PKI" ]; then
    export EASYRSA_PKI="$OPENVPN/pki"
fi
if ! source "/opt/code/.lib"; then
    echo "Could not source /opt/code/.lib."
    exit 1
fi

PIPE=${1:-/opt/pipes/vpnpipe}
PTH=${2:-/opt/files}
CERTS=${3:-/opt/clients}

function watcher() {
  FILE=$1
  WAIT=${2:-4}
  sleep $WAIT
  [ -e "$FILE" ] && return
  [ -e "$FILE.err" ] && return
  echo "Script not started!" > "$FILE.err"
}
function reporter() {
  ERR="$1"
  DONE="${2:-$ERR}"
  [ -e "$ERR" ] && mv "$ERR" "$DONE" || echo "Finished without report" > "$ERR"
}
function clean() {
    sleep 50
    rm "$1$2"
    rm "$1$3"
}
function executer() {
    UNIC=$1
    COMMAND="$2"
    USER1="$3"
    USERorOPTS="$4"

    ERR="$PTH/$UNIC.err"
    DONE="$PTH/$UNIC"

    echo "UNIC: $UNIC COMMAND: $COMMAND USER1: $USER1 USERorOPTS: $USERorOPTS"
    case $COMMAND in
    rules)
      iptables-save > "$DONE"
      ;;
    users)
      getusers > "$DONE"
      ;;
    control)
      control_visability.sh 1>&2 > "$ERR" && reporter "$ERR" "$DONE"
      ;;
    publicate)
      public_host.sh "$USER1" 1>&2 > "$ERR" && reporter "$ERR" "$DONE"
      ;;
    hidetotal)
      stop_vis.sh "$USER1" 1>&2 > "$ERR" && reporter "$ERR" "$DONE"
      ;;
    hidefrom)
      hide_from_host.sh "$USER1" "$USERorOPTS" 1>&2 > "$ERR" && reporter "$ERR" "$DONE"
      ;;
    bidirectional)
      add_immediate_vis.sh "$USER1" "$USERorOPTS" 1>&2 > "$ERR" &&  reporter "$ERR" "$DONE"
      ;;
    newuser)
      [ "$USER1" ] || {
        echo "You have to give user name." >> "$ERR"
        return
      }
      [ -e  "$EASYRSA_PKI/private/${USER1}.key" ] && {
        echo "User: $USER1 already exist." >> "$ERR"
        return
      }
      easyrsa build-client-full "$USER1" nopass 1>&2 >> "$ERR" && reporter "$ERR" "$DONE"
      ;;
    revoke)
      stop_vis.sh "$USER1" 1>&2
      #> "$ERR" && reporter "$ERR" "$DONE"
      echo "yes" | ovpn_revokeclient "$USER1" 1>&2 >> "$ERR" && reporter "$ERR" "$DONE"
      reporter "$ERR" "$DONE"
      ;;
    cert)
      [ "$USER1" ] || {
          echo "You have to give user name." >> "$ERR"
          return
      }
      [ -e "$CERTS/$USER1.ovpn" ] && {
        echo "All confs files are ready." > "$DONE"
        return
      }

      ovpn_getclient "$USER1" separated
      zip -ry "$CERTS/$USER1.zip" "$OPENVPN/clients/$USER1" "$OPENVPN/clients/$USER1.conf"
      [ -e "$CERTS/$USER1.zip" ] || echo "Can't create confs zip for $USER1" >> "$ERR"
      ovpn_getclient "$USER1"
      cat "$OPENVPN/clients/$USER1.ovpn" > "$CERTS/$USER1.ovpn"
      [ -e "$CERTS/$USER1.ovpn" ] || echo "Can't create confs file for $USER1" >> "$ERR"
      [ -e "$ERR" ] || echo "All confs files are ready." >> "$DONE"
      (clean "$CERTS/$USER1" ".ovpn" ".zip" &)
      ;;

    disable)
      USER1=${USER1:-'jask###sds7832ds!saa'}
      [ -e "$OPENVPN/ccd/$USER1" ] || {
        echo "Profile not found." > "$ERR"
        return
      }
      [ "$(grep '#ifconfig' $OPENVPN/ccd/$USER1)" ] && {
        echo "Profile: $USER1 already disabled." > "$DONE"
      } || {
        sed -i "s/ifconfig/#ifconfig/g" "$OPENVPN/ccd/$USER1" 1>&2 && \
                echo "Profile: $USER1 now disabled." > "$DONE" || \
                echo "Unknown error with disabling $USER1" > "$ERR"
      }
      ;;
    enable)
      USER1=${USER1:-'jask###sds7832ds!saa'}
      [ -e "$OPENVPN/ccd/$USER1" ] || {
        echo "Profile not found." > "$ERR"
        return
      }
      [ "$(grep '#ifconfig' $OPENVPN/ccd/$USER1)" ] || {
        echo "Profile: $USER1 already enabled." > "$DONE"
      } && {
        sed -i "s/#ifconfig/ifconfig/g" "$OPENVPN/ccd/$USER1" 1>&2 && \
                echo "Profile: $USER1 now enabled." > "$DONE"  || \
                echo "Unknown error with disabling $USER1" > "$ERR"
      }
      ;;
    *)
      echo "Command '$COMMAND' does't exist." > "$ERR"
      ;;
    esac
}
echo "$PIPE|"  >> /tmp/serve
while true; do
    read OPTS < "$PIPE"
    echo "$OPTS" >> /tmp/serve
    UNIC=$(echo "$OPTS" | awk '{print $1}')
    echo "$UNIC"
    (watcher "$PTH/$UNIC" &)
    executer $OPTS
    (clean "$PTH/$UNIC" "" ".err" &)
done








