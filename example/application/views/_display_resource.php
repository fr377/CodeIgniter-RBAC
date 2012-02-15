<?php $resource = $display; ?>
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
		<?php
		if ($resource->components) {
			foreach ($resource->components as $component) {
				$entity = Entity::find($component->entity_id);
				?>
				<li>
					<?=form_open('resources/exclude')?>
						<?=form_hidden('resource_id', $resource->id)?>
						<?=form_hidden('entity_id', $entity->id)?>
						<?=form_submit('submit', 'Exclude')?>
						<?=$entity->name?> from <?=$resource->name?>
					<?=form_close()?>
				</li>
				<?php
			}
		} else {
			echo "<li>This resource does not subsume any components.</li>";
		}
		?>
	</ul>
	
	<strong>Rules</strong>
	<ul>
		<?php
		if ($resource->rules) {
			foreach ($resource->rules as $rule) {
				echo "<li>Member of rule {$rule->id}</li>";
			}
		} else {
			echo "<li>This resource is not governed by any rules.</li>";
		}
		?>	
	</ul>
</div>
<div class="clear"></div>
<hr>