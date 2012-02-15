<?php
$_frm = & new Form_bv(array('method'=>'POST', 'action'=>'', 'name'=>'frm_users'));


$_frm->Assign('<!TITLE!>', '<div class="hdr">Users Form:</div>');

// INPUT ---------
$_frm->AssignInput('<USERNAME>', 'username', array('type'=>'text',  'size'=>'30', 'class'=>'inp', 'info'=>'', 'maxlength'=>50 ));
$_frm->SetInputRules('username', array('table'=>'users', 'unique', 'alphanum', 'required', 'unique', 'maxlength'=>50));
// INPUT ---------
$_frm->AssignInput('<PSWD>', 'pswd', array('type'=>'password', 'class'=>'inp', 'info'=>'', 'maxlength'=>70 ));
$_frm->SetInputRules('pswd', array('table'=>'users', 'password', 'required', 'maxlength'=>70));

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
			[#]<USERNAME_LBL>[#]
		</td>
		<td>
			[#]<USERNAME>[#]
		</td>
	</tr>
	<tr >
		<td class="lbl">
			[#]<PSWD_LBL>[#]
		</td>
		<td>
			[#]<PSWD>[#]
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
