<h2>Entities</h2>
<ul>
	<?foreach($EM->createQuery('SELECT e FROM models\RBAC\Entity e')->getResult() as $index => $entity):?>
		<li>
			<?=form_open('entities/delete')?>
				<?=form_hidden('entity_id', $entity->getId())?>
				<?=form_submit('submit', 'Delete')?>
			<?=form_close()?>
			<?=anchor('entity/' . $entity->getId(), $entity->getName())?>
		</li>
	<?endforeach?>
	<li>
		<?=form_open('entities/create')?>
			<?=form_input('name')?>
			<?=form_submit('submit', 'Save entity')?>
		<?=form_close()?>
	</li>
</ul>