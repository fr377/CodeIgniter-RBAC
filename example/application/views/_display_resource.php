<?php
$resource = Resource::find(
	$search_id,
	array(
		'include' => array(
			'components' => array(
				'entity'
			),
			'rules'
		)
	)
);
?>
<div class="grid_6">
	<h2>Resource (<?=$resource->name?>)</h2>
	<?=$resource->description ? '<p>' . $resource->description . '</p>' : NULL?>
<!--
<pre>
<?=print_r($resource)?>
</pre>
-->

	<strong>Subsumes entities</strong>
	<ul>
		<?if( ! $resource->components ):?>
			<li>This resource is empty.</li>
		<?else:?>
			<?foreach($resource->components as $component):?>
				<li>
					<?=form_open('resources/exclude')?>
						<?=form_hidden('resource_id', $resource->id)?>
						<?=form_hidden('entity_id', $component->entity_id)?>
						<?=form_submit('submit', 'Exclude')?>
						<?=$component->entity->name?>
					<?=form_close()?>
				</li>
			<?endforeach?>
		<?endif?>
		<li>
			<?=form_open('resources/subsume')?>
				<?=form_hidden('resource_id', $resource->id)?>
				<?=form_submit('submit', 'Subsume')?>
				<select name="entity_id">
					<option value="">--- entity ---</option>
					<?foreach(Entity::all() as $entity):?>
						<?if( ! ($resource->includes($entity)) ):?>
							<option value="<?=$entity->id?>"><?=$entity->name?></option>
						<?endif?>
					<?endforeach;?>
				</select>
			<?=form_close()?>
		</li>
	</ul>

	<strong>Rules</strong>
	<ul>
		<?if( ! $resource->rules ):?>
			<li>No rules pertain to this group.</li>
		<?else:?>
			<?foreach($resource->rules as $rule):?>
				<li>
					<?=form_open('rules/delete')?>
						<?=form_hidden('id', $rule->id)?>
						<?=form_submit('submit', 'Delete')?>
						(<?=$rule->id?>) <?=($rule->allowed) ? 'Allow' : 'Deny'?> <?=Privilege::find($rule->privilege_id)->name?> on <?=Resource::find($rule->resource_id)->name?>
					<?=form_close()?>
				</li>
			<?endforeach?>
		<?endif?>
	</ul>
</div>
<div class="clear"></div>
<hr>