#!/usr/bin/python2

from socket import *
import re
import xml
import xml.dom.minidom as dom
import string
import signal
import sys
import MySQLdb as mdb
import ipaddr

def parse(xml):
	l = []
	try:
		tree = dom.parseString(xml)
	except xml.parsers.expat.ExpatError:
		print >> sys.stderr , xml
		return []

	d = {}
	for i in tree.firstChild.childNodes:
		if i.nodeName == "ASCII_MSG":
			for elems in i.childNodes:
				if elems.nodeName == "UPDATE":
					for update in elems.childNodes:
						if (update.nodeName == "NLRI"):
							for nlri in update.childNodes:
								if (nlri.nodeName == "PREFIX"):
									for prefix in nlri.childNodes:
										if (prefix.nodeName == "ADDRESS"):
											for txt in prefix.childNodes:
												s = txt.data.split("/")
												if not "prefix" in d:
													d["prefix"] = []
												p = {}
												p["address"] = s[0]
												p["len"] = s[1]
												d["prefix"].append(p)
						if (update.nodeName == "PATH_ATTRIBUTES"):
							for pattr in update.childNodes:
								if (pattr.nodeName == "ATTRIBUTE" ):
									for pattr in pattr.childNodes:
										if (pattr.nodeName == "AS_PATH"):
											for asPath in pattr.childNodes:
												if (asPath.nodeName == "AS_SEG"):
													length = int(asPath.getAttribute("length"))
													origin_as = asPath.childNodes[length-1]
													d["origin_as"] = str(origin_as.childNodes[0].data)
			break;
	if ("prefix" in d):
		l.append(d)
	return l

# with open(sys.argv[1], 'r') as content_file:
#     content = content_file.read()
#     d = parse(content)
#     print "ASN \t Prefix \tLength"
#     for i in d:
#     	for j in i["prefix"]:
#     		print i["origin_as"] + "\t" + j["address"] + "\t" + j["len"]

connection=mdb.connect('localhost', 'root', 'noedelsoep', 'bgp')
cursor=connection.cursor()
cursor.execute("SELECT * FROM export")
result = cursor.fetchall()

cli = socket( AF_INET ,SOCK_STREAM)
cli.connect(("livebgp.netsec.colostate.edu", 50001))
data = ""
msg = ""
signal.signal(signal.SIGPIPE , signal.SIG_DFL)
signal.signal(signal.SIGINT , signal.SIG_IGN)

while True:
	data = cli.recv(1024) #14= </BGP_MESSAGE >
	if (re.search('</BGP_MESSAGE>', msg)):
		l = msg.split('</BGP_MESSAGE>', 1)
		bgp_update = l[0] + "</BGP_MESSAGE>"
		bgp_update = string.replace(bgp_update ,"<xml>","")
		d = parse(bgp_update)
		msg = ''.join(l[1:])
		for i in d:
			for j in i["prefix"]:
				if j["address"].endswith("::"):
					intbin = long(bin(ipaddr.IPv6Network(j["address"]).network)[2:])
					strbin = "%0128d" % (intbin)
				else:
					intbin = int(bin(ipaddr.IPv4Network(j["address"]).network)[2:])
					strbin = "%032d" % (intbin)
				cursor.execute("""SELECT c.code FROM ip2nationCountries c, ip2nation i 
					WHERE i.ip < INET_ATON('%s') 
	            	AND c.code = i.country ORDER BY i.ip DESC LIMIT 0,1""" % (j["address"]))
				country = cursor.fetchone()
				valid=0
				for x in result:
					if x[3] == strbin[:len(x[3])]:
						if x[0][2:] == i["origin_as"] and j["len"] >= x[1].split("/")[1] and j["len"] <= x[2]:
							valid=1
							cursor.execute("""INSERT INTO announcements (ASN, IP_Prefix, Validity, Country) VALUES ('%s', '%s/%s', 'V', '%s') ON DUPLICATE KEY UPDATE Validity='V', Country='%s';""" % (i["origin_as"], j["address"], j["len"], country[0], country[0]))
							# print "Message:\t" + i["origin_as"] + "/" + x[0][2:] + "\t" + \
							# j["address"] + "/" + x[1].split("/")[0] + "\t" + \
							# j["len"] + "/" + x[1].split("/")[1] + "-" + x[2] + "\tValid"
							break

						elif (x[0][2:] != i["origin_as"]) and (j["len"] < x[1].split("/")[1] or j["len"] > x[2]):
							# print "Message:\t" + i["origin_as"] + "/" + x[0][2:] + "\t" + \
							# j["address"] + "/" + x[1].split("/")[0] + "\t" + \
							# j["len"] + "/" + x[1].split("/")[1] + "-" + x[2] + "\tInvalid prefix+AS"
							valid=5

						elif x[0][2:] != i["origin_as"]:
						 	# print "Message:\t" + i["origin_as"] + "/" + x[0][2:] + "\t" + \
						 	# j["address"] + "/" + x[1].split("/")[0] + "\t" + \
						 	# j["len"] + "/" + x[1].split("/")[1] + "-" + x[2] + "\tInvalid AS"
						 	valid=6

						elif j["len"] < x[1].split("/")[1] or j["len"] > x[2]:
							# print "Message:\t" + i["origin_as"] + "/" + x[0][2:] + "\t" + \
							# j["address"] + "/" + x[1].split("/")[0] + "\t" + \
							# j["len"] + "/" + x[1].split("/")[1] + "-" + x[2] + "\tInvalid prefix"
							valid=7

				if valid == 0:
					cursor.execute("""INSERT INTO announcements (ASN, IP_Prefix, Validity, Country) VALUES ('%s', '%s/%s', 'U', '%s') ON DUPLICATE KEY UPDATE Validity='U', Country='%s';""" % (i["origin_as"], j["address"], j["len"], country[0], country[0]))
				elif valid == 5:
					cursor.execute("""INSERT INTO announcements (ASN, IP_Prefix, Validity, Country) VALUES ('%s', '%s/%s', 'IB', '%s') ON DUPLICATE KEY UPDATE Validity='IB', Country='%s';""" % (i["origin_as"], j["address"], j["len"], country[0], country[0]))
				elif valid == 6:
					cursor.execute("""INSERT INTO announcements (ASN, IP_Prefix, Validity, Country) VALUES ('%s', '%s/%s', 'IA', '%s') ON DUPLICATE KEY UPDATE Validity='IA', Country='%s';""" % (i["origin_as"], j["address"], j["len"], country[0], country[0]))
				elif valid == 7:
					cursor.execute("""INSERT INTO announcements (ASN, IP_Prefix, Validity, Country) VALUES ('%s', '%s/%s', 'IP', '%s') ON DUPLICATE KEY UPDATE Validity='IP', Country='%s';""" % (i["origin_as"], j["address"], j["len"], country[0], country[0]))
				connection.commit()

				# if result != []:
				# 	#print "Result is not null"
				# 	#print result
				# 	for x in result:
				# 		print "========Begin Match========"
				# 		print "Message:\t" + i["origin_as"] + "\t" + j["address"] + "\t" + j["len"] + "\t" + binstr[:int(j["len"])]
				# 		print "Database:\t" + x[0] + "\t" + x[1] + "\t" + x[2] + "\t" + x[3] + "\t"
				# 		print "========End Match========"
				# 		break
				# 		if j["len"] <= x[2]:
				# 			print i["origin_as"] + "\t" + j["address"] + "\t" + j["len"] + "\tValid"
				# 		else:
				# 			print i["origin_as"] + "\t" + j["address"] + "\t" + j["len"] + "\tInvalid-B"
				# else:
				# 	break
				# 	cursor.execute("SELECT * FROM export WHERE ASN = '%s' OR IP_PREFIX = '%s/%s'"% (i["origin_as"],j["address"],j["len"]))
				# 	result = cursor.fetchall()
				# 	if result != []:
				# 		print i["origin_as"] + "\t" + j["address"] + "\t" + j["len"] + "\tUwotm8?"
				# 	else:
				# 		print i["origin_as"] + "\t" + j["address"] + "\t" + j["len"] + "\tUnknown"


				#print i["origin_as"] + "\t" + j["address"] + "\t" + j["len"]
	msg += str(data)