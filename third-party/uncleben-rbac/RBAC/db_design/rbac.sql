SET FOREIGN_KEY_CHECKS = 0;

DROP DATABASE IF EXISTS rbac;

CREATE DATABASE rbac;

SET FOREIGN_KEY_CHECKS = 1;

USE rbac;


/************ Update: Tables ***************/

/******************** Add Table: rbac_actions ************************/

/* Build Table Structure */
CREATE TABLE rbac_actions
(
	id INTEGER UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
	name VARCHAR(50) NULL,
	description TEXT NULL
) ENGINE=INNODB;

/* Table Items: rbac_actions */

/* Update Indexes for: rbac_actions */
CREATE UNIQUE INDEX unique_name ON rbac_actions (name);

/******************** Add Table: rbac_domains ************************/

/* Build Table Structure */
CREATE TABLE rbac_domains
(
	id INTEGER UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
	name VARCHAR(40) NULL,
	description TEXT NULL,
	is_singular TINYINT NOT NULL DEFAULT 0
) ENGINE=INNODB;

/* Table Items: rbac_domains */

/* Update Indexes for: rbac_domains */
CREATE UNIQUE INDEX unique_name ON rbac_domains (name);

/******************** Add Table: rbac_domains_has_objects ************************/

/* Build Table Structure */
CREATE TABLE rbac_domains_has_objects
(
	id INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY,
	domains_id INTEGER UNSIGNED NOT NULL DEFAULT 0,
	objects_id INTEGER UNSIGNED NOT NULL DEFAULT 0
) ENGINE=INNODB;

/* Table Items: rbac_domains_has_objects */

/* Update Indexes for: rbac_domains_has_objects */
CREATE INDEX fk_domains_has_objects_domains ON rbac_domains_has_objects (domains_id);
CREATE INDEX fk_domains_has_objects_objects ON rbac_domains_has_objects (objects_id);

/******************** Add Table: rbac_objects ************************/

/* Build Table Structure */
CREATE TABLE rbac_objects
(
	id INTEGER UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
	name VARCHAR(50) NULL,
	description TEXT NULL
) ENGINE=INNODB;

/* Table Items: rbac_objects */

/* Update Indexes for: rbac_objects */
CREATE UNIQUE INDEX unique_name ON rbac_objects (name);

/******************** Add Table: rbac_privileges ************************/

/* Build Table Structure */
CREATE TABLE rbac_privileges
(
	id INTEGER UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
	name VARCHAR(50) NULL,
	description TEXT NULL,
	is_singular TINYINT NOT NULL DEFAULT 0
) ENGINE=INNODB;

/* Table Items: rbac_privileges */

/* Update Indexes for: rbac_privileges */
CREATE UNIQUE INDEX unique_name ON rbac_privileges (name);

/******************** Add Table: rbac_privileges_has_actions ************************/

/* Build Table Structure */
CREATE TABLE rbac_privileges_has_actions
(
	id INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY,
	privileges_id INTEGER UNSIGNED NOT NULL DEFAULT 0,
	actions_id INTEGER UNSIGNED NOT NULL DEFAULT 0
) ENGINE=INNODB;

/* Table Items: rbac_privileges_has_actions */

/* Update Indexes for: rbac_privileges_has_actions */
CREATE INDEX fk_privileges_has_actions_actions ON rbac_privileges_has_actions (actions_id);
CREATE INDEX fk_privileges_has_actions_privileges ON rbac_privileges_has_actions (privileges_id);

/******************** Add Table: rbac_roles ************************/

/* Build Table Structure */
CREATE TABLE rbac_roles
(
	id INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY,
	name VARCHAR(200) NOT NULL,
	description TEXT NOT NULL,
	importance INTEGER NOT NULL DEFAULT 0
) TYPE=InnoDB;

/* Table Items: rbac_roles */

/* Update Indexes for: rbac_roles */
CREATE UNIQUE INDEX unique_name ON rbac_roles (name);

/******************** Add Table: rbac_roles_has_domain_privileges ************************/

/* Build Table Structure */
CREATE TABLE rbac_roles_has_domain_privileges
(
	id INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY,
	roles_id INTEGER NULL,
	privileges_id INTEGER UNSIGNED NOT NULL DEFAULT 0,
	domains_id INTEGER UNSIGNED NOT NULL DEFAULT 0,
	is_allowed TINYINT UNSIGNED NULL DEFAULT 0
) ENGINE=INNODB;

/* Table Items: rbac_roles_has_domain_privileges */

/* Update Indexes for: rbac_roles_has_domain_privileges */
CREATE INDEX fk_user_has_domain_privileges_domains ON rbac_roles_has_domain_privileges (domains_id);
CREATE INDEX fk_user_has_domain_privileges_privileges ON rbac_roles_has_domain_privileges (privileges_id);
CREATE UNIQUE INDEX unique_users_privileges_domains ON rbac_roles_has_domain_privileges (roles_id, domains_id, privileges_id);

/******************** Add Table: rbac_users_has_roles ************************/

/* Build Table Structure */
CREATE TABLE rbac_users_has_roles
(
	id INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY,
	users_id INTEGER NOT NULL,
	roles_id INTEGER UNSIGNED NOT NULL
) TYPE=InnoDB;

/* Table Items: rbac_users_has_roles */

/* Update Indexes for: rbac_users_has_roles */
CREATE INDEX fk_roles_id_Idx ON rbac_users_has_roles (roles_id);
CREATE INDEX fk_users_id_idx ON rbac_users_has_roles (users_id);
CREATE UNIQUE INDEX unique_users_roles ON rbac_users_has_roles (users_id, roles_id);

/******************** Add Table: users ************************/

/* Build Table Structure */
CREATE TABLE users
(
	id INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY,
	username VARCHAR(50) NOT NULL,
	pswd VARCHAR(70) NOT NULL
) ENGINE=INNODB;

/* Table Items: users */

/* Update Indexes for: users */
CREATE UNIQUE INDEX unique_username ON users (username);


/************ Add Foreign Keys to Database ***************/
/*-----------------------------------------------------------
Warning: Versions of MySQL prior to 4.1.2 require indexes on all columns involved in a foreign key. The following indexes may be required: 
fk_auth_user_has_domain_privileges_auth_roles may require an index on table: rbac_roles_has_domain_privileges, column: roles_id
-----------------------------------------------------------
*/

/************ Foreign Key: fk_auth_domains_has_objects_auth_domains ***************/
ALTER TABLE rbac_domains_has_objects ADD CONSTRAINT fk_auth_domains_has_objects_auth_domains
	FOREIGN KEY (domains_id) REFERENCES rbac_domains (id);

/************ Foreign Key: fk_auth_domains_has_objects_auth_objects ***************/
ALTER TABLE rbac_domains_has_objects ADD CONSTRAINT fk_auth_domains_has_objects_auth_objects
	FOREIGN KEY (objects_id) REFERENCES rbac_objects (id);

/************ Foreign Key: fk_auth_privileges_has_actions_auth_actions ***************/
ALTER TABLE rbac_privileges_has_actions ADD CONSTRAINT fk_auth_privileges_has_actions_auth_actions
	FOREIGN KEY (actions_id) REFERENCES rbac_actions (id);

/************ Foreign Key: fk_auth_privileges_has_actions_auth_privileges ***************/
ALTER TABLE rbac_privileges_has_actions ADD CONSTRAINT fk_auth_privileges_has_actions_auth_privileges
	FOREIGN KEY (privileges_id) REFERENCES rbac_privileges (id);

/************ Foreign Key: fk_auth_user_has_domain_privileges_auth_domains ***************/
ALTER TABLE rbac_roles_has_domain_privileges ADD CONSTRAINT fk_auth_user_has_domain_privileges_auth_domains
	FOREIGN KEY (domains_id) REFERENCES rbac_domains (id);

/************ Foreign Key: fk_auth_user_has_domain_privileges_auth_privileges ***************/
ALTER TABLE rbac_roles_has_domain_privileges ADD CONSTRAINT fk_auth_user_has_domain_privileges_auth_privileges
	FOREIGN KEY (privileges_id) REFERENCES rbac_privileges (id);

/************ Foreign Key: fk_auth_user_has_domain_privileges_auth_roles ***************/
ALTER TABLE rbac_roles_has_domain_privileges ADD CONSTRAINT fk_auth_user_has_domain_privileges_auth_roles
	FOREIGN KEY (roles_id) REFERENCES rbac_roles (id);

/************ Foreign Key: fk_users_has_roles_roles ***************/
ALTER TABLE rbac_users_has_roles ADD CONSTRAINT fk_users_has_roles_roles
	FOREIGN KEY (roles_id) REFERENCES rbac_roles (id);