<?=form_open('inquiry')?>
	Does
	(<select name="group_id">
		<option value="" selected>--- Group ---</option>
		<?foreach (Group::all(array('conditions' => array('singular' => FALSE))) as $group):?>
			<option value="<?=$group->id?>"><?=$group->name?></option>
		<?endforeach?>
	</select>
	or
	<select name="user_id">
		<option value="" selected>--- User ---</option>
		<?foreach(User::all() as $user):?>
			<option value="<?=$user->id?>"><?=$user->email?></option>
		<?endforeach?>
	</select>)

	<div class="clear"></div>
	have
	(<select name="privilege_id">
		<option value="" selected>--- Privilege ---</option>
		<?foreach(Privilege::all(array('conditions' => array('singular' => FALSE))) as $privilege):?>
			<option value="<?=$privilege->id?>"><?=$privilege->name?></option>
		<?endforeach;?>
	</select>
	or
	<select name="action_id">
		<option value="" selected>--- Action ---</option>
		<?foreach(Action::all() as $action):?>
			<option value="<?=$action->id?>"><?=$action->name?></option>
		<?endforeach;?>
	</select>)
	
	
	<div class="clear"></div>
	to
	(<select name="resource_id">
		<option value="" selected>--- Resource ---</option>
		<?foreach(Resource::all(array('conditions' => array('singular' => FALSE))) as $resource):?>
			<option value="<?=$resource->id?>"><?=$resource->name?></option>
		<?endforeach;?>
	</select>
	or
	<select name="entity_id">
		<option value="" selected>--- Entity ---</option>
		<?foreach(Entity::all() as $entity):?>
			<option value="<?=$entity->id?>"><?=$entity->name?></option>
		<?endforeach;?>
	</select>)

	<div class="clear"></div>
	<?=form_submit('submit', 'Inquire')?>
<?=form_close()?>
