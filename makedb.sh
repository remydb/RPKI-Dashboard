#!/bin/bash
bash newcsv.sh
python csv2sqlite.py export.csv | mysql -u root -pnoedelsoep bgp
