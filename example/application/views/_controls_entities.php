<h2>Entities</h2>
<ul>
	<?php
	foreach (Entity::all() as $entity) {
		?>
		<li>
			<?=form_open('entities/delete')?>
				<?=form_hidden('id', $entity->id)?>
				<?=form_submit('submit', 'Delete')?>
			<?=form_close()?>
			<?=anchor('entity/' . $entity->id, $entity->name)?>
		</li>
		<?php
	}
	?>
	<li>
		<?=form_open('entities/create')?>
			<?=form_input('name')?>
			<?=form_submit('submit', 'Save entity')?>
		<?=form_close()?>
	</li>
</ul>