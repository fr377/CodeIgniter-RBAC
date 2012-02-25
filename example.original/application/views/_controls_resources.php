<h2>Resources</h2>
<ul>
	<?php
	if ($resources = Resource::all(array('conditions' => 'singular is FALSE'))) {
		foreach ($resources as $resource) {
			?>
			<li>
				<?=form_open('resources/delete')?>
					<?=form_hidden('id', $resource->id)?>
					<?=form_submit('submit', 'Delete')?>
				<?=form_close()?>
				<?=anchor('resource/' . $resource->id, $resource->name)?> (<?=count($resource->components)?>)
			</li>
			<?php
		}
	} else {
		echo "<li>No resources</li>";
	}
	?>
	<li>
		<?=form_open('resources/create')?>
			<?=form_input('name')?>
			<?=form_submit('submit', 'Save resource')?>
		<?=form_close()?>
	</li>
</ul>