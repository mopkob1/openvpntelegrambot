#!/bin/bash

if [ $# -lt 2 ]; then
    echo "Usage: `basename $0` <network> </netmask>"
    echo "Example: `basename $0` 192.168.2.0 /23"
    exit 1
fi

export NETWORK=$1
export BIG_MASK=$2

(
ipcalc ${NETWORK} ${BIG_MASK} /30 |\
grep -E "(HostMin|HostMax)" |\
tr -s " " |\
cut -f 1,2 -d " " |\
sed -e s/"HostMin: "/"_"/ |\
tr -d "\n";
echo
) |\
tr "_" "\n" |\
sed -e s/"HostMax:"// |\
tail -n +4
