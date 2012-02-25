<?php
$entity = Entity::find(
	$search_id,
	array(
		'include' => array(
			'components' => array(
				'resource'
			),
		),
	)
);
?>
<div class="grid_6">
	<h2>Entity (<?=$entity->name?>) <?=$entity->description ? '<small>' . $entity->description . '</small>' : NULL?></h2>

	<strong>Component of</strong>
	<ul>
		<?foreach($entity->components as $component):?>
			<?if( ! $component->resource->singular ):?>
				<li>
					<?=form_open('resources/exclude')?>
						<?=form_hidden('resource_id', $component->resource->id)?>
						<?=form_hidden('entity_id', $entity->id)?>
						<?=form_submit('submit', 'Exclude')?>
						from <?=$component->resource->name?>
					<?=form_close()?>
				</li>
			<?endif;?>
		<?endforeach;?>
		<li>
			<?=form_open('resources/subsume')?>
				<?=form_hidden('entity_id', $entity->id)?>
				<?=form_submit('submit', 'Add')?> to 
				<select name="resource_id">
					<option value="">--- resource ---</option>
					<?foreach(Resource::all(array('conditions' => 'singular is FALSE')) as $resource):?>
						<?if( ! ($resource->includes($entity)) ):?>
							<option value="<?=$resource->id?>"><?=$resource->name?></option>
						<?endif?>
					<?endforeach?>
				</select>
			<?=form_close()?>
		</li>
	</ul>
</div>
<div class="clear"></div>
<hr>