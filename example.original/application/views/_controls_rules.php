<h2>Rules</h2>
<ul>
	<?php
	foreach (Rule::all(array('include' => array('privilege', 'resource', 'group'))) as $rule) {
		?>
		<li>
			<?=form_open('rules/delete')?>
				<?=form_hidden('id', $rule->id)?>
				<?=form_submit('submit', 'Delete')?>
			<?=form_close()?>
			<?=($rule->allowed ? 'Allow': 'Deny') . ' ' . $rule->group->name . ' ' . $rule->privilege->name . ' to ' . $rule->resource->name?>
		</li>
		<?php
	}
	?>
	<li>
		<?=form_open('rules/create')?>
			<?=form_dropdown('allowed', array(TRUE => 'ALLOW', FALSE => 'DENY'), TRUE)?>

			<select name="group_id">
				<optgroup label="Groups">
					<?foreach (Group::all(array('conditions' => array('singular' => FALSE))) as $group):?>
						<option value="<?=$group->id?>"><?=$group->name?></option>
					<?endforeach?>
				</optgroup>
				<optgroup label="Users">
					<?foreach (Group::all(array('conditions' => array('singular' => TRUE))) as $group):?>
						<option value="<?=$group->id?>"><?=$group->name?></option>
					<?endforeach?>
				</optgroup>
			</select>
			
			<select name="privilege_id">
				<optgroup label="Privileges">
					<?foreach (Privilege::all(array('conditions' => array('singular' => FALSE))) as $privilege):?>
						<option value="<?=$privilege->id?>"><?=$privilege->name?></option>
					<?endforeach?>
				</optgroup>
				<optgroup label="Individual actions">
					<?foreach (Privilege::all(array('conditions' => array('singular' => TRUE))) as $privilege):?>
						<option value="<?=$privilege->id?>"><?=$privilege->name?></option>
					<?endforeach?>
				</optgroup>
			</select>

			<select name="resource_id">
				<optgroup label="Resources">
					<?foreach (Resource::all(array('conditions' => array('singular' => FALSE))) as $resource):?>
						<option value="<?=$resource->id?>"><?=$resource->name?></option>
					<?endforeach?>
				</optgroup>
				<optgroup label="Individual entities">
					<?foreach (Resource::all(array('conditions' => array('singular' => TRUE))) as $resource):?>
						<option value="<?=$resource->id?>"><?=$resource->name?></option>
					<?endforeach?>
				</optgroup>
			</select>

			<?=form_submit('submit', 'Save rule')?>
		<?=form_close()?>
	</li>
</ul>