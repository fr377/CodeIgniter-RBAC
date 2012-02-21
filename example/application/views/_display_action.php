<?php
$action = Action::find(
	$search_id,
	array(
		'include' => array(
			'liberties' => array(
				'privilege'
			)
		)
	)
);
?>
<div class="grid_6">
	<h2>Action (<?=$action->name?>)</h2>

<!--
<pre>
<?=print_r($action)?>
</pre>
-->
	<strong>Participates in privileges</strong>
	<ul>
		<?foreach($action->liberties as $liberty):?>
			<?if( ! $liberty->privilege->singular ):?>
				<li>
					<?=form_open('actions/revoke')?>
						<?=form_hidden('privilege_id', $liberty->privilege->id)?>
						<?=form_hidden('action_id', $action->id)?>
						<?=form_submit('submit', 'Exclude')?>
						from <?=$liberty->privilege->name?>
					<?=form_close()?>
				</li>
			<?endif;?>
		<?endforeach;?>
		<li>
			<?=form_open('actions/grant')?>
				<?=form_hidden('action_id', $action->id)?>
				<?=form_submit('submit', 'Grant')?> to 
				<select name="privilege_id">
					<option value="">--- privilege ---</option>
					<?foreach(Privilege::all(array('conditions' => 'singular is FALSE')) as $privilege):?>
						<?if( ! ($privilege->allows($action)) ):?>
							<option value="<?=$privilege->id?>"><?=$privilege->name?></option>
						<?endif?>
					<?endforeach?>
				</select>
			<?=form_close()?>
		</li>
	</ul>
</div>
<div class="clear"></div>
<hr>