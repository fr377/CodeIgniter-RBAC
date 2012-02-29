<h2>Resources</h2>

<?php
$ns_manager = new \DoctrineExtensions\NestedSet\Manager(new \DoctrineExtensions\NestedSet\Config($EM, 'models\RBAC\Resource'));
$rootNode = $this->doctrine->em->createQuery('SELECT r FROM models\RBAC\Resource r WHERE r.root = 1 AND r.lft = 1');
$rootNode = $rootNode->getResult();
?>

<?if( ! function_exists('__display_resources__') ):?>
	<? function __display_resources__(DoctrineExtensions\NestedSet\NodeWrapper $node) { ?>
		<ul>
			<li>
				<?=form_open('resources/delete')?>
					<?=form_hidden('resource_id', $node->getId())?>
					<?=form_submit('submit', 'Delete')?>
				<?=form_close()?>
				<?=anchor('resource/' . $node->getId(), $node)?> (<?=$node->getNode()->countEntities()?>)
				
				<? if($node->getNumberChildren() > 0): ?>
					<ul>
						<?foreach($node->getChildren() as $node):?>
							<? __display_resources__($node); ?>
						<?endforeach?>
						</ul>
				<?endif?>

		</li></ul>
	<? } ?>
<?endif?>

<?if( ! empty($rootNode) ):?>
	<?php
	$rootNode = $ns_manager->wrapNode($rootNode[0]);
	__display_resources__($rootNode);
	?>
<?else:?>
	<ul>
		<li>No resources</li>
	</ul>
<?endif?>
<ul>
	<li>
		<?=form_open('resources/create')?>
			<?=form_input('name')?>
			<?=form_submit('submit', 'Save resource')?>
		<?=form_close()?>
	</li>
</ul>
