<h2>Rules</h2>

<?php
$rules = $EM->createQuery('SELECT r FROM models\RBAC\Rule r')->getResult()
?>

<ul>
	<?foreach($rules as $rule):?>
		<li>
			<?=form_open('rules/delete')?>
				<?=form_hidden('rule_id', $rule->getId())?>
				<?=form_submit('submit', 'Delete')?>
			<?=form_close()?>
			<?=($rule->isAllowed() ? 'Allow ': 'Deny ') . strtolower($rule->getGroup()->getName() . ' ' . $rule->getPrivilege()->getName() . ' to ' . $rule->getResource()->getName())?>
		</li>	
	<?endforeach?>
	<li>
		<?=form_open('rules/create')?>
			<?=form_dropdown('allowed', array(TRUE => 'ALLOW', FALSE => 'DENY'), TRUE)?>

			<select name="group_id">
				<optgroup label="Groups">
					<?foreach ($EM->createQuery('SELECT g FROM models\RBAC\Group g WHERE g.granular = 0 AND g.root = 1')->getResult() as $group):?>
						<option value="<?=$group->getId()?>"><?=$group->getName()?></option>
					<?endforeach?>
				</optgroup>
				<optgroup label="Users">
					<?foreach ($EM->createQuery('SELECT g FROM models\RBAC\Group g WHERE g.granular = 1 AND g.root = 2')->getResult() as $group):?>
						<option value="<?=$group->getId()?>"><?=$group->getName()?></option>
					<?endforeach?>
				</optgroup>
			</select>

			<select name="privilege_id">
				<optgroup label="Privileges">
					<?foreach ($EM->createQuery('SELECT p FROM models\RBAC\Privilege p WHERE p.granular = 0 AND p.root = 1')->getResult() as $privilege):?>
						<option value="<?=$privilege->getId()?>"><?=$privilege->getName()?></option>
					<?endforeach?>
				</optgroup>
				<optgroup label="Individual actions">
					<?foreach ($EM->createQuery('SELECT p FROM models\RBAC\Privilege p WHERE p.granular = 1 AND p.root = 2')->getResult() as $privilege):?>
						<option value="<?=$privilege->getId()?>"><?=$privilege->getName()?></option>
					<?endforeach?>
				</optgroup>
			</select>

			<select name="resource_id">
				<optgroup label="Resources">
					<?foreach ($EM->createQuery('SELECT r FROM models\RBAC\Resource r WHERE r.granular = 0 AND r.root = 1')->getResult() as $resource):?>
						<option value="<?=$resource->getId()?>"><?=$resource->getName()?></option>
					<?endforeach?>
				</optgroup>
				<optgroup label="Individual entities">
					<?foreach ($EM->createQuery('SELECT r FROM models\RBAC\Resource r WHERE r.granular = 1 AND r.root = 2')->getResult() as $resource):?>
						<option value="<?=$resource->getId()?>"><?=$resource->getName()?></option>
					<?endforeach?>
				</optgroup>
			</select>

			<?=form_submit('submit', 'Save rule')?>
		<?=form_close()?>
	</li>
</ul>