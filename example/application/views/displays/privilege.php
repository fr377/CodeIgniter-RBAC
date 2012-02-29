<?php
$privilege = $EM->find('\models\RBAC\Privilege', $search_id);
?>
<div class="grid_6">
	<h2>Privilege (<?=$privilege->getName()?>)</h2>
	
	<strong>Grants actions</strong>
	<ul>
		<?if( ! $privilege->hasActions() ):?>
			<li>This privilege grants liberties to no actions.</li>
		<?else:?>
			<?foreach($privilege->getActions() as $action):?>
				<li>
					<?=form_open('privileges/revoke')?>
						<?=form_hidden('privilege_id', $privilege->getId())?>
						<?=form_hidden('action_id', $action->getId())?>
						<?=form_submit('submit', 'Revoke')?>
						<?=$action->getName()?>
					<?=form_close()?>
				</li>
			<?endforeach?>
		<?endif?>
		<li>
			<?=form_open('privileges/grant')?>
				<?=form_hidden('privilege_id', $privilege->getId())?>
				<?=form_submit('submit', 'Grant')?>
				<select name="action_id">
					<option value="">--- action ---</option>
					<?foreach($EM->createQuery('SELECT a FROM models\RBAC\Action a')->getResult() as $action):?>
						<?if( ! $privilege->hasAction($action) ):?>
							<option value="<?=$action->getId()?>"><?=$action->getName()?></option>
						<?endif?>
					<?endforeach;?>
				</select>
			<?=form_close()?>
		</li>
	</ul>

	<strong>Rules</strong>
	<ul>
		<?if( ! $privilege->hasRules() ):?>
			<li>No rules pertain to this privilege.</li>
		<?else:?>
			<?foreach($privilege->getRules() as $rule):?>
				<li>
					<?=form_open('rules/delete')?>
						<?=form_hidden('id', $rule->getId())?>
						<?=form_submit('submit', 'Delete')?>
						(<?=$rule->id?>) <?=($rule->getAllowed()) ? 'Allow' : 'Deny'?> GROUP PRIVILEGE on RESOURCE
					<?=form_close()?>
				</li>
			<?endforeach?>
		<?endif?>
	</ul>
</div>
<div class="clear"></div>
<hr>