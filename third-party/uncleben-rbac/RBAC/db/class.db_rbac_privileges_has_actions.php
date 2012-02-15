<?php

class rbac_privileges_has_actions
{
	Function GetMetaData(){
				return array(
					'id'=>array('type'=>'int', 'length'=>11, 'null'=>'NO', 'key'=>'PRI', 'default'=>'', 'extra'=>'auto_increment'),
					'privileges_id'=>array('type'=>'int', 'length'=>10, 'null'=>'NO', 'key'=>'MUL', 'default'=>'0', 'extra'=>''),
					'actions_id'=>array('type'=>'int', 'length'=>10, 'null'=>'NO', 'key'=>'MUL', 'default'=>'0', 'extra'=>'')
			);
	}
}
?>