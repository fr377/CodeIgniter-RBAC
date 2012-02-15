<?php
$_frm = & new Form_bv(array('method'=>'POST', 'action'=>'', 'name'=>'frm_rbac_objects'));


$_frm->Assign('<!TITLE!>', '<div class="hdr">objects Form:</div>');

// INPUT ---------
$_frm->AssignInput('<NAME>', 'name', array('type'=>'text',  'size'=>'30', 'class'=>'inp', 'info'=>'', 'maxlength'=>50 ));
$_frm->SetInputRules('name', array('table'=>'rbac_objects', 'alphanum', 'required', 'unique', 'maxlength'=>50));
// INPUT ---------
$_frm->AssignInput('<DESCRIPTION>', 'description', array('type'=>'textarea', 'cols'=>'40', 'rows'=>'10','class'=>'inp', 'info'=>'' ));
$_frm->SetInputRules('description', array('table'=>'rbac_objects'));

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
			[#]<NAME_LBL>[#]
		</td>
		<td>
			[#]<NAME>[#]
		</td>
	</tr>
	<tr >
		<td class="lbl">
			[#]<DESCRIPTION_LBL>[#]
		</td>
		<td>
			[#]<DESCRIPTION>[#]
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
