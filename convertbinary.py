#!/usr/bin/python2

import ipaddr

source = open("export.csv", "r")
dest = open("export.csv.binary", "w")
for line in source:
	if not "Length" in line:
		minlength = line.split(",")[1].split("/")[1]
		if not "::" in line:
			prefix = "%032d" % (int(bin(ipaddr.IPv4Network(line.rstrip().split(',')[1]).network)[2:]))
			dest.write(line.rstrip() + ",%s\n" % (prefix[:int(minlength)]))
		else:
			prefix = "%0128d\n" % (long(bin(ipaddr.IPv6Network(line.rstrip().split(',')[1]).network)[2:]))
			dest.write(line.rstrip() + ",%s\n" % (prefix[:int(minlength)]))
	else:
		dest.write(line.replace(" ", "_").rstrip() + ",Bin\n")

source = open("riswhoisdump.IPv4", "r")
dest = open("riswhoisdump.IPv4.binary", "w")
for line in source:
	length = line.split(",")[1].split("/")[1]
	prefix = "%032d" % (int(bin(ipaddr.IPv4Network(line.rstrip().split(',')[1]).network)[2:]))
	dest.write(line.rstrip() + ",%s\n" % (prefix[:int(length)]))

source = open("riswhoisdump.IPv6", "r")
dest = open("riswhoisdump.IPv6.binary", "w")
for line in source:
	length = line.split(",")[1].split("/")[1]
	prefix = "%032d" % (int(bin(ipaddr.IPv6Network(line.rstrip().split(',')[1]).network)[2:]))
	dest.write(line.rstrip() + ",%s\n" % (prefix[:int(length)]))