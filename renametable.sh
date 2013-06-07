#!/bin/bash
mysql -u root -pnoedelsoep bgp -e "rename table announcements to \``date -d yesterday +%d-%m-%y`\`"
mysql -u root -pnoedelsoep bgp -e "CREATE TABLE announcements (
	ASN VARCHAR(20), 
	IP_Prefix VARCHAR(70), 
	Validity VARCHAR(2), 
	Country VARCHAR(40), 
	PRIMARY KEY (ASN, IP_PREFIX));"
