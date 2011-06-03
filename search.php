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

require_once(WB_PATH .'/modules/'.basename(dirname(__FILE__)).'/class.extension.php');
require_once(WB_PATH .'/modules/'.basename(dirname(__FILE__)).'/interface.php');

if (!class_exists('kitToolsLibrary'))   	require_once(WB_PATH.'/modules/kit_tools/class.tools.php');

function droplets_extension_search($func_vars) { 
	$dbDropletExt = new dbDropletsExtension();
	$kitTools = new kitToolsLibrary();
	$SQL = sprintf(	"SELECT * FROM %s WHERE %s='%s'", 
									$dbDropletExt->getTableName(),
									dbDropletsExtension::field_type,
									dbDropletsExtension::type_search);
	$droplets = array();
	if (!$dbDropletExt->sqlExec($SQL, $droplets)) {
		trigger_error(sprintf('[%s - %s] %s', __FUNCTION__, __LINE__, $dbDropletExt->getError()), E_USER_ERROR);
		return false;
	}
	extract($func_vars, EXTR_PREFIX_ALL, 'func');
	$result = false;
	foreach ($droplets as $droplet) {
		if (droplet_exists($droplet[dbDropletsExtension::field_droplet_name], $droplet[dbDropletsExtension::field_page_id])) {
			if (file_exists(WB_PATH.'/modules/'.$droplet[dbDropletsExtension::field_module_directory].'/droplet.extension.php')) { 
				include(WB_PATH.'/modules/'.$droplet[dbDropletsExtension::field_module_directory].'/droplet.extension.php');
				$user_func = $droplet[dbDropletsExtension::field_module_directory].'_droplet_search';
				if (function_exists($user_func)) {
					$kitTools->getUrlByPageID($droplet[dbDropletsExtension::field_page_id], $page_url);
					$search_result = call_user_func($user_func, $droplet[dbDropletsExtension::field_page_id], $page_url);
					if (is_array($search_result)) { 
						foreach ($search_result as $search) {
							$url = isset($search['url']) ? $search['url'] : ''; 
							$mod_vars = array( 
								'page_link' 					=> $url,
								'page_link_target' 		=> isset($search['params']) && !empty($search['params']) ? sprintf('%s%s', (strpos($url, '?') === false) ? '?' : '&', $search['params']) : '',
								'page_title' 					=> isset($search['title']) ? $search['title'] : $func_page_title,
								'page_description' 		=> isset($search['description']) ? $search['description'] : '',
								'page_modified_when' 	=> isset($search['modified_when']) ? $search['modified_when'] : '',
								'page_modified_by' 		=> isset($search['modified_by']) ? $search['modified_by'] : '',
								'text'								=> isset($search['text']) ? $search['text'] : ''
							);
							if (print_excerpt2($mod_vars, $func_vars)) {  
								$result = true;
							}
						}
					} 
				}
			}			
		}
		else {
			unregister_droplet_search($droplet[dbDropletsExtension::field_droplet_name]);
		}
	}
	return $result;
} // droplet_extension_search()

?>