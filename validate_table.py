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

#Retrieve total amount of ROAs in database
cursor.execute("SELECT COUNT(*) FROM `today_vrp`;")
numroa = cursor.fetchone()

#Some stuff for time tracking
starttime = strftime("%Y-%m-%d %H:%M:%S")
print "Started at: " + starttime

#Start looping through each route
n=0
while n < numroa[0]:
	#Select nth row
	cursor.execute("SELECT * FROM `today_vrp` LIMIT %d,1;" % (n))
	roa = cursor.fetchone()

	cursor.execute("SELECT * FROM `today_routes` WHERE `Bin` LIKE '%s%%'" % (roa[4]))
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
					validity = "IP"
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
				# IP = Prefix too specific   #
				# IA = AS does not match     #
				# IB = Prefix too specific   #
				#      AND AS does not match #
				#  V = Valid                 #
				#  U = Unknown               #
				##############################
		cursor.execute("UPDATE `today_routes` SET Validity = '%s', VRP = '%s' WHERE ASN = '%d' AND Prefix = '%s';" % (validity, relroas, route[0], route[1]))
		connection.commit()
	n += 1
	print "Done ROA %d/%d" % (n, numroa[0])








	# #Start comparing every ROA to this prefix
	# for roa in vrp:
	# 	#Check if first n bits of a ROA match the first n bits of an IP prefix
	# 	if roa[4] == strbin[:len(roa[4])]:
	# 		#Check if AS number matches and if length falls between min and max length
	# 		if roa[1] == row[0] and length >=roa[2].split("/")[1] and length <= roa[3]:
	# 			#Route is valid
	# 			validity = 'V'
	# 			relroas.append(roa[0])
	# 		else:
	# 			#Check if length is too great (prefix too specific)
	# 			if roa[1] == row[0] and length > roa[3]:
	# 				if validity != 'V':
	# 					validity = "IP"
	# 				relroas.append(roa[0])
	# 			#Check if AS number mismatches but prefix falls within valid range
	# 			elif roa[1] != row[0] and length >=roa[2].split("/")[1] and length <= roa[3]:
	# 				if validity != 'V':
	# 					validity = "IA"
	# 				relroas.append(roa[0])
	# 			#Check if both AS does not match and prefix is too specific
	# 			elif roa[1] != row[0] and length > roa[3]:
	# 				if validity != 'V':
	# 					validity = "IB"
	# 				relroas.append(roa[0])

	# 				##############################
	# 				#Validity states:            #
	# 				##############################
	# 				#                            #
	# 				# IP = Prefix too specific   #
	# 				# IA = AS does not match     #
	# 				# IB = Prefix too specific   #
	# 				#      AND AS does not match #
	# 				#  V = Valid                 #
	# 				#  U = Unknown               #
	# 				##############################

	# #Done looping through ROAs, we now have to check if the AS matches 
	# #
	# # Soon come
	# risas = -1

	# #Now we have all the data, we can update the record:
	# cursor.execute("UPDATE `today_routes` SET Validity = '%s', Country = '%s', VRP = '%s', RISAS = '%d' WHERE ASN = '%d' AND Prefix = '%s';" % (validity, country[0], str(relroas).translate(None, "L"), risas, row[0], prefix))
	# n += 1

	# connection.commit()

	# if n == tenpercent:
	# 	currenttime = strftime("%Y-%m-%d %H:%M:%S")
	# 	print "10 percent at: " + currenttime
	# if n == tenpercent * 2:
	# 	currenttime = strftime("%Y-%m-%d %H:%M:%S")
	# 	print "20 percent at: " + currenttime
	# if n == tenpercent * 3:
	# 	currenttime = strftime("%Y-%m-%d %H:%M:%S")
	# 	print "30 percent at: " + currenttime
	# if n == tenpercent * 4:
	# 	currenttime = strftime("%Y-%m-%d %H:%M:%S")
	# 	print "40 percent at: " + currenttime
	# if n == tenpercent * 5:
	# 	currenttime = strftime("%Y-%m-%d %H:%M:%S")
	# 	print "50 percent at: " + currenttime
	# if n == tenpercent * 6:
	# 	currenttime = strftime("%Y-%m-%d %H:%M:%S")
	# 	print "60 percent at: " + currenttime
	# if n == tenpercent * 7:
	# 	currenttime = strftime("%Y-%m-%d %H:%M:%S")
	# 	print "70 percent at: " + currenttime
	# if n == tenpercent * 8:
	# 	currenttime = strftime("%Y-%m-%d %H:%M:%S")
	# 	print "80 percent at: " + currenttime
	# if n == tenpercent * 9:
	# 	currenttime = strftime("%Y-%m-%d %H:%M:%S")
	# 	print "90 percent at: " + currenttime


currenttime = strftime("%Y-%m-%d %H:%M:%S")
print "Done at: " + currenttime
