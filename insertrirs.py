#!/usr/bin/python2

import MySQLdb as mdb


source = open("rirs", "r")

connection=mdb.connect('localhost', 'root', 'noedelsoep', 'bgp') #Don't worry, not a real password
cursor=connection.cursor()

for line in source:
	prefix = line.rstrip().split(" ")[0]
	rir = line.rstrip().split(" ")[1]
	cursor.execute("UPDATE `today_routes` SET `RIR` = '%s' WHERE IPver = '4' AND `Prefix` LIKE '%s.%%'" % (rir, prefix))

connection.commit()
connection.close()
