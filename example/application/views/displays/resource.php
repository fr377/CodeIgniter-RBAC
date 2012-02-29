<?php
$resource = $EM->find('\models\RBAC\Resource', $search_id);
?>
<div class="grid_6">
	<h2>Resource (<?=$resource->getName()?>)</h2>
	<?=$resource->getDescription() ? '<p>' . $resource->getDescription() . '</p>' : NULL?>

	<strong>Subsumes entities</strong>
	<ul>
		<?if( ! $resource->hasEntities() ):?>
			<li>This resource is empty.</li>
		<?else:?>
			<?foreach($resource->getEntities() as $entity):?>
				<li>
					<?=form_open('resources/excludes')?>
						<?=form_hidden('resource_id', $resource->getId())?>
						<?=form_hidden('entity_id', $entity->getId())?>
						<?=form_submit('submit', 'Exclude')?>
						<?=$entity->getName()?>
					<?=form_close()?>
				</li>
			<?endforeach?>
		<?endif?>
		<li>
			<?=form_open('resources/includes')?>
				<?=form_hidden('resource_id', $resource->getId())?>
				<?=form_submit('submit', 'Subsume')?>
				<select name="entity_id">
					<option value="">--- entity ---</option>
					<?foreach($EM->createQuery('SELECT e FROM models\RBAC\Entity e')->getResult() as $entity):?>
						<?if( ! ($resource->hasEntity($entity)) ):?>
							<option value="<?=$entity->getId()?>"><?=$entity->getName()?></option>
						<?endif?>
					<?endforeach;?>
				</select>
			<?=form_close()?>
		</li>
	</ul>

	<strong>Rules</strong>
	<ul>
		<?if( ! $resource->hasRules() ):?>
			<li>No rules pertain to this group.</li>
		<?else:?>
			<?foreach($resource->getRules() as $rule):?>
				<li>
					<?=form_open('rules/delete')?>
						<?=form_hidden('id', $rule->getId())?>
						<?=form_submit('submit', 'Delete')?>
						(<?=$rule->id?>) <?=($rule->getAllowed()) ? 'Allow' : 'Deny'?> PRIVILEGE on RESOURCE
					<?=form_close()?>
				</li>
			<?endforeach?>
		<?endif?>
	</ul>
</div>
<div class="clear"></div>
<hr>