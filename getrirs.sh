#!/bin/bash

cd `dirname "${BASH_SOURCE[0]}"`
wget -N http://www.iana.org/assignments/ipv4-address-space/ipv4-address-space.txt

#Our solution is bad and we should feel bad
grep -Eo "whois.*.net" ipv4-address-space.txt | cut -d '.' -f2 > row2
grep whois.*.net ipv4-address-space.txt | grep -oE "[0-9]{3}/8" | cut -d '/' -f1 | sed -E 's/^0*//' > row1
paste --delimiter=' ' row1 row2 > rirs
rm row1 row2

python insertrirs.py