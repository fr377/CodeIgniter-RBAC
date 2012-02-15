<?php
/**
  * This class implements a fine grained user management system.
  *
  * Database connection uses the db_bv class. (You need the file class.db_bv.php)
  *
  * The IsAllowedTo() function can be called as a static function, in which case a database connection must be provided.
  *
  * Copyright Ben Vautier 2006-2007 (ben@sqlrecipes.com)
  * License: MIT
  * version: 2007-02-07
  *
  **/

class Rbac_bv
{
	
	var $mConn; // database connection
	
	/**
	  * CONSTRUCTOR
	  *
	  * @param $conn object database connection
	  **/
	function Rbac_bv($conn)
	{
		$this->mConn = $conn;
	}
	
	/**
	  * IsAllowedTo() checks whether a user is allowed to perform an action on a given object.
	  * Return TRUE on success or FALSE on failure.
	  *
	  * If a user has two roles with the same importance. And each of those roles have the same action on an object but one is allowed and the other isn't.
	  * The result will always be negative. I.e. is not allowed always wins when evertyhing else is equal.
	  *
	  * @access public
	  * @param $id int, the user id
	  * @param $action string, the action name
	  * @param $object string, the object name
	  * @param $conn object, the database connection. (Needed if called as a static function.)
	  *
	  **/
	function IsAllowedTo($id, $action, $object, $conn = ''){
		
		if (!is_object($conn)){
			if (!isset($this->mConn)){
				trigger_error('Must supply database connection.', E_USER_ERROR);
			}
			else{
				$conn = $this->mConn;
			}
		}
		
		// Check whether answer is already in sessions.
		if (isset($_SESSION['rbac_bv'][$id][$action][$object])) {
			if ($_SESSION['rbac_bv'][$id][$action][$object]){ // Do not touch. This if statement must be inside the parent if statement.
				return TRUE;
			} else {
				return FALSE;
			}
		}
		
		// We order the role by importance. The most important role will come first. Therefore when we loop through the record we will ignore
		// all other roles.
		$_sql = "
			SELECT is_allowed, t2.name AS privilege, t2.is_singular AS is_privilege_singular, t4.name AS action, t5.name AS domain, t5.is_singular AS is_domain_singular, t7.name AS object, t8.name as role, t8.importance FROM rbac_roles_has_domain_privileges AS t1
				-- Privileges Joins --
				INNER JOIN rbac_privileges AS t2 ON t2.id = t1.privileges_id 
				INNER JOIN rbac_privileges_has_actions AS t3 ON t3.privileges_id = t2.id
				INNER JOIN rbac_actions AS t4 ON t4.id = t3.actions_id
				-- Domain Joins --
				INNER JOIN rbac_domains AS t5 ON t5.id = t1.domains_id
				INNER JOIN rbac_domains_has_objects AS t6 ON t6.domains_id = t5.id
				INNER JOIN rbac_objects AS t7 ON t7.id = t6.objects_id
				-- Roles to user Joins --
				INNER JOIN rbac_roles AS t8 ON t8.id = t1.roles_id
				INNER JOIN rbac_users_has_roles AS t9 ON t9.roles_id = t8.id
			WHERE users_id = $id AND t4.name = '$action' AND t7.name = '$object'
			ORDER BY t8.importance DESC, t8.name
			";
		
		$conn->GetAll($_sql);
		
		//----------------
		// Initialise variables.
		$weight = -1; // Used to find out which privileges take precedence.
		$is_allowed = 0; // FALSE, initialise
		$prev_importance = '';
		$count = 0;
		
		// Loop through all matches
		while ($conn->NextRow(FALSE)){
			
			$importance = $conn->importance;
			$is_allowed = (int) $conn->is_allowed;
			
			// We are only interested in the roles with the most importance (i.e. Some roles may have the same importance.)
			if ($count > 0 && $importance !== $prev_importance){
				break;
			}
			
			$new_weight = (int) $conn->is_privilege_singular + (int) $conn->is_domain_singular;
			
			if ($new_weight > $weight){
				$weight = $new_weight;
			}
			else if ($new_weight == $weight && (int) $is_allowed === 1 && (int) $is_allowed === 0){
				
				// We always give more weight to denials.
				$weight = $new_weight;
			}
			
			// echo "Role is $conn->role and weight is $new_weight and is_allowed $conn->is_allowed ($is_allowed)<br>";
			
			$prev_importance = $importance;
			$count++;
			
		}
		
		//------------------------------
		// Store value in sessions for next time.
		$_SESSION['rbac_bv'][$id][$action][$object] = $is_allowed;
		//session_write_close();
		
		//-------------
		// Return answer
		if ($is_allowed){
			return TRUE;
		} else {
			return FALSE;
		}
	}
	
	
	/**
	  * LoadAllUserPrivileges() Will load ALL the privileges associated with a user at once.
	  *
	  * @access public
	  * @param $id int, the user id
	  * @param $conn object, the database connection. (Needed if called as a static function.)
	  *
	  **/
	function LoadAllUserPrivileges($id, $conn = ''){
		
		unset($_SESSION['rbac_bv']);
		
		if (!is_object($conn)){
			if (!isset($this->mConn)){
				trigger_error('Must supply database connection.', E_USER_ERROR);
			}
			else{
				$conn = $this->mConn;
			}
		}
		
		// We order the role by importance. The most important role will come first. Therefore when we loop through the record we will ignore
		// all other roles.
		$_sql = "
			SELECT is_allowed, t2.name AS privilege, t2.is_singular AS is_privilege_singular, t4.name AS action, t5.name AS domain, t5.is_singular AS is_domain_singular, t7.name AS object, t8.name as role, t8.importance FROM rbac_roles_has_domain_privileges AS t1
				-- Privileges Joins --
				INNER JOIN rbac_privileges AS t2 ON t2.id = t1.privileges_id 
				INNER JOIN rbac_privileges_has_actions AS t3 ON t3.privileges_id = t2.id
				INNER JOIN rbac_actions AS t4 ON t4.id = t3.actions_id
				-- Domain Joins --
				INNER JOIN rbac_domains AS t5 ON t5.id = t1.domains_id
				INNER JOIN rbac_domains_has_objects AS t6 ON t6.domains_id = t5.id
				INNER JOIN rbac_objects AS t7 ON t7.id = t6.objects_id
				-- Roles to user Joins --
				INNER JOIN rbac_roles AS t8 ON t8.id = t1.roles_id
				INNER JOIN rbac_users_has_roles AS t9 ON t9.roles_id = t8.id
			WHERE users_id = $id
			ORDER BY t4.name,  t7.name, t8.importance DESC
			";
		
		$conn->GetAll($_sql);
		
		//----------------
		// Initialise variables.
		$weight = -1; // Used to find out which privileges take precedence.
		$is_allowed = 0; // FALSE, initialise
		$prev_importance = '';
		$prev_action= '';
		$prev_object = '';
		$prev_is_allowed= '';
		$count = 0;
		$arr_data = array(); // Store temporary data
		
		// Loop through all matches
		while ($conn->NextRow(FALSE)){
			
			$action = $conn->action;
			$object = $conn->object;
			$importance = $conn->importance;
			$is_allowed = (int) $conn->is_allowed;
			
			// We are only interested in the roles with the most importance (i.e. Some roles may have the same importance.)
			
			if ($count > 0 && $action === $prev_action && $object === $prev_object){
				if ($importance < $prev_importance || $prev_is_allowed === 0){
					continue;
				}
			}
			
			$new_weight = (int) $conn->is_privilege_singular + (int) $conn->is_domain_singular;
			
			if ($new_weight > $weight){
				
				$weight = $new_weight;
			}
			else if ($new_weight == $weight && (int) $is_allowed === 1 && $is_allowed === 0){
				
				// We always give more weight to denials.
				$weight = $new_weight;
			}
			
			// echo "Role is $conn->role and weight is $new_weight and is_allowed $conn->is_allowed ($is_allowed)<br>";
			
			$prev_importance = $importance;
			$prev_object = $object;
			$prev_action = $action;
			$prev_is_allowed = $is_allowed;
			
			$count++;
			
			//------------------------------
			// Store value in sessions for next time.
			echo "The action is $action and the object $object and is allowed $is_allowed<br>";
			$_SESSION['rbac_bv'][$id][$action][$object] = $is_allowed;
			
		}
		
		session_write_close();
		
		//-------------
		// Return answer
		if ($is_allowed){
			return TRUE;
		} else {
			return FALSE;
		}
	}
}
?>
