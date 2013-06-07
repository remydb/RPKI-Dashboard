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
import syslog

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

connection=mdb.connect('localhost', 'root', 'noedelsoep', 'bgp') #Don't worry, not a real password
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
	data = cli.recv(1024)
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
							try:
								cursor.execute("""INSERT INTO announcements (ASN, IP_Prefix, Validity, Country) VALUES ('%s', '%s/%s', 'V', '%s') ON DUPLICATE KEY UPDATE Validity='V', Country='%s';""" % (i["origin_as"], j["address"], j["len"], country[0], country[0]))
							except:
								syslog.syslog(syslog.LOG_WARNING, '[Validator] Inserting to MySQL table failed')
							break

						elif (x[0][2:] != i["origin_as"]) and (j["len"] < x[1].split("/")[1] or j["len"] > x[2]):
							valid=5

						elif x[0][2:] != i["origin_as"]:
						 	valid=6

						elif j["len"] < x[1].split("/")[1] or j["len"] > x[2]:
							valid=7

				if valid == 0:
					try:
						cursor.execute("""INSERT INTO announcements (ASN, IP_Prefix, Validity, Country) VALUES ('%s', '%s/%s', 'U', '%s') ON DUPLICATE KEY UPDATE Validity='U', Country='%s';""" % (i["origin_as"], j["address"], j["len"], country[0], country[0]))
					except:
						syslog.syslog(syslog.LOG_WARNING, '[Validator] Inserting to MySQL table failed')
				elif valid == 5:
					try:
						cursor.execute("""INSERT INTO announcements (ASN, IP_Prefix, Validity, Country) VALUES ('%s', '%s/%s', 'IB', '%s') ON DUPLICATE KEY UPDATE Validity='IB', Country='%s';""" % (i["origin_as"], j["address"], j["len"], country[0], country[0]))
					except:
						syslog.syslog(syslog.LOG_WARNING, '[Validator] Inserting to MySQL table failed')
				elif valid == 6:
					try:
						cursor.execute("""INSERT INTO announcements (ASN, IP_Prefix, Validity, Country) VALUES ('%s', '%s/%s', 'IA', '%s') ON DUPLICATE KEY UPDATE Validity='IA', Country='%s';""" % (i["origin_as"], j["address"], j["len"], country[0], country[0]))
					except:
						syslog.syslog(syslog.LOG_WARNING, '[Validator] Inserting to MySQL table failed')
				elif valid == 7:
					try:
						cursor.execute("""INSERT INTO announcements (ASN, IP_Prefix, Validity, Country) VALUES ('%s', '%s/%s', 'IP', '%s') ON DUPLICATE KEY UPDATE Validity='IP', Country='%s';""" % (i["origin_as"], j["address"], j["len"], country[0], country[0]))
					except:
						syslog.syslog(syslog.LOG_WARNING, '[Validator] Inserting to MySQL table failed')
				connection.commit()

	msg += str(data)