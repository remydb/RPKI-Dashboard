RPKI Dashboard
===

This repository holds the files needed to set up an RPKI dashboard like the one hosted here: http://academic.slowpoke.nl

The project consists of two parts, namely the scripts for collecting data and a web-dashboard for presenting the statistics of said data.

Prerequisites:
* MySQL server
  * Database called 'bgp'
* Python (v2.7)
  * Python-ipaddr
  * Python-mysqldb
* PHP5
  * PHP5-mysql

To set up the dashboard, perform the following steps:

* Grab the files from the 'master' branch
* Set up a MySQL database 
* Edit the database credentials for the following files:
  * run_daily.sh
  * insertrirs.py
  * validate_table.py
* Grab the files from the 'dashboard' branch and place them in your website root (or where ever you want them to be)
 * Edit the 'include/functions.php' file and change the mysql username and password
* Edit the 'createstatic.sh' file to place the static HTML files in the website root
* Add rules to your crontab for running 'run_daily.sh' and 'createstatic.sh' every day, eg:

<pre>0 1 * * * /home/xxx/scripts/run_daily.sh > /dev/null 2>&1
0 5 * * * /home/xxx/scripts/createstatic.sh > /dev/null 2>&1</pre>

Note that there is a lot of time between the running of 'run_daily.sh' and the creating of the static pages, as the validation part of the information gathering can take quite a bit of time.
These lines were added to the crontab of the root user, as some file permissions have to be changed during the 'run_daily.sh' script.

Run the scripts manually to check if anything is still broken, hopefully it isn't and you'll be good to go!
