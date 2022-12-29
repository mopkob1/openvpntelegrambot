#!/bin/bash

# Режет сеть в краткой записи ($1)
# на подсети заданные маской ($2)
# Выдает диапазон фактических адресов (строка: первый последний)
# OPENVPN="${OPENVPN:-/home/mopkob/apps/docker-openvpn}"
source "/opt/code/.lib"

nets(){
  netcalc -s $2 $1  | \
  awk '/Split/,0'| \
  grep -v Split | \
  awk '{print $3" "$5}'| \
  grep -v '^[ ]*$'
}

NET="${1:-192.168.220.0/23}"
MASK="${3:-30}"
SEP="${2:- }"
for BORDERS in $(nets $NET $MASK) ; do
  [ "$START" ] || {
    START=$(changeip ${BORDERS% *})
    continue
  }

  END=$(changeip ${BORDERS##* } "-")
  echo "$START$SEP$END"
  START=""
  END=""
done