<?php
$_frm = & new Form_bv(array('method'=>'POST', 'action'=>'', 'name'=>'frm_rbac_privileges_has_actions'));


$_frm->Assign('<!TITLE!>', '<div class="hdr">privileges has actions Form:</div>');

// INPUT ---------
$_frm->AssignInput('<PRIVILEGES_ID>', 'privileges_id', array('type'=>'combo', 'ids'=>'', 'first'=>'- -', 
	'options'=>'SELECT CONCAT(name, IF(is_singular = 1, \' (SINGULAR)\', \' (NOT SINGULAR)\')) as name, id FROM rbac_privileges WHERE is_singular = 0 ORDER BY is_singular ASC',
'class'=>'inp', 'info'=>'', 'maxlength'=>10, 'alt'=>'Privilege' ));
$_frm->SetInputRules('privileges_id', array('table'=>'rbac_privileges_has_actions', 'int', 'maxlength'=>10));
// INPUT ---------
$_frm->AssignInput('<ACTIONS_ID>', 'actions_id', array('type'=>'combo', 'ids'=>'', 'first'=>'- -', 'options'=>'SELECT name, id FROM rbac_actions', //'combo', 'ids'=>'', 'first'=>'- -', 'options'=>array('1'=>'one', '5'=>'five')
'class'=>'inp', 'info'=>'', 'maxlength'=>10, 'alt'=>'Action' ));
$_frm->SetInputRules('actions_id', array('table'=>'rbac_privileges_has_actions', 'int', 'maxlength'=>10));

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
			[#]<PRIVILEGES_ID_LBL>[#]
		</td>
		<td>
			[#]<PRIVILEGES_ID>[#]
		</td>
	</tr>
	<tr >
		<td class="lbl">
			[#]<ACTIONS_ID_LBL>[#]
		</td>
		<td>
			[#]<ACTIONS_ID>[#]
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
