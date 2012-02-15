<?php
$_frm = & new Form_bv(array('method'=>'POST', 'action'=>'', 'name'=>'frm_rbac_roles_has_domain_privileges'));


$_frm->Assign('<!TITLE!>', '<div class="hdr">Roles permission:</div>');

// INPUT ---------
$_frm->AssignInput('<ROLES_ID>', 'roles_id', array('type'=>'combo', 'ids'=>'', 'first'=>'- -', 
	'options'=>'SELECT  name, id FROM rbac_roles',
'class'=>'inp', 'info'=>'', 'maxlength'=>11, 'alt'=>'Role' ));
$_frm->SetInputRules('roles_id', array('table'=>'rbac_roles_has_domain_privileges', 'int', 'maxlength'=>11));
// INPUT ---------
$_frm->AssignInput('<PRIVILEGES_ID>', 'privileges_id', array('type'=>'combo', 'ids'=>'', 'first'=>'- -', 
	'options'=>'SELECT CONCAT( name, IF(is_singular = 1, \' (SINGULAR)\', \' (NOT SINGULAR)\')) as name, id FROM rbac_privileges  ORDER BY is_singular ASC', 
'class'=>'inp', 'info'=>'', 'maxlength'=>10, 'alt'=>'Privilege' ));
$_frm->SetInputRules('privileges_id', array('table'=>'rbac_roles_has_domain_privileges', 'int', 'maxlength'=>10));
// INPUT ---------
$_frm->AssignInput('<DOMAINS_ID>', 'domains_id', array('type'=>'combo', 'ids'=>'', 'first'=>'- -', 
	'options'=>'SELECT CONCAT( name, IF(is_singular = 1, \' (SINGULAR)\', \' (NOT SINGULAR)\')) as name, id FROM rbac_domains  ORDER BY is_singular ASC',
'class'=>'inp', 'info'=>'', 'maxlength'=>10, 'alt'=>'Domain' ));
$_frm->SetInputRules('domains_id', array('table'=>'rbac_roles_has_domain_privileges', 'int', 'maxlength'=>10));
// INPUT ---------
$_frm->AssignInput('<IS_ALLOWED>', 'is_allowed', array('type'=>'checkbox', 'checked'=>'', 'class'=>'inp', 'info'=>'', 'maxlength'=>3 ));
$_frm->SetInputRules('is_allowed', array('table'=>'rbac_roles_has_domain_privileges', 'int', 'maxlength'=>3));

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
			[#]<ROLES_ID_LBL>[#]
		</td>
		<td>
			[#]<ROLES_ID>[#]
		</td>
	</tr>
	<tr >
		<td class="lbl">
			[#]<PRIVILEGES_ID_LBL>[#]
		</td>
		<td>
			[#]<PRIVILEGES_ID>[#]
		</td>
	</tr>
	<tr >
		<td class="lbl">
			[#]<DOMAINS_ID_LBL>[#]
		</td>
		<td>
			[#]<DOMAINS_ID>[#]
		</td>
	</tr>
	<tr >
		<td class="lbl">
			[#]<IS_ALLOWED_LBL>[#]
		</td>
		<td>
			[#]<IS_ALLOWED>[#]
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
