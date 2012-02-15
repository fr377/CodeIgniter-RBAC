<?php

/**
  * This class is to be used in conjunction with class.db.php. It's purpose is to generate a meta data file
  * for a given database table.
  * Make sure the folder where the files will be saved has sufficient read and write permissions.
  *
  * @author BV <junk@vhd.com.au> 
  * @license BSD or LGPL 
  * @version 1.0 (2005_12_21) 
  **/

class MakeTableMetaData_bv {

	Function Make($table, $path, $con){
		
		if ($table === 'directory'){return;}
		
		$file_name = 'class.db_'.$table.'.php';
		
		if ($_fp = fopen($path.$file_name, 'w')){
			
			$_content = MakeTableMetaData_bv::GetFileContent($table, $con);
			
			fwrite($_fp, $_content) or trigger_error('Could not write content to meta data file.', E_USER_ERROR);
			fclose($_fp);
			
			return TRUE;
			
		}
		else{
			trigger_error("Make sure '$path' has write permission.", E_USER_ERROR);
			
			return FALSE;
		}
	}
	
	// Create the file contents
	Function GetFileContent($table, $con){
		
		//-----------------------------
		// Get all fields that have unique indices.
		$arr_unique = array();
		
		$_sql = "SHOW INDEX FROM $table";
		$arr_rs = $con->GetAll($_sql);
		
		foreach ($arr_rs as $row_uni){
			if ($row_uni['Non_unique'] === '0'){
				$arr_unique[] = $row_uni['Column_name'];
			}
		}
		
		//-----------------------------
		// Get meta data for all fields
		$_sql = "DESCRIBE $table";
		$arr_meta = $con->GetAll($_sql);
		
		$_t = "\t\t\t\treturn array(\n";
		
		foreach ($arr_meta as $row_meta){
			$field_name = $row_meta['Field'];
			$field_type = $row_meta['Type'];
			$field_null = $row_meta['Null'];
			$field_key = $row_meta['Key'];
			$field_default = $row_meta['Default'];
			$field_extra = $row_meta['Extra'];
			
			if ($field_key === 'MUL' && in_array($field_name, $arr_unique)){
				$field_key = 'UNI';
			}
			
			if (preg_match_all('/\d+/', $field_type, $match)){
				
				if(count($match[0]) > 1){ // We have a DECIMAL field
					$field_length = $match[0][0] +1;
				}
				else {
					$field_length = $match[0][0];
				}
			}
			else{
				$field_length = "''";
			}
			
			if (preg_match('/(smallint|tinyint|int)/', $field_type)){
				$field_type = 'int';
			}
			
			if (preg_match('/varchar/', $field_type)){
				$field_type = 'varchar';
			}
			
			// TODO: Deal with enum more elegantly.
			if (preg_match('/enum/', $field_type)){
				$field_type = 'varchar';
			}
			
			if (preg_match('/longtext/', $field_type)){
				$field_type = 'text';
			}
			
			$_t .= "\t\t\t\t\t'$field_name'=>array('type'=>'$field_type', 'length'=>$field_length, 'null'=>'$field_null', 'key'=>'$field_key', 'default'=>'$field_default', 'extra'=>'$field_extra'),\n";
		}
		
		$_t = rtrim($_t, ", \n");
		
		$_t .= "\n\t\t\t);";
		
		// construct the whole file
		$_content = "<?php\n\n";
		$_content .= "class $table\n";
		$_content .= "{\n";
		$_content .= "\tFunction GetMetaData(){\n";
		$_content .= "$_t\n";
		$_content .= "\t}\n";
		$_content .= "}\n";
		$_content .= "?>";
		
		return $_content;
		
	}


}



?>
