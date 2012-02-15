<?php

class rbac_roles
{
	Function GetMetaData(){
				return array(
					'id'=>array('type'=>'int', 'length'=>11, 'null'=>'NO', 'key'=>'PRI', 'default'=>'', 'extra'=>'auto_increment'),
					'name'=>array('type'=>'varchar', 'length'=>200, 'null'=>'NO', 'key'=>'UNI', 'default'=>'', 'extra'=>''),
					'description'=>array('type'=>'text', 'length'=>'', 'null'=>'NO', 'key'=>'', 'default'=>'', 'extra'=>''),
					'importance'=>array('type'=>'int', 'length'=>11, 'null'=>'NO', 'key'=>'', 'default'=>'0', 'extra'=>'')
			);
	}
}
?>