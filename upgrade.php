<?php
/**
 * DropletsExtension
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

global $admin;

$error = '';

$dbDropletsExt = new dbDropletsExtension();

if ($dbDropletsExt->sqlFieldExists('drop_css_file')) {
	if (!$dbDropletsExt->sqlAlterTableChangeField('drop_css_file', dbDropletsExtension::field_file, "VARCHAR(255) NOT NULL DEFAULT ''")) {
		$error .= sprintf('[UPGRADE] %s', $dbDropletsExt->getError());
	}
}

// Prompt Errors
if (!empty($error)) {
	$admin->print_error($error);
}

?>