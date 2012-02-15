<h2>Privileges</h2>
<ul>
	<?php
	if ($privileges = Privilege::all(array('conditions' => 'singular is FALSE'))) {
		foreach ($privileges as $privilege) {
		?>
			<li>
				<?=form_open('privileges/delete')?>
					<?=form_hidden('id', $privilege->id)?>
					<?=form_submit('submit', 'Delete')?>
				<?=form_close()?>
				<?=anchor('privilege/' . $privilege->id, $privilege->name)?> (<?=count($privilege->liberties)?>)
			</li>
			<?php
		}
	} else {
		echo "<li>No privileges</li>";
	}
	?>
	<li>
		<?=form_open('privileges/create')?>
			<?=form_input('name')?>
			<?=form_submit('submit', 'Save privilege')?>
		<?=form_close()?>
	</li>
</ul>