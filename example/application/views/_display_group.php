<?

$group = Group::find($search_id, array('include' => array('memberships', 'rules')));

?>
<div class="grid_6">
	<h2>Group (<?=$group->name?>)</h2>

	<ul>
		<li>Importance <?=number_format($group->importance)?></li>
	</ul>

	<strong>Members</strong>
	<ul>
		<?php
		if ( ! $group->memberships ) {
			echo '<li>This group has no members.</li>';
		} else {
		
			foreach ($group->memberships as $membership) {
				$user = User::find($membership->user_id);
				?>
				<li>
					<?=form_open('groups/eject')?>
						<?=form_hidden('group_id', $group->id)?>
						<?=form_hidden('user_id', $user->id)?>
						<?=form_submit('submit', 'Eject')?>
						<?=$user->email?>
					<?=form_close()?>
				</li>
				<?php
			}
		}
		?>
		<li>
			<?=form_open('groups/enroll')?>
				<?=form_hidden('group_id', $group->id)?>
				<?=form_submit('submit', 'Add')?>
				<select name="user_id">
					<?foreach(User::all() as $user):?>
						<option value="<?=$user->id?>"><?=$user->email?></option>
					<?endforeach;?>
				</select>
			<?=form_close()?>
		</li>
	</ul>

	<strong>Rules</strong>
	<ul>
		<?php
		if ( ! $group->rules ) {
			echo '<li>This group has no rules.</li>';
		} else {
			foreach ($group->rules as $rule) {
				?>
				<li>
					<?=form_open('rules/delete')?>
						<?=form_hidden('id', $rule->id)?>
						<?=form_submit('submit', 'Delete')?>
						(<?=$rule->id?>) <?=($rule->allowed) ? 'Allow' : 'Deny'?> <?=Privilege::find($rule->privilege_id)->name?> on <?=Resource::find($rule->resource_id)->name?>
					<?=form_close()?>
				</li>
				<?php
			}
		}
		?>
		<li>
			<?=form_open('rules/create')?>
				<?=form_dropdown('permit', array(TRUE => 'ALLOW', FALSE => 'DENY'))?>
				<select name="privilege_id">
					<?php
					foreach(Privilege::all() as $privilege) {
						echo "<option value=\"{$privilege->id}\">{$privilege->name}</option>";
					}
					?>
				</select>
				<select name="resources">
					<?php
					foreach(Resource::all() as $resource) {
						echo "<option value=\"{$resource->id}\">{$resource->name}</option>";
					}
					?>
				</select>
				<?=form_submit('submit', 'Save rule')?>
			<?=form_close()?>
		</li>
	</ul>
</div>
<div class="clear"></div>
<hr>