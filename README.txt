Created by: Scott Carter - ScottCarter87@gmail.com


Follow these instructions to setup monti on an apache webserver.

1. Start with fresh install of apahce, mysql, php5, and phpmyadmin
2. Copy contents of "html" directory into the webserver root (usually /var/www)
3. Open phpmyadmin in your browser (Usually http://{server-ip}/phpmyadmin) and login as root
4. Create a new user "monti" with password "GMLKeUFGU3temGB9" (NOTICE: THIS IS CASE SENSITIVE SO COPY THE USERNAME AND PASSWORD EXACTLY!!)
5. Go back to the home screen of phpmyadmin and click the import tab.
6. Select the file "monti.sql" in the mysql folder to import the database structure and data
7. Close phpmyadmin and direct your browser to "http://{server-ip}/". If you you put the source files within a folder in /var/www then you will want to browser to "http://{server-ip}/{folder-name}"
8. Start the GrannyTracker daemon by going to "http://{server-ip}/daemon_admin.php" or "http://{server-ip}/{folder-name}/daemon_admin.php" if source files are in a sub folder.
9. At this point GrannyTracker should be working and waiting for emails to log

FAQ's

1. How do I change the database username and password that is used?
   Go into /var/www/ and open the file called "info.php".  In this file is where you can change the database information.
   
2. How do I change the gmail account and password used for GrannyTracker?
   Go into /var/www/ and open the file called "info.php".  In this file is where you can change the gmail information.

3. Do I need to change the gmail and database settings?
   No you do not need to change any of the settings.  If you have followed the instructions above GrannyTracker will work without modifying any of the information in the info.php file.