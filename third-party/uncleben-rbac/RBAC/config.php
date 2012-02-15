<?php

define('DEBUG', TRUE);

define('BASE_DIR', '/var/www/classes/rbac/');

define('CLASSES_DIR', BASE_DIR);


define('ADODB_INC', '/var/www/adodb/adodb.inc.php');
define('DB_CON_FILE', BASE_DIR.'db_connection.php');
define('DB_DEF_DIR', BASE_DIR.'db/'); // This folder shouldhave write permission


// Include standard files
include_once CLASSES_DIR.'class.db_bv.php';

?>
