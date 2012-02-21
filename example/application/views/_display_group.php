<?php
$group = Group::find(
	$search_id,
	array(
		'include' => array(
			'memberships' => array(
				'user'
			),
			'rules'
		)
	)
);
?>
<div class="grid_6">
	<h2>Group (<?=$group->name?>)</h2>

	<strong>Members</strong>
	<ul>
		<?if( ! $group->memberships ):?>
			<li>This group has no members.</li>
		<?else:?>
			<?foreach($group->memberships as $membership):?>
				<li>
					<?=form_open('groups/eject')?>
						<?=form_hidden('group_id', $group->id)?>
						<?=form_hidden('user_id', $membership->user_id)?>
						<?=form_submit('submit', 'Eject')?>
						<?=$membership->user->email?>
					<?=form_close()?>
				</li>
			<?endforeach?>
		<?endif?>
		<li>
			<?=form_open('groups/enroll')?>
				<?=form_hidden('group_id', $group->id)?>
				<?=form_submit('submit', 'Add')?>
				<select name="user_id">
					<option value="">--- user ---</option>
					<?foreach(User::all() as $user):?>
						<?if( ! ($user->in_group($group)) ):?>
							<option value="<?=$user->id?>"><?=$user->email?></option>
						<?endif?>
					<?endforeach;?>
				</select>
			<?=form_close()?>
		</li>
	</ul>

	<strong>Rules</strong>
	<ul>
		<?if( ! $group->rules ):?>
			<li>No rules pertain to this group.</li>
		<?else:?>
			<?foreach($group->rules as $rule):?>
				<li>
					<?=form_open('rules/delete')?>
						<?=form_hidden('id', $rule->id)?>
						<?=form_submit('submit', 'Delete')?>
						(<?=$rule->id?>) <?=($rule->allowed) ? 'Allow' : 'Deny'?> <?=Privilege::find($rule->privilege_id)->name?> on <?=Resource::find($rule->resource_id)->name?>
					<?=form_close()?>
				</li>
			<?endforeach?>
		<?endif?>
	</ul>
</div>
<div class="clear"></div>
<hr>