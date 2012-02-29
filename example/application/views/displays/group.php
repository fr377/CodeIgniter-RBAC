<?php
$group = $EM->find('\models\RBAC\Group', $search_id);
?>
<div class="grid_6">
	<h2>Group (<?=$group->getName()?>)</h2>

	<strong>Members</strong>
	<ul>
		<?if( ! $group->hasMembers() ):?>
			<li>This group has no members.</li>
		<?else:?>
			<?foreach($group->getUsers() as $user):?>
				<li>
					<?=form_open('groups/discharge')?>
						<?=form_hidden('group_id', $group->getId())?>
						<?=form_hidden('user_id', $user->getId())?>
						<?=form_submit('submit', 'Eject')?>
						<?=$user->getEmail()?>
					<?=form_close()?>
				</li>
			<?endforeach?>
		<?endif?>
		<li>
			<?=form_open('groups/enlist')?>
				<?=form_hidden('group_id', $group->getId())?>
				<?=form_submit('submit', 'Add')?>
				<select name="user_id">
					<option value="">--- user ---</option>
					<?foreach($EM->createQuery('SELECT u FROM models\RBAC\User u')->getResult() as $user):?>
						<?if( ! $user->inGroup($group) ):?>
							<option value="<?=$user->getId()?>"><?=$user->getEmail()?></option>
						<?endif?>
					<?endforeach;?>
				</select>
			<?=form_close()?>
		</li>
	</ul>

	<strong>Rules</strong>
	<ul>
		<?if( ! $group->hasRules() ):?>
			<li>No rules pertain to this group.</li>
		<?else:?>
			<?foreach($group->getRules() as $rule):?>
				<li>
					<?=form_open('rules/delete')?>
						<?=form_hidden('id', $rule->getId())?>
						<?=form_submit('submit', 'Delete')?>

					<?=form_close()?>
				</li>
			<?endforeach?>
		<?endif?>
	</ul>
</div>
<div class="clear"></div>
<hr>