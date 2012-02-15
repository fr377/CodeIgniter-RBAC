<?php

define('DEBUG', TRUE);

define('DOMAIN', 'http://localhost/rbac/');
define('BASE_DIR', '/var/www/rbac/');

define('CLASSES_DIR', BASE_DIR.'classes/');
define('CSS_DIR', DOMAIN.'classes/form/css/');
define('TPL_DIR', BASE_DIR.'templates/');

define('FORM_INFO_IMG', 'images/question.png');
define('FORM_WARN_IMG', 'images/warning.png');

define('ADODB_INC', '/var/www/adodb/adodb.inc.php');
define('DB_CON_FILE', BASE_DIR.'db_connection.php');
define('DB_DEF_DIR', BASE_DIR.'db/');

define('FORM_DIR', BASE_DIR.'cp/files/forms/');


// Include standard files
include_once CLASSES_DIR.'db/class.db_bv.php';
include_once CLASSES_DIR.'template/class.template_bv.php';
include_once CLASSES_DIR.'form/class.form_bv.php';

?>
