<h2>Users</h2>
<ul>
	<?php
	foreach (User::all() as $user) {
		?>
		<li>
			<?=form_open('users/delete')?>
				<?=form_hidden('id', $user->id)?>
				<?=form_submit('submit', 'Delete')?>
			<?=form_close()?>
<!-- 			<?=form_button('edit', 'Edit', 'disabled')?> -->
			<?=anchor('user/' . $user->id, $user->email)?>
		</li>
		<?php
	}
	?>
	<li>
		<?=form_open('users/create')?>
			<?=form_input('email')?>
			<?=form_submit('submit', 'Save user')?>
		<?=form_close()?>
	</li>
</ul>