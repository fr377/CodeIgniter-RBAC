<?php $privilege = $display; ?>
<div class="grid_6">
	<h2>Privilege (<?=$privilege->name?>)</h2>

	<?=form_open('privileges/bestow')?>
		<?=form_hidden('privilege_id', $privilege->id)?>
		<ul>
			<?php
			foreach(Action::all() as $action) {
				?>
				<li><label><input type="checkbox" name="<?=$action->id?>" <?=set_checkbox($action->id, $action->id, $privilege->allows($action))?>> <?=$action->name?></label></li>
				<?php
			}
			?>
			<li><?=form_submit('submit', 'Update liberties')?></li>
		</ul>
	<?=form_close()?>

	<ul>
		<li>Rules<ul>
			<?php
			foreach ($privilege->rules as $rule) {
				$permission = ($rule->allowed) ? 'Allow' : 'Deny';
				echo "<li>{$permission} {$rule->group->name} {$rule->privilege->name} to {$rule->resource->name}</li>";
			}
			?>
		</ul></li>
	</ul>
		
</div>
<div class="clear"></div>
<hr>