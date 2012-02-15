<?php

class rbac_domains_has_objects
{
	Function GetMetaData(){
				return array(
					'id'=>array('type'=>'int', 'length'=>11, 'null'=>'NO', 'key'=>'PRI', 'default'=>'', 'extra'=>'auto_increment'),
					'domains_id'=>array('type'=>'int', 'length'=>10, 'null'=>'NO', 'key'=>'MUL', 'default'=>'0', 'extra'=>''),
					'objects_id'=>array('type'=>'int', 'length'=>10, 'null'=>'NO', 'key'=>'MUL', 'default'=>'0', 'extra'=>'')
			);
	}
}
?>