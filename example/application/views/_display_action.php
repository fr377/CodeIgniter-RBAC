<? $action = Action::find($search_id, array('include' => array('liberties', 'privileges'))); ?>
<div class="grid_6">
	<h2>Action (<?=$action->name?>)</h2>

<!--
<pre>
<?=print_r($action)?>
</pre>
-->

	<ul>
		<?if($action->privileges):?>
			<?foreach($action->privileges as $privilege):?>
				<li>
					<?=form_open()?>
						<?=form_submit('submit', 'Revoke')?> from '<?=$privilege->name?>'
					<?=form_close()?>
				</li>
			<?endforeach;?>
		<?endif;?>
		<li>
			<?=form_open()?>
				<?=form_submit('submit', 'Add')?> to
				<select name="privilege_id">
					<?foreach(Privilege::all(array('conditions' => 'singular is FALSE')) as $privilege):?>
						<option value="<?=$privilege->id?>"><?=$privilege->name?></option>
					<?endforeach;?>
				</select>
			<?=form_close()?>
		</li>
	</ul>
	<?php



	echo '<strong>The following privileges govern this action</strong>';
	echo '<ul>';
	foreach ($action->privileges as $privilege) {
		$rules = Rule::find_all_by_privilege_id($privilege->id, array('include' => 'resource'));

		echo "<li>{$privilege->name}";
		if ($rules) {
			echo '<ul>';
			foreach ($rules as $rule) {
				echo '<li>';
				echo ($rule->allowed) ? 'Allow' : 'Deny';
				echo " {$rule->group->name} {$rule->privilege->name} to {$rule->resource->name}</li>";
			}
			echo '</ul>';
		} else {
			echo "<ul><li>No rules govern this privilege</li></ul>";
		}
		echo "</li>";
	}
	echo '</ul>';
	?>
</div>
<div class="clear"></div>
<hr>