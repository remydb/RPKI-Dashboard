#!/usr/bin/python2

import MySQLdb as mdb
from time import strftime

source = open("rirs", "r")

connection=mdb.connect('localhost', 'root', 'noedelsoep', 'bgp') #Don't worry, not a real password
cursor=connection.cursor()

tablepart = strftime("%d-%m-%y")

for line in source:
	prefix = line.rstrip().split(" ")[0]
	rir = line.rstrip().split(" ")[1]
	cursor.execute("UPDATE `%s_routes` SET `RIR` = '%s' WHERE IPver = '4' AND `Prefix` LIKE '%s.%%'" % (tablepart, rir, prefix))

connection.commit()
connection.close()
