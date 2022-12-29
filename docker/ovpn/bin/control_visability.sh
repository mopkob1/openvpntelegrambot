#!/bin/bash
# Устанавливает контроль IPTABLES над видимостью хостов
OPENVPN="${OPENVPN:-/etc/openvpn}"

if ! source "/opt/code/.lib"; then
    echo "Could not source /opt/code/.lib."
    exit 1
fi

if ! source "$OPENVPN/ovpn_env.sh"; then
    echo "Could not source $OPENVPN/ovpn_env.sh."
    exit 1
fi

  TEMPLATE="${1:-/opt/code/iptables.tmpl}"
  TMP=/tmp/ipt.tmp

  [ "$(iptables-save | grep 'filter')" ] || {
    iptables-save > "$TMP"
    echo '*filter' >> "$TMP"
    cat "$TEMPLATE" >> "$TMP"
    echo '' >> "$TMP"
    echo 'COMMIT' >> "$TMP"
    iptables-restore < "$TMP"
  }
  iptables-save > "$TMP"
  START=$(lineinrange "$TMP" "\*filter" "COMMIT" "filter")
  TO=$(lineinrange "$TMP" "\*filter" "COMMIT")
  let "FROM=$START + 1"
  let "TO=$TO - 1"

  sed -i "$FROM,$TO d" $TMP
  cat $TEMPLATE | envsubst > "$TMP.1"
  insertafter "$START" "$TMP.1" "$TMP" > "$OPENVPN/iptables"
  iptables-restore < "$OPENVPN/iptables"
  rm "$TMP"
  rm "$TMP.1"

echo "Visibility of VPN-hosts now controlled by iptables."