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

function is_droplet_search_registered($droplet_name) {
	$dbDropletExt = new dbDropletExtensionSearch();
	$where = array(dbDropletExtensionSearch::field_droplet_name => $droplet_name);
	$result = array();
	if (!$dbDropletExt->sqlSelectRecord($where, $result)) {
		trigger_error($dbDropletExt->getError(), E_ERROR);
		return false;
	}
	$result = (count($result) > 0) ? true : false;
	return $result;
} 

?>