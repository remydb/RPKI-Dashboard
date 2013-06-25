#!/usr/bin/python
#
# By Remy de Boer  & Javy de Koning
#

import sys
import MySQLdb as mdb
import ipaddr
import syslog
import string
from time import gmtime, strftime

#Set up database connection
connection=mdb.connect('localhost', 'root', 'noedelsoep', 'bgp') #Don't worry, not a real password
cursor=connection.cursor()

#Retrieve VRP
#cursor.execute("SELECT * FROM `today_vrp`;")
#vrp = cursor.fetchall()

tablepart = strftime("%d-%m-%y")

#Retrieve total amount of ROAs in database
cursor.execute("SELECT COUNT(*) FROM `%s_vrp`;" % (tablepart))
numroa = cursor.fetchone()

#Some stuff for time tracking
starttime = strftime("%Y-%m-%d %H:%M:%S")
print "Started at: " + starttime

#Start looping through each route
n=0
while n < numroa[0]:
	#Select nth row
	cursor.execute("SELECT * FROM `%s_vrp` LIMIT %d,1;" % (tablepart, n))
	roa = cursor.fetchone()

	cursor.execute("SELECT * FROM `%s_routes` WHERE `Bin` LIKE '%s%%'" % (tablepart, roa[4]))
	result = cursor.fetchall()

	for route in result:

		validity = route[2]
		relroas = route[4] + "%d," % (roa[0])

		if len(route[6]) >= len(roa[4]) and len(route[6]) <= roa[3] and route[0] == roa[1]:
			validity = 'V'
		else:
			#Check if length is too great (prefix too specific)
			if roa[1] == route[0] and len(route[6]) > roa[3]:
				if validity != 'V':
					if len(roa[4]) == int(roa[3]):
						validity = "IP"
					else:
						validity = "IQ"
			#Check if AS number mismatches but prefix falls within valid range
			elif roa[1] != route[0] and len(route[6]) >= len(roa[4]) and len(route[6]) <= roa[3]:
				if validity != 'V':
					validity = "IA"
			#Check if both AS does not match and prefix is too specific
			elif roa[1] != route[0] and len(route[6]) > roa[3]:
				if validity != 'V':
					validity = "IB"

				##############################
				#Validity states:            #
				##############################
				#                            #
				# IP = Fixed length exceeded #
				# IQ = Length range exceeded #
				# IA = AS does not match     #
				# IB = Prefix too specific   #
				#      AND AS does not match #
				#  V = Valid                 #
				#  U = Unknown               #
				##############################
		cursor.execute("UPDATE `%s_routes` SET Validity = '%s', VRP = '%s' WHERE ASN = '%d' AND Prefix = '%s';" % (tablepart, validity, relroas, route[0], route[1]))
		connection.commit()
	n += 1
	print "Done ROA %d/%d" % (n, numroa[0])

currenttime = strftime("%Y-%m-%d %H:%M:%S")
print "Done at: " + currenttime
