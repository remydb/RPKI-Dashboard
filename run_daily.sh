#!/bin/bash
cd `dirname "${BASH_SOURCE[0]}"`
LOC=`pwd`/
DBUSER=username
DBPASS=password

#Grab the latest VRPs
wget -N http://rpki.surfnet.nl:8080/export.csv &> /dev/null

#And the latest routes
wget -N http://www.ris.ripe.net/dumps/riswhoisdump.IPv4.gz &> /dev/null
wget -N http://www.ris.ripe.net/dumps/riswhoisdump.IPv6.gz &> /dev/null
gunzip -f riswhoisdump.IPv4.gz
gunzip -f riswhoisdump.IPv6.gz

sed '/^%/d' riswhoisdump.IPv4 | sed '/^$/d' | awk '$3 > 4 { print $0 }' | cut -f1,2 | sed -r '/^\{[0-9]*,[0-9]*/d' | sed 's/\t/,/g' | sed 's/{//g' | sed 's/}//g' | awk '{ print $0 ",U,,,4"}' | uniq > riswhoisdump.IPv4.new
sed '/^%/d' riswhoisdump.IPv6 | sed '/^$/d' | awk '$3 > 4 { print $0 }' | cut -f1,2 | sed -r '/^\{[0-9]*,[0-9]*/d' | sed 's/\t/,/g' | sed 's/{//g' | sed 's/}//g' | awk '{ print $0 ",U,,,6"}' | uniq > riswhoisdump.IPv6.new
mv riswhoisdump.IPv4.new riswhoisdump.IPv4
mv riswhoisdump.IPv6.new riswhoisdump.IPv6

#Add binary data to CSV
python convertbinary.py
tail -n +2 export.csv.binary | sed 's/^AS//g' > export.csv
mv riswhoisdump.IPv4.binary riswhoisdump.IPv4
mv riswhoisdump.IPv6.binary riswhoisdump.IPv6
rm export.csv.binary

chown mysql:mysql {riswhoisdump.IPv4,riswhoisdump.IPv6}
#chmod 777 {riswhoisdump.IPv4,riswhoisdump.IPv6}
cp {riswhoisdump.IPv4,riswhoisdump.IPv6,export.csv} /var/lib/mysql/bgp/

#Debug stuff
mysql -u $DBUSER -p$DBPASS bgp -e "DROP TABLE \``date +%d-%m-%y`_vrp\`"
mysql -u $DBUSER -p$DBPASS bgp -e "DROP TABLE \``date +%d-%m-%y`_routes\`"

#Create new database tables
mysql -u $DBUSER -p$DBPASS bgp -e "CREATE TABLE \``date +%d-%m-%y`_vrp\` ( 
       id INT NOT NULL AUTO_INCREMENT,
       ASN INT(10), 
       IP_Prefix VARCHAR(43), 
       Max_Length TINYINT(3), 
       bin VARCHAR(128), 
       PRIMARY KEY (ASN, IP_Prefix, Max_Length),
       INDEX (id));"
mysql -u $DBUSER -p$DBPASS bgp -e "CREATE TABLE \``date +%d-%m-%y`_routes\` (
	ASN INT(10),
	Prefix VARCHAR(43),
	Validity VARCHAR(2),
	RIR VARCHAR(10),
  VRP VARCHAR(100),
  IPver TINYINT(1),
  Bin VARCHAR(128),
	PRIMARY KEY (ASN, Prefix));"

#Wait a second to make sure the tables have been created
sleep 1

#Start pumping everything into the database
mysql -u $DBUSER -p$DBPASS bgp -e "load data infile 'riswhoisdump.IPv4' into table \``date +%d-%m-%y`_routes\` columns terminated by ',' (ASN,Prefix,Validity,RIR,VRP,IPver,Bin);"
mysql -u $DBUSER -p$DBPASS bgp -e "load data infile 'riswhoisdump.IPv6' into table \``date +%d-%m-%y`_routes\` columns terminated by ',' (ASN,Prefix,Validity,RIR,VRP,IPver,Bin);"
mysql -u $DBUSER -p$DBPASS bgp -e "load data infile 'export.csv' into table \``date +%d-%m-%y`_vrp\` columns terminated by ',' (ASN, \`IP_Prefix\`, \`Max_Length\`, bin) SET id = NULL;"

python validate_table.py

wget -N http://www.iana.org/assignments/ipv4-address-space/ipv4-address-space.txt

grep -Eo "whois.*.net" ipv4-address-space.txt | cut -d '.' -f2 > row2
grep whois.*.net ipv4-address-space.txt | grep -oE "[0-9]{3}/8" | cut -d '/' -f1 | sed -E 's/^0*//' > row1
paste --delimiter=' ' row1 row2 > rirs
rm row1 row2

wget -N http://www.iana.org/assignments/ipv6-unicast-address-assignments/ipv6-unicast-address-assignments.txt

grep -Eo "whois.*.net" ipv6-unicast-address-assignments.txt | cut -d '.' -f2 > row2
grep whois.*.net ipv6-unicast-address-assignments.txt | awk '{print $1}' > row1
paste --delimiter=' ' row1 row2 > rirs-ipv6
rm row1 row2

python insertrirs.py

rm riswhoisdump.IPv{4,6} rirs rirs-ipv6 export.csv