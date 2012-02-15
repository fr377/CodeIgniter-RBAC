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
			<?=anchor('group/' . $group->id, $group->name)?> (<?=count($group->memberships)?> mem., if. <?=number_format($group->importance)?>)
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