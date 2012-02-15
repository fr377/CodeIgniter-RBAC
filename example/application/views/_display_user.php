<?php
$user = User::find(
	$search_id,
	array(
		'include' => array(
			'memberships' => array(
				'group'
			)
		)
	)
);
?>
<div class="grid_6">
	<h2>User (<?=$user->email?>)</h2>
	<ul>
		<li><?=$user->first_name?> (<?=$user->id?>)</li>
	</ul>

	<ul>
		<li>
			Memberships
			<ul>
				<?foreach($user->memberships as $membership):?>
					<?if( ! $membership->group->singular ):?>
						<li>
							<?=form_open('users/leave_group')?>
								<?=form_hidden('user_id', $user->id)?>
								<?=form_hidden('group_id', $membership->group->id)?>
								<?=form_submit('submit', 'Leave group')?> <?=$membership->group->name?>
							<?=form_close()?>
						</li>
					<?endif;?>
				<?endforeach;?>
				<li>
					<?=form_open('users/join_group')?>
						<?=form_hidden('user_id', $user->id)?>
						<?=form_submit('submit', 'Join group')?>
						<select name="group_id">
							<?foreach(Group::all(array('conditions' => 'singular is FALSE')) as $group):?>
								<option value="<?=$group->id?>"><?=$group->name?></option>
							<?endforeach;?>
						</select>
					<?=form_close()?>
				</li>
			</ul>
		</li>
	</ul>
</div>
<div class="clear"></div>
<hr>