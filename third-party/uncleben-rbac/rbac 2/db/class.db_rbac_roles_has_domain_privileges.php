<?php

class rbac_roles_has_domain_privileges
{
	Function GetMetaData(){
				return array(
					'id'=>array('type'=>'int', 'length'=>11, 'null'=>'NO', 'key'=>'PRI', 'default'=>'', 'extra'=>'auto_increment'),
					'roles_id'=>array('type'=>'int', 'length'=>11, 'null'=>'YES', 'key'=>'UNI', 'default'=>'', 'extra'=>''),
					'privileges_id'=>array('type'=>'int', 'length'=>10, 'null'=>'NO', 'key'=>'UNI', 'default'=>'0', 'extra'=>''),
					'domains_id'=>array('type'=>'int', 'length'=>10, 'null'=>'NO', 'key'=>'UNI', 'default'=>'0', 'extra'=>''),
					'is_allowed'=>array('type'=>'int', 'length'=>3, 'null'=>'YES', 'key'=>'', 'default'=>'0', 'extra'=>'')
			);
	}
}
?>