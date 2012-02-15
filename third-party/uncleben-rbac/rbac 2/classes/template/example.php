<?php

include 'class.template_bv.php';

$_tpl = &new Template_bv('main.tpl');

// Page title
$_tpl->Assign('<TITLE>', 'Fast Templating class.'); //  Just an example

// CSS in the Head of the HTML document
$_tpl->Assign('<HEAD>', '<link href="text.css" rel="stylesheet" type="text/css">'); //  Just an example
$_tpl->Append('<HEAD>', '<script type="javascript"> function whatever() {}</script>');

// Page header
$_tpl->Assign('<HEADER>', 'Welcome to Template!');
$_tpl->Enclose('<HEADER>', '<h1>', '</h1>');

// Left column
$_tpl->Assign('<LEFT>', 'Left column information');

// Center column
$_tpl->Assign('<CENTER>', '<b>This is the center column.</b>');
$_tpl->Prepend('<CENTER>', 'Add this in front of the the center column. ');
$_tpl->Append('<CENTER>', ' Put this at the end.');


// Right column
$_tpl->Assign('<RIGHT>', 'Right column information');

// Page footer
$_tpl->Assign('<FOOTER>', '<center>Copyright (c) 2006</center>');

// Print to screen
echo $_tpl->PrintTemplate();


?>
