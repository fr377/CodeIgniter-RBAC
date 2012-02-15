<?php

class rbac_privileges
{
	Function GetMetaData(){
				return array(
					'id'=>array('type'=>'int', 'length'=>10, 'null'=>'NO', 'key'=>'PRI', 'default'=>'', 'extra'=>'auto_increment'),
					'name'=>array('type'=>'varchar', 'length'=>50, 'null'=>'YES', 'key'=>'UNI', 'default'=>'', 'extra'=>''),
					'description'=>array('type'=>'text', 'length'=>'', 'null'=>'YES', 'key'=>'', 'default'=>'', 'extra'=>''),
					'is_singular'=>array('type'=>'int', 'length'=>4, 'null'=>'NO', 'key'=>'', 'default'=>'0', 'extra'=>'')
			);
	}
}
?>