<?php

class users
{
	Function GetMetaData(){
				return array(
					'id'=>array('type'=>'int', 'length'=>11, 'null'=>'NO', 'key'=>'PRI', 'default'=>'', 'extra'=>'auto_increment'),
					'username'=>array('type'=>'varchar', 'length'=>50, 'null'=>'NO', 'key'=>'UNI', 'default'=>'', 'extra'=>''),
					'pswd'=>array('type'=>'varchar', 'length'=>70, 'null'=>'NO', 'key'=>'', 'default'=>'', 'extra'=>'')
			);
	}
}
?>