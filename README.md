Download Codes
=============

Manage MP3 Download Codes

TODO
-------------
* add modify and del functionality for albums
* add automatic codes backup to flat file upon creation
* add reset code feature

Installation
------------
* upload to your webserver
* create a database and a database user
* edit the config.php file in the main folder
* specify your DB connection info
* specify the path to the folder to use for storing the upladed MP3 archive
* you want to make sure you use a path that's not accessible (ie, outside the web root) so that people can't link directly to the archive files
* password protect the /admin/ folder (use a .htaccess / .htpasswd file)
* browse to /admin/install.php to setup the DB
* once the DB ins setup remove or rename install.php so you don't accidentally destroy your DB later