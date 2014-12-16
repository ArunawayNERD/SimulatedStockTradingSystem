This document will discuss the cronjobs used by the system . This document is intended for programmers and the systems admin(s)

The system requires four cronjobs to function correctly. For each of the cronjobs there exists a folder
in the cron folder. This folder will contain the script called by the cron daemon. Besides the script the
script, the folder will also contain any other files needed by the script to execute correctly.


The cronjob schedules are contained in the file crontab.txt This text file is given to crontab to
to set up the crontabs


Job List
==========
1) dailyStockUpdate
2) dailyLogMaker
3) update_historical
4) compJob


Jobs
==========
Folder: dailyStockUpdate
Script: UpdateScript.sh
Run Time: Every hour between 8:00 am and 5:00 PM every Monday- Friday
Description: The purpose of this script is to automate the process of updating our stock data every
			 hour. The script will call a php function defined in the StockEngine folder which is 
			 responsible for the actual stock data retrieval. The script will then call a sql file
			 which will add the new data to the stocks database


Folder: dailyLogMaker
Script: dailyLogMaker.sh
Run Time: Every day a 11:00 pm
Description: The purpose of this job is to create the new log file for the coming day. It is
			 It is run at night because the log file needs to have certain permissions set. If
			 we simply allowed the first logging object to create the log file then we could
			 not guarantee that the permissions would be set correctly


Folder: update_historical
Script: update_historical.sh
Run Time: Every Monday - Friday at 8:00 PM
Description: The purpose of this script is to add the current day's closing prices to the historical
			 database. It is through this script and batch uploads of data that the system maintains
			 stock data for the whatif Scenarios


Folder: compJob 
Script: compJob
Run Time: One minute past the hour every hour
Description: This cron job manages the competitions. If a competition start time has passed then
			 the script will call the startComp method on it. The competition has ended then the
			 the script will call the endComp method on it.
