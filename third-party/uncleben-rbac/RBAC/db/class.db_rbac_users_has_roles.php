<?php

class rbac_users_has_roles
{
	Function GetMetaData(){
				return array(
					'id'=>array('type'=>'int', 'length'=>11, 'null'=>'NO', 'key'=>'PRI', 'default'=>'', 'extra'=>'auto_increment'),
					'users_id'=>array('type'=>'int', 'length'=>11, 'null'=>'NO', 'key'=>'UNI', 'default'=>'', 'extra'=>''),
					'roles_id'=>array('type'=>'int', 'length'=>10, 'null'=>'NO', 'key'=>'UNI', 'default'=>'', 'extra'=>'')
			);
	}
}
?>