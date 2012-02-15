-------------
REQUIREMENTS:
-------------
MySQL. 4 or greater
PHP 4 or greater
ADODB connection layer (Get it from adodb.sourceforge.net)

-------
LICENSE:
-------

The rbac classes and control panel are distributed under the BSD license.

------
SETUP:
------
1) Run the rbac.sql script located in the /db_design/ folder to create the tables.
2) Change the database connection settings in the db_connection.php file
3) Change the DOMAIN and BASE_DIR constants in the config.php

4) The folder /db/ must have write permission

5) Run the setup.php script to populate the rbac tables with standard values

That is it. You should be ready to go. Point your browser to the index.php file and enjoy.

------
NOTES:
------
You can see how a user is checked in the test.php file.

The control panel is located in the /cp/ folder.

The rbac classes are located in the /classes/rbac/ folder.

Please let me know how the installation went. If you came across a problem please let me know at ben@sqlrecipes.com

All feedback and suggestions are welcome.


