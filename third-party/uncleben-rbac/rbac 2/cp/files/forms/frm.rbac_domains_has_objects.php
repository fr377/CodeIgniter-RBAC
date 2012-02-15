<?php
$_frm = & new Form_bv(array('method'=>'POST', 'action'=>'', 'name'=>'frm_rbac_domains_has_objects'));


$_frm->Assign('<!TITLE!>', '<div class="hdr">domains has objects Form:</div>');

// INPUT ---------
$_frm->AssignInput('<DOMAINS_ID>', 'domains_id', array('type'=>'combo', 'ids'=>'', 'first'=>'- -', 
	'options'=>'SELECT CONCAT(name, IF(is_singular = 1, \' (SINGULAR)\', \' (NOT SINGULAR)\')) as name, id FROM rbac_domains ORDER BY is_singular ASC', //'combo', 'ids'=>'', 'first'=>'- -', 'options'=>array('1'=>'one', '5'=>'five')
'class'=>'inp', 'info'=>'', 'maxlength'=>10, 'alt'=>'Domain' ));
$_frm->SetInputRules('domains_id', array('table'=>'rbac_domains_has_objects', 'int', 'maxlength'=>10));
// INPUT ---------
$_frm->AssignInput('<OBJECTS_ID>', 'objects_id', array('type'=>'combo', 'ids'=>'', 'first'=>'- -', 'options'=>'SELECT name, id FROM rbac_objects', //'combo', 'ids'=>'', 'first'=>'- -', 'options'=>array('1'=>'one', '5'=>'five')
'class'=>'inp', 'info'=>'', 'maxlength'=>10, 'alt'=>'Object' ));
$_frm->SetInputRules('objects_id', array('table'=>'rbac_domains_has_objects', 'int', 'maxlength'=>10));

// BUTTON ---------
$_frm->AssignInput('<!BUTTON!>', 'add', array('type'=>'submit', 'value'=>'Add!'));


//-------------------
// SET TEMPLATE
$_frm->SetTemplate(
'
<table border="0" width="450" align="center" class="tbl_frm">
	<tr >
		<td colspan="2" id="hdr">
			[#]<!TITLE!>[#]<!SUBTITLE!>[#]<!NOTICE!>[#]
		</td>
	</tr>
	<tr >
		<td width="30%" class="lbl">
			[#]<DOMAINS_ID_LBL>[#]
		</td>
		<td>
			[#]<DOMAINS_ID>[#]
		</td>
	</tr>
	<tr >
		<td class="lbl">
			[#]<OBJECTS_ID_LBL>[#]
		</td>
		<td>
			[#]<OBJECTS_ID>[#]
		</td>
	</tr>
	<tr >
		<td colspan="2" align="center" id="button">
			[#]<!BUTTON!>[#]
		</td>
	</tr>
</table>
');
?>
