<?php
/**
 * dropletsExtension
 * 
 * @author Ralf Hertsch (ralf.hertsch@phpmanufaktur.de)
 * @link http://phpmanufaktur.de
 * @copyright 2011
 * @license GNU GPL (http://www.gnu.org/licenses/gpl.html)
 * @version $Id$
 */

// prevent this file from being accessed directly
if (!defined('WB_PATH')) die('invalid call of '.$_SERVER['SCRIPT_NAME']);

require_once(WB_PATH .'/modules/'.basename(dirname(__FILE__)).'/class.extension.php');
require_once(WB_PATH .'/modules/'.basename(dirname(__FILE__)).'/class.pages.php');
 
/**
 * Ueberprueft ob das angegebene Droplet registriert ist
 * 
 * @param STR $droplet_name
 * @param STR $register_type
 * @param INT REFRENCE $page_id - die PAGE_ID fuer die das Droplet registriert ist
 */
function is_registered_droplet($droplet_name, $register_type, &$page_id=-1) {
	$dbDropletExt = new dbDropletsExtension();
	$droplet_name = clear_droplet_name($droplet_name);
	$where = array(	dbDropletsExtension::field_droplet_name 	=> $droplet_name,
									dbDropletsExtension::field_type 					=> $register_type);
	$droplet = array();
	if (!$dbDropletExt->sqlSelectRecord($where, $droplet)) {
		trigger_error(sprintf('[%s - %s] %s', __FUNCTION__, __LINE__, $dbDropletExt->getError()), E_USER_ERROR);
		return false;
	}
	$result = (count($droplet) > 0) ? true : false;
	$page_id = ($result) ? $droplet[0][dbDropletsExtension::field_page_id] :  -1;
	if ($page_id > 0) {
		// pruefen, ob eine droplet_search section existiert
		check_droplet_search_section($page_id);	
	}
	return $result;
} // is_registered_droplet()

/**
 * Ueberprueft ob das angegebene Droplet fuer die Droplet Suche registriert ist
 * 
 * @param STR $droplet_name
 * @param INT REFRENCE $page_id - die PAGE_ID fuer die das Droplet registriert ist
 */
function is_registered_droplet_search($droplet_name, &$page_id=-1) {
	return is_registered_droplet($droplet_name, dbDropletsExtension::type_search, &$page_id);
} // is_registered_droplet_search()

/**
 * Ueberprueft ob das das angegebene Droplet fuer den Template Header registriert ist
 * 
 * @param STR $droplet_name
 * @param INT REFERENCE $page_id
 */
function is_registered_droplet_header($droplet_name, &$page_id=-1) {
	return is_registered_droplet($droplet_name, dbDropletsExtension::type_header, &$page_id);
} // is_registered_droplet_header()

/**
 * Registriert das angegebene Droplet
 * 
 * @param STR $droplet_name - Name des Droplets
 * @param INT $page_id - PAGE_ID der Seite auf der das Droplet verwendet wird
 * @param STR $module_directory - Modul Verzeichnis in dem die Suche die Datei droplet.extension.php findet
 */
function register_droplet($droplet_name, $page_id, $module_directory, $register_type) {
	// zuerst pruefen, ob eine droplet_search section existiert
	if ($register_type == dbDropletsExtension::type_search) check_droplet_search_section($page_id); 
	$droplet_name = clear_droplet_name($droplet_name);
	if (!droplet_exists($droplet_name, $page_id)) {
		return false;
	}
	$dbDropletExt = new dbDropletsExtension();
	if (is_registered_droplet($droplet_name, $register_type, $old_page_id)) {
		if ($old_page_id != $page_id) {
			// Datensatz aktualisieren
			$where = array(	dbDropletsExtension::field_droplet_name 	=> $droplet_name,
											dbDropletsExtension::field_type					=> $register_type);
			$data = array(dbDropletsExtension::field_page_id 				=> $page_id);
			if (!$dbDropletExt->sqlUpdateRecord($data, $where)) {
				trigger_error(sprintf('[%s - %s] %s', __FUNCTION__, __LINE__, $dbDropletExt->getError()), E_USER_ERROR);
				return false;
			}
			return true;
		}
		else {
			// Droplet ist bereits registriert
			return true;
		}
	}
	$module_directory = clear_module_directory($module_directory);
	$data = array(
		dbDropletsExtension::field_droplet_name 			=> $droplet_name,
		dbDropletsExtension::field_page_id						=> $page_id,
		dbDropletsExtension::field_module_directory 	=> $module_directory,
		dbDropletsExtension::field_type							=> $register_type
	);
	if (!$dbDropletExt->sqlInsertRecord($data)) {
		trigger_error(sprintf('[%s - %s] %s', __FUNCTION__, __LINE__, $dbDropletExt->getError()), E_USER_ERROR);
		return false;
	}
	return true;
} // register_droplet()

/**
 * Registriert das angegegebene Droplet fuer die Suche
 * 
 * @param STR $droplet_name
 * @param STR $page_id
 * @param STR $module_directory
 * @return BOOL
 */
function register_droplet_search($droplet_name, $page_id, $module_directory) {
	return register_droplet($droplet_name, $page_id, $module_directory, dbDropletsExtension::type_search);
} // register_droplet_search()

/**
 * Registriert das angegebene Droplet fuer den Template Header
 * 
 * @param STR $droplet_name
 * @param STR $page_id
 * @param STR $module_directory
 */
function register_droplet_header($droplet_name, $page_id, $module_directory) { 
	return register_droplet($droplet_name, $page_id, $module_directory, dbDropletsExtension::type_header);
} // register_droplet_header()


/**
 * Entfernt das angegebene Droplet
 * 
 * @param STR $droplet_name
 * @return BOOL
 */
function unregister_droplet($droplet_name, $register_type) {
	$droplet_name = clear_droplet_name($droplet_name);
	$dbDropletExt = new dbDropletsExtension();
	$where = array(	dbDropletsExtension::field_droplet_name 	=> $droplet_name,
									dbDropletsExtension::field_type					=> $register_type);
	if (!$dbDropletExt->sqlDeleteRecord($where)) {
		trigger_error(sprintf('[%s - %s] %s', __FUNCTION__, __LINE__, $dbDropletExt->getError()), E_USER_ERROR);
		return false;
	}
	return true;
} // unregister_droplet()

/**
 * Entfernt das angegebene Droplet aus der Suche
 * 
 * @param STR $droplet_name
 * @return BOOL
 */
function unregister_droplet_search($droplet_name) {
	return unregister_droplet($droplet_name, dbDropletsExtension::type_search);
} // unregister_droplet_search()

/**
 * Entfernt das angegebene Droplet aus dem Template Header
 * 
 * @param STR $droplet_name
 * @return BOOL
 */
function unregister_droplet_header($droplet_name) {
	return unregister_droplet($droplet_name, dbDropletsExtension::type_header);
} // unregister_droplet_header()

/**
 * Bereinigt den angegebenen Droplet Namen
 * 
 * @param STR $droplet_name
 * @return STR $droplet_name
 */
function clear_droplet_name($droplet_name) {
	$droplet_name = strtolower($droplet_name);
	$droplet_name = str_replace('[', '', $droplet_name);
	$droplet_name = str_replace(']', '', $droplet_name);
	$droplet_name = trim($droplet_name);
	return $droplet_name;
} // clear_droplet_name()

/**
 * Bereinigt den Name dss angegebene Modul Verzeichnis
 * 
 * @param STR $module_directory
 * @return STR $module_directory
 */
function clear_module_directory($module_directory) {
	$module_directory = str_replace('/', '', $module_directory); 
	$module_directory = str_replace('\\', '', $module_directory);
	$module_directory = trim($module_directory);
	return $module_directory;
} // clear_module_directory()

/**
 * Ueberprueft ob das angegebene Droplet auf der Seite mit der PAGE_ID 
 * verwendet wird
 * 
 * @param STR $droplet_name
 * @param INT $page_id
 * @return BOOL
 */
function droplet_exists($droplet_name, $page_id) {
	$droplet_name = clear_droplet_name($droplet_name);
	$dbWYSIWYG = new db_wb_mod_wysiwyg();
	$SQL = sprintf(	"SELECT * FROM %s WHERE %s='%s' AND ((%s LIKE '%%[[%s?%%') OR (%s LIKE '%%[[%s]]%%'))",
									$dbWYSIWYG->getTableName(),
									db_wb_mod_wysiwyg::field_page_id,
									$page_id,
									db_wb_mod_wysiwyg::field_text,
									$droplet_name,
									db_wb_mod_wysiwyg::field_text,
									$droplet_name);
	$result = array();
	if (!$dbWYSIWYG->sqlExec($SQL, $result)) {
		trigger_error(sprintf('[%s - %s] %s', __FUNCTION__, __LINE__, $dbWYSIWYG->getError()), E_USER_ERROR);
		return false;
	}
	return (count($result) > 0) ? true : false;
} // droplet_exists()

/**
 * Prueft ob eine SECTION mit droplet_extension existiert und legt sie ggf. an.
 * 
 * @param INT $page_id
 * @return BOOL
 */
function check_droplet_search_section($page_id=-1) {
	$dbSections = new db_wb_sections();
	$SQL = sprintf( "SELECT * FROM %s WHERE %s='%s'",
									$dbSections->getTableName(),
									db_wb_sections::field_module,
									'droplet_extension');
	$result = array();
	if (!$dbSections->sqlExec($SQL, $result)) {
		trigger_error(sprintf('[%s - %s] %s', __FUNCTION__, __LINE__, $dbSections->getError()), E_USER_ERROR);
		return false;
	}
	if (count($result) > 0) return true;
	if ($page_id < 1) return false;
	$SQL = sprintf(	"SELECT * FROM %s WHERE %s='%s' ORDER BY %s DESC LIMIT 1",
									$dbSections->getTableName(),
									db_wb_sections::field_page_id,
									$page_id,
									db_wb_sections::field_position);
	$result = array();
	if (!$dbSections->sqlExec($SQL, $result)) {
		trigger_error(sprintf('[%s - %s] %s', __FUNCTION__, __LINE__, $dbSections->getError()), E_USER_ERROR);
		return false;
	}
	if (count($result) < 1) return false;
	$x = $result[0][db_wb_sections::field_position];
	$data = array(
		db_wb_sections::field_block				=> 1,
		db_wb_sections::field_publ_end		=> 0,
		db_wb_sections::field_publ_start	=> 0,
		db_wb_sections::field_module			=> 'droplet_extension',
		db_wb_sections::field_page_id			=> $page_id,
		db_wb_sections::field_position		=> $x+1
	);
	if (!$dbSections->sqlInsertRecord($data)) {
		trigger_error(sprintf('[%s - %s] %s', __FUNCTION__, __LINE__, $dbSections->getError()), E_USER_ERROR);
		return false;
	}
	return true;
} // check_droplet_search_section()


function print_page_head() {
	global $database;
	global $wb;
	global $page_id;
	
	$title = $wb->page_title;
	$description = $wb->page_description;
	$keywords = $wb->page_keywords;
	
	if (defined('TOPIC_ID')) {
		// Es handelt sich um eine TOPICS Seite
		$SQL = sprintf("SELECT * FROM %smod_topics WHERE topic_id='%d'", TABLE_PREFIX, TOPIC_ID);
  	if (false !== ($topics = $database->query($SQL))) {
  		if (false !== ($topic = $topics->fetchRow(MYSQL_ASSOC))) {
  			if (isset($topic['title']) && !empty($topic['title'])) $title = $topic['title'];
  			if (isset($topic['short_description']) && !empty($topic['short_description'])) $description = $topic['short_description'];
  			if (isset($topic['keywords']) && !empty($topic['keywords'])) $keywords = $topic['keywords'];
  		}
  		else {
  			trigger_error(sprintf('[%s - %s] %s', __FUNCTION__, __LINE__, $database->get_error()), E_USER_ERROR);
				return false;
  		}
  	}
  	else {
  		trigger_error(sprintf('[%s - %s] %s', __FUNCTION__, __LINE__, $database->get_error()), E_USER_ERROR);
			return false;
  	}		
	}
	else {
		// Droplets pruefen
		$dbDropletExt = new dbDropletsExtension();
		$SQL = sprintf( "SELECT * FROM %s WHERE %s='%s' AND %s='%s'",
										$dbDropletExt->getTableName(),
										dbDropletsExtension::field_type,
										dbDropletsExtension::type_header,
										dbDropletsExtension::field_page_id,
										$page_id);
		$droplet = array();
		if (!$dbDropletExt->sqlExec($SQL, $droplet)) {
			trigger_error(sprintf('[%s - %s] %s', __FUNCTION__, __LINE__, $dbDropletExt->getError()), E_USER_ERROR);
			return false;
		}
		if (count($droplet) > 0) {
			// es ist ein Droplet angemeldet
			$droplet = $droplet[0];
			if (droplet_exists($droplet[dbDropletsExtension::field_droplet_name], $page_id)) { 
				// das Droplet existiert
				if (file_exists(WB_PATH.'/modules/'.$droplet[dbDropletsExtension::field_module_directory].'/droplet.extension.php')) { 
					include(WB_PATH.'/modules/'.$droplet[dbDropletsExtension::field_module_directory].'/droplet.extension.php');
					$user_func = $droplet[dbDropletsExtension::field_module_directory].'_droplet_header';
					if (function_exists($user_func)) { 
						$header = call_user_func($user_func, $page_id);
						if (is_array($header)) {
							if (isset($header['title']) && !empty($header['title'])) $title = $header['title'];
							if (isset($header['description']) && !empty($header['description'])) $description = $header['description'];
							if (isset($header['keywords']) && !empty($header['keywords'])) $keywords = $header['keywords']; 
						}
					}
				}
			}
			else {
				// das Droplet existiert nicht...
				unregister_droplet_header($droplet[dbDropletsExtension::field_droplet_name]);
			}
		}
	}
	
	$head = sprintf('<meta name="description" content="%s" />'."\n".'<meta name="keywords" content="%s" />'."\n".'<title>%s</title>'."\n",
									$description,
									$keywords,
									$title);
	echo $head;
} // print_page_head()

?>