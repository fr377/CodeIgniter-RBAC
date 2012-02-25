<h2>Groups</h2>
<ul>
	<?php
	foreach (Group::all(array('conditions' => 'singular is FALSE', 'include' => 'memberships')) as $group) {
		?>
		<li>
			<?=form_open('groups/delete')?>
				<?=form_hidden('id', $group->id)?>
				<?=form_submit('submit', 'Delete')?>
			<?=form_close()?>
			<?=form_open('groups/change_importance')?>
				<?=form_hidden('id', $group->id)?>
				<?php
				$options = array();
				for ($i = 1; $i <= 99; $i++)
					$options[$i] = $i;
				?>
				<?=form_dropdown('importance', $options, $group->importance, 'onChange="this.form.submit();"')?>
			<?=form_close()?>
			<?=anchor('group/' . $group->id, $group->name)?> (<?=count($group->memberships)?>)
		</li>
		<?php
	}
	?>
	<li>
		<?=form_open('groups/create')?>
			<?=form_input('name')?>
			<?=form_submit('submit', 'Save group')?>
		<?=form_close()?>
	</li>
</ul>