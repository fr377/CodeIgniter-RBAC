<h2>Privileges</h2>

<?php
$ns_manager = new \DoctrineExtensions\NestedSet\Manager(new \DoctrineExtensions\NestedSet\Config($EM, 'models\RBAC\Privilege'));
$rootNode = $this->doctrine->em->createQuery('SELECT p FROM models\RBAC\Privilege p WHERE p.root = 1 AND p.lft = 1');
$rootNode = $rootNode->getResult();
?>

<?if( ! function_exists('__display_privileges__') ):?>
	<? function __display_privileges__(DoctrineExtensions\NestedSet\NodeWrapper $node) { ?>
		<ul>
			<li>
				<?=form_open('privileges/delete')?>
					<?=form_hidden('privilege_id', $node->getId())?>
					<?=form_submit('submit', 'Delete')?>
				<?=form_close()?>
				<?=anchor('privilege/' . $node->getId(), $node)?> (<?=$node->getNode()->countActions()?>)
				
				<? if($node->getNumberChildren() > 0): ?>
					<ul>
						<?foreach($node->getChildren() as $node):?>
							<? __display_privileges__($node); ?>
						<?endforeach?>
						</ul>
				<?endif?>

		</li></ul>
	<? } ?>
<?endif?>

<?if( ! empty($rootNode) ):?>
	<?php
	$rootNode = $ns_manager->wrapNode($rootNode[0]);
	__display_privileges__($rootNode);
	?>
<?else:?>
	<ul>
		<li>No privileges</li>
	</ul>
<?endif?>
<ul>
	<li>
		<?=form_open('privileges/create')?>
			<?=form_input('name')?>
			<?=form_submit('submit', 'Save privilege')?>
		<?=form_close()?>
	</li>
</ul>