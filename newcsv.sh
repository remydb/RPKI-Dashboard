#!/bin/bash
rm export.csv
wget "http://rpki.surfnet.nl:8080/export.csv" 
python convertbinary.py
mv export.csv.binary export.csv
