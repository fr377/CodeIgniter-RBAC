<?php
/* $entity = Entity::find($search_id, array('include' => array('components', 'resources'))); */
/*
$entity = Entity::find(
	$search_id,
	array('include' =>
		array(
			'components',
			'resources'
		),
	)
);
*/

try {
	$entity = Entity::find(
		$search_id,
		array(
			'include' => array(
				'components' => array(
					'resource'
				),
/* 				'resources' */
			),
		)
	);



/*
$posts = Post::find(
	'first',
	array(
		'include' => array(
			'category',
			'comments' => array(
				'author'
			)
		)
	)
);
*/


?>
<div class="grid_6">
	<h2>Entity (<?=$entity->name?>)</h2>
	<?=$entity->description ? '<p>' . $entity->description . '</p>' : NULL?>
<pre>
<?=print_r($entity)?>
</pre>
	
	<strong>Resources</strong>
	<ul>
		<?if($entity->resources):?>
			<?foreach($entity->resources as $resource):?>
			<li>
				<?=form_open('resources/exclude')?>
					<?=form_hidden('resource_id', $resource->id)?>
					<?=form_hidden('entity_id', $entity->id)?>
					<?=form_submit('submit', 'Exclude')?>
					from <?=$resource->name?>
				<?=form_close()?>
			</li>
			<?endforeach;?>
		<?endif;?>

		<?php
/*
		if ($entity->components) {
			foreach ($entity->components as $component) {
				$resource = Resource::find($component->resource_id);
				?>
				<li>
					<?=form_open('resources/exclude')?>
						<?=form_hidden('resource_id', $resource->id)?>
						<?=form_hidden('entity_id', $entity->id)?>
						<?=form_submit('submit', 'Exclude')?>
						from <?=$resource->name?>
					<?=form_close()?>
				</li>
				<?php
			}
		} else {
			echo "<li>This entity does not form part of any component.</li>";
		}
*/
		?>
		<li>
			<?=form_open('resources/subsume')?>
				<?=form_hidden('entity_id', $entity->id)?>
				<?=form_submit('submit', 'Add')?> to 
				<select name="resource_id">
					<?php
					foreach(Resource::all(array('conditions' => 'singular is FALSE')) as $resource) {
						if ( ! $resource->includes($entity) )
							echo "<option value=\"{$resource->id}\">{$resource->name}</option>";
					}
					?>
				</select>
				
			<?=form_close()?>
		</li>
	</ul>
</div>
<div class="clear"></div>
<hr>

<?php
} catch (Exception $e) {
	echo $e->getMessage();
/* 	print_r($e); */
}

?>