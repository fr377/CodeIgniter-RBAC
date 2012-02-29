<?php
$user = $EM->find('\models\RBAC\User', $search_id);
?>
<div class="grid_6">
	<h2>User (<?=$user->getEmail()?>) <small><?=$user->getFirstName()?> (<?=$user->getId()?>)</small></h2>

	<strong>Memberships</strong>
	<ul>
		<?foreach($user->getGroups() as $group):?>
			<?if( ! $group->isGranular() ):?>
				<li>
					<?=form_open('groups/discharge')?>
						<?=form_hidden('user_id', $user->getId())?>
						<?=form_hidden('group_id', $group->getId())?>
						<?=form_submit('submit', 'Leave group')?> <?=$group->getName()?>
					<?=form_close()?>
				</li>
			<?endif;?>
		<?endforeach;?>
		<li>
			<?=form_open('groups/enlist')?>
				<?=form_hidden('user_id', $user->getId())?>
				<?=form_submit('submit', 'Join group')?>
				<select name="group_id">
					<option value="">--- group ---</option>
					<?foreach($EM->createQuery('SELECT g FROM models\RBAC\Group g WHERE g.granular = FALSE AND g.root = 1')->getResult() as $group):?>
						<?if( ! $user->inGroup($group)):?>
							<option value="<?=$group->getId()?>"><?=$group->getName()?></option>
						<?endif?>
					<?endforeach?>
				</select>
			<?=form_close()?>
		</li>
	</ul>
</div>
<div class="clear"></div>
<hr>