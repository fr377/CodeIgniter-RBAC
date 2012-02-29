<?php
$action = $EM->find('\models\RBAC\Action', $search_id);
?>
<div class="grid_6">
	<h2>Action (<?=$action->getName()?>)</h2>

	<strong>Participates in privileges</strong>
	<ul>
		<?foreach($action->getPrivileges() as $privilege):?>
			<?if( ! $privilege->isGranular() ):?>
				<li>
					<?=form_open('privileges/revoke')?>
						<?=form_hidden('privilege_id', $privilege->getId())?>
						<?=form_hidden('action_id', $action->getId())?>
						<?=form_submit('submit', 'Exclude')?>
						from <?=$privilege->getName()?>
					<?=form_close()?>
				</li>
			<?endif;?>
		<?endforeach;?>
		<li>
			<?=form_open('privileges/grant')?>
				<?=form_hidden('action_id', $action->getId())?>
				<?=form_submit('submit', 'Grant')?> to 
				<select name="privilege_id">
					<option value="">--- privilege ---</option>
					<?foreach($EM->createQuery('SELECT p FROM models\RBAC\Privilege p WHERE p.granular = FALSE AND p.root = 1')->getResult() as $privilege):?>
						<?if( ! ($privilege->hasAction($action)) ):?>
							<option value="<?=$privilege->getId()?>"><?=$privilege->getName()?></option>
						<?endif?>
					<?endforeach?>
				</select>
			<?=form_close()?>
		</li>
	</ul>
</div>
<div class="clear"></div>
<hr>