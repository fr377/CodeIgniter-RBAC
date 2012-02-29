<h2>Users</h2>
<ul>
	<?foreach($EM->createQuery('SELECT u FROM models\RBAC\User u')->getResult() as $index => $user):?>
		<li>
			<?=form_open('users/delete')?>
				<?=form_hidden('user_id', $user->getId())?>
				<?=form_submit('submit', 'Delete')?>
			<?=form_close()?>
<!-- 			<?=form_button('edit', 'Edit', 'disabled')?> -->
			<?=anchor('user/' . $user->getId(), $user->getEmail())?>
		</li>
	<?endforeach?>
	<li>
		<?=form_open('users/create')?>
			<?=form_input('email')?>
			<?=form_submit('submit', 'Save user')?>
		<?=form_close()?>
	</li>
</ul>