#!/usr/bin/python2

from socket import *
import re
import xml
import xml.dom.minidom as dom
import string
import signal
import sys
import sqlite3

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

connection=sqlite3.connect("validated.db")
cursor=connection.cursor()

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
				#print "Executing cursor"
				#print('SELECT * FROM export WHERE ASN = %s AND IP_PREFIX = %s/%s'% (i["origin_as"],j["address"],j["len"]))
				#quit()
				cursor.execute("SELECT * FROM export WHERE ASN = '%s' AND IP_PREFIX = '%s/%s'" % (i["origin_as"],j["address"],j["len"]))
				#print "Fetching from cursor"
				result = cursor.fetchall()
				if result != []:
					#print "Result is not null"
					#print result
					for x in result:
						print x
						if j["len"] <= x[2]:
							print i["origin_as"] + "\t" + j["address"] + "\t" + j["len"] + "\tValid"
						else:
							print i["origin_as"] + "\t" + j["address"] + "\t" + j["len"] + "\tInvalid-B"
				else:
					cursor.execute("SELECT * FROM export WHERE ASN = '%s' OR IP_PREFIX = '%s/%s'"% (i["origin_as"],j["address"],j["len"]))
					result = cursor.fetchall()
					if result != []:
						print i["origin_as"] + "\t" + j["address"] + "\t" + j["len"] + "\tUwotm8?"
					else:
						print i["origin_as"] + "\t" + j["address"] + "\t" + j["len"] + "\tUnknown"


				#print i["origin_as"] + "\t" + j["address"] + "\t" + j["len"]
	msg += str(data)