<?php
$_frm = & new Form_bv(array('method'=>'POST', 'action'=>'', 'name'=>'frm_comment'), TRUE);


$_frm->Assign('<!TITLE!>', '<div class="hdr">Leave a comment:</div>');

// INPUT ---------
$_frm->AssignInput('<TITLE>', 'title', array('type'=>'text', 'class'=>'inp', 'info'=>'', 'size'=>'40'));
$_frm->SetInputRules('title', array('table'=>'comments', 'required'));
// INPUT ---------
$_frm->AssignInput('<COMMENT>', 'comment', array('type'=>'textarea', 'cols'=>'40', 'rows'=>'10','class'=>'inp', 'info'=>''));
$_frm->SetInputRules('comment', array('table'=>'comments', 'required'));
$_frm->Append('<COMMENT>', '<br>HTML tags allowed.');

// BUTTON ---------
$_frm->AssignInput('<!BUTTON!>', 'add', array('type'=>'submit', 'value'=>'Submit'));


//-------------------
// SET TEMPLATE
$_frm->SetTemplate(
'
<table border="0" width="100%" align="center" class="tbl_frm" margin="0" cellpadding="0" cellspacing="5">
	<tr >
		<td colspan="2" id="hdr">
			[#]<!TITLE!>[#]<!SUBTITLE!>[#]<!NOTICE!>[#]
		</td>
	</tr>
	<tr >
		<td class="lbl">
			[#]<TITLE_LBL>[#]
		</td>
		<td>
			[#]<TITLE>[#]
		</td>
	</tr>
	<tr >
		<td class="lbl">
			[#]<COMMENT_LBL>[#]
		</td>
		<td>
			[#]<COMMENT>[#]
		</td>
	</tr>
	<tr >
		<td class="lbl">
			[#]<NAME_LBL>[#]
		</td>
		<td>
			[#]<NAME>[#]
		</td>
	</tr>
	<tr >
		<td class="lbl" valign="top">
			[#]<EMAIL_LBL>[#]
		</td>
		<td>
			[#]<EMAIL>[#]
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
