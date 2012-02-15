<?php $inquiry = $display; ?>
<div class="grid_6">
	<h2>Access request resolution</h2>
<pre>
<?=print_r($inquiry)?>
</pre>

	<ul>
		<li><?=$display->first_name?> (<?=$display->id?>)</li>
		<li><?=$display->email?></li>
	</ul>

	<?=form_open('groups/enroll_bulk')?>
		<?=form_hidden('user_id', $display->id)?>
		<ul>
			<li>Memberships<ul>
				<?php
				foreach (Group::all() as $group) {
					?>
					<li><label><input type="checkbox" name="<?=md5($group->name)?>" value="<?=$group->id?>" <?=set_checkbox(md5($group->name), $group->id, $display->in_group($group))?>> <?=$group->name?></label></li>
					<?php
				}
				?>
			</ul></li>
			<li><?=form_submit('submit', 'Update')?></li>
		</ul>
	<?=form_close()?>
</div>
<div class="clear"></div>
<hr>