<?php
$entity = $EM->find('\models\RBAC\Entity', $search_id);
?>
<div class="grid_6">
	<h2>Entity (<?=$entity->getName()?>) <?=$entity->getDescription() ? '<small>' . $entity->getDescription() . '</small>' : NULL?></h2>

	<strong>Component of</strong>
	<ul>
		<?foreach($entity->getResources() as $resource):?>
			<?if( ! $resource->isGranular() ):?>
				<li>
					<?=form_open('resources/excludes')?>
						<?=form_hidden('resource_id', $resource->getId())?>
						<?=form_hidden('entity_id', $entity->getId())?>
						<?=form_submit('submit', 'Exclude')?>
						from <?=$resource->getName()?>
					<?=form_close()?>
				</li>
			<?endif;?>
		<?endforeach;?>
		<li>
			<?=form_open('resources/includes')?>
				<?=form_hidden('entity_id', $entity->getId())?>
				<?=form_submit('submit', 'Add')?> to 
				<select name="resource_id">
					<option value="">--- resource ---</option>
					<?foreach($EM->createQuery('SELECT r FROM models\RBAC\Resource r WHERE r.granular = FALSE AND r.root = 1')->getResult() as $resource):?>
						<?if( ! ($resource->hasEntity($entity)) ):?>
							<option value="<?=$resource->getId()?>"><?=$resource->getName()?></option>
						<?endif?>
					<?endforeach?>
				</select>
			<?=form_close()?>
		</li>
	</ul>
</div>
<div class="clear"></div>
<hr>