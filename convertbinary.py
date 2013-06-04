#!/usr/bin/python2

import ipaddr

source = open("export.csv", "r")
dest = open("export.csv.binary", "w")
for line in source:
	if not "Length" in line:
		if not "::" in line:
			dest.write(line.rstrip() + "," + bin(ipaddr.IPv4Network(line.rstrip().split(',')[1]).network)[2:] + "\n")
		else:
			dest.write(line.rstrip() + "," + bin(ipaddr.IPv6Network(line.rstrip().split(',')[1]).network)[2:] + "\n")
	else:
		dest.write(line.rstrip() + ",Binary\n")