# fb-like-tracker
Track the growth of any Facebook pages easily.

## Setup
1. Clone onto your server. 
2. Edit pages.php.example with the graph numbers of each page you want to track, and rename it pages.php
3. Set up a cron job to run scrape.php once per day.

Data is stored in the data/ directory, in csv files. Performance might be better with MySQL or something like that, but the script runs in under a minute with 100+ pages and over a year of data.
