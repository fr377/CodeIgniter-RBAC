<?php
$_frm = & new Form_bv(array('method'=>'POST', 'action'=>'', 'name'=>'frm_rbac_users_has_roles'));


$_frm->Assign('<!TITLE!>', '<div class="hdr">users has roles Form:</div>');

// INPUT ---------
$_frm->AssignInput('<USERS_ID>', 'users_id', array('type'=>'combo', 'ids'=>'', 'first'=>'- -', 'options'=>'SELECT username, id FROM users', //'combo', 'ids'=>'', 'first'=>'- -', 'options'=>array('1'=>'one', '5'=>'five')
'class'=>'inp', 'info'=>'', 'maxlength'=>11, 'alt'=>'Username' ));
$_frm->SetInputRules('users_id', array('table'=>'rbac_users_has_roles', 'int', 'maxlength'=>11));
// INPUT ---------
$_frm->AssignInput('<ROLES_ID>', 'roles_id', array('type'=>'combo', 'ids'=>'', 'first'=>'- -', 'options'=>'SELECT name, id FROM rbac_roles', //'combo', 'ids'=>'', 'first'=>'- -', 'options'=>array('1'=>'one', '5'=>'five')
'class'=>'inp', 'info'=>'', 'maxlength'=>10, 'alt'=>'Roles'));
$_frm->SetInputRules('roles_id', array('table'=>'rbac_users_has_roles', 'int', 'maxlength'=>10));

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
			[#]<USERS_ID_LBL>[#]
		</td>
		<td>
			[#]<USERS_ID>[#]
		</td>
	</tr>
	<tr >
		<td class="lbl">
			[#]<ROLES_ID_LBL>[#]
		</td>
		<td>
			[#]<ROLES_ID>[#]
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
