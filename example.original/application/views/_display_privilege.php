<?php
$privilege = Privilege::find(
	$search_id,
	array(
		'include' => array(
			'liberties' => array(
				'action'	
			),
			'rules'
		)
	)
);
?>
<div class="grid_6">
	<h2>Privilege (<?=$privilege->name?>)</h2>
	
	<strong>Grants actions</strong>
	<ul>
		<?if( ! $privilege->liberties ):?>
			<li>This privilege grants liberties to no actions.</li>
		<?else:?>
			<?foreach($privilege->liberties as $liberty):?>
				<li>
					<?=form_open('privileges/revoke')?>
						<?=form_hidden('privilege_id', $privilege->id)?>
						<?=form_hidden('action_id', $liberty->action_id)?>
						<?=form_submit('submit', 'Revoke')?>
						<?=$liberty->action->name?>
					<?=form_close()?>
				</li>
			<?endforeach?>
		<?endif?>
		<li>
			<?=form_open('privileges/grant')?>
				<?=form_hidden('privilege_id', $privilege->id)?>
				<?=form_submit('submit', 'Grant')?>
				<select name="action_id">
					<option value="">--- action ---</option>
					<?foreach(Action::all() as $action):?>
						<?if( ! ($privilege->allows($action)) ):?>
							<option value="<?=$action->id?>"><?=$action->name?></option>
						<?endif?>
					<?endforeach;?>
				</select>
			<?=form_close()?>
		</li>
	</ul>

	<strong>Rules</strong>
	<ul>
		<?if( ! $privilege->rules ):?>
			<li>No rules pertain to this privilege.</li>
		<?else:?>
			<?foreach($privilege->rules as $rule):?>
				<li>
					<?=form_open('rules/delete')?>
						<?=form_hidden('id', $rule->id)?>
						<?=form_submit('submit', 'Delete')?>
						(<?=$rule->id?>) <?=($rule->allowed) ? 'Allow' : 'Deny'?> <?=Group::find($rule->group_id)->name?> <?=$privilege->name?> on <?=Resource::find($rule->resource_id)->name?>
					<?=form_close()?>
				</li>
			<?endforeach?>
		<?endif?>
	</ul>
</div>
<div class="clear"></div>
<hr>