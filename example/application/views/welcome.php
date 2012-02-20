<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>'Fine-grained' Role-Based Access Control Example</title>
	<link rel="stylesheet" media="screen" href="https://cdn.localhost/css/grid.960.12.css">

	<style type="text/css">

/*
	::selection{ background-color: #E13300; color: white; }
	::moz-selection{ background-color: #E13300; color: white; }
	::webkit-selection{ background-color: #E13300; color: white; }
*/

	form {
		display:inline-block;
	}

	body {
		background-color:#fff;
		margin: 40px;
		font: 13px/20px normal Helvetica, Arial, sans-serif;
		color: #4F5155;
	}

	a {
		color: #003399;
		background-color: transparent;
		font-weight: normal;
	}
	
	div.controls_box {
		min-height:140px;
	}

	h1 {
		color: #444;
		background-color: transparent;
		border-bottom: 1px solid #D0D0D0;
		font-size: 19px;
		font-weight: normal;
		margin: 0 0 14px 0;
		padding: 14px 15px 10px 15px;
	}

	code {
		font-family: Consolas, Monaco, Courier New, Courier, monospace;
		font-size: 12px;
		background-color: #f9f9f9;
		border: 1px solid #D0D0D0;
		color: #002166;
		display: block;
		margin: 14px 0 14px 0;
		padding: 12px 10px 12px 10px;
	}

	.body{
		margin: 0 15px 0 15px;
	}
	
	p.footer{
		text-align: right;
		font-size: 11px;
		border-top: 1px solid #D0D0D0;
		line-height: 32px;
		padding: 0 10px 0 10px;
		margin: 20px 0 0 0;
	}
	
	.container_12 {
		border: 1px solid #D0D0D0;
		-webkit-box-shadow: 0 0 8px #D0D0D0;
	}
	</style>
</head>
<body>
<div class="clearfix container_12">
	<h1><?=anchor(NULL, "'Fine-Grained' Role Based Access Control Demo")?></h1>
	
	<div class="body">
		<p><?=anchor('welcome/setup', 'Re-initialize')?> the database. Try a populating the database with a curious <?=anchor('welcome/scenario/1', 'scenario')?>, or imagine your own.</p>
		
		<?=form_open('inquiry')?>
			Does
			<select name="user_id">
			<?foreach(User::all() as $user):?>
				<option value="<?=$user->id?>"><?=$user->email?></option>
			<?endforeach;?>
			</select>
			
			have
			<select name="action_id">
			<?foreach(Action::all() as $action):?>
				<option value="<?=$action->id?>"><?=$action->name?></option>
			<?endforeach;?>
			</select>
			
			to
			<select name="entity_id">
			<?foreach(Entity::all() as $entity):?>
				<option value="<?=$entity->id?>"><?=$entity->name?></option>
			<?endforeach;?>
			</select>

			<?=form_submit('submit', 'Inquire')?>
		<?=form_close()?>
	</div>

	<?php
	switch ($display) {
		case 'Action':
			$this->load->view('_display_action');
			break;
			
		case 'User':
			$this->load->view('_display_user');
			break;

		case 'Group':
			$this->load->view('_display_group');
			break;

		case 'Privilege':
			$this->load->view('_display_privilege');
			break;
			
		case 'Resource':
			$this->load->view('_display_resource');
			break;
		
		case 'Entity':
			$this->load->view('_display_entity');
			break;

		default:
			if ($display)
				echo '<code style="margin:20px;"><pre>' . print_r($display, TRUE) . '</pre></code>';
	}
	?>

	<div class="clear"></div>

	<div class="grid_4">
		<div class="controls_box"><?=$this->load->view('_controls_groups');?></div>
		<div class="controls_box"><?=$this->load->view('_controls_users');?></div>
	</div>

	<div class="grid_4">
		<div class="controls_box"><?=$this->load->view('_controls_privileges');?></div>
		<div class="controls_box"><?=$this->load->view('_controls_actions');?></div>
	</div>
	
	<div class="grid_4">
		<div class="controls_box"><?=$this->load->view('_controls_resources');?></div>
		<div class="controls_box"><?=$this->load->view('_controls_entities');?></div>
	</div>

	<div class="grid_12">
		<?=$this->load->view('_controls_rules');?>
	</div>

	<div class="grid_12">
		<p class="footer">Page rendered in <strong>{elapsed_time}</strong> seconds using {memory_usage}</p>
	</div>
</div>

</body>
</html>