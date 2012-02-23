<h2>Actions</h2>
<ul>
	<?if($actions = Action::all()):?>
		<?foreach($actions as $action):?>
			<li>
				<?=form_open('actions/delete')?>
					<?=form_hidden('id', $action->id)?>
					<?=form_submit('submit', 'Delete')?>
				<?=form_close()?>
				<?=anchor('action/' . $action->id, $action->name)?>
			</li>
		<?endforeach?>
	<?endif?>
	<li>
		<?=form_open('actions/create')?>
			<?=form_input('name')?>
			<?=form_submit('submit', 'Save action')?>
		<?=form_close()?>
	</li>
</ul>
