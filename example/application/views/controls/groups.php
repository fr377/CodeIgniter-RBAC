<h2>Groups</h2>

<?php
$ns_manager = new \DoctrineExtensions\NestedSet\Manager(new \DoctrineExtensions\NestedSet\Config($EM, 'models\RBAC\Group'));
$rootNode = $this->doctrine->em->createQuery('SELECT g FROM models\RBAC\Group g WHERE g.root = 1 AND g.lft = 1');
$rootNode = $rootNode->getResult();
?>

<?if( ! function_exists('__display_groups__') ):?>
	<? function __display_groups__(DoctrineExtensions\NestedSet\NodeWrapper $node) { ?>
		<ul>
			<li>
				<?=form_open('groups/delete')?>
					<?=form_hidden('group_id', $node->getId())?>
					<?=form_submit('submit', 'Delete')?>
				<?=form_close()?>
				<?=form_open('groups/change_importance')?>
					<?=form_hidden('group_id', $node->getId())?>
					<?php
					$options = array();
					for ($i = 1; $i <= 99; $i++)
						$options[$i] = $i;
					?>
					<?=form_dropdown('importance', $options, $node->getNode()->getImportance(), 'onChange="this.form.submit();"')?>
				<?=form_close()?>
				<?=anchor('group/' . $node->getId(), $node)?> (<?=$node->getNode()->countMembers()?>)
				
				<? if($node->getNumberChildren() > 0): ?>
					<ul>
						<?foreach($node->getChildren() as $node):?>
							<? __display_groups__($node); ?>
						<?endforeach?>
						</ul>
				<?endif?>

		</li></ul>
	<? } ?>
<?endif?>

<?if( ! empty($rootNode) ):?>
	<?php
	$rootNode = $ns_manager->wrapNode($rootNode[0]);
	__display_groups__($rootNode);
	?>
<?else:?>
	<ul>
		<li>No groups</li>
	</ul>
<?endif?>
<ul>
	<li>
		<?=form_open('groups/create')?>
			<?=form_input('name')?>
			<?=form_submit('submit', 'Save group')?>
		<?=form_close()?>
	</li>
</ul>