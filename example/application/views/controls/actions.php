<h2>Actions</h2>
<ul>
	<?if ($actions = $EM->createQuery('SELECT a FROM models\RBAC\Action a')->getResult()):?>
		<?foreach($actions as $action):?>
			<li>
				<?=form_open('actions/delete')?>
					<?=form_hidden('action_id', $action->getId())?>
					<?=form_submit('submit', 'Delete')?>
				<?=form_close()?>
				<?=anchor('action/' . $action->getId(), $action->getName())?>
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
