<h2>Rule Tester</h2>
<?=form_open('inquiry')?>
	<span style="display:inline-block;width:45px;">Does</span>
	<select name="group_id">
		<option value="">--- Groups ---</option>
		<?foreach ($EM->createQuery('SELECT g FROM models\RBAC\Group g WHERE g.granular = 0 AND g.root = 1')->getResult() as $group):?>
			<option value="<?=$group->getId()?>"><?=$group->getName()?></option>
		<?endforeach?>
	</select> or <select name="user_id">
		<option value="">--- Users ---</option>
		<?foreach ($EM->createQuery('SELECT u FROM models\RBAC\User u')->getResult() as $user):?>
			<option value="<?=$user->getId()?>"><?=$user->getEmail()?></option>
		<?endforeach?>
	</select>

	<div class="clear"></div>
	<span style="display:inline-block;width:45px;">have</span>
	<select name="privilege_id">
		<optgroup label="Privileges">
		<?foreach ($EM->createQuery('SELECT p FROM models\RBAC\Privilege p WHERE p.granular = 0 AND p.root = 1')->getResult() as $privilege):?>
			<option value="<?=$privilege->getId()?>"><?=$privilege->getName()?></option>
		<?endforeach;?>
		</optgroup>
		<optgroup label="Actions">
			<?foreach ($EM->createQuery('SELECT p FROM models\RBAC\Privilege p WHERE p.granular = 1 AND p.root = 2')->getResult() as $action):?>
				<option value="<?=$action->getId()?>"><?=$action->getName()?></option>
			<?endforeach;?>
		</optgroup>
	</select> to <select name="resource_id">
		<optgroup label="Resources">
			<?foreach ($EM->createQuery('SELECT r FROM models\RBAC\Resource r WHERE r.granular = 0 AND r.root = 1')->getResult() as $resource):?>
				<option value="<?=$resource->getId()?>"><?=$resource->getName()?></option>
			<?endforeach;?>
		</optgroup>
		<optgroup label="Entities">
			<?foreach ($EM->createQuery('SELECT r FROM models\RBAC\Resource r WHERE r.granular = 1 AND r.root = 2')->getResult() as $resource):?>
				<option value="<?=$resource->getId()?>"><?=$resource->getName()?></option>
			<?endforeach;?>
		</optgroup>
	</select>

	<div class="clear"></div>
	<?=form_reset('reset', 'Reset')?>
	<?=form_submit('submit', 'Inquire')?>
<?=form_close()?>
