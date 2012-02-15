<?php

class rbac_actions
{
	Function GetMetaData(){
				return array(
					'id'=>array('type'=>'int', 'length'=>10, 'null'=>'NO', 'key'=>'PRI', 'default'=>'', 'extra'=>'auto_increment'),
					'name'=>array('type'=>'varchar', 'length'=>50, 'null'=>'YES', 'key'=>'UNI', 'default'=>'', 'extra'=>''),
					'description'=>array('type'=>'text', 'length'=>'', 'null'=>'YES', 'key'=>'', 'default'=>'', 'extra'=>'')
			);
	}
}
?>