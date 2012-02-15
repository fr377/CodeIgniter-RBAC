<?php
/**
  * This class is used to manage a fine grained user management system.
  *
  * Database connection uses the db_bv
  * Mainly a container for static methods.
  *
  **/

class RbacAdmin_bv
{
	
	/**
	  * SetPrivilege() will 
	  *
	  * If $arrActions is empty we have a singular Privelege
	  **/
	function SetPrivilege($name, $desc, $arrActions = '', $conn)
	{
		
		$is_new_privilege = FALSE;
		$is_singular_privilege = FALSE;
		
		// If the $arrActions is not specified we have a singular privilege. However we will add the action to the all_actions privilege
		if ($arrActions == ''){
			$arrActions = array($name=>$desc);
			$name = 'all_actions';
			$desc = '';
		}
		
		// First check whether the privilege name exists or not
		$_sql = "SELECT id FROM rbac_privileges WHERE name = '$name'";
		
		if ($_row = $conn->GetRow($_sql)){
			$privilege_id = $_row['id'];
			echo "Privilege <b>$name</b> with id <b>$privilege_id</b> already exists.<br>";
		}
		else {
			$conn->SetTable('rbac_privileges');
			$conn->AddData('name', $name);
			$conn->AddData('description', $desc);
			$conn->Insert();
			
			$privilege_id = $conn->GetId();
			
			$is_new_privilege = TRUE;
			
			echo "New privilege inserted <b>$name</b> with id <b>$privilege_id</b><br>";
		}
		
		// Loop through the actions
		foreach ($arrActions as $_action=>$_desc){
			$_sql = "SELECT id FROM rbac_actions WHERE name = '$_action'";
			
			$is_new_action = FALSE;
			
			if (!$_row = $conn->GetRow($_sql)){ // action does not exist
				
				$conn->SetTable('rbac_actions');
				$conn->AddData('name', $_action);
				$conn->AddData('description', $_desc);
				$conn->Insert();
				
				$action_id = $conn->GetId();
				$is_new_action = TRUE;
				
				echo "New action inserted <b>$_action</b> with id <b>$action_id</b><br>";
			}
			else{
				$action_id = $_row['id'];
				echo "Action <b>$_action</b> with id <b>$action_id</b> already exists.<br>";
			}
			
			//-----------------------------------
			if ($is_new_privilege || $is_new_action){
				$conn->SetTable('rbac_privileges_has_actions');
				$conn->AddData('privileges_id', $privilege_id);
				$conn->AddData('actions_id', $action_id);
				if ($conn->Insert()){
					echo "New relationship between <b>$name ($privilege_id)</b> and <b>$_action ($action_id)</b> has been added.<br>";
				}
			}
			
			//----------------------
			if ($is_new_action){
				// We need to create a singularity in the privileges table
				$conn->SetTable('rbac_privileges');
				$conn->AddData('name', $_action);
				$conn->AddData('description', $_desc);
				$conn->AddData('is_singular', 1);
				$conn->Insert();
				
				$action_privilege_id = $conn->GetId();
				
				echo "New singular privilege inserted <b>$_action</b> with id <b>$action_privilege_id</b><br>";
				
				$conn->SetTable('rbac_privileges_has_actions');
				$conn->AddData('privileges_id', $action_privilege_id);
				$conn->AddData('actions_id', $action_id);
				$conn->Insert();
				
				echo "New singluar relationship between <b>$_action ($action_privilege_id)</b> and <b>$_action ($action_id)</b> has been added.<br>";
				
			}
		}
		
		echo "<br>";
	}
	
	
	/**
	   *
	   *
	   **/
	// delete privilege.
	function DeletePrivilege($name, $conn)
	{
		// First delete all the rows in the rbac_privileges_has_actions table.
		$privilege_id = RbacAdmin_bv::GetRowId('rbac_privileges', $name, $conn);
		
		if ($privilege_id === FALSE){ // the privilege name does not exist
			return FALSE;
		}
		
		if ($conn->Exists("privileges_id = $privilege_id", 'rbac_privileges_has_actions')){
			$_sql = "DELETE FROM rbac_privileges_has_actions WHERE privileges_id = $privilege_id";
			
			$conn->Execute($_sql);
		}	
		
		// Find out if it is singular
		if ($conn->Exists("id = $privilege_id AND is_singular = 1", 'rbac_privileges')){ // Is singular
			
			// If it is singular we need to find out the action_id and revisit the rbac_privileges_has_action table
			$_sql = "SELECT id FROM rbac_actions WHERE name = '$name'";
			$_row = $conn->GetRow($_sql);
			$action_id = $_row['id'];
			
			if ($conn->Exists("actions_id = $action_id", 'rbac_privileges_has_actions')){
				
				$_sql = "DELETE FROM rbac_privileges_has_actions WHERE actions_id = $action_id";
				
				if ($conn->Execute($_sql)){
					$_sql = "DELETE FROM rbac_actions WHERE id = $action_id";
					$conn->Execute($_sql);
				}
			}
		}
		
		// Delete the row from the privilege entry
		$_sql = "DELETE FROM rbac_privileges WHERE id = $privilege_id";
		$conn->Execute($_sql);
		
	}
	
	/**
	   *
	   *
	   **/
	function DeleteAction($name, $conn)
	{
		// First delete all the rows in the rbac_privileges_has_actions table.
		$action_id = RbacAdmin_bv::GetRowId('rbac_actions', $name, $conn);
		
		if ($action_id === FALSE){ // the privilege name does not exist
			return FALSE;
		}
		
		// Must delete the parent table first to avoid FK violation.
		if ($conn->Exists("actions_id = $action_id", 'rbac_privileges_has_actions')){
			$_sql = "DELETE FROM rbac_privileges_has_actions WHERE actions_id = $action_id";
			$conn->Execute($_sql);
		}
		
		$action_id = RbacAdmin_bv::GetRowId('rbac_actions', $name, $conn);
		$_sql = "DELETE FROM rbac_actions WHERE id = $action_id";
		$conn->Execute($_sql);
		
		// Delete the row from the privilege entry
		$_sql = "DELETE FROM rbac_privileges WHERE name = '$name'"; // actions always have an entry in the privilege table with the same name
		$conn->Execute($_sql);	
	}
	
	/**
	  * SetDomain() will 
	  *
	  * If $arrObjects is empty we have a singular Privelege
	  **/
	function SetDomain($name, $desc, $arrObjects = '', $conn)
	{
		
		$is_new_domain = FALSE;
		$is_singular_domain = FALSE;
		
		// If the $arrObjects is not specified we have a singular domain. However we will add the object to the all_objects domain
		if ($arrObjects == ''){
			$arrObjects = array($name=>$desc);
			$name = 'all_objects';
			$desc = '';
		}
		
		// First check whether the domain name exists or not
		$_sql = "SELECT id FROM rbac_domains WHERE name = '$name'";
		
		if ($_row = $conn->GetRow($_sql)){
			$domain_id = $_row['id'];
			echo "Domain <b>$name</b> with id <b>$domain_id</b> already exists.<br>";
		}
		else {
			$conn->SetTable('rbac_domains');
			$conn->AddData('name', $name);
			$conn->AddData('description', $desc);
			$conn->Insert();
			
			$domain_id = $conn->GetId();
			
			$is_new_domain = TRUE;
			
			echo "New domain inserted <b>$name</b> with id <b>$domain_id</b><br>";
		}
		
		// Loop through the objects
		foreach ($arrObjects as $_object=>$_desc){
			$_sql = "SELECT id FROM rbac_objects WHERE name = '$_object'";
			
			$is_new_object = FALSE;
			
			if (!$_row = $conn->GetRow($_sql)){ // object does not exist
				
				$conn->SetTable('rbac_objects');
				$conn->AddData('name', $_object);
				$conn->AddData('description', $_desc);
				$conn->Insert();
				
				$object_id = $conn->GetId();
				$is_new_object = TRUE;
				
				echo "New object inserted <b>$_object</b> with id <b>$object_id</b><br>";
			}
			else{
				$object_id = $_row['id'];
				echo "Object <b>$_object</b> with id <b>$object_id</b> already exists.<br>";
			}
			
			//-----------------------------------
			if ($is_new_domain || $is_new_object){
				$conn->SetTable('rbac_domains_has_objects');
				$conn->AddData('domains_id', $domain_id);
				$conn->AddData('objects_id', $object_id);
				if ($conn->Insert()){
					echo "New relationship between <b>$name ($domain_id)</b> and <b>$_object ($object_id)</b> has been added.<br>";
				}
			}
			
			//----------------------
			if ($is_new_object){
				// We need to create a singularity in the domains table
				$conn->SetTable('rbac_domains');
				$conn->AddData('name', $_object);
				$conn->AddData('description', $_desc);
				$conn->AddData('is_singular', 1);
				$conn->Insert();
				
				$object_domain_id = $conn->GetId();
				
				echo "New singular domain inserted <b>$_object</b> with id <b>$object_domain_id</b><br>";
				
				$conn->SetTable('rbac_domains_has_objects');
				$conn->AddData('domains_id', $object_domain_id);
				$conn->AddData('objects_id', $object_id);
				$conn->Insert();
				
				echo "New singluar relationship between <b>$_object ($object_domain_id)</b> and <b>$_object ($object_id)</b> has been added.<br>";
				
			}
		}
		
		echo "<br>";
	}
	
	/**
	   *
	   *
	   **/
	// delete domain.
	function DeleteDomain($name, $conn)
	{
		// First delete all the rows in the rbac_domains_has_objects table.
		$domain_id = RbacAdmin_bv::GetRowId('rbac_domains', $name, $conn);
		
		if ($domain_id === FALSE){ // the domain name does not exist
			return FALSE;
		}
		
		if ($conn->Exists("domains_id = $domain_id", 'rbac_domains_has_objects')){
			$_sql = "DELETE FROM rbac_domains_has_objects WHERE domains_id = $domain_id";
			
			$conn->Execute($_sql);
		}	
		
		// Find out if it is singular
		if ($conn->Exists("id = $domain_id AND is_singular = 1", 'rbac_domains')){ // Is singular
			
			// If it is singular we need to find out the object_id and revisit the rbac_domains_has_object table
			$_sql = "SELECT id FROM rbac_objects WHERE name = '$name'";
			$_row = $conn->GetRow($_sql);
			$object_id = $_row['id'];
			
			if ($conn->Exists("objects_id = $object_id", 'rbac_domains_has_objects')){
				
				$_sql = "DELETE FROM rbac_domains_has_objects WHERE objects_id = $object_id";
				
				if ($conn->Execute($_sql)){
					$_sql = "DELETE FROM rbac_objects WHERE id = $object_id";
					$conn->Execute($_sql);
				}
			}
		}
		
		// Delete the row from the domain entry
		$_sql = "DELETE FROM rbac_domains WHERE id = $domain_id";
		$conn->Execute($_sql);
		
	}
	
	/**
	   *
	   *
	   **/
	function DeleteObject($name, $conn)
	{
		// First delete all the rows in the rbac_domains_has_objects table.
		$object_id = RbacAdmin_bv::GetRowId('rbac_objects', $name, $conn);
		
		if ($object_id === FALSE){ // the domain name does not exist
			return FALSE;
		}
		
		// Must delete the parent table first to avoid FK violation.
		if ($conn->Exists("objects_id = $object_id", 'rbac_domains_has_objects')){
			$_sql = "DELETE FROM rbac_domains_has_objects WHERE objects_id = $object_id";
			$conn->Execute($_sql);
		}
		
		$object_id = RbacAdmin_bv::GetRowId('rbac_objects', $name, $conn);
		$_sql = "DELETE FROM rbac_objects WHERE id = $object_id";
		$conn->Execute($_sql);
		
		// Delete the row from the domain entry
		$_sql = "DELETE FROM rbac_domains WHERE name = '$name'"; // objects always have an entry in the domain table with the same name
		$conn->Execute($_sql);	
	}
	
	/**
	  *
	  *
	  **/
	function SetRole($name, $importance = 0, $arrDefs, $conn, $description='')
	{
		// $arrDef is defined as 0=>Privelege, 1=>Domain, 2=>is_allowed
		
		// First check that the role name already exists or not.
		$_sql = "SELECT id FROM rbac_roles WHERE name = '$name'";
		
		if ($_row = $conn->GetRow($_sql)){
			$role_id = $_row['id'];
			echo "Role <b>$name</b> with id <b>$role_id</b> already exists.<br>";
		}
		else {
			// Create the role
			$conn->SetTable('rbac_roles');
			$conn->AddData('name', $name);
			$conn->AddData('description', $description);
			$conn->AddData('importance', $importance);
			$conn->Insert();
			
			$role_id = $conn->GetId();
			echo "New role: <b>$name</b> created.<br>";
		}
		
		if ($role_id){
			
			// Loop through the role definition.
			foreach ($arrDefs as $arr_def){
				
				$_privilege = $arr_def['0'];
				$_domain = $arr_def['1'];
				$is_allowed = (int) $arr_def['2'];
				
				$privilege_id = RbacAdmin_bv::GetRowId('rbac_privileges', $_privilege, $conn);
				$domain_id = RbacAdmin_bv::GetRowId('rbac_domains', $_domain, $conn);
				
				if ($privilege_id && $domain_id){ // If we have a valid ID
					
					$_sql = "INSERT IGNORE INTO rbac_roles_has_domain_privileges 
									(roles_id, is_allowed, privileges_id, domains_id) 
									VALUES 
									($role_id, $is_allowed, $privilege_id, $domain_id)
									-- ON DUPLICATE KEY UPDATE 
									--	 is_allowed = $is_allowed,
									--	 privileges_id = '$privilege_id',
									-- domains_id = '$domain_id'
								";
					
					$conn->Execute($_sql);
					
					echo "-- Role <b>$name</b> has the following privilege (<b>$_privilege: $privilege_id</b>) and domain (<b>$_domain: $domain_id</b>). Is allowed: <b>$is_allowed</b><br>";
				}
			}
		}
	}
	
	/**
	  *
	  *
	  **/
	function SetUserRole($userId, $role, $conn)
	{
		if (!$role_id = RbacAdmin_bv:: GetRowId('rbac_roles', $role, $conn))
		{
			//echo "The role<b>$role</b> does not exist.<br>";
			return FALSE;
		}
		
		$conn->SetTable('rbac_users_has_roles');
		$conn->AddData('users_id', $userId);
		$conn->AddData('roles_id', $role_id);
		if ($conn->Insert()){
			echo "User <b>$userId</b> has been given the role <b>$role</b>.<br>";
		}
	}
	
	/**
	  *
	  *
	  * @access private
	  **/
	function GetRowId ($table, $name, $conn)
	{
		$_sql = "SELECT id FROM $table WHERE name = '$name'";
		
		if ($_row = $conn->GetRow($_sql)){
			return $_row['id'];
		}
		else{
			return FALSE;
		}
	}
	
}
