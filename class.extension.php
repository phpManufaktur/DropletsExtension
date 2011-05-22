<?php
/**
 * dropletExtension
 * 
 * @author Ralf Hertsch (ralf.hertsch@phpmanufaktur.de)
 * @link http://phpmanufaktur.de
 * @copyright 2011
 * @license GNU GPL (http://www.gnu.org/licenses/gpl.html)
 * @version $Id$
 */

// prevent this file from being accessed directly
if (!defined('WB_PATH')) die('invalid call of '.$_SERVER['SCRIPT_NAME']);

if (!class_exists('dbconnectle')) require_once(WB_PATH.'/modules/dbconnect_le/include.php');

class dbDropletExtension extends dbConnectLE {
	
	const field_id								= 'drop_id';
	const field_droplet_name			= 'drop_droplet_name';
	const field_page_id						= 'drop_page_id';
	const field_module_directory	= 'drop_module_dir';
	const field_type							= 'drop_type';
	const field_timestamp					= 'drop_timestamp';
	
	const type_search							= 'search';
	const type_header							= 'header';
	const type_undefined					= 'undefined';
	
	public function __construct($createTables = false) {
  	$this->createTables = $createTables;
  	parent::__construct();
  	$this->setTableName('mod_droplets_extension');
  	$this->addFieldDefinition(self::field_id, "INT(11) NOT NULL AUTO_INCREMENT", true);
  	$this->addFieldDefinition(self::field_droplet_name, "VARCHAR(255) NOT NULL DEFAULT ''");
  	$this->addFieldDefinition(self::field_page_id, "INT(11) NOT NULL DEFAULT '-1'");
  	$this->addFieldDefinition(self::field_module_directory, "VARCHAR(255) NOT NULL DEFAULT ''");
  	$this->addFieldDefinition(self::field_type, "VARCHAR(20) NOT NULL DEFAULT '".self::type_undefined."'");
  	$this->addFieldDefinition(self::field_timestamp, "TIMESTAMP");
  	$this->checkFieldDefinitions();
  	// Tabelle erstellen
  	if ($this->createTables) {
  		if (!$this->sqlTableExists()) {
  			if (!$this->sqlCreateTable()) {
  				$this->setError(sprintf('[%s - %s] %s', __METHOD__, __LINE__, $this->getError()));
  			}
  		}
  	}
  } // __construct()
	
} // class dbDropletExtensionSearch

?>