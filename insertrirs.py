#!/usr/bin/python2

import MySQLdb as mdb
from time import strftime
import ipaddr

connection=mdb.connect('localhost', 'username', 'password', 'bgp') #Don't worry, not a real password
cursor=connection.cursor()

tablepart = strftime("%d-%m-%y")

#IPv4:
source = open("rirs", "r")

for line in source:
	prefix = line.rstrip().split(" ")[0]
	rir = line.rstrip().split(" ")[1]
	cursor.execute("UPDATE `%s_routes` SET `RIR` = '%s' WHERE IPver = '4' AND `Prefix` LIKE '%s.%%'" % (tablepart, rir, prefix))

connection.commit()

#IPv6:
source = open("rirs-ipv6", "r")

for line in source:
	prefix = line.rstrip().split(" ")[0]
	binprefix = "%0128d\n" % (long(bin(ipaddr.IPv6Network(prefix).network)[2:]))
	length = int(prefix.split("/")[1])
	matchprefix = "%s" % (binprefix[:length])
	rir = line.rstrip().split(" ")[1]
	cursor.execute("UPDATE `%s_routes` SET `RIR` = '%s' WHERE IPver = '6' AND `Bin` LIKE '%s%%'" % (tablepart, rir, matchprefix))

connection.commit()
connection.close()
